<?php

/**
 * Sentry (PHP errors only, no performance tracing): uncaught exceptions, fatal errors, PHP warnings / errors, and the
 * Wonolog records of level ERROR and above (HTTP API errors, custom channels).
 * Enabled when SENTRY_DSN is defined and not empty (config/local.php of the environment, see config/local.sample.php).
 * No personal data: no IP, no cookies, no request body (forms hold children data), user id and role only; secrets
 * of the URLs and of the log contexts redacted (05-log-redaction.php).
 */

namespace Globalis\WP\Cubi;

require_once __DIR__ . '/05-log-redaction.php';

/**
 * Query string parameters never sent (payment links, webhooks, nonces, password reset)
 */
const SENTRY_REDACTED_PARAMS = ['key', 'secret', 'token', '_wpnonce', 'wp-cubi-webhooks-secret', 'pass', 'pwd', 'rp_key'];

/**
 * SDK options, null when Sentry is disabled (no DSN)
 *
 * @return array<string, mixed>|null
 */
function sentry_options(string $dsn, string $environment, ?string $release, string $rootDir): ?array
{
    if ('' === trim($dsn)) {
        return null;
    }

    return [
        'dsn' => $dsn,
        'environment' => $environment,
        'release' => $release ?: null,
        'send_default_pii' => false,
        'max_request_body_size' => 'never',
        // Errors only
        'traces_sample_rate' => null,
        // WordPress core and plugins raise many deprecations and notices: not reported
        'error_types' => E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_NOTICE & ~E_USER_NOTICE,
        'in_app_include' => [$rootDir . '/web/app/themes', $rootDir . '/web/app/mu-modules', $rootDir . '/config'],
        'in_app_exclude' => [$rootDir . '/vendor', $rootDir . '/web/wpcb', $rootDir . '/web/app/modules'],
        'before_send' => __NAMESPACE__ . '\\sentry_before_send',
        'before_breadcrumb' => __NAMESPACE__ . '\\sentry_before_breadcrumb',
    ];
}

/**
 * URL query string without its secrets: "action=x&key=[redacted]"
 */
function sentry_redact_query(string $query): string
{
    if ('' === $query) {
        return $query;
    }

    parse_str($query, $params);
    foreach ($params as $name => $value) {
        if (in_array(strtolower((string)$name), SENTRY_REDACTED_PARAMS, true)) {
            $params[$name] = LOG_REDACTED;
        }
    }

    return urldecode(http_build_query($params));
}

function sentry_before_send(\Sentry\Event $event, ?\Sentry\EventHint $hint): \Sentry\Event
{
    $request = $event->getRequest();
    if (!empty($request['query_string'])) {
        $request['query_string'] = sentry_redact_query((string)$request['query_string']);
    }
    if (!empty($request['url']) && str_contains((string)$request['url'], '?')) {
        [$url, $query] = explode('?', (string)$request['url'], 2);
        $request['url'] = $url . '?' . sentry_redact_query($query);
    }
    unset($request['data'], $request['cookies']);
    $event->setRequest($request);

    $event->setExtra(redact_log_context($event->getExtra(), true));

    return $event;
}

function sentry_before_breadcrumb(\Sentry\Breadcrumb $breadcrumb): \Sentry\Breadcrumb
{
    foreach (redact_log_context($breadcrumb->getMetadata(), true) as $name => $value) {
        $breadcrumb = $breadcrumb->withMetadata((string)$name, $value);
    }

    return $breadcrumb;
}

/**
 * Monolog handler for Wonolog: records of level ERROR and above become Sentry issues, INFO and above breadcrumbs.
 * The PHP-ERROR channel is skipped: the SDK reports PHP errors and exceptions itself (no duplicates).
 */
function sentry_monolog_handler(): \Monolog\Handler\HandlerInterface
{
    return new class extends \Monolog\Handler\AbstractHandler {
        /** @var \Monolog\Handler\HandlerInterface[] */
        private array $handlers;

        public function __construct()
        {
            parent::__construct(\Monolog\Level::Info);
            $hub = \Sentry\SentrySdk::getCurrentHub();
            $this->handlers = [
                new \Sentry\Monolog\BreadcrumbHandler($hub, \Monolog\Level::Info),
                new \Sentry\Monolog\Handler($hub, \Monolog\Level::Error, true, true),
            ];
        }

        public function isHandling(\Monolog\LogRecord $record): bool
        {
            return 'PHP-ERROR' !== $record->channel && parent::isHandling($record);
        }

        public function handle(\Monolog\LogRecord $record): bool
        {
            if (!$this->isHandling($record)) {
                return false;
            }
            foreach ($this->handlers as $handler) {
                if ($handler->isHandling($record)) {
                    $handler->handle($record);
                }
            }

            return false === $this->bubble;
        }
    };
}

/**
 * The SDK is ready: Wonolog records are forwarded (00-wonolog.php)
 */
function sentry_enabled(): bool
{
    return class_exists(\Sentry\SentrySdk::class) && null !== \Sentry\SentrySdk::getCurrentHub()->getClient();
}

$sentryOptions = sentry_options(
    defined('SENTRY_DSN') ? (string)SENTRY_DSN : '',
    defined('WP_ENV') ? (string)WP_ENV : 'production',
    defined('SENTRY_RELEASE') ? (string)SENTRY_RELEASE : null,
    defined('ROOT_DIR') ? ROOT_DIR : dirname(__DIR__, 5)
);

if (null !== $sentryOptions && function_exists('Sentry\\init')) {
    \Sentry\init($sentryOptions);

    \Sentry\configureScope(function (\Sentry\State\Scope $scope): void {
        $scope->setTag('context', match (true) {
            defined('WP_CLI') && WP_CLI => 'cli',
            wp_doing_cron() => 'cron',
            wp_doing_ajax() => 'ajax',
            is_admin() => 'admin',
            default => 'front',
        });
    });

    // User id and roles only, never the email nor the name
    add_action('set_current_user', function () {
        $user = wp_get_current_user();
        \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($user): void {
            $user->exists()
                ? $scope->setUser(['id' => (string)$user->ID, 'roles' => implode(',', (array)$user->roles)])
                : $scope->removeUser();
        });
    });
}

unset($sentryOptions);

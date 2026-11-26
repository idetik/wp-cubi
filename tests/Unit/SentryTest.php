<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sentry\Event;
use Sentry\SentrySdk;
use Sentry\State\Hub;
use Sentry\Transport\Result;
use Sentry\Transport\ResultStatus;
use Sentry\Transport\TransportInterface;

use function Globalis\WP\Cubi\sentry_options;
use function Globalis\WP\Cubi\sentry_redact_query;

/**
 * Sentry (web/app/mu-modules/00-wp-cubi-core-mu/src/01-sentry.php): enabled by SENTRY_DSN, no personal data nor secret
 */
class SentryTest extends TestCase
{
    /** @var Event[] */
    protected array $sent = [];

    public static function setUpBeforeClass(): void
    {
        // No SENTRY_DSN in the tests: the module only declares its functions
        require_once dirname(__DIR__, 2) . '/web/app/mu-modules/00-wp-cubi-core-mu/src/01-sentry.php';
    }

    protected function tearDown(): void
    {
        SentrySdk::setCurrentHub(new Hub());
    }

    /**
     * SDK initialized with the module options, events kept here instead of being sent
     */
    protected function init(): void
    {
        $transport = new class ($this->sent) implements TransportInterface {
            public function __construct(private array &$sent)
            {
            }

            public function send(Event $event): Result
            {
                $this->sent[] = $event;
                return new Result(ResultStatus::success(), $event);
            }

            public function close(?int $timeout = null): Result
            {
                return new Result(ResultStatus::success());
            }
        };

        $options = sentry_options('https://public@sentry.example.org/1', 'staging', '2026.11.10', '/srv/app');
        // No error / exception handler installed by the tests
        \Sentry\init($options + ['transport' => $transport, 'default_integrations' => false]);
    }

    public function testDisabledWithoutDsn(): void
    {
        $this->assertNull(sentry_options('', 'production', null, '/srv/app'));
        $this->assertNull(sentry_options('  ', 'production', null, '/srv/app'));
    }

    public function testOptions(): void
    {
        $options = sentry_options('https://public@sentry.example.org/1', 'production', null, '/srv/app');

        $this->assertSame('production', $options['environment']);
        $this->assertNull($options['release']);
        $this->assertFalse($options['send_default_pii']);
        $this->assertSame('never', $options['max_request_body_size']);
        $this->assertNull($options['traces_sample_rate'], 'Errors only');
        $this->assertSame(0, $options['error_types'] & (E_DEPRECATED | E_USER_DEPRECATED | E_NOTICE | E_USER_NOTICE));
        $this->assertNotSame(0, $options['error_types'] & E_WARNING);
        $this->assertContains('/srv/app/web/app/themes', $options['in_app_include']);
    }

    public function testSecretsOfTheQueryString(): void
    {
        $this->assertSame(
            'action=app_payment_notification&key=[redacted]&registration=12',
            sentry_redact_query('action=app_payment_notification&key=abc123&registration=12')
        );
        $this->assertSame('_wpnonce=[redacted]&page=x', sentry_redact_query('_wpnonce=f00&page=x'));
        $this->assertSame('', sentry_redact_query(''));
    }

    public function testEventWithoutSecretsNorRequestBody(): void
    {
        $this->init();
        \Sentry\configureScope(function ($scope) {
            $scope->setExtra('query_args', ['headers' => ['Authorization' => 'Bearer abc.def']]);
        });

        $event = Event::createEvent();
        $event->setMessage('HelloAsso checkout failed');
        $event->setRequest([
            'url' => 'https://example.org/wp-admin/admin-post.php?action=app_checkout&key=s3cr3t',
            'query_string' => 'action=app_checkout&key=s3cr3t',
            'data' => ['player_firstname' => 'Lucas'],
            'cookies' => ['wordpress_logged_in' => 'x'],
        ]);
        SentrySdk::getCurrentHub()->captureEvent($event);

        $this->assertCount(1, $this->sent);
        $request = $this->sent[0]->getRequest();
        $this->assertSame('https://example.org/wp-admin/admin-post.php?action=app_checkout&key=[redacted]', $request['url']);
        $this->assertSame('action=app_checkout&key=[redacted]', $request['query_string']);
        $this->assertArrayNotHasKey('data', $request);
        $this->assertArrayNotHasKey('cookies', $request);
        $this->assertSame('[redacted]', $this->sent[0]->getExtra()['query_args']['headers']['Authorization']);
        $this->assertSame('staging', $this->sent[0]->getEnvironment());
        $this->assertSame('2026.11.10', $this->sent[0]->getRelease());
    }

    public function testBreadcrumbsWithoutSecrets(): void
    {
        $this->init();
        \Sentry\addBreadcrumb(new \Sentry\Breadcrumb('info', 'default', 'HTTP', 'call', ['headers' => ['Authorization' => 'Bearer abc']]));
        \Sentry\captureMessage('Oops');

        $breadcrumbs = $this->sent[0]->getBreadcrumbs();
        $this->assertSame('[redacted]', $breadcrumbs[0]->getMetadata()['headers']['Authorization']);
    }
}

<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * wp-cubi webhooks (web/app/mu-modules/20-wp-cubi-webhooks): run only with the secret of the environment, never when
 * no secret is defined (config/vars.php). Each case runs in its own PHP process (the webhook ends with exit).
 */
class WebhooksSecretTest extends TestCase
{
    /**
     * @return string "RUN" when the webhook ran, "" otherwise
     */
    protected function trigger(?string $secret, mixed $given): string
    {
        $module = dirname(__DIR__, 2) . '/web/app/mu-modules/20-wp-cubi-webhooks/20-wp-cubi-webhooks.php';
        $code = sprintf(
            'namespace Globalis\WP\Cubi { function add_action() {} function do_action() { echo "RUN"; } function header() {} }
            namespace { %s $_GET = %s; require %s; Globalis\WP\Cubi\trigger_webhooks(); }',
            null === $secret ? '' : sprintf('define("WP_CUBI_WEBHOOKS_SECRET", %s);', var_export($secret, true)),
            var_export(['wp-cubi-webhooks-run' => 'clear-wp-cubi-transient-cache', 'wp-cubi-webhooks-secret' => $given], true),
            var_export($module, true)
        );

        $output = [];
        exec(escapeshellarg(PHP_BINARY) . ' -r ' . escapeshellarg($code) . ' 2>&1', $output);

        return implode("\n", $output);
    }

    public function testRunsWithTheSecretOfTheEnvironment(): void
    {
        $this->assertSame('RUN', $this->trigger('s3cr3t-of-staging', 's3cr3t-of-staging'));
    }

    public function testRefusedWithAnotherSecret(): void
    {
        $this->assertSame('', $this->trigger('s3cr3t-of-staging', 'guess'));
        $this->assertSame('', $this->trigger('s3cr3t-of-staging', ['s3cr3t-of-staging']));
    }

    public function testDisabledWithoutSecret(): void
    {
        $this->assertSame('', $this->trigger(null, ''));
        $this->assertSame('', $this->trigger('', ''));
    }
}

<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use function Globalis\WP\Cubi\redact_log_context;

/**
 * Log files: no secret nor personal data (web/app/mu-modules/00-wp-cubi-core-mu/src/05-log-redaction.php)
 */
class LogRedactionTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once dirname(__DIR__, 2) . '/web/app/mu-modules/00-wp-cubi-core-mu/src/05-log-redaction.php';
    }

    /**
     * Context of Wonolog's HttpApiListener for a failed HelloAsso call
     */
    public function testHttpErrorContext(): void
    {
        $context = redact_log_context([
            'transport' => 'WpOrg\\Requests\\Requests',
            'query_args' => [
                'method' => 'POST',
                'headers' => ['Authorization' => 'Bearer eyJhbGciOi.abc-123', 'Accept' => 'application/json'],
                'body' => '{"payer":{"email":"famille@example.org"},"totalAmount":5000}',
            ],
            'response_body' => '{"errors":[{"message":"payer famille@example.org"}]}',
            'url' => 'https://api.helloasso.com/v5/organizations/acme/checkout-intents',
        ], true);

        $this->assertSame('[redacted]', $context['query_args']['headers']['Authorization']);
        $this->assertSame('application/json', $context['query_args']['headers']['Accept']);
        $this->assertSame('[redacted]', $context['query_args']['body']);
        $this->assertSame('[redacted]', $context['response_body']);
        $this->assertSame('POST', $context['query_args']['method']);
        $this->assertStringContainsString('api.helloasso.com', $context['url']);
    }

    public function testSensitiveKeysAtAnyDepthAndBearerInStrings(): void
    {
        $context = redact_log_context([
            'user' => ['login' => 'pierre', 'user_pass' => 'secret', 'Client_Secret' => 'abc'],
            'details' => 'curl -H "Authorization: Bearer abc.def-ghi" failed',
            'body' => 'kept outside the HTTP channel',
            3 => ['token' => 'xyz'],
        ]);

        $this->assertSame(['login' => 'pierre', 'user_pass' => '[redacted]', 'Client_Secret' => '[redacted]'], $context['user']);
        $this->assertSame('curl -H "Authorization: Bearer [redacted]" failed', $context['details']);
        $this->assertSame('kept outside the HTTP channel', $context['body']);
        $this->assertSame(['token' => '[redacted]'], $context[3]);
    }
}

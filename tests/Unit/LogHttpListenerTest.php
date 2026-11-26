<?php

namespace Tests\Unit;

use Globalis\WP\Cubi\HttpApiListener;
use PHPUnit\Framework\TestCase;

use function Globalis\WP\Cubi\is_unknown_non_blocking_http_response;

/**
 * HTTP API errors without the non-blocking calls false positives
 * (web/app/mu-modules/00-wp-cubi-core-mu/src/06-log-http-listener.php)
 */
class LogHttpListenerTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once dirname(__DIR__, 2) . '/web/app/mu-modules/00-wp-cubi-core-mu/src/06-log-http-listener.php';
    }

    /**
     * Response returned by WP_Http for a non-blocking call: no status code
     *
     * @return array<mixed>
     */
    private function nonBlockingResponse(): array
    {
        return ['headers' => [], 'body' => '', 'response' => ['code' => false, 'message' => false], 'cookies' => []];
    }

    /**
     * The wp-cubi transient cache ping, fired on shutdown after each back-office save
     */
    public function testTransientCachePingSkipped(): void
    {
        $this->assertTrue(is_unknown_non_blocking_http_response([
            $this->nonBlockingResponse(),
            'response',
            'WpOrg\\Requests\\Requests',
            ['method' => 'POST', 'timeout' => 0.01, 'blocking' => false, 'sslverify' => false],
            'https://staging.example.org/?action=wp-cubi-transient-cache-ping',
        ]));
    }

    public function testBlockingCallKept(): void
    {
        $this->assertFalse(is_unknown_non_blocking_http_response([
            ['response' => ['code' => 500, 'message' => 'Internal Server Error']],
            'response',
            'WpOrg\\Requests\\Requests',
            ['method' => 'POST', 'blocking' => true],
            'https://api.helloasso.com/v5/organizations/acme/checkout-intents',
        ]));
    }

    public function testBlockingDefaultKept(): void
    {
        $this->assertFalse(is_unknown_non_blocking_http_response([
            ['response' => ['code' => 404, 'message' => 'Not Found']],
            'response',
            'WpOrg\\Requests\\Requests',
            ['method' => 'GET'],
            'https://example.org/',
        ]));
    }

    /**
     * A non-blocking call can still fail at the transport level (DNS, connection refused)
     */
    public function testNonBlockingTransportErrorKept(): void
    {
        $this->assertFalse(is_unknown_non_blocking_http_response([
            new \stdClass(), // WP_Error in WordPress: anything but an array
            'response',
            'WpOrg\\Requests\\Requests',
            ['blocking' => false],
            'https://example.org/',
        ]));
    }

    public function testMissingArguments(): void
    {
        $this->assertFalse(is_unknown_non_blocking_http_response([]));
        $this->assertFalse(is_unknown_non_blocking_http_response([$this->nonBlockingResponse()]));
    }

    public function testListensToHttpApiDebug(): void
    {
        $this->assertSame(['http_api_debug'], (new HttpApiListener())->listenTo());
    }
}

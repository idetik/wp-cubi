<?php

/**
 * Wonolog's HttpApiListener logs every non-blocking HTTP call as an error: WordPress returns no status code for them
 * (`response.code` false) and the listener tests the missing code before the `blocking` argument. The wp-cubi transient
 * cache ping (`?action=wp-cubi-transient-cache-ping`, fired on shutdown after each cache clear) became a
 * "WP HTTP API Error" in the log files and Sentry on every save in the back-office.
 */

namespace Globalis\WP\Cubi;

use Inpsyde\Wonolog\HookListener\ActionListener;
use Inpsyde\Wonolog\HookListener\HttpApiListener as WonologHttpApiListener;
use Inpsyde\Wonolog\LogActionUpdater;

/**
 * Non-blocking call without a transport error: nothing to log, the response is unknown
 *
 * @param array<mixed> $args `http_api_debug` arguments: response, context, class, request args, url
 */
function is_unknown_non_blocking_http_response(array $args): bool
{
    [$response, , , $httpArgs] = array_pad($args, 4, null);

    return is_array($response)
        && is_array($httpArgs)
        && array_key_exists('blocking', $httpArgs)
        && !$httpArgs['blocking'];
}

/**
 * Wonolog's HttpApiListener (final), non-blocking calls without a WP_Error skipped
 */
final class HttpApiListener implements ActionListener
{
    private WonologHttpApiListener $listener;

    public function __construct(?WonologHttpApiListener $listener = null)
    {
        $this->listener = $listener ?? new WonologHttpApiListener();
    }

    public function listenTo(): array
    {
        return $this->listener->listenTo();
    }

    public function update(string $hook, array $args, LogActionUpdater $updater): void
    {
        if (is_unknown_non_blocking_http_response($args)) {
            return;
        }

        $this->listener->update($hook, $args, $updater);
    }
}

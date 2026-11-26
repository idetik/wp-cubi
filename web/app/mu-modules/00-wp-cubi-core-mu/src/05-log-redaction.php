<?php

/**
 * Secrets and personal data are never written to the log files: Wonolog's HTTP listener logs the whole request
 * arguments of a failed call (Authorization header, form body) and the beginning of the response (payer data…).
 */

namespace Globalis\WP\Cubi;

const LOG_REDACTED = '[redacted]';

/**
 * Keys whose value is replaced at any depth (case insensitive)
 */
const LOG_SENSITIVE_KEYS = [
    'authorization', 'proxy-authorization', 'cookie', 'set-cookie', 'x-api-key',
    'password', 'pass', 'pwd', 'user_pass', 'secret', 'client_secret', 'token', 'access_token', 'refresh_token',
    'api_key', 'apikey', 'key', 'iban', 'bic',
];

/**
 * @param array<mixed> $context log record context
 * @param bool $http HTTP channel: request body and response body dropped too
 * @return array<mixed>
 */
function redact_log_context(array $context, bool $http = false): array
{
    foreach ($context as $key => $value) {
        $name = is_string($key) ? strtolower($key) : '';

        if (in_array($name, LOG_SENSITIVE_KEYS, true)) {
            $context[$key] = LOG_REDACTED;
        } elseif ($http && in_array($name, ['body', 'response_body'], true) && '' !== $value && null !== $value) {
            $context[$key] = LOG_REDACTED;
        } elseif (is_array($value)) {
            $context[$key] = redact_log_context($value, $http);
        } elseif (is_string($value)) {
            // "Bearer xxx" / "Basic xxx" anywhere in a message part
            $context[$key] = preg_replace('/\b(Bearer|Basic)\s+[A-Za-z0-9._~+\/=-]+/i', '$1 ' . LOG_REDACTED, $value);
        }
    }

    return $context;
}

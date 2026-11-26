<?php

/* DEBUG */
// Public URL: errors never displayed, logged by Wonolog in log/ (WP_CUBI_LOG_DIR, see WONOLOG below)
ini_set('display_errors', 0);
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', false);
// Query Monitor lists the queries without it (its db.php drop-in): no query log kept in memory on each request
define('SAVEQUERIES', false);
define('SQL_CACHE_QUERIES', false);
define('WP_DISABLE_FATAL_ERROR_HANDLER', false);
define('WP_ENVIRONMENT_TYPE', 'staging');

/* QUERY MONITOR */
define('QM_DISABLE_ERROR_HANDLER', false);
define('QM_ENABLE_CAPS_PANEL', true);

/* SENTRY: PHP errors (web/app/mu-modules/00-wp-cubi-core-mu/src/01-sentry.php), environment = WP_ENV.
   The DSN is a public key (Sentry project > Settings > Client Keys); config/local.php may override it (empty string: disabled) */
if (!defined('SENTRY_DSN')) {
    define('SENTRY_DSN', '');
}

/* WONOLOG */
define('WP_CUBI_LOG_ENABLED', true);
define('WP_CUBI_LOG_LEVEL', 'DEBUG');
define('WP_CUBI_LOG_PHP_ERRORS', true);

/* MEMORY */
define('WP_MEMORY_LIMIT', '256M');

/* AUTOSAVE, REVISIONS, TRASH */
define('AUTOSAVE_INTERVAL', '300');
define('WP_POST_REVISIONS', 10);
define('MEDIA_TRASH', true);
define('EMPTY_TRASH_DAYS', '50');

/* HIDE ACF MENU */
define('ACF_LITE', true);

/* WP-CRON: run by the server crontab every minute (README.md, "wp-cron tasks"), the Coretik queue relies on it */
define('DISABLE_WP_CRON', true);
define('ALTERNATE_WP_CRON', false);
define('WP_CRON_LOCK_TIMEOUT', 60);

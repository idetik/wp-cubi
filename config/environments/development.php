<?php

/* DEBUG */
ini_set('display_errors', 1);
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
define('SCRIPT_DEBUG', true);
define('SAVEQUERIES', PHP_SAPI != 'cli');
define('SQL_CACHE_QUERIES', false);

/* QUERY MONITOR */
define('QM_DISABLE_ERROR_HANDLER', true);
define('QM_ENABLE_CAPS_PANEL', true);

/* WONOLOG */
define('WP_CUBI_LOG_ENABLED', true);
define('WP_CUBI_LOG_LEVEL', 'DEBUG');

/* MEMORY */
define('WP_MEMORY_LIMIT', '128M');

/* SECURITY */
define('FORCE_SSL_ADMIN', 'https' === WP_CUBI_CONFIG['WEB_SCHEME']);

/* AUTOSAVE, REVISIONS, TRASH */
define('AUTOSAVE_INTERVAL', '300');
define('WP_POST_REVISIONS', false);
define('MEDIA_TRASH', false);
define('EMPTY_TRASH_DAYS', '50');

/* HIDE ACF MENU */
define('ACF_LITE', false);

/* WP-CRON */
define('DISABLE_WP_CRON', false);
define('ALTERNATE_WP_CRON', PHP_SAPI != 'cli');
define('WP_CRON_LOCK_TIMEOUT', 60);

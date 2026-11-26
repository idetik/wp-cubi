<?php

// phpcs:ignoreFile -- WordPress tests bootstrap (Coretik testing kit, see vendor/idetik/coretik/README.md "Testing")

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Database emptied at each run: never a real one (WP_TESTS_DB_* environment variables in the CI)
\Coretik\Testing\Bootstrap::make(dirname(__DIR__, 2))
    ->database('wp_cubi_tests')
    ->wordpress('web/wpcb')
    ->content('web/app', 'web/app/modules')
    ->theme('starter')
    ->plugins('advanced-custom-fields/acf.php', 'acf-extended-pro/acf-extended.php')
    ->define('WP_ENV', 'testing')
    // The wp-cubi Wonolog configuration (log/) is a must-use module, not loaded by the tests
    ->define('WONOLOG_DISABLE', true)
    ->run();

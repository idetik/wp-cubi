<?php

namespace Globalis\WP\Cubi;

add_action('after_setup_theme', function () {

    $modules = [
        'clean-up',
        'disable-trackbacks',
        'nice-search',
        'disable-asset-versioning',
        'nav-walker',
        // 'js-to-footer',
    ];

    if (!\defined('WP_CLI') || !WP_CLI) {
        $modules[] = 'relative-urls';
    }

    add_theme_support('soil', $modules);
}, 10);

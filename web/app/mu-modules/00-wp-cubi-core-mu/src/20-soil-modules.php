<?php

namespace Globalis\WP\Cubi;

add_action('after_setup_theme', function () {

    $modules = [
        'clean-up',
        'disable-trackbacks',
        'nice-search',
        'relative-urls',
        'disable-asset-versioning',
        'nav-walker',
        //'js-to-footer',
        //'disable-rest-api',
    ];

    add_theme_support('soil', $modules);
}, 10);

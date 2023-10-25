<?php

namespace MyNamespace\Services\WP\Setup;

class ThemeSupport implements SetupInterface
{
    public function hooks()
    {
        \add_action('after_setup_theme', [__CLASS__, 'run']);
    }

    public static function run()
    {
        \add_theme_support('post-thumbnails');
        \add_theme_support('title-tag');
        \add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);
    }
}

<?php

namespace MyNamespace\Services\WP\Setup;

class ImageSize implements SetupInterface
{
    public function hooks()
    {
        \add_action('after_setup_theme', [__CLASS__, 'run']);
    }

    /**
     * Default:
     * thumbnail: 150x150
     * medium: 300x300
     * large: 1024x1024
     */
    public static function run()
    {
        // \set_post_thumbnail_size(623, 467);
        // \add_image_size('themetik-third--wide', 2560, 845, ['center', 'center']);
    }
}

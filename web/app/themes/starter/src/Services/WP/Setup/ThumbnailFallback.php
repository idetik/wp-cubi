<?php

namespace MyNamespace\Services\WP\Setup;

class ThumbnailFallback implements SetupInterface
{
    public function hooks()
    {
        \add_filter('coretik/postModel/thumbnail_fallback_id', [__CLASS__, 'run']);
        \add_action('acf/init', [__CLASS__, 'registerFields']);
    }

    public static function run($fallback)
    {
        $option = \get_field('thumbnail_fallback_id', 'options');
        if (!empty($option)) {
            $fallback = $option;
        }

        return $fallback;
    }

    public static function registerFields()
    {
        if (!\is_cli() && \is_admin() && !\wp_doing_ajax() && !\wp_doing_cron()) {
            $field = new FieldsBuilder('fallback_thumbnail', ['title' => 'Images par défaut', 'acfe_autosync' => ['php']]);
            $field->addImage('thumbnail_fallback_id', [
                'return_format' => 'id',
                'uploader' => 'wp',
                'acfe_thumbnail' => 0,
                'preview_size' => 'medium',
                'library' => 'all',
                'label' => 'Image des pages et articles par défaut',
            ]);

            $field
                ->setLocation('options_page', '==', 'app-options-mapping');
            \app()->get('acf.composer')->registerFields($field);
        }
    }
}

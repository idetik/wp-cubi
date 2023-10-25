<?php

namespace MyNamespace\Services\WP\Setup;

class Acf implements SetupInterface
{
    public function hooks()
    {
        \add_filter('acfe/flexible/prepend/template', [__CLASS__, 'templatePath'], 10, 3);
        \add_action('admin_init', function () {
            \add_filter('safe_style_css', [__CLASS__, 'allowAllStyle']);
        });
        $this->registerOptionsPages();

        /**
         * Composer
         */
        \add_action('acf/init', [__CLASS__, 'composerInstallOnAdminScreen']);
        \add_action('wp_ajax_acfe/flexible/layout_preview', [__CLASS__, 'composerInstall'], 5);


        if (\defined('WP_ENV') && 'development' === WP_ENV) {
            \add_action('coretik/service/acf-composer/after_update', [__CLASS__, 'replacePlaceholders']);
            \add_action('acf/init', [__CLASS__, 'composerUpdateOnAdminScreen'], 11);
        }
    }

    public static function templatePath($path, $field, $layout)
    {
        return 'File :';
    }

    protected function registerOptionsPages()
    {
        if (\function_exists('acf_add_options_page')) {
            $parent = \acf_add_options_page([
                'page_title' => 'Préférences',
                'menu_title' => 'Préférences',
                'menu_slug' => 'app-options',
                'capability' => 'edit_posts',
                'position' => '100',
                'parent_slug' => '',
                'icon_url' => 'dashicons-admin-settings',
                'redirect' => true,
                'post_id' => 'options',
                'autoload' => true,
                'update_button' => 'Mettre à jour',
                'updated_message' => 'Vos préférences ont été mises à jour',
            ]);

            \acf_add_options_sub_page([
                'page_title' => 'Préférences',
                'menu_title' => 'Préférences',
                'menu_slug' => 'app-options-settings',
                'capability' => 'edit_posts',
                'position' => '1',
                'parent_slug' => $parent['menu_slug'],
                'icon_url' => 'dashicons-admin-settings',
                'redirect' => true,
                'post_id' => 'options',
                'autoload' => true,
                'update_button' => 'Mettre à jour',
                'updated_message' => 'Vos préférences ont été mises à jour',
            ]);
        }
    }

    public static function allowAllStyle()
    {
        \add_filter('safe_style_css', '__return_empty_array');
    }

    public static function composerInstallOnAdminScreen()
    {
        if (!\is_cli() && \is_admin() && !\wp_doing_ajax() && !\wp_doing_cron()) {
            static::composerInstall();
        }
    }

    public static function composerUpdateOnAdminScreen()
    {
        if (!\is_cli() && \is_admin() && !\wp_doing_ajax() && !\wp_doing_cron()) {
            static::composerUpdate();
        }
    }

    public static function composerInstall()
    {
        \app()->get('acf.composer')->autoload();
        \app()->get('acf.composer')->install();
    }

    public static function composerUpdate()
    {
        \app()->get('acf.composer')->update();
    }

    public static function replacePlaceholders(): void
    {
        $config = [
            '<##ASSETS_URL##>' => "' . THEMETIK_ASSETS_URL . '",
            '<##WP_SITEURL##>' => "' . WP_SITEURL . '",
            '<##WP_HOME##>' => "' . WP_HOME . '",
        ];
        $path = \acf_get_setting('acfe/php_save');
        $from = \array_keys($config);
        $to = \array_values($config);

        foreach (\glob($path . '/*.php') as $file) {
            $text = \file_get_contents($file);
            $text = \str_replace($from, $to, $text, $count);
            if ($count > 0) {
                \file_put_contents($file, $text);
            }
        }
    }
}

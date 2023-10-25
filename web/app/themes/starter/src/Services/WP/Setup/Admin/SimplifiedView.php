<?php

namespace MyNamespace\Services\WP\Setup\Admin;

use StoutLogic\AcfBuilder\FieldsBuilder;
use MyNamespace\Services\WP\Setup\SetupInterface;

class SimplifiedView implements SetupInterface
{
    const FIELD = 'admin_simplified_view';

    public function hooks()
    {
        \add_action('admin_head', [__CLASS__, 'adminSimplifiedCss'], 999);
        \add_action('wp_head', [__CLASS__, 'adminSimplifiedCss'], 999);
        \add_action('admin_menu', [__CLASS__, 'adminSimplifiedAddMenus'], 999);
        // \add_action('admin_bar_menu', [__CLASS__, 'adminSimplifiedHideEnvironmentInfo'], 999);
        \add_action('user_has_cap', [__CLASS__, 'adminSimplifiedDisableQueryMonitor'], 999, 4);
        $this->registerFields();
    }

    public function registerFields()
    {
        $field = new FieldsBuilder('user_simplified_view', ['title' => __('Préférences tableau de bord', 'themetik'), 'acfe_autosync' => ['php']]);
        $field->addTrueFalse(static::FIELD, [
            'label' => __('Activer l\'affichage simplifié'),
            'ui' => 1,
        ]);

        $field
            ->setLocation('user_form', '==', 'all')
            ->and('user_role', '==', 'administrator');
        app()->get('acf.composer')->registerFields($field);
    }

    public static function isAdminSimplifiedView()
    {
        if (!\function_exists('get_field')) {
            return false;
        }
        if (!\is_admin() && !\is_admin_bar_showing()) {
            return false;
        }
        if (!\is_user_logged_in()) {
            return false;
        }
        if (\defined('DOING_AJAX') && DOING_AJAX) {
            return false;
        }
        if (true !== \get_field('admin_simplified_view', 'user_' . \get_current_user_id())) {
            return false;
        }
        return true;
    }

    public static function adminSimplifiedCss()
    {
        if (!static::isAdminSimplifiedView()) {
            return;
        }
        ?>
        <style type="text/css">
            #adminmenu #menu-media { display: none; }
            #adminmenu #menu-plugins { display: none; }
            #adminmenu #menu-tools { display: none; }
            #adminmenu #menu-appearance { display: none; }
            #adminmenu #toplevel_page_edit-post_type-acf-field-group { display: none; }
            #adminmenu #toplevel_page_theseoframework-settings { display: none; }
            #adminmenu #menu-settings { display: none; }
            #adminmenu #menu-users { display: none; }
            <?php if (!defined('THEMETIK_BLOG_ROOT')) : ?>
                #adminmenu #menu-comments { display: none; }
            <?php endif; ?>
            #wp-admin-bar-site-name #wp-admin-bar-themes { display:none; }
            #wp-admin-bar-new-content #wp-admin-bar-new-media { display: none; }
            #wp-admin-bar-new-content #wp-admin-bar-new-user { display: none; }
            #wp-admin-bar-new-content #wp-admin-bar-new-post { display: none; visibility: hidden; }
            #wp-admin-bar-sage_template { display: none; }
            #toplevel_page_itsec { display: none; }
            #adminmenu #toplevel_page_wp-mail-smtp { display: none; visibility: hidden; }
            .tsf-flex-inside-wrap .tsf-flex-nav-tab-inner #tsf-flex-nav-tab-visibility { display:none; visibility: hidden; }
            #wp-admin-bar-switch-off { display: none; visibility: hidden; }
            #toplevel_page_app-schema-viewer { display: none; visibility: hidden; }
        </style>
        <?php
    }

    public static function adminSimplifiedAddMenus()
    {
        if (!static::isAdminSimplifiedView()) {
            return;
        }
        \add_menu_page('Menus', 'Menus', 'edit_theme_options', 'nav-menus.php', '', 'dashicons-menu', 60);
    }

    public static function adminSimplifiedHideEnvironmentInfo($wp_admin_bar)
    {
        if (!static::isAdminSimplifiedView()) {
            return;
        }

        foreach ($wp_admin_bar->get_nodes() as $node) {
            if ('website-env' === $node->parent) {
                $wp_admin_bar->remove_node($node->id);
            }
        }
    }

    public static function adminSimplifiedDisableQueryMonitor($allcaps, $caps, $args, $user)
    {
        if (!isset($args[0]) || 'view_query_monitor' !== $args[0]) {
            return $allcaps;
        }

        if (!isset($args[1]) || true !== (bool)get_user_meta($args[1], 'admin_simplified_view', true)) {
            return $allcaps;
        }

        foreach ((array) $caps as $cap) {
            if (isset($allcaps[$cap])) {
                unset($allcaps[$cap]);
            }
        }

        return $allcaps;
    }
}

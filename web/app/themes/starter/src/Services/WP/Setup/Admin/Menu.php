<?php

namespace MyNamespace\Services\WP\Setup\Admin;

use MyNamespace\Services\WP\Setup\SetupInterface;

class Menu implements SetupInterface
{
    public function hooks()
    {
        \add_action('register_post_type_args', [__CLASS__, 'pagePosition'], 10, 2);
        \add_action('admin_menu', [__CLASS__, 'menuFrontPage']);
        \add_action('admin_head', [__CLASS__, 'menuFrontPageParent']);
        \add_action('admin_menu', [__CLASS__, 'removeTools']);
    }

    public static function pagePosition($args, $name)
    {
        if ('page' !== $name) {
            return $args;
        }
        $args['menu_position'] = 9;
        return $args;
    }

    public static function menuFrontPage()
    {
        global $menu;
        $menu[4] = [
            __('Page d\'accueil', 'themetik'),
            'edit_published_pages',
            'post.php?post=' . app()->map()->ids()->frontPage() . '&action=edit',
            '',
            'menu-top menu-front-page',
            'menu-links',
            'dashicons-admin-home'
        ];
    }

    public static function removeTools()
    {
        if (!current_user_can('manage_options')) {
            \remove_menu_page('tools.php');
        }
    }

    public static function menuFrontPageParent()
    {
        global $parent_file;

        if ('edit.php?post_type=page' !== $parent_file) {
            return;
        }

        $page_on_front_id = app()->map()->ids()->frontPage() ?: -99;

        if ($page_on_front_id !== intval(get_the_ID())) {
            return;
        }

        $parent_file = 'post.php?post=' . $page_on_front_id . '&action=edit';
    }
}

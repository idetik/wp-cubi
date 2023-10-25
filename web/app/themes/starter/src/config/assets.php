<?php

namespace MyNamespace\Config\Assets;

\add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_styles', 100);
\add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_scripts', 100);
\add_action('admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_scripts_admin', 100);
\add_action('admin_print_styles', __NAMESPACE__ . '\\enqueue_styles_admin', 11);
\add_action('admin_bar_init', __NAMESPACE__ . '\\fixAdminBarStyle', 11);

function enqueue_styles()
{
    \wp_enqueue_style('app/main', \app()->assets()->url('styles/main.css', ASSETS_VERSIONING_STYLES), [], null);
}

function enqueue_scripts()
{
    \app()->assets()->enqueueModularScript('main/esm', 'scripts/main.esm.min.js', ['jquery']);
    \app()->assets()->enqueueNoModularScript('main/iife', 'scripts/main.iife.min.js', ['jquery']);

    $AppData = [
        'ajax_url' => \admin_url('admin-ajax.php'),
    ];
    \wp_localize_script(\app()->assets()->family() . '/main/esm', 'AppData', $AppData);
    \wp_localize_script(\app()->assets()->family() . '/main/iife', 'AppData', $AppData);
}

function enqueue_scripts_admin()
{
    \app()->assets()->enqueueScript('admin', 'scripts/admin.iife.min.js');
    $AppData = [
        'ajax_url' => \admin_url('admin-ajax.php'),
    ];
    \wp_localize_script(\app()->assets()->family() . '/admin', 'AppData', $AppData);
}

function enqueue_styles_admin()
{
    $admin_handle = 'app/admin';
    $admin_stylesheet = \app()->assets()->url('styles/admin.css', ASSETS_VERSIONING_STYLES);
    \wp_enqueue_style($admin_handle, $admin_stylesheet);
}

function fixAdminBarStyle()
{
    ?>
    <style>
        @media screen and ( max-width: 782px ) {
            html { margin-top: 46px !important; }
        }
    </style>
    <?php
}

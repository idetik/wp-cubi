<?php

namespace Globalis\WP\Cubi;

/*
 * Disable dashboard browse-happy requests / widget
 */
if (is_blog_admin() && is_array($_SERVER) && isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])) {
    add_filter('pre_site_transient_browser_' . md5($_SERVER['HTTP_USER_AGENT']), '__return_empty_array');
}

/*
 * Disable legacy filter causing useless SQL queries
 */
add_filter('pre_option__split_terms', '__return_empty_array');

/*
 * Avoid useless SQL queries caused by script_concat_settings()
 */
add_action('widgets_init', __NAMESPACE__ . '\\declare_global_wp_concat_settings', 5, 1);

function declare_global_wp_concat_settings()
{
    if (is_admin()) {
        return;
    }

    global $concatenate_scripts, $compress_scripts, $compress_css;
    $concatenate_scripts = false;
    $compress_scripts    = false;
    $compress_css        = false;
}

/*
 * Disable get_option('theme_switched') useless queries (check autoload options only)
 */
add_filter('pre_option_theme_switched', __NAMESPACE__ . '\\get_option_autoload_theme_switched', 10, 1);

function get_option_autoload_theme_switched($default)
{
    $alloptions = wp_load_alloptions();

    if (isset($alloptions['theme_switched'])) {
        return $alloptions['theme_switched'];
    } else {
        return '';
    }
}

add_filter('pre_option_can_compress_scripts', '__return_zero');
remove_filter('theme_mod_custom_logo', '_override_custom_logo_theme_mod');

disable_php_version_check();

function disable_php_version_check()
{
    if (!is_admin()) {
        return;
    }
    $version = phpversion();
    $key = md5($version);
    add_filter('pre_site_transient_php_check_' . $key, function () {
        return ['is_acceptable' => true];
    });
}

add_filter('pre_site_transient_theme_roots', function () {
    return [WP_DEFAULT_THEME => "/themes"];
});

add_action('admin_init', function () {
    remove_action('admin_init', ['WP_Privacy_Policy_Content', 'text_change_check'], 100);
});

add_filter('pre_option_https_migration_required', function () {
    return '0';
});

add_filter('default_wp_template_part_areas', '__return_empty_array');
add_filter('default_template_types', '__return_empty_array');

/*
 * Options absent by default and autoloaded once written: read them from alloptions only.
 * Without this, each missing option costs one SQL query on every request.
 */
autoloaded_options_only([
    'db_upgraded'                  => '',
    'two_factor_enabled_providers' => null,
]);

function autoloaded_options_only(array $options)
{
    foreach ($options as $option => $absent) {
        add_filter('pre_option_' . $option, function ($pre, $option, $default_value) use ($absent) {
            $alloptions = wp_load_alloptions();

            if (isset($alloptions[$option])) {
                return maybe_unserialize($alloptions[$option]);
            }

            // pre_option only short-circuits on a value other than false
            return false !== $default_value ? $default_value : $absent;
        }, 10, 3);
    }
}

/*
 * Options read on every request but stored without autoload: autoload them on first read.
 */
force_options_autoload([
    'uninstall_plugins',
]);

function force_options_autoload(array $options)
{
    foreach ($options as $option) {
        add_filter('option_' . $option, function ($value, $option) {
            if (!isset(wp_load_alloptions()[$option])) {
                wp_set_option_autoload($option, true);
            }
            return $value;
        }, 10, 2);
    }
}

/*
 * Theme block patterns: skip the transient lookup when the theme has no patterns directory
 */
add_action('init', function () {
    if (!is_dir(get_stylesheet_directory() . '/patterns') && !is_dir(get_template_directory() . '/patterns')) {
        remove_action('init', '_register_theme_block_patterns');
    }
}, 0);

/*
 * Classic themes without theme.json have no user global styles: skip the wp_global_styles query.
 * The block editor and the REST API may create the global styles post, so they keep the query.
 */
add_filter('posts_pre_query', function ($posts, $wp_query) {
    if ('wp_global_styles' !== $wp_query->get('post_type')) {
        return $posts;
    }
    if (wp_is_block_theme() || wp_theme_has_theme_json() || wp_is_serving_rest_request()) {
        return $posts;
    }
    if (is_admin() && function_exists('get_current_screen') && get_current_screen()?->is_block_editor()) {
        return $posts;
    }
    return [];
}, 10, 2);

/*
 * Admin list tables call wp(), which matches the admin URL against the rewrite rules (and runs get_page_by_path()).
 * WP::parse_request() drops the matched permalink vars on admin requests: use rules that never match during parsing.
 */
add_filter('do_parse_request', function ($do_parse, $wp) {
    if (!is_admin() || wp_doing_ajax()) {
        return $do_parse;
    }

    $never_match = fn () => ['(?!)' => 'index.php'];
    add_filter('pre_option_rewrite_rules', $never_match);
    add_action('parse_request', function () use ($never_match) {
        remove_filter('pre_option_rewrite_rules', $never_match);
        $GLOBALS['wp_rewrite']->rules = null;
    });

    return $do_parse;
}, 10, 2);

/*
 * List screens enqueue the media modal (ACF Extended list forms) but rarely use it: skip the months dropdown query
 */
foreach (['load-edit.php', 'load-users.php', 'load-edit-tags.php'] as $list_screen) {
    add_action($list_screen, function () {
        add_filter('media_library_months_with_files', '__return_empty_array');
    });
}

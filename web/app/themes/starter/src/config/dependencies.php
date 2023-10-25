<?php

use Coretik\App;
use Coretik\Core\Container;
use Coretik\Services\Assets\Loader as AssetsLoader;
use Coretik\Services\AcfComposer\Composer as AcfComposer;
use Coretik\Services\Forms;
use Coretik\Services\Menu\Menu;
use Coretik\Navigation\Navigation;
use MyNamespace\Services\Mapping\Map;
use MyNamespace\Services\Normalizer\Normalize;
use MyNamespace\Services\TransientCache\Cache;
use MyNamespace\Services\Migration\Core\Manager as MigrationManager;
use MyNamespace\Services\Migration\Core\Launcher\Admin as AdminMigrationLauncher;
// use MyNamespace\Services\Migration\{
    
// };
// use MyNamespace\Services\Migration\Seed\{
// };

$container = new Container();

/**
 * Date, locale
 */
$container['timezone'] = fn ($container) => \wp_timezone();
// $container['carbon'] = fn ($container) => new Carbon\Factory([
//     'locale' => 'fr_FR',
//     'timezone' => \wp_timezone_string(),
// ]);

/**
 * Menus
 */
$container['menu'] = function ($container) {
    return Menu::make([
        'header' => 'Menu principal',
        'footer-1' => 'Pied de page 1',
        'footer-2' => 'Pied de page 2',
        'footer-3' => 'Pied de page 3',
    ]);
};

/**
 * Assets
 */
$container['assets'] = function ($container) {
    $version_path = get_theme_file_path('dist/version');
    if (!file_exists($version_path)) {
        $version = false;
    } else {
        $version = intval(file_get_contents($version_path));
    }
    return new AssetsLoader('dist/', $version, WP_DEFAULT_THEME);
};


$container['map'] = function ($container) {
    return new Map();
};

$container['navigation'] = function ($container) {
    return new Navigation($container);
};

/**
 * Forms
 */
$container['forms'] = function ($container) {
    $config = new Forms\Config();
    return new Forms\Core\Handler($config);
};
$container['forms.singletons'] = [
    //
];
$container['forms.factories'] = [
    //
];

/**
 * Emails
 */
$container['email.builder'] = $container->factory(function ($container) {
    return new \Coretik\Services\Email\Builder();
});

$container['email'] = function ($container) {
    $builder = $container->get('email.builder');
    $builder->setStylesheetPath($container->get('assets')->path('styles/email.css'))->setBasePath('templates/emails/base');
    return new \Coretik\Services\Email\Email(
        $builder,
        'templates/emails'
    );
};

/**
 * Socials Networks
 */
$container['share'] = function ($container) {
    return new \MyNamespace\Services\SocialsNetworks\Share($container);
};

/**
 * Faker
 */
$container['faker'] = function ($container) {
    return Coretik\Faker\Generator::get();
};

/**
 * SEO
 */
$container['seo'] = function ($container) {
    $seo = new MyNamespace\Services\Seo\Seo($container);
    $seo->module('Excerpt')->set('post_types', ['page', 'post']);
    $seo->module('NoIndex')->set('post_types', ['post']);
    // $seo->module('NoIndex')->set('templates', []);
    // $seo->module('StructuredData')->set('post_types', []);

    return $seo;
};

/**
 * Chrome
 */
if ('development' === WP_ENV && !empty(WP_CUBI_CONFIG['CHROME_PATH'])) {
    $container['chrome'] = function ($container) {
        $binary = WP_CUBI_CONFIG['CHROME_PATH'];
        $headlessChromer = new daandesmedt\PHPHeadlessChrome\HeadlessChrome();
        $headlessChromer->setBinaryPath($binary);
        return $headlessChromer;
    };
}

/**
 * WP
 */
$container['wp'] = function ($container) {
    return new MyNamespace\Services\WP\Wp($container);
};

/**
 * Page builder
 */
$container->extend('pageBuilder.blocks', function ($blocks, $c) {
    $blocks = $blocks->filter(fn ($block) => !in_array($block::NAME, [
        'containers.container',
    ]));

    $our_blocks = [
    ];

    foreach ($our_blocks as $class) {
        $blocks->push($class);
    }

    return $blocks;
});

$container['acf.composer'] = function ($container) {
    $theme_dir = 'src/admin/fields';

    // autoload child theme first then parent theme, in order to override fields
    $autoload = [];
    $current_theme_path = get_stylesheet_directory() . '/' . $theme_dir;
    if (is_dir($current_theme_path)) {
        $autoload[] = $current_theme_path;
    }

    // $parent_theme_path = get_parent_theme_file_path($theme_dir);
    // if ($parent_theme_path !== $current_theme_path) {
    //     $autoload[] = $parent_theme_path;
    // }

    return new AcfComposer($container, $autoload);
};

/**
 * Normalizer
 */
$container['normalizer'] = fn () => new Normalize();

/**
 * TransientCache
 */
$container['cache'] = fn () => new Cache();

 /**
 * Migrations
 */
$container['migrations'] = function ($container) {
    $manager = new MigrationManager();
    // $manager->add(new WpOptionMigration());
    return $manager;
};

$container['migrations.launcher'] = fn ($container) => new AdminMigrationLauncher($container->get('migrations'));

App::run($container);

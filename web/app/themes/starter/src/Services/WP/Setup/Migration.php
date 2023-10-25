<?php

namespace MyNamespace\Services\WP\Setup;

class Migration implements SetupInterface
{
    public function hooks()
    {
        \add_action('admin_init', [__CLASS__, 'load'], 5);
        \add_action('admin_init', [__CLASS__, 'launch']);
    }

    public static function load(): void
    {
        // app()->migrations()->add();
    }

    public static function launch(): void
    {
        app()->get('migrations.launcher')->launch();
    }
}

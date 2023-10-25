<?php

namespace MyNamespace\Services\WP\Setup;

class WpControl implements SetupInterface
{
    public function hooks()
    {
        \add_action('admin_head', [__CLASS__, 'style']);
        \remove_filter('cron_schedules', 'Crontrol\\filter_cron_schedules');
    }

    public static function style()
    {
        ?>
        <style type="text/css">
            form[action="options-general.php?page=crontrol_admin_options_page"] {
                display: none;
            }
        </style>
        <?php
    }
}

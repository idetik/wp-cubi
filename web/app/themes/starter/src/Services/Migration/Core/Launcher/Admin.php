<?php

namespace MyNamespace\Services\Migration\Core\Launcher;

use MyNamespace\Services\Migration\Core\Manager;

class Admin extends Launcher
{
    public function launch()
    {
        if (!\is_admin()) {
            return;
        }

        if (!\current_user_can('administrator')) {
            return;
        }

        if (!\array_key_exists('migration-request', $_GET ?? [])) {
            return;
        }

        if ('all' === $_GET['migration-request']) {
            $this->manager->handle($this->manager->keys());
            return;
        }

        $this->manager->handle($_GET['migration-request']);
    }
}

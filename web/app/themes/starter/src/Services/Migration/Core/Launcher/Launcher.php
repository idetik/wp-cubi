<?php

namespace MyNamespace\Services\Migration\Core\Launcher;

use MyNamespace\Services\Migration\Core\Manager;

abstract class Launcher
{
    protected Manager $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    abstract public function launch();
}

<?php

namespace MyNamespace\Services\Migration\Core;

use MyNamespace\Services\Migration\Core\MigrationI;
use MyNamespace\Services\Migration\Core\Manager;

abstract class Migration implements MigrationI
{
    const MIGRATION_KEY  = '';

    protected array $logs = [];

    abstract public function migrate();

    public function getKey(): string
    {
        return static::MIGRATION_KEY;
    }

    public function handleMigrate()
    {
        $this->migrate();
    }

    public function log(string $message, string $type = 'success'): self
    {
        $this->logs[] = [
            'message' => $message,
            'type' => $type
        ];

        return $this;
    }

    public function getLogs(): array
    {
        return $this->logs;
    }
}

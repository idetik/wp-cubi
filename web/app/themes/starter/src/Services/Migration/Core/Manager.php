<?php

namespace MyNamespace\Services\Migration\Core;

use MyNamespace\Services\Migration\Core\MigrationI;
use MyNamespace\Services\Migration\Core\MigrationErrorException;
use Coretik\Core\Utils\Arr;

class Manager
{
    protected array $migrations = [];
    protected array $keys = [];

    public function keys(): array
    {
        return \array_keys($this->migrations);
    }

    public function migrations(): array
    {
        return $this->migrations;
    }

    public function add(MigrationI $migration): self
    {
        $this->migrations[$migration->getKey()] = $migration;
        return $this;
    }

    public function handle(string|array $migrationKeys)
    {
        $migrations = array_intersect($this->keys(), Arr::wrap($migrationKeys));
        if (empty($migrations)) {
            return;
        }

        foreach ($migrations as $migrationKey) {
            $migration = $this->migrations[$migrationKey];
            try {
                $migration->migrate();
                static::log($migration->getKey(), '<b>Migration success</b>', 'success');
            } catch (MigrationErrorException $exception) {
                static::log($migration->getKey(), '<b>Migration failed</b> : ' . $exception->getMessage(), 'error');
            } finally {
                foreach ($migration->getLogs() as $log) {
                    static::log($migration->getKey(), $log['message'] ?? '', $log['type'] ?? 'info');
                }
            }
        }
    }

    public static function log(string $key, string $message, string $type = 'success')
    {
        app()->notices()->$type(sprintf('<span class="migration-tag">%s</span>%s', $key, $message));
    }
}

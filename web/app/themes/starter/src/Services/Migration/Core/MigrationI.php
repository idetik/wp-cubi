<?php

namespace MyNamespace\Services\Migration\Core;

interface MigrationI
{
    public function getKey(): string;
    public function log(string $message, string $type = 'success'): self;
    public function getLogs(): array;
    public function handleMigrate();
}

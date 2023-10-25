<?php

namespace MyNamespace\Services\TransientCache;

interface CachingTools
{
    public function get(string $file, array $data, string $group): string;
    public function echo(string $file, array $data, string $group): void;
}

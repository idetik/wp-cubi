<?php

namespace MyNamespace\Services\Seo\Modules;

use MyNamespace\Services\Seo\Seo;

abstract class Module implements ModuleInterface
{
    protected static $seo;
    protected $conf;

    abstract public function hooks();

    public function __construct(Seo $seo, array $conf = [])
    {
        if (empty(static::$seo)) {
            static::$seo = $seo;
        }
        $this->conf = $conf;
    }

    public function conf(string $key, $default = null)
    {
        return $this->conf[$key] ?? $default;
    }

    public function set(string $key, $value)
    {
        $this->conf[$key] = $value;
    }
}

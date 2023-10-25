<?php

namespace MyNamespace\Services\WP;

class Wp
{
    protected $app;
    protected static $setuped = [];

    public function __construct($app)
    {
        $this->app = $app;
    }

    protected function load($class)
    {
        if (!\in_array($class, static::$setuped)) {
            $reflectionClass = new \ReflectionClass($class);
            if ($reflectionClass->isInstantiable() && $reflectionClass->implementsInterface(__NAMESPACE__ . '\\Setup\\SetupInterface')) {
                (new $class($this->app))->hooks();
                static::$setuped[] = $class;
            }
        }
    }

    public function resolveFileInfo($fileInfo, $iterator, $class = '')
    {
        if (
            $fileInfo->isFile()
            && 'php' === $fileInfo->getExtension()
            && __FILE__ !== $fileInfo->getPathname()
        ) {
            $class .= \str_replace('.php', '', $fileInfo->getFileName());
            $this->load($class);
        } elseif ($fileInfo->isDir()) {
            $class .= $fileInfo->getFileName() . '\\';
            $subIterator = $iterator->getChildren();
            foreach ($subIterator as $sub) {
                $this->resolveFileInfo($sub, $subIterator, $class);
            }
        }
    }

    public function setup()
    {
        $class = __NAMESPACE__ . '\\Setup\\';
        $iterator = new \RecursiveDirectoryIterator(__DIR__  . '/Setup/', \RecursiveDirectoryIterator::SKIP_DOTS);

        foreach ($iterator as $fileInfo) {
            $this->resolveFileInfo($fileInfo, $iterator, $class);
        }
    }

    public function option(string $name)
    {
        switch ($name) {
            default:
                $classname = ucfirst($name);
                if (\file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'Options' . DIRECTORY_SEPARATOR . $classname . '.php')) {
                    $class = __NAMESPACE__ . '\\Options\\' . $classname;
                    return $class::instance()->get();
                }
                break;
        }
    }
}

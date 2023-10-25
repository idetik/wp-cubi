<?php

spl_autoload_register('app_autoloader');

function app_autoloader($class)
{
    $namespace = 'MyNamespace';

    if (strpos($class, $namespace) !== 0) {
        return;
    }

    $class = str_replace($namespace, '', $class);
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    $directory = get_stylesheet_directory();
    $path = $directory . DIRECTORY_SEPARATOR . 'src' . $class;

    if (file_exists($path)) {
        require_once($path);
    }
}

<?php

namespace MyNamespace\Services\WP\Options;

use Coretik\Core\Utils\Traits\Singleton;

abstract class Option
{
    use Singleton;

    abstract public function get();
}

<?php

namespace MyNamespace\Services\Mapping;

class Templates
{
    private $map;

    public function __construct(Map $map)
    {
        $this->map = $map;
    }

    public function get(string $key): string
    {
        $templatesMap = $this->map->getTemplatesMap();
        if (\array_key_exists($key, $templatesMap)) {
            return $templatesMap[$key];
        }

        return '';
    }

    /**
     * @param string $template : key
     */
    public function is(string $key): bool
    {
        if (!\is_page()) {
            return false;
        }

        if ($id = $this->map->id($key)) {
            return \is_page($id);
        }

        return \is_page_template($this->get($key));
    }

    /**
     * Get template file from key (camel case)
     * Example : Templates::login(), Templates::resetPassword()
     */
    public function __call($method, $args)
    {
        $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $method));
        return $this->get($snake);
    }
}

<?php

namespace MyNamespace\Services\WP\Options;

class SocialsNetworks extends Option
{
    protected $resource;
    protected $instance;

    protected function __construct()
    {
        $this->resource = \get_field('socials_networks', 'options') ?: [];
    }

    public function get()
    {
        $return = [];
        if (!\is_array($this->resource)) {
            return $return;
        }
        foreach ($this->resource as $item) {
            $return[$item['social_type']] = $this->toArray($item);
        }
        return $return;
    }

    /**
     * @return [
     *     'id',
     *     'name',
     *     'icon',
     *     'url',
     * ]
     */
    protected function toArray($item)
    {
        $return = [];
        switch ($item['social_type']) {
            case 'facebook':
            case 'twitter':
            case 'instagram':
                $return['name'] = ucfirst($item['social_type']);
                $return['icon'] = sprintf('<i class="icon-%s"></i>', $item['social_type']);
                $return['id'] = $item['social_type'];
                break;
            case 'linkedin':
                $return['name'] = 'LinkedIn';
                $return['icon'] = sprintf('<i class="icon-%s"></i>', $item['social_type']);
                $return['id'] = $item['social_type'];
                break;
            case 'other':
                $return['icon'] = sprintf('<i class="icon-%s"></i>', $item['icon']);
                $return['name'] = ucfirst($item['social_title']);
                $return['id'] = \mb_strtolower(\sanitize_title($item['social_type']));
                break;
        }
        $return['url'] = $item['social_url'];

        return $return;
    }
}

<?php

namespace MyNamespace\Services\Seo\Modules;

use function Globalis\WP\Cubi\is_frontend;

class NoIndex extends Module
{
    public function hooks()
    {
        \add_action('template_redirect', [$this, 'forceNoIndex'], 1);
        \add_filter('the_seo_framework_supported_post_type', [$this, 'disableSeoMetaboxForPostTypes'], 10, 2);
    }

    public function forceNoIndex()
    {
        if ($this->needNoIndex()) {
            \add_filter('wp_robots', 'wp_robots_no_robots');
        }
    }

    public function postTypes()
    {
        return $this->conf('post_types') ?? [];
    }

    public function templates()
    {
        return $this->conf('templates') ?? [];
    }

    protected function needNoIndex()
    {
        if (!is_frontend()) {
            return false;
        }

        if (\in_array(\get_post_type(), $this->postTypes())) {
            return true;
        }

        foreach ($this->templates() as $template) {
            if (\is_page_template($template)) {
                return true;
            }
        }

        return false;
    }

    public function sitemapExcludeIds()
    {
        static $ids;

        if (!is_array($ids)) {
            $query = new \WP_Query([
                'posts_per_page' => -1,
                'post_type'      => ['post', 'page'],
                'post_status'    => 'publish',
                'fields'         => 'ids',
                'meta_query'             => [
                    'relation' => 'OR',
                    [
                        'key' => '_genesis_noindex',
                        'compare' => '=',
                        'value' => '1',
                    ],
                    [
                        'key'     => '_wp_page_template',
                        'compare' => 'IN',
                        'value'   => $this->templates(),

                    ],
                ],
            ]);

            $ids = empty($query->posts) ? [] : $query->posts;
        }

        return $ids;
    }

    function disableSeoMetaboxForPostTypes($default, $post_type)
    {
        if (\in_array($post_type, $this->postTypes())) {
            return true;
        }
        return $default;
    }
}

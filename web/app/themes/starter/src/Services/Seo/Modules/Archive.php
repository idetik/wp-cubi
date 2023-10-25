<?php

namespace MyNamespace\Services\Seo\Modules;

use function Globalis\WP\Cubi\is_frontend;

class Archive extends Module
{
    public function hooks()
    {
        // \add_action('pre_get_posts', [$this, 'archivesSetPostsPerPage']);
        \add_filter('the_seo_framework_title_from_generation', [$this, 'archivesTitle'], 99, 2);
        \add_filter('the_seo_framework_generated_description', [$this, 'archivesDescription'], 99, 2);
    }

    public function archivesSetPostsPerPage($query)
    {
        if (!is_frontend()) {
            return;
        }
        if (!$query->is_main_query()) {
            return;
        }
        if (!isset($query->query_vars['post_type']) || 'post' !== $query->query_vars['post_type']) {
            return;
        }
        if (isset($query->query_vars['posts_per_page'])) {
            return;
        }
        $query->set('posts_per_page', '20');
    }

    public function getCurrentArchivesTitle($with_additions = false)
    {
        $title = static::$seo->app->get('navigation')->title();

        if ($with_additions) {
            $current_page = \is_paged() ? \intval(\get_query_var('paged', 0)) : 1;
            $pagination = $current_page > 1 ? sprintf(" (Page %d)", $current_page) : '';
            $title .= $pagination . " sur " . WP_DOMAIN;
        }

        return $title;
    }

    public function archivesTitle($default, $args)
    {
        if (!\is_tax() && !static::$seo->app->get('navigation')->isArchive() && !\is_home()) {
            return $default;
        }

        $title = $this->getCurrentArchivesTitle();

        if (empty($title)) {
            return $default;
        }

        return $title;
    }

    public function archivesDescription($default, $args)
    {
        if (!is_tax() && !is_archive() && !is_home()) {
            return $default;
        }

        $description = $this->getCurrentArchivesTitle(true);

        if (empty($description)) {
            return $default;
        }

        return $description;
    }
}

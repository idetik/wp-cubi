<?php

namespace MyNamespace\Services\Seo\Modules;

class MetaDescription extends Module
{
    public function hooks()
    {
        \add_filter('the_seo_framework_fetched_description_excerpt', [$this, 'forceGeneratedDescription'], 99, 3);
    }

    public function getDescription($post_id)
    {
        $seo_framework_description = $this->getSeoFrameworkUserDescription($post_id);

        if (is_string($seo_framework_description) && !empty($seo_framework_description)) {
            return self::cleanText($seo_framework_description);
        }

        $generated_description = $this->getGeneratedDescription($post_id);

        if (is_string($generated_description) && !empty($generated_description)) {
            return self::cleanText($generated_description);
        }

        return false;
    }

    public function getSeoFrameworkUserDescription($post_id)
    {
        $description = \get_post_meta($post_id, '_genesis_description', true);

        if (is_string($description) && !empty($description)) {
            return self::cleanText($description);
        }

        return false;
    }

    public function getGeneratedDescription($post_id)
    {
        try {
            $builder = static::$seo->app->get('schema')->get(\get_post_type($post_id), 'post');

            if (!empty($builder)) {
                $excerpt = $builder->model($post_id)->excerpt();

                if (is_string($excerpt) && !empty($excerpt)) {
                    return self::cleanText($excerpt);
                }
            }
        } catch (\Coretik\Core\Exception\ContainerValueNotFoundException $e) {
            //
        }

        $excerpt = \get_the_excerpt($post_id);

        if (is_string($excerpt) && !empty($excerpt)) {
            return self::cleanText($excerpt);
        }

        return false;
    }

    public function forceGeneratedDescription($default, $deprecated, $args)
    {
        if (null === $args && \Globalis\WP\Cubi\is_frontend() && is_singular()) {
            $args = [
                'id' => get_the_ID(),
            ];
        }

        if (!is_array($args)) {
            return self::cleanText($default);
        }

        if (!isset($args['id']) || empty($args['id'])) {
            return self::cleanText($default);
        }

        if (isset($args['taxonomy']) && !empty($args['taxonomy'])) {
            return self::cleanText($default);
        }

        $generated_description = $this->getGeneratedDescription($args['id']);

        if (false === $generated_description) {
            return self::cleanText($default);
        }

        return $generated_description;
    }

    protected static function cleanText($string)
    {
        return \trim(\wptexturize(\strip_tags(\nl2br($string))));
    }
}

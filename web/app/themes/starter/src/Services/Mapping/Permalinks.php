<?php

namespace MyNamespace\Services\Mapping;

use Coretik\Core\Exception\ContainerValueNotFoundException;

use function Globalis\WP\Cubi\get_permalink_by_template;

class Permalinks
{
    private $map;

    public function __construct(Map $map)
    {
        $this->map = $map;
    }

    public function home(): string
    {
        return \home_url('/');
    }

    // public static function latestPosts(): string
    // {
    //     return static::get('latest_posts');
    // }

    // public static function termX(): string
    // {
    //     return \get_term_link(Ids::termX());
    // }

    public function get(string $key): ?string
    {

        if (in_array($key, $this->map->map('posts'))) {

            /**
             * Resolve post permalink
             */
            $post_id = $this->map->id($key);
            if (empty($post_id)) {
                return null;
            }

            try {

                return \app()->schema(\get_post_type($post_id), 'post')->model($post_id)->permalink();

            } catch (ContainerValueNotFoundException $e) {

                return \get_permalink($post_id);

            }


        } elseif (in_array($key, $this->map->map('terms'))) {

            /**
             * Resolve term permalink
             */
            $term_id = $this->map->id($key);
            if (empty($term_id)) {
                return null;
            }

            $term = \get_term( $term_id );
            $taxonomy = $term->taxonomy;

            try {

                return \app()->schema($taxonomy, 'taxonomy')->model($term_id)->permalink();

            } catch (ContainerValueNotFoundException $e) {

                return \get_term_link($term, $taxonomy);

            }

        } elseif (in_array($key, $this->map->map('templates'))) {

            /**
             * Resolve template permalink
             * Seems not to be found in the posts map
             */
            return get_permalink_by_template($this->map->template($key));

        }

        return null;
    }

    /**
     * Get permalink from template key (camel case)
     * Example : Permalinks::login(), Permalinks::resetPassword()
     */
    public function __call($method, $args)
    {
        $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $method));
        return $this->get($snake);
    }
}

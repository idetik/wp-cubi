<?php

namespace MyNamespace\Services\Mapping;

use Coretik\Core\Utils\Str;
use StoutLogic\AcfBuilder\FieldsBuilder;

class Ids
{
    const ACF_OPTION_PATTERN = 'page_id_%s';

    private $map;

    public function __construct(Map $map)
    {
        $this->map = $map;
    }

    public function registerFields()
    {
        \acf_add_options_sub_page([
            'page_title'  => 'Mapping',
            'menu_title'  => 'Mapping',
            'menu_slug'  => 'app-options-mapping',
            'parent_slug' => 'app-options',
            'autoload' => true,
        ]);

        $field = new FieldsBuilder('map', ['title' => 'Pages', 'acfe_autosync' => ['php']]);

        foreach ($this->map->map() as $type => $key) {
            if (!in_array($type, ['posts', 'terms', 'users'])) {
                continue;
            }

            $fieldName = sprintf(static::ACF_OPTION_PATTERN, $key);
            switch ($type) {
                case 'posts':
                    $field->addPostObject($fieldName, [
                        'label' => Str::humanize($key),
                        'required' => 0,
                        'post_type' => ['page', 'post'],
                        'return_format' => 'id',
                        'save_custom' => 1,
                        'save_post_type' => 'page',
                        'save_post_status' => 'publish',
                    ]);
                    break;

                case 'terms':
                    $field->addField($fieldName, 'acfe_taxonomy_terms', [
                        'label' => Str::humanize($key),
                        'required' => 0,
                        // 'taxonomy' => [],
                        'field_type' => 'select',
                        'return_format' => 'id'
                    ]);
                    break;

                case 'users':
                    $field->addUser($fieldName, [
                        'label' => Str::humanize($key),
                        'required' => 0,
                        // 'role' => [],
                        'return_format' => 'id'
                    ]);
                    break;
            }
        }

        $field
            ->setLocation('options_page', '==', 'app-options-mapping');
        $this->map->app()->get('acf.composer')->registerFields($field);
    }

    public function frontPage(): int
    {
        return (int) get_option('page_on_front', false);
    }

    // public static function lastestPosts(): int
    // {
    //     return static::id('latest_posts');
    // }

    // public static function termX(): int
    // {
    //     return intval(get_field('term_id_X', 'options'));
    // }


    public function is(string $key): bool
    {
        $id = $this->get($key);
        if (empty($id)) {
            return false;
        }

        $current = \acfe_get_post_id();
        if (empty($current)) {
            return false;
        }

        $current = \acf_decode_post_id($current);

        if (in_array($key, $this->map->map('posts'))) {

            if ('post' !== $current['type']) {
                return false;
            }

            return $current['id'] === $id;

        } elseif (in_array($key, $this->map->map('terms'))) {
            if ('term' !== $current['type']) {
                return false;
            }

            return $current['id'] === $id;
        }

        return false;
    }

    public function get(string $key): int
    {
        return intval(\get_field(sprintf(static::ACF_OPTION_PATTERN, $key), 'options'));
    }

    /**
     * Get page ID from acf option (camel case)
     * Example : Ids::login(), Ids::resetPassword()
     */
    public function __call($method, $args)
    {
        $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $method));
        return $this->get($snake);
    }
}

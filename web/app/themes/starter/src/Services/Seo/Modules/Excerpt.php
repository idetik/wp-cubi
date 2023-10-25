<?php

namespace MyNamespace\Services\Seo\Modules;

class Excerpt extends Module
{
    public function hooks()
    {
        \add_action('wp_insert_post', [$this, 'updateExcerpt'], 99);
    }

    protected function postTypes()
    {
        return $this->conf('post_types') ?? [];
    }

    public function updateExcerpt($post_id)
    {
        if (!in_array(\get_post_type($post_id), $this->postTypes())) {
            return;
        }

        try {
            $model = static::$seo->app->get('schema')->type('post')->model($post_id);
        } catch (\Coretik\Core\Exception\ContainerValueNotFoundException $e) {
            return false;
        }

        \remove_action(\current_action(), [$this, __FUNCTION__], 99);

        $model->post_excerpt = '';
        $model->save();

        $description = static::$seo->module('MetaDescription')->getGeneratedDescription($post_id);

        if (empty($description)) {
            $description = '';
        }

        $model->post_excerpt = $description;
        $model->save();

        add_action(current_action(), [$this, __FUNCTION__], 99);
    }
}

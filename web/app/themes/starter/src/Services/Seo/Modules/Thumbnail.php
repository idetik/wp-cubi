<?php

namespace MyNamespace\Services\Seo\Modules;

use Coretik\Core\Exception\ContainerValueNotFoundException;

class Thumbnail extends Module
{
    public function hooks()
    {
        \add_filter('the_seo_framework_image_generation_params', [__CLASS__, 'imageGenerator'], 10, 3);
    }

    public static function imageGenerator($params, $args, $context)
    {
        if ('social' !== $context) {
            return $params;
        }

        if (null === $args) {
            if (is_singular() || is_tax()) {
                $params['cbs']['featured'] = [__CLASS__, 'getThumbnail'];
            }
        } else {
            if (! $args['taxonomy']) {
                $params['cbs']['featured'] = [__CLASS__, 'getThumbnail'];
            }
        }

        return $params;
    }

    public static function getThumbnail($args = null, $size = 'full')
    {
        $current_id = isset($args['id']) ? $args['id'] : \the_seo_framework()->get_the_real_ID();

        try {
            $model = match (true) {
                is_tax() => app()->schema(get_queried_object()?->taxonomy, 'taxonomy')->model($current_id),
                default => !empty($current_id) ? app()->schema(get_post_type($current_id), 'post')->model($current_id) : throw new ContainerValueNotFoundException(),
            };

            if (!method_exists($model, 'thumbnailUrl') || !method_exists($model, 'thumbnailId')) {
                throw new ContainerValueNotFoundException();
            }

            $url = $model->thumbnailUrl($size);
            $id = $model->thumbnailId();

            yield [
                'url' => $url,
                'id'  => $id,
            ];
        } catch (ContainerValueNotFoundException $e) {
            yield [
                'url' => '',
                'id'  => 0,
            ];
        }
    }
}

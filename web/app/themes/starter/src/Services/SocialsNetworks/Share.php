<?php

namespace MyNamespace\Services\SocialsNetworks;

use function Globalis\WP\Cubi\get_current_url;

class Share
{
    const BASE_FACEBOOK = 'https://www.facebook.com/sharer/sharer.php';
    const BASE_TWITTER = 'https://twitter.com/intent/tweet';
    const BASE_LINKEDIN = 'https://www.linkedin.com/shareArticle?mini=true';
    const BASE_PINTEREST = 'https://pinterest.com/pin/create/button/';

    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    protected static function cleanAndSafeText(string $text)
    {
        return \htmlspecialchars(\urlencode(\html_entity_decode($text, ENT_COMPAT, 'UTF-8')));
    }

    public function facebook($id = false)
    {
        if ($id) {
            try {
                $model = $this->container->get('schema')->type('post')->model($id);
                $link = $model->permalink();
            } catch (\Coretik\Core\Exception\ContainerValueNotFoundException $e) {
                $link = \get_the_permalink($id);
            }
        } else {
            $link = get_current_url();
        }

        $param_url = '?u=' . static::cleanAndSafeText($link);
        return self::BASE_FACEBOOK . $param_url;
    }

    public function twitter($id = false)
    {
        if (!$id) {
            $args['link']  = get_current_url();
            $args['title'] = $this->container->get('navigation')->title();
            $args['via']   = \get_bloginfo('name');
        } else {
            try {
                $model = $this->container->get('schema')->type('post')->model($id);
                $link = $model->permalink();
                $title = $model->title();
            } catch (\Coretik\Core\Exception\ContainerValueNotFoundException $e) {
                $link = \get_the_permalink($id);
                $title = $this->container->get('navigation')->title();
            }

            $args['link']  = $link;
            $args['title'] = $title;
            $args['via']   = \get_bloginfo('name');
        }

        $text = '?text=' . static::cleanAndSafeText($args['title']);
        $url = '&url=' . static::cleanAndSafeText($args['link']);
        $via = '&via=' . static::cleanAndSafeText($args['via']);

        return self::BASE_TWITTER . $text . $url . $via;
    }

    public function linkedin($id = false)
    {
        if (!$id) {
            $args['link']  = get_current_url();
            $args['title'] = $this->container->get('navigation')->title();
        } else {
            try {
                $model = $this->container->get('schema')->type('post')->model($id);
                $link = $model->permalink();
                $title = $model->title();
            } catch (\Coretik\Core\Exception\ContainerValueNotFoundException $e) {
                $link = \get_the_permalink($id);
                $title = $this->container->get('navigation')->title();
            }

            $args['link']  = $link;
            $args['title'] = $title;
        }

        $title = '&title=' . static::cleanAndSafeText($args['title']);
        $url = '&url=' . static::cleanAndSafeText($args['link']);

        return self::BASE_LINKEDIN . $title . $url;
    }

    public function pinterest($id = false)
    {
        if (!$id) {
            $args['link']  = get_current_url();
            $args['title'] = $this->container->get('navigation')->title();
            $args['media'] = \get_the_post_thumbnail_url();
        } else {
            try {
                $model = $this->container->get('schema')->type('post')->model($id);
                $link = $model->permalink();
                $title = $model->title();
                $media = $model->thumbnailUrl();
            } catch (\Coretik\Core\Exception\ContainerValueNotFoundException $e) {
                $link = \get_the_permalink($id);
                $title = $this->container->get('navigation')->title();
                $media = \get_the_post_thumbnail_url();
            }

            $args['link']  = $link;
            $args['title'] = $title;
            $args['media'] = $media;
        }

        $url   = '?url=' . static::cleanAndSafeText($args['link']);
        $media = '&media=' . static::cleanAndSafeText($args['media']);
        $desc  = '&description=' . static::cleanAndSafeText($args['title']);

        return self::BASE_PINTEREST . $url . $media . $desc;
    }

    public function mailto($id = false, $args = [])
    {
        if (!$id) {
            $id = \get_the_ID();
        }

        try {
            $model = $this->container->get('schema')->type('post')->model($id);
            $title = $model->title();
        } catch (\Coretik\Core\Exception\ContainerValueNotFoundException $e) {
            if (!isset($args['subject'])) {
                $args['subject'] = \rawurlencode(
                    \sprintf(
                        __('À lire sur %s : %s', 'themetik'),
                        \home_url(),
                        get_the_title($id)
                    )
                );
            }
            if (!isset($args['body'])) {
                $args['body'] = 'Lire l\'article : ' . get_permalink($id);
            }
            return "mailto:?subject=" . $args['subject'] . "&body=" . $args['body'];
        }

        if (!isset($args['subject'])) {
            $args['subject'] = \rawurlencode(
                \sprintf(
                    __('À lire sur %s : %s', 'themetik'),
                    \home_url(),
                    $title
                )
            );
        }

        if (!isset($args['body'])) {
            $header = \rawurlencode($title)
            . "%0D%0A" . "%0D%0A"
            . \rawurlencode("Publié le " . \get_the_date('d/m/Y', $id));

            if (\method_exists($model, 'category')) {
                $header = $header . rawurlencode(" dans la catégorie " . $model->category()->name)
                . "%0D%0A" . "%0D%0A";
            } else {
                $header = $header . "%0D%0A" . "%0D%0A";
            }

            $content = \rawurlencode($model->excerpt());
            $content = \nl2br($content);
            $content = \str_ireplace(['<p>', '</p>', '<br>', '<br/>', '<br />'], ['', "%0D%0A", "%0D%0A", "%0D%0A", "%0D%0A"], $content);
            $content = \wp_strip_all_tags($content);

            $footer = "%0D%0A" . "%0D%0A"
            . "-----------------------------------------------"
            . "%0D%0A" . "%0D%0A"
            . rawurlencode("Lire en entier : " . $model->permalink());

            $args['body'] = $header . $content . $footer;
        }

        $link = "mailto:?subject=" . $args['subject'] . "&body=" . $args['body'];
        return $link;
    }
}

<?php

namespace MyNamespace\Services\Seo\Modules;

class StructuredData extends Module
{
    public function hooks()
    {
        if (!defined('THE_SEO_FRAMEWORK_VERSION')) {
            return;
        }
        \add_filter('the_seo_framework_receive_json_data', [$this, 'fixStructuredDataOrganization'], 5, 2);
        \add_filter('the_seo_framework_do_after_output', [$this, 'outputStructuredDataItemlist'], 5);
        \add_filter('the_seo_framework_do_after_output', [$this, 'outputStructuredDataNewsArticles'], 5);
        \add_filter('the_seo_framework_amp_pro', [$this, 'outputStructuredDataNewsArticles'], 5);
    }

    public function postTypes()
    {
        return $this->conf('post_types') ?? [];
    }

    public function fixStructuredDataOrganization($data, $key)
    {
        if ("links" === $key) {
            $data = [
                "@context" => "https://schema.org",
            ];
            $data = \array_merge($data, static::getOrganization());
        }
        return $data;
    }

    protected static function outputJsonStructuredData($data)
    {
        if (empty($data)) {
            return;
        }

        $options  = 0;
        $options |= JSON_UNESCAPED_SLASHES;
        $options |= SCRIPT_DEBUG ? JSON_PRETTY_PRINT : 0;

        echo \sprintf('<script type="application/ld+json">%s</script>', \json_encode($data, $options)) . PHP_EOL;
    }

    public function outputStructuredDataItemlist()
    {
        if (!\is_tax() && !\is_archive() && !\is_home()) {
            return;
        }

        if (!\in_array(\get_post_type(), $this->postTypes())) {
            return;
        }

        $data = $this->getStructuredDataItemlist();

        static::outputJsonStructuredData($data);
    }

    public function outputStructuredDataNewsArticles()
    {
        if (!is_singular('post')) {
            return;
        }

        $data = $this->getStructuredDataNewsArticles();

        static::outputJsonStructuredData($data);
    }

    protected function getStructuredDataItemlist()
    {
        $data = [
            "@context" => "https://schema.org",
            "type" => "ItemList",
        ];

        $data["itemListElement"] = $this->getItemListElements();

        return $data;
    }

    protected function getItemListElements()
    {
        $list = [];
        $i = 1;
        while (\have_posts()) {
            \the_post();
            $list[] = [
                "@type" => "ListItem",
                "position" => $i++,
                "url" => \esc_url_raw(\get_permalink()),
            ];
        }
        return $list;
    }

    protected function getStructuredDataNewsArticles()
    {
        try {
            $model = static::$seo->app->get('schema')->type('post')->model(\get_queried_object_id());
        } catch (\Coretik\Core\Exception\ContainerValueNotFoundException $e) {
            return false;
        }

        if (false === static::getImages($model)) {
            return false;
        }

        $data = [
            "@context" => "https://schema.org",
            "type" => "NewsArticle",
        ];

        $data["mainEntityOfPage"] = static::getMainEntityOfPage();
        $data["headline"] = static::getHeadline($model);
        $data["image"] = static::getImages($model);
        $data["datePublished"] = static::getDatePublished($model);
        $data["dateModified"] = static::getDateModified($model);
        $data["author"] = static::getAuthors();
        $data["publisher"] = static::getOrganization();
        $data["description"] = static::getDescription($model);

        foreach ($data as $key => $value) {
            if (empty($value)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    protected static function getOrganization()
    {
        $tsf = \the_seo_framework();

        $publisher = [
            '@type' => 'Organization',
        ];

        $publisher["url"] = \esc_url_raw(\home_url('/'));
        $publisher["name"] = \esc_attr(static::sanitizeForJson($tsf->get_option('knowledge_name') ?: $tsf->get_blogname()));

        $image_id = (int) $tsf->get_option('knowledge_logo_id') ?: \get_option('site_icon');

        if (!empty($image_id)) {
            $image = \wp_get_attachment_image_src($image_id, "full");

            if (!empty($image)) {
                $publisher["logo"] = [
                    '@type'  => 'ImageObject',
                    'url'    => \esc_url_raw($image[0], ['https', 'http']),
                    'width'  => abs(filter_var($image[1], FILTER_SANITIZE_NUMBER_INT)),
                    'height' => abs(filter_var($image[2], FILTER_SANITIZE_NUMBER_INT)),
                ];
            }
        }

        $sameurls_options = (array) \apply_filters(
            'the_seo_framework_json_options',
            [
                'knowledge_facebook',
                'knowledge_twitter',
                'knowledge_gplus',
                'knowledge_instagram',
                'knowledge_youtube',
                'knowledge_linkedin',
                'knowledge_pinterest',
                'knowledge_soundcloud',
                'knowledge_tumblr',
            ]
        );

        $sameurls = [];
        foreach ($sameurls_options as $option_name) {
            $option_value = $tsf->get_option($option_name) ?: '';
            if ($option_value) {
                $sameurls[] = \esc_url_raw($option_value, ['https', 'http']);
            }
        }

        if (!empty($sameurls)) {
            $publisher["sameAs"] = $sameurls;
        }

        return $publisher;
    }

    protected static function getMainEntityOfPage()
    {
        return     [
            "@type" => "WebPage",
            "@id" => \esc_url_raw(\get_permalink()),
        ];
    }

    protected static function getHeadline($model)
    {
        $headline = $model->title();
        $headline = \trim($headline);
        $headline = \html_entity_decode($headline);
        $headline = \mb_strlen($headline) > 110 ? \mb_substr($headline, 0, 110) : $headline;
        $headline = \htmlentities($headline);
        $headline = static::sanitizeForJson($headline);
        return $headline;
    }

    protected static function sanitizeForJson($string)
    {
        $string = \str_replace(["\r", "\n"], ["", ""], $string);
        $string = \str_ireplace(["<br>", "<br />", "<br/>"], "<br>", $string);
        for ($i = 0; $i < 10; $i++) {
            $string = \str_replace("<br><br>", "<br>", $string);
        }
        $string = \str_replace(["<br>", "<br />"], " ", $string);
        $string = \strip_tags($string);
        $string = \wptexturize($string);
        $string = \convert_chars($string);
        $string = \esc_html($string);
        $string = \trim($string);

        return (string) $string;
    }

    protected static function getImages($model)
    {
        static $images = null;

        $min_width = 696;
        $thumbnail_sizes = [
            'post-thumbnail',
        ];

        $min_width_alt = 320;
        $thumbnail_sizes_alt = [
            'post-thumbnail',
        ];

        if (\is_null($images)) {
            $image_id = $model->thumbnailId();
            $images = static::getImagesDetails($image_id, $min_width, $thumbnail_sizes);
            if (empty($images)) {
                $images = static::getImagesDetails($image_id, $min_width_alt, $thumbnail_sizes_alt);
            }
            if (empty($images)) {
                $images = false;
            } else {
                $images = count($images) > 1 ? $images : reset($images);
            }
        }

        return $images;
    }

    protected static function getImagesDetails($image_id, $min_width, $thumbnail_sizes)
    {
        $images_sizes = [];
        $images = [];

        foreach ($thumbnail_sizes as $thumbnail_size) {
            $image_data = \wp_get_attachment_image_src($image_id, $thumbnail_size);
            if (empty($image_data)) {
                continue;
            }
            $image_url = \esc_url_raw($image_data[0], ['https', 'http']);
            $image_width = abs(filter_var($image_data[1], FILTER_SANITIZE_NUMBER_INT));
            $image_height = abs(filter_var($image_data[2], FILTER_SANITIZE_NUMBER_INT));
            $image_size = $image_width . 'x' . $image_height;

            if (!in_array($image_size, $images_sizes) && $image_width >= $min_width) {
                $images[] = [
                    '@type'  => 'ImageObject',
                    'url'    => $image_url,
                    'width'  => $image_width,
                    'height' => $image_height,
                ];
                $images_sizes[] = $image_size;
            }
        }

        return $images;
    }

    protected static function getDatePublished($model)
    {
        $i = strtotime($model->post_date_gmt);
        return \esc_attr(\gmdate('c', $i));
    }

    protected static function getDateModified($model)
    {
        $i = strtotime($model->post_modified_gmt);
        return \esc_attr(\gmdate('c', $i));
    }

    protected static function getAuthors()
    {
        $authors = [
            "@type" => "Person",
            "name" => static::sanitizeForJson(ucfirst(WP_DOMAIN)),
        ];

        return $authors;
    }

    protected static function getDescription($model)
    {
        return static::sanitizeForJson(static::$seo->module('MetaDescription')->getDescription($model->id()));
    }
}

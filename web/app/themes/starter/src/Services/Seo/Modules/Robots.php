<?php

namespace MyNamespace\Services\Seo\Modules;

class Robots extends Module
{
    public function hooks()
    {
        \add_action('template_redirect', [__CLASS__, 'metaRoboEucd'], 2, 1);
        \add_action('amp_post_template_head', [__CLASS__, 'metaRoboEucd'], 2, 1);
        \add_filter('the_seo_framework_robots_txt_pro', [__CLASS__, 'txtCustom'], 10, 1);
    }

    public static function metaRoboEucd()
    {
        \add_filter('wp_robots', function ($rules) {
            $rules['max-snippet'] = '-1';
            $rules['max-image-preview'] = 'large';
            $rules['max-video-preview'] = '-1';
            return $rules;
        });
    }

    public static function txtCustom($text)
    {
        $text .= "Disallow: /wp-login.php\r\n";
        $text .= "Disallow: /wp/wp-login.php\r\n";
        $text .= "Disallow: /*trackback\r\n";
        $text .= "Disallow: /*/comments\r\n";
        $text .= "Disallow: /cgi-bin\r\n";
        $text .= "Disallow: /*.php$\r\n";
        $text .= "Disallow: /*.inc$\r\n";
        $text .= "Disallow: /*.gz\r\n";
        $text .= "Disallow: /*.cgi\r\n";
        $text .= "\r\n";
        $text .= "# Google Image\r\n";
        $text .= "User-agent: Googlebot-Image\r\n";
        $text .= "Disallow:\r\n";
        $text .= "\r\n";
        $text .= "# Adsense\r\n";
        $text .= "User-agent: Mediapartners-Google\r\n";
        $text .= "Disallow:\r\n";
        return $text;
    }
}

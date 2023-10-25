<?php

namespace MyNamespace\Services\WP\Setup;

class Gdpr implements SetupInterface
{
    public function hooks()
    {
        \add_action('wp_loaded', [__CLASS__, 'removeGlobalisGdprAssets']);
        \add_action('wp_loaded', [__CLASS__, 'removeGlobalisGdprForms']);
        \add_action('wp_footer', [__CLASS__, 'script']);
    }

    public static function enabled()
    {
        return \apply_filters('themetik/services/globalis-gdpr/enabled', \class_exists('\Globalis\GDPR\Cookies'));
    }

    public static function removeGlobalisGdprAssets()
    {
        if (static::enabled()) {
            \remove_action('wp_enqueue_scripts', ['Globalis\\GDPR\\Forms', 'add_assets']);
            \remove_action('wp_enqueue_scripts', ['Globalis\\GDPR\\Cookies', 'front_assets']);

            global $post;    
            $postId = $post ? $post->ID : null;
            $privacyPageId = (int)get_option('wp_page_for_privacy_policy');

            $options = [
                'popin_dom' => \Globalis\GDPR\include_template('popin.php', [], true),
                'banner_dom' => \Globalis\GDPR\include_template('banner.php', [], true),
                'isPrivacyPage' => $privacyPageId === $postId,
            ];

            \add_action('wp_enqueue_scripts', function () use ($options) {
                wp_localize_script(app()->assets()->family() . '/main/esm', 'options', $options);
                wp_localize_script(app()->assets()->family() . '/main/iife', 'options', $options);
            }, 110);
        }
    }

    public static function removeGlobalisGdprForms()
    {
        if (static::enabled()) {
            \remove_action('admin_menu', ['Globalis\\GDPR\\Forms', 'menu']);
        }
    }

    public static function script()
    {
        if (static::enabled()) {
            ?>
            <script>
                function updateConsentTrigger() {
                    var cookie = JSON.parse($.getCookie('gdpr-allowance') || []);
                    if (!!cookie["cookies-a-des-fins-statistiques"]) {
                        updateGAConsent(true);
                    } else {
                        updateGAConsent(false);
                    }
                }

                document.addEventListener('gdprAcceptAll', updateConsentTrigger);
                document.addEventListener('gdprAccept', updateConsentTrigger);
            </script>
            <?php
        }
    }
}

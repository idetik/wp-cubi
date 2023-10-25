<?php

namespace MyNamespace\Services\WP\Setup;

class GoogleAnalytics implements SetupInterface
{
    const CONFIG = [
        'anonymize_ip' => true,
        'cookie_expires' => 34164000,
        'cookie_update' => false
    ];

    public function hooks()
    {
        \add_action('wp_head', [__CLASS__, 'gtag']);
    }

    public static function hasConsent()
    {
        return !\class_exists('\Globalis\GDPR\Cookies') || \Globalis\GDPR\Cookies::is_category_allowed('cookies-a-des-fins-statistiques');
    }

    public static function gaEnabled()
    {
        return WP_ENV === 'production' && defined('APP_GOOGLE_ANALYTICS') && !current_user_can('manage_options');
    }

    protected static function config()
    {
        return apply_filters('coretik/services/wp/setup/google_analytics/config', static::CONFIG);
    }

    public static function logTag(string $message, bool $showOnProduction = false)
    {
        if (WP_ENV === 'production' && !$showOnProduction) {
            return;
        }
        ?>
        <script type="text/javascript">
            <?= static::log($message, $showOnProduction); ?>
        </script>
        <?php
    }

    public static function log(string $message, bool $showOnProduction = false)
    {
        if (WP_ENV === 'production' && !$showOnProduction) {
            return;
        }
        printf("console.log('%%c[GTM]', \"font-weight: bold;background: yellow; color: black; padding:3px 5px\" , \"%s\")", $message);
    }

    public static function gtag()
    {
        if (!static::gaEnabled()) {
            static::logTag('disabled', true);

            ?>
            <script>
                function updateGAConsent(consent) {
                    if (consent) {
                        <?php static::log('Consent update: granted'); ?>
                    } else {
                        <?php static::log('Consent update: denied') ?>
                    }
                }
            </script>
            <?php
            return;
        }
        ?>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <link rel="preconnect" href="https://www.google-analytics.com">
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= APP_GOOGLE_ANALYTICS ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            function updateGAConsent(consent) {
                if (consent) {
                    gtag('consent', 'update', {
                        'ad_storage': 'granted',
                        'analytics_storage': 'granted',
                    });
                    <?php static::log('Consent update: granted'); ?>
                } else {
                    gtag('consent', 'update', {
                        'ad_storage': 'denied',
                        'analytics_storage': 'denied',
                    });
                    <?php static::log('Consent update: denied') ?>
                }
            }

            gtag('consent', 'default', {
                'ad_storage': 'denied',
                'analytics_storage': 'denied'
            });

            gtag('js', new Date());
            gtag('config', '<?= APP_GOOGLE_ANALYTICS ?>', <?= json_encode(static::config(), JSON_PRETTY_PRINT) ?>);
        </script>
        <?php
    }
}


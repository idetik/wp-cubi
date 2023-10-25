<?php

namespace MyNamespace\Services\WP\Setup;

class Axeptio implements SetupInterface
{
    public function hooks()
    {
        \add_action('wp_footer', [__CLASS__, 'axeptio']);
    }

    public static function enabled()
    {
        return defined('APP_AXEPTIO_ID') && defined('APP_AXEPTIO_VERSION');
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
        printf("console.log('%%c[Axeptio]', \"font-weight: bold;background: orange; color: white; padding:3px 5px\" , \"%s\")", $message);
    }

    public static function axeptio()
    {
        if (!static::enabled()) {
            static::logTag('disabled', true);
            return;
        }
        ?>
        <script>
            window.axeptioSettings = {
                clientId: "<?= APP_AXEPTIO_ID ?>",
                cookiesVersion: "<?= APP_AXEPTIO_VERSION ?>",
            };
            (function(d, s) {
                var t = d.getElementsByTagName(s)[0], e = d.createElement(s);
                e.async = true; e.src = "//static.axept.io/sdk.js";
                t.parentNode.insertBefore(e, t);
            })(document, "script");

            void 0 === window._axcb && (window._axcb = []);
            window._axcb.push(function(axeptio) {
                axeptio.on("cookies:complete", function(choices) {
                    if(choices.google_analytics) {
                        updateGAConsent(true);
                    } else {
                        updateGAConsent(false);
                    }
                });
            });
        </script>
        <?php
    }
}


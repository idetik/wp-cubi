<?php

use Globalis\WP\Cubi\Robo\BuildTrait;
use Globalis\WP\Cubi\Robo\DeployTrait;
use Globalis\WP\Cubi\Robo\GitTrait;
use Globalis\WP\Cubi\Robo\InstallTrait;
use Globalis\WP\Cubi\Robo\WordPressTrait;
use Idetik\Robo\BuildAssetsTrait;
use Idetik\Robo\GenerateBlocksThumbnailTrait;

class RoboFile extends \Globalis\WP\Cubi\Robo\RoboFile
{
    use BuildTrait;
    use DeployTrait;
    use GitTrait;
    use InstallTrait {
        install as traitInstall;
    }
    use WordPressTrait {
        wpUrl as wpUrlInherited;
    }
    use BuildAssetsTrait;
    use GenerateBlocksThumbnailTrait;

    const ROOT                          = __DIR__;

    const PATH_DIRECTORY_CONFIG         = 'config';
    const PATH_DIRECTORY_WEB            = 'web';
    const PATH_DIRECTORY_MEDIA          = 'web/media';
    const PATH_DIRECTORY_LOG            = 'log';

    const PATH_FILE_WP_CLI_EXECUTABLE   = __DIR__ . '/vendor/bin/wp';
    const PATH_FILE_PROPERTIES          = __DIR__ . '/.robo/properties.php';
    const PATH_FILE_PROPERTIES_REMOTE   = __DIR__ . '/.robo/properties.remote.php';
    const PATH_FILE_CONFIG_VARS         = 'config/vars.php';
    const PATH_FILE_CONFIG_VARS_REMOTE  = 'config/vars.%s.php';
    const PATH_FILE_CONFIG_APPLICATION  = 'config/application.php';
    const PATH_FILE_CONFIG_LOCAL        = 'config/local.php';
    const PATH_FILE_CONFIG_LOCAL_SAMPLE = 'config/local.sample.php';
    const PATH_FILE_CONFIG_SALT_KEYS    = 'config/salt-keys.php';

    const PATH_FILES_BUILD_ASSETS       = [];

    const HTACCESS_BUILD                = 'web/.htaccess';
    const HTACCESS_CONFIG_DIRECTORY     = 'config/htaccess';
    const HTACCESS_PARTS                = [
        'htaccess-general',
        'htaccess-seo',
        'htaccess-performances',
        'htaccess-redirect',
        'htaccess-security',
        'htaccess-urls',
        'htaccess-wp-permalinks',
    ];

    const PATH_VENDORS = [
        '/vendor',
        '/web/wpcb',
        '/web/app/modules',
    ];

    const CONFIRM_CONFIG_BEFORE_DEPLOY  = true;
    const THEME_SLUG  = 'starter';

    protected function wpUrl()
    {
        $scheme = $this->getConfig('development', 'WEB_SCHEME');
        $domain = $this->getConfig('development', 'WEB_DOMAIN');
        $path   = $this->getConfig('development', 'WEB_PATH');
        return $scheme . '://' . $domain . $path . '/wpcb';
    }

    public function install($options = ['setup-wordpress' => false])
    {
        $this->traitInstall($options);

        if ($this->io()->confirm('Would you like to generate page builder blocks thumbnails ?', false)) {
            $this->generateThumbs();
        } else {
            return;
        }
    }
}

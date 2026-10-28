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
    use DeployTrait {
        deploy as protected traitDeploy;
    }
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
    const SEED_LANGUAGE = 'fr_FR';
    // Theme migrations run by deploy:seed (wp app migrate <key>...)
    const SEED_MIGRATIONS = ['initial-seeder'];

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

    public function deploy($environment, $gitRevision, $options = ['ignore-assets' => false, 'ignore-composer' => false])
    {
        $deployed = $this->traitDeploy($environment, $gitRevision, $options);

        // First deployment: an empty database must be installed right away (install.php is public until then)
        if ($deployed && !$this->remoteWpInstalled($environment)) {
            $this->io()->warning(sprintf('WordPress is not installed on %s yet.', $environment));
            if ($this->io()->confirm('Install and seed the database now (deploy:seed) ?', true)) {
                $this->deploySeed($environment);
            }
        }

        return $deployed;
    }

    /**
     * First database of a remote environment: WordPress install, theme, plugins, language, then the theme migrations
     * of SEED_MIGRATIONS (wp app migrate). Run after deploy:setup and deploy. Never on an installed WordPress.
     */
    public function deploySeed($environment)
    {
        $this->io()->title('Seed the database of ' . $environment);
        $config = $this->getConfig($environment);

        if ($this->remoteWpInstalled($environment)) {
            throw new \RuntimeException(sprintf('WordPress is already installed on %s: its database is kept.', $environment));
        }

        $title    = $this->io()->ask('Site title');
        $username = $this->io()->ask('Admin username');
        $email    = $this->io()->ask('Admin email', $config['DEV_MAIL'] ?? null);
        $password = $this->io()->askHidden('Admin password');

        // The password goes through stdin (--prompt), not the remote command line
        $this->remoteWp($environment, sprintf(
            'core install --url=%s --title=%s --admin_user=%s --admin_email=%s --skip-email --prompt=admin_password',
            escapeshellarg($config['WEB_SCHEME'] . '://' . $config['WEB_DOMAIN'] . $config['WEB_PATH'] . '/wpcb'),
            escapeshellarg($title),
            escapeshellarg($username),
            escapeshellarg($email)
        ), $password . PHP_EOL);

        $this->remoteWp($environment, 'theme activate ' . \RoboFile::THEME_SLUG);
        $this->remoteWp($environment, 'plugin activate --all');
        $this->remoteWp($environment, 'cap add administrator view_query_monitor');

        // Translations are versioned (web/app/languages) and deployed: downloaded only when missing
        if (!$this->remoteWp($environment, 'language core activate ' . static::SEED_LANGUAGE, null, false)->isSuccessful()) {
            $this->io()->warning('Translations are missing from web/app/languages: commit them, the next deploy deletes them.');
            $this->remoteWp($environment, 'language core install --activate ' . static::SEED_LANGUAGE);
        }

        if (static::SEED_MIGRATIONS) {
            $this->remoteWp($environment, 'app migrate ' . implode(' ', array_map('escapeshellarg', static::SEED_MIGRATIONS)));
        }

        $this->io()->success(sprintf('Database of %s is ready.', $environment));
    }

    protected function remoteWpInstalled($environment): bool
    {
        $process = $this->remoteWp($environment, 'core is-installed', null, false);

        // 1: not installed; anything else (SSH 255, PHP error…) must not be taken for an empty database
        if (!in_array($process->getExitCode(), [0, 1], true)) {
            throw new \RuntimeException(sprintf('Could not check the WordPress install of %s.', $environment));
        }

        return $process->isSuccessful();
    }

    /**
     * WP-CLI of the deployed application (vendor/bin/wp), through SSH
     */
    protected function remoteWp($environment, string $command, ?string $input = null, bool $mustRun = true): \Symfony\Component\Process\Process
    {
        $config = $this->getConfig($environment);
        $remote = sprintf('cd %s && vendor/bin/wp %s', escapeshellarg($config['REMOTE_PATH']), $command);

        $process = new \Symfony\Component\Process\Process([
            'ssh', '-p', (string) $config['REMOTE_PORT'], $config['REMOTE_USERNAME'] . '@' . $config['REMOTE_HOSTNAME'], $remote,
        ]);
        $process->setTimeout(600);
        $process->setInput($input);

        $this->io()->text('<info>wp ' . $command . '</info>');
        $process->run(fn ($type, $buffer) => $this->output()->write($buffer));

        if ($mustRun && !$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('Remote command failed: wp %s', $command));
        }

        return $process;
    }
}

<?php

namespace MyNamespace\Services\Seo;

class Seo
{
    const MODULES = [
        'Archive',
        'Excerpt',
        'MetaDescription',
        'NoIndex',
        'Permalinks',
        'Robots',
        'StructuredData',
        'Thumbnail',
    ];

    protected $modules = [];
    protected $modulesLoaded = [];
    protected static $modulesHooked = [];
    public $app;

    protected $defaultSettings;

    public function __construct($app, $selectedModules = null)
    {
        $this->app = $app;

        if (!is_null($selectedModules) && \is_array($selectedModules)) {
            $this->modules = \array_intersect(self::MODULES, $selectedModules);
        } else {
            $this->modules = self::MODULES;
        }

        foreach ($this->modules as $module) {
            $this->load($module);
        }
    }

    protected function load($module)
    {
        $class = __NAMESPACE__ . '\\Modules\\' . $module;
        $this->modulesLoaded[$module] = new $class($this);
    }

    public function hooks()
    {
        \add_filter('the_seo_framework_default_site_options', [$this, 'defaultSiteOptions']);
        \add_action('init', [$this, 'forceKnowledgesOption']);

        foreach ($this->modulesLoaded as $moduleName => $moduleInstance) {
            if (\apply_filters("coretik/services/seo/{$moduleName}/enabled", true) && !\in_array($moduleName, static::$modulesHooked)) {
                $moduleInstance->hooks();
                static::$modulesHooked[] = $moduleName;
            }
        }
    }

    public function module(string $name)
    {
        if (empty($this->modulesLoaded[$name])) {
            if (\array_key_exists($name, static::MODULES)) {
                $this->load($name);
            }
            return null;
        }

        return $this->modulesLoaded[$name];
    }

    public function defaultSiteOptions($options)
    {
        if (!isset($this->defaultSettings)) {
            $options['alter_search_query'] = 0;
            $options['alter_archive_query'] = 0;

            //@todo deprecated ?
            $socials = $this->app->get('wp')->option('socialsNetworks');
            if (!empty($socials)) {
                foreach ($socials as $social) {
                    switch ($social['id']) {
                        case 'facebook':
                            $options['knowledge_facebook'] = $social['url'];
                            break;
                        case 'instagram':
                            $options['knowledge_instagram'] = $social['url'];
                            break;
                        case 'twitter':
                            $options['knowledge_twitter'] = $social['url'];
                            break;
                        case 'pinterest':
                            $options['knowledge_pinterest'] = $social['url'];
                            break;
                        case 'youtube':
                            $options['knowledge_youtube'] = $social['url'];
                            break;
                        case 'linkedin':
                            $options['knowledge_linkedin'] = $social['url'];
                            break;
                        case 'soundcloud':
                            $options['knowledge_soundcloud'] = $social['url'];
                            break;
                    }
                }
            }

            $this->defaultSettings = $options;
        }

        return $this->defaultSettings;
    }

    public function forceKnowledgesOption()
    {
        $options = [];
        $socials = $this->app->get('wp')->option('socialsNetworks');
        if (!empty($socials)) {
            foreach ($socials as $social) {
                switch ($social['id']) {
                    case 'facebook':
                        $options['knowledge_facebook'] = $social['url'];
                        break;
                    case 'instagram':
                        $options['knowledge_instagram'] = $social['url'];
                        break;
                    case 'twitter':
                        $options['knowledge_twitter'] = $social['url'];
                        break;
                    case 'pinterest':
                        $options['knowledge_pinterest'] = $social['url'];
                        break;
                    case 'youtube':
                        $options['knowledge_youtube'] = $social['url'];
                        break;
                    case 'linkedin':
                        $options['knowledge_linkedin'] = $social['url'];
                        break;
                    case 'soundcloud':
                        $options['knowledge_soundcloud'] = $social['url'];
                        break;
                }
            }
        }

        foreach ($options as $opt => $value) {
            \add_filter('pre_option_' . $opt, function ($default) use ($value) {
                return $value;
            });
        }
    }
}

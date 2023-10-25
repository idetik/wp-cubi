<?php

namespace MyNamespace\Services\Mapping;

use Coretik\Core\Container as App;
use MyNamespace\Services\Mapping\Ids;
use MyNamespace\Services\Mapping\Permalinks;
use MyNamespace\Services\Mapping\Templates;

class Map
{
    protected $app;
    protected array $postEntries = [];
    protected array $templateEntries = [];
    protected array $termEntries = [];
    protected array $userEntries = [];

    private $templates;
    private $ids;
    private $permalinks;

    public function __construct()
    {
    }

    public static function make(): self
    {
        return new self();
    }

    public function setApp(App $app): self
    {
        $this->app = $app;
        return $this;
    }

    /**
     * Add page template to the map
     * Create an acf option field to choose the corresponding post id
     */
    public function addPageWithTemplate(string $key, $template): self
    {
        $this->addAnonymousPost($key);
        $this->addTemplateEntry($key, $template);
        return $this;
    }

    /**
     * Add an anonymous post object to the map, referred by this key
     * Create an acf option field to choose the corresponding post id
     */
    public function addAnonymousPost(string $key): self
    {
        return $this->addPostEntry($key);
    }

    /**
     * Add an anonymous term object to the map, referred by this key
     * Create an acf option field to choose the corresponding term id
     */
    public function addAnonymousTerm(string $key): self
    {
        return $this->addTermEntry($key);
    }

    /**
     * Add an anonymous object to the map, referred by this key
     * Create an acf option field to choose the corresponding post id
     */
    public function addAnonymousUser(string $key): self
    {
        return $this->addUserEntry($key);
    }

    protected function addPostEntry(string $key): self
    {
        if (!in_array($key, $this->postEntries)) {
            $this->postEntries[] = $key;
        }
        return $this;
    }

    protected function addTermEntry(string $key): self
    {
        if (!in_array($key, $this->termEntries)) {
            $this->termEntries[] = $key;
        }
        return $this;
    }

    protected function addUserEntry(string $key): self
    {
        if (!in_array($key, $this->userEntries)) {
            $this->userEntries[] = $key;
        }
        return $this;
    }

    protected function addTemplateEntry(string $key, string $templateName): self
    {
        $this->templateEntries[$key] = $templateName;
        return $this;
    }

    public function enqueueRegisterFields(): self
    {
        \add_action('acf/init', function () {
            if (!\is_cli() && \is_admin() && !\wp_doing_ajax() && !\wp_doing_cron()) {
                $this->registerFields();
            }
        });
        return $this;
    }

    public function registerFields()
    {
        $this->ids()->registerFields();
    }

    public function map($key = false)
    {
        switch ($key) {
            case 'posts':
                return $this->getPostsMap();
            case 'terms':
                return $this->getTermsMap();
            case 'templates':
                return $this->getTemplatesMap();
            case 'users':
                return $this->getUsersMap();
            default:
                return [
                    'posts' => $this->getPostsMap(),
                    'terms' => $this->getTermsMap(),
                    'templates' => $this->getTemplatesMap(),
                    'users' => $this->getUsersMap(),
                ];
        }
    }

    public function getPostsMap(): array
    {
        return $this->postEntries;
    }

    public function getTermsMap(): array
    {
        return $this->termEntries;
    }

    public function getTemplatesMap(): array
    {
        return $this->templateEntries;
    }

    public function getUsersMap(): array
    {
        return $this->userEntries;
    }

    public function app()
    {
        return $this->app ?? \app();
    }

    public function is($page): bool
    {
        switch (true) {
            case \is_int($page):
                return $this->ids()->is($page);
            // case \filter_var($page, FILTER_VALIDATE_URL):
            //     return $this->permalinks()->is($page);
            case \is_string($page):
                return $this->templates()->is($page);
            default:
                return false;
        }
    }

    /**
     * Get the post_id mapped to the key
     */
    public function id($key): int
    {
        return $this->ids()->get($key);
    }

    /**
     * Get the template name mapped to the key
     */
    public function template($key): string
    {
        return $this->templates()->get($key);
    }

    /**
     * Get the permalink mapped to the key
     */
    public function permalink($key): string
    {
        return $this->permalinks()->get($key);
    }

    public function ids(): Ids
    {
        if (!isset($this->ids)) {
            $this->ids = new Ids($this);
        }
        return $this->ids;
    }

    public function templates(): Templates
    {
        if (!isset($this->templates)) {
            $this->templates = new Templates($this);
        }
        return $this->templates;
    }

    public function permalinks()
    {
        if (!isset($this->permalinks)) {
            $this->permalinks = new Permalinks($this);
        }
        return $this->permalinks;
    }
}

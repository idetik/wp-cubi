<?php

namespace MyNamespace\Services\TransientCache\Tools;

use MyNamespace\Services\TransientCache\CachingTools;
use Globalis\WP\Cubi\TransientCache\Cache;

use function Globalis\WP\Cubi\include_template_part;

class IdentifiableObject implements CachingTools
{
    private int|string $objectId;

    public function __construct(null|int|string $id = null)
    {
        if (!empty($id)) {
            $this->from($id);
        }
    }

    public function from(null|int|string $objectId): self
    {
        $this->objectId = $objectId;
        return $this;
    }

    public function get(string $file, array $data = [], string $group = 'all'): string
    {
        if (empty($this->objectId) || defined('WP_CUBI_TRANSIENT_CACHE_BYPASS_TEMPLATES') && WP_CUBI_TRANSIENT_CACHE_BYPASS_TEMPLATES) {
            $value = include_template_part($file, $data, true);
        } else {
            $key = $this->objectId . '_' . $file;

            $value = Cache::get($key, $group);

            if (!$value) {
                $value = include_template_part($file, $data, true);
                \add_filter('expiration_of_transient_' . $key, fn () => DAY_IN_SECONDS);
                Cache::set($key, $value, $group);
            }
        }

        return $value;
    }

    public function echo(string $file, array $data = [], string $group = 'all'): void
    {
        echo $this->get($file, $data, $group);
    }
}

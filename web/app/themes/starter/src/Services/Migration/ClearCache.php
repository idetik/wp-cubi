<?php

namespace MyNamespace\Services\Migration;

use MyNamespace\Services\Migration\Core\Migration;
use Globalis\WP\Cubi\TransientCache\Cache;

class ClearCache extends Migration
{
    /**
     * ?migration-request=clear-cache
     */
    const MIGRATION_KEY  = 'clear-cache';

    public function migrate()
    {
        Cache::clearGroups(['all']);
        $this->log('&#x2714; All cache');
    }
}

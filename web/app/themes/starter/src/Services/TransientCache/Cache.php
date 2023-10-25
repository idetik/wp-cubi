<?php

namespace MyNamespace\Services\TransientCache;

use Coretik\Core\Models\Interfaces\ModelInterface;
use MyNamespace\Services\TransientCache\Tools\{
    IdentifiableObject,
};

class Cache
{
    protected array $models = [];

    public function model(?ModelInterface $model = null): CachingTools
    {
        $key = sprintf('%s_%s', $model->name(), $model->id());
        if (array_key_exists($key, $this->models)) {
            return $this->models[$key];
        }

        $this->models[$key] = new IdentifiableObject($key);
        return $this->models[$key];
    }
}

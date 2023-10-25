<?php

namespace MyNamespace\Models;

use Coretik\Core\Models\Wp\PostModel as ParentModel;

class PostModel extends ParentModel
{
    protected function initializeModel(): void
    {
        $this->declareMetas(['redirect']);
    }
}

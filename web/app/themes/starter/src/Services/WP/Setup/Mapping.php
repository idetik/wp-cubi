<?php

namespace MyNamespace\Services\WP\Setup;

class Mapping implements SetupInterface
{
    public function hooks()
    {
        app()->map()
            ->addPageWithTemplate('contact', 'template-contact.php')
            ->addAnonymousPost('mentions-legales');
    }
}

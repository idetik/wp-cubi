<?php

namespace MyNamespace\Services\WP\Setup\Admin;

use MyNamespace\Services\WP\Setup\SetupInterface;

class TinyMce implements SetupInterface
{
    public function hooks()
    {
        \add_action('tiny_mce_before_init', [__CLASS__, 'blockFormats']);
        \add_filter('mce_buttons', [__CLASS__, 'buttons'], 5);
    }

    public static function blockFormats($in)
    {
        $in['block_formats'] = "Paragraph=p; Titre 1=h2; Titre 2=h3; Titre 3=h4; Titre 4=h5; Titre 5=h6;Preformatted=pre";
        return $in;
    }

    public static function buttons($buttons)
    {
        $position = array_search('alignright', $buttons);

        if (!\is_int($position)) {
            return \array_merge($buttons, ['alignjustify']);
        }

        return \array_merge(
            \array_slice($buttons, 0, $position + 1),
            ['alignjustify'],
            \array_slice($buttons, $position + 1)
        );
    }
}

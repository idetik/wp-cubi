<?php

use function Globalis\WP\Cubi\include_template_part;

$breadcrumb = $breadcrumb ?? app()->navigation()->breadcrumb()->map(function ($row) {
    return $row->toArray();
})->all();

?>

<nav class="breadcrumb-container" id="app-breadcrumb">
    <ul class="breadcrumb">
        <?php
        include_template_part('templates/common/breadcrumb-home');
        foreach ($breadcrumb as $item) :
            if ($item['current']) {
                $tag = sprintf('<span>%s</span>', $item['title']);
            } else {
                $tag = sprintf('<a href="%s">%s</a>', $item['url'], $item['title']);
            }
            printf(
                '<li class="breadcrumb__item%s">%s</li>',
                $item['current'] ? ' breadcrumb__item--current' : '',
                $tag
            );
        endforeach;
        ?>
    </ul>
</nav>

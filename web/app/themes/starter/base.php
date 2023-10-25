<?php

$header = $header ?? true;
$footer = $footer ?? true;
$body_classes = apply_filters('themetik/template/base/body_class', []);

?>

<!doctype html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php wp_head(); ?>
    </head>
    <body<?php !empty($body_classes) && printf(' class="%s"', implode(' ', $body_classes)) ?>>
        <?php $header && get_template_part('templates/header/index'); ?>
        <main>
            <?php !empty($html) ? print($html) : include app()->get('templating.wrapper')->mainTemplatePath() ?>
        </main>
        <?php $footer && get_template_part('templates/footer/index'); ?>
        <?php wp_footer(); ?>
    </body>
</html>

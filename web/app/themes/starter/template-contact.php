<?php

/*
Template Name: Contact
*/


use function Globalis\WP\Cubi\include_template_part;

the_post();

$page = app()->schema('page')->model(\get_the_id());

?>
<?php include_template_part('templates/blocks/headings/page-title', [
    'title' => $page->title(),
    'img_src' => $page->thumbnailUrl('themetik-two-thirds--xlarge'),
]) ?>

<section class="contact">
    <?php include_template_part('templates/contact/part-form', ['model' => $page]); ?>
    <div class="grid--12">
        <div class="grid__col--small-12 grid__col--6 bg--background-lighten-1 flex flex--direction-column">
            <?php include_template_part('templates/contact/part-informations', ['model' => $page]) ?>
        </div>
        <div class="grid__col--small-12 grid__col--6">
            <div class="contact-map">
                <iframe class="embed" width="100%" height="300px" frameborder="0" allowfullscreen src="<?= $page->getField('contact_map') ?>"></iframe>
            </div>
        </div>
    </div>
</section>

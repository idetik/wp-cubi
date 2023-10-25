<?php

use function Globalis\WP\Cubi\include_template_part;

?>
<div class="contact-infos-container flex--grow-2">
    <section class="contact-infos">
        <h2 class="contact-infos__title"><?= $model->getField('contact_places_title') ?></h2>
        <?php
        foreach ($model->contactPlaces() ?: [] as $place) :
            include_template_part('templates/contact/part-informations-address-row', ['place' => $place, 'model' => $model]);
        endforeach;
        ?>
    </section>
</div>
<?php
include_template_part('templates/contact/part-informations-socials');

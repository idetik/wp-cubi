<?php



$containerId = 'form-contact-container';

?>
<div id="<?= $containerId ?>">
    <div class="contact-form">
        <?php
        if (app()->forms()->has('contact')) {
            app()->forms()->get('contact')->view([
                'container_id' => $containerId,
                'model' => $model
            ]);
        }
        ?>
    </div>
</div>

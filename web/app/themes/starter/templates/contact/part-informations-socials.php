<?php



$socials = app()->wp()->option('socialsNetworks');

?>
<div class="contact-socials-container">
    <?php foreach ($socials as $social) : ?>
        <a href="<?= $social['url'] ?>" aria-label="<?= $social['name'] ?>" class="contact-socials__item" target="_blank"><?= $social['icon'] ?></a>
    <?php endforeach; ?>
</div>

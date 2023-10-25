<?php

use function Globalis\WP\Cubi\get_template_part_cached;

$socials = app()->wp()->option('socialsNetworks');

?>
<footer class="footer">
    <ul class="footer__socials social-networks-nav">
        <?php
        foreach ($socials as $social) :
            ?>
            <li class="social-networks-nav__item"><a rel="noopener noreferrer" href="<?= $social['url'] ?>" aria-label="<?= $social['name'] ?>" target="_blank"><?= $social['icon'] ?></a></li>
            <?php
        endforeach;
        ?>
    </ul>

    <div class="footer__navs">
        <?php get_template_part_cached('templates/footer/menus', 'menus'); ?>
    </div>
    <p><small>© <?= strtoupper(bloginfo('name') ?? '') ?> - <?= date('Y') ?></small></p>
</footer>

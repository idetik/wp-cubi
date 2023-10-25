<?php

do_action('get_header');

$socials = app()->wp()->option('socialsNetworks');

?>
<header class="<?= implode(' ', apply_filters('app/topbar/classes', ['topbar'])) ?>" id="app-topbar">
    <div class="navbar container">
        <a class="navbar__logo" href="<?= home_url('/') ?>" title="Aller sur la page d'accueil">
            <span class="visuallyhidden">Aller sur la page d'accueil</span>
        </a>
        <nav class="navbar__right" aria-label="Menu principal">
            <div class="navbar-mobile">
                <div class="navbar-mobile-close">
                    <button class="navbar-mobile-close__button" data-close-navbar>
                        <span class="icon-flaticon-cancel"></span>
                        <span class="visuallyhidden">Fermer le menu</span>
                    </button>
                    <a class="navbar-mobile-logo" href="<?= home_url('/') ?>" aria-label="<?php printf('%s: %s', get_bloginfo('name'), 'Page d\'accueil') ?>"></a>
                </div>
            </div>
            <?php get_template_part('templates/header/menu'); ?>
            <div class="navbar-mobile">
                <ul class="social-networks-nav margin-top--2">
                    <?php
                    foreach ($socials as $social) :
                        ?>
                        <li class="social-networks-nav__item"><a rel="noopener noreferrer" href="<?= $social['url'] ?>" aria-label="<?= $social['name'] ?>" class="<?= $social['id'] ?>" target="_blank"><?= $social['icon'] ?></a></li>
                        <?php
                    endforeach;
                    ?>
                </ul>
            </div>
        </nav>
        <div class="nav-burger hidden--up-medium" id="nav-burger">
            <a href="#" class="nav-burger__link" title="<?= __('Ouvrir ou fermer le menu') ?>"></a>
        </div>
    </div>
</header>

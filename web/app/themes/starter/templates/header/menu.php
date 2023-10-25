<?php

echo app()->menu()->html(
    'header',
    [
        'menu_class' => 'navbar-nav',
        'items_wrap' => '<ul id="%1$s" class="%2$s" role="menu">%3$s</ul>',
        'menu_id' => 'orpd-primary-nav'
    ],
    'aria', // builtIn Walker
    true
);

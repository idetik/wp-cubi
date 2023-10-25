<?php

add_filter('nav_menu_css_class', fn ($classes, $item, $args, $depth) => $depth === 0 ? array_merge($classes, ['footer-nav__item']) : $classes, 10, 4);
echo app()->menu()->html(
    'footer-1',
    [
        'menu_class' => 'footer-nav',
        'menu_id' => 'app-footer-nav-1',
        'items_wrap' => '<ul id="%1$s" class="%2$s" role="menu"><li class="footer-nav__item footer-nav__item--title" role="none">' . app()->menu()->title('footer-1') . '</li>%3$s</ul>'
    ],
    'aria', // builtIn Walker
    true
);

echo app()->menu()->html(
    'footer-2',
    [
        'menu_class' => 'footer-nav',
        'menu_id' => 'app-footer-nav-2',
        'items_wrap' => '<ul id="%1$s" class="%2$s" role="menu"><li class="footer-nav__item footer-nav__item--title" role="none">' . app()->menu()->title('footer-2') . '</li>%3$s</ul>'
    ],
    'aria', // builtIn Walker
    true
);

echo app()->menu()->html(
    'footer-3',
    [
        'menu_class' => 'footer-nav',
        'menu_id' => 'app-footer-nav-3',
        'items_wrap' => '<ul id="%1$s" class="%2$s" role="menu"><li class="footer-nav__item footer-nav__item--title" role="none">' . app()->menu()->title('footer-3') . '</li>%3$s</ul>'
    ],
    'aria', // builtIn Walker
    true
);

<?php
    define('LOTS_PER_PAGE', 9);
    define('ERROR_KEY', 'error');

    $paths = [
        'images' => 'img/',
        'avatars' => 'img/avatars/',
    ];

    $category_styles = [
        'tile' => [
            'ul_classname' => 'promo__list',
            'li_classname' => 'promo__item promo__item--boards',
            'a_classname' => 'promo__link'
        ],
        'bar' => [
            'ul_classname' => 'nav__list container',
            'li_classname' => 'nav__item',
            'a_classname' => ''
        ]
    ];

    date_default_timezone_set('Europe/Moscow');
    $is_auth = rand(0, 1);
    $user_name = 'GreyCat';
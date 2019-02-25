<?php
    define('LOTS_PER_PAGE', 9);
    define('ERROR_KEY', 'error');
    define('EMPTY_CATEGORY', 'Выберите категорию');
    define('IMAGE_RULE', 'image_validation');
    define('MAX_IMAGE_SIZE', 200000);

    define('VALID_IMAGE_TYPES', [
        'image/png',
        'image/jpeg']);

    define('PATHS', [
        'images' => 'img/',
        'avatars' => 'img/avatars/',
    ]);

    define('CATEGORY_STYLES', [
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
    ]);

    define('GET_DATA_STATUS', [
        'db_error' => 'Ошибка БД при получении данных',
        'no_data' => 'В выборке нет данных',
        'data_received' => 'Данные получены',
        'data_added' => 'Данные добавлены'
    ]);

    date_default_timezone_set('Europe/Moscow');
    $is_auth = 0;
    $user_name = 'GreyCat';
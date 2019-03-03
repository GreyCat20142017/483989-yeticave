<?php
    define('RECORDS_PER_PAGE', 9);
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

    define('YETI_SESSION', 'current_user');

    define('BIDDING_IS_OVER', 'Другой победитель');
    define('FINAL_BID', 'Ставка выиграла');
    define('EXPIRED', 'Торги окончены');
    define('ACTIVE', 'Открытый лот');

    define('RATE_CLASSNAME', [
        FINAL_BID =>  'rates__item--win',
        BIDDING_IS_OVER => '',
        EXPIRED =>  'rates__item--end',
        ACTIVE =>  ''
    ]);

    define('TIMER_CLASSNAME', [
        FINAL_BID =>  'timer--win',
        BIDDING_IS_OVER => 'timer--finishing',
        EXPIRED =>  'timer--end',
        ACTIVE =>  ''
    ]);

    define('SEARCH_RESULT', 'Результаты поиска');
    define('ALL_LOTS', 'Все лоты');

    define('PAGINATION_CONTEXT', [
        SEARCH_RESULT => 'search-result',
        ALL_LOTS =>  'all-lots'
    ]);

    define('TEST_EMAIL', 'nrz3siaatg81@mail.ru');

    date_default_timezone_set('Europe/Moscow');
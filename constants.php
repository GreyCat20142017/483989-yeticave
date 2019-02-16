<?php
    define('LOTS_PER_PAGE', 9);
    define('ERROR_KEY', 'error');

    $paths = [
        'images' => 'img/',
        'avatars' => 'img/avatars/',
    ];

    date_default_timezone_set('Europe/Moscow');
    $is_auth = rand(0, 1);
    $user_name = 'GreyCat';
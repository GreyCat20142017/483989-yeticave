<?php
    define('LOTS_PER_PAGE', 9);
    define('ERROR_KEY', 'error');

    require_once('functions.php');
    require_once('connection_config.php');

    date_default_timezone_set('Europe/Moscow');
    $is_auth = rand(0, 1);
    $user_name = 'GreyCat';

    $connection = get_connection($connection_config);

    if (!$connection) {
        die('Невозможно подключиться к базе данных: ' . mysqli_connect_error());
    }

    $categories = get_all_categories($connection);
    $lots = get_open_lots($connection, LOTS_PER_PAGE);

    $main_categories_content = include_template('categories.php',
        [
            'categories' => $categories,
            'ul_classname' => 'promo__list',
            'li_classname' => 'promo__item promo__item--boards',
            'a_classname' => 'promo__link'
        ]);

    $footer_categories_content = include_template('categories.php',
        [
            'categories' => $categories,
            'ul_classname' => 'nav__list container',
            'li_classname' => 'nav__item',
            'a_classname' => ''
        ]);

    $page_content = include_template('index.php',
        [
            'lots' => $lots,
            'categories_content' => $main_categories_content
        ]);

    $layout_content = include_template('layout.php',
        [
            'main_content' => $page_content,
            'title' => 'Главная',
            'categories_content' => $footer_categories_content,
            'is_auth' => $is_auth,
            'user_name' => $user_name
        ]);

    print($layout_content);

?>

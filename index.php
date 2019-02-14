<?php
    define('LOTS_PER_PAGE', 9);
    define('ERROR_KEY', 'error');

    require_once('functions.php');

    date_default_timezone_set('Europe/Moscow');
    $is_auth = rand(0, 1);
    $user_name = 'GreyCat';

    $connection = get_connection();

    if ($connection) {

        $categories = get_data_from_db(
            $connection,
            'SELECT id, name FROM categories',
            'Cписок категорий недоступен');

        $lots = get_data_from_db(
            $connection,
            'SELECT c.name AS category, l.name, l.price, l.image
            FROM lots AS l
                   JOIN categories AS c ON l.category_id = c.id
            WHERE l.completion_date IS NULL
            ORDER BY l.creation_date DESC ' . ' LIMIT ' . LOTS_PER_PAGE . ';',
            'Cписок лотов недоступен');

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

    } else {

        $error = mysqli_connect_error();
        $page_content = include_template('error.php', [
            'mysql_error_message' => $error,
            'user_error_message' => 'Отсутствует подключение к базе данных']);
        $footer_categories_content = '';

    }

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

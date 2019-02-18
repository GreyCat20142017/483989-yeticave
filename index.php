<?php

    require_once('constants.php');
    require_once('connection.php');
    require_once('functions.php');
    require_once('db_functions.php');

    $categories = get_all_categories($connection);
    $lots = get_open_lots($connection, LOTS_PER_PAGE);

    $main_categories_content = include_template('categories.php',
        [
            'categories' => $categories,
            'style' => get_assoc_element($category_styles, 'tile')
        ]);

    $footer_categories_content = include_template('categories.php',
        [
            'categories' => $categories,
            'style' => get_assoc_element($category_styles, 'bar')
        ]);

    $page_content = include_template('index.php',
        [
            'lots' => $lots,
            'categories_content' => $main_categories_content,
            'images' => get_assoc_element($paths, 'images')
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

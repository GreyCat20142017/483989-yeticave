<?php

    session_start();

    require_once('functions.php');

    $categories = get_all_categories($connection);
    $lots = get_open_lots($connection, LOTS_PER_PAGE);

    $main_categories_content = include_template('categories.php',
        [
            'categories' => $categories,
            'style' => get_assoc_element(CATEGORY_STYLES, 'tile')
        ]);

    $footer_categories_content = include_template('categories.php',
        [
            'categories' => $categories,
            'style' => get_assoc_element(CATEGORY_STYLES, 'bar')
        ]);

    $page_content = include_template('index.php',
        [
            'lots' => $lots,
            'categories_content' => $main_categories_content,
            'images' => get_assoc_element(PATHS, 'images')
        ]);

    $layout_content = include_template('layout.php',
        [
            'main_content' => $page_content,
            'title' => 'Главная',
            'categories_content' => $footer_categories_content,
            'is_auth' => is_auth_user(),
            'user_name' => get_auth_user_property('name')
        ]);

    print($layout_content);

?>

<?php

    session_start();
    require_once('init.php');

    $categories = get_all_categories($connection);
    $lots = get_open_lots($connection, RECORDS_PER_PAGE);
    $search_string = '';

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

    $lots_content = include_template('lots.php',
        [
            'lots' => $lots,
            'images' => get_assoc_element(PATHS, 'images'),
            'title' => 'Открытые лоты'
        ]);

    $page_content = include_template('index.php',
        [
            'lots_content' => $lots_content,
            'categories_content' => $main_categories_content
        ]);

    require_once('search.php');

    $search_content = include_template('search.php', ['search_string' => $search_string]);

    $layout_content = include_template('layout.php',
        [
            'main_content' => $page_content,
            'search_content' => $search_content,
            'title' => 'Главная',
            'categories_content' => $footer_categories_content,
            'is_auth' => is_auth_user(),
            'user_name' => get_auth_user_property('name')
        ]);

    print($layout_content);



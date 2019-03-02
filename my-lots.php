<?php

    session_start();

    require_once('functions.php');
    require_once('search.php');

    $categories = get_all_categories($connection);
    $page_title = 'Мои ставки';
    $search_string = '';

    $bids = is_auth_user() ? get_user_bids($connection, get_auth_user_property('id')) : [];

    $main_categories_content = include_template('categories.php',
        [
            'categories' => $categories,
            'style' => get_assoc_element(CATEGORY_STYLES, 'bar')
        ]);

    $footer_categories_content = include_template('categories.php',
        [
            'categories' => $categories,
            'style' => get_assoc_element(CATEGORY_STYLES, 'bar')
        ]);

    $page_content = include_template('my-lots.php',
        [
            'bids' => $bids,
            'categories_content' => $main_categories_content,
            'images' => get_assoc_element(PATHS, 'images'),
            'title' => $page_title
        ]);

    $search_content = include_template('search.php', ['search_string' => $search_string, 'search_enable' => true]);

    $layout_content = include_template('layout.php',
        [
            'main_content' => $page_content,
            'search_content' => $search_content,
            'title' => $page_title,
            'categories_content' => $footer_categories_content,
            'is_auth' => is_auth_user(),
            'user_name' => get_auth_user_property('name')
        ]);

    print($layout_content);
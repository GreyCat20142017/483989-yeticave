<?php

    require_once('constants.php');
    require_once('connection.php');
    require_once('functions.php');
    require_once('db_functions.php');

    $lot_id = isset($_GET['id']) ? $_GET['id'] : null;
    $lot = $lot_id ? get_lot_info($connection, $lot_id) : null;

    $is_ok = ($lot_id && $lot && !was_error($lot));

    $categories = get_all_categories($connection);

    $categories_content = include_template('categories.php',
        [
            'categories' => $categories,
            'style' => get_assoc_element($category_styles, 'bar')
        ],
        true);

    $page_content = $is_ok ?
        include_template('lot.php',
            [
                'lot' => $lot,
                'categories_content' => $categories_content,
                'images' => get_assoc_element($paths, 'images'),
            ],
            true)
        :
        include_template('404.php',
            [
                'categories_content' => $categories_content,
            ],
            true);

    $layout_content = include_template('layout.php',
        [
            'main_content' => $page_content,
            'title' => get_assoc_element($lot, 'name'),
            'categories_content' => $categories_content,
            'is_auth' => $is_auth,
            'user_name' => $user_name
        ],
        true);

    if (!$is_ok) {
        http_response_code(404);
    }

    print($layout_content);
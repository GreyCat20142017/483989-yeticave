<?php

    require_once('../constants.php');
    require_once('../connection.php');
    require_once('../functions.php');
    require_once('../query_functions.php');

    $lot_id = isset($_GET['id']) ? $_GET['id'] : null;
    $lots = $lot_id ? get_lot_info($connection, $lot_id) : [];
    $lot = isset($lots[0]) && !was_error($lots) ? $lots[0] : null;

    $categories = get_all_categories($connection);

    $categories_content = include_template('categories.php',
        [
            'categories' => $categories,
            'ul_classname' => 'nav__list container',
            'li_classname' => 'nav__item',
            'a_classname' => ''
        ],
        true);

    if ($lot_id && $lot) {

        $page_content = include_template('lot.php',
            [
                'lot' => $lot,
                'categories_content' => $categories_content,
                'images' => '../' . get_assoc_element($paths, 'images'),
            ],
            true);

    } else {

        $page_content = include_template('404.php',
            [
                'categories_content' => $categories_content,
            ],
            true);

    }

    $layout_content = include_template('layout.php',
        [
            'main_content' => $page_content,
            'title' => get_assoc_element($lot, 'name'),
            'categories_content' => $categories_content,
            'is_auth' => $is_auth,
            'user_name' => $user_name
        ],
        true);

    print($layout_content);
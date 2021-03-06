<?php

    session_start();
    require_once('init.php');

    $categories = get_all_categories($connection);

    $category_id = isset($_GET['id']) ? intval(strip_tags($_GET['id'])) : null;
    $page = isset($_GET['page']) ? intval(strip_tags($_GET['page'])) : 1;

    $index = array_search($category_id, array_column($categories, 'id'));
    $pagination_data = get_lot_category_pagination($connection, RECORDS_PER_PAGE, $category_id);
    $page_count = intval(get_assoc_element($pagination_data, 'page_count'));
    $search_string = '';

    $page_title = $category_id ?
        'Все лоты в категории <span>«' . ($index >= 0 ? get_assoc_element(get_element($categories, $index, true), 'name') : '') . '»</span>' :
        'Все лоты';

    $lots = get_open_lots($connection, RECORDS_PER_PAGE, ($page - 1) * RECORDS_PER_PAGE, $category_id);

    $categories_content = include_template('categories.php',
        [
            'categories' => $categories,
            'style' => get_assoc_element(CATEGORY_STYLES, 'bar'),
            'current_id' => $category_id
        ]);



    $pagination_content = include_template('pagination.php',
        [
            'need_pagination' => intval(get_assoc_element($pagination_data, 'total_records')) > RECORDS_PER_PAGE,
            'page_count' => $page_count,
            'pages' => range(1, $page_count),
            'active' => $page,
            'pagination_context' => get_assoc_element(PAGINATION_CONTEXT, ALL_LOTS),
            'pre_page_string' => ($category_id) ? 'id=' . $category_id . '&' : ''

        ]);

    $lots_content = include_template('lots.php',
        [
            'lots' => $lots,
            'images' => get_assoc_element(PATHS, 'images'),
            'title' => $page_title
        ]);

    $page_content = include_template('all-lots.php',
        [
            'lots_content' => $lots_content,
            'categories_content' => $categories_content,
            'title' => $page_title,
            'page' => $page,
            'pagination_content' => $pagination_content
        ]);

    $search_content = include_template('search.php', ['search_string' => $search_string]);

    $layout_content = include_template('layout.php',
        [
            'main_content' => $page_content,
            'search_content' => $search_content,
            'title' => $page_title,
            'categories_content' => $categories_content,
            'is_auth' => is_auth_user(),
            'user_name' => get_auth_user_property('name')
        ]);

    print($layout_content);
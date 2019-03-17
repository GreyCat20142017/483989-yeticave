<?php

    session_start();
    require_once('init.php');

    $categories = get_all_categories($connection);

    $search_string = isset($_GET['search']) ? trim(strip_tags($_GET['search'])) : null;
    $page = isset($_GET['page']) ? intval(strip_tags($_GET['page'])) : 1;

    $pagination_data = get_search_result_pagination($connection, RECORDS_PER_PAGE, $search_string);
    $page_count = intval(get_assoc_element($pagination_data, 'page_count'));

    $page_title = empty($search_string) ? 'Поле поиска не должно быть пустым!' : 'Результаты поиска по запросу «' . strip_tags($search_string) . '»';

    $lots = empty($search_string) ? [] : get_search_result($connection, RECORDS_PER_PAGE, ($page - 1) * RECORDS_PER_PAGE, $search_string);

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

    $pagination_content = include_template('pagination.php',
        [
            'need_pagination' => intval(get_assoc_element($pagination_data, 'total_records')) > RECORDS_PER_PAGE,
            'page_count' => $page_count,
            'pages' => range(1, $page_count),
            'active' => $page,
            'pagination_context' => get_assoc_element(PAGINATION_CONTEXT, SEARCH_RESULT),
            'pre_page_string' => 'search=' . $search_string . '&'

        ]);

    $lots_content = include_template('lots.php',
        [
            'lots' => $lots,
            'images' => get_assoc_element(PATHS, 'images'),
            'title' => $page_title
        ]);

    $page_content = include_template('search-result.php',
        [
            'lots_content' => $lots_content,
            'categories_content' => $main_categories_content,
            'title' => $page_title,
            'page' => $page,
            'pagination_content' => $pagination_content
        ]);

    $search_content = include_template('search.php', ['search_string' => $search_string]);

    $layout_content = include_template('layout.php',
        [
            'main_content' => $page_content,
            'search_content' => $search_content,
            'title' => 'Результаты поиска',
            'categories_content' => $footer_categories_content,
            'is_auth' => is_auth_user(),
            'user_name' => get_auth_user_property('name')
        ]);

    print($layout_content);
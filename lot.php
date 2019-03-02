<?php

    session_start();

    require_once('functions.php');

    $lot_id = isset($_GET['id']) ? $_GET['id'] : null;
    $lot = $lot_id ? get_lot_info($connection, $lot_id) : null;

    $is_ok = ($lot_id && $lot && !was_error($lot));

    $categories = get_all_categories($connection);
    $history = $lot_id ? get_lot_history($connection, $lot_id) : [];
    $min_bid = intval(get_assoc_element($lot, 'min_bid'));
    $search_string = '';

    require_once('bid.php');

    $bid_hidden_status = !is_auth_user() ||
        (get_assoc_element($lot, 'not_expired') === '0') ||
        (intval(get_assoc_element($lot, 'owner_id')) === intval(get_auth_user_property('id'))) ||
        !empty(get_assoc_element($lot, 'winner_id')) ||
        !get_bid_ability($connection, $lot_id, intval(get_auth_user_property('id')));

    $categories_content = include_template('categories.php',
        [
            'categories' => $categories,
            'style' => get_assoc_element(CATEGORY_STYLES, 'bar')
        ]);

    $history_content = include_template('lot-history.php',
        [
            'bids' => $history
        ]);

    $bid_content = include_template('bid.php', [
        'errors' => $errors,
        'min_bid' => $min_bid,
        'bid' => ['cost' => $min_bid],
        'lot_id' => $lot_id
    ]);

    $page_content = include_template($is_ok ? 'lot.php' : '404.php',
        [
            'lot' => $lot,
            'categories_content' => $categories_content,
            'history_content' => $history_content,
            'bid_content' => $bid_content,
            'images' => get_assoc_element(PATHS, 'images'),
            'bid_hidden_status' => $bid_hidden_status
        ]);

    $search_content = include_template('search.php', ['search_string' => $search_string]);

    $layout_content = include_template('layout.php',
        [
            'main_content' => $page_content,
            'search_content' => $search_content,
            'main_content' => $page_content,
            'title' => get_assoc_element($lot, 'name'),
            'categories_content' => $categories_content,
            'is_auth' => is_auth_user(),
            'user_name' => get_auth_user_property('name')
        ]);

    if (!$is_ok) {
        http_response_code(404);
    }

    print($layout_content);
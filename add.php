<?php

    require_once('functions.php');

    $categories = get_all_categories($connection);

    $categories_content = include_template('categories.php',
        [
            'categories' => $categories,
            'style' => get_assoc_element(CATEGORY_STYLES, 'bar')
        ]);

    $errors = [];
    $lot = [];
    $category = 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $lot = array_map(function ($item) {
            return trim(strip_tags($item));
        }, $_POST);
        /**
         * Описания полей для валидации. Если правила слишком специфичны, то в required для обязательных полей
         * нужно установить false, при этом заполнение контролировать специфическими правилами
         */
        $fields = [
            'category' => ['description' => 'Категория', 'required' => false, 'validation_rules' => ['category_validation']],
            'lot-name' => ['description' => 'Наименование', 'required' => true],
            'message' => ['description' => 'Описание', 'required' => true],
            'lot-rate' => ['description' => 'Начальная цена', 'required' => true, 'validation_rules' => ['decimal_validation']],
            'lot-step' => ['description' => 'Шаг ставки', 'required' => true, 'validation_rules' => ['decimal_validation']],
            'lot-date' => ['description' => 'Дата завершения', 'required' => true, 'validation_rules' => ['lot_date_validation']],
            'lot-image' => ['description' => 'Изображение', 'required' => true, 'validation_rules' => [IMAGE_RULE]]
        ];

        $errors = get_validation_result($fields, $lot, $_FILES);

        $status_ok = empty(get_form_validation_classname($errors));

        $image_fields = get_image_fields($fields);

        if ($status_ok) {

            try_upload_images($image_fields, $_FILES, $errors, get_assoc_element(PATHS, 'images'), 'lot', $lot);

            $add_result = add_lot($connection, $lot);
            if (isset($add_result) && array_key_exists('id', $add_result)) {
                header('Location: lot.php?id=' . get_assoc_element($add_result, 'id'));
            } else {
                header('Location: lot.php?id=' . 'add_lot_error' . '&msg=' . get_assoc_element($add_result, 'error'));
            }
        } else {

            /**
             * Если были ошибки, изображения нужно загрузить снова в любом случае
             */
            $_FILES = [];
            foreach ($image_fields as $key_image_field => $image_field) {
                $description = get_assoc_element($fields, $key_image_field);
                set_assoc_element($description, 'errors', []);
            }
        }
    }

    $categories_dropdown = include_template('categories_dropdown.php',
        [
            'categories' => $categories,
            'empty_category' => EMPTY_CATEGORY,
            'errors' => $errors,
            'current' => get_assoc_element($lot, 'category')
        ]);

    $page_content = include_template('add.php', [
        'categories_dropdown' => $categories_dropdown,
        'categories_content' => $categories_content,
        'images' => get_assoc_element(PATHS, 'images'),
        'errors' => $errors,
        'lot' => $lot
    ]);

    $layout_content = include_template('layout.php',
        [
            'main_content' => $page_content,
            'title' => 'Добавление лота',
            'categories_content' => $categories_content,
            'is_auth' => $is_auth,
            'user_name' => $user_name
        ]);

    print($layout_content);
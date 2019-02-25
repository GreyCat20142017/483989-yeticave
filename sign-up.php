<?php

    require_once('functions.php');

    $categories = get_all_categories($connection);

    $categories_content = include_template('categories.php',
        [
            'categories' => $categories,
            'style' => get_assoc_element(CATEGORY_STYLES, 'bar')
        ]);

    $errors = [];
    $user = [];
    $status_text = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $user = array_map(function ($item) {
            return trim(strip_tags($item));
        }, $_POST);
        /**
         * Описания полей для валидации. Если правила слишком специфичны, то в required для обязательных полей
         * нужно установить false, при этом заполнение контролировать специфическими правилами
         */
        $fields = [
            'email' => ['description' => 'E-mail', 'required' => true, 'validation_rules' => ['email_validation']],
            'password' => ['description' => 'Пароль', 'required' => true],
            'name' => ['description' => 'Описание', 'required' => true],
            'message' => ['description' => 'Контактные данные', 'required' => true],
            'avatar' => ['description' => 'Аватар', 'required' => false, 'validation_rules' => [IMAGE_RULE]]
        ];

        $errors = get_validation_result($fields, $user, $_FILES);

        $status_ok = empty(get_form_validation_classname($errors));

        $image_fields = get_image_fields($fields);

        if ($status_ok) {

            try_upload_images($image_fields, $_FILES, $errors, get_assoc_element(PATHS, 'avatars'), 'user', $user);

            $add_result = add_user($connection, $user);
            if ($add_result) {
                header('Location: login.php');
            } else {
                $status_text = 'Не удалось добавить пользователя в БД';
            }

        } else {

            $_FILES = [];
            foreach ($image_fields as $key_image_field => $image_field) {
                $description = get_assoc_element($fields, $key_image_field);
                set_assoc_element($description, 'errors', []);
            }
        }
    }

    $page_content = include_template('sign-up.php', [
        'categories_content' => $categories_content,
        'images' => get_assoc_element(PATHS, 'avatars'),
        'errors' => $errors,
        'user' => $user,
        'status' => $status_text
    ]);

    $layout_content = include_template('layout.php',
        [
            'main_content' => $page_content,
            'title' => 'Регистрация',
            'categories_content' => $categories_content,
            'is_auth' => $is_auth,
            'user_name' => $user_name
        ]);

    print($layout_content);
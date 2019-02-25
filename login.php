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
    $status_text = 'Вход на сайт невозможен';

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
            'password' => ['description' => 'Пароль', 'required' => true]
        ];

        $errors = get_validation_result($fields, $user, $_FILES);

        $status_ok = empty(get_form_validation_classname($errors));

        if ($status_ok) {

            $db_status_ok = false;
            $search_result = get_user_by_email($connection, get_assoc_element($user, 'email'));

            switch (get_assoc_element($search_result, 'status')) {

                case get_assoc_element(GET_DATA_STATUS, 'data_received'):
                    $db_status_ok = true;
                    if (!password_verify(get_assoc_element($user, 'password'), get_assoc_element($search_result, 'user_password'))) {
                        $db_status_ok = false;
                        add_error_message($errors, 'password', 'Неверный пароль');
                    }
                    break;

                case get_assoc_element(GET_DATA_STATUS, 'no_data'):
                    add_error_message($errors, 'email', 'Пользователь с таким email не зарегистрирован на сайте');
                    break;

                case get_assoc_element(GET_DATA_STATUS, 'db_error'):
                    break;

                default:
                    break;
            }

            if ($db_status_ok) {
                $status_text = '';
//                тут успешно залогинились
            }

        }

    }

    $page_content = include_template('login.php', [
        'categories_content' => $categories_content,
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
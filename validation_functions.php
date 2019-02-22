<?php

    /**
     * Вспомогательная функция суммирования для array_reduce
     * @param $total
     * @param $item
     * @return int
     */
    function counter ($total, $item) {
        $total += is_array($item) ? count($item) : 0;
        return $total;
    }

    /**
     * Функция возращает название класса для формы на основе переданного массива с результатами валидации
     * @param $errors
     * @return string
     */
    function get_form_validation_classname (&$errors) {
        return isset($errors) && array_reduce($errors, function ($total, $item) {
            $total += is_array($item) ? count($item) : 0;
            return $total;
        }) > 0 ? 'form--invalid' : '';
    }

    /**
     * Функция возращает текст сообщения для формы на основе переданного массива с результатами валидации
     * @param $errors
     * @return string
     */
    function get_form_validation_message (&$errors) {
        return isset($errors) && array_reduce($errors, function ($total, $item) {
            $total += is_array($item) ? count($item) : 0;
            return $total;
        }) > 0 ? 'Пожалуйста, исправьте ошибки в форме' : '';
    }

    /**
     * Функция возращает название класса для обертки поля формы на основе переданного массива с результатами валидации и названия поля
     * Для изображения передается название класса.
     * @param $errors
     * @param $field_name
     * @return string
     */
    function get_field_validation_classname (&$errors, $field_name, $success_classname = '') {
        $success_classname = is_array($errors) && count($errors) === 0 ? '' : $success_classname;
        $field_errors = get_assoc_element($errors, $field_name);
        return is_array($field_errors) && count($field_errors) > 0 ? 'form__item--invalid' : $success_classname;
    }

    /**
     * Функция возвращает описание ошибок валадации для поля по массиву ошибок и названию поля (название поля - ключ в массиве с ошибками)
     * @param $errors
     * @param $field_name
     * @return string
     */
    function get_field_validation_message (&$errors, $field_name) {
        $field_errors = get_assoc_element($errors, $field_name);
        return is_array($field_errors) ? join('. ', $field_errors) : '';
    }

    /**
     * Функция возвращает результат валидации в виде ассоциативного массива с ключом 'Имя поля' по массиву с описанием полей формы
     * Сначала добавляются результаты проверок на required, затем - результаты дополнительных специфических
     * @param $fields
     * @return array
     */
    function get_validation_result ($fields, $form_data, &$files) {
        $errors = [];
        foreach ($fields as $field_name => $field) {
            $errors[$field_name] = [];
            $current_field = get_assoc_element($form_data, $field_name);
            if (get_assoc_element($field, 'required') && empty($current_field)) {
                array_push($errors[$field_name], 'Поле ' . get_assoc_element($field, 'description') . ' (' . $field_name . ') необходимо заполнить');
            }
            if (isset($field['validation_rules']) && is_array($field['validation_rules'])) {
                foreach ($field['validation_rules'] as $rule) {
                    $result = ($rule === IMAGE_RULE) ?
                        get_image_validation_result($field_name, $files) :
                        get_additional_validation_result($rule, $current_field);
                    if (!empty($result)) {
                        array_push($errors[$field_name], $result);
                    }
                }
            }
        }
        return $errors;
    }

    /**
     * Функция возвращает результат проверки правильности выбора значения из списка категорий
     * @param $category_value
     * @return string
     */
    function get_category_validation_result ($category_value) {
        return empty($category_value) || ($category_value === EMPTY_CATEGORY) ? 'Необходимо выбрать категорию!' : '';
    }

    /**
     * Функция возвращает результат проверки, является ли поле числом больше 0
     * @param $number
     * @return string
     */
    function get_decimal_validation_result ($number) {
        $is_ok = is_numeric($number) && ($number > 0);
        return $is_ok ? '' : 'Поле должно быть числом больше 0';
    }

    /**
     * Функция проверяет, явяется ли
     * @param $date
     * @return string
     */
    function get_lot_date_validation_result ($date) {
        $error_message = 'Необходима дата в формате ДД.ММ.ГГГГ больше текущей минимум на 1 день';
        $is_ok = (strlen($date) === 10);
        preg_match('/\d{2}.\d{2}.\d{4}/', $date, $matches);
        $is_ok = $is_ok && count($matches) > 0 && $matches[0] === $date;
        $parts = (preg_split("/\./", $date));
        $is_ok = $is_ok && (count($parts) === 3) && checkdate($parts[1], $parts[0], $parts[2]);
        if (!$is_ok) {
            return $error_message;
        }
        $now = date_create("now");
        $date = date_create($date);
        $days_count = date_interval_format(date_diff($date, $now), "%d");
        $is_ok = $date > $now && $days_count >= 1;
        return $is_ok ? '' : 'Необходима дата в формате ДД.ММ.ГГГГ больше текущей минимум на 1 день';
    }

    /**
     * Функция-распределитель для вызова дополнительных проверок (правильности email и т.д)
     * @param $kind
     * @param $current_field
     * @return string
     */
    function get_additional_validation_result ($kind, $current_field) {
        switch ($kind) {
            case 'category_validation':
                return get_category_validation_result($current_field);
            case 'email_validation':
                return !filter_var($current_field, FILTER_VALIDATE_EMAIL) ? 'Email должен быть корректным' : '';
            case 'lot_date_validation':
                return get_lot_date_validation_result($current_field);
            case 'decimal_validation':
                return get_decimal_validation_result($current_field);
            default:
                return '';
        }
    }

    /**
     * Функция проверяет, являются ли загружаемые файлы допустимого типа. Дальнейшие действия по загрузке и проверке вынесены в другую функцию
     * @param $field_name
     * @param $files
     * @return string
     */
    function get_image_validation_result ($field_name, &$files) {

        if (isset($files[$field_name]['name'])) {
            if (get_assoc_element($files[$field_name], 'error') !== 0) {
                return 'Изображение не загружено';
            }
            $tmp_name = $files[$field_name]['tmp_name'];
            $file_size = $files[$field_name]['size'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($finfo, $tmp_name);
            $is_ok = in_array($file_type, VALID_IMAGE_TYPES) && ($file_size <= MAX_IMAGE_SIZE);
            return $is_ok ? '' : 'Загружаемая картинка должна быть в формате jpeg или png и размером не более 200Кб';
        }
        return 'Необходимо загрузить файл в формате jpeg или png (не более 200Кб)';
    }

    /**
     * Фильтрует список описаний полей для последующей обработки загрузки изображений
     * @param $fields
     * @return array
     */
    function get_image_fields ($fields) {
        return array_filter($fields, function ($item) {
            return
                isset($item['validation_rules']) && is_array($item['validation_rules']) ?
                    in_array(IMAGE_RULE, $item['validation_rules'])
                    : false;
        });
    }

    /**
     * Функция пытается загрузить изображения и записать путь к изображению в данные формы, либо пополняет массив ошибок
     * @param $image_fields
     * @param $files
     * @param $errors
     * @param $image_path
     * @param $image_key
     * @param $lot
     */
    function try_upload_images ($image_fields, &$files, &$errors, $image_path, $image_key, &$lot) {
        foreach ($image_fields as $field_name => $field) {
            $tmp_name = $files[$field_name]['tmp_name'];
            if (is_uploaded_file($tmp_name)) {
                $path = uniqid($image_key . '-', true) . '.' . pathinfo($files[$field_name]['name'], PATHINFO_EXTENSION);
                move_uploaded_file($tmp_name, $image_path . $path);
                $lot[$field_name] = $path;
            } else {
                $result = 'Загрузка файла невозможна: ' . $files[$field_name]['tmp_name'];
                array_push($errors[$field_name], 'Загрузка файла невозможна: ' . $files[$field_name]['tmp_name']);
            }
        }
    }
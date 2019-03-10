<?php

    require_once('constants.php');
    require_once('connection.php');
    require_once('mysql_helper.php');
    require_once('db_functions.php');
    require_once('validation_functions.php');
    require_once('session_functions.php');

    /**
     * Функция принимает два аргумента: имя файла шаблона и ассоциативный массив с данными для этого шаблона.
     * Функция возвращает строку — итоговый HTML-код с подставленными данными или описание ошибки
     * @param $name string
     * @param $data array
     * @return false|string
     */
    function include_template ($name, $data) {
        $name = 'templates/' . $name;
        if (!is_readable($name)) {
            return 'Шаблон с именем ' . $name . ' не существует или недоступен для чтения';
        }
        ob_start();
        extract($data);
        require $name;
        return ob_get_clean();
    }

    /**
     * Функция округляет число в большую сторону и возвращает строку с добавленным символом рубля и делением на разряды
     * Необязательный параметр simple позволяет вывести сумму без стилизованного тега с svg,
     * @param $sum
     * @param bool $simple optional, default = false
     * @param bool $only_digit optional, default = false
     * @return string
     */
    function get_rubles ($sum, $simple = false, $only_digit = false) {
        $ruble = ' ' . ($simple ? 'р' : '<b class="rub">р</b>');
        $ruble = $only_digit ? '' : $ruble;
        return number_format(ceil($sum), 0, '', ' ') . $ruble;
    }

    /**
     * Функция проверяет существование ключа ассоциативного массива и возвращает значение по ключу, если
     * существуют ключ и значение. В противном случае будет возвращена пустая строка или пустой массив (если передан
     * третий параметр, запрашивающий пустой массив в случае отсутствия значения)
     * @param array $data
     * @param string $key
     * @param bool $array_return
     * @return array|string
     */
    function get_assoc_element ($data, $key, $array_return = false) {
        $empty_value = $array_return ? [] : '';
        return isset($data) && is_array($data) && array_key_exists($key, $data) && isset($data[$key]) ? $data[$key] : $empty_value;
    }

    /**
     * Функция проверяет существование ключа ассоциативного массива и устанавливает значение по ключу,
     * если существуют ключ. Возвращает true в случае успеха.
     * @param $data
     * @param $key
     * @param $value
     * @return bool
     */
    function set_assoc_element ($data, $key, $value) {
        $result = false;
        if (isset($data) && array_key_exists($key, $data) && isset($data[$key])) {
            $data[$key] = $value;
            $result = true;
        }
        return $result;
    }

    /**
     * Функция проверяет существование элемента массива и возвращает его, если он существует.
     * В противном случае будет возвращена пустая строка
     * @param $array
     * @param $index
     * @param boolean $array_return
     * @return any|string|array
     */
    function get_element ($array, $index, $array_return = false) {
        $empty_value = $array_return ? [] : '';
        return is_array($array) && isset($array[$index]) ? $array[$index] : $empty_value;
    }

    /**
     * Функция для предотвращения пустых атрибутов class в шаблоне.
     * Возвращает часть тега с названием класса, либо пустую строку
     * @param string $classname
     * @return string
     */
    function get_classname ($classname) {
        return empty($classname) ? '' : ' class="' . $classname . '" ';
    }

    /**
     * Функция проверяет наличие данных в массиве по ключу, фильтрует содержимое функцией strip_tags и убирает пробелы
     * @param $data
     * @param $key
     * @return string
     */
    function get_pure_data ($data, $key) {
        return isset($data) && array_key_exists($key, $data) && isset($data[$key]) ? trim(strip_tags($data[$key])) : '';
    }

    /**
     * Функция возвращает значение атрибута selected для выпадающего списка
     * @param $element_id
     * @param $current_id
     * @return string
     */
    function get_selected_state ($element_id, $current_id) {
        return $element_id === $current_id ? ' selected ' : '';
    }

    /** Функция пытается получить параметр msg из массива _GET. В случае неудачи выводит стандартное сообщение.
     * @param $get
     * @param string $standard_message
     * @return string
     */
    function get_error_info (&$get, $standard_message = 'Данной страницы не существует на сайте.') {
        $message = get_pure_data($get, 'msg');
        return empty($message) ? $standard_message : $message;
    }

    /**
     * Функция позаимствована на просторах интернета. Проверяет является ли нечто существующей папкой.
     * @param $folder
     * @return bool
     */
    function folder_exists ($folder) {
        $path = realpath($folder);
        return ($path !== false AND is_dir($path));
    }

    /**
     * Функция проверяет, существует ли путь, при отсутствии - пытается создать. Возвращает true, если путь существует
     * @param $base
     * @return bool
     */
    function check_and_repair_path ($base) {
        $result = folder_exists($base);
        return $result ? $result : mkdir(trim($base), 0700, true);
    }

    /**
     * Функция возвращает название класса для кнопки пагинации активной страницы
     * @param $page
     * @param $active
     * @return string
     */
    function get_active_page_classname ($page, $active) {
        return ($page === $active) ? 'pagination-item-active' : '';
    }

    /**
     * Возвращает текст href для кнопки пагинации "Назад"
     * @param $active
     * @param $id
     * @param $pagination_context
     * @return string
     */
    function get_prev_href ($pagination_context, $active, $pre_page_string) {
        return $active > 1 ? ' href="' . $pagination_context . '.php?' . $pre_page_string . 'page=' . ($active - 1) . '"' : '';
    }

    /**
     * Функция возвращает текст href для кнопки пагинации "Вперед"
     * @param $pagination_context
     * @param $id
     * @param $active
     * @param $last
     * @return string
     */
    function get_next_href ($pagination_context, $active, $last, $pre_page_string) {
        return $active < $last ? ' href="' . $pagination_context . '.php?' . $pre_page_string . 'page=' . ($active + 1) . '"' : '';
    }

    /**
     * Функция возвращает текст href для кнопки пагинации № n
     * @param $pagination_context
     * @param $page
     * @param $pre_page_string
     * @return string
     */
    function get_page_href ($pagination_context, $page, $pre_page_string) {
        return 'href="' . $pagination_context . '.php?' . $pre_page_string . 'page=' . ($page) . '"';
    }

    /**
     * Функция возвращает название класса-модификатора для get_rates__item в зависимости от результата из get_user_bids
     * @param $result
     * @return string
     */
    function get_rates__item_classname ($result) {
        return get_assoc_element(RATE_CLASSNAME, $result);
    }

    /**
     * Функция возвращает название класса-модификатора для get_rates__item в зависимости от результата из get_user_bids
     * @param $result
     * @param $expired
     * @return string
     */
    function get_timer_classname ($result) {
        return get_assoc_element(TIMER_CLASSNAME, $result);
    }

    /**
     * Функция возвращает либо строку с результатом завершения торгов, либо оставшийся срок лота
     * @param $result
     * @param $time_left
     * @return mixed
     */
    function get_timer_info ($result, $time_left) {
        return in_array($result, [FINAL_BID, EXPIRED]) ? $result : $time_left;
    }

    /**
     * Функция возвращает время в формате H:i:s, принимая в качестве параметра количество оставшихся секунд.
     * @param $seconds_left
     * @return string
     */
    function get_formatted_time_from_seconds ($seconds_left) {
        $seconds_left = empty($seconds_left) ? 0 : $seconds_left;
        $days = floor($seconds_left / (3600 * 24));
        $time = floor($seconds_left % (3600 * 24));
        $parts = explode(':', gmdate('H:i:s', $time));
        $parts[0] = intval($parts[0]) + $days * 24;
        return implode(':', $parts);
    }

    /**
     * Функция возвращает название класса-модификатора для пункта меню категорий (активный, не активный)
     * @param $current_id
     * @param $category_id
     * @param $styled_classname
     * @return string
     */
    function get_current_item_classname ($current_id, $category_id, $styled_classname) {
        $classname = ($current_id) && (intval($current_id) === intval($category_id)) ?
            $styled_classname . ' nav__item--current ' : $styled_classname;
        return get_classname($classname);
    }

    /**
     * @param $seconds_left
     * @return string
     */
    function get_time_left_classname ($seconds_left) {
        $seconds_left = empty($seconds_left) ? 0 : $seconds_left;
        return ($seconds_left <= 60 * 60) ? get_assoc_element(TIMER_CLASSNAME, BIDDING_IS_OVER) : '';
    }

    /**
     * Функция возрвращает число ставок + словоформу со склонением либо строку "Стартовая цена" (если ставок не было)
     * @param $bids_count
     * @return string
     */
    function get_bids_info ($bids_count) {
        $bids_count = intval($bids_count);
        return $bids_count === 0 ? 'Стартовая цена' : $bids_count . ' ' . get_text_form($bids_count, ['ставка', 'ставки', ' ставок']);
    }

    /**
     * Функция возвращает форму слова для числа по переданному массиву словоформ и числу
     * @param $source_number
     * @param $text_forms
     * @return mixed
     */
    function get_text_form ($source_number, $text_forms) {
        $source_number = abs($source_number) % 100;
        $temporary_number = $source_number % 10;
        if ($source_number > 10 && $source_number < 20) {
            return $text_forms[2];
        }
        if ($temporary_number > 1 && $temporary_number < 5) {
            return $text_forms[1];
        }
        if ($temporary_number === 1) {
            return $text_forms[0];
        }
        return $text_forms[2];
    }
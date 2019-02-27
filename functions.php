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
     * Необязательный параметр simple позволяет вывести сумму без стилизованного тега с svg
     * @param $sum
     * @param bool $simple optional, default = false
     * @return string
     */
    function get_rubles ($sum, $simple = false) {
        $ruble = $simple ? 'р' : '<b class="rub">р</b>';
        return number_format(ceil($sum), 0, '', ' ') . ' ' . $ruble;
    }

    /**
     * Функция возвращает разницу между текущим временем и ближайшей полуночью в виде строки в формате ЧЧ-ММ
     * @return string
     */
    function get_lot_lifetime () {
        $current_date = date_create("now");
        $limit_date = date_create("tomorrow midnight");
        return date_interval_format(date_diff($current_date, $limit_date), "%H:%I");
    }

    /**
     * Функция проверяет существование ключа ассоциативного массива и возвращает значение по ключу, если
     * существуют ключ и значение. В противном случае будет возвращена пустая строка или пустой массив (если передан
     * третий параметр, запрашивающий пустой массив в случае отсутствия значения)
     * @param array $data
     * @param string $key
     * @param bool $array_return
     * @return any or string or array
     */
    function get_assoc_element ($data, $key, $array_return = false) {
        $empty_value = $array_return ? [] : '';
        return isset($data) && array_key_exists($key, $data) && isset($data[$key]) ? $data[$key] : $empty_value;
    }

    /**
     * Функция проверяет существование ключа ассоциативного массива и устанавливает значение по ключу,
     * если существуют ключ и значение. Возвращает true в случае успеха.
     * @param $data
     * @param $key
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
     * @return element or string
     */
    function get_element ($array, $index) {
        return isset($array[$index]) ? $array[$index] : '';
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
     * @param $category_id
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

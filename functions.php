<?php
    /**
     * Функция принимает два или три аргумента: имя файла шаблона и ассоциативный массив с данными для этого шаблона.
     * Третий (необязательный) параметр $is_inner (по умолчанию = false) определяет, является ли страница внутренней
     * Функция возвращает строку — итоговый HTML-код с подставленными данными или описание ошибки
     * @param $name string
     * @param $data array
     * @param $is_inner boolean optional
     * @return false|string
     */
    function include_template ($name, $data, $is_inner = false) {
        $name = ($is_inner ? '../' : '') . 'templates/' . $name;
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
     * существуют ключ и значение. В противном случае будет возвращена пустая строка
     * @param $data
     * @param $key
     * @return element or string
     */
    function get_assoc_element ($data, $key) {
        return isset($data) && array_key_exists($key, $data) && isset($data[$key]) ? $data[$key] : '';
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
     * Функция принимает ассоциативный массив с параметрами подключения к БД (host, user, password, database)
     * Возвращает соединение или false
     * @param array $db
     * @return mysqli
     */
    function get_connection (&$config) {
        $connection = mysqli_connect($config['host'], $config['user'], $config['password'], $config['database']);
        if ($connection) {
            mysqli_set_charset($connection, "utf8");
        }
        return $connection;
    }

    /**
     * Функция принимает соединение, текст запроса и пользовательское сообщение для вывода в случае ошибки.
     * Возвращает либо данные, полученные из БД в виде массива, либо ассоциативный массив с описанием ошибки
     * @param $connection
     * @param $query
     * @param $user_error_message
     * @return array
     */
    function get_data_from_db (&$connection, $query, $user_error_message) {
        $data = [[ERROR_KEY => $user_error_message]];
        if ($connection) {
            $result = mysqli_query($connection, $query);
            if ($result) {
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            } else {
                $error = mysqli_error($connection);
            }
        }
        return $data;
    }

    /**
     * Функция устанавливает, имел ли место факт ошибки при получении данных, анализируя переданный по ссылке массив,
     * полученный функцией get_data_from_db
     * @param $data
     * @return bool
     */
    function was_error (&$data) {
        return isset($data[0]) && array_key_exists(ERROR_KEY, $data[0]);
    }

    /**
     * Функция для совместного использования с функцией was_error. Возвращает описание ошибки.
     * @param $data
     * @return element value|string
     */
    function get_error_description (&$data) {
        return isset($data[0]) ? get_assoc_element($data[0], ERROR_KEY) : 'Неизвестная ошибка...';
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
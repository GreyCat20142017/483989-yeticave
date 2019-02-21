<?php
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
     * @param string $user_error_message
     * @param bool $single
     * @return array|null
     */
    function get_data_from_db (&$connection, $query, $user_error_message, $single = false) {
        $data = [[ERROR_KEY => $user_error_message]];
        if ($connection) {
            $result = mysqli_query($connection, $query);
            if ($result) {
                $data = $single ? mysqli_fetch_assoc($result) : mysqli_fetch_all($result, MYSQLI_ASSOC);
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
     * Функция принимает соединение с БД
     * Возвращает либо список категорий, полученный из БД в виде массива, либо ассоциативный массив с описанием ошибки
     * @param $connection
     * @return array
     */
    function get_all_categories (&$connection) {
        $sql = 'SELECT id, name FROM categories;';
        return get_data_from_db($connection, $sql, 'Cписок категорий недоступен');
    }

    /**
     * Функция принимает соединение с БД, количество требуемых лотов, и $offset (необязательный параметр)
     * ($offset - по умолчанию = 0 (для главной страницы) или <n> для пагинации)
     * Возвращает либо список открытых лотов, полученный из БД в виде массива, либо ассоциативный массив с описанием ошибки
     * @param $connection
     * @param int $limit
     * @param int $offset optional
     * @return array
     */
    function get_open_lots (&$connection, $limit, $offset = 0) {
        $sql = 'SELECT l.id, c.name AS category, l.name, l.price, l.image
                FROM lots AS l
                JOIN categories AS c ON l.category_id = c.id
                WHERE l.completion_date IS NULL
                ORDER BY l.creation_date DESC ' . ' LIMIT ' . $limit . ' OFFSET ' . $offset . ';';
        return get_data_from_db($connection, $sql, 'Cписок лотов недоступен');
    }

    /**
     * Функция принимает соединение с БД и id лота
     * Возвращает либо массив с основными параметрами лота, полученный из БД в виде массива, либо ассоциативный массив с описанием ошибки
     * @param $connection
     * @param $lot_id
     * @return array
     */
    function get_lot_info (&$connection, $lot_id) {
        $sql = 'SELECT l.id, c.name AS category, l.name, l.creation_date, l.price, l.description, l.image, (l.price + l.step) AS min_bid
                FROM lots AS l
                       JOIN categories AS c ON l.category_id = c.id
                WHERE l.id = ' . $lot_id . ';';
        return get_data_from_db($connection, $sql, 'Невозможно получить данные о лоте ' . $lot_id, true);
    }
    /**
     * Функция принимает соединение и массив с данными формы. Возвращает либо массив с id добавленной записи, либо массив с ошибкой
     * @param $connection
     * @param $lot
     * @return array
     */
    function add_lot ($connection, $lot) {
        $sql = 'INSERT INTO lots (category_id, owner_id,  name, description, image, price,  step, completion_date) 
                          VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)';

        $stmt = db_get_prepare_stmt($connection, $sql, [
            get_assoc_element($lot, 'category'),
            1,
            get_assoc_element($lot, 'lot-name'),
            get_assoc_element($lot, 'message'),
            get_assoc_element($lot, 'lot-image'),
            get_assoc_element($lot, 'lot-rate'),
            get_assoc_element($lot, 'lot-step'),
            get_assoc_element($lot, 'lot-date')
        ]);

        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            $new_id = mysqli_insert_id($connection);
            return ['id' => $new_id];
        }
        return ['error' => mysqli_error($connection)];
    }
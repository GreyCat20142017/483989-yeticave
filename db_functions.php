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
                WHERE l.winner_id IS NULL
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
     * В случае попытки использовать несуществующие id пользователя или id категории возвращает ошибку
     * @param $connection
     * @param $lot
     * @return array
     */
    function add_lot ($connection, $lot, $current_user = 1) {

        $current_category = get_assoc_element($lot, 'category');

        $user_status = get_id_existance($connection, 'users', $current_user);
        $category_status = get_id_existance($connection, 'categories', $current_category);

        if (was_error($category_status) || was_error($user_status)) {
            return ['error' => 'Попытка использовать несуществующие данные для добавления лота. Лот не будет добавлен!'];
        }

        $sql = 'INSERT INTO lots (category_id, owner_id,  name, description, image, price,  step, completion_date) 
                          VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)';

        $stmt = db_get_prepare_stmt($connection, $sql, [
            $current_category,
            $current_user,
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

    /**
     * Функция проверяет существование ключа в указанной таблице БД
     * @param $connection
     * @param $table
     * @param $id
     * @return array|null
     */
    function get_id_existance ($connection, $table, $id) {
        $data = [[ERROR_KEY => 'Id =  ' . $id . ' в таблице ' . $table . ' не существует! ']];
        $sql = 'SELECT id FROM ' . $table . ' WHERE id = ' . $id . ' LIMIT 1';
        $result = get_data_from_db($connection, $sql, 'Невозможно получить id из таблицы ' . $table, true);
        if ($result) {
            $data = $result;
        }
        return $data;
    }

    /**
     * Функция возращает ошибку, если невозможно получить данные из БД, массив с id пользователя, если пользователь
     * с таким email существует, null - если не было ошибки и такого пользователя нет в БД
     * @param $connection
     * @param $email
     * @return null || array
     */
    function get_id_by_email ($connection, $email) {
        $sql = 'SELECT id FROM users WHERE email="' . mysqli_real_escape_string($connection, $email) . '" LIMIT 1';
        return get_data_from_db($connection, $sql, 'Невозможно получить id пользователя', true);
    }

    /**
     * Функция возвращает true в случае успешного добавления пользователя, false - в случае ошибки
     * @param $connection
     * @param $user
     * @return bool
     */
    function add_user ($connection, $user) {

        $user_status = get_id_by_email($connection, get_assoc_element($user, 'email'));

        if ($user_status) {
            return $user_status;
        }

        $sql = 'INSERT INTO users ( email, name, user_password, avatar, contacts) 
                          VALUES ( ?, ?, ?, ?, ?)';

        $stmt = db_get_prepare_stmt($connection, $sql, [
            get_assoc_element($user, 'email'),
            get_assoc_element($user, 'name'),
            password_hash(get_assoc_element($user, 'password'), PASSWORD_DEFAULT),
            get_assoc_element($user, 'avatar'),
            get_assoc_element($user, 'message')
        ]);

        $res = mysqli_stmt_execute($stmt);

        return ($res) ? true : false;
    }

    /**
     * Функция возвращает результат запроса в виде ассоциативного массива со статусом и данными
     * @param $connection
     * @param $email
     * @return array|null
     */
    function get_user_by_email ($connection, $email) {
        $sql = 'SELECT id, email, user_password FROM users WHERE email="' . mysqli_real_escape_string($connection, $email) . '" LIMIT 1';
        $data = get_data_from_db($connection, $sql, 'Невозможно получить данные пользователя', true);
        if (!$data) {
            $result = ['status' => get_assoc_element(GET_DATA_STATUS, 'no_data'), 'data' => null];
        } else if (was_error($data)) {
            $result = ['status' => get_assoc_element(GET_DATA_STATUS, 'db_error'), 'data' => null];
        } else {
            $result = ['status' => get_assoc_element(GET_DATA_STATUS, 'data_received'), 'data' => $data];
        }
        return $result;
    }
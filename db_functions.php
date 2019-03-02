<?php
    /**
     * Функция принимает ассоциативный массив с параметрами подключения к БД (host, user, password, database)
     * Возвращает соединение или false
     * @param $config
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
                $data = [[ERROR_KEY => mysqli_error($connection)]];
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
     * @param array $data
     * @return string
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
     * @param null $category_id
     * @return array
     */
    function get_open_lots (&$connection, $limit, $offset = 0, $category_id = null) {
        $category_condition = $category_id ? ' l.category_id = ' . mysqli_real_escape_string($connection, $category_id) . '  AND ' : '';
        $sql = 'SELECT l.id, c.name AS category, l.name, l.price, l.image, 
                   CONCAT(floor(GREATEST(0, TIMESTAMPDIFF(MINUTE,  NOW(), l.completion_date)) / 60) , ":",
                   LPAD(floor(GREATEST(0, TIMESTAMPDIFF(MINUTE,  NOW(), l.completion_date)) % 60), 2, "0")) AS time_left
                FROM lots AS l
                JOIN categories AS c ON l.category_id = c.id
                WHERE ' . $category_condition . ' (l.winner_id IS NULL) AND (l.completion_date > NOW()) 
                ORDER BY l.creation_date DESC LIMIT ' . $limit . ' OFFSET ' . $offset . ';';
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
        $sql = 'SELECT  l.id, l.owner_id, c.name AS category, l.name, l.creation_date, l.completion_date, l.price, l.description, l.image,
                   CASE WHEN MAX(b.declared_price) IS NULL THEN l.price ELSE MAX(b.declared_price) END + l.step AS min_bid,
                   l.completion_date > NOW() AS not_expired,
                   CONCAT(floor(GREATEST(0, TIMESTAMPDIFF(MINUTE,  NOW(), completion_date)) / 60) , ":",
                   LPAD(floor(GREATEST(0, TIMESTAMPDIFF(MINUTE,  NOW(), completion_date)) % 60), 2, "0")) AS time_left
                FROM lots AS l
                INNER JOIN categories AS c ON l.category_id = c.id
                LEFT OUTER JOIN bids AS b ON l.id = b.lot_id
                WHERE l.id = ' . $lot_id . ';';
        return get_data_from_db($connection, $sql, 'Невозможно получить данные о лоте ' . $lot_id, true);
    }
    /**
     * Функция принимает соединение и массив с данными формы. Возвращает либо массив с id добавленной записи, либо массив с ошибкой
     * В случае попытки использовать несуществующие id пользователя или id категории возвращает ошибку
     * @param $connection
     * @param $lot
     * @param int $current_user
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
        $sql = 'SELECT id FROM ' . $table . ' WHERE id = ' . $id . ' LIMIT 1;';
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
        $sql = 'SELECT id FROM users WHERE email="' . mysqli_real_escape_string($connection, $email) . '" LIMIT 1;';
        return get_data_from_db($connection, $sql, 'Невозможно получить id пользователя', true);
    }

    /**
     * Функция возвращает true в случае успешного добавления пользователя, false - в случае ошибки
     * Если пользователь с таким email уже сушествовал - возвращается массив c id.
     * @param $connection
     * @param $user
     * @return bool || array
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
        $sql = 'SELECT id, email, user_password, name FROM users WHERE email="' . mysqli_real_escape_string($connection, $email) . '" LIMIT 1;';
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

    /**
     * Функция возвращает результаты расчета для пагинации по лотам и категориям.
     * Передается соединение, число записей на страницу и в качестве необязательного параметра id категории
     * @param $connection
     * @param $limit
     * @param null $category_id
     * @return array|null
     */
    function get_lot_category_pagination ($connection, $limit, $category_id = null) {
        $category_condition = $category_id ?
            ' AND category_id = ' . mysqli_real_escape_string($connection, $category_id) . ' GROUP BY category_id;' :
            '  GROUP BY category_id; ';
        $sql = 'SELECT CEIL(COUNT(*) / ' . $limit . ') AS page_count, COUNT(*) AS total_records FROM lots 
            WHERE winner_id IS NULL  AND completion_date > NOW() ' . $category_condition;
        $data = get_data_from_db($connection, $sql, 'Невозможно получить данные для пагинации', true);
        return (!$data || was_error($data)) ? [] : $data;
    }

    /**
     * Функция возвращает данные о сделанных ставках по лоту либо пустой массив
     * @param $connection
     * @param $lot_id
     * @return array
     */
    function get_lot_history ($connection, $lot_id) {
        $sql = 'SELECT u.name, b.declared_price, b.placement_date,   
                   CASE
                     WHEN ABS(TIMESTAMPDIFF(MINUTE, NOW(), b.placement_date)) < 1 THEN "меньше минуты назад"
                     WHEN ABS(TIMESTAMPDIFF(MINUTE, NOW(), b.placement_date)) < 60 THEN CONCAT("около ", ABS(TIMESTAMPDIFF(MINUTE, NOW(), b.placement_date)) , " минут назад")
                     WHEN ABS(TIMESTAMPDIFF(HOUR , NOW(), b.placement_date)) < 24 THEN CONCAT("около ", ABS(TIMESTAMPDIFF(HOUR , NOW(), b.placement_date)) , " часов назад")
                     WHEN ABS(TIMESTAMPDIFF(HOUR, NOW(), b.placement_date)) < 48 THEN CONCAT("Вчера, в ", DATE_FORMAT(b.placement_date, "%H:%i"))
                     ELSE DATE_FORMAT(b.placement_date, "%d.%m.%Y в %H:%i") END AS time_ago
                    FROM bids AS b
                           join users AS u ON b.user_id = u.id
                    WHERE b.lot_id = ' . $lot_id . '
                    ORDER BY b.placement_date DESC;';
        $data = get_data_from_db($connection, $sql, 'Невозможно получить историю для лота');
        return (!$data || was_error($data)) ? [] : $data;
    }

    /**
     * Функция возвращает минимальную сумму следующей ставки, либо ноль в случае ошибки
     * @param $connection
     * @param $lot_id
     * @return int
     */
    function get_next_bid ($connection, $lot_id) {
        $sql = 'SELECT CASE WHEN MAX(b.declared_price) IS NULL THEN l.price ELSE MAX(b.declared_price) END + l.step AS next_bid
                    FROM lots AS l
                           LEFT OUTER JOIN bids AS b ON l.id = b.lot_id
                    WHERE b.lot_id = ' . $lot_id . ';';
        $data = get_data_from_db($connection, $sql, 'Невозможно получить данные о ставках лота', true);
        return (!$data || was_error($data)) ? 0 : intval(get_assoc_element($data, 'next_bid'));
    }

    /**
     * Функция возврщает true, false или ошибку как результат попытки добпавления лота
     * @param $connection
     * @param $lot_id
     * @param int $current_user
     * @param $declared_price
     * @param $errors
     * @return array|bool
     */
    function add_bid ($connection, $lot_id, $current_user, $declared_price, &$errors) {
        $user_status = get_id_existance($connection, 'users', $current_user);
        $lot_status = get_id_existance($connection, 'lots', $lot_id);
        $next_bid = get_next_bid($connection, $lot_id);

        if (was_error($user_status) || was_error($lot_status) || $declared_price < $next_bid) {
            add_error_message($errors, 'cost', 'Попытка использовать некорректные данные для добавления ставки!');
            return false;
        }

        $sql = 'INSERT INTO bids (user_id, lot_id, declared_price) 
                          VALUES ( ?, ?, ?)';

        $stmt = db_get_prepare_stmt($connection, $sql, [
            $current_user,
            $lot_id,
            $declared_price
        ]);

        $res = mysqli_stmt_execute($stmt);

        return $res ? true : false;
    }

    /**
     * Возвращает true, если пользователь имеет возможноcть добавить ставку к лоту. Если не удалось получить данные,
     * или пользователь уже добавлял ставку - возвращается false
     * @param $connection
     * @param $lot_id
     * @param $user_id
     * @return bool
     */
    function get_bid_ability ($connection, $lot_id, $user_id) {
        $sql = 'SELECT COUNT(*) AS amount FROM bids WHERE lot_id=' . mysqli_real_escape_string($connection, $lot_id) . ' AND 
                user_id=' . mysqli_real_escape_string($connection, $user_id) . ';';
        $data = get_data_from_db($connection, $sql, 'Невозможно получить данные', true);
        return (!$data || was_error($data)) ? false : (intval(get_assoc_element($data, 'amount')) === 0);
    }

    /**
     * Функция возвращает массив со ставками пользователя, либо пустой массив в случае ошибки
     * @param $connection
     * @param $user_id
     * @return array
     */
    function get_user_bids ($connection, $user_id) {
        $sql = 'SELECT b.lot_id,
                   l.image,
                   l.name,
                   c.name AS category,
                   b.declared_price,
                   CASE
                     WHEN ABS(TIMESTAMPDIFF(MINUTE, NOW(), b.placement_date)) < 1 THEN "меньше минуты назад"
                     WHEN ABS(TIMESTAMPDIFF(MINUTE, NOW(), b.placement_date)) < 60 THEN CONCAT("около ", ABS(TIMESTAMPDIFF(MINUTE, NOW(), b.placement_date)), " минут назад")
                     WHEN ABS(TIMESTAMPDIFF(HOUR, NOW(), b.placement_date)) < 24 THEN CONCAT("около ", ABS(TIMESTAMPDIFF(HOUR, NOW(), b.placement_date)), " часов назад")
                     WHEN ABS(TIMESTAMPDIFF(HOUR, NOW(), b.placement_date)) < 48 THEN CONCAT("Вчера, в ", DATE_FORMAT(b.placement_date, "%H:%i"))
                     ELSE DATE_FORMAT(b.placement_date, "%d.%m.%Y в %H:%i") 
                   END AS placement_date,
                   CASE
                     WHEN b.user_id = l.winner_id THEN "' . FINAL_BID . '"
                     WHEN l.winner_id IS NOT NULL THEN "' . BIDDING_IS_OVER . '"
                     WHEN TIMESTAMPDIFF(minute,  NOW(), completion_date) <=0 THEN  "' . EXPIRED . '"
                     ELSE  "' . ACTIVE . '"
                    END AS result,
                    CONCAT(floor(GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), completion_date)) / 3600), ":",
                                 LPAD(floor(GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), completion_date)) % 3600), 2, "0"), ":",
                                 LPAD(GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), completion_date)) -
                                      floor(GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), completion_date)) / 3600) -
                                      floor(GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), completion_date)) % 3600), 2, "0")
                    ) AS time_left,
                    TIMESTAMPDIFF(minute,  NOW(), completion_date) <=0 AS expired,
                    CASE
                     WHEN b.user_id = l.winner_id THEN u.contacts
                     ELSE ""
                    END as contacts
                    FROM bids AS b
                           JOIN lots AS l ON b.lot_id = l.id
                           JOIN categories AS c ON l.category_id = c.id
                           JOIN users AS u ON l.owner_id = u.id
                    WHERE b.user_id = ' . $user_id . ' ORDER BY  b.placement_date DESC;';
        $data = get_data_from_db($connection, $sql, 'Невозможно получить данные о ставках пользователя');
        return (!$data || was_error($data)) ? [] : $data;
    }

    /**
     * Функция возвращает в виде ассоциативного массива список лотов с информацией о будущем победителе либо пустой массив
     * (в случае ошибки или отсутствия данных)
     * @param $connection
     * @return array
     */
    function get_last_bids ($connection) {
        $sql = 'SELECT lb.last_bid, lb.lot_id, bb.user_id, u.email, u.name as username, ll.name, ll.completion_date, bb.declared_price
                FROM (SELECT max(b.id) AS last_bid, b.lot_id
                      FROM bids AS b
                             JOIN lots AS l ON b.lot_id = l.id
                      WHERE l.winner_id IS NULL  AND completion_date<=NOW()
                      GROUP BY b.lot_id) AS lb
                       JOIN bids AS bb ON lb.last_bid = bb.id
                JOIN users AS u ON bb.user_id=u.id
                       JOIN lots AS ll ON bb.lot_id=ll.id;';
        $data = get_data_from_db($connection, $sql, 'Невозможно получить данные для выявления победителя');
        return (!$data || was_error($data)) ? [] : $data;
    }

    /**
     * Функция возвращает результат полнотекстового поиска по имени и названию открытых лотов
     * @param $connection
     * @param $limit
     * @param int $offset
     * @param $search_string
     * @return array|null
     */
    function get_search_result ($connection, $limit, $offset = 0, $search_string) {
        $sql = 'SELECT l.id, c.name AS category, l.name, l.price, l.image, 
                   CONCAT(floor(GREATEST(0, TIMESTAMPDIFF(MINUTE,  NOW(), l.completion_date)) / 60) , ":",
                   LPAD(floor(GREATEST(0, TIMESTAMPDIFF(MINUTE,  NOW(), l.completion_date)) % 60), 2, "0")) AS time_left
                FROM lots AS l
                JOIN categories AS c ON l.category_id = c.id
                WHERE MATCH(l.name, l.description) AGAINST("' . $search_string . '" IN BOOLEAN MODE) AND (l.winner_id IS NULL) AND (l.completion_date > NOW()) 
                ORDER BY l.creation_date DESC LIMIT ' . $limit . ' OFFSET ' . $offset . ';';
        $data = get_data_from_db($connection, $sql, 'Невозможно получить результат полнотекстового поиска');
        return (!$data || was_error($data)) ? [] : $data;
    }

    /**
     * Функция возвращает результаты расчета для пагинации для результатов полнотекстовго поиска из get_search_result
     * Передается соединение, число записей на страницу и строка поиска
     * @param $connection
     * @param $limit
     * @param null $category_id
     * @return array|null
     */
    function get_search_result_pagination ($connection, $limit, $search_string) {
        $condition = ' WHERE MATCH(l.name, l.description) AGAINST("' . $search_string . '" IN BOOLEAN MODE) AND (l.winner_id IS NULL) AND (l.completion_date > NOW()) ';
        $sql = 'SELECT CEIL(COUNT(*) / ' . $limit . ') AS page_count, COUNT(*) AS total_records FROM lots  as l ' . $condition;
        $data = get_data_from_db($connection, $sql, 'Невозможно получить данные для пагинации результатов поиска', true);
        return (!$data || was_error($data)) ? [] : $data;
    }
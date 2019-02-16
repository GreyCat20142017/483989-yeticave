<?php
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
        return get_data_from_db($connection, $sql, 'Невозможно получить данные о лоте ' . $lot_id);
    }

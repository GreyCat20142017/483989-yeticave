<?php
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
     * @param $sum float
     * @return string
     */
    function get_rubles ($sum) {
        return number_format(ceil($sum), 0, '', ' ') . ' ' . '<b class="rub">р</b>';
    }

    /**
     * Функция возвращает разницу между текущим временем и ближайшей полуночью в виде строки в формате ЧЧ-ММ
     * @return string
     */
    function get_lot_lifetime () {
        return date_diff(date_create("now"), date_create("tomorrow midnight"))->format("%H:%I");
    }
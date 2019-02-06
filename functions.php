<?php
/**
 * Функция принимает два аргумента: имя файла шаблона и ассоциативный массив с данными для этого шаблона.
 * Функция возвращает строку — итоговый HTML-код с подставленными данными.
 * @param $name string
 * @param $data array
 * @return false|string
 */
function include_template($name, $data) {
$name = 'templates/' . $name;
$result = '';
if (!is_readable($name)) {
return $result;
}
ob_start();
extract($data);
require $name;
$result = ob_get_clean();
return $result;
};

/**
* Функция округляет число в большую сторону и возвращает строку с добавленным символом рубля и делением на разряды
* @param $sum float
* @return string
*/
function get_rubles($sum) {
return number_format(ceil($sum), 0,  '', ' ') . ' ' . '<b class="rub">р</b>';
};

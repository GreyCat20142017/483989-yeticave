<?php
    /**
     * Возвращает true, если залогинен пользователь
     * @return bool
     */
    function is_auth_user () {
        return isset($_SESSION[YETI_SESSION]);
    }

    /**
     * Возвращает параметр сессии (имя пользователя, id и т.д.) если есть залогиненный пользователь
     * @param $property_name
     * @return string
     */
    function get_auth_user_property ($property_name) {
        $current = '';
        if (isset($_SESSION[YETI_SESSION])) {
            $current = strip_tags(get_assoc_element($_SESSION[YETI_SESSION], $property_name));
        }
        return $current;
    }

    /**
     * Logout 
     *
     */
    function logout_current_user () {
        if (isset($_SESSION[YETI_SESSION])) {
            unset($_SESSION[YETI_SESSION]);
        }
    }
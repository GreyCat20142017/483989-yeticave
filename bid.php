<?php

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $bid = array_map(function ($item) {
            return trim(strip_tags($item));
        }, $_POST);

        $fields = [
            'cost' => ['description' => 'ставка', 'required' => true, 'validation_rules' => ['decimal_validation', 'auth_validation']]
        ];

        $errors = get_validation_result($fields, $bid, $_FILES);

        $status_ok = empty(get_form_validation_classname($errors));

        if ($status_ok) {

            $add_status = add_bid(
                $connection,
                $lot_id,
                get_auth_user_property('id'),
                intval(get_assoc_element($bid, 'cost')),
                $errors);

            if ($add_status) {
                header('Location:/lot.php?id=' . strip_tags($lot_id));
            }
        }
    }
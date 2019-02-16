<?php
    require_once('connection_config.php');
    require_once('functions.php');

    $connection = get_connection($connection_config);

    if (!$connection) {
        die('Невозможно подключиться к базе данных: ' . mysqli_connect_error());
    }

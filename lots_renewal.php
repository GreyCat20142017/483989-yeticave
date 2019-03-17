<?php

    session_start();
    require_once('init.php');

    if (!is_auth_user()) {
        http_response_code(403);
        exit();
    }

    if (renew_lots($connection)) {
        header('Location: index.php');
    }

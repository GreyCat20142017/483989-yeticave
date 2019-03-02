<?php

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $search_string = get_pure_data($_POST, 'search');
        header('Location:/search-result.php?search=' . $search_string);

    }
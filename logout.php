<?php
    session_start();

    require_once ('functions.php');

    logout_current_user();
    header('Location: index.php');
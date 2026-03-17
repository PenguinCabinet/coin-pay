<?php

function authenticate($db) {
    session_start();

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        exit;
    }

    return $_SESSION['user_id'];
}
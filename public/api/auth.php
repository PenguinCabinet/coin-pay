<?php

function authenticate($db) {

    $headers = getallheaders();

    if (!isset($headers["Authorization"])) {
        http_response_code(401);
        exit;
    }

    $token = str_replace("Bearer ", "", $headers["Authorization"]);

    $stmt = $db->prepare("SELECT * FROM tokens WHERE token = ?");
    $stmt->execute([$token]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || $row["expires"] < time()) {
        http_response_code(401);
        exit;
    }

    return $row["user_id"];
}
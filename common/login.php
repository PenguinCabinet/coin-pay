<?php

function login($username,$password,$db){

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user["password"])) {
        http_response_code(401);
        echo json_encode(["error" => "invalid login"]);
        exit;
    }

    $token = bin2hex(random_bytes(32));
    $expires = time() + 3600;

    $stmt = $db->prepare("INSERT INTO tokens (token, user_id, expires) VALUES (?, ?, ?)");
    $stmt->execute([$token, $user["id"], $expires]);

    http_response_code(200);
    return ([
        "token" => $token,
        "expires" => $expires
    ]);
}

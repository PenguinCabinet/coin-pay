<?php

/*データベースを読み込み*/
$db = new PDO("sqlite:database.db");

try{
    /*php://inputは、HTTPリクエストのBody*/
    $data = json_decode(file_get_contents("php://input"), true, 512, JSON_THROW_ON_ERROR);
}catch (JsonException $e) {
    http_response_code(400);
    exit;
}

$username = $data["username"];
$password = password_hash($data["password"], PASSWORD_DEFAULT);

$stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->execute([$username, $password]);

http_response_code(201);


<?php

/*データベースを読み込み*/
$db = new PDO("sqlite:database.db");

/*php://inputは、HTTPリクエストのBody*/
$data = json_decode(file_get_contents("php://input"), true);

$username = $data["username"];
$password = password_hash($data["password"], PASSWORD_DEFAULT);

$stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->execute([$username, $password]);

http_response_code(201);


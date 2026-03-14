<?php

$db = new PDO("sqlite:" . __DIR__ . "/database.db");

try{
    $data = json_decode(file_get_contents("php://input"), true, 512, JSON_THROW_ON_ERROR);
}catch (JsonException $e) {
    http_response_code(400);
    exit;
}

$username = $data["username"];
$password = $data["password"];

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
echo json_encode([
    "token" => $token,
    "expires" => $expires
]);
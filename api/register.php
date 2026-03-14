<?php
require "../common/login.php";

$db = new PDO("sqlite:../database.db");

if (!(isset($_POST["username"])&&isset($_POST["password"]))) {
    http_response_code(400);
    exit;
}
$username = $_POST["username"];
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);

$stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->execute([$username, $password]);

http_response_code(201);

//login($_POST["username"],$_POST["password"],$db);
echo json_encode(login($_POST["username"],$_POST["password"],$db));

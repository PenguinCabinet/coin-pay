<?php
require "../common/login.php";
$db = new PDO("sqlite:../database.db");

if (!(isset($_POST["username"])&&isset($_POST["password"]))) {
    http_response_code(400);
    exit;
}
http_response_code(200);
echo json_encode(login($_POST["username"],$_POST["password"],$db));

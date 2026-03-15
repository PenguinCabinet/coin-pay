<?php
require "../../common/login.php";

$db = new PDO("sqlite:../../database/database.db");

/*POSTされたデータのバリデーション*/
if (!(isset($_POST["username"])&&isset($_POST["password"]))) {
    http_response_code(400);
    exit;
}
if ($_POST["username"]==""||$_POST["password"]=="") {
    http_response_code(400);
    exit;
}

$username = $_POST["username"];
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);

$stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
/*ユーザーが存在する場合、終了 */
if ($user !== false) {
    http_response_code(400);
    exit;
}

$db->beginTransaction();

$stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$result = $stmt->execute([$username, $password]);
/*SQL文が実行に失敗した場合、ロールバック */
if (!$result) {
    $db->rollBack();
    http_response_code(500);
    exit;
}
$user_id = $db->lastInsertId();

$stmt = $db->prepare("INSERT INTO users_data (user_id, owncoin) VALUES (?, ?)");
$result = $stmt->execute([$user_id, 50]);
/*SQL文が実行に失敗した場合、ロールバック */
if (!$result) {
    $db->rollBack();
    http_response_code(500);
    exit;
}

$db->commit();

http_response_code(201);

echo json_encode(login($_POST["username"],$_POST["password"],$db));

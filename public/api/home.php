<?php

require "../../common/auth.php";

$db = new PDO("sqlite:../../database/database.db");
$userId = authenticate($db);

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$result = $stmt->execute([$userId]);
/*SQL文が実行に失敗した場合、ステータスコード500をレスポンス*/
if (!$result) {
    http_response_code(500);
    exit;
}
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT * FROM users_data WHERE user_id = ?");
$result = $stmt->execute([$userId]);
/*SQL文が実行に失敗した場合、ステータスコード500をレスポンス*/
if (!$result) {
    http_response_code(500);
    exit;
}
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "user_id" => $user_data["user_id"],
    "username" => $user["username"],
    "owncoin" => $user_data["owncoin"]
]);

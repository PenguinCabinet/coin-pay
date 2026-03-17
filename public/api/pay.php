<?php

require "../../common/auth.php";
require "../../common/csrf_token.php";

$db = new PDO("sqlite:../../database/database.db");
$userId = authenticate($db);

if (!(isset($_POST["price"])&&isset($_POST["recipient"]))) {
    http_response_code(400);
    exit;
}
if(!(is_numeric($_POST["price"])&&is_numeric($_POST["recipient"]))){
    http_response_code(400);
    exit;
}

/*csrf_tokenが適切ではない場合、ステータスコード403を返却*/
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    exit;
}

$price = (int)($_POST["price"]);
$recipient = $_POST["recipient"];

/*0以下の送金はできない */
if ($price<=0) {
    http_response_code(400);
    exit;
}
/*同一のユーザに送金はできない */
if ($recipient==$userId) {
    http_response_code(400);
    exit;
}

$db->beginTransaction();

$stmt = $db->prepare("SELECT * FROM users_data WHERE user_id = ?");
$result = $stmt->execute([$userId]);
/*SQL文が実行に失敗した場合、ロールバック */
if (!$result) {
    $db->rollBack();
    http_response_code(500);
    exit;
}
$sender_user_data = $stmt->fetch(PDO::FETCH_ASSOC);

$result = $stmt->execute([$recipient]);
/*SQL文が実行に失敗した場合、ロールバック */
if (!$result) {
    $db->rollBack();
    http_response_code(500);
    exit;
}
$recipient_user_data = $stmt->fetch(PDO::FETCH_ASSOC);

/*所有する金額<送金額ならロールバックする*/
if($sender_user_data["owncoin"]<$price){
    $db->rollBack();
    http_response_code(400);
    exit;
}

$stmt = $db->prepare("UPDATE users_data SET owncoin=? WHERE user_id=?");

/*送金元のユーザデータの所有コインを減らす */
$result = $stmt->execute([$sender_user_data["owncoin"]-$price,$userId]);
/*SQL文が実行に失敗した場合、ロールバック */
if (!$result) {
    $db->rollBack();
    http_response_code(500);
    exit;
}

/*送金先のユーザデータの所有コインを増やす */
$result = $stmt->execute([$recipient_user_data["owncoin"]+$price,$recipient]);
/*SQL文が実行に失敗した場合、ロールバック */
if (!$result) {
    $db->rollBack();
    http_response_code(500);
    exit;
}

$db->commit();

http_response_code(200);

/*リクエストのたび、CSRFトークンを再生成 */
regenerate_csrf_token();

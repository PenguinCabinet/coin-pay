<?php

require "auth.php";

$db = new PDO("sqlite:../../database/database.db");
$userId = authenticate($db);

if (!(isset($_POST["price"])&&isset($_POST["recipient"]))) {
    http_response_code(400);
    exit;
}
$price = (int)($_POST["price"]);
$recipient = $_POST["recipient"];

if ($price<=0) {
    http_response_code(400);
    exit;
}
error_log($recipient.$userId);
if ($recipient==$userId) {
    http_response_code(400);
    exit;
}

error_log("test3");

$db->beginTransaction();

$stmt1 = $db->prepare("SELECT * FROM users_data WHERE user_id = ?");
$stmt1->execute([$userId]);
$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);


/*所有する金額<送金額ならロールバックする*/
if($row1["owncoin"]<$price){
    $db->rollBack();
    http_response_code(400);
    exit;
}

$stmt2 = $db->prepare("UPDATE users_data SET owncoin=? WHERE user_id=?");
$stmt2->execute([$row1["owncoin"]-$price,$userId]);

$stmt2->execute([$row1["owncoin"]+$price,$recipient]);

$db->commit();

http_response_code(200);

echo json_encode([
    "user_id" => $row2["user_id"],
    "username" => $row1["username"],
    "owncoin" => $row2["owncoin"]
]);

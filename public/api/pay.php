<?php

require "../../common/auth.php";

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
if ($recipient==$userId) {
    http_response_code(400);
    exit;
}

$db->beginTransaction();

$stmt1 = $db->prepare("SELECT * FROM users_data WHERE user_id = ?");
$stmt1->execute([$userId]);
$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);

$stmt1->execute([$recipient]);
$recipient_row = $stmt1->fetch(PDO::FETCH_ASSOC);

/*所有する金額<送金額ならロールバックする*/
if($row1["owncoin"]<$price){
    $db->rollBack();
    http_response_code(400);
    exit;
}

$stmt3 = $db->prepare("UPDATE users_data SET owncoin=? WHERE user_id=?");
$stmt3->execute([$row1["owncoin"]-$price,$userId]);

$stmt3->execute([$recipient_row["owncoin"]+$price,$recipient]);

$db->commit();

http_response_code(200);

echo json_encode([
    "user_id" => $row2["user_id"],
    "username" => $row1["username"],
    "owncoin" => $row2["owncoin"]
]);

<?php

require "../../common/auth.php";

$db = new PDO("sqlite:../../database/database.db");
$userId = authenticate($db);

$stmt1 = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt1->execute([$userId]);
$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);

$stmt2 = $db->prepare("SELECT * FROM users_data WHERE user_id = ?");
$stmt2->execute([$userId]);
$row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "user_id" => $row2["user_id"],
    "username" => $row1["username"],
    "owncoin" => $row2["owncoin"]
]);

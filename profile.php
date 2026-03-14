<?php

require "auth.php";

$userId = authenticate();

echo json_encode([
    "message" => "Hello user " . $userId
]);

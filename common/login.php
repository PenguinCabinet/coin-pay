<?php

function login($username,$password,$db){

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $result = $stmt->execute([$username]);
    /*SQL文が実行に失敗した場合、ステータスコード500をレスポンス*/
    if (!$result) {
        http_response_code(500);
        exit;
    }
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    /**ユーザーが存在しないまたはパスワードが異なる場合、ステータスコード401をレスポンス */
    if (!$user || !password_verify($password, $user["password"])) {
        http_response_code(401);
        exit;
    }

    session_start();
    $_SESSION['user_id'] = $user["id"];

    return $_SESSION['user_id'];
}

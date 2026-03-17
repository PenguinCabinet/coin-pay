<?php

if (!is_dir("./database")) {
    mkdir("./database", 0755, true);
}

$db = new PDO("sqlite:./database/database.db");

$db->exec("
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    password TEXT
);

CREATE TABLE IF NOT EXISTS users_data (
    user_id TEXT PRIMARY KEY,
    owncoin INTEGER
);
");

echo "DB initialized";

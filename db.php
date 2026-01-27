<?php
session_start();

$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$port = getenv('DB_PORT') ?: 3306;

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4;ssl-mode=REQUIRED";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('DB接続失敗: ' . $e->getMessage());
}

$current_user_id   = $_SESSION['user_id'] ?? null;
$current_user_name = $_SESSION['user_name'] ?? null;

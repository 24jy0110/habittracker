<?php
session_start();

$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$port = getenv('DB_PORT');

// Aiven MySQL 必须使用 SSL
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/ca-certificates.crt',  // ★ Render 允许的 CA
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,  // ★ 禁用严格验证（否则会失败）
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("DB接続失敗: " . $e->getMessage());
}

// SESSION 用
$current_user_id   = $_SESSION['user_id']   ?? null;
$current_user_name = $_SESSION['user_name'] ?? null;


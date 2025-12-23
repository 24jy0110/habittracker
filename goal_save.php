<?php
require_once 'db.php';
require_once 'require_login.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start_date  = $_POST['start_date'] ?? '';
    $end_date    = $_POST['end_date'] ?? null;
    $days        = $_POST['days'] ?? [];

    if ($title === '' || $start_date === '' || empty($days)) {
        echo '入力が不足しています。<br>';
        echo '<a href="goal_form.php">戻る</a>';
        exit;
    }

    $days_str = implode(',', $days);

    $sql = "INSERT INTO goals (user_id, title, description, start_date, end_date, days_of_week)
            VALUES (:user_id, :title, :description, :start_date, :end_date, :days_of_week)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $current_user_id, PDO::PARAM_INT);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
    if ($end_date === '') {
        $stmt->bindValue(':end_date', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
    }
    $stmt->bindValue(':days_of_week', $days_str, PDO::PARAM_STR);
    $stmt->execute();

    header('Location: goal_success.php');
    exit;
}

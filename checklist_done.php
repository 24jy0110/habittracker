<?php
require_once 'db.php';
require_once 'require_login.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $goal_id = (int)($_POST['goal_id'] ?? 0);
    $today   = date('Y-m-d');

    if ($goal_id > 0) {
        $sql = "SELECT id FROM progress
                WHERE user_id = :uid AND goal_id = :gid AND date = :date";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uid' => $current_user_id,
            ':gid' => $goal_id,
            ':date'=> $today
        ]);
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$exists) {
            $sql = "INSERT INTO progress (goal_id, user_id, date, is_done)
                    VALUES (:gid, :uid, :date, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':gid' => $goal_id,
                ':uid' => $current_user_id,
                ':date'=> $today
            ]);
        }
    }
}

header('Location: checklist.php');
exit;

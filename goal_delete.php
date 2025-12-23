<?php
require_once 'db.php';
require_once 'require_login.php';

$goal_id = (int)($_POST['goal_id'] ?? 0);

if ($goal_id > 0) {
    $sql = "DELETE FROM `progress` WHERE goal_id = :gid AND user_id = :uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':gid' => $goal_id,
        ':uid' => $current_user_id
    ]);

    $sql = "DELETE FROM `goals` WHERE id = :gid AND user_id = :uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':gid' => $goal_id,
        ':uid' => $current_user_id
    ]);
}

header('Location: goal_list.php');
exit;

<?php
require_once 'db.php';
require_once 'require_login.php';

$group_id = (int)($_POST['group_id'] ?? 0);

// 再次确认是否是 owner
$sql = "SELECT * FROM `groups` WHERE id = :gid AND owner_id = :uid";
$stmt = $pdo->prepare($sql);
$stmt->execute([':gid'=>$group_id, ':uid'=>$current_user_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) { echo "権限がありません"; exit; }

// 获取所有成员的小组目标 goals.id
$sql = "SELECT id FROM `goals` WHERE group_id = :gid";
$stmt = $pdo->prepare($sql);
$stmt->execute([':gid'=>$group_id]);
$goal_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

// 删除 progress
if (!empty($goal_ids)) {
    $placeholder = implode(",", array_fill(0, count($goal_ids), "?"));
    $sql = "DELETE FROM `progress` WHERE goal_id IN ($placeholder)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($goal_ids);
}

// 删除 goals
$sql = "DELETE FROM `goals` WHERE group_id = :gid";
$stmt = $pdo->prepare($sql);
$stmt->execute([':gid'=>$group_id]);

// 删除 group_members
$sql = "DELETE FROM `group_members` WHERE group_id = :gid";
$stmt = $pdo->prepare($sql);
$stmt->execute([':gid'=>$group_id]);

// 删除 group 本身
$sql = "DELETE FROM `groups` WHERE id = :gid";
$stmt = $pdo->prepare($sql);
$stmt->execute([':gid'=>$group_id]);

header("Location: groups.php");
exit;

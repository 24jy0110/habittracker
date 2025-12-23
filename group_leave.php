<?php
// group_leave.php
require_once 'db.php';
require_once 'require_login.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = (int)($_POST['group_id'] ?? 0);

    if ($group_id > 0) {
        // 1. このユーザーを group_members から削除
        $sql = "DELETE FROM group_members
                WHERE group_id = :gid AND user_id = :uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':gid' => $group_id,
            ':uid' => $current_user_id
        ]);

        // 2. このユーザーの「このグループ目標」を取得（goals.id を取る）
        $sql = "SELECT id FROM goals
                WHERE user_id = :uid AND group_id = :gid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uid' => $current_user_id,
            ':gid' => $group_id
        ]);
        $goal_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($goal_ids)) {
            // 3. まず progress から関連打刻記録を削除
            $in_placeholders = implode(',', array_fill(0, count($goal_ids), '?'));
            $sql = "DELETE FROM progress WHERE user_id = ? AND goal_id IN ($in_placeholders)";
            $stmt = $pdo->prepare($sql);

            // bind 参数：第一个是 user_id，后面是 goal_ids
            $params = array_merge([$current_user_id], $goal_ids);
            $stmt->execute($params);

            // 4. 次に goals からグループ目標を削除
            $sql = "DELETE FROM goals
                    WHERE user_id = ? AND group_id = ?"; // このユーザーのこのグループ目標だけ
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$current_user_id, $group_id]);
        }
    }
}

// 完了したらグループ一覧に戻る
header('Location: groups.php');
exit;

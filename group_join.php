<?php
require_once 'db.php';
require_once 'require_login.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = (int)($_POST['group_id'] ?? 0);

    if ($group_id > 0) {

        // 1. group_members に存在しなければ追加
        $sql = "SELECT id FROM group_members WHERE group_id = :gid AND user_id = :uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':gid' => $group_id,
            ':uid' => $current_user_id
        ]);
        $existing_member = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existing_member) {
            $sql = "INSERT INTO group_members (group_id, user_id)
                    VALUES (:gid, :uid)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':gid' => $group_id,
                ':uid' => $current_user_id
            ]);
        }

        // 2. このユーザーの「このグループ目標」が goals にもうあるかチェック
        $sql = "SELECT id FROM `goals` WHERE user_id = :uid AND group_id = :gid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uid' => $current_user_id,
            ':gid' => $group_id
        ]);
        $existing_goal = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existing_goal) {
            // 3. グループ情報を取得して、共通目標を自動生成
            $sql = "SELECT name, description FROM `groups` WHERE id = :gid";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':gid' => $group_id]);
            $group = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($group) {
                $title       = $group['name'];  // 例：「読書習慣グループ」
                $desc        = 'グループ「' . $group['name'] . '」の共通目標です。';
                $start_date  = date('Y-m-d');
                $end_date    = null;
                $days_of_week = 'Mon,Tue,Wed,Thu,Fri,Sat,Sun'; // 毎日

                $sql = "INSERT INTO goals
                        (user_id, group_id, title, description, start_date, end_date, days_of_week)
                        VALUES
                        (:uid, :gid, :title, :description, :start_date, :end_date, :days_of_week)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':uid'          => $current_user_id,
                    ':gid'          => $group_id,
                    ':title'        => $title,
                    ':description'  => $desc,
                    ':start_date'   => $start_date,
                    ':end_date'     => $end_date,
                    ':days_of_week' => $days_of_week
                ]);
            }
        }
    }
}

header('Location: groups.php');
exit;

<?php
// group_create.php
require_once 'db.php';
require_once 'require_login.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        $error = 'グループ名を入力してください。';
    } else {
        // トランザクション（失敗したらまとめてロールバックしたい場合）
        $pdo->beginTransaction();
        try {
            // 1. groups に追加（owner_id を自分にする）
            $sql = "INSERT INTO `groups` (name, description, owner_id)
                    VALUES (:name, :description, :owner_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name'       => $name,
                ':description'=> $description,
                ':owner_id'   => $current_user_id,
            ]);

            $new_group_id = (int)$pdo->lastInsertId();

            // 2. 自分自身を group_members に追加
            $sql = "INSERT INTO `group_members` (group_id, user_id)
                    VALUES (:gid, :uid)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':gid' => $new_group_id,
                ':uid' => $current_user_id,
            ]);

            // 3. 自分用の「グループ共通目標」を goals に自動作成
            //    タイトルはグループ名、毎日実行にしておく
            $title       = $name;
            $desc_goal   = 'グループ「' . $name . '」の共通目標です。';
            $start_date  = date('Y-m-d');
            $end_date    = null;
            $days_of_week = 'Mon,Tue,Wed,Thu,Fri,Sat,Sun';  // 毎日

            $sql = "INSERT INTO goals
                    (user_id, group_id, title, description, start_date, end_date, days_of_week)
                    VALUES
                    (:uid, :gid, :title, :description, :start_date, :end_date, :days_of_week)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':uid'          => $current_user_id,
                ':gid'          => $new_group_id,
                ':title'        => $title,
                ':description'  => $desc_goal,
                ':start_date'   => $start_date,
                ':end_date'     => $end_date,
                ':days_of_week' => $days_of_week,
            ]);

            $pdo->commit();

            // 作成完了 → groups 一覧へ
            header('Location: groups.php');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'グループ作成中にエラーが発生しました：' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>グループ作成</title>
    <link rel="stylesheet" href="global.css">
</head>
<body class="bg-light">
<?php require_once 'sidebar.php'; ?>
<div class="container py-4">
    <h1>新しいグループを作成</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo nl2br(htmlspecialchars($error, ENT_QUOTES, 'UTF-8')); ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">グループ名</label>
            <input type="text" name="name" class="form-control"
                   value="<?php echo isset($name) ? htmlspecialchars($name, ENT_QUOTES, 'UTF-8') : ''; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">グループ説明（任意）</label>
            <textarea name="description" class="form-control" rows="4"><?php
                echo isset($description) ? htmlspecialchars($description, ENT_QUOTES, 'UTF-8') : '';
            ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">作成する</button>
        <a href="groups.php" class="btn btn-secondary ms-2">戻る</a>
    </form>
</div>
</body>
</html>

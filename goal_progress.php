<?php
require_once 'db.php';
require_once 'require_login.php';

$goal_id = (int)($_GET['goal_id'] ?? 0);

$sql = "SELECT * FROM goals WHERE id = :gid AND user_id = :uid";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':gid' => $goal_id,
    ':uid' => $current_user_id
]);
$goal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$goal) {
    echo "目標が見つかりません。";
    exit;
}

$sql = "SELECT date FROM progress
        WHERE goal_id = :gid AND user_id = :uid
        ORDER BY date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':gid' => $goal_id,
    ':uid' => $current_user_id
]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>達成記録</title>
    <link rel="stylesheet" href="global.css">

    <style>
        .page-title {
            font-weight: 800;
            font-size: 1.8rem;
            color: #6b62ff;
            text-shadow: 0 2px 6px rgba(150,168,255,0.35);
        }

        .glass-card {
            background: rgba(255,255,255,0.55);
            border-radius: 20px;
            padding: 20px;
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255,255,255,0.6);
            box-shadow: 0 8px 25px rgba(150,160,220,0.25);
        }

        .record-item {
            background: rgba(255,255,255,0.45);
            padding: 12px 16px;
            margin-bottom: 8px;
            border-radius: 14px;
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 14px rgba(150,160,220,0.18);
            font-weight: 600;
            color: #555;
        }

        .btn-back {
            margin-top: 18px;
        }
    </style>
</head>

<body class="bg-light">
<?php require_once 'sidebar.php'; ?>

<div class="container py-4">

    <h1 class="page-title mb-4">
        📘 達成記録：<?php echo htmlspecialchars($goal['title'], ENT_QUOTES, 'UTF-8'); ?>
    </h1>

    <?php if (empty($records)): ?>

        <div class="glass-card mb-3">
            まだ達成記録がありません…<br>
            今日から少しずつがんばろうね！🌱✨
        </div>

    <?php else: ?>

        <div class="glass-card mb-4">
            <h5 class="mb-3">🔥 達成した日一覧</h5>

            <?php foreach ($records as $r): ?>
                <div class="record-item">
                    ✔ <?php echo htmlspecialchars($r['date'], ENT_QUOTES, 'UTF-8'); ?> に達成 🎉
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

    <a href="goal_list.php" class="btn-cute-outline btn-back">← 目標一覧へ戻る</a>
</div>

</body>
</html>

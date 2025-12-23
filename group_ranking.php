<?php
require_once 'db.php';
require_once 'require_login.php';

$group_id = (int)($_GET['group_id'] ?? 0);
if ($group_id <= 0) {
    header('Location: groups.php');
    exit;
}

// グループ名取得
$sql = "SELECT name FROM `groups` WHERE id = :gid";
$stmt = $pdo->prepare($sql);
$stmt->execute([':gid' => $group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    echo "グループが見つかりません。";
    exit;
}

// 今週のランキング（上位3名）
$sql = "
    SELECT u.name, COUNT(p.id) AS count_done
    FROM progress p
    JOIN `goals` g ON p.goal_id = g.id
    JOIN group_members gm
      ON gm.user_id = p.user_id
     AND gm.group_id = :gid
    JOIN users u ON u.id = p.user_id
    WHERE g.group_id = :gid
      AND YEARWEEK(p.date, 1) = YEARWEEK(CURDATE(), 1)
    GROUP BY u.id, u.name
    ORDER BY count_done DESC
    LIMIT 3
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':gid' => $group_id]);
$ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
<meta charset="UTF-8">
<title>グループランキング</title>
<link rel="stylesheet" href="global.css">

<style>
    .page-title {
        font-weight: 800;
        color: #6b62ff;
        text-shadow: 0 2px 6px rgba(150,168,255,0.35);
    }

    .glass-card {
        background: rgba(255,255,255,0.55);
        padding: 20px;
        border-radius: 18px;
        backdrop-filter: blur(15px);
        box-shadow: 0 8px 20px rgba(150,160,220,0.25);
        margin-bottom: 20px;
    }

    .rank-item {
        background: rgba(255,255,255,0.65);
        padding: 14px;
        border-radius: 12px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        font-size: 1.1rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(200,200,255,0.4);
    }

    .rank-medal {
        font-size: 1.4rem;
        margin-right: 8px;
    }

    .btn-back {
        margin-top: 15px;
    }
</style>
</head>

<body class="bg-light">

<?php require_once 'sidebar.php'; ?>

<div class="container py-4">

    <h1 class="page-title mb-3">
        🏆 今週のランキング  
        <div class="mt-2" style="font-size:1.2rem;">
            グループ：<?= htmlspecialchars($group['name']) ?>
        </div>
    </h1>

    <div class="glass-card">
        <p class="text-muted">共通目標に対する「今週の達成回数」上位3名です。</p>
    </div>

    <?php if (empty($ranking)): ?>

        <div class="glass-card">
            まだ今週の達成記録がありません…🕊  
            みんなで頑張ろう〜！💪🌈
        </div>

    <?php else: ?>

        <div class="glass-card">
            <?php
            $medals = ["🥇", "🥈", "🥉"];
            $i = 0;
            foreach ($ranking as $row):
            ?>
                <div class="rank-item">
                    <span>
                        <span class="rank-medal"><?= $medals[$i] ?></span>
                        <?= htmlspecialchars($row['name']) ?>
                    </span>
                    <strong><?= (int)$row['count_done'] ?> 回</strong>
                </div>
            <?php $i++; endforeach; ?>
        </div>

    <?php endif; ?>

    <a href="group_detail.php?group_id=<?= $group_id ?>" class="btn-cute-outline btn-back">
        ← グループ詳細へ戻る
    </a>

</div>

</body>
</html>

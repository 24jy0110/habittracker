<?php
require_once 'db.php';
require_once 'require_login.php';

// 連続達成日数を計算する関数（原样保留）
function calc_streak(PDO $pdo, int $user_id, int $goal_id): int {
    $today = date('Y-m-d');

    $sql = "SELECT date
            FROM progress
            WHERE user_id = :uid AND goal_id = :gid AND date <= :today
            ORDER BY date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':uid'   => $user_id,
        ':gid'   => $goal_id,
        ':today' => $today
    ]);
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($rows)) return 0;
    if ($rows[0] !== $today) return 0;

    $streak = 1;
    $expected = new DateTime($today);

    for ($i = 1; $i < count($rows); $i++) {
        $expected->modify('-1 day');
        if ($rows[$i] === $expected->format('Y-m-d')) {
            $streak++;
        } else {
            break;
        }
    }
    return $streak;
}

// 目標取得
$sql = "SELECT * FROM goals WHERE user_id = :uid ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':uid', $current_user_id, PDO::PARAM_INT);
$stmt->execute();
$goals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 可爱 emoji
$emojis = ["📘","🌟","🧸","🎀","💗","🌈","✏️","📖","🪄","🐣"];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>目標一覧</title>
    <link rel="stylesheet" href="global.css">

    <style>
        .goal-card {
            background: rgba(255, 255, 255, 0.45);
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 20px;
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: 0 8px 28px rgba(150, 160, 220, 0.2);
            transition: 0.25s ease;
        }
        .goal-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 36px rgba(150, 160, 220, 0.28);
        }
        .goal-icon {
            width: 48px;
            height: 48px;
            border-radius: 999px;
            background: linear-gradient(135deg, #b7a8ff, #81c7ff);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.4rem;
            margin-right: 15px;
            box-shadow: 0 3px 12px rgba(160, 160, 255, .35);
        }

        .goal-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #5b59b8;
        }
        .goal-period, .goal-streak {
            font-size: 0.9rem;
            color: #6b6f87;
        }

        .btn-view {
            border-radius: 12px;
            padding: 8px 14px;
            font-weight: 600;
            background: rgba(255,255,255,0.6);
            border: 1px solid #81c7ff;
            color: #5577aa;
        }
        .btn-view:hover {
            background: rgba(255,255,255,0.85);
        }

        .btn-delete {
            border-radius: 12px;
            padding: 8px 14px;
            font-weight: 600;
            background: rgba(255,255,255,0.6);
            border: 1px solid #ff9db5;
            color: #d64565;
        }
        .btn-delete:hover {
            background: #ffe3ea;
        }

        .page-title {
            font-weight: 800;
            color: #6b62ff;
            text-shadow: 0 2px 6px rgba(150,168,255,0.35);
        }
    </style>
</head>
<body class="bg-light">

<?php require_once 'sidebar.php'; ?>

<div class="container py-4">

    <h1 class="page-title mb-4">🎯 目標一覧</h1>

    <!-- 返回 + 新建按钮 -->
    <div class="mb-3 d-flex gap-2">
        <a href="index.php" class="btn-cute-outline">← メニューへ戻る</a>
        <a href="goal_form.php" class="btn-cute">＋ 新しい目標を登録</a>
    </div>

    <?php if (empty($goals)): ?>

        <div class="glass-card text-center mb-4">
            まだ目標がありません…<br>
            今日から一緒にがんばろう！💗🧸
        </div>

    <?php else: ?>

        <?php foreach ($goals as $g): ?>
            <?php $streak = calc_streak($pdo, $current_user_id, (int)$g['id']); ?>

            <div class="goal-card d-flex justify-content-between align-items-center">

                <div class="d-flex align-items-center">
                    <div class="goal-icon">
                        <?= $emojis[array_rand($emojis)] ?>
                    </div>

                    <div>
                        <div class="goal-title">
                            <?= htmlspecialchars($g['title'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>

                        <div class="goal-period">
                            📅 <?= htmlspecialchars($g['start_date']); ?> 〜
                            <?= htmlspecialchars($g['end_date'] ?: '未設定'); ?>
                        </div>

                        <div class="goal-streak">
                            🔥 連続達成日数：<strong><?= $streak ?></strong> 日
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column">
                    <a href="goal_progress.php?goal_id=<?= (int)$g['id']; ?>"
                       class="btn-view mb-2 text-center">
                        達成記録を見る
                    </a>

                    <form method="post" action="goal_delete.php"
                          onsubmit="return confirm('本当にこの目標を削除しますか？');">
                        <input type="hidden" name="goal_id" value="<?= (int)$g['id']; ?>">
                        <button class="btn-delete">削除</button>
                    </form>
                </div>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>
</div>

</body>
</html>

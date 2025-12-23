<?php
require_once 'db.php';
require_once 'require_login.php';

$today   = date('Y-m-d');
$weekday = date('D');

// 今日の実行対象となる目標を取得
$sql = "SELECT g.*, p.id AS progress_id
        FROM goals g
        LEFT JOIN progress p
          ON g.id = p.goal_id
         AND p.date = :today
         AND p.user_id = :user_id
        WHERE g.user_id = :user_id
          AND g.start_date <= :today
          AND (g.end_date IS NULL OR g.end_date >= :today)
          AND g.days_of_week LIKE :weekday_pattern
        ORDER BY g.id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':today', $today);
$stmt->bindValue(':user_id', $current_user_id);
$stmt->bindValue(':weekday_pattern', '%' . $weekday . '%');
$stmt->execute();
$goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>今日のチェックリスト</title>
    <link rel="stylesheet" href="global.css">

    <style>
        /* タイトル */
        .page-title {
            font-weight: 800;
            color: #6b62ff;
            text-shadow: 0 2px 6px rgba(150,168,255,0.35);
        }

        /* ガラスの目標カード */
        .goal-card {
            background: rgba(255,255,255,0.55);
            padding: 18px;
            border-radius: 20px;
            margin-bottom: 18px;
            backdrop-filter: blur(18px);
            box-shadow: 0 6px 20px rgba(150,160,220,0.25);
            transition: 0.25s;
        }
        .goal-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(150,160,220,0.35);
        }

        /* 可愛い達成ボタン */
        .btn-cute-done {
            background: linear-gradient(135deg, #81c7ff, #b7a8ff);
            padding: 8px 16px;
            font-weight: 700;
            border-radius: 14px;
            border: none;
            color: white;
            box-shadow: 0 4px 12px rgba(130,150,230,0.35);
            transition: 0.25s;
        }
        .btn-cute-done:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(130,150,230,0.45);
        }

        /* 達成済みのバッジ */
        .badge-done {
            display: inline-block;
            padding: 6px 12px;
            background: #90e5a9;
            border-radius: 12px;
            font-weight: 700;
            color: #2e6b3a;
        }

        /* カード内の説明文字 */
        .goal-desc {
            font-size: 0.9rem;
            color: #6b7280;
        }
    </style>
</head>

<body class="bg-light">
<?php require_once 'sidebar.php'; ?>

<div class="container py-4">

    <h1 class="page-title mb-3">
        今日のチェックリスト（<?php echo htmlspecialchars($today); ?>）
    </h1>

    <!-- 上部メニュー -->
    <div class="glass-card p-3 mb-4">
        <strong><?php echo htmlspecialchars($current_user_name); ?></strong> さん、今日もがんばろう！💪✨
        <div class="mt-2">
            <a href="index.php" class="btn-cute-outline me-2">メニューへ戻る</a>
            <a href="goal_form.php" class="btn-cute me-2">＋ 新しい目標を登録</a>
            <a href="goal_list.php" class="btn-cute-outline">目標一覧を見る</a>
        </div>
    </div>

    <?php if (empty($goals)): ?>

        <div class="glass-card p-3">
            今日実行する目標はありません。  
            <br>ゆっくり休むのも大事だよ〜🌸
        </div>

    <?php else: ?>

        <?php foreach ($goals as $g): ?>
            <div class="goal-card">

                <!-- 目標タイトル -->
                <h4 class="mb-2">
                    <?php echo htmlspecialchars($g['title']); ?>
                </h4>

                <!-- 説明 -->
                <?php if (!empty($g['description'])): ?>
                    <p class="goal-desc mb-2">
                        説明：<?php echo nl2br(htmlspecialchars($g['description'])); ?>
                    </p>
                <?php endif; ?>

                <!-- 期間 -->
                <p class="goal-desc mb-2">
                    期間：<?php echo htmlspecialchars($g['start_date']); ?> 〜
                    <?php echo htmlspecialchars($g['end_date'] ?: '未設定'); ?>
                </p>

                <!-- 達成かどうか -->
                <?php if ($g['progress_id']): ?>
                    <span class="badge-done">✔ 今日分は達成済み！</span>
                <?php else: ?>
                    <form action="checklist_done.php" method="post" class="d-inline">
                        <input type="hidden" name="goal_id" value="<?php echo (int)$g['id']; ?>">
                        <button type="submit" class="btn-cute-done">
                            ✨ 今日の分を達成にする
                        </button>
                    </form>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

</body>
</html>

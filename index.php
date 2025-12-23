<?php
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>習慣化WEB</title>
    <link rel="stylesheet" href="global.css">
    <style>
        .page-title {
            font-weight: 800;
            color: #6b62ff;
            text-shadow: 0 2px 6px rgba(150,168,255,0.35);
        }
        .welcome-sub {
            color: #6b7280;
            font-size: 0.95rem;
        }
        .shortcut-card {
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .shortcut-icon {
            font-size: 1.8rem;
            margin-bottom: 8px;
        }
        .shortcut-title {
            font-weight: 700;
            margin-bottom: 4px;
            color: #4b4ba8;
        }
        .shortcut-text {
            font-size: 0.85rem;
            color: #6b7280;
        }
    </style>
</head>
<body class="bg-light">

<?php require_once 'sidebar.php'; ?>

<div class="container py-4">
    <h1 class="page-title mb-4">習慣化WEB</h1>

    <?php if ($current_user_id): ?>

        <!-- ログイン済み用：大きなウェルカムガラスカード -->
        <div class="glass-card mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="mb-1">
                        ようこそ、
                        <strong><?php echo htmlspecialchars($current_user_name, ENT_QUOTES, 'UTF-8'); ?></strong> さん！
                    </div>
                    <div class="welcome-sub">
                        今日も少しずつ、一緒に習慣化がんばろう💗🌈
                    </div>
                </div>
                <div style="font-size: 2.2rem;">🧸✨</div>
            </div>
        </div>

        <!-- ショートカットのガラスカード群 -->
        <div class="row g-3">

            <div class="col-md-6">
                <a href="goal_form.php" class="shortcut-card">
                    <div class="glass-card">
                        <div class="shortcut-icon">📝</div>
                        <div class="shortcut-title">目標を登録する</div>
                        <div class="shortcut-text">
                            新しい習慣やチャレンジを追加してみよう。
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6">
                <a href="goal_list.php" class="shortcut-card">
                    <div class="glass-card">
                        <div class="shortcut-icon">🎯</div>
                        <div class="shortcut-title">目標一覧を見る</div>
                        <div class="shortcut-text">
                            いま取り組んでいる目標と連続達成日数をチェック。
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6">
                <a href="checklist.php" class="shortcut-card">
                    <div class="glass-card">
                        <div class="shortcut-icon">✔️</div>
                        <div class="shortcut-title">今日のチェック</div>
                        <div class="shortcut-text">
                            今日のタスクを達成済みにして、コツコツ記録しよう。
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6">
                <a href="stats.php" class="shortcut-card">
                    <div class="glass-card">
                        <div class="shortcut-icon">📈</div>
                        <div class="shortcut-title">達成グラフ</div>
                        <div class="shortcut-text">
                            直近の達成状況をグラフで振り返ってみよう。
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-12">
                <a href="groups.php" class="shortcut-card">
                    <div class="glass-card">
                        <div class="shortcut-icon">👥</div>
                        <div class="shortcut-title">グループに参加する</div>
                        <div class="shortcut-text">
                            同じ目標を持つ仲間と一緒にがんばると、もっと続けやすくなるよ。
                        </div>
                    </div>
                </a>
            </div>

        </div>

    <?php else: ?>

        <!-- 未ログイン用：ようこそガラスカード -->
        <div class="glass-card mb-4 text-center">
            <h2 class="mb-3">ようこそ！✨</h2>
            <p class="mb-2">
                ログインすると、目標登録・チェックリスト・グループ機能など<br>
                習慣化をサポートする機能が使えるようになります。
            </p>
            <p class="mb-3">
                今日も習慣化がんばろう！💪🔥
            </p>
            <a href="login.php" class="btn-cute me-2">ログイン</a>
            <a href="register.php" class="btn-cute-outline">新規登録</a>
        </div>

    <?php endif; ?>
</div>

</body>
</html>


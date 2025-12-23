<?php
require_once 'db.php';
require_once 'require_login.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>目標登録完了</title>
    <link rel="stylesheet" href="global.css">

    <style>
        body {
            animation: fadeIn 0.6s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        .success-card {
            max-width: 520px;
            margin: 80px auto;
            padding: 30px 28px;
            background: rgba(255,255,255,0.55);
            border-radius: 22px;
            backdrop-filter: blur(18px);
            box-shadow: 0 10px 28px rgba(150,160,220,0.25);
            text-align: center;
            animation: floatUp 0.7s ease-out;
        }

        @keyframes floatUp {
            from { transform: translateY(20px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }

        .emoji {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .auto-msg {
            font-size: 0.95rem;
            color: #666;
            margin-top: 10px;
        }
    </style>

    <script>
        // 自動遷移（3秒後）
        setTimeout(() => {
            window.location.href = "checklist.php";
        }, 3000);
    </script>
</head>

<body class="bg-light">

<?php require_once 'sidebar.php'; ?>

<div class="container">

    <div class="success-card">

        <div class="emoji">🎉</div>

        <h2 class="page-title">目標を登録しました！</h2>

        <p class="mt-2">
            今日から新しい習慣が始まるよ！✨<br>
            一緒にコツコツがんばろうね🧸💕
        </p>

        <p class="auto-msg">3秒後にチェックリストへ移動します…</p>

        <a href="checklist.php" class="btn-cute mt-3">
            👉 すぐ確認する
        </a>

    </div>

</div>

</body>
</html>


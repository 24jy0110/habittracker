<?php
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>登録完了</title>
    <link rel="stylesheet" href="global.css">

    <style>
        .page-title {
            font-weight: 800;
            color: #6b62ff;
            text-shadow: 0 2px 6px rgba(150,168,255,0.35);
        }

        .success-card {
            max-width: 600px;
            margin: 40px auto;
            padding: 28px 30px;
            background: rgba(255,255,255,0.55);
            border-radius: 22px;
            backdrop-filter: blur(18px);
            box-shadow: 0 10px 30px rgba(150,160,220,0.25);
            text-align: center;
        }

        .success-emoji {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .btn-area {
            margin-top: 22px;
            display: flex;
            justify-content: center;
            gap: 12px;
        }
    </style>
</head>

<body class="bg-light">

<?php require_once 'sidebar.php'; ?>

<div class="container py-4">

    <div class="success-card">

        <div class="success-emoji">🎉</div>

        <h1 class="page-title mb-3">ユーザー登録が完了しました！</h1>

        <p class="mb-3">
            登録したログインIDとパスワードで、ログインできます。<br>
            今日から一緒に習慣化をがんばろうね！💪✨
        </p>

        <div class="btn-area">
            <a href="login.php" class="btn-cute">ログインへ進む</a>
            <a href="index.php" class="btn-cute-outline">トップへ戻る</a>
        </div>

    </div>

</div>

</body>
</html>

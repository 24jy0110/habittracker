<?php
require_once 'db.php';
require_once 'require_login.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン成功</title>
    <link rel="stylesheet" href="global.css">
    <style>
        .success-wrapper {
            max-width: 600px;
            margin: 40px auto;
        }
        .success-card {
            background: rgba(255,255,255,0.65);
            border-radius: 22px;
            padding: 28px 30px;
            backdrop-filter: blur(18px);
            box-shadow: 0 10px 30px rgba(150,160,220,0.25);
            text-align: center;
        }
        .page-title {
            font-weight: 800;
            color: #6b62ff;
            text-shadow: 0 2px 6px rgba(150,168,255,0.35);
        }
        .success-emoji {
            font-size: 3rem;
            margin-bottom: 10px;
            animation: pop 0.8s ease-out;
        }
        .countdown {
            font-weight: 700;
            color: #6b62ff;
        }
        @keyframes pop {
            0% { transform: scale(0.7); opacity: 0; }
            60% { transform: scale(1.08); opacity: 1; }
            100% { transform: scale(1); }
        }
        .bar-wrap {
            margin-top: 14px;
            width: 100%;
            height: 6px;
            border-radius: 999px;
            background: rgba(255,255,255,0.8);
            overflow: hidden;
        }
        .bar-inner {
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg,#81c7ff,#b7a8ff);
            transform-origin: left center;
            animation: shrink 3s linear forwards;
        }
        @keyframes shrink {
            from { transform: scaleX(1); }
            to   { transform: scaleX(0); }
        }
        .btn-area {
            margin-top: 18px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
    </style>
</head>
<body class="bg-light">

<?php require_once 'sidebar.php'; ?>

<div class="success-wrapper">
    <div class="success-card">
        <div class="success-emoji">✅</div>

        <h1 class="page-title mb-2">ログインしました！</h1>

        <p class="mb-2">
            <?php echo htmlspecialchars($current_user_name, ENT_QUOTES, 'UTF-8'); ?> さん、<br>
            今日も習慣化がんばろう〜！💪🌈
        </p>

        <p class="mb-1">
            <span class="countdown" id="countNum">3</span> 秒後にトップページへ自動で移動します。
        </p>

        <div class="bar-wrap">
            <div class="bar-inner"></div>
        </div>

        <div class="btn-area">
            <a href="index.php" class="btn-cute">すぐにトップへ行く</a>
        </div>
    </div>
</div>

<script>
    let sec = 3;
    const span = document.getElementById('countNum');

    const timer = setInterval(() => {
        sec--;
        if (sec <= 0) {
            clearInterval(timer);
        }
        if (sec >= 0) {
            span.textContent = sec;
        }
    }, 1000);

    setTimeout(() => {
        window.location.href = 'index.php';
    }, 3000);
</script>

</body>
</html>

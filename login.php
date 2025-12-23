<?php
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_id = trim($_POST['login_id'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login_id === '' || $password === '') {
        $error = 'ログインIDとパスワードを入力してください。';
    } else {
        $sql = "SELECT * FROM users WHERE login_id = :login_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':login_id', $login_id, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header('Location: login_success.php');
            exit;
        } else {
            $error = 'ログインIDまたはパスワードが違います。';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <link rel="stylesheet" href="global.css">
    <style>
        .page-title {
            font-weight: 800;
            color: #6b62ff;
            text-shadow: 0 2px 6px rgba(150,168,255,0.35);
        }
        .login-wrapper {
            max-width: 480px;
            margin: 40px auto;
        }
        .login-card {
            background: rgba(255,255,255,0.65);
            border-radius: 22px;
            padding: 24px 26px;
            backdrop-filter: blur(18px);
            box-shadow: 0 10px 30px rgba(150,160,220,0.25);
        }
        .form-label {
            font-weight: 600;
            color: #555;
            display: block;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }
        .input-cute {
            width: 100%;
            border-radius: 12px;
            border: 1px solid #c6d4ff;
            padding: 8px 10px;
            box-sizing: border-box;
            font-size: 0.95rem;
            transition: 0.2s;
        }
        .input-cute:focus {
            outline: none;
            border-color: #8cb4ff;
            box-shadow: 0 0 8px rgba(130,150,250,0.4);
        }
        .error-box {
            background: rgba(255,210,210,0.75);
            border-radius: 14px;
            padding: 10px 14px;
            margin-bottom: 14px;
            border-left: 4px solid #ff7a7a;
            color: #b3263a;
            font-size: 0.9rem;
        }
        .btn-row {
            margin-top: 18px;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }
        .link-small {
            font-size: 0.85rem;
            margin-top: 10px;
            text-align: right;
        }
    </style>
</head>
<body class="bg-light">

<?php require_once 'sidebar.php'; ?>

<div class="login-wrapper">
    <h1 class="page-title mb-3">🔐 ログイン</h1>

    <?php if ($error): ?>
        <div class="error-box">
            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <div class="login-card">
        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">ログインID</label>
                <input type="text" name="login_id" class="input-cute" required>
            </div>

            <div class="mb-2">
                <label class="form-label">パスワード</label>
                <input type="password" name="password" class="input-cute" required>
            </div>

            <div class="btn-row">
                <a href="index.php" class="btn-cute-outline">← トップへ</a>
                <button type="submit" class="btn-cute">ログインする ✨</button>
            </div>

            <div class="link-small">
                アカウントをお持ちでない方は
                <a href="register.php">新規登録はこちら</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>

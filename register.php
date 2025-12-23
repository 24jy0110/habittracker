<?php
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_id  = trim($_POST['login_id'] ?? '');
    $name      = trim($_POST['name'] ?? '');
    $password  = $_POST['password']  ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($login_id === '' || $name === '' || $password === '' || $password2 === '') {
        $error = 'すべて入力してください。';
    } elseif ($password !== $password2) {
        $error = 'パスワードが一致しません。';
    } else {
        // ログインID重複チェック
        $sql = "SELECT id FROM users WHERE login_id = :login_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':login_id', $login_id, PDO::PARAM_STR);
        $stmt->execute();
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $error = 'このログインIDは既に使用されています。';
        } else {
            // 登録
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (login_id, name, password_hash)
                    VALUES (:login_id, :name, :password_hash)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':login_id', $login_id, PDO::PARAM_STR);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $hash, PDO::PARAM_STR);
            $stmt->execute();

            header('Location: register_success.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー登録</title>
    <link rel="stylesheet" href="global.css">
    <style>
        .page-title {
            font-weight: 800;
            color: #6b62ff;
            text-shadow: 0 2px 6px rgba(150,168,255,0.35);
        }
        .register-wrapper {
            max-width: 720px;
            margin: 40px auto;
        }
        .register-card {
            background: rgba(255,255,255,0.65);
            border-radius: 22px;
            padding: 24px 28px 20px;
            backdrop-filter: blur(18px);
            box-shadow: 0 10px 30px rgba(150,160,220,0.25);
        }
        .register-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            column-gap: 16px;
            row-gap: 14px;
        }
        .full-row {
            grid-column: 1 / -1;
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
            background: rgba(255,210,210,0.7);
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
        .btn-text-link {
            background: transparent;
            border: none;
            color: #666;
            padding: 0;
            font-size: 0.9rem;
            text-decoration: underline;
            cursor: pointer;
        }

        @media (max-width: 640px) {
            .register-grid {
                grid-template-columns: 1fr;
            }
            .full-row {
                grid-column: auto;
            }
        }
    </style>
</head>
<body class="bg-light">

<div class="register-wrapper">
    <h1 class="page-title mb-3">🧸 ユーザー登録</h1>

    <?php if ($error): ?>
        <div class="error-box">
            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <div class="register-card">
        <form action="" method="post">
            <div class="register-grid">
                <!-- 1行目：ログインID & 表示名 -->
                <div>
                    <label class="form-label">ログインID</label>
                    <input type="text" name="login_id" class="input-cute" required>
                </div>
                <div>
                    <label class="form-label">ニックネーム</label>
                    <input type="text" name="name" class="input-cute" required>
                </div>

                <!-- 2行目：パスワード -->
                <div class="full-row">
                    <label class="form-label">パスワード</label>
                    <input type="password" name="password" class="input-cute" required>
                </div>

                <!-- 3行目：パスワード（確認） -->
                <div class="full-row">
                    <label class="form-label">パスワード（確認）</label>
                    <input type="password" name="password2" class="input-cute" required>
                </div>
            </div>

            <div class="btn-row">
                <a href="index.php" class="btn-cute-outline">← トップへ</a>
                <button type="submit" class="btn-cute">登録する ✨</button>
            </div>

            <div style="margin-top:10px;">
                <a href="login.php" class="btn-text-link">すでにアカウントをお持ちの方はこちら</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>

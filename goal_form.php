<?php
require_once 'db.php';
require_once 'require_login.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>目標登録</title>
    <link rel="stylesheet" href="global.css">
    <style>
        .page-title {
            font-weight: 800;
            color: #6b62ff;
            text-shadow: 0 2px 6px rgba(150,168,255,0.35);
        }
        .form-label {
            font-weight: 600;
            color: #4b4ba8;
            display: block;
            margin-bottom: 4px;
        }
        .form-control,
        .form-check-input {
            border-radius: 12px;
            border: 1px solid #d0d4ff;
            padding: 8px 12px;
        }
        .form-check-inline {
            margin-right: 10px;
            font-size: 0.9rem;
            color: #4b5563;
        }
        .weekday-label {
            margin-left: 4px;
        }
    </style>
</head>
<body class="bg-light">

<?php require_once 'sidebar.php'; ?>

<div class="container py-4">
    <h1 class="page-title mb-4">📝 目標登録</h1>

    <div class="glass-card">

        <form action="goal_save.php" method="post">
            <div class="mb-3">
                <label class="form-label">目標タイトル <span style="color:#e11d48;">＊</span></label>
                <input type="text" name="title" class="form-control" required
                       placeholder="例：毎日10分読書する">
            </div>

            <div class="mb-3">
                <label class="form-label">説明（任意）</label>
                <textarea name="description" class="form-control" rows="3"
                          placeholder="目標の補足や理由などがあれば書いてみましょう。"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">開始日</label>
                <input type="date" name="start_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">終了日（任意）</label>
                <input type="date" name="end_date" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">実行する曜日</label>
                <div class="mb-1" style="font-size:0.85rem;color:#6b7280;">
                    ※ 実行したい曜日を選択してください（複数選択可）
                </div>

                <label class="form-check-inline">
                    <input class="form-check-input" type="checkbox" name="days[]" value="Mon">
                    <span class="weekday-label">月</span>
                </label>
                <label class="form-check-inline">
                    <input class="form-check-input" type="checkbox" name="days[]" value="Tue">
                    <span class="weekday-label">火</span>
                </label>
                <label class="form-check-inline">
                    <input class="form-check-input" type="checkbox" name="days[]" value="Wed">
                    <span class="weekday-label">水</span>
                </label>
                <label class="form-check-inline">
                    <input class="form-check-input" type="checkbox" name="days[]" value="Thu">
                    <span class="weekday-label">木</span>
                </label>
                <label class="form-check-inline">
                    <input class="form-check-input" type="checkbox" name="days[]" value="Fri">
                    <span class="weekday-label">金</span>
                </label>
                <label class="form-check-inline">
                    <input class="form-check-input" type="checkbox" name="days[]" value="Sat">
                    <span class="weekday-label">土</span>
                </label>
                <label class="form-check-inline">
                    <input class="form-check-input" type="checkbox" name="days[]" value="Sun">
                    <span class="weekday-label">日</span>
                </label>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn-cute">
                    ✨ この内容で登録する
                </button>
                <a href="index.php" class="btn-cute-outline">
                    メニューへ戻る
                </a>
            </div>
        </form>

    </div>
</div>

</body>
</html>

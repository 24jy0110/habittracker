<?php
// ログインしていない場合はログイン画面へ飛ばす
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

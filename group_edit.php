<?php
require_once 'db.php';
require_once 'require_login.php';

$group_id = (int)($_GET['group_id'] ?? 0);

// 检查是否管理员
$sql = "SELECT * FROM `groups` WHERE id = :gid AND owner_id = :uid";
$stmt = $pdo->prepare($sql);
$stmt->execute([':gid'=>$group_id, ':uid'=>$current_user_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    echo "権限がありません。"; exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $desc = $_POST['description'];

    $sql = "UPDATE groups SET description = :desc WHERE id = :gid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':desc'=>$desc, ':gid'=>$group_id]);

    header("Location: group_detail.php?group_id=$group_id");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>グループ説明編集</title>
<link rel="stylesheet" href="global.css">
</head>
<body class="bg-light">
<?php require_once 'sidebar.php'; ?>
<div class="container py-4">
<h2>グループ説明を編集</h2>

<form method="post">
    <div class="mb-3">
        <label class="form-label">説明</label>
        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($group['description']); ?></textarea>
    </div>
    <button class="btn btn-primary">保存する</button>
    <a href="group_detail.php?group_id=<?php echo $group_id; ?>" class="btn btn-secondary">戻る</a>
</form>

</div>
</body>
</html>

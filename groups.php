<?php
require_once 'db.php';
require_once 'require_login.php';

$sql = "
    SELECT
        g.id,
        g.name,
        g.description,
        g.owner_id,


        (
            SELECT COUNT(*)
            FROM group_members gm
            WHERE gm.group_id = g.id
        ) AS member_count,

        EXISTS (
            SELECT 1
            FROM group_members gm2
            WHERE gm2.group_id = g.id
              AND gm2.user_id = :uid
        ) AS is_member

    FROM `groups` g
    ORDER BY g.id DESC
";
;
$stmt = $pdo->prepare($sql);
$stmt->execute([':uid' => $current_user_id]);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 分类
$my_groups = [];
$joined_groups = [];
$other_groups = [];

foreach ($groups as $g) {
    if ($g['owner_id'] == $current_user_id) {
        $my_groups[] = $g;
    } elseif ($g['is_member']) {
        $joined_groups[] = $g;
    } else {
        $other_groups[] = $g;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>グループ一覧</title>
    <link rel="stylesheet" href="global.css">
    <style>
        .page-title {
            font-weight: 800;
            color: #6b62ff;
            text-shadow: 0 2px 6px rgba(150, 168, 255, 0.35);
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #4b4ba8;
        }

        .group-desc {
            font-size: 0.9rem;
            color: #6b7280;
        }

        /* カード全体リンク用 */
        .card-link-wrapper {
            display: block;
            text-decoration: none;
            color: inherit;
        }

        .card-link-wrapper:hover .glass-card {
            transform: translateY(-6px);
            box-shadow: 0 12px 36px rgba(140, 160, 220, 0.28);
            cursor: pointer;
        }

        .btn-join {
            border-radius: 12px;
            padding: 6px 14px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid #81c7ff;
            color: #5577aa;
            transition: 0.2s;
        }

        .btn-join:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-1px);
        }
    </style>
</head>

<body class="bg-light">
    <?php require_once 'sidebar.php'; ?>

    <div class="container py-4">

        <h1 class="page-title mb-3">👥 グループ一覧</h1>

        <div class="mb-4">
            <a class="btn-cute me-2" href="group_create.php">＋ 新しいグループを作成</a>
            <a href="index.php" class="btn-cute-outline">メニューへ戻る</a>
        </div>

        <!-- ① 私が作成したグループ -->
        <div class="mb-3">
            <div class="section-title">📌 私が作成したグループ</div>
        </div>
        <?php if (empty($my_groups)): ?>
            <div class="glass-card mb-3">
                <span class="text-muted">まだ作成したグループはありません。</span>
            </div>
        <?php else: ?>
            <?php foreach ($my_groups as $g): ?>
                <!-- カード全体をリンクにする -->
                <a href="group_detail.php?group_id=<?php echo (int) $g['id']; ?>" class="card-link-wrapper">
                    <div class="glass-card mb-3">
                        <h5 class="mb-1">
                            <?php echo htmlspecialchars($g['name'], ENT_QUOTES, 'UTF-8'); ?>
                            <span class="badge-cute ms-2">管理者</span>
                        </h5>
                        <p class="group-desc mb-0">
                            <?php echo nl2br(htmlspecialchars($g['description'], ENT_QUOTES, 'UTF-8')); ?>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>


        <!-- ② 参加中のグループ -->
        <div class="mt-4 mb-3">
            <div class="section-title">🙋‍♂️ 参加中のグループ</div>
        </div>
        <?php if (empty($joined_groups)): ?>
            <div class="glass-card mb-3">
                <span class="text-muted">参加しているグループはありません。</span>
            </div>
        <?php else: ?>
            <?php foreach ($joined_groups as $g): ?>
                <!-- カード全体リンク -->
                <a href="group_detail.php?group_id=<?php echo (int) $g['id']; ?>" class="card-link-wrapper">
                    <div class="glass-card mb-3">
                        <h5 class="mb-1">
                            <?php echo htmlspecialchars($g['name'], ENT_QUOTES, 'UTF-8'); ?>
                            <span class="badge-cute ms-2">参加中</span>
                        </h5>
                        <p class="group-desc mb-0">
                            <?php echo nl2br(htmlspecialchars($g['description'], ENT_QUOTES, 'UTF-8')); ?>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>


        <!-- ③ その他のグループ -->
        <div class="mt-4 mb-3">
            <div class="section-title">🌐 その他のグループ</div>
        </div>
        <?php if (empty($other_groups)): ?>
            <div class="glass-card mb-3">
                <span class="text-muted">表示できるグループはありません。</span>
            </div>
        <?php else: ?>
            <?php foreach ($other_groups as $g): ?>
                <div class="glass-card mb-3 d-flex justify-content-between align-items-start">
                    <!-- 左側：名前＋説明部分だけ大きなクリック領域 -->
                    <a href="group_detail.php?group_id=<?php echo (int) $g['id']; ?>" class="card-link-wrapper"
                        style="flex:1; margin-right: 12px;">
                        <h5 class="mb-1">
                            <?php echo htmlspecialchars($g['name'], ENT_QUOTES, 'UTF-8'); ?>
                        </h5>
                        <p class="group-desc mb-0">
                            <?php echo nl2br(htmlspecialchars($g['description'], ENT_QUOTES, 'UTF-8')); ?>
                        </p>
                    </a>

                    <!-- 右側：参加ボタン（フォーム） -->
                    <div>
                        <form action="group_join.php" method="post">
                            <input type="hidden" name="group_id" value="<?php echo (int) $g['id']; ?>">
                            <button class="btn-join">このグループに参加する</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</body>

</html>
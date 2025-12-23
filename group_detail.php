<?php
// group_detail.php
require_once 'db.php';
require_once 'require_login.php';

$group_id = (int)($_GET['group_id'] ?? 0);
if ($group_id <= 0) {
    header('Location: groups.php');
    exit;
}

// --- 1) グループ情報 ---
$sql = "SELECT id, name, description, owner_id FROM `groups` WHERE id = :gid";
$stmt = $pdo->prepare($sql);
$stmt->execute([':gid' => $group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    echo "グループが見つかりません。";
    exit;
}

// --- 2) メンバー判定 ---
$sql = "SELECT id FROM group_members WHERE group_id = :gid AND user_id = :uid";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':gid' => $group_id,
    ':uid' => $current_user_id
]);
$is_member = (bool)$stmt->fetch(PDO::FETCH_ASSOC);

$is_owner = ($group['owner_id'] == $current_user_id);

// ============ 共通関数 ============

// 連続日数
function calc_streak(PDO $pdo, int $user_id, int $goal_id): int {
    if ($goal_id <= 0) return 0;

    $today = date('Y-m-d');

    $sql = "SELECT date FROM progress
            WHERE user_id = :uid AND goal_id = :gid AND date <= :today
            ORDER BY date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':uid' => $user_id,
        ':gid' => $goal_id,
        ':today' => $today
    ]);
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($rows) || $rows[0] !== $today) return 0;

    $streak = 1;
    $expected = new DateTime($today);
    for ($i = 1; $i < count($rows); $i++) {
        $expected->modify('-1 day');
        if ($rows[$i] === $expected->format('Y-m-d')) {
            $streak++;
        } else break;
    }
    return $streak;
}

// 今日達成？
function is_done_today(PDO $pdo, int $user_id, int $goal_id, string $today): bool {
    if ($goal_id <= 0) return false;

    $sql = "SELECT id FROM progress
            WHERE user_id = :uid AND goal_id = :gid AND date = :today";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':uid' => $user_id,
        ':gid' => $goal_id,
        ':today' => $today
    ]);

    return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
}

// ============ メンバー一覧取得 ============
$members = [];
$today = date('Y-m-d');

if ($is_member) {
    $sql = "
        SELECT
            gm.user_id,
            u.name AS user_name,
            g.id AS goal_id
        FROM group_members gm
        JOIN users u ON gm.user_id = u.id
        LEFT JOIN goals g
            ON g.user_id = gm.user_id
           AND g.group_id = :gid
        WHERE gm.group_id = :gid
        ORDER BY u.name
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':gid' => $group_id]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
<meta charset="UTF-8">
<title>グループ詳細</title>
<link rel="stylesheet" href="global.css">

<style>
    .page-title {
        font-weight: 800;
        color: #6b62ff;
        text-shadow: 0 2px 6px rgba(150,168,255,0.35);
    }
    .glass-title-card {
        padding: 24px;
        border-radius: 22px;
        margin-bottom: 20px;
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(20px);
        box-shadow: 0 10px 28px rgba(150,160,220,0.25);
    }
    .badge-owner {
        background: linear-gradient(135deg,#b7a8ff,#81c7ff);
        padding: 6px 12px;
        color: #fff;
        border-radius: 12px;
        font-weight: 700;
    }
    .section-title {
        font-weight: 700;
        color: #4b4ba8;
        margin-bottom: 10px;
    }
    .glass-info, .glass-card {
        background: rgba(255,255,255,0.55);
        border-radius: 18px;
        padding: 18px;
        backdrop-filter: blur(15px);
        box-shadow: 0 8px 20px rgba(150,160,220,0.25);
        margin-bottom: 20px;
    }
    .btn-delete {
        background: rgba(255,240,245,0.8);
        border: 1px solid #ff8fa3;
        padding: 8px 14px;
        border-radius: 12px;
        color: #c93160;
        font-weight: 600;
    }
    .btn-delete:hover {
        background: #ffe2ea;
    }
</style>
</head>

<body class="bg-light">

<?php require_once 'sidebar.php'; ?>

<div class="container py-4">

    <!-- タイトルカード -->
    <div class="glass-title-card">
        <h1 class="page-title">
            👥 グループ：<?= htmlspecialchars($group['name']) ?>
            <?php if ($is_owner): ?>
                <span class="badge-owner">管理者</span>
            <?php endif; ?>
        </h1>
    </div>

    <!-- 説明 -->
    <div class="glass-card">
        <h4 class="section-title">📘 グループ説明</h4>
        <p><?= nl2br(htmlspecialchars($group['description'] ?: "（説明なし）")) ?></p>
    </div>

    <!-- ボタン -->
    <div class="mb-3 d-flex flex-wrap gap-2">
        <a href="groups.php" class="btn-cute-outline">← グループ一覧へ</a>

        <?php if ($is_member): ?>
            <a href="group_ranking.php?group_id=<?= $group_id ?>" class="btn-cute">🌟 今週のランキング</a>
        <?php endif; ?>

        <?php if ($is_owner): ?>
            <a href="group_edit.php?group_id=<?= $group_id ?>" class="btn-cute">✏️ 説明を編集</a>

            <!-- 解散 -->
            <form action="group_delete.php" method="post" class="d-inline"
                  onsubmit="return confirm('本当に解散しますか？全メンバーのグループ目標も削除されます。');">
                <input type="hidden" name="group_id" value="<?= $group_id ?>">
                <button class="btn-delete">🗑 グループを解散</button>
            </form>

        <?php elseif ($is_member): ?>

            <!-- 退室（非管理者のみ） -->
            <form action="group_leave.php" method="post" class="d-inline"
                  onsubmit="return confirm('本当に退出しますか？グループ目標も削除されます。');">
                <input type="hidden" name="group_id" value="<?= $group_id ?>">
                <button class="btn-delete">🚪 グループを退出</button>
            </form>

        <?php endif; ?>
    </div>

    <?php if (!$is_member): ?>

        <div class="glass-info">
            このグループのメンバーではないため、達成状況は表示されません。
        </div>

        <form action="group_join.php" method="post">
            <input type="hidden" name="group_id" value="<?= $group_id ?>">
            <button class="btn-cute">🙋‍♀️ このグループに参加する</button>
        </form>

    <?php else: ?>

        <!-- メンバー状況 -->
        <div class="glass-card">
            <h4 class="section-title">🔥 メンバーの本日の状況（<?= $today ?>）</h4>
        </div>

        <?php if (empty($members)): ?>

            <div class="glass-info">まだメンバーがいません。</div>

        <?php else: ?>

            <table class="table table-striped glass-card align-middle">
                <thead>
                    <tr>
                        <th>メンバー</th>
                        <th>今日の達成</th>
                        <th>連続達成日数</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $m): ?>
                        <?php
                        $gid = (int)$m['goal_id'];
                        $done = is_done_today($pdo, $m['user_id'], $gid, $today);
                        $streak = calc_streak($pdo, $m['user_id'], $gid);
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($m['user_name']) ?></td>
                            <td>
                                <?php if ($done): ?>
                                    <span class="badge bg-success">✔ 達成</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">未達成</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $streak ?> 日</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>

    <?php endif; ?>

</div>
</body>
</html>

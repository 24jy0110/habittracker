<?php
require_once 'db.php';
require_once 'require_login.php';

$days  = 14;
$today = new DateTime();
$dates = [];

for ($i = $days - 1; $i >= 0; $i--) {
    $d = clone $today;
    $d->modify("-{$i} day");
    $dates[] = $d->format('Y-m-d');
}

$sql = "SELECT date, COUNT(*) AS cnt
        FROM progress
        WHERE user_id = :uid
          AND date BETWEEN :from AND :to
        GROUP BY date";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':uid'  => $current_user_id,
    ':from' => $dates[0],
    ':to'   => $dates[count($dates)-1]
]);
$rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$labels = [];
$data   = [];
foreach ($dates as $d) {
    $labels[] = (new DateTime($d))->format('m/d');
    $data[]   = isset($rows[$d]) ? (int)$rows[$d] : 0;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>達成状況の統計</title>
    <link rel="stylesheet" href="global.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        .page-title {
            font-weight: 800;
            color: #6b62ff;
            text-shadow: 0 2px 6px rgba(120,150,255,0.35);
        }
        .subtitle {
            color: #6b7280;
            font-size: 0.9rem;
        }

        /* 图表容器 */
        .chart-box {
            padding: 20px;
            border-radius: 18px;
            background: rgba(255,255,255,0.55);
            backdrop-filter: blur(18px);
            box-shadow: 0 6px 20px rgba(150,160,220,0.25);
        }
    </style>
</head>

<body class="bg-light">
<?php require_once 'sidebar.php'; ?>

<div class="container py-4">

    <h1 class="page-title mb-4">📈 達成状況の統計</h1>

    <!-- 登录信息 -->
    <div class="glass-card p-3 mb-3">
        <strong><?php echo htmlspecialchars($current_user_name); ?></strong> さん、  
        最近のがんばりをグラフで見てみましょう！✨
        <p class="subtitle mt-2">（直近 14 日間の達成回数）</p>
    </div>

    <!-- 导航按钮 -->
    <div class="mb-4 d-flex flex-wrap gap-2">
        <a href="index.php" class="btn-cute-outline">メニューへ戻る</a>
        <a href="goal_list.php" class="btn-cute-outline">目標一覧</a>
        <a href="checklist.php" class="btn-cute">今日のチェックリスト</a>
    </div>

    <!-- 图表 -->
    <div class="chart-box">
        <h4 class="mb-3">📊 直近14日間の達成回数</h4>
        <canvas id="progressChart" height="120"></canvas>
    </div>

</div>

<script>
    const ctx = document.getElementById('progressChart');

    const labels = <?php echo json_encode($labels, JSON_UNESCAPED_UNICODE); ?>;
    const data   = <?php echo json_encode($data, JSON_UNESCAPED_UNICODE); ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: '達成回数',
                data: data,
                backgroundColor: 'rgba(130,160,255,0.55)',
                borderColor: 'rgba(120,140,255,0.85)',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
</script>

</body>
</html>

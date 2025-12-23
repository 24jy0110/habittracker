<?php
// sidebar.php
require_once 'db.php';

$logged_in = ($current_user_id !== null);

// 随机加油提示语（ゆめかわ系）
$messages = [
    "✨ 今日もコツコツ、習慣化パワーアップ！",
    "💗 少しだけでもOK！続けるあなたはえらい！",
    "🌈 小さな一歩が、未来の自分を変えていくよ。",
    "⭐ 今日のチェックも忘れずに〜！",
    "🩵 無理せずマイペースに、一緒にがんばろう。"
];
$motivation = $messages[array_rand($messages)];
?>

<!-- 如果页面没有加载 Bootstrap，也可以在这里兜底一次（重复加载也没关系） -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* 顶部玻璃导航条（粉蓝渐变） */
.app-navbar {
    background: linear-gradient(120deg, rgba(183,168,255,0.75), rgba(129,199,255,0.75));
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.6);
    box-shadow: 0 6px 24px rgba(145, 158, 255, 0.22);
}

/* 左侧 offcanvas 毛玻璃 + 粉蓝系 */
.offcanvas-cute {
    background: radial-gradient(circle at top left, rgba(255,222,255,0.6), rgba(224,241,255,0.7));
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    border-right: 1px solid rgba(255,255,255,0.65);
    box-shadow: 8px 0 28px rgba(150, 160, 210, 0.35);
}

/* 加油提示语玻璃块（粉蓝渐变 + 光泽） */
.motivation-box {
    position: relative;
    padding: 16px 18px;
    border-radius: 18px;
    background: linear-gradient(135deg, #ffe9f7, #e6f1ff, #fef6ff);
    background-size: 200% 200%;
    animation: motiBg 7s ease-in-out infinite;
    box-shadow:
        0 6px 20px rgba(180, 170, 230, 0.4),
        inset 0 0 18px rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.9);
}

.motivation-text {
    font-weight: 700;
    font-size: 0.95rem;
    color: #6b5bff;
}

/* 闪闪的小星星 */
.motivation-sparkle {
    position: absolute;
    top: 6px;
    right: 12px;
    font-size: 1.2rem;
    opacity: 0.8;
    animation: sparkleTwinkle 1.8s ease-in-out infinite alternate;
}

/* 用户信息小头像圆圈 */
.user-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
}

.user-avatar {
    width: 28px;
    height: 28px;
    border-radius: 999px;
    background: linear-gradient(135deg, #b7a8ff, #81c7ff);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 0.9rem;
}

/* 菜单项可爱玻璃风 */
.nav-link.cute-link {
    color: #455a8f;
    padding-left: 6px;
    border-radius: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-link.cute-link span.icon {
    font-size: 1.1rem;
}

.nav-link.cute-link:hover {
    background: rgba(255,255,255,0.8);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
}

/* 毛玻璃按钮（登出用） */
.btn-glass-cute {
    background: rgba(255,255,255,0.85);
    border-radius: 14px;
    border: 1px solid rgba(183,168,255,0.9);
    color: #5b5b8f;
    font-weight: 600;
    padding: 10px 16px;
    transition: 0.25s ease;
}

.btn-glass-cute:hover {
    background: rgba(255,255,255,1);
    transform: translateY(-1px);
}

/* 渐变按钮（登录） */
.btn-gradient-cute {
    background: linear-gradient(130deg, #b7a8ff, #81c7ff);
    border-radius: 14px;
    border: none;
    color: #fff;
    font-weight: 700;
    padding: 10px 16px;
    transition: 0.25s ease;
}

.btn-gradient-cute:hover {
    opacity: 0.95;
    transform: translateY(-1px);
}

/* 动画 */
@keyframes motiBg {
    0%   { background-position: 0% 50%; }
    50%  { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes sparkleTwinkle {
    0%   { transform: scale(1); opacity: 0.4; }
    100% { transform: scale(1.35); opacity: 1; }
}
</style>

<!-- 顶部粉蓝玻璃导航 -->
<nav class="navbar app-navbar px-3 mb-3">
    <button class="btn btn-light border-0" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
        ☰ メニュー
    </button>
    <span class="navbar-brand ms-3 text-white fw-semibold">習慣化WEB</span>
</nav>

<!-- 粉蓝可爱玻璃 Offcanvas -->
<div class="offcanvas offcanvas-start offcanvas-cute" id="sidebarMenu" tabindex="-1">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">メニュー</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">

        <!-- ✨ 可爱加油提示语 -->
        <div class="motivation-box mb-4">
            <span class="motivation-text">
                <?php echo htmlspecialchars($motivation, ENT_QUOTES, 'UTF-8'); ?>
            </span>
            <span class="motivation-sparkle">✦</span>
        </div>

        <!-- 登录状态区域 -->
        <?php if ($logged_in): ?>
            <?php
                // 头像上的首字母（名字第一个字符）
                $initial = mb_substr($current_user_name ?? 'U', 0, 1, 'UTF-8');
            ?>
            <div class="mb-3">
                <div class="user-pill">
                    <div class="user-avatar"><?php echo htmlspecialchars($initial, ENT_QUOTES, 'UTF-8'); ?></div>
                    <div>
                        <div style="font-size: 0.8rem; color: #6b7280;">ログイン中</div>
                        <div style="font-weight: 700;">
                            <?php echo htmlspecialchars($current_user_name, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="mb-3" style="font-size: 0.9rem;">
                ✨ まだログインしていません。<br>
                ログインして、一緒に習慣化を続けてみましょう！
            </div>
        <?php endif; ?>

        <hr>

        <!-- 菜单 -->
        <ul class="nav flex-column mb-3">
            <li class="nav-item">
                <a href="index.php" class="nav-link cute-link">
                    <span class="icon">🏠</span> <span>ホーム</span>
                </a>
            </li>

            <?php if ($logged_in): ?>
                <li class="nav-item">
                    <a href="goal_form.php" class="nav-link cute-link">
                        <span class="icon">📝</span> <span>目標登録</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="goal_list.php" class="nav-link cute-link">
                        <span class="icon">🎯</span> <span>目標一覧</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="checklist.php" class="nav-link cute-link">
                        <span class="icon">✔️</span> <span>今日のチェック</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="stats.php" class="nav-link cute-link">
                        <span class="icon">📈</span> <span>達成グラフ</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="groups.php" class="nav-link cute-link">
                        <span class="icon">👥</span> <span>グループ一覧</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <hr>

        <!-- 登录 / 登出按钮 -->
        <?php if ($logged_in): ?>
            <a href="logout.php" class="btn btn-glass-cute w-100">🚪 ログアウト</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-gradient-cute w-100 mb-2">ログイン</a>
            <a href="register.php" class="btn btn-glass-cute w-100">新規登録</a>
        <?php endif; ?>

    </div>
</div>

<?php
session_start();
if (empty($_SESSION['owner'])) {
    header('Location: login.php');
    exit;
}
require '../includes/db.php';

$username = $_SESSION['owner'];

$stmt = $pdo->prepare("SELECT id, name, age, city, compatibility_score, scheduled_date, submitted_at
                        FROM responses WHERE owner_username = ? ORDER BY submitted_at DESC");
$stmt->execute([$username]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>my dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        (function() {
            const t = localStorage.getItem('ownerTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
</head>
<body class="admin-dashboard">

    <div class="owner-topbar">
        <span class="owner-topbar-name">@<?= htmlspecialchars($username) ?></span>
        <div class="owner-topbar-right">
            <button class="topbar-btn" id="themeBtn" onclick="toggleTheme()">☀️ light</button>
            <a href="logout.php" class="topbar-btn topbar-logout">logout</a>
        </div>
    </div>

    <h1>hi, <?= htmlspecialchars($username) ?> 🌸</h1>

    <div style="display:flex; gap:1.2rem; margin-bottom:1.8rem; flex-wrap:wrap; font-size:0.85rem;">
        <a href="change_password.php" style="color:var(--pink); text-decoration:none;">🔑 change password</a>
        <span style="color:var(--border);">·</span>
        <a href="compatibility_answers.php" style="color:var(--pink); text-decoration:none;">💘 my compatibility answers</a>
        <span style="color:var(--border);">·</span>
        <a href="edit_profile.php" style="color:var(--pink); text-decoration:none;">✏️ edit my profile</a>
    </div>

    <p style="color:var(--muted); margin-bottom:1rem; font-size:0.9rem;">
        <?= count($rows) ?> response<?= count($rows) !== 1 ? 's' : '' ?> so far
    </p>

    <?php if (empty($rows)): ?>
        <p style="color:var(--muted); font-style:italic; margin-top:2rem;">
            no responses yet. share your link with your crush! 🌸
        </p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th>City</th>
                <th>Compatibility</th>
                <th>Scheduled Date</th>
                <th>Submitted</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
            <tr onclick="window.location='view.php?id=<?= $r['id'] ?>'">
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= $r['age'] ?></td>
                <td><?= htmlspecialchars($r['city']) ?></td>
                <td><?= $r['compatibility_score'] ? $r['compatibility_score'] . '%' : '—' ?></td>
                <td><?= $r['scheduled_date'] ?: '—' ?></td>
                <td><?= $r['submitted_at'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p class="table-hint">tap a row to view full response</p>
    <?php endif; ?>

    <script>
        const btn = document.getElementById('themeBtn');
        const saved = localStorage.getItem('ownerTheme') || 'dark';
        btn.textContent = saved === 'light' ? '🌙 dark' : '☀️ light';

        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('ownerTheme', next);
            btn.textContent = next === 'light' ? '🌙 dark' : '☀️ light';
        }
    </script>
</body>
</html>
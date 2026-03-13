<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require '../includes/db.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM responses WHERE id = ?");
$stmt->execute([$id]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$r) die("Not found.");

$sections = [
    'about them' => [
        'age'           => 'Age',
        'city'          => 'City',
        'communication' => 'Reach them via',
        'best_time'     => 'Best time',
        'food_drink'    => 'Food & drink',
        'flower'        => 'Flower',
        'craving'       => 'Craving lately',
        'temperature'   => 'Temperature pref',
        'dislikes'      => 'Dislikes going out',
        'dessert'       => 'Dessert',
        'dealbreaker'   => 'Dealbreaker',
    ],
    'date preferences' => [
        'date_type'        => 'Date type',
        'spontaneity'      => 'Spontaneity',
        'energy'           => 'Energy',
        'mood'             => 'Mood',
        'crowd'            => 'Crowd',
        'convo_style'      => 'Convo style',
        'walking'          => 'Walking',
        'awkwardness'      => 'Awkwardness',
        'convo_difficulty' => 'Difficulty',
    ],
    'vibes & extras' => [
        'vibes'       => 'Vibes',
        'custom_vibe' => 'Their idea',
    ],
    'results' => [
        'compatibility_score' => 'Compatibility',
        'scheduled_date'      => 'Scheduled date',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>response #<?= $id ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
    <script>
        (function() {
            const t = localStorage.getItem('adminTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
</head>
<body class="admin-view">

    <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn">☀️ light</button>

    <div class="view-wrapper">

        <a href="dashboard.php" class="view-back">← back to dashboard</a>

        <div class="view-header">
            <div class="view-name"><?= htmlspecialchars($r['name']) ?></div>
            <div class="view-id">#<?= $id ?></div>
        </div>
        <div class="view-submitted">submitted <?= htmlspecialchars($r['submitted_at']) ?></div>

        <?php foreach ($sections as $sectionName => $fields): ?>
        <div class="view-section">
            <div class="view-section-label"><?= $sectionName ?></div>

            <?php foreach ($fields as $key => $label):
                $raw     = $r[$key] ?? '';
                $isEmpty = ($raw === '' || $raw === null);
                $val     = $isEmpty ? '—' : htmlspecialchars($raw);
                if ($key === 'compatibility_score' && !$isEmpty) $val = $raw . '%';

                $extraClass = '';
                if ($key === 'compatibility_score') $extraClass = 'score-field';
                if ($key === 'scheduled_date' && !$isEmpty) $extraClass = 'date-field';
            ?>
            <div class="field <?= $extraClass ?>">
                <strong><?= $label ?></strong>
                <span class="<?= $isEmpty ? 'empty' : '' ?>"><?= $val ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

    </div>

    <script>
        const btn = document.getElementById('themeBtn');
        const saved = localStorage.getItem('adminTheme') || 'dark';
        applyTheme(saved);

        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'light' ? 'dark' : 'light';
            applyTheme(next);
            localStorage.setItem('adminTheme', next);
        }

        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            btn.textContent = theme === 'light' ? '🌙 dark' : '☀️ light';
        }
    </script>
</body>
</html>
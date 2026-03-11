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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>response #<?= $id ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body { padding: 2rem; background: #0f0f0f; color: #eee; max-width: 700px; margin: 0 auto; }
        h1 { font-family: 'Playfair Display', serif; color: #f4a7b9; }
        .field { margin-bottom: 1rem; padding: 0.75rem; background: #1a1a1a; border-radius: 8px; }
        .field strong { color: #f4a7b9; display: block; margin-bottom: 0.25rem; font-size: 0.85rem; }
        a { color: #f4a7b9; }
    </style>
</head>
<body>
    <a href="dashboard.php">← back</a>
    <h1><?= htmlspecialchars($r['name']) ?> <span style="font-size:0.6em;color:#aaa;">#<?= $id ?></span></h1>

    <?php
    $labels = [
        'age' => 'Age', 'city' => 'City', 'communication' => 'Communication',
        'best_time' => 'Best time', 'food_drink' => 'Food & Drink', 'dealbreaker' => 'Dealbreaker',
        'date_type' => 'Date type', 'spontaneity' => 'Spontaneity', 'energy' => 'Energy',
        'mood' => 'Mood', 'crowd' => 'Crowd', 'convo_style' => 'Convo style',
        'vibes' => 'Vibes', 'custom_vibe' => 'Custom vibe', 'walking' => 'Walking',
        'awkwardness' => 'Awkwardness', 'convo_difficulty' => 'Convo difficulty',
        'compatibility_score' => 'Compatibility score', 'scheduled_date' => 'Scheduled date',
        'submitted_at' => 'Submitted'
    ];

    foreach ($labels as $key => $label) {
        $val = htmlspecialchars($r[$key] ?? '—');
        if ($key === 'compatibility_score' && $val !== '—') $val .= '%';
        echo "<div class='field'><strong>$label</strong>$val</div>";
    }
    ?>
</body>
</html>
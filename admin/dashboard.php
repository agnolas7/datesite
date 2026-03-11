<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require '../includes/db.php';

$stmt = $pdo->query("SELECT id, name, age, city, compatibility_score, scheduled_date, submitted_at FROM responses ORDER BY submitted_at DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>admin dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body { padding: 2rem; background: #0f0f0f; color: #eee; }
        h1 { font-family: 'Playfair Display', serif; margin-bottom: 1.5rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #333; }
        th { background: #1a1a1a; color: #f4a7b9; }
        tr:hover { background: #1e1e1e; cursor: pointer; }
        .logout { float: right; color: #aaa; text-decoration: none; }
        .logout:hover { color: #f4a7b9; }
    </style>
</head>
<body>
    <a href="logout.php" class="logout">logout</a>
    <h1>responses dashboard 📋</h1>
    <p><?= count($rows) ?> people answered</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
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
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= $r['age'] ?></td>
                <td><?= htmlspecialchars($r['city']) ?></td>
                <td><?= $r['compatibility_score'] ?>%</td>
                <td><?= $r['scheduled_date'] ?: '—' ?></td>
                <td><?= $r['submitted_at'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
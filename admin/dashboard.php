<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require '../includes/db.php';

$stmt = $pdo->query("SELECT id, name, age, city, compatibility_score, scheduled_date, submitted_at FROM responses ORDER BY submitted_at DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ownerCount = $pdo->query("SELECT COUNT(*) FROM site_owners")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>admin dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin-dashboard">

    <a href="logout.php" class="logout">logout</a>
    <h1>responses dashboard 📋</h1>

    <!-- Quick links -->
    <div class="admin-quick-links">
        <a href="compatibility_answers.php" class="quick-link">
            ✦ my compatibility answers
        </a>
        <a href="create_owner.php" class="quick-link quick-link-highlight">
            + create buyer account
            <span class="owner-count"><?= $ownerCount ?> account<?= $ownerCount != 1 ? 's' : '' ?></span>
        </a>
    </div>

    <p style="color:var(--muted); font-size:0.85rem; margin-bottom:1.2rem;">
        <?= count($rows) ?> response<?= count($rows) != 1 ? 's' : '' ?> total
    </p>

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
                <td><?= $r['compatibility_score'] ? $r['compatibility_score'] . '%' : '—' ?></td>
                <td><?= $r['scheduled_date'] ?: '—' ?></td>
                <td><?= $r['submitted_at'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require '../includes/db.php';

$stmt = $pdo->query("SELECT id, name, age, city, compatibility_score, scheduled_date, submitted_at, owner_username
                     FROM responses ORDER BY submitted_at DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ownerCount = $pdo->query("SELECT COUNT(*) FROM site_owners")->fetchColumn();

// Fetch all maybe reasons
$maybeStmt = $pdo->query("
    SELECT mr.reason, mr.submitted_at, mr.owner_username, r.name
    FROM maybe_reasons mr
    LEFT JOIN responses r ON mr.response_id = r.id
    ORDER BY mr.submitted_at DESC
");
$maybeRows = $maybeStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>admin dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
    <script>
        (function() {
            const t = localStorage.getItem('adminTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
</head>
<body class="admin-dashboard">

    <a href="logout.php" class="logout">logout</a>
    <h1>responses dashboard 📋</h1>

    <!-- Quick links -->
    <div class="admin-quick-links">
        <a href="compatibility_answers.php" class="quick-link">
            ✦ my compatibility answers
        </a>
        <a href="view_feedback.php" class="quick-link">
            🐛 view feedback & bugs
        </a>
        <a href="create_owner.php" class="quick-link quick-link-highlight">
            + create buyer account
            <span class="owner-count"><?= $ownerCount ?> account<?= $ownerCount != 1 ? 's' : '' ?></span>
        </a>
    </div>

    <p style="color:var(--muted); font-size:0.85rem; margin-bottom:1.2rem;">
        <?= count($rows) ?> response<?= count($rows) != 1 ? 's' : '' ?> total
    </p>

    <!-- ── Responses table ── -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Age</th>
                <th>City</th>
                <th>Owner</th>
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
                <td style="color:var(--pink); font-size:0.82rem;">
                    <?= htmlspecialchars($r['owner_username'] ?? '—') ?>
                </td>
                <td><?= $r['compatibility_score'] ? $r['compatibility_score'] . '%' : '—' ?></td>
                <td><?= $r['scheduled_date'] ?: '—' ?></td>
                <td><?= $r['submitted_at'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- ── Maybe reasons ── -->
    <?php if (!empty($maybeRows)): ?>
    <div style="margin-top:3rem;">
        <h2 style="font-family:'Playfair Display',serif; font-size:1.1rem;
                   margin-bottom:0.4rem; color:var(--text);">
            why she said maybe 🤔
        </h2>
        <p style="color:var(--muted); font-size:0.8rem; margin-bottom:1.2rem;">
            <?= count($maybeRows) ?> reason<?= count($maybeRows) !== 1 ? 's' : '' ?> recorded across all accounts
        </p>
        <table>
            <thead>
                <tr>
                    <th>Reason</th>
                    <th>Owner</th>
                    <th>Name</th>
                    <th>When</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($maybeRows as $mr): ?>
                <tr>
                    <td><?= htmlspecialchars($mr['reason']) ?></td>
                    <td style="color:var(--pink); font-size:0.82rem;">
                        <?= htmlspecialchars($mr['owner_username'] ?? '—') ?>
                    </td>
                    <td>
                        <?php if ($mr['name']): ?>
                            <?= htmlspecialchars($mr['name']) ?>
                        <?php else: ?>
                            <span style="color:var(--muted); font-style:italic; font-size:0.82rem;">
                                never filled form
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:0.8rem; color:var(--muted);"><?= $mr['submitted_at'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <script>
        (function() {
            const t = localStorage.getItem('adminTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
</body>
</html>
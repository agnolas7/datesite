<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require '../includes/db.php';

$stmt = $pdo->query("SELECT id, name, message, type, submitted_at FROM feedback ORDER BY submitted_at DESC");
$feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>feedback & bugs</title>
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

    <a href="dashboard.php" style="color:var(--muted); text-decoration:none; font-size:0.85rem;">← back to dashboard</a>
    <a href="logout.php" class="logout">logout</a>
    
    <h1>feedback & bug reports 🐛</h1>

    <?php if (empty($feedback)): ?>
    <p style="color:var(--muted); font-style:italic; margin-top:2rem;">
        no feedback yet. people are probably too shy to send any 😅
    </p>
    <?php else: ?>
    <p style="color:var(--muted); font-size:0.85rem; margin-bottom:1.5rem;">
        <?= count($feedback) ?> submission<?= count($feedback) != 1 ? 's' : '' ?> total
    </p>

    <div style="display: grid; gap: 1rem;">
        <?php foreach ($feedback as $fb): ?>
        <div style="background:var(--card); border:1px solid var(--border); border-radius:12px; padding:1.2rem;">
            <div style="display:flex; gap:0.8rem; align-items:flex-start; margin-bottom:0.8rem;">
                <span style="
                    display: inline-block;
                    padding: 0.3rem 0.6rem;
                    border-radius: 6px;
                    font-size: 0.7rem;
                    font-weight: 500;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    <?php 
                        if ($fb['type'] === 'bug') {
                            echo 'background:rgba(255,100,100,0.2); color:#ff6464;';
                        } elseif ($fb['type'] === 'suggestion') {
                            echo 'background:rgba(100,150,255,0.2); color:#6496ff;';
                        } else {
                            echo 'background:rgba(100,200,140,0.2); color:#6dc88a;';
                        }
                    ?>
                ">
                    <?= $fb['type'] ?>
                </span>
                <span style="color:var(--muted); font-size:0.8rem; margin-left:auto;">
                    <?= $fb['submitted_at'] ?>
                </span>
            </div>

            <p style="color:var(--text); margin-bottom:0.8rem; line-height:1.6;">
                <?= nl2br(htmlspecialchars($fb['message'])) ?>
            </p>

            <p style="color:var(--muted); font-size:0.8rem; font-style:italic;">
                — <?= htmlspecialchars($fb['name']) ?>
            </p>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</body>
</html>

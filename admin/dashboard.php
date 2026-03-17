<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require '../includes/db.php';

$stmt = $pdo->query("
    SELECT r.id, r.name, r.age, r.city, r.compatibility_score, r.scheduled_date, r.submitted_at, r.owner_username,
           IF(r.scheduled_date IS NOT NULL, r.scheduled_date, 
              IF(m.id IS NOT NULL, 'not sure', NULL)) as display_date
    FROM responses r
    LEFT JOIN messages m ON r.id = m.response_id
    WHERE r.compatibility_score IS NOT NULL
    GROUP BY r.id
    ORDER BY r.submitted_at DESC
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ownerCount = $pdo->query("SELECT COUNT(*) FROM site_owners")->fetchColumn();
$responseCount = count($rows);
$scheduledCount = $pdo->query("
    SELECT COUNT(DISTINCT r.id) FROM responses r
    LEFT JOIN messages m ON r.id = m.response_id
    WHERE r.compatibility_score IS NOT NULL
          AND (r.scheduled_date IS NOT NULL OR m.id IS NOT NULL)
")->fetchColumn();
$feedbackCount = $pdo->query("SELECT COUNT(*) FROM feedback")->fetchColumn();

// Recent accounts
$recentAccounts = $pdo->query("SELECT username, created_at FROM site_owners ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Recent responses by date
$recentResponses = $pdo->query("
    SELECT DATE(r.submitted_at) as date, COUNT(DISTINCT r.id) as count
    FROM responses r
    LEFT JOIN messages m ON r.id = m.response_id
    WHERE r.compatibility_score IS NOT NULL
          AND (r.scheduled_date IS NOT NULL OR m.id IS NOT NULL)
    GROUP BY DATE(r.submitted_at)
    ORDER BY date DESC
    LIMIT 7
")->fetchAll(PDO::FETCH_ASSOC);

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
    <h1 id="mainTitle">create account 🔐</h1>

    <!-- Create account section - enlarged -->
    <div style="margin:2.5rem 0;">
        <a href="create_owner.php" style="
            display:block;
            background:linear-gradient(135deg, rgba(244,167,185,0.15), rgba(244,167,185,0.05));
            border:1px solid rgba(244,167,185,0.3);
            border-radius:16px;
            padding:3rem 2rem;
            text-align:center;
            text-decoration:none;
            transition:all 0.3s ease;
            cursor:pointer;
        "
        onmouseover="this.style.borderColor='var(--pink)'; this.style.background='linear-gradient(135deg, rgba(244,167,185,0.25), rgba(244,167,185,0.1))';"
        onmouseout="this.style.borderColor='rgba(244,167,185,0.3)'; this.style.background='linear-gradient(135deg, rgba(244,167,185,0.15), rgba(244,167,185,0.05))'">
            <div style="font-size:2rem; color:var(--pink); margin-bottom:0.5rem; font-weight:500;">+</div>
            <div style="font-size:1.3rem; color:var(--text); margin-bottom:0.3rem; font-family:'Playfair Display',serif;">create account</div>
            <span style="color:var(--muted); font-size:0.9rem;"><?= $ownerCount ?> account<?= $ownerCount != 1 ? 's' : '' ?> created</span>
        </a>
    </div>

    <!-- Feedback card -->
    <div style="margin:2.5rem 0;">
        <a href="view_feedback.php" style="
            display:block;
            background:linear-gradient(135deg, rgba(200,150,150,0.15), rgba(200,150,150,0.05));
            border:1px solid rgba(200,150,150,0.3);
            border-radius:16px;
            padding:2rem;
            text-align:center;
            text-decoration:none;
            transition:all 0.3s ease;
            cursor:pointer;
        "
        onmouseover="this.style.borderColor='#c89696'; this.style.background='linear-gradient(135deg, rgba(200,150,150,0.25), rgba(200,150,150,0.1))';"
        onmouseout="this.style.borderColor='rgba(200,150,150,0.3)'; this.style.background='linear-gradient(135deg, rgba(200,150,150,0.15), rgba(200,150,150,0.05))'">
            <div style="font-size:2rem; margin-bottom:0.5rem;">🐛</div>
            <div style="font-size:1.2rem; color:var(--text); margin-bottom:0.5rem;"><?= $feedbackCount ?> feedback received</div>
            <div style="color:var(--muted); font-size:0.85rem;">click to view all feedback & bug reports</div>
        </a>
    </div>

    <!-- Toggle button -->
    <div style="text-align:center; margin:2rem 0;">
        <button onclick="toggleResponsesView()" style="
            background:transparent; 
            border:1px solid rgba(244,167,185,0.3); 
            color:var(--muted); 
            border-radius:50px; 
            padding:0.5rem 1.2rem; 
            font-size:0.8rem; 
            cursor:pointer; 
            transition:all 0.2s ease;
            font-family:'DM Sans',sans-serif;"
            onmouseover="this.style.borderColor='var(--pink)'; this.style.color='var(--pink)';"
            onmouseout="this.style.borderColor='rgba(244,167,185,0.3)'; this.style.color='var(--muted)';">
            Noriel Salonga
        </button>
       
    </div>

    <!-- Hidden responses section -->
    <div id="responsesSection" style="display:none;">
        <a href="compatibility_answers.php" class="quick-link" style="display:inline-block; margin-bottom:1rem;">
            ✦ my compatibility answers
        </a>
        <a href="view_feedback.php" class="quick-link" style="display:inline-block; margin-bottom:1rem;">
            🐛 view feedback & bugs
        </a>

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
                    <td>
                        <?php if ($r['display_date'] === 'not sure'): ?>
                            ⏳ not sure
                        <?php elseif ($r['display_date']): ?>
                            📅 <?= htmlspecialchars($r['display_date']) ?>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
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
    </div>

    <script>
        (function() {
            const t = localStorage.getItem('adminTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();

        // Hidden keyboard shortcut to toggle responses (Ctrl+Shift+R)
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.shiftKey && e.key === 'R') {
                const section = document.getElementById('responsesSection');
                const title = document.getElementById('mainTitle');
                const isHidden = section.style.display === 'none';
                section.style.display = isHidden ? 'block' : 'none';
                title.textContent = isHidden ? 'responses dashboard 📋' : 'create account 🔐';
            }
        });

        function toggleResponsesView() {
            const section = document.getElementById('responsesSection');
            const title = document.getElementById('mainTitle');
            const isHidden = section.style.display === 'none';
            section.style.display = isHidden ? 'block' : 'none';
            title.textContent = isHidden ? 'responses dashboard 📋' : 'create account 🔐';
            
            // Scroll to the section if showing
            if (isHidden) {
                setTimeout(() => {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }
        }
    </script>
</body>
</html>
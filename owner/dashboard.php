<?php
session_start();
if (empty($_SESSION['owner'])) {
    header('Location: login.php');
    exit;
}
require '../includes/db.php';

$username = $_SESSION['owner'];

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);
    
    // Verify ownership
    $checkStmt = $pdo->prepare("SELECT id FROM responses WHERE id = ? AND owner_username = ?");
    $checkStmt->execute([$deleteId, $username]);
    
    if ($checkStmt->rowCount() > 0) {
        $delStmt = $pdo->prepare("DELETE FROM responses WHERE id = ? AND owner_username = ?");
        $delStmt->execute([$deleteId, $username]);
        
        // Also delete any maybe reasons associated with this response
        $delMaybeStmt = $pdo->prepare("DELETE FROM maybe_reasons WHERE response_id = ?");
        $delMaybeStmt->execute([$deleteId]);
        
        header('Location: dashboard.php');
        exit;
    }
}

$stmt = $pdo->prepare("
    SELECT r.id, r.name, r.age, r.city, r.scheduled_date, r.submitted_at,
           IF(r.scheduled_date IS NOT NULL, r.scheduled_date, 
              IF(m.id IS NOT NULL, 'not sure', NULL)) as display_date
    FROM responses r
    LEFT JOIN messages m ON r.id = m.response_id
    WHERE r.owner_username = ? AND r.compatibility_score IS NOT NULL
    GROUP BY r.id
    ORDER BY r.submitted_at DESC
");
$stmt->execute([$username]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch maybe reasons for this owner
$maybeStmt = $pdo->prepare("
    SELECT mr.reason, mr.submitted_at, r.name
    FROM maybe_reasons mr
    LEFT JOIN responses r ON mr.response_id = r.id
    WHERE mr.owner_username = ?
    ORDER BY mr.submitted_at DESC
");
$maybeStmt->execute([$username]);
$maybeRows = $maybeStmt->fetchAll(PDO::FETCH_ASSOC);
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

    <h1>yo, <?= htmlspecialchars($username) ?>. what's good g</h1>

    <div style="display:flex; gap:1.2rem; margin-bottom:1.8rem; flex-wrap:wrap; font-size:0.85rem;">
        <a href="change_password.php" style="color:var(--pink); text-decoration:none;">🔑 change password</a>
        <span style="color:var(--border);">·</span>
        <a href="compatibility_answers.php" style="color:var(--pink); text-decoration:none;">💘 my compatibility answers</a>
        <span style="color:var(--border);">·</span>
        <a href="edit_profile.php" style="color:var(--pink); text-decoration:none;">✏️ edit my profile</a>
        <span style="color:var(--border);">·</span>
        <a href="https://instagram.com/sa.loooong.a" target="_blank" style="color:var(--pink); text-decoration:none;">💬 message dev</a>
    </div>

    <!-- ── Responses table ── -->
    <?php 
        $count = count($rows);
        $messages = [
            0 => "no bites yet...share mo kasi link wag ka torpe",
            1 => "naks one person interested, not bad ",
            2 => "two dates?? sana same person lang yan",
            3 => "three's a crowd bro... baka may mag overlap sa sched mo nyan",
            4 => "four dates?? hakot ka na boi magtira ka naman",
            5 => "five?? this mf knows ball",
        ];
        $message = $messages[$count] ?? ($count . " dates na naka-set mo g, paawat ka naman");
    ?>
    <p style="color:var(--pink); margin-bottom:1rem; font-size:0.95rem; font-weight:500;">
        <?= $message ?>
    </p>

    <?php if (empty($rows)): ?>
        <div style="background:rgba(244,167,185,0.08); border:1px solid rgba(244,167,185,0.2); 
                    border-radius:12px; padding:1.5rem; margin-top:2rem;">
            <p style="color:var(--text); margin-bottom:0.5rem; font-weight:500;">
                share mo na link na to para mareject ka na
            </p>
            <p style="color:var(--muted); font-size:0.9rem;">
                goodluck out there, thank me later
            </p>
        </div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th>City</th>
                <th>Planned Date</th>
                <th>Responded</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
            <tr onclick="window.location='view.php?id=<?= $r['id'] ?>'">
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= $r['age'] ?></td>
                <td><?= htmlspecialchars($r['city']) ?></td>
                <td style="<?= $r['display_date'] ? 'color:var(--pink); font-weight:500;' : 'color:var(--muted);' ?>">
                    <?php if ($r['display_date'] === 'not sure'): ?>
                        ⏳ not sure
                    <?php elseif ($r['display_date']): ?>
                        📅 <?= htmlspecialchars($r['display_date']) ?>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td style="font-size:0.8rem; color:var(--muted);"><?= $r['submitted_at'] ?></td>
                <td style="text-align:right;">
                    <button onclick="playfulDelete(event, '<?= htmlspecialchars($r['name']) ?>', <?= $r['id'] ?>)" style="background:none; border:none; color:var(--pink); cursor:pointer; font-size:1rem; padding:0.4rem;" title="delete">🗑️</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p class="table-hint">✨ tap a row to see if they're actually your person</p>
    <?php endif; ?>

    <!-- ── Maybe reasons ── -->
    <?php if (!empty($maybeRows)): ?>
    <div style="margin-top:3rem;">
        <h2 style="font-family:'Playfair Display',serif; font-size:1.1rem;
                   margin-bottom:0.4rem; color:var(--text);">
            on the fence 
        </h2>
        <p style="color:var(--muted); font-size:0.8rem; margin-bottom:1.2rem;">
            people said maybe, and here's why. <?= count($maybeRows) ?> reason<?= count($maybeRows) !== 1 ? 's' : '' ?> below:
        </p>
        <table>
            <thead>
                <tr>
                    <th>What They Said</th>
                    <th>Who</th>
                    <th>When</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($maybeRows as $mr): ?>
                <tr>
                    <td>
                        <span style="color:var(--pink);">💭</span>
                        <?= htmlspecialchars($mr['reason']) ?>
                    </td>
                    <td>
                        <?php if ($mr['name']): ?>
                            <strong><?= htmlspecialchars($mr['name']) ?></strong>
                        <?php else: ?>
                            <span style="color:var(--muted); font-style:italic;">mystery person</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:0.8rem; color:var(--muted);"><?= $mr['submitted_at'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- ── Footer message ── -->
    <?php if ($count < 10): ?>
    
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

        function playfulDelete(event, name, id) {
            event.stopPropagation();
            
            const messages = [
                `nah, ${name}'s not it?`,
                `bat mo naman dedelete si ${name} `,
                `removing ${name} from the maybe pile?`,
                `${name} didn't pass vibe?`,
                `pass kay ${name}?`,
                `out with ${name}? okay okay, your loss`,
            ];
            
            const randomMsg = messages[Math.floor(Math.random() * messages.length)];
            
            if (confirm(randomMsg + '\n\n\nsure ka na? this can\'t be undone...')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = '<input type="hidden" name="delete_id" value="' + id + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

    <footer style="margin-top:4rem; padding:1.5rem 0; border-top:1px solid var(--border); text-align:center; opacity:0.5;">
        <p style="color:var(--muted); font-weight:400; font-size:0.75rem; margin:0;">
            © <?= date('Y') ?> @sa.loooong.a. all rights reserved.
        </p>
    </footer>
</body>
</html>
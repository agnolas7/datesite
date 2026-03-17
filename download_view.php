<?php
session_start();
require 'includes/db.php';

// Support direct link with ?id=TOKEN
if (!empty($_GET['id'])) {
    $token = $_GET['id'];
    // decode: base64 of "responseId_salt"
    $decoded = base64_decode($token);
    $parts   = explode('_', $decoded);
    $id      = intval($parts[0] ?? 0);
} elseif (!empty($_SESSION['response_id'])) {
    $id = $_SESSION['response_id'];
} else {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM responses WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) die("Response not found.");

// Security: only the owner of this response (via session) OR a direct token link can view
// If coming via session, verify it matches
if (empty($_GET['id']) && $_SESSION['response_id'] != $id) {
    header('Location: index.php');
    exit;
}

$name = htmlspecialchars($row['name']);

// Generate a stable shareable token for this response
$shareToken = base64_encode($id . '_ds2025');
$shareUrl   = (isset($_SERVER['HTTPS']) ? 'https' : 'http')
            . '://' . $_SERVER['HTTP_HOST']
            . strtok($_SERVER['REQUEST_URI'], '?')
            . '?id=' . $shareToken;

// Compatibility score
$compatStmt = $pdo->prepare("SELECT compatibility_score FROM responder_compatibility_answers WHERE response_id = ? ORDER BY id DESC LIMIT 1");
$compatStmt->execute([$id]);
$compatRow   = $compatStmt->fetch(PDO::FETCH_ASSOC);
$compatScore = $compatRow['compatibility_score'] ?? null;

// Format scheduled date
$displayDate = '';
if (!empty($row['scheduled_date'])) {
    try {
        $d = new DateTime($row['scheduled_date']);
        $displayDate = $d->format('l, F j, Y · g:i A');
    } catch (Exception $e) {
        $displayDate = $row['scheduled_date'];
    }
}

function v($val) {
    return trim($val) ? htmlspecialchars(trim($val)) : '<em style="color:#888">—</em>';
}

function tags($val) {
    $val = trim($val);
    if (!$val) return '<em style="color:#888">—</em>';
    $items = array_filter(array_map('trim', explode(',', $val)));
    $out = '';
    foreach ($items as $item) {
        $out .= '<span style="display:inline-block; background:#1f1f1f; border:1px solid #333;
                 border-radius:50px; padding:0.2rem 0.6rem; font-size:0.75rem;
                 color:#f0ece4; margin:0.15rem;">' . htmlspecialchars($item) . '</span>';
    }
    return $out;
}

// If ?download=1, force download as HTML file
if (!empty($_GET['download'])) {
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . preg_replace('/[^a-z0-9]/i', '_', $row['name']) . '_date_profile.html"');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>your answers — <?= $name ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --pink: #f4a7b9;
            --bg: #0d0d0d;
            --text: #f0ece4;
            --muted: #888;
            --border: #2a2a2a;
            --input-bg: #1f1f1f;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── Top bar ── */
        .top-bar {
            background: var(--pink);
            color: #1a0a10;
            padding: 0.7rem 1.2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
            font-size: 0.82rem;
            font-weight: 500;
        }

        .top-bar-left { display: flex; align-items: center; gap: 0.5rem; }

        .top-bar-right { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }

        .top-btn {
            background: #1a0a10;
            color: var(--pink);
            border: none;
            border-radius: 50px;
            padding: 0.3rem 0.85rem;
            font-size: 0.75rem;
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: opacity 0.2s;
            white-space: nowrap;
        }

        .top-btn:hover { opacity: 0.8; }

        /* ── Wrapper ── */
        .wrapper {
            max-width: 680px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 5rem;
        }

        /* ── Header ── */
        .doc-header {
            text-align: center;
            margin-bottom: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border);
        }

        .doc-tag {
            display: inline-block;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--pink);
            border: 1px solid var(--pink);
            border-radius: 50px;
            padding: 0.25rem 0.75rem;
            margin-bottom: 1rem;
        }

        .doc-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 5vw, 2.5rem);
            color: var(--text);
            margin-bottom: 0.3rem;
            line-height: 1.2;
        }

        .doc-header h1 span { color: var(--pink); font-style: italic; }
        .doc-header p { color: var(--muted); font-size: 0.82rem; }

        /* ── Copy success toast ── */
        .toast {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: #1f1f1f;
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 50px;
            padding: 0.5rem 1.2rem;
            font-size: 0.8rem;
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            pointer-events: none;
            z-index: 999;
            white-space: nowrap;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* ── Banners ── */
        .banner {
            border-radius: 14px;
            padding: 1rem 1.4rem;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .banner-pink {
            background: rgba(244, 167, 185, 0.07);
            border: 1px solid rgba(244, 167, 185, 0.3);
        }

        .banner-green {
            background: rgba(109, 200, 138, 0.06);
            border: 1px solid rgba(109, 200, 138, 0.25);
        }

        .banner-icon { font-size: 1.8rem; flex-shrink: 0; }

        .banner-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 500;
            margin-bottom: 0.2rem;
        }

        .banner-pink .banner-label { color: var(--pink); }
        .banner-green .banner-label { color: #6dc88a; }
        .banner-value { font-size: 0.9rem; color: var(--text); font-weight: 500; }

        .banner-score {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: #6dc88a;
            line-height: 1;
            flex-shrink: 0;
        }

        /* ── Sections ── */
        .section { margin-bottom: 2.2rem; }

        .section-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--muted);
            margin-bottom: 0.7rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border);
        }

        /* ── Rows ── */
        .row {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 0.8rem;
            padding: 0.75rem 0.6rem;
            border-bottom: 1px solid var(--border);
        }

        .row:last-child { border-bottom: none; }

        .row-label {
            font-size: 0.7rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding-top: 0.1rem;
            line-height: 1.4;
        }

        .row-val {
            font-size: 0.88rem;
            color: var(--text);
            line-height: 1.5;
        }

        /* ── Footer ── */
        .doc-footer {
            text-align: center;
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
            color: var(--muted);
            font-size: 0.75rem;
            line-height: 1.8;
        }

        .doc-footer span { color: var(--pink); }

        /* ── Print ── */
        @media print {
            .top-bar { display: none !important; }
            .toast   { display: none !important; }
            body { background: #fff; color: #111; }
            :root {
                --bg: #fff; --text: #111; --muted: #555;
                --border: #ddd; --input-bg: #f5f5f5; --pink: #c45c78;
            }
            .wrapper { padding: 1rem; }
        }

        /* ── Mobile ── */
        @media (max-width: 480px) {
            .wrapper { padding: 1.5rem 1rem 4rem; }
            .row { grid-template-columns: 1fr; gap: 0.2rem; }
            .row-label { font-size: 0.68rem; }
            .doc-header h1 { font-size: 1.6rem; }
            .top-bar { font-size: 0.75rem; }
        }
    </style>
</head>
<body>

    <!-- Top bar -->
    <div class="top-bar">
        <div class="top-bar-left">
            📄 your answers
        </div>
        <div class="top-bar-right">
            <a class="top-btn" href="?id=<?= $shareToken ?>&download=1">download file 📥</a>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast">link copied!</div>

    <div class="wrapper">

        <!-- Header -->
        <div class="doc-header">
            <div class="doc-tag">✦ your answers</div>
            <h1>hey, <span><?= $name ?></span> 🌸</h1>
            <p>here's everything you told me — submitted <?= htmlspecialchars($row['submitted_at']) ?></p>
        </div>

        <!-- Scheduled date banner -->
        <?php if ($displayDate): ?>
        <div class="banner banner-pink">
            <span class="banner-icon">🗓️</span>
            <div>
                <div class="banner-label">date scheduled</div>
                <div class="banner-value"><?= htmlspecialchars($displayDate) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Compatibility banner -->
        <?php if ($compatScore !== null): ?>
        <div class="banner banner-green">
            <span class="banner-score"><?= round($compatScore) ?>%</span>
            <div>
                <div class="banner-label">compatibility score</div>
                <div class="banner-value" style="color:var(--muted); font-size:0.82rem; font-weight:400;">
                    <?php
                    if ($compatScore >= 90)     echo "suspiciously compatible 👀";
                    elseif ($compatScore >= 70) echo "this might actually work 🌸";
                    elseif ($compatScore >= 50) echo "some differences but interesting 🤔";
                    else                        echo "we might argue about movie choices 😅";
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- About you -->
        <div class="section">
            <div class="section-label">about you</div>
            <div class="row"><span class="row-label">Name</span><span class="row-val"><?= v($row['name']) ?></span></div>
            <div class="row"><span class="row-label">Age</span><span class="row-val"><?= v($row['age']) ?></span></div>
            <div class="row"><span class="row-label">City</span><span class="row-val"><?= v($row['city']) ?></span></div>
            <div class="row"><span class="row-label">Dealbreaker</span><span class="row-val"><?= v($row['dealbreaker']) ?></span></div>
        </div>

        <!-- How to reach you -->
        <div class="section">
            <div class="section-label">how to reach you</div>
            <div class="row"><span class="row-label">Communication</span><span class="row-val"><?= tags($row['communication']) ?></span></div>
            <div class="row"><span class="row-label">Best time</span><span class="row-val"><?= v($row['best_time']) ?></span></div>
        </div>

        <!-- Food & preferences -->
        <div class="section">
            <div class="section-label">food and preferences</div>
            <div class="row"><span class="row-label">Food & drink</span><span class="row-val"><?= v($row['food_drink']) ?></span></div>
            <div class="row"><span class="row-label">Flower</span><span class="row-val"><?= v($row['flower']) ?></span></div>
            <div class="row"><span class="row-label">Craving lately</span><span class="row-val"><?= v($row['craving']) ?></span></div>
            <div class="row"><span class="row-label">Dessert</span><span class="row-val"><?= v($row['dessert']) ?></span></div>
            <div class="row"><span class="row-label">Temperature</span><span class="row-val"><?= v($row['temperature']) ?></span></div>
            <div class="row"><span class="row-label">Dislikes going out</span><span class="row-val"><?= tags($row['dislikes']) ?></span></div>
        </div>

        <!-- Date preferences -->
        <div class="section">
            <div class="section-label">date preferences</div>
            <div class="row"><span class="row-label">Date type</span><span class="row-val"><?= v($row['date_type']) ?></span></div>
            <div class="row"><span class="row-label">Spontaneity</span><span class="row-val"><?= v($row['spontaneity']) ?></span></div>
            <div class="row"><span class="row-label">Energy level</span><span class="row-val"><?= v($row['energy']) ?></span></div>
            <div class="row"><span class="row-label">Mood</span><span class="row-val"><?= v($row['mood']) ?></span></div>
            <div class="row"><span class="row-label">Crowd</span><span class="row-val"><?= v($row['crowd']) ?></span></div>
            <div class="row"><span class="row-label">Walking</span><span class="row-val"><?= v($row['walking']) ?></span></div>
            <div class="row"><span class="row-label">Convo style</span><span class="row-val"><?= v($row['convo_style']) ?></span></div>
            <div class="row"><span class="row-label">Yapper or listener?</span><span class="row-val"><?= v($row['awkwardness']) ?></span></div>
        </div>

        <!-- Before we plan -->
        <div class="section">
            <div class="section-label">before we plan</div>
            <div class="row"><span class="row-label">Curfew</span><span class="row-val"><?= v($row['curfew']) ?></span></div>
            <div class="row"><span class="row-label">Parents' rules</span><span class="row-val"><?= v($row['parents']) ?></span></div>
            <div class="row"><span class="row-label">Distance willing</span><span class="row-val"><?= v($row['distance']) ?></span></div>
        </div>

        <!-- Vibes -->
        <div class="section">
            <div class="section-label">vibes and activities</div>
            <div class="row"><span class="row-label">Picked vibes</span><span class="row-val"><?= tags($row['vibes']) ?></span></div>
            <?php if (!empty($row['custom_vibe'])): ?>
            <div class="row"><span class="row-label">Their idea</span><span class="row-val"><?= v($row['custom_vibe']) ?></span></div>
            <?php endif; ?>
            <div class="row"><span class="row-label">Place in mind?</span><span class="row-val"><?= v($row['place_in_mind']) ?></span></div>
            <?php if (!empty($row['place_name'])): ?>
            <div class="row"><span class="row-label">Place name</span><span class="row-val"><?= v($row['place_name']) ?></span></div>
            <?php endif; ?>
            <?php if (!empty($row['place_timing'])): ?>
            <div class="row"><span class="row-label">When?</span><span class="row-val"><?= v($row['place_timing']) ?></span></div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="doc-footer">
            ✦ saved on <?= date('F j, Y') ?> ✦<br>
            made just for you
        </div>

    </div>

    <script>
    function showToast(msg) {
        const toast = document.getElementById('toast');
        toast.textContent = msg;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2500);
    }
    </script>

    <footer style="margin-top:4rem; padding:2rem 1rem; text-align:center;">
        <a href="feedback.php" style="
            display: inline-block;
            color: var(--muted);
            text-decoration: none;
            font-size: 0.8rem;
            padding: 0.8rem 1.2rem;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            transition: all 0.2s ease;
        " 
        onmouseover="this.style.backgroundColor='rgba(244,167,185,0.08)'; this.style.borderColor='rgba(244,167,185,0.3)'; this.style.color='var(--pink)'"
        onmouseout="this.style.backgroundColor='transparent'; this.style.borderColor='rgba(255,255,255,0.1)'; this.style.color='var(--muted)'">
            🐛 found a bug or have feedback?
        </a>
    </footer>
</body>
</html>
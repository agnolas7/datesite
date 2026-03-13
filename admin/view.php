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

// Fetch messages
$msgStmt = $pdo->prepare("SELECT message_text, sent_at FROM messages WHERE response_id = ? ORDER BY sent_at ASC");
$msgStmt->execute([$id]);
$messages = $msgStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch detailed compatibility results
$compatStmt = $pdo->prepare("SELECT * FROM responder_compatibility_answers WHERE response_id = ? ORDER BY id DESC LIMIT 1");
$compatStmt->execute([$id]);
$compatData = $compatStmt->fetch(PDO::FETCH_ASSOC);

// Build compatibility details if admin answered and responder answered
$compatDetails = '';
$matchingDetail = []; // store ALL details for display
if ($compatData && $r['owner_username']) {
    require '../includes/compatibility_questions.php';
    
    $adminStmt = $pdo->prepare("SELECT * FROM admin_compatibility_answers WHERE owner_username = ? ORDER BY id DESC LIMIT 1");
    $adminStmt->execute([$r['owner_username']]);
    $adminData = $adminStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($adminData) {
        $matches = [];
        $mismatches = 0;
        foreach ($compatibility_questions as $name => $q) {
            $respVal = $compatData[$name] ?? '';
            $adminVal = $adminData[$name] ?? '';
            $isMatch = trim($respVal) === trim($adminVal) && trim($respVal);
            
            if ($isMatch) {
                $matches[] = $q['label'];
            }
            
            // Add to detailed list regardless of match
            $matchingDetail[] = [
                'label' => $q['label'],
                'type' => $q['type'],
                'responder' => $respVal,
                'owner' => $adminVal,
                'isMatch' => $isMatch,
                'options' => $q['options'] ?? []
            ];
            
            if (!$isMatch && trim($respVal) && trim($adminVal)) {
                $mismatches++;
            }
        }
        $matchText = !empty($matches) ? count($matches) . ' match' . (count($matches) != 1 ? 'es' : '') : '0 matches';
        $compatDetails = $matchText . ' • ' . $mismatches . ' difference' . ($mismatches != 1 ? 's' : '');
    }
}
$matchingDetailJSON = json_encode($matchingDetail);

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
    <style>
        .match-detail-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.2s ease;
        }
        .match-detail-modal.open {
            display: flex;
        }
        .match-detail-content {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 70vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease;
        }
        .match-detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .match-detail-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            color: var(--text);
            margin: 0;
        }
        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--muted);
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .close-btn:hover {
            color: var(--text);
        }
        .match-item {
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.2rem;
            margin-bottom: 1rem;
        }
        .match-item-label {
            font-weight: bold;
            color: var(--pink);
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
        }
        .match-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .match-badge, .diff-badge {
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .match-badge {
            background: rgba(76, 175, 80, 0.2);
            color: #4caf50;
        }
        .diff-badge {
            background: rgba(255, 152, 0, 0.2);
            color: #ff9800;
        }
        .is-match {
            border-left: 3px solid rgba(76, 175, 80, 0.5);
        }
        .is-diff {
            border-left: 3px solid rgba(255, 152, 0, 0.5);
            opacity: 0.85;
        }
        .rank-list > div {
            margin-bottom: 0.3rem;
            font-size: 0.85rem;
        }
        .match-comparison {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .match-side {
            padding: 0.8rem;
            background: var(--bg);
            border-radius: 8px;
            border: 1px solid var(--border);
        }
        .match-side-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--muted);
            margin-bottom: 0.4rem;
        }
        .match-side-value {
            color: var(--text);
            font-size: 0.9rem;
            line-height: 1.5;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .messages-section {
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.2rem;
            margin-top: 1.5rem;
        }
        .messages-section-label {
            font-weight: bold;
            color: var(--pink);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
            display: block;
        }
        .message-item {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.8rem;
            margin-bottom: 0.8rem;
            line-height: 1.6;
            color: var(--text);
            font-size: 0.9rem;
        }
        .message-time {
            font-size: 0.7rem;
            color: var(--muted);
            margin-top: 0.4rem;
        }
    </style>
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
            <?php if ($sectionName === 'results' && $compatDetails): ?>
            <div class="field">
                <strong>Matching answers</strong>
                <span class="clickable-match" onclick="showMatchDetail()">click to see details →</span>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <?php if (!empty($messages)): ?>
        <div class="messages-section">
            <span class="messages-section-label">💌 messages (<?= count($messages) ?>)</span>
            <?php foreach ($messages as $msg): ?>
            <div class="message-item">
                <?= htmlspecialchars($msg['message_text']) ?>
                <div class="message-time"><?= $msg['sent_at'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>

    <!-- Matching details modal -->
    <div id="matchModal" class="match-detail-modal">
        <div class="match-detail-content">
            <div class="match-detail-header">
                <h3>full compatibility breakdown 💬</h3>
                <button class="close-btn" onclick="closeMatchDetail()">×</button>
            </div>
            <div id="matchItemsContainer"></div>
        </div>
    </div>

    <script>
        const matchingData = <?= $matchingDetailJSON ?>;

        function showMatchDetail() {
            const modal = document.getElementById('matchModal');
            const container = document.getElementById('matchItemsContainer');
            
            if (!matchingData || matchingData.length === 0) {
                container.innerHTML = '<p style="color:var(--muted);">no matching answers</p>';
                modal.classList.add('open');
                return;
            }

            container.innerHTML = matchingData.map(item => {
                const badgeClass = item.isMatch ? 'match-badge' : 'diff-badge';
                const badgeText = item.isMatch ? '✓ match' : '✕ different';
                
                // Handle ranking display - prepare HTML but mark it for later innerHTML assignment
                let respContent = escapeHtml(item.responder);
                let ownerContent = escapeHtml(item.owner);
                let isRank = item.type === 'rank';
                
                if (isRank) {
                    const formatRank = (str) => {
                        return str.split(', ').map((val, i) => `<div>#${i+1}: ${escapeHtml(val)}</div>`).join('');
                    };
                    respContent = formatRank(item.responder);
                    ownerContent = formatRank(item.owner);
                }
                
                return `
                    <div class="match-item ${item.isMatch ? 'is-match' : 'is-diff'}">
                        <div class="match-item-header">
                            <div class="match-item-label">${escapeHtml(item.label)}</div>
                            <span class="${badgeClass}">${badgeText}</span>
                        </div>
                        <div class="match-comparison">
                            <div class="match-side">
                                <div class="match-side-label">👤 Responder</div>
                                <div class="match-side-value ${isRank ? 'rank-list' : ''}" ${isRank ? 'data-rank-content="true"' : ''}>${isRank ? '' : respContent}</div>
                            </div>
                            <div class="match-side">
                                <div class="match-side-label">📋 You</div>
                                <div class="match-side-value ${isRank ? 'rank-list' : ''}" ${isRank ? 'data-rank-content="true"' : ''}>${isRank ? '' : ownerContent}</div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            // Store rank content and populate after DOM ready
            const rankPairs = matchingData.filter(item => item.type === 'rank').map((item, idx) => ({
                idx,
                resp: item.responder.split(', ').map((val, i) => `<div>#${i+1}: ${escapeHtml(val)}</div>`).join(''),
                owner: item.owner.split(', ').map((val, i) => `<div>#${i+1}: ${escapeHtml(val)}</div>`).join('')
            }));
            
            // Fill in rank content
            let rankIdx = 0;
            document.querySelectorAll('[data-rank-content="true"]').forEach((el, globalIdx) => {
                const pair = rankPairs[rankIdx];
                if (globalIdx % 2 === 0) {
                    el.innerHTML = pair.resp;
                } else {
                    el.innerHTML = pair.owner;
                    rankIdx++;
                }
            });
            
            modal.classList.add('open');
        }

        function closeMatchDetail() {
            document.getElementById('matchModal').classList.remove('open');
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Close modal on background click
        document.getElementById('matchModal').addEventListener('click', function(e) {
            if (e.target === this) closeMatchDetail();
        });
    </script>

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
<?php
session_start();
if (empty($_SESSION['owner'])) {
    header('Location: login.php');
    exit;
}
require '../includes/db.php';

$id       = intval($_GET['id'] ?? 0);
$username = $_SESSION['owner'];

$stmt = $pdo->prepare("SELECT * FROM responses WHERE id = ? AND owner_username = ?");
$stmt->execute([$id, $username]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$r) die("Not found.");

// Get response number (rank) for this owner
$rankStmt = $pdo->prepare("SELECT COALESCE((SELECT COUNT(*) FROM responses WHERE owner_username = ? AND id < ?), 0) + 1 as response_number");
$rankStmt->execute([$username, $id]);
$rankData = $rankStmt->fetch(PDO::FETCH_ASSOC);
$responseNumber = $rankData['response_number'] ?? 1;

// Fetch messages (exclude "not sure" marker)
$msgStmt = $pdo->prepare("SELECT message_text, instagram_handle, sent_at FROM messages WHERE response_id = ? AND message_text != '__not_sure_marker__' ORDER BY sent_at ASC");
$msgStmt->execute([$id]);
$messages = $msgStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch detailed compatibility results
$compatStmt = $pdo->prepare("SELECT * FROM responder_compatibility_answers WHERE response_id = ? ORDER BY id DESC LIMIT 1");
$compatStmt->execute([$id]);
$compatData = $compatStmt->fetch(PDO::FETCH_ASSOC);

// Build compatibility details
$compatDetails = '';
$matchingDetail = []; // store ALL details for display
if ($compatData) {
    require '../includes/compatibility_questions.php';
    
    $adminStmt = $pdo->prepare("SELECT * FROM admin_compatibility_answers WHERE owner_username = ? ORDER BY id DESC LIMIT 1");
    $adminStmt->execute([$username]);
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
        'maybe_reason' => 'Why she said maybe',
    ],
    'date preferences' => [
        'date_type'        => 'Date type',
        'spontaneity'      => 'Spontaneity',
        'energy'           => 'Energy',
        'mood'             => 'Mood',
        'crowd'            => 'Crowd',
        'convo_style'      => 'Convo style',
        'walking'          => 'Walking',
        'awkwardness'      => 'Yapper or listener?',
    ],
    'before we plan' => [
        'curfew'   => 'Do you have a curfew?',
        'parents'  => 'How are your parents about going out?',
        'distance' => 'How far from home are you okay going?',
    ],
    'vibes & extras' => [
        'vibes'        => 'What activities sound good?',
        'custom_vibe'  => 'Something else you had in mind?',
        'place_in_mind' => 'Is there somewhere you want to go?',
        'place_name'   => 'What place?',
        'place_timing' => 'When do you want to go?',
    ],
    'results' => [

        'scheduled_date'      => 'Scheduled date',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($r['name']) ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        (function() {
            const t = localStorage.getItem('ownerTheme') || 'dark';
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
        .rank-list > div.rank-match {
            background: rgba(76, 175, 80, 0.15);
            padding: 0.3rem 0.4rem;
            border-radius: 4px;
            color: #4caf50;
            font-weight: 500;
        }
        .rank-list > div.rank-shared {
            background: rgba(255, 193, 7, 0.15);
            padding: 0.3rem 0.4rem;
            border-radius: 4px;
            color: #ffc107;
            font-weight: 500;
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
        .clickable-match {
            cursor: pointer;
            text-decoration: underline;
            color: var(--pink);
        }
        .clickable-match:hover {
            opacity: 0.8;
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
        .message-instagram {
            font-size: 0.8rem;
            color: var(--pink);
            margin-top: 0.4rem;
            margin-bottom: 0.4rem;
            font-weight: 600;
        }
    </style>
</head>
<body class="admin-view">

    <div class="view-wrapper">

        <a href="dashboard.php" class="view-back">← back to dashboard</a>

        <div class="view-header">
            <div class="view-name"><?= htmlspecialchars($r['name']) ?></div>
            <div class="view-id">#<?= $responseNumber ?></div>
        </div>
        <div class="view-submitted">submitted <?= htmlspecialchars($r['submitted_at']) ?></div>

        <?php foreach ($sections as $sectionName => $fields): ?>
        <div class="view-section">
            <div class="view-section-label"><?= $sectionName ?></div>

            <?php foreach ($fields as $key => $label):
                // Skip maybe_reason if they already have a scheduled date
                if ($key === 'maybe_reason' && !empty($r['scheduled_date'])) {
                    continue;
                }
                
                $raw     = $r[$key] ?? '';
                $isEmpty = ($raw === '' || $raw === null);
                
                // Check if this field has multiple comma-separated values (from checkboxes)
                $items = array_filter(array_map('trim', explode(', ', $raw)));
                $isMultiple = !$isEmpty && count($items) > 1;
                
                $extraClass = '';
                if ($key === 'scheduled_date' && !$isEmpty) $extraClass = 'date-field';
            ?>
            <div class="field <?= $extraClass ?>">
                <strong><?= $label ?></strong>
                <?php if ($isEmpty): ?>
                    <span class="empty">—</span>
                <?php elseif ($isMultiple): ?>
                    <div class="field-tags">
                        <?php foreach ($items as $item): ?>
                        <span class="field-tag"><?= htmlspecialchars($item) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <span><?= htmlspecialchars($raw) ?></span>
                <?php endif; ?>
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
                <?php if (!empty($msg['instagram_handle'])): ?>
                <div class="message-instagram">📱ig: <?= htmlspecialchars($msg['instagram_handle']) ?></div>
                <?php endif; ?>
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
                container.innerHTML = '<p style="color:var(--muted);">no compatibility data</p>';
                modal.classList.add('open');
                return;
            }

            container.innerHTML = matchingData.map((item, idx) => {
                let isRank = item.type === 'rank';
                
                // Determine badge for non-rank items only
                let badgeClass = '';
                let badgeText = '';
                if (!isRank) {
                    badgeClass = item.isMatch ? 'match-badge' : 'diff-badge';
                    badgeText = item.isMatch ? '✓ match' : '✕ different';
                }
                
                // For rankings, find matching positions AND shared items at different positions
                let respMatchPositions = [];
                let ownerMatchPositions = [];
                let respSharedDiffPos = [];
                let ownerSharedDiffPos = [];
                if (isRank) {
                    const respRanks = item.responder.split(', ');
                    const ownerRanks = item.owner.split(', ');
                    
                    // Find exact position matches
                    respRanks.forEach((val, i) => {
                        if (val.trim() === ownerRanks[i]?.trim()) {
                            respMatchPositions.push(i);
                            ownerMatchPositions.push(i);
                        }
                    });
                    
                    // Find shared items at different positions
                    respRanks.forEach((val, i) => {
                        if (!respMatchPositions.includes(i)) {
                            const ownerIndex = ownerRanks.findIndex(v => v.trim() === val.trim());
                            if (ownerIndex !== -1) {
                                respSharedDiffPos.push(i);
                            }
                        }
                    });
                    ownerRanks.forEach((val, i) => {
                        if (!ownerMatchPositions.includes(i)) {
                            const respIndex = respRanks.findIndex(v => v.trim() === val.trim());
                            if (respIndex !== -1) {
                                ownerSharedDiffPos.push(i);
                            }
                        }
                    });
                }
                
                // Handle ranking display with highlighting
                const formatRank = (str, matchPositions, sharedDiffPos) => {
                    return str.split(', ').map((val, i) => {
                        const isMatchPos = matchPositions && matchPositions.includes(i);
                        const isSharedDiff = sharedDiffPos && sharedDiffPos.includes(i);
                        let className = '';
                        if (isMatchPos) className = 'rank-match';
                        else if (isSharedDiff) className = 'rank-shared';
                        return `<div class="${className}">#${i+1}: ${escapeHtml(val)}</div>`;
                    }).join('');
                };
                
                let respContent = escapeHtml(item.responder);
                let ownerContent = escapeHtml(item.owner);
                
                if (isRank) {
                    respContent = formatRank(item.responder, respMatchPositions, respSharedDiffPos);
                    ownerContent = formatRank(item.owner, ownerMatchPositions, ownerSharedDiffPos);
                }
                
                const badgeHtml = badgeText ? `<span class="${badgeClass}">${badgeText}</span>` : '';
                
                return `
                    <div class="match-item ${item.isMatch && !isRank ? 'is-match' : !isRank ? 'is-diff' : ''}">
                        <div class="match-item-header">
                            <div class="match-item-label">${escapeHtml(item.label)}</div>
                            ${badgeHtml}
                        </div>
                        <div class="match-comparison">
                            <div class="match-side">
                                <div class="match-side-label">👤 Responder</div>
                                <div class="match-side-value ${isRank ? 'rank-list' : ''}">${isRank ? respContent : respContent}</div>
                            </div>
                            <div class="match-side">
                                <div class="match-side-label">📋 You</div>
                                <div class="match-side-value ${isRank ? 'rank-list' : ''}">${isRank ? ownerContent : ownerContent}</div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
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

        document.getElementById('matchModal').addEventListener('click', function(e) {
            if (e.target === this) closeMatchDetail();
        });
    </script>
</body>
</html>
<?php
session_start();
require 'includes/db.php';

if (empty($_SESSION['response_id'])) {
    header('Location: index.php');
    exit;
}

$id = $_SESSION['response_id'];
$stmt = $pdo->prepare("SELECT * FROM responses WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$name = htmlspecialchars($_SESSION['name']);

// Check if already scheduled
$alreadyScheduled = !empty($row['scheduled_date']);
$scheduledDate    = $row['scheduled_date'] ?? '';

// Format it for display if it exists
$displayScheduled = '';
if ($alreadyScheduled) {
    try {
        $dateObj = new DateTime($scheduledDate);
        $displayScheduled = $dateObj->format('l, F j, Y · g:i A');
    } catch (Exception $e) {
        $displayScheduled = $scheduledDate;
    }
}

// Load owner instagram for message links
$ownerUsername = $row['owner_username'] ?? null;
$instagramLink = 'https://instagram.com/sa.loooong.a'; // default
if ($ownerUsername) {
    $ownerStmt = $pdo->prepare("SELECT * FROM site_owners WHERE username = ?");
    $ownerStmt->execute([$ownerUsername]);
    $ownerData = $ownerStmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>results are in 🎊</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        (function() {
            const t = localStorage.getItem('siteTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <style>
        body.result-page {
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 2rem;
            padding-top: 4rem;
        }

        .loading-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 3.5rem 2.5rem;
            max-width: 460px;
            width: 100%;
            text-align: center;
            animation: fadeUp 0.5s ease;
        }

        .result-book {
            max-width: 860px;
            width: 100%;
        }

        .result-wrapper {
            display: grid;
            grid-template-columns: 1fr 0fr;
            width: 100%;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 24px;
            overflow: hidden;
            animation: fadeUp 0.5s ease;
            transition: grid-template-columns 0.6s cubic-bezier(0.4, 0, 0.2, 1),
                        box-shadow 0.4s ease;
        }

        .result-wrapper.open {
            grid-template-columns: 1fr 1fr;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        /* ── Left ── */
        .result-left {
            padding: 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            border-right: 1px solid transparent;
            transition: border-color 0.4s ease;
            min-width: 0;
        }

        .result-wrapper.open .result-left {
            border-right-color: var(--border);
        }

        .result-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--pink);
            border: 1px solid var(--pink);
            border-radius: 50px;
            padding: 0.3rem 0.8rem;
            width: fit-content;
            margin-bottom: 1.5rem;
        }

        .result-left h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            line-height: 1.3;
            color: var(--text);
        }

        .result-left h1 .approved {
            color: var(--pink);
            font-style: italic;
        }

        .result-left-bottom {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            margin-top: 1.5rem;
        }

        .result-left-bottom .btn {
            text-align: center;
            width: 100%;
        }

        .btn-sched { color: #fff; }
        .btn-sched.active { background: var(--dark-pink); }

        .already-scheduled-note {
            background: rgba(244, 167, 185, 0.08);
            border: 1px solid rgba(244, 167, 185, 0.3);
            border-radius: 12px;
            padding: 0.8rem 1rem;
            font-size: 0.8rem;
            color: var(--pink);
            line-height: 1.6;
            text-align: center;
        }

        .already-scheduled-note strong {
            display: block;
            font-family: 'Playfair Display', serif;
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
        }

        .scheduled-date-display {
            cursor: pointer;
            transition: opacity 0.2s;
            padding: 0.3rem 0.5rem;
            border-radius: 6px;
        }

        .scheduled-date-display:hover {
            opacity: 0.7;
            background: rgba(244, 167, 185, 0.1);
        }

        .scheduled-date-display::after {
            content: ' ✏️';
            font-size: 0.65rem;
        }

        .scheduled-edit-mode {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .scheduled-edit-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
        }

        .scheduled-edit-inputs input {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid rgba(244, 167, 185, 0.5);
            border-radius: 8px;
            padding: 0.5rem 0.6rem;
            color: var(--text);
            font-size: 0.75rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .scheduled-edit-inputs input:focus { border-color: var(--pink); }

        .scheduled-edit-buttons {
            display: flex;
            gap: 0.5rem;
            font-size: 0.75rem;
        }

        .scheduled-edit-buttons button {
            flex: 1;
            padding: 0.4rem;
            border: none;
            border-radius: 6px;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
        }

        .scheduled-edit-save { background: var(--pink); color: #fff; }
        .scheduled-edit-save:hover { opacity: 0.9; }
        .scheduled-edit-cancel { background: transparent; border: 1px solid var(--border) !important; color: var(--muted); }
        .scheduled-edit-cancel:hover { color: var(--pink); border-color: var(--pink) !important; }

        .result-theme-btn {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 0.28rem 0.7rem;
            font-size: 0.72rem;
            font-family: 'DM Sans', sans-serif;
            color: var(--muted);
            cursor: pointer;
            transition: color 0.2s, border-color 0.2s;
        }

        .result-theme-btn:hover { color: var(--pink); border-color: var(--pink); }

        /* ── Right ── */
        .result-right {
            background: var(--bg);
            display: flex;
            flex-direction: column;
            width: 0;
            overflow: hidden;
            opacity: 0;
            padding: 0;
            transform-origin: left center;
            transform: rotateY(-25deg) scaleX(0.85);
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1),
                        opacity 0.5s ease 0.15s,
                        transform 0.6s cubic-bezier(0.4, 0, 0.2, 1),
                        padding 0.5s ease;
        }

        .result-right.open {
            width: 100%;
            opacity: 1;
            padding: 3.5rem 3rem;
            transform: rotateY(0deg) scaleX(1);
        }

        .result-right-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-shrink: 0;
        }

        .result-right-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
        }

        /* no flex:1 — natural content height */
        .scheduler-box {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        .scheduler-heading {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            color: var(--text);
        }

        .scheduler-sub {
            font-size: 0.82rem;
            color: var(--muted);
            margin-top: 0.2rem;
        }

        .datetime-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.8rem;
        }

        .dt-field {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .dt-field label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--pink);
            font-weight: 500;
        }

        .dt-field input {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.7rem 0.9rem;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
            cursor: pointer;
        }

        .dt-field input:focus { border-color: var(--pink); }

        .not-sure-btn {
            background: transparent;
            border: none;
            color: var(--muted);
            font-size: 0.78rem;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            text-decoration: underline;
            text-underline-offset: 3px;
            padding: 0;
            transition: color 0.2s;
            text-align: left;
        }

        .not-sure-btn:hover { color: var(--pink); }

        /* no flex:1 — natural content height */
        .not-sure-box {
            display: none;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1rem;
        }

        .not-sure-box.show { display: flex; }

        /* no flex:1 — natural content height */
        .confirmed-box {
            display: none;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1rem;
        }

        .confirmed-box.show { display: flex; }

        .confirmed-emoji {
            font-size: 3rem;
            line-height: 1;
            animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes popIn {
            0%   { transform: scale(0); opacity: 0; }
            70%  { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }

        .confirmed-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            color: var(--text);
            line-height: 1.3;
        }

        .confirmed-funny {
            font-size: 0.8rem;
            color: var(--muted);
            font-style: italic;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.6rem 1rem;
            line-height: 1.5;
            width: 100%;
        }

        .confirmed-date-display {
            background: var(--input-bg);
            border: 1px solid var(--pink);
            border-radius: 12px;
            padding: 0.8rem 1.5rem;
            font-size: 0.88rem;
            color: var(--pink);
            font-weight: 500;
            width: 100%;
        }

        .confirmed-sub {
            font-size: 0.8rem;
            color: var(--muted);
            font-style: italic;
            line-height: 1.6;
        }

        .confirmed-resched {
            font-size: 0.78rem;
            color: var(--muted);
            text-decoration: none;
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 0.4rem 1rem;
            transition: color 0.2s, border-color 0.2s;
        }

        .confirmed-resched:hover { color: var(--pink); border-color: var(--pink); }

        /* margin-top fixed — not auto */
        .result-compat-section {
            margin-top: 1.5rem;
            padding-top: 1.2rem;
            border-top: 1px solid var(--border);
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .result-divider-label {
            font-size: 0.72rem;
            color: var(--muted);
            text-align: center;
            margin-bottom: 0.3rem;
        }

        .compat-optional-btn {
            display: block;
            text-align: center;
            color: var(--muted);
            font-size: 0.82rem;
            text-decoration: none;
            padding: 0.4rem;
            border-radius: 8px;
            transition: color 0.2s;
        }

        .compat-optional-btn:hover { color: var(--pink); }

        .download-btn {
            display: block;
            text-align: center;
            font-size: 0.82rem;
            text-decoration: none;
            padding: 0.5rem;
            border-radius: 8px;
            color: var(--pink);
            border: 1px solid rgba(244, 167, 185, 0.3);
            transition: background 0.2s, border-color 0.2s;
        }

        .download-btn:hover {
            background: rgba(244, 167, 185, 0.08);
            border-color: var(--pink);
        }

        /* ── Message section ── */
        .message-section {
            margin-top: 1.8rem;
            padding-top: 1.8rem;
            border-top: 1px solid rgba(244, 167, 185, 0.2);
            width: 100%;
        }

        .message-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            display: block;
            margin-bottom: 0.8rem;
        }

        .message-textarea {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.8rem;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            resize: vertical;
            min-height: 80px;
            outline: none;
            transition: border-color 0.2s;
            margin-bottom: 0.8rem;
        }

        .message-textarea::placeholder { color: var(--muted); }
        .message-textarea:focus { border-color: var(--pink); }

        .message-button {
            width: 100%;
            background: var(--pink);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.7rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .message-button:hover { opacity: 0.9; }
        .message-button:disabled { opacity: 0.5; cursor: not-allowed; }

        .message-sent {
            background: rgba(76, 175, 80, 0.1);
            border: 1px solid rgba(76, 175, 80, 0.3);
            border-radius: 10px;
            padding: 0.8rem;
            text-align: center;
            color: #4caf50;
            font-size: 0.85rem;
        }

        /* ── Mobile ── */
        @media (max-width: 680px) {
            body.result-page {
                padding: 1rem;
                padding-top: 2rem;
                align-items: flex-start;
                justify-content: flex-start;
            }

            .result-wrapper { grid-template-columns: 1fr; }
            .result-wrapper.open { grid-template-columns: 1fr; }

            .result-left {
                border-right: none !important;
                border-bottom: 1px solid var(--border);
                padding: 2rem 1.5rem;
            }

            .result-right {
                width: 100%;
                max-height: 0;
                transform: none;
                transition: max-height 0.6s cubic-bezier(0.4, 0, 0.2, 1),
                            opacity 0.4s ease 0.1s,
                            padding 0.5s ease;
            }

            .result-right.open {
                width: 100%;
                max-height: 9999px;
                opacity: 1;
                padding: 2rem 1.5rem;
                transform: none;
            }

            .datetime-grid { grid-template-columns: 1fr; }
            .scheduled-edit-inputs { grid-template-columns: 1fr; }
        }

        @media (max-width: 400px) {
            .result-left { padding: 1.5rem 1.2rem; }
        }
    </style>
</head>
<body class="result-page">

    <!-- Loading -->
    <div class="loading-card" id="loadingScreen">
        <div class="spinner"></div>
        <p class="loading-text">Processing results...</p>
    </div>

    <!-- Result -->
    <div class="result-book hidden" id="resultScreen">
    <div class="result-wrapper" id="resultWrapper">

        <!-- ── Left ── -->
        <div class="result-left">
            <div>
                <div class="result-tag">✦ official result</div>
                <h1>
                    Seems like we should go on a
                    <span class="approved">date.</span>
                </h1>
            </div>

            <div class="result-left-bottom" id="leftButtons">
                <?php if ($alreadyScheduled): ?>
                    <div class="already-scheduled-note">
                        <strong>date already set</strong>
                        <div class="scheduled-date-display" id="scheduledDateDisplay"
                             onclick="startEditingScheduledDate()">
                            <?= $displayScheduled ?>
                        </div>
                    </div>
                    <a href="view_response.php" class="btn btn-yes btn-sched active"
                       style="text-align:center; display:block; color:#fff; text-decoration:none;">
                        view details
                    </a>
                <?php else: ?>
                    <button class="btn btn-yes btn-sched" id="schedBtn" onclick="openScheduler()">
                        when are u free. sched a date 📅
                    </button>
                    <button class="btn btn-maybe" id="iplanBtn" onclick="openIPlan()">
                        i'll plan it, i'll let you know 🗓️
                    </button>
                <?php endif; ?>
                <a href="<?= $instagramLink ?>" target="_blank" class="btn btn-maybe">
                    send me a message instead 💌
                </a>
                <a href="download_view.php" target="_blank" class="download-btn">
                    📥 save a copy of your answers
                </a>
            </div>
        </div>

        <!-- ── Right ── -->
        <div class="result-right" id="resultRight">

            <div class="result-right-top">
                <span class="result-right-label" id="rightLabel">
                    <?= $alreadyScheduled ? "it's a date! 🎉" : "pick a date 🗓️" ?>
                </span>
                <button class="result-theme-btn" id="themeBtn" onclick="toggleTheme()">☀️ light</button>
            </div>

            <!-- Scheduler -->
            <div class="scheduler-box" id="schedulerBox"
                 style="<?= $alreadyScheduled ? 'display:none;' : '' ?>">
                <div>
                    <p class="scheduler-heading">when works for you?</p>
                    <p class="scheduler-sub">pick a date and time and i'll make it happen.</p>
                </div>

                <div class="datetime-grid">
                    <div class="dt-field">
                        <label>📅 date</label>
                        <input type="date" id="schedDate">
                    </div>
                    <div class="dt-field">
                        <label>🕐 time</label>
                        <input type="time" id="schedTime" step="900">
                    </div>
                </div>

                <p id="dateError" style="color:#e07a8a; font-size:0.8rem; display:none;">
                    ⚠️ pick both a date and time first!
                </p>

                <button class="btn btn-yes" style="width:100%; color:#fff;" onclick="saveSchedule()">
                    confirm 
                </button>

                <button class="not-sure-btn" onclick="showNotSure()">
                    not sure about the date yet
                </button>

                <div class="result-compat-section">
                    <p class="result-divider-label">also —</p>
                    <a href="download_view.php" target="_blank" class="download-btn">
                        📥 save a copy of your answers
                    </a>
                    <a href="compatibility.php" class="compat-optional-btn">
                        check our compatibility 💘
                    </a>
                </div>
            </div>

            <!-- Not sure state -->
            <div class="not-sure-box" id="notSureBox">
                <div class="confirmed-emoji">🥹</div>
                <p class="confirmed-title">okay, no pressure!</p>
                <p class="confirmed-funny">
                    just message me when you're ready.<br>
                    i'll be here. probably waiting. 😭
                </p>
                <a href="<?= $instagramLink ?>" target="_blank"
                   class="btn btn-yes" style="color:#fff; width:100%; text-align:center;">
                    message me on instagram 💌
                </a>
                <button class="not-sure-btn" onclick="showSchedulerAgain()">
                    wait actually i have a date in mind
                </button>
                <div class="result-compat-section">
                    <p class="result-divider-label">also —</p>
                    <a href="download_view.php" target="_blank" class="download-btn">
                        📥 save a copy of your answers
                    </a>
                    <a href="compatibility.php" class="compat-optional-btn">
                        check our compatibility 💘
                    </a>
                </div>
            </div>

            <!-- I plan state -->
            <div class="not-sure-box" id="iPlanBox">
                <div class="confirmed-emoji">🗓️</div>
                <p class="confirmed-title">i got this 🙌</p>
                <p class="confirmed-funny">
                    i'll plan everything — just message me so we can check
                    if the time works for both of us. 
                </p>
                <a href="<?= $instagramLink ?>" target="_blank"
                   class="btn btn-yes" style="color:#fff; width:100%; text-align:center;">
                    message me on instagram 💌
                </a>
                <button class="not-sure-btn" onclick="showSchedulerFromIPlan()">
                    actually, i want to pick the date myself
                </button>
                <div class="result-compat-section">
                    <p class="result-divider-label">also —</p>
                    <a href="download_view.php" target="_blank" class="download-btn">
                        📥 save a copy of your answers
                    </a>
                    <a href="compatibility.php" class="compat-optional-btn">
                        check our compatibility 💘
                    </a>
                </div>
            </div>

            <!-- Confirmed state -->
            <div class="confirmed-box <?= $alreadyScheduled ? 'show' : '' ?>" id="confirmedBox">
                <div class="confirmed-emoji">🎉</div>
                <p class="confirmed-title">it's a date!</p>
                <div class="confirmed-date-display" id="confirmedDateDisplay">
                    <?= $alreadyScheduled ? $displayScheduled : '—' ?>
                </div>
                <p class="confirmed-funny" id="confirmedFunny">
                    <?= $alreadyScheduled ? 'sana di ka paasa 🙏' : '' ?>
                </p>
                <p class="confirmed-sub">
                    i'll be looking forward to it.<br>
                    see you soon, <?= $name ?> 🌸
                </p>
                <a href="<?= $instagramLink ?>" target="_blank" class="confirmed-resched">
                    message me on instagram if you need to resched 💌
                </a>

                <div class="message-section" id="messageSection">
                    <label class="message-label">💌 leave a message</label>
                    <textarea class="message-textarea" id="messageText"
                        placeholder="hey! anything you want to tell me before we meet?"></textarea>
                    <button class="message-button" id="messageSendBtn" onclick="sendMessage()">
                        send message 💌
                    </button>
                    <div class="message-sent hidden" id="messageSentConfirm">
                        ✓ message sent! i'll see it soon 🌸
                    </div>
                </div>

                <div class="result-compat-section">
                    <p class="result-divider-label">also —</p>
                    <a href="download_view.php" target="_blank" class="download-btn">
                        📥 save a copy of your answers
                    </a>
                    <a href="compatibility.php" class="compat-optional-btn">
                        check our compatibility 💘
                    </a>
                </div>
            </div>

        </div>
    </div>
    </div>

    <script>
    // ── Theme ──
    const themeBtn = document.getElementById('themeBtn');
    const savedTheme = localStorage.getItem('siteTheme') || 'dark';
    themeBtn.textContent = savedTheme === 'light' ? '🌙 dark' : '☀️ light';

    function toggleTheme() {
        const current = document.documentElement.getAttribute('data-theme');
        const next = current === 'light' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('siteTheme', next);
        themeBtn.textContent = next === 'light' ? '🌙 dark' : '☀️ light';
    }

    // ── Min date = today ──
    const schedDateEl = document.getElementById('schedDate');
    if (schedDateEl) {
        const today = new Date();
        const yyyy  = today.getFullYear();
        const mm    = String(today.getMonth() + 1).padStart(2, '0');
        const dd    = String(today.getDate()).padStart(2, '0');
        schedDateEl.min = `${yyyy}-${mm}-${dd}`;
    }

    // ── Already scheduled? Open right panel immediately ──
    const alreadyScheduled = <?= $alreadyScheduled ? 'true' : 'false' ?>;

    setTimeout(() => {
        document.getElementById('loadingScreen').classList.add('hidden');
        document.getElementById('resultScreen').classList.remove('hidden');

        if (alreadyScheduled) {
            const wrapper = document.getElementById('resultWrapper');
            const right   = document.getElementById('resultRight');
            wrapper.style.transition = 'none';
            right.style.transition   = 'none';
            wrapper.classList.add('open');
            right.classList.add('open');
            setTimeout(() => {
                wrapper.style.transition = '';
                right.style.transition   = '';
            }, 50);
        }
    }, 1000);

    // ── Book open ──
    let isOpen = <?= $alreadyScheduled ? 'true' : 'false' ?>;

    function openRightPanel() {
        if (isOpen) return;
        isOpen = true;
        const wrapper = document.getElementById('resultWrapper');
        const right   = document.getElementById('resultRight');
        wrapper.classList.add('open');
        setTimeout(() => { right.classList.add('open'); }, 80);
        setTimeout(() => {
            right.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 400);
    }

    function hideAllRightPanels() {
        document.getElementById('schedulerBox').style.display  = 'none';
        document.getElementById('notSureBox').classList.remove('show');
        document.getElementById('iPlanBox').classList.remove('show');
        document.getElementById('confirmedBox').classList.remove('show');
    }

    function openScheduler() {
        openRightPanel();
        const btn = document.getElementById('schedBtn');
        if (btn) { btn.classList.add('active'); btn.textContent = 'see you soon 🌸'; }
        hideAllRightPanels();
        document.getElementById('schedulerBox').style.display = 'flex';
        document.getElementById('schedulerBox').style.flexDirection = 'column';
        document.getElementById('rightLabel').textContent = 'pick a date 🗓️';
    }

    function openIPlan() {
        openRightPanel();
        const btn = document.getElementById('iplanBtn');
        if (btn) {
            btn.classList.add('active');
            btn.style.background = 'var(--dark-pink)';
            btn.style.color = '#fff';
            btn.style.border = 'none';
            btn.textContent = 'i got this 🙌';
        }
        hideAllRightPanels();
        document.getElementById('iPlanBox').classList.add('show');
        document.getElementById('rightLabel').textContent = 'i got this 🗓️';
    }

    function showNotSure() {
        hideAllRightPanels();
        document.getElementById('notSureBox').classList.add('show');
        document.getElementById('rightLabel').textContent = 'no pressure 🥹';
    }

    function showSchedulerAgain() {
        hideAllRightPanels();
        document.getElementById('schedulerBox').style.display = 'flex';
        document.getElementById('schedulerBox').style.flexDirection = 'column';
        document.getElementById('rightLabel').textContent = 'pick a date 🗓️';
    }

    function showSchedulerFromIPlan() {
        hideAllRightPanels();
        document.getElementById('schedulerBox').style.display = 'flex';
        document.getElementById('schedulerBox').style.flexDirection = 'column';
        document.getElementById('rightLabel').textContent = 'pick a date 🗓️';
    }

    // ── Funny messages ──
    const funnyMsgs = [
        "sana di ka paasa ;(",
        "pag umatras ka iiyak ako",
        "noted. di ko to kakalimutan",
        "screenshot ko to para may proof",
        "bawal na mag-back out",
        "sure ka na ba sa desisyon mo",
    ];

    // ── Save schedule ──
    function saveSchedule() {
        const date  = document.getElementById('schedDate').value;
        const time  = document.getElementById('schedTime').value;
        const error = document.getElementById('dateError');

        if (!date || !time) {
            error.style.display = 'block';
            return;
        }

        error.style.display = 'none';

        const combined = date + ' ' + time;
        const dateObj  = new Date(date + 'T' + time);

        const displayDate = dateObj.toLocaleDateString('en-PH', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
        const displayTime = dateObj.toLocaleTimeString('en-PH', {
            hour: '2-digit', minute: '2-digit'
        });

        fetch('save_schedule.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:    'id=<?= $id ?>&date=' + encodeURIComponent(combined)
        }).then(() => {
            const funny = funnyMsgs[Math.floor(Math.random() * funnyMsgs.length)];

            hideAllRightPanels();
            document.getElementById('confirmedBox').classList.add('show');
            document.getElementById('confirmedDateDisplay').textContent = displayDate + ' · ' + displayTime;
            document.getElementById('confirmedFunny').textContent       = funny;
            document.getElementById('rightLabel').textContent           = "it's a date! 🎉";

            document.getElementById('leftButtons').innerHTML = `
                <div class="already-scheduled-note">
                    <strong>date already set 🌸</strong>
                    ${displayDate} · ${displayTime}
                </div>
                <a href="view_response.php" class="btn btn-yes btn-sched active"
                   style="text-align:center; display:block; color:#fff; text-decoration:none;">
                    view details
                </a>
                <a href="<?= $instagramLink ?>" target="_blank" class="btn btn-maybe"
                   style="text-align:center; display:block;">
                    send me a message instead 💌
                </a>
                <a href="download_view.php" target="_blank" class="download-btn">
                    📥 save a copy of your answers
                </a>
            `;
        });
    }

    // ── Edit scheduled date ──
    const origScheduledDate = '<?= $scheduledDate ?>';

    function startEditingScheduledDate() {
        const display = document.getElementById('scheduledDateDisplay');
        if (!origScheduledDate) return;

        const dateObj = new Date(origScheduledDate + 'Z');
        const yyyy = dateObj.getUTCFullYear();
        const mm   = String(dateObj.getUTCMonth() + 1).padStart(2, '0');
        const dd   = String(dateObj.getUTCDate()).padStart(2, '0');
        const hh   = String(dateObj.getUTCHours()).padStart(2, '0');
        const min  = String(dateObj.getUTCMinutes()).padStart(2, '0');

        display.innerHTML = `
            <div class="scheduled-edit-mode">
                <div class="scheduled-edit-inputs">
                    <input type="date" id="editScheduledDate" value="${yyyy}-${mm}-${dd}">
                    <input type="time" id="editScheduledTime" value="${hh}:${min}" step="900">
                </div>
                <div class="scheduled-edit-buttons">
                    <button class="scheduled-edit-save" onclick="saveEditedScheduledDate()">save</button>
                    <button class="scheduled-edit-cancel" onclick="cancelEditScheduledDate()">cancel</button>
                </div>
            </div>
        `;

        const today = new Date();
        document.getElementById('editScheduledDate').min =
            `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
    }

    function cancelEditScheduledDate() {
        document.getElementById('scheduledDateDisplay').innerHTML =
            `<span><?= $displayScheduled ?></span>`;
    }

    function saveEditedScheduledDate() {
        const date = document.getElementById('editScheduledDate').value;
        const time = document.getElementById('editScheduledTime').value;

        if (!date || !time) { alert('pick both a date and time'); return; }

        const combined  = date + ' ' + time;
        const dateObj   = new Date(date + 'T' + time);
        const displayDate = dateObj.toLocaleDateString('en-PH', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
        const displayTime = dateObj.toLocaleTimeString('en-PH', {
            hour: '2-digit', minute: '2-digit'
        });

        fetch('save_schedule.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=<?= $id ?>&date=' + encodeURIComponent(combined)
        }).then(() => {
            document.getElementById('scheduledDateDisplay').innerHTML =
                `<span>${displayDate} · ${displayTime}</span>`;
            document.getElementById('confirmedDateDisplay').textContent =
                displayDate + ' · ' + displayTime;
        });
    }

    // ── Send message ──
    function sendMessage() {
        const message = document.getElementById('messageText').value.trim();
        const btn     = document.getElementById('messageSendBtn');
        const confirm = document.getElementById('messageSentConfirm');

        if (!message) { alert('write something before sending! 💌'); return; }

        btn.disabled    = true;
        btn.textContent = 'sending...';

        fetch('save_message.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=<?= $id ?>&message=' + encodeURIComponent(message)
        }).then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('messageText').value = '';
                confirm.classList.remove('hidden');
                btn.textContent = 'message sent! 💌';
                setTimeout(() => {
                    btn.disabled    = false;
                    btn.textContent = 'send another message 💌';
                }, 2000);
            } else {
                alert('failed to send. try again.');
                btn.disabled    = false;
                btn.textContent = 'send message 💌';
            }
        }).catch(() => {
            alert('error sending message.');
            btn.disabled    = false;
            btn.textContent = 'send message 💌';
        });
    }
    </script>
    <script src="js/main.js"></script>
</body>
</html>
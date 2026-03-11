<?php
session_start();
require 'includes/db.php';

if (empty($_SESSION['response_id'])) {
    header('Location: index.php');
    exit;
}

$id   = $_SESSION['response_id'];
$stmt = $pdo->prepare("SELECT * FROM responses WHERE id = ?");
$stmt->execute([$id]);
$row  = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header('Location: index.php');
    exit;
}

$name = htmlspecialchars($_SESSION['name']);

// Compatibility score
$compatStmt = $pdo->prepare("SELECT compatibility_score FROM responder_compatibility_answers WHERE response_id = ? ORDER BY id DESC LIMIT 1");
$compatStmt->execute([$id]);
$compatRow   = $compatStmt->fetch(PDO::FETCH_ASSOC);
$compatScore = $compatRow['compatibility_score'] ?? null;

// Handle edit save
$editSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_section'])) {
    $section = $_POST['edit_section'];

    if ($section === 'about') {
        $pdo->prepare("UPDATE responses SET name=?, age=?, city=?, food_drink=?, dealbreaker=? WHERE id=?")
            ->execute([
                trim($_POST['name'] ?? ''),
                $_POST['age'] ?? '',
                trim($_POST['city'] ?? ''),
                trim($_POST['food_drink'] ?? ''),
                trim($_POST['dealbreaker'] ?? ''),
                $id
            ]);
        $_SESSION['name'] = trim($_POST['name'] ?? $_SESSION['name']);
    }

    if ($section === 'reach') {
        $comm = isset($_POST['communication']) ? implode(', ', $_POST['communication']) : '';
        $pdo->prepare("UPDATE responses SET communication=?, best_time=? WHERE id=?")
            ->execute([$comm, $_POST['best_time'] ?? '', $id]);
    }

    if ($section === 'prefs') {
        $pdo->prepare("UPDATE responses SET date_type=?, spontaneity=?, energy=?, mood=?,
                        crowd=?, walking=?, convo_style=?, awkwardness=?, convo_difficulty=? WHERE id=?")
            ->execute([
                $_POST['date_type'] ?? '',
                $_POST['spontaneity'] ?? '',
                $_POST['energy'] ?? '',
                $_POST['mood'] ?? '',
                $_POST['crowd'] ?? '',
                $_POST['walking'] ?? '',
                $_POST['convo_style'] ?? '',
                $_POST['awkwardness'] ?? '',
                $_POST['convo_difficulty'] ?? '',
                $id
            ]);
    }

    if ($section === 'vibes') {
        $vibes = isset($_POST['vibes']) ? implode(', ', $_POST['vibes']) : '';
        $pdo->prepare("UPDATE responses SET vibes=?, custom_vibe=? WHERE id=?")
            ->execute([$vibes, trim($_POST['custom_vibe'] ?? ''), $id]);
    }

    // Reload row
    $stmt = $pdo->prepare("SELECT * FROM responses WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $editSuccess = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>your answers 💌</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        (function() {
            const t = localStorage.getItem('siteTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <style>
        body.view-response-page {
            min-height: 100vh;
            padding: 0;
            align-items: flex-start;
            display: flex;
            flex-direction: column;
            background: var(--bg);
        }

        .vr-wrapper {
            max-width: 680px;
            width: 100%;
            margin: 0 auto;
            padding: 3rem 2rem 6rem;
        }

        .vr-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 3rem;
        }

        .vr-back {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--muted);
            text-decoration: none;
            font-size: 0.82rem;
            transition: color 0.2s;
        }

        .vr-back:hover { color: var(--pink); }

        .vr-theme-btn {
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

        .vr-theme-btn:hover {
            color: var(--pink);
            border-color: var(--pink);
        }

        .vr-header {
            margin-bottom: 2rem;
        }

        .vr-header-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--pink);
            border: 1px solid var(--pink);
            border-radius: 50px;
            padding: 0.25rem 0.75rem;
            margin-bottom: 1rem;
        }

        .vr-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.6rem, 4vw, 2.2rem);
            color: var(--text);
            line-height: 1.2;
            margin-bottom: 0.4rem;
        }

        .vr-header h1 span { color: var(--pink); font-style: italic; }
        .vr-header p { color: var(--muted); font-size: 0.83rem; }

        /* ── Banners ── */
        .vr-sched-banner {
            background: rgba(244, 167, 185, 0.07);
            border: 1px solid rgba(244, 167, 185, 0.3);
            border-radius: 14px;
            padding: 1rem 1.4rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .vr-sched-icon { font-size: 1.6rem; flex-shrink: 0; }

        .vr-sched-text { display: flex; flex-direction: column; gap: 0.15rem; }

        .vr-sched-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--pink);
            font-weight: 500;
        }

        .vr-sched-value { font-size: 0.9rem; color: var(--text); font-weight: 500; }

        .vr-compat-banner {
            background: rgba(109, 200, 138, 0.06);
            border: 1px solid rgba(109, 200, 138, 0.25);
            border-radius: 14px;
            padding: 1rem 1.4rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .vr-compat-score {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #6dc88a;
            line-height: 1;
            flex-shrink: 0;
        }

        .vr-compat-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #6dc88a;
            font-weight: 500;
        }

        .vr-compat-sub { font-size: 0.8rem; color: var(--muted); }

        /* ── Success toast ── */
        .edit-success {
            background: rgba(100, 200, 140, 0.1);
            border: 1px solid rgba(100, 200, 140, 0.4);
            border-radius: 10px;
            padding: 0.7rem 1.2rem;
            color: #6dc88a;
            font-size: 0.82rem;
            margin-bottom: 1.5rem;
            animation: fadeUp 0.3s ease;
        }

        /* ── Section card ── */
        .vr-section {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            margin-bottom: 1.2rem;
            overflow: hidden;
            transition: border-color 0.2s;
        }

        .vr-section:has(.edit-form:not(.hidden)) {
            border-color: var(--pink);
        }

        .vr-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.4rem;
            border-bottom: 1px solid var(--border);
        }

        .vr-section-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .vr-edit-btn {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 0.25rem 0.75rem;
            font-size: 0.72rem;
            font-family: 'DM Sans', sans-serif;
            color: var(--muted);
            cursor: pointer;
            transition: color 0.2s, border-color 0.2s;
        }

        .vr-edit-btn:hover {
            color: var(--pink);
            border-color: var(--pink);
        }

        .vr-edit-btn.cancel {
            border-color: #c87a7a44;
            color: #c87a7a;
        }

        .vr-edit-btn.cancel:hover {
            border-color: #c87a7a;
        }

        /* ── Answer rows ── */
        .vr-rows {
            padding: 0.4rem 0;
        }

        .vr-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 1rem;
            padding: 0.75rem 1.4rem;
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }

        .vr-row:last-child { border-bottom: none; }
        .vr-row:hover { background: var(--input-bg); }

        .vr-question {
            font-size: 0.72rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding-top: 0.1rem;
            line-height: 1.4;
        }

        .vr-answer { font-size: 0.88rem; color: var(--text); line-height: 1.5; }
        .vr-answer.empty { color: var(--border); font-style: italic; }

        .vr-tags { display: flex; flex-wrap: wrap; gap: 0.35rem; }

        .vr-tag {
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 0.2rem 0.65rem;
            font-size: 0.75rem;
            color: var(--text);
        }

        /* ── Edit form inside section ── */
        .edit-form {
            padding: 1.2rem 1.4rem;
            border-top: 1px solid var(--border);
            background: var(--input-bg);
            display: flex;
            flex-direction: column;
            gap: 1rem;
            animation: fadeUp 0.25s ease;
        }

        .edit-form.hidden { display: none; }

        .edit-form .form-group {
            margin-bottom: 0;
        }

        .edit-form .form-group label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: var(--pink);
            font-weight: 500;
            margin-bottom: 0.4rem;
        }

        .edit-form input[type="text"],
        .edit-form select {
            width: 100%;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.88rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .edit-form input[type="text"]:focus,
        .edit-form select:focus {
            border-color: var(--pink);
        }

        .edit-form .checkbox-group,
        .edit-form .radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }

        .edit-form .check-item,
        .edit-form .radio-item {
            background: var(--card);
            border: 1.5px solid var(--border);
            border-radius: 50px;
            padding: 0.3rem 0.75rem;
            font-size: 0.78rem;
            cursor: pointer;
            transition: border-color 0.2s;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .edit-form .check-item:hover,
        .edit-form .radio-item:hover {
            border-color: var(--pink);
        }

        .edit-form-actions {
            display: flex;
            gap: 0.6rem;
            margin-top: 0.3rem;
        }

        .edit-save-btn {
            background: var(--pink);
            border: none;
            border-radius: 50px;
            padding: 0.5rem 1.2rem;
            color: #1a0a10;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.82rem;
            font-weight: 500;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .edit-save-btn:hover { opacity: 0.85; }

        .edit-cancel-btn {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 0.5rem 1.2rem;
            color: var(--muted);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.82rem;
            cursor: pointer;
            transition: color 0.2s, border-color 0.2s;
        }

        .edit-cancel-btn:hover {
            color: #c87a7a;
            border-color: #c87a7a44;
        }

        /* ── Bottom actions ── */
        .vr-actions {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            margin-top: 2rem;
        }

        .vr-actions .btn { text-align: center; color: #fff; }
        .vr-actions .btn-maybe { color: var(--pink); }

        @media (max-width: 480px) {
            .vr-wrapper { padding: 2rem 1.2rem 5rem; }
            .vr-row { grid-template-columns: 1fr; gap: 0.25rem; }
            .vr-question { font-size: 0.7rem; }
        }
    </style>
</head>
<body class="view-response-page">
<div class="vr-wrapper">

    <div class="vr-topbar">
        <a href="result.php" class="vr-back">← back to results</a>
        <button class="vr-theme-btn" id="themeBtn" onclick="toggleTheme()">☀️ light</button>
    </div>

    <div class="vr-header">
        <div class="vr-header-tag">✦ your answers</div>
        <h1>here's what you told me, <span><?= $name ?></span> 🌸</h1>
        <p>tap "edit" on any section to update your answers.</p>
    </div>

    <?php if ($editSuccess): ?>
    <div class="edit-success">✔ changes saved!</div>
    <?php endif; ?>

    <!-- Scheduled date -->
    <?php if (!empty($row['scheduled_date'])): ?>
    <div class="vr-sched-banner">
        <span class="vr-sched-icon">🗓️</span>
        <div class="vr-sched-text">
            <span class="vr-sched-label">date scheduled</span>
            <span class="vr-sched-value">
                <?php
                try {
                    $d = new DateTime($row['scheduled_date']);
                    echo $d->format('l, F j, Y · g:i A');
                } catch (Exception $e) {
                    echo htmlspecialchars($row['scheduled_date']);
                }
                ?>
            </span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Compatibility score -->
    <?php if ($compatScore !== null): ?>
    <div class="vr-compat-banner">
        <span class="vr-compat-score"><?= round($compatScore) ?>%</span>
        <div>
            <div class="vr-compat-label">compatibility score</div>
            <div class="vr-compat-sub">
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

    <!-- ══════════════════════════════════
         SECTION 1 — About you
    ══════════════════════════════════ -->
    <div class="vr-section">
        <div class="vr-section-header">
            <span class="vr-section-label">👤 about you</span>
            <button class="vr-edit-btn" onclick="toggleEdit('about')">✏️ edit</button>
        </div>

        <div class="vr-rows" id="view-about">
            <?php
            $aboutFields = [
                'name'        => 'Name / Nickname',
                'age'         => 'Age',
                'city'        => 'City / Location',
                'food_drink'  => 'Fave food & drink',
                'dealbreaker' => 'Dealbreaker',
            ];
            foreach ($aboutFields as $key => $label):
                $val = trim($row[$key] ?? '');
            ?>
            <div class="vr-row">
                <span class="vr-question"><?= $label ?></span>
                <span class="vr-answer <?= $val ? '' : 'empty' ?>"><?= $val ? htmlspecialchars($val) : '—' ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Edit form -->
        <form class="edit-form hidden" id="form-about" method="POST">
            <input type="hidden" name="edit_section" value="about">

            <div class="form-group">
                <label>Name / Nickname</label>
                <input type="text" name="name" value="<?= htmlspecialchars($row['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Age</label>
                <select name="age">
                    <?php foreach (['19','20','21','22','23+'] as $a): ?>
                    <option <?= ($row['age'] ?? '') === $a ? 'selected' : '' ?>><?= $a ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>City / Location</label>
                <input type="text" name="city" value="<?= htmlspecialchars($row['city'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Fave food & drink</label>
                <input type="text" name="food_drink" value="<?= htmlspecialchars($row['food_drink'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Dealbreaker</label>
                <div class="radio-group">
                    <?php foreach (['Bad music taste','Pineapple on pizza','Slow walkers',"None I'm chill"] as $d): ?>
                    <label class="radio-item">
                        <input type="radio" name="dealbreaker" value="<?= htmlspecialchars($d) ?>"
                            <?= ($row['dealbreaker'] ?? '') === $d ? 'checked' : '' ?>> <?= htmlspecialchars($d) ?>
                    </label>
                    <?php endforeach; ?>
                    <label class="radio-item">
                        <input type="radio" name="dealbreaker" value="__custom__"
                            id="edit-custom-radio"
                            <?= !in_array($row['dealbreaker'] ?? '', ['Bad music taste','Pineapple on pizza','Slow walkers',"None I'm chill"]) && !empty($row['dealbreaker']) ? 'checked' : '' ?>>
                        something else...
                    </label>
                </div>
                <input type="text" name="dealbreaker_custom" id="edit-custom-input"
                    placeholder="type your dealbreaker..."
                    value="<?= !in_array($row['dealbreaker'] ?? '', ['Bad music taste','Pineapple on pizza','Slow walkers',"None I'm chill"]) ? htmlspecialchars($row['dealbreaker'] ?? '') : '' ?>"
                    style="margin-top:0.6rem; display:<?= !in_array($row['dealbreaker'] ?? '', ['Bad music taste','Pineapple on pizza','Slow walkers',"None I'm chill"]) && !empty($row['dealbreaker']) ? 'block' : 'none' ?>">
            </div>

            <div class="edit-form-actions">
                <button type="submit" class="edit-save-btn" onclick="handleDealbreaker()">save changes</button>
                <button type="button" class="edit-cancel-btn" onclick="toggleEdit('about')">cancel</button>
            </div>
        </form>
    </div>

    <!-- ══════════════════════════════════
         SECTION 2 — How to reach you
    ══════════════════════════════════ -->
    <div class="vr-section">
        <div class="vr-section-header">
            <span class="vr-section-label">📲 how to reach you</span>
            <button class="vr-edit-btn" onclick="toggleEdit('reach')">✏️ edit</button>
        </div>

        <div class="vr-rows" id="view-reach">
            <?php
            $comms = array_filter(array_map('trim', explode(', ', $row['communication'] ?? '')));
            ?>
            <div class="vr-row">
                <span class="vr-question">Communication</span>
                <div class="vr-answer">
                    <?php if ($comms): ?>
                    <div class="vr-tags">
                        <?php foreach ($comms as $c): ?>
                        <span class="vr-tag"><?= htmlspecialchars($c) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?><span class="empty">—</span><?php endif; ?>
                </div>
            </div>
            <div class="vr-row">
                <span class="vr-question">Best time</span>
                <span class="vr-answer <?= $row['best_time'] ? '' : 'empty' ?>">
                    <?= $row['best_time'] ? htmlspecialchars($row['best_time']) : '—' ?>
                </span>
            </div>
        </div>

        <form class="edit-form hidden" id="form-reach" method="POST">
            <input type="hidden" name="edit_section" value="reach">

            <div class="form-group">
                <label>Preferred communication</label>
                <div class="checkbox-group">
                    <?php
                    $commOptions = ['Instagram','Messenger','Text','Twitter','Telegram','Shopee message',"I'll just pull up to your house",'liham','In our dreams'];
                    foreach ($commOptions as $c):
                    ?>
                    <label class="check-item">
                        <input type="checkbox" name="communication[]" value="<?= htmlspecialchars($c) ?>"
                            <?= in_array($c, $comms) ? 'checked' : '' ?>> <?= htmlspecialchars($c) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Best time for a date</label>
                <div class="radio-group">
                    <?php foreach (['Morning coffee','Afternoon hangout','Sunset','Night vibes'] as $t): ?>
                    <label class="radio-item">
                        <input type="radio" name="best_time" value="<?= htmlspecialchars($t) ?>"
                            <?= ($row['best_time'] ?? '') === $t ? 'checked' : '' ?>> <?= htmlspecialchars($t) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="edit-form-actions">
                <button type="submit" class="edit-save-btn">save changes</button>
                <button type="button" class="edit-cancel-btn" onclick="toggleEdit('reach')">cancel</button>
            </div>
        </form>
    </div>

    <!-- ══════════════════════════════════
         SECTION 3 — Date preferences
    ══════════════════════════════════ -->
    <div class="vr-section">
        <div class="vr-section-header">
            <span class="vr-section-label">🗓️ date preferences</span>
            <button class="vr-edit-btn" onclick="toggleEdit('prefs')">✏️ edit</button>
        </div>

        <div class="vr-rows" id="view-prefs">
            <?php
            $prefFields = [
                'date_type'        => 'Kind of date',
                'spontaneity'      => 'Spontaneity',
                'energy'           => 'Energy level',
                'mood'             => 'First date mood',
                'crowd'            => 'Crowd preference',
                'walking'          => 'Walking tolerance',
                'convo_style'      => 'Conversation style',
                'awkwardness'      => 'Awkwardness level',
                'convo_difficulty' => 'Convo difficulty',
            ];
            foreach ($prefFields as $key => $label):
                $val = trim($row[$key] ?? '');
            ?>
            <div class="vr-row">
                <span class="vr-question"><?= $label ?></span>
                <span class="vr-answer <?= $val ? '' : 'empty' ?>"><?= $val ? htmlspecialchars($val) : '—' ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <form class="edit-form hidden" id="form-prefs" method="POST">
            <input type="hidden" name="edit_section" value="prefs">
            <?php
            $prefOptions = [
                'date_type'        => ['label' => 'Kind of date',       'options' => ['Cozy indoor date','Outdoor adventure','Food trip','Nice view','Random spontaneous hang out','Surprise me']],
                'spontaneity'      => ['label' => 'Spontaneity level',  'options' => ['Yes please','A little structure',"Let's wing it",'Chaos']],
                'energy'           => ['label' => 'Energy level',       'options' => ['Chill','Medium','Active','Illegal activities (joke)']],
                'mood'             => ['label' => 'First date mood',    'options' => ['Relaxed','Playful','Adventurous','Slightly awkward but fun']],
                'crowd'            => ['label' => 'Crowd preference',   'options' => ['Quiet','Some people','Busy',"Doesn't matter"]],
                'walking'          => ['label' => 'Walking tolerance',  'options' => ['Minimal','Some walking','A lot','If we get lost we get lost']],
                'convo_style'      => ['label' => 'Conversation style', 'options' => ['Deep talks','Random funny stuff','Getting to know each other','Bahala na']],
                'awkwardness'      => ['label' => 'Awkwardness level',  'options' => ['Very','A little','Smooth',"I'll carry the conversation"]],
                'convo_difficulty' => ['label' => 'Convo difficulty',   'options' => ['Easy mode','Medium difficulty','Hard mode','Legendary boss fight']],
            ];
            foreach ($prefOptions as $name => $q):
            ?>
            <div class="form-group">
                <label><?= $q['label'] ?></label>
                <div class="radio-group">
                    <?php foreach ($q['options'] as $opt): ?>
                    <label class="radio-item">
                        <input type="radio" name="<?= $name ?>" value="<?= htmlspecialchars($opt) ?>"
                            <?= ($row[$name] ?? '') === $opt ? 'checked' : '' ?>> <?= htmlspecialchars($opt) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="edit-form-actions">
                <button type="submit" class="edit-save-btn">save changes</button>
                <button type="button" class="edit-cancel-btn" onclick="toggleEdit('prefs')">cancel</button>
            </div>
        </form>
    </div>

    <!-- ══════════════════════════════════
         SECTION 4 — Vibes
    ══════════════════════════════════ -->
    <div class="vr-section">
        <div class="vr-section-header">
            <span class="vr-section-label">✨ vibes & activities</span>
            <button class="vr-edit-btn" onclick="toggleEdit('vibes')">✏️ edit</button>
        </div>

        <div class="vr-rows" id="view-vibes">
            <?php
            $vibes = array_filter(array_map('trim', explode(', ', $row['vibes'] ?? '')));
            ?>
            <div class="vr-row">
                <span class="vr-question">Vibe check</span>
                <div class="vr-answer">
                    <?php if ($vibes): ?>
                    <div class="vr-tags">
                        <?php foreach ($vibes as $v): ?>
                        <span class="vr-tag"><?= htmlspecialchars($v) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?><span class="empty">—</span><?php endif; ?>
                </div>
            </div>
            <div class="vr-row">
                <span class="vr-question">Custom idea</span>
                <span class="vr-answer <?= !empty($row['custom_vibe']) ? '' : 'empty' ?>">
                    <?= !empty($row['custom_vibe']) ? htmlspecialchars($row['custom_vibe']) : '—' ?>
                </span>
            </div>
        </div>

        <form class="edit-form hidden" id="form-vibes" method="POST">
            <input type="hidden" name="edit_section" value="vibes">

            <div class="form-group">
                <label>Vibe check</label>
                <div class="checkbox-group">
                    <?php
                    $vibeOptions = ['Coffee shop','Night drive','Arcade / games','Watch a movie','Street food crawl','Stroll','Parking lot hangout','Beer and smoke','Nature','Dinner','Lunch','Creative activities'];
                    foreach ($vibeOptions as $v):
                    ?>
                    <label class="check-item">
                        <input type="checkbox" name="vibes[]" value="<?= htmlspecialchars($v) ?>"
                            <?= in_array($v, $vibes) ? 'checked' : '' ?>> <?= htmlspecialchars($v) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Your own idea</label>
                <input type="text" name="custom_vibe"
                    value="<?= htmlspecialchars($row['custom_vibe'] ?? '') ?>"
                    placeholder="suggest something...">
            </div>

            <div class="edit-form-actions">
                <button type="submit" class="edit-save-btn">save changes</button>
                <button type="button" class="edit-cancel-btn" onclick="toggleEdit('vibes')">cancel</button>
            </div>
        </form>
    </div>

    <!-- Bottom actions -->
    <div class="vr-actions">
        <a href="result.php" class="btn btn-yes">← back to results</a>
        <?php if ($compatScore === null): ?>
        <a href="compatibility.php" class="btn btn-maybe">check our compatibility 💘</a>
        <?php endif; ?>
    </div>

</div>

<script>
    // ── Theme ──
    const themeBtn = document.getElementById('themeBtn');
    const saved = localStorage.getItem('siteTheme') || 'dark';
    themeBtn.textContent = saved === 'light' ? '🌙 dark' : '☀️ light';

    function toggleTheme() {
        const current = document.documentElement.getAttribute('data-theme');
        const next = current === 'light' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('siteTheme', next);
        themeBtn.textContent = next === 'light' ? '🌙 dark' : '☀️ light';
    }

    // ── Toggle edit form ──
    function toggleEdit(section) {
        const form = document.getElementById('form-' + section);
        const isHidden = form.classList.contains('hidden');

        // close all others first
        ['about','reach','prefs','vibes'].forEach(s => {
            document.getElementById('form-' + s)?.classList.add('hidden');
            const btn = document.querySelector(`[onclick="toggleEdit('${s}')"]`);
            if (btn) { btn.textContent = '✏️ edit'; btn.classList.remove('cancel'); }
        });

        if (isHidden) {
            form.classList.remove('hidden');
            const btn = document.querySelector(`[onclick="toggleEdit('${section}')"]`);
            if (btn) { btn.textContent = '✕ cancel'; btn.classList.add('cancel'); }
            form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    // ── Custom dealbreaker toggle ──
    const editCustomRadio = document.getElementById('edit-custom-radio');
    const editCustomInput = document.getElementById('edit-custom-input');

    if (editCustomRadio) {
        document.querySelectorAll('input[name="dealbreaker"]').forEach(r => {
            r.addEventListener('change', function() {
                editCustomInput.style.display = this.value === '__custom__' ? 'block' : 'none';
                if (this.value === '__custom__') editCustomInput.focus();
            });
        });
    }

    // ── Handle dealbreaker custom value before submit ──
    function handleDealbreaker() {
        const checked = document.querySelector('input[name="dealbreaker"]:checked');
        if (checked && checked.value === '__custom__') {
            const custom = editCustomInput.value.trim();
            if (custom) {
                checked.value = custom;
            }
        }
    }
</script>
</body>
</html>
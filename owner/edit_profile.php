<?php
session_start();
if (empty($_SESSION['owner'])) {
    header('Location: login.php');
    exit;
}
require '../includes/db.php';

$username = $_SESSION['owner'];
$success  = false;
$error    = '';

$stmt  = $pdo->prepare("SELECT * FROM site_owners WHERE username = ?");
$stmt->execute([$username]);
$owner = $stmt->fetch(PDO::FETCH_ASSOC);

// Defaults
$defaultItems = [
    '😊 genuinely kind and thoughtful',
    '🎵 good music taste (subjective but trust me)',
    '🍜 will always share food',
    '🌙 good late night company',
    '🗣️ actually listens when you talk',
    '😂 kinda funny naman',
    '🚗 may wheels (important)',
];

$defaultExpectations = [
    'i pay attention to the small things you mention. if you say you\'ve been craving something, i\'ll remember it.',
    'i don\'t just buy gifts — i make them. cards, playlists, little things that took actual thought and time.',
    'if something needs fixing, i fix it. if you need help carrying something, i\'m already carrying it.',
    'i check in. not in an overwhelming way — just a "how was your day" kind of way that actually means it.',
    'i\'ll plan the date so you don\'t have to think about it. just show up.',
    'i\'m the kind of person who stays until the end — of the movie, the conversation, the night.',
    'i\'ll make sure you get home safe. always.',
];

$defaultSkills = [
    '✦ active listener',
    '✦ gift maker (not just buyer)',
    '✦ remembers what you said',
    '✦ drives',
    '✦ pays for food',
    '✦ actually funny',
    '✦ good playlist curator',
    '✦ will not ghost',
    '✦ opens doors',
    '✦ no weird expectations',
    '✦ night drive certified 🚗',
    '✦ makes the effort',
];

$savedItems        = $owner['profile_items']         ? json_decode($owner['profile_items'], true)         : $defaultItems;
$savedExpectations = $owner['resume_expectations']   ? json_decode($owner['resume_expectations'], true)   : $defaultExpectations;
$savedSkills       = $owner['resume_skills']         ? json_decode($owner['resume_skills'], true)         : $defaultSkills;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items        = array_values(array_filter(array_map('trim', $_POST['items'] ?? [])));
    $expectations = array_values(array_filter(array_map('trim', $_POST['expectations'] ?? [])));
    $skills       = array_values(array_filter(array_map('trim', $_POST['skills'] ?? [])));

    if (empty($items)) {
        $error = 'add at least one item in core qualifications!';
    } else {
        $promise = trim($_POST['promise_text'] ?? '');
        $whyyy   = trim($_POST['whyyy_text'] ?? '');

        $pdo->prepare("UPDATE site_owners SET
            profile_items = ?, promise_text = ?, whyyy_text = ?,
            resume_expectations = ?, resume_skills = ?
            WHERE username = ?")
            ->execute([
                json_encode($items),
                $promise,
                $whyyy,
                json_encode($expectations),
                json_encode($skills),
                $username
            ]);

        $stmt  = $pdo->prepare("SELECT * FROM site_owners WHERE username = ?");
        $stmt->execute([$username]);
        $owner             = $stmt->fetch(PDO::FETCH_ASSOC);
        $savedItems        = json_decode($owner['profile_items'], true);
        $savedExpectations = json_decode($owner['resume_expectations'], true);
        $savedSkills       = json_decode($owner['resume_skills'], true);
        $success           = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>edit my profile</title>
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
        .profile-editor {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .profile-editor-row {
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
        }

        .profile-editor-row input,
        .profile-editor-row textarea {
            flex: 1;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.65rem 1rem;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.88rem;
            outline: none;
            transition: border-color 0.2s;
            resize: none;
            line-height: 1.5;
        }

        .profile-editor-row input:focus,
        .profile-editor-row textarea:focus {
            border-color: var(--pink);
        }

        .remove-btn {
            background: transparent;
            border: 1px solid #c87a7a44;
            border-radius: 8px;
            color: #c87a7a;
            padding: 0.5rem 0.7rem;
            cursor: pointer;
            font-size: 0.82rem;
            transition: border-color 0.2s;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .remove-btn:hover { border-color: #c87a7a; }

        .add-item-btn {
            background: transparent;
            border: 1.5px dashed var(--border);
            border-radius: 10px;
            color: var(--muted);
            padding: 0.6rem 1rem;
            cursor: pointer;
            font-size: 0.82rem;
            font-family: 'DM Sans', sans-serif;
            width: 100%;
            transition: border-color 0.2s, color 0.2s;
            margin-top: 0.3rem;
        }

        .add-item-btn:hover {
            border-color: var(--pink);
            color: var(--pink);
        }

        .section-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--pink);
            font-weight: 500;
            margin-bottom: 0.4rem;
            display: block;
        }

        .section-desc {
            color: var(--muted);
            font-size: 0.78rem;
            margin-bottom: 0.8rem;
            line-height: 1.5;
        }

        .resume-section-divider {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--muted);
            margin: 2rem 0 1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .resume-section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }
    </style>
</head>
<body class="form-page">
<div class="form-container" style="max-width:640px;">

    <div class="owner-topbar">
        <a href="dashboard.php" style="color:var(--muted); text-decoration:none; font-size:0.85rem;">← back</a>
        <a href="logout.php" class="topbar-btn topbar-logout">logout</a>
    </div>

    <h1 style="margin-top:1rem;">edit my profile 🌸</h1>
    <p class="subtitle">this is your "resume" shown on the maybe page when your crush is deciding</p>

    <?php if ($success): ?>
    <div style="background:rgba(100,200,140,0.1); border:1px solid rgba(100,200,140,0.4);
                border-radius:10px; padding:0.85rem 1.2rem; color:#6dc88a; margin:1rem 0; font-size:0.9rem;">
        ✔ profile saved!
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div style="background:rgba(224,122,138,0.1); border:1px solid rgba(224,122,138,0.4);
                border-radius:10px; padding:0.85rem 1.2rem; color:#e07a8a; margin:1rem 0; font-size:0.9rem;">
        ⚠️ <?= $error ?>
    </div>
    <?php endif; ?>

    <form method="POST" id="profileForm">

        <!-- ── WHYYY text ── -->
        <div class="resume-section-divider">header text</div>

        <div class="form-group">
            <span class="section-label">text under "WHYYY 😭"</span>
            <input type="text" name="whyyy_text"
                value="<?= htmlspecialchars($owner['whyyy_text'] ?? 'okay okay let me make my case first...') ?>"
                placeholder="okay okay let me make my case first..."
                style="width:100%; background:var(--input-bg); border:1px solid var(--border);
                       border-radius:10px; padding:0.75rem 1rem; color:var(--text);
                       font-family:'DM Sans',sans-serif; font-size:0.9rem; outline:none;">
        </div>

        <!-- ── Core qualifications ── -->
        <div class="resume-section-divider">core qualifications</div>

        <div class="form-group">
            <span class="section-label">your selling points 😄</span>
            <p class="section-desc">
                these show as bullet points under "core qualifications". add emoji at the start!
            </p>
            <div class="profile-editor" id="itemsList">
                <?php foreach ($savedItems as $item): ?>
                <div class="profile-editor-row">
                    <input type="text" name="items[]"
                        value="<?= htmlspecialchars($item) ?>"
                        placeholder="add something about yourself...">
                    <button type="button" class="remove-btn" onclick="removeItem(this, 'itemsList')">✕</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="add-item-btn" onclick="addItem('itemsList', 'items[]', 'add something about yourself...')">
                + add another point
            </button>
        </div>

        <!-- ── What you can expect ── -->
        <div class="resume-section-divider">what they can actually expect</div>

        <div class="form-group">
            <span class="section-label">specific things you'd actually do 🌸</span>
            <p class="section-desc">
                this is the convincing part — be specific and genuine. "i remember the small things you mention", "i make gifts instead of just buying them", etc.
            </p>
            <div class="profile-editor" id="expectationsList">
                <?php foreach ($savedExpectations as $exp): ?>
                <div class="profile-editor-row">
                    <textarea name="expectations[]" rows="2"
                        placeholder="something specific you'd do..."><?= htmlspecialchars($exp) ?></textarea>
                    <button type="button" class="remove-btn" onclick="removeItem(this, 'expectationsList')">✕</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="add-item-btn" onclick="addTextarea('expectationsList')">
                + add another thing
            </button>
        </div>

        <!-- ── Skills ── -->
        <div class="resume-section-divider">skills & competencies</div>

        <div class="form-group">
            <span class="section-label">your tags / skills 🏷️</span>
            <p class="section-desc">
                these show as small pill tags. keep them short — 2 to 5 words each. tip: start with ✦ for the highlighted ones.
            </p>
            <div class="profile-editor" id="skillsList">
                <?php foreach ($savedSkills as $skill): ?>
                <div class="profile-editor-row">
                    <input type="text" name="skills[]"
                        value="<?= htmlspecialchars($skill) ?>"
                        placeholder="e.g. ✦ will not ghost">
                    <button type="button" class="remove-btn" onclick="removeItem(this, 'skillsList')">✕</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="add-item-btn" onclick="addItem('skillsList', 'skills[]', 'e.g. ✦ will not ghost')">
                + add skill
            </button>
        </div>

        <!-- ── Promise text ── -->
        <div class="resume-section-divider">closing line</div>

        <div class="form-group">
            <span class="section-label">your promise / closing line</span>
            <input type="text" name="promise_text"
                value="<?= htmlspecialchars($owner['promise_text'] ?? 'di ako masamang tao promise, go out with me please') ?>"
                placeholder="di ako masamang tao promise..."
                style="width:100%; background:var(--input-bg); border:1px solid var(--border);
                       border-radius:10px; padding:0.75rem 1rem; color:var(--text);
                       font-family:'DM Sans',sans-serif; font-size:0.9rem; outline:none;">
            <p style="color:var(--muted); font-size:0.75rem; margin-top:0.4rem;">
                shows in the handwritten-style box at the bottom of your resume
            </p>
        </div>

        <button type="submit" class="btn btn-yes" style="width:100%; margin-top:1.5rem; color:#fff;">
            save profile 🌸
        </button>
    </form>
</div>

<script>
function addItem(listId, fieldName, placeholder) {
    const list = document.getElementById(listId);
    const row  = document.createElement('div');
    row.className = 'profile-editor-row';
    row.innerHTML = `
        <input type="text" name="${fieldName}" placeholder="${placeholder}">
        <button type="button" class="remove-btn" onclick="removeItem(this, '${listId}')">✕</button>
    `;
    list.appendChild(row);
    row.querySelector('input').focus();
}

function addTextarea(listId) {
    const list = document.getElementById(listId);
    const row  = document.createElement('div');
    row.className = 'profile-editor-row';
    row.innerHTML = `
        <textarea name="expectations[]" rows="2"
            placeholder="something specific you'd do..."></textarea>
        <button type="button" class="remove-btn" onclick="removeItem(this, '${listId}')">✕</button>
    `;
    list.appendChild(row);
    row.querySelector('textarea').focus();
}

function removeItem(btn, listId) {
    const list = document.getElementById(listId);
    const rows = list.querySelectorAll('.profile-editor-row');
    if (rows.length <= 1) return;
    btn.closest('.profile-editor-row').remove();
}
</script>
</body>
</html>
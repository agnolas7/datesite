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

// Fetch current owner data
$stmt  = $pdo->prepare("SELECT * FROM site_owners WHERE username = ?");
$stmt->execute([$username]);
$owner = $stmt->fetch(PDO::FETCH_ASSOC);

// Default profile items if none saved yet
$defaultItems = [
    '😊 genuinely kind and thoughtful',
    '🎵 good music taste (subjective but trust me)',
    '🍜 will always share food',
    '🌙 good late night company',
    '🗣️ actually listens when you talk',
    '😂 kinda funny naman',
    '🚗 may wheels (important)',
];

$savedItems = $owner['profile_items']
    ? json_decode($owner['profile_items'], true)
    : $defaultItems;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Clean up items — remove empty ones
    $items = $_POST['items'] ?? [];
    $items = array_values(array_filter(array_map('trim', $items)));

    if (empty($items)) {
        $error = 'add at least one item!';
    } else {
        $promise = trim($_POST['promise_text'] ?? '');
        $whyyy   = trim($_POST['whyyy_text'] ?? '');

        $pdo->prepare("UPDATE site_owners SET profile_items = ?, promise_text = ?, whyyy_text = ? WHERE username = ?")
            ->execute([json_encode($items), $promise, $whyyy, $username]);

        // Reload
        $stmt  = $pdo->prepare("SELECT * FROM site_owners WHERE username = ?");
        $stmt->execute([$username]);
        $owner      = $stmt->fetch(PDO::FETCH_ASSOC);
        $savedItems = json_decode($owner['profile_items'], true);
        $success    = true;
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
            gap: 0.6rem;
            margin-bottom: 0.5rem;
        }

        .profile-editor-row {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .profile-editor-row input {
            flex: 1;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.65rem 1rem;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .profile-editor-row input:focus {
            border-color: var(--pink);
        }

        .remove-btn {
            background: transparent;
            border: 1px solid #c87a7a44;
            border-radius: 8px;
            color: #c87a7a;
            padding: 0.5rem 0.7rem;
            cursor: pointer;
            font-size: 0.85rem;
            transition: border-color 0.2s;
            flex-shrink: 0;
        }

        .remove-btn:hover {
            border-color: #c87a7a;
        }

        .add-item-btn {
            background: transparent;
            border: 1.5px dashed var(--border);
            border-radius: 10px;
            color: var(--muted);
            padding: 0.6rem 1rem;
            cursor: pointer;
            font-size: 0.85rem;
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
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--pink);
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }

        .preview-box {
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .preview-box h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            color: var(--muted);
            margin-bottom: 1rem;
            font-weight: 400;
        }

        .preview-item {
            padding: 0.5rem 0.8rem;
            background: var(--card);
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }

        .preview-promise {
            margin-top: 1rem;
            font-style: italic;
            color: var(--pink);
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="form-page">
<div class="form-container" style="max-width:620px;">

    <!-- Top bar -->
    <div class="owner-topbar">
        <a href="dashboard.php" style="color:var(--muted); text-decoration:none; font-size:0.85rem;">← back</a>
        <a href="logout.php" class="topbar-btn topbar-logout">logout</a>
    </div>

    <h1 style="margin-top:1rem;">edit my profile 🌸</h1>
    <p class="subtitle">this shows up on the "maybe" page when your crush is deciding</p>

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

        <!-- WHYYY subtext -->
        <div class="form-group" style="margin-top:1.5rem;">
            <span class="section-label">text under "WHYYY 😭"</span>
            <input type="text" name="whyyy_text"
                value="<?= htmlspecialchars($owner['whyyy_text'] ?? 'okay okay let me make my case first...') ?>"
                placeholder="okay okay let me make my case first..."
                style="width:100%; background:var(--input-bg); border:1px solid var(--border);
                       border-radius:10px; padding:0.75rem 1rem; color:var(--text);
                       font-family:'DM Sans',sans-serif; font-size:0.9rem; outline:none;">
        </div>

        <!-- Profile bullet points -->
        <div class="form-group">
            <span class="section-label">your selling points 😄</span>
            <p style="color:var(--muted); font-size:0.8rem; margin-bottom:0.8rem;">
                these show up as bullet points on the profile section. you can add emoji at the start!
            </p>

            <div class="profile-editor" id="itemsList">
                <?php foreach ($savedItems as $i => $item): ?>
                <div class="profile-editor-row">
                    <input type="text" name="items[]"
                        value="<?= htmlspecialchars($item) ?>"
                        placeholder="add something about yourself...">
                    <button type="button" class="remove-btn" onclick="removeItem(this)">✕</button>
                </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="add-item-btn" onclick="addItem()">
                + add another point
            </button>
        </div>

        <!-- Promise text -->
        <div class="form-group">
            <span class="section-label">your promise / closing line</span>
            <input type="text" name="promise_text"
                value="<?= htmlspecialchars($owner['promise_text'] ?? 'di ako masamang tao promise, go out with me please') ?>"
                placeholder="di ako masamang tao promise..."
                style="width:100%; background:var(--input-bg); border:1px solid var(--border);
                       border-radius:10px; padding:0.75rem 1rem; color:var(--text);
                       font-family:'DM Sans',sans-serif; font-size:0.9rem; outline:none;">
            <p style="color:var(--muted); font-size:0.78rem; margin-top:0.4rem;">
                this shows in italics below your bullet points
            </p>
        </div>

        <!-- Live preview -->
        <div class="preview-box" id="previewBox">
            <h3>preview 👀</h3>
            <div id="previewItems"></div>
            <p class="preview-promise" id="previewPromise"></p>
        </div>

        <button type="submit" class="btn btn-yes" style="width:100%; margin-top:1.5rem;">
            save profile 🌸
        </button>
    </form>
</div>

<script>
// ── Add / remove items ──
function addItem() {
    const list = document.getElementById('itemsList');
    const row  = document.createElement('div');
    row.className = 'profile-editor-row';
    row.innerHTML = `
        <input type="text" name="items[]" placeholder="add something about yourself...">
        <button type="button" class="remove-btn" onclick="removeItem(this)">✕</button>
    `;
    list.appendChild(row);
    row.querySelector('input').focus();
    updatePreview();
}

function removeItem(btn) {
    const rows = document.querySelectorAll('.profile-editor-row');
    if (rows.length <= 1) return; // keep at least one
    btn.closest('.profile-editor-row').remove();
    updatePreview();
}

// ── Live preview ──
function updatePreview() {
    const inputs  = document.querySelectorAll('input[name="items[]"]');
    const promise = document.querySelector('input[name="promise_text"]').value;
    const preview = document.getElementById('previewItems');

    preview.innerHTML = '';
    inputs.forEach(input => {
        if (input.value.trim()) {
            const div = document.createElement('div');
            div.className = 'preview-item';
            div.textContent = input.value;
            preview.appendChild(div);
        }
    });

    document.getElementById('previewPromise').textContent = promise
        ? `"${promise}" 🙏`
        : '';
}

// Listen for typing
document.addEventListener('input', updatePreview);
updatePreview(); // run on load
</script>
</body>
</html>
<?php
session_start();
if (empty($_SESSION['owner'])) {
    header('Location: login.php');
    exit;
}
require '../includes/db.php';
require '../includes/compatibility_questions.php';

$username = $_SESSION['owner'];
$success  = false;

$stmt  = $pdo->prepare("SELECT * FROM admin_compatibility_answers WHERE owner_username = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$username]);
$saved = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];
    foreach ($compatibility_questions as $name => $q) {
        if ($q['type'] === 'rank' || $q['type'] === 'checkbox') {
            $data[$name] = isset($_POST[$name]) ? implode(', ', $_POST[$name]) : '';
        } else {
            $data[$name] = $_POST[$name] ?? '';
        }
    }

    if ($saved) {
        $sets = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        $pdo->prepare("UPDATE admin_compatibility_answers SET $sets WHERE id = ?")
            ->execute(array_merge(array_values($data), [$saved['id']]));
    } else {
        $data['owner_username'] = $username;
        $cols         = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $pdo->prepare("INSERT INTO admin_compatibility_answers ($cols) VALUES ($placeholders)")
            ->execute(array_values($data));
    }

    $stmt  = $pdo->prepare("SELECT * FROM admin_compatibility_answers WHERE owner_username = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$username]);
    $saved   = $stmt->fetch(PDO::FETCH_ASSOC);
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>my compatibility answers</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        (function() {
            const t = localStorage.getItem('ownerTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <style>
        .rank-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.3rem;
        }

        .rank-option {
            position: relative;
            cursor: pointer;
        }

        .rank-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--input-bg);
            border: 1.5px solid var(--border);
            border-radius: 50px;
            padding: 0.4rem 0.9rem;
            font-size: 0.85rem;
            color: var(--text);
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            user-select: none;
        }

        .rank-label:hover { border-color: var(--pink); }

        .rank-badge {
            display: none;
            background: var(--pink);
            color: #1a0a10;
            font-size: 0.65rem;
            font-weight: 700;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .rank-option.selected .rank-label {
            border-color: var(--pink);
            background: rgba(244, 167, 185, 0.08);
            color: var(--pink);
        }

        .rank-option.selected .rank-badge { display: inline-flex; }

        .rank-option.maxed:not(.selected) .rank-label {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .rank-hint {
            font-size: 0.72rem;
            color: var(--muted);
            margin-top: 0.4rem;
            font-style: italic;
        }

        .rank-hint span { color: var(--pink); font-weight: 500; }
    </style>
</head>
<body class="form-page">
<div class="form-container" style="max-width:680px;">

    <a href="dashboard.php" style="color:var(--muted); text-decoration:none; font-size:0.85rem;">← back</a>
    <h1 style="margin-top:1rem;">my compatibility answers 💘</h1>
    <p class="subtitle">these are compared against your responders' answers</p>

    <?php if ($success): ?>
    <div style="background:rgba(100,200,140,0.1); border:1px solid rgba(100,200,140,0.4);
                border-radius:10px; padding:0.85rem 1.2rem; color:#6dc88a; margin:1.5rem 0; font-size:0.9rem;">
        ✔ saved!
    </div>
    <?php endif; ?>

    <form method="POST" id="compatForm">
        <?php foreach ($compatibility_questions as $name => $q):
            $savedVal = $saved[$name] ?? '';
            $savedArr = array_filter(array_map('trim', explode(', ', $savedVal)));
        ?>
        <div class="form-group" data-name="<?= $name ?>" data-type="<?= $q['type'] ?>"
             <?php if ($q['type'] === 'rank'): ?>
             data-max="<?= $q['max'] ?>" data-min="<?= $q['min'] ?>"
             <?php endif; ?>>
            <label><?= htmlspecialchars($q['label']) ?></label>

            <?php if ($q['type'] === 'rank'): ?>
                <p class="rank-hint">
                    pick at least <span><?= $q['min'] ?></span>,
                    up to <span><?= $q['max'] ?></span> — tap in order of preference
                </p>
                <div class="rank-group" id="rank-<?= $name ?>">
                    <?php foreach ($q['options'] as $opt):
                        $safe    = htmlspecialchars($opt);
                        $rankPos = array_search($opt, array_values($savedArr));
                    ?>
                    <label class="rank-option <?= $rankPos !== false ? 'selected' : '' ?>"
                           data-value="<?= $safe ?>">
                        <span class="rank-label">
                            <span class="rank-badge"><?= $rankPos !== false ? $rankPos + 1 : '' ?></span>
                            <?= $safe ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <div id="hidden-<?= $name ?>">
                    <?php foreach ($savedArr as $val): ?>
                    <input type="hidden" name="<?= $name ?>[]" value="<?= htmlspecialchars($val) ?>">
                    <?php endforeach; ?>
                </div>

            <?php elseif ($q['type'] === 'checkbox'): ?>
                <div class="checkbox-group">
                    <?php foreach ($q['options'] as $opt):
                        $safe    = htmlspecialchars($opt);
                        $checked = in_array($opt, $savedArr) ? 'checked' : '';
                    ?>
                    <label class="check-item">
                        <input type="checkbox" name="<?= $name ?>[]" value="<?= $safe ?>" <?= $checked ?>> <?= $safe ?>
                    </label>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <div class="radio-group">
                    <?php foreach ($q['options'] as $opt):
                        $safe    = htmlspecialchars($opt);
                        $checked = in_array($opt, $savedArr) ? 'checked' : '';
                    ?>
                    <label class="radio-item">
                        <input type="radio" name="<?= $name ?>" value="<?= $safe ?>" <?= $checked ?>> <?= $safe ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-yes" style="width:100%; margin-top:1rem; color:#fff;">
            save 💾
        </button>
    </form>
</div>

<script>
// ── Ranking logic ──
document.querySelectorAll('.form-group[data-type="rank"]').forEach(group => {
    const name    = group.dataset.name;
    const max     = parseInt(group.dataset.max);
    const min     = parseInt(group.dataset.min);
    const options = group.querySelectorAll('.rank-option');

    // restore saved order from hidden inputs
    let selected = Array.from(
        document.querySelectorAll(`#hidden-${name} input`)
    ).map(i => i.value).filter(Boolean);

    function updateUI() {
        const hiddenContainer = document.getElementById('hidden-' + name);
        hiddenContainer.innerHTML = '';

        options.forEach(opt => {
            const val   = opt.dataset.value;
            const idx   = selected.indexOf(val);
            const badge = opt.querySelector('.rank-badge');

            if (idx >= 0) {
                opt.classList.add('selected');
                opt.classList.remove('maxed');
                badge.textContent = idx + 1;
            } else {
                opt.classList.remove('selected');
                badge.textContent = '';
                opt.classList.toggle('maxed', selected.length >= max);
            }
        });

        selected.forEach(val => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = name + '[]';
            inp.value = val;
            hiddenContainer.appendChild(inp);
        });

        const hint = group.querySelector('.rank-hint');
        if (hint) {
            if (selected.length === 0) {
                hint.innerHTML = `pick at least <span>${min}</span>, up to <span>${max}</span> — tap in order of preference`;
            } else {
                hint.innerHTML = `<span>${selected.length}</span> selected${selected.length < max ? ` — you can pick ${max - selected.length} more` : ' — max reached'}`;
            }
        }
    }

    options.forEach(opt => {
        opt.addEventListener('click', () => {
            const val = opt.dataset.value;
            const idx = selected.indexOf(val);
            if (idx >= 0) {
                selected.splice(idx, 1);
            } else if (selected.length < max) {
                selected.push(val);
            }
            updateUI();
        });
    });

    updateUI();
});
</script>
</body>
</html>
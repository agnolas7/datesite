<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require '../includes/db.php';
require '../includes/compatibility_questions.php';

$saved   = null;
$success = false;

$stmt  = $pdo->query("SELECT * FROM admin_compatibility_answers ORDER BY id DESC LIMIT 1");
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
        $stmt = $pdo->prepare("UPDATE admin_compatibility_answers SET $sets WHERE id = ?");
        $stmt->execute(array_merge(array_values($data), [$saved['id']]));
    } else {
        $cols         = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt         = $pdo->prepare("INSERT INTO admin_compatibility_answers ($cols) VALUES ($placeholders)");
        $stmt->execute(array_values($data));
    }

    $stmt  = $pdo->query("SELECT * FROM admin_compatibility_answers ORDER BY id DESC LIMIT 1");
    $saved = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        (function() {
            const t = localStorage.getItem('adminTheme') || 'dark';
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

        .rank-option { cursor: pointer; }

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

    <a href="dashboard.php" style="color:var(--muted); text-decoration:none; font-size:0.85rem;">← back to dashboard</a>
    <h1 style="margin-top:1rem;">my compatibility answers 💘</h1>
    <p class="subtitle">these are used to calculate compatibility with responders</p>

    <?php if ($success): ?>
    <div class="validation-banner" style="background:rgba(100,200,140,0.1); border-color:rgba(100,200,140,0.4); color:#6dc88a; margin-bottom:1.5rem;">
        <span class="validation-icon">✔</span> answers saved successfully!
    </div>
    <?php endif; ?>

    <form method="POST" id="compatForm">
        <?php foreach ($compatibility_questions as $name => $q):
            $savedVal = $saved[$name] ?? '';
            $savedArr = array_filter(array_map('trim', explode(', ', $savedVal)));
        ?>
        <div class="form-group"
             data-name="<?= $name ?>"
             data-type="<?= $q['type'] ?>"
             data-min="<?= $q['min'] ?? 1 ?>">

            <label><?= htmlspecialchars($q['label']) ?></label>

            <?php if ($q['type'] === 'rank'): ?>
                <p class="rank-hint" id="hint-<?= $name ?>">
                    rank as many as you like — tap in order of preference.
                    skip the ones you don't vibe with.
                    minimum <span><?= $q['min'] ?></span>.
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
            save my answers 💾
        </button>
    </form>
</div>

<script src="../js/main.js"></script>
<script>
document.querySelectorAll('.form-group[data-type="rank"]').forEach(group => {
    const name    = group.dataset.name;
    const min     = parseInt(group.dataset.min);
    const options = group.querySelectorAll('.rank-option');

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
                badge.textContent = idx + 1;
            } else {
                opt.classList.remove('selected');
                badge.textContent = '';
            }
        });

        selected.forEach(val => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = name + '[]';
            inp.value = val;
            hiddenContainer.appendChild(inp);
        });

        const hint = document.getElementById('hint-' + name);
        if (hint) {
            if (selected.length === 0) {
                hint.innerHTML = `rank as many as you like — tap in order of preference. skip what you don't vibe with. minimum <span>${min}</span>.`;
            } else {
                hint.innerHTML = `<span>${selected.length}</span> ranked — tap again to deselect`;
            }
        }
    }

    options.forEach(opt => {
        opt.addEventListener('click', () => {
            const val = opt.dataset.value;
            const idx = selected.indexOf(val);
            if (idx >= 0) {
                selected.splice(idx, 1);
            } else {
                selected.push(val); // no max — just keep adding
            }
            updateUI();
        });
    });

    updateUI();
});
</script>
</body>
</html>
<?php
session_start();
if (empty($_SESSION['response_id'])) {
    header('Location: index.php');
    exit;
}
require 'includes/compatibility_questions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>compatibility check 💘</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        (function() {
            const t = localStorage.getItem('siteTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <style>
        /* ── Rank picker ── */
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

        .rank-option input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
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

        .rank-label:hover {
            border-color: var(--pink);
        }

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

        .rank-option.selected .rank-badge {
            display: inline-flex;
        }

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

        .rank-hint span {
            color: var(--pink);
            font-weight: 500;
        }
    </style>
</head>
<body class="form-page">
<div class="form-container">
    <h1>compatibility check 💘</h1>
    <p class="subtitle">answer honestly — this is for science</p>

    <div class="validation-banner hidden" id="validationBanner">
        <span class="validation-icon">⚠️</span>
        <span id="validationMsg">please answer all questions before continuing!</span>
    </div>

    <form id="compatForm" action="save_compatibility.php" method="POST" novalidate>

        <?php foreach ($compatibility_questions as $name => $q): ?>
        <div class="form-group compat-group" id="group-<?= $name ?>"
             data-name="<?= $name ?>"
             data-type="<?= $q['type'] ?>"
             <?php if ($q['type'] === 'rank'): ?>
             data-max="<?= $q['max'] ?>"
             data-min="<?= $q['min'] ?>"
             <?php endif; ?>>

            <label>
                <?= htmlspecialchars($q['label']) ?>
                <span class="required-star">*</span>
            </label>

            <?php if ($q['type'] === 'rank'): ?>
                <p class="rank-hint">
                    pick at least <span><?= $q['min'] ?></span>, up to <span><?= $q['max'] ?></span> —
                    tap in order of preference
                </p>
                <div class="rank-group" id="rank-<?= $name ?>">
                    <?php foreach ($q['options'] as $opt):
                        $safe = htmlspecialchars($opt); ?>
                    <label class="rank-option" data-value="<?= $safe ?>">
                        <input type="hidden" name="<?= $name ?>_rank[]" value="" disabled>
                        <span class="rank-label">
                            <span class="rank-badge">1</span>
                            <?= $safe ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <!-- hidden inputs to hold ordered values for submission -->
                <div id="hidden-<?= $name ?>"></div>

            <?php elseif ($q['type'] === 'checkbox'): ?>
                <div class="checkbox-group">
                    <?php foreach ($q['options'] as $opt):
                        $safe = htmlspecialchars($opt); ?>
                    <label class="check-item">
                        <input type="checkbox" name="<?= $name ?>[]" value="<?= $safe ?>"> <?= $safe ?>
                    </label>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <div class="radio-group">
                    <?php foreach ($q['options'] as $opt):
                        $safe = htmlspecialchars($opt); ?>
                    <label class="radio-item">
                        <input type="radio" name="<?= $name ?>" value="<?= $safe ?>"> <?= $safe ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <span class="field-error hidden">please answer this one 🥺</span>
        </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-yes" style="width:100%; margin-top:1.5rem; color:#fff;">
            see our compatibility 💘
        </button>
    </form>
</div>

<script src="js/main.js"></script>
<script>
// ── Ranking logic ──
document.querySelectorAll('.compat-group[data-type="rank"]').forEach(group => {
    const name    = group.dataset.name;
    const max     = parseInt(group.dataset.max);
    const options = group.querySelectorAll('.rank-option');
    let   selected = []; // ordered array of values

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
                if (selected.length >= max) {
                    opt.classList.add('maxed');
                } else {
                    opt.classList.remove('maxed');
                }
            }
        });

        // inject hidden inputs with ranked values
        selected.forEach((val, i) => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = name + '[]';
            inp.value = val;
            hiddenContainer.appendChild(inp);
        });

        // update hint
        const hint = group.querySelector('.rank-hint span:last-child');
        if (hint) {
            if (selected.length === 0) {
                group.querySelector('.rank-hint').innerHTML =
                    `pick at least <span>${group.dataset.min}</span>, up to <span>${max}</span> — tap in order of preference`;
            } else {
                group.querySelector('.rank-hint').innerHTML =
                    `<span>${selected.length}</span> selected — ${selected.length < max ? `you can pick ${max - selected.length} more` : 'max reached'}`;
            }
        }
    }

    options.forEach(opt => {
        opt.addEventListener('click', () => {
            const val = opt.dataset.value;
            const idx = selected.indexOf(val);

            if (idx >= 0) {
                // deselect — remove and shift ranks
                selected.splice(idx, 1);
            } else {
                if (selected.length < max) {
                    selected.push(val);
                }
                // if maxed, do nothing
            }

            updateUI();

            // clear error on interaction
            if (group.classList.contains('has-error')) {
                group.classList.remove('has-error');
                group.querySelector('.field-error').classList.add('hidden');
                if (!document.querySelector('.compat-group.has-error')) {
                    document.getElementById('validationBanner').classList.add('hidden');
                }
            }
        });
    });

    updateUI();
});

// ── Validation ──
document.getElementById('compatForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let hasError = false;

    document.querySelectorAll('.compat-group').forEach(g => {
        g.classList.remove('has-error');
        g.querySelector('.field-error').classList.add('hidden');
    });
    document.getElementById('validationBanner').classList.add('hidden');

    document.querySelectorAll('.compat-group').forEach(group => {
        const type = group.dataset.type;
        const name = group.dataset.name;
        const min  = parseInt(group.dataset.min || '1');
        let valid  = false;

        if (type === 'rank') {
            const filled = document.querySelectorAll(`#hidden-${name} input`).length;
            valid = filled >= min;
        } else if (type === 'checkbox') {
            valid = group.querySelectorAll('input[type="checkbox"]:checked').length > 0;
        } else {
            valid = !!group.querySelector('input[type="radio"]:checked');
        }

        if (!valid) {
            group.classList.add('has-error');
            group.querySelector('.field-error').classList.remove('hidden');
            hasError = true;
        }
    });

    if (hasError) {
        document.getElementById('validationBanner').classList.remove('hidden');
        document.querySelector('.compat-group.has-error')
            .scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    this.submit();
});

// Live clear on radio/checkbox
document.querySelectorAll('.compat-group input').forEach(input => {
    input.addEventListener('change', function() {
        const group = this.closest('.compat-group');
        if (group?.classList.contains('has-error')) {
            group.classList.remove('has-error');
            group.querySelector('.field-error').classList.add('hidden');
            if (!document.querySelector('.compat-group.has-error')) {
                document.getElementById('validationBanner').classList.add('hidden');
            }
        }
    });
});
</script>
</body>
</html>
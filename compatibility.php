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
        <div class="form-group compat-group" id="group-<?= $name ?>" data-name="<?= $name ?>" data-type="<?= $q['type'] ?>">
            <label><?= htmlspecialchars($q['label']) ?> <span class="required-star">*</span></label>

            <div class="<?= $q['type'] === 'checkbox' ? 'checkbox-group' : 'radio-group' ?>">
                <?php foreach ($q['options'] as $opt):
                    $safe = htmlspecialchars($opt);
                    if ($q['type'] === 'checkbox'): ?>
                        <label class="check-item">
                            <input type="checkbox" name="<?= $name ?>[]" value="<?= $safe ?>"> <?= $safe ?>
                        </label>
                    <?php else: ?>
                        <label class="radio-item">
                            <input type="radio" name="<?= $name ?>" value="<?= $safe ?>"> <?= $safe ?>
                        </label>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <span class="field-error hidden">please answer this one 🥺</span>
        </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-yes" style="width:100%; margin-top:1rem;">
            see our compatibility 💘
        </button>
    </form>
</div>

<script src="js/main.js"></script>
<script>
document.getElementById('compatForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let hasError = false;

    // Clear previous errors
    document.querySelectorAll('.compat-group').forEach(g => {
        g.classList.remove('has-error');
        g.querySelector('.field-error').classList.add('hidden');
    });
    document.getElementById('validationBanner').classList.add('hidden');

    // Validate each group
    document.querySelectorAll('.compat-group').forEach(group => {
        const name  = group.dataset.name;
        const type  = group.dataset.type;

        if (type === 'checkbox') {
            const checked = group.querySelectorAll('input[type="checkbox"]:checked');
            if (checked.length === 0) {
                markError(group);
                hasError = true;
            }
        } else {
            const checked = group.querySelector('input[type="radio"]:checked');
            if (!checked) {
                markError(group);
                hasError = true;
            }
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

function markError(group) {
    group.classList.add('has-error');
    group.querySelector('.field-error').classList.remove('hidden');
}

// Live clear on interaction
document.querySelectorAll('.compat-group input').forEach(input => {
    input.addEventListener('change', function() {
        const group = this.closest('.compat-group');
        if (group.classList.contains('has-error')) {
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
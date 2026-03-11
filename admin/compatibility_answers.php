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

// Load existing admin answers
$stmt = $pdo->query("SELECT * FROM admin_compatibility_answers ORDER BY id DESC LIMIT 1");
$saved = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];
    foreach ($compatibility_questions as $name => $q) {
        if ($q['type'] === 'checkbox') {
            $data[$name] = isset($_POST[$name]) ? implode(', ', $_POST[$name]) : '';
        } else {
            $data[$name] = $_POST[$name] ?? '';
        }
    }

    if ($saved) {
        // Update existing
        $sets = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        $stmt = $pdo->prepare("UPDATE admin_compatibility_answers SET $sets WHERE id = ?");
        $stmt->execute(array_merge(array_values($data), [$saved['id']]));
    } else {
        // Insert first time
        $cols         = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt         = $pdo->prepare("INSERT INTO admin_compatibility_answers ($cols) VALUES ($placeholders)");
        $stmt->execute(array_values($data));
    }

    // Reload saved
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

    <form method="POST" id="adminCompatForm">
        <?php foreach ($compatibility_questions as $name => $q):
            // Pre-select saved answers
            $savedVal = $saved[$name] ?? '';
            $savedArr = array_map('trim', explode(', ', $savedVal));
        ?>
        <div class="form-group">
            <label><?= htmlspecialchars($q['label']) ?></label>
            <div class="<?= $q['type'] === 'checkbox' ? 'checkbox-group' : 'radio-group' ?>">
                <?php foreach ($q['options'] as $opt):
                    $safe    = htmlspecialchars($opt);
                    $checked = in_array($opt, $savedArr) ? 'checked' : '';
                    if ($q['type'] === 'checkbox'): ?>
                        <label class="check-item">
                            <input type="checkbox" name="<?= $name ?>[]" value="<?= $safe ?>" <?= $checked ?>> <?= $safe ?>
                        </label>
                    <?php else: ?>
                        <label class="radio-item">
                            <input type="radio" name="<?= $name ?>" value="<?= $safe ?>" <?= $checked ?>> <?= $safe ?>
                        </label>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-yes" style="width:100%; margin-top:1rem;">
            save my answers 💾
        </button>
    </form>
</div>
<script src="../js/main.js"></script>
</body>
</html>
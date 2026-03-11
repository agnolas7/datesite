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
        $data[$name] = ($q['type'] === 'checkbox' && isset($_POST[$name]))
            ? implode(', ', $_POST[$name])
            : ($_POST[$name] ?? '');
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

    <form method="POST">
        <?php foreach ($compatibility_questions as $name => $q):
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
        <button type="submit" class="btn btn-yes" style="width:100%; margin-top:1rem;">save 💾</button>
    </form>
</div>
</body>
</html>
<?php
session_start();
if (empty($_SESSION['owner'])) {
    header('Location: login.php');
    exit;
}
require '../includes/db.php';

$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $stmt  = $pdo->prepare("SELECT password FROM site_owners WHERE username = ?");
    $stmt->execute([$_SESSION['owner']]);
    $owner = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($current, $owner['password'])) {
        $error = 'current password is wrong.';
    } elseif (strlen($new) < 6) {
        $error = 'new password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $error = "passwords don't match.";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE site_owners SET password = ? WHERE username = ?")
            ->execute([$hash, $_SESSION['owner']]);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>change password</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        (function() {
            const t = localStorage.getItem('ownerTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
</head>
<body class="landing-page">
<div class="center-card" style="max-width:440px;">
    <a href="dashboard.php" style="display:block; color:var(--muted); text-decoration:none;
       font-size:0.85rem; margin-bottom:1.5rem; text-align:left;">← back</a>

    <h1 style="font-size:1.6rem;">change password 🔑</h1>
    <p class="subtitle">pick something you'll remember</p>

    <?php if ($success): ?>
        <div style="background:rgba(100,200,140,0.1); border:1px solid rgba(100,200,140,0.4);
                    border-radius:10px; padding:0.85rem 1.2rem; color:#6dc88a; margin:1.5rem 0; font-size:0.9rem;">
            ✔ password changed!
        </div>
        <a href="dashboard.php" class="btn btn-yes" style="display:block; text-align:center;">back to dashboard</a>
    <?php else: ?>
        <?php if ($error): ?>
            <div style="background:rgba(224,122,138,0.1); border:1px solid rgba(224,122,138,0.4);
                        border-radius:10px; padding:0.85rem 1.2rem; color:#e07a8a; margin:1.5rem 0; font-size:0.9rem;">
                ⚠️ <?= $error ?>
            </div>
        <?php endif; ?>
        <form method="POST" style="display:flex; flex-direction:column; gap:1rem; margin-top:1.5rem;">
            <?php
            $fields = [
                'current_password' => 'current password',
                'new_password'     => 'new password',
                'confirm_password' => 'confirm new password',
            ];
            foreach ($fields as $fname => $flabel): ?>
            <div style="display:flex; flex-direction:column; gap:0.4rem; text-align:left;">
                <label style="color:var(--pink); font-size:0.78rem; text-transform:uppercase;
                              letter-spacing:0.4px; font-weight:500;"><?= $flabel ?></label>
                <input type="password" name="<?= $fname ?>" required
                    style="background:var(--input-bg); border:1px solid var(--border); border-radius:10px;
                           padding:0.75rem 1rem; color:var(--text); font-family:'DM Sans',sans-serif;
                           font-size:0.95rem; outline:none; width:100%;">
            </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-yes" style="width:100%; margin-top:0.5rem;">
                save new password
            </button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
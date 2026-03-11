<?php
session_start();

// Always clear any existing owner session when landing on login page
// This ensures each client starts fresh and can't accidentally access
// a previous user's dashboard
session_unset();

require '../includes/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM site_owners WHERE username = ?");
    $stmt->execute([$username]);
    $owner = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($owner && password_verify($password, $owner['password'])) {
        $_SESSION['owner']    = $owner['username'];
        $_SESSION['owner_id'] = $owner['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'wrong username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>owner login</title>
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
<div class="center-card" style="max-width:420px;">

    <h1 style="font-size:1.8rem;">goodluck g</h1>
    <p class="subtitle">log in to see your responses</p>

    <?php if ($error): ?>
        <p style="color:#e07a8a; margin:1rem 0; font-size:0.9rem;">⚠️ <?= $error ?></p>
    <?php endif; ?>

    <form method="POST" style="display:flex; flex-direction:column; gap:1rem; margin-top:1.5rem;">
        <div style="display:flex; flex-direction:column; gap:0.4rem; text-align:left;">
            <label style="color:var(--pink); font-size:0.78rem; text-transform:uppercase;
                          letter-spacing:0.4px; font-weight:500;">username</label>
            <input type="text" name="username" required
                style="background:var(--input-bg); border:1px solid var(--border); border-radius:10px;
                       padding:0.75rem 1rem; color:var(--text); font-family:'DM Sans',sans-serif;
                       font-size:0.95rem; outline:none; width:100%;">
        </div>
        <div style="display:flex; flex-direction:column; gap:0.4rem; text-align:left;">
            <label style="color:var(--pink); font-size:0.78rem; text-transform:uppercase;
                          letter-spacing:0.4px; font-weight:500;">password</label>
            <input type="password" name="password" required
                style="background:var(--input-bg); border:1px solid var(--border); border-radius:10px;
                       padding:0.75rem 1rem; color:var(--text); font-family:'DM Sans',sans-serif;
                       font-size:0.95rem; outline:none; width:100%;">
        </div>
        <button type="submit" class="btn btn-yes" style="width:100%; margin-top:0.5rem;">
            log in
        </button>
    </form>

</div>
</body>
</html>
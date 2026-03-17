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
    } elseif (strlen($new) < 8) {
        $error = 'new password must be at least 8 characters.';
    } elseif (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/\\|`~]/', $new)) {
        $error = 'new password must include at least one special character (!@#$%^&* etc).';
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
    <style>
        .password-strength { margin-top: 0.4rem; font-size: 0.78rem; }
        .strength-bar { height: 4px; background: #333; border-radius: 2px; margin-top: 0.2rem; overflow: hidden; }
        .strength-fill { height: 100%; width: 0%; transition: width 0.2s, background-color 0.2s; background-color: #999; }
        .strength-text { margin-top: 0.2rem; }
    </style>
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
            <div style="display:flex; flex-direction:column; gap:0.4rem; text-align:left;">
                <label style="color:var(--pink); font-size:0.78rem; text-transform:uppercase;
                              letter-spacing:0.4px; font-weight:500;">current password</label>
                <input type="password" name="current_password" required
                    style="background:var(--input-bg); border:1px solid var(--border); border-radius:10px;
                           padding:0.75rem 1rem; color:var(--text); font-family:'DM Sans',sans-serif;
                           font-size:0.95rem; outline:none; width:100%;">
            </div>
            <div style="display:flex; flex-direction:column; gap:0.4rem; text-align:left;">
                <label style="color:var(--pink); font-size:0.78rem; text-transform:uppercase;
                              letter-spacing:0.4px; font-weight:500;">new password</label>
                <input type="password" name="new_password" id="newPassword" required
                    style="background:var(--input-bg); border:1px solid var(--border); border-radius:10px;
                           padding:0.75rem 1rem; color:var(--text); font-family:'DM Sans',sans-serif;
                           font-size:0.95rem; outline:none; width:100%;">
                <div class="password-strength">
                    <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                    <div class="strength-text" id="strengthText" style="color:var(--muted);"></div>
                </div>
                <p style="color:var(--muted); font-size:0.75rem; margin-top:0.3rem; line-height:1.4;">
                    requirements: at least 8 characters + 1 special character (!@#$%^&* etc)
                </p>
            </div>
            <div style="display:flex; flex-direction:column; gap:0.4rem; text-align:left;">
                <label style="color:var(--pink); font-size:0.78rem; text-transform:uppercase;
                              letter-spacing:0.4px; font-weight:500;">confirm new password</label>
                <input type="password" name="confirm_password" id="confirmPassword" required
                    style="background:var(--input-bg); border:1px solid var(--border); border-radius:10px;
                           padding:0.75rem 1rem; color:var(--text); font-family:'DM Sans',sans-serif;
                           font-size:0.95rem; outline:none; width:100%;">
                <div id="matchText" style="color:var(--muted); font-size:0.75rem; margin-top:0.3rem;"></div>
            </div>
            <button type="submit" class="btn btn-yes" style="width:100%; margin-top:0.5rem;">
                save new password
            </button>
        </form>
        <script>
            const newPassInput = document.getElementById('newPassword');
            const confirmPassInput = document.getElementById('confirmPassword');
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');
            const matchText = document.getElementById('matchText');

            function checkPasswordStrength() {
                const pass = newPassInput.value;
                let strength = 0;
                let feedback = [];

                // Length check (8+)
                if (pass.length >= 8) {
                    strength += 33;
                } else {
                    feedback.push(`${pass.length}/8 chars`);
                }

                // Special character check
                if (/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/\\|`~]/.test(pass)) {
                    strength += 33;
                } else {
                    feedback.push('needs special char');
                }

                // Mixed case
                if (/[a-z]/.test(pass) && /[A-Z]/.test(pass)) {
                    strength += 34;
                }

                strengthFill.style.width = strength + '%';
                
                if (strength < 50) {
                    strengthFill.style.backgroundColor = '#c87a7a';
                    strengthText.textContent = 'weak';
                    strengthText.style.color = '#c87a7a';
                } else if (strength < 90) {
                    strengthFill.style.backgroundColor = '#d4a574';
                    strengthText.textContent = 'good: ' + feedback.join(', ');
                    strengthText.style.color = '#d4a574';
                } else {
                    strengthFill.style.backgroundColor = '#6dc88a';
                    strengthText.textContent = 'strong!';
                    strengthText.style.color = '#6dc88a';
                }

                // Check if passwords match
                if (confirmPassInput.value) {
                    if (pass === confirmPassInput.value) {
                        matchText.textContent = 'passwords match';
                        matchText.style.color = '#6dc88a';
                    } else {
                        matchText.textContent = "passwords don't match";
                        matchText.style.color = '#c87a7a';
                    }
                }
            }

            function checkMatch() {
                const pass = newPassInput.value;
                const confirm = confirmPassInput.value;
                if (confirm) {
                    if (pass === confirm) {
                        matchText.textContent = 'passwords match';
                        matchText.style.color = '#6dc88a';
                    } else {
                        matchText.textContent = "passwords don't match";
                        matchText.style.color = '#c87a7a';
                    }
                } else {
                    matchText.textContent = '';
                }
            }

            newPassInput.addEventListener('input', checkPasswordStrength);
            confirmPassInput.addEventListener('input', checkMatch);
        </script>
    <?php endif; ?>
</div>
</body>
</html>
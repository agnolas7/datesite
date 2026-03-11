<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require '../includes/db.php';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        if (!$username || !$password) {
            $error = 'fill in both fields.';
        } else {
            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("INSERT INTO site_owners (username, password) VALUES (?, ?)")
                    ->execute([$username, $hash]);
                $success = "✔ account created! send them → username: <strong>$username</strong> / password: <strong>$password</strong>";
            } catch (PDOException $e) {
                $error = 'that username is already taken.';
            }
        }
    }

    if ($action === 'reset') {
        $reset_id = intval($_POST['owner_id']);
        $new_pass = trim($_POST['new_password'] ?? '');
        if ($new_pass) {
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE site_owners SET password = ? WHERE id = ?")
                ->execute([$hash, $reset_id]);
            $success = "✔ password reset. new password: <strong>$new_pass</strong>";
        }
    }

    if ($action === 'delete') {
        $del_id = intval($_POST['owner_id']);
        $pdo->prepare("DELETE FROM site_owners WHERE id = ?")->execute([$del_id]);
        $success = '✔ account deleted.';
    }
}

$owners = $pdo->query("SELECT * FROM site_owners ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>manage accounts</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body class="admin-dashboard">
    <a href="dashboard.php" style="color:var(--muted); text-decoration:none; font-size:0.85rem; display:inline-block; margin-bottom:1.5rem;">← back to dashboard</a>
    <h1>manage buyer accounts 🔑</h1>
    <p style="color:var(--muted); font-size:0.85rem; margin-bottom:2rem;">
        ⚠️ keep this page private. only you should access this.
    </p>

    <?php if ($success): ?>
        <div style="background:rgba(100,200,140,0.1); border:1px solid rgba(100,200,140,0.4);
                    border-radius:10px; padding:0.85rem 1.2rem; color:#6dc88a; margin-bottom:1.5rem; font-size:0.9rem;">
            <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div style="background:rgba(224,122,138,0.1); border:1px solid rgba(224,122,138,0.4);
                    border-radius:10px; padding:0.85rem 1.2rem; color:#e07a8a; margin-bottom:1.5rem; font-size:0.9rem;">
            ⚠️ <?= $error ?>
        </div>
    <?php endif; ?>

    <!-- Create form -->
    <div style="background:#111; border:1px solid #222; border-radius:16px; padding:1.5rem; max-width:460px; margin-bottom:2.5rem;">
        <h2 style="font-size:1rem; color:var(--pink); margin-bottom:1rem;">create new buyer account</h2>
        <form method="POST" style="display:flex; flex-direction:column; gap:0.8rem;">
            <input type="hidden" name="action" value="create">
            <input type="text" name="username" placeholder="username  (e.g. juan123)"
                style="background:#1a1a1a; border:1px solid #333; border-radius:8px;
                       padding:0.65rem 0.9rem; color:#eee; font-family:'DM Sans',sans-serif;
                       font-size:0.9rem; outline:none;">
            <input type="text" name="password" placeholder="temporary password"
                style="background:#1a1a1a; border:1px solid #333; border-radius:8px;
                       padding:0.65rem 0.9rem; color:#eee; font-family:'DM Sans',sans-serif;
                       font-size:0.9rem; outline:none;">
            <button type="submit" class="btn btn-yes" style="align-self:flex-start; padding:0.6rem 1.4rem; font-size:0.9rem;">
                create account
            </button>
        </form>
    </div>

    <!-- Existing accounts -->
    <h2 style="font-size:0.8rem; color:var(--muted); text-transform:uppercase;
               letter-spacing:0.5px; margin-bottom:1rem;">existing accounts (<?= count($owners) ?>)</h2>

    <?php if (empty($owners)): ?>
        <p style="color:#333; font-style:italic; font-size:0.9rem;">no accounts created yet.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>username</th>
                <th>created</th>
                <th>reset password</th>
                <th>delete</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($owners as $o): ?>
        <tr>
            <td><?= htmlspecialchars($o['username']) ?></td>
            <td style="font-size:0.82rem; color:var(--muted);"><?= $o['created_at'] ?></td>
            <td>
                <form method="POST" style="display:flex; gap:0.5rem; align-items:center;">
                    <input type="hidden" name="action" value="reset">
                    <input type="hidden" name="owner_id" value="<?= $o['id'] ?>">
                    <input type="text" name="new_password" placeholder="new password"
                        style="background:#1a1a1a; border:1px solid #333; border-radius:6px;
                               padding:0.4rem 0.7rem; color:#eee; font-size:0.8rem;
                               font-family:'DM Sans',sans-serif; outline:none; width:130px;">
                    <button type="submit"
                        style="background:#222; border:1px solid #333; border-radius:6px;
                               padding:0.4rem 0.8rem; color:#aaa; cursor:pointer; font-size:0.8rem;">
                        reset
                    </button>
                </form>
            </td>
            <td>
                <form method="POST" onsubmit="return confirm('delete <?= htmlspecialchars($o['username']) ?>? this cannot be undone.')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="owner_id" value="<?= $o['id'] ?>">
                    <button type="submit"
                        style="background:transparent; border:1px solid #c87a7a; border-radius:6px;
                               padding:0.4rem 0.8rem; color:#c87a7a; cursor:pointer; font-size:0.8rem;">
                        delete
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</body>
</html>
<?php
require '../includes/db.php';

$password = 'rmlnll2329';
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = ?")
        ->execute([$hash, 'admin']);
    echo "<h2 style='color:green; text-align:center; margin-top:3rem;'>✓ Password updated successfully!</h2>";
    echo "<p style='text-align:center; margin-top:1rem;'>You can now login with:</p>";
    echo "<p style='text-align:center; font-weight:bold;'>Username: <code>admin</code></p>";
    echo "<p style='text-align:center; font-weight:bold;'>Password: <code>rmlnll2329</code></p>";
    echo "<p style='text-align:center; margin-top:2rem;'><a href='login.php' style='color:#f4a7b9; text-decoration:none; font-weight:bold;'>→ Go to login</a></p>";
} catch (Exception $e) {
    echo "<h2 style='color:red;'>Error: " . $e->getMessage() . "</h2>";
}
?>

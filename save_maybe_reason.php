<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason         = trim($_POST['reason'] ?? '');
    $owner_username = $_SESSION['owner'] ?? null;
    $response_id    = $_SESSION['response_id'] ?? null;

    if ($reason) {
        // Save to its own table regardless of whether form was filled
        $pdo->prepare("INSERT INTO maybe_reasons (owner_username, reason, response_id) VALUES (?, ?, ?)")
            ->execute([$owner_username, $reason, $response_id]);

        // Also store in session in case she changes her mind and fills the form
        $_SESSION['maybe_reason'] = $reason;
    }
}

echo 'ok';
?>
<?php
require 'includes/db.php';

$id = intval($_POST['id'] ?? 0);
$date = $_POST['date'] ?? '';

if ($id && $date) {
    $stmt = $pdo->prepare("UPDATE responses SET scheduled_date = ? WHERE id = ?");
    $stmt->execute([$date, $id]);
}

echo 'ok';
?>
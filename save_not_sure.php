<?php
session_start();
require 'includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    echo json_encode(['success' => false, 'error' => 'invalid request']);
    exit;
}

$id = intval($_POST['id']);

try {
    // Check if response exists
    $check = $pdo->prepare("SELECT id FROM responses WHERE id = ?");
    $check->execute([$id]);
    if (!$check->fetch()) {
        echo json_encode(['success' => false, 'error' => 'response not found']);
        exit;
    }
    
    // Insert a placeholder message to mark "not sure" state if none exists yet
    // We'll insert a special marker message
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            response_id INT NOT NULL,
            message_text LONGTEXT NOT NULL,
            instagram_handle VARCHAR(255) DEFAULT NULL,
            sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (response_id) REFERENCES responses(id) ON DELETE CASCADE
        )
    ");
    
    // Check if already marked
    $check = $pdo->prepare("SELECT id FROM messages WHERE response_id = ?");
    $check->execute([$id]);
    
    if (!$check->fetch()) {
        // Insert marker for "not sure" state
        $stmt = $pdo->prepare("INSERT INTO messages (response_id, message_text) VALUES (?, ?)");
        $stmt->execute([$id, '__not_sure_marker__']);
    }
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>

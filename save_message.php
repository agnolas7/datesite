<?php
session_start();
require 'includes/db.php';

header('Content-Type: application/json');

// Verify response exists
$id = $_POST['id'] ?? null;
$message = $_POST['message'] ?? null;
$instagram = $_POST['instagram'] ?? null;

if (!$id || !$message) {
    echo json_encode(['success' => false, 'error' => 'missing data']);
    exit;
}

$message = trim($message);
if (strlen($message) === 0 || strlen($message) > 5000) {
    echo json_encode(['success' => false, 'error' => 'invalid message']);
    exit;
}

try {
    // Check if response exists
    $check = $pdo->prepare("SELECT id FROM responses WHERE id = ?");
    $check->execute([$id]);
    if (!$check->fetch()) {
        echo json_encode(['success' => false, 'error' => 'response not found']);
        exit;
    }

    // Create messages table if it doesn't exist
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

    // Check if instagram_handle column exists, if not add it
    $columns = $pdo->query("SHOW COLUMNS FROM messages WHERE Field = 'instagram_handle'")->fetch();
    if (!$columns) {
        $pdo->exec("ALTER TABLE messages ADD COLUMN instagram_handle VARCHAR(255) DEFAULT NULL");
    }

    // Insert message with optional instagram handle
    $stmt = $pdo->prepare("INSERT INTO messages (response_id, message_text, instagram_handle) VALUES (?, ?, ?)");
    $stmt->execute([$id, $message, $instagram ?: null]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>

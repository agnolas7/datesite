<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['response_id'])) {
    $id = $_SESSION['response_id'];
    $date_type = $_POST['date_type'] ?? '';
    $spontaneity = $_POST['spontaneity'] ?? '';
    $energy = $_POST['energy'] ?? '';
    $mood = $_POST['mood'] ?? '';
    $crowd = $_POST['crowd'] ?? '';
    $convo_style = $_POST['convo_style'] ?? '';
    $walking = $_POST['walking'] ?? '';
    $awkwardness = $_POST['awkwardness'] ?? '';
    $convo_difficulty = $_POST['convo_difficulty'] ?? '';
    $vibes = isset($_POST['vibes']) ? implode(', ', $_POST['vibes']) : '';
    $custom_vibe = trim($_POST['custom_vibe'] ?? '');
    $compatibility_score = rand(69, 99); // always high lol

    $stmt = $pdo->prepare("UPDATE responses SET 
        date_type=?, spontaneity=?, energy=?, mood=?, crowd=?, convo_style=?,
        walking=?, awkwardness=?, convo_difficulty=?, vibes=?, custom_vibe=?,
        compatibility_score=?
        WHERE id=?");

    $stmt->execute([
        $date_type, $spontaneity, $energy, $mood, $crowd, $convo_style,
        $walking, $awkwardness, $convo_difficulty, $vibes, $custom_vibe,
        $compatibility_score, $id
    ]);

    header('Location: result.php');
    exit;
}

header('Location: index.php');
exit;
?>
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
    $vibes = isset($_POST['vibes']) ? implode(', ', $_POST['vibes']) : '';
    $custom_vibe = trim($_POST['custom_vibe'] ?? '');
    $curfew = $_POST['curfew'] ?? '';
    $parents = $_POST['parents'] ?? '';
    $distance = $_POST['distance'] ?? '';
    $place_in_mind = $_POST['place_in_mind'] ?? '';
    $place_name = trim($_POST['place_name'] ?? '');
    $place_timing = $_POST['place_timing'] ?? '';
    $compatibility_score = rand(69, 99); // always high lol

    $stmt = $pdo->prepare("UPDATE responses SET 
        date_type=?, spontaneity=?, energy=?, mood=?, crowd=?, convo_style=?,
        walking=?, awkwardness=?, vibes=?, custom_vibe=?,
        curfew=?, parents=?, distance=?, place_in_mind=?, place_name=?,
        place_timing=?, compatibility_score=?, submitted_at=NOW()
        WHERE id=?");

    $stmt->execute([
        $date_type, $spontaneity, $energy, $mood, $crowd, $convo_style,
        $walking, $awkwardness, $vibes, $custom_vibe,
        $curfew, $parents, $distance, $place_in_mind, $place_name,
        $place_timing, $compatibility_score, $id
    ]);

    header('Location: result.php');
    exit;
}

header('Location: index.php');
exit;
?>
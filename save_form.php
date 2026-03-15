<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name          = trim($_POST['name'] ?? '');
    $age           = $_POST['age'] ?? '';
    $city          = trim($_POST['city'] ?? '');
    $communication = isset($_POST['communication']) ? implode(', ', $_POST['communication']) : '';
    $best_time     = $_POST['best_time'] ?? '';
    $food_drink    = trim($_POST['food_drink'] ?? '');
    $dealbreaker   = trim($_POST['dealbreaker'] ?? '');
    $craving       = trim($_POST['craving'] ?? '');
    $temperature   = $_POST['temperature'] ?? '';
    $dislikes      = isset($_POST['dislikes']) ? implode(', ', $_POST['dislikes']) : '';
    $dessert       = $_POST['dessert'] ?? '';
    $owner_username = $_SESSION['owner'] ?? null;

    // flower is now an array — join into a string
    $flower = '';
    if (!empty($_POST['flower'])) {
        $flowerArr = is_array($_POST['flower']) ? $_POST['flower'] : [$_POST['flower']];
        // filter out the placeholder value just in case
        $flowerArr = array_filter($flowerArr, fn($f) => $f !== '__custom_flower__');
        $flower = implode(', ', array_map('trim', $flowerArr));
    }

    if (empty($name)) {
        die("Please go back and enter your name!");
    }

    $stmt = $pdo->prepare("INSERT INTO responses
        (name, age, city, communication, best_time, food_drink, dealbreaker,
         flower, craving, temperature, dislikes, dessert, owner_username)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $name, $age, $city, $communication, $best_time, $food_drink, $dealbreaker,
        $flower, $craving, $temperature, $dislikes, $dessert, $owner_username
    ]);

    $new_id = $pdo->lastInsertId();
    $_SESSION['response_id'] = $new_id;
    $_SESSION['name']        = $name;

    // If they came through the maybe flow, save their reason
    if (!empty($_SESSION['maybe_reason'])) {
        $pdo->prepare("UPDATE responses SET maybe_reason = ? WHERE id = ?")
            ->execute([$_SESSION['maybe_reason'], $new_id]);
        unset($_SESSION['maybe_reason']);
    }

    header('Location: greeting.php');
    exit;
}
?>
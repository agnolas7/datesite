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

    // Handle flower — could be a preset or custom text
    $flower = trim($_POST['flower'] ?? '');

    $owner_username = $_SESSION['owner'] ?? null;

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

    $_SESSION['response_id'] = $pdo->lastInsertId();
    $_SESSION['name']        = $name;

    header('Location: greeting.php');
    exit;
}
?>
<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $age = $_POST['age'] ?? '';
    $city = trim($_POST['city'] ?? '');
    $communication = isset($_POST['communication']) ? implode(', ', $_POST['communication']) : '';
    $best_time = $_POST['best_time'] ?? '';
    $food_drink = trim($_POST['food_drink'] ?? '');
    $dealbreaker = $_POST['dealbreaker'] ?? '';

    if (empty($name)) {
        die("Please go back and enter your name!");
    }

    $stmt = $pdo->prepare("INSERT INTO responses 
        (name, age, city, communication, best_time, food_drink, dealbreaker) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([$name, $age, $city, $communication, $best_time, $food_drink, $dealbreaker]);

    // Save the new row's ID in session so we can update it later with preferences
    $_SESSION['response_id'] = $pdo->lastInsertId();
    $_SESSION['name'] = $name;

    header('Location: greeting.php');
    exit;
}
?>
<?php
session_start();
if (empty($_SESSION['name'])) {
    header('Location: index.php');
    exit;
}
$name = htmlspecialchars($_SESSION['name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>hi there 👋</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body class="greeting-page">
    <div class="center-card">
        <p class="subtitle">well well well 👀</p>
        <h1 class="greeting-name">Nice to meet you, <span><?= $name ?></span> 🌸</h1>
        <p>now i have a few more questions for you...</p>
        <p class="small-note">(important research purposes)</p>
        <a href="preferences.php" class="btn btn-yes" style="margin-top:2rem;">
            okay let's go 💌
        </a>
    </div>
    <script src="js/main.js"></script>
</body>
</html>
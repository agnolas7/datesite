<?php
session_start();
require 'includes/db.php';

if (empty($_SESSION['response_id'])) {
    header('Location: index.php');
    exit;
}

$id = $_SESSION['response_id'];
$stmt = $pdo->prepare("SELECT * FROM responses WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$score = $row['compatibility_score'] ?? rand(69, 99);
$name = htmlspecialchars($_SESSION['name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>results are in 🎊</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body class="result-page">

    <div class="center-card" id="loadingScreen">
        <div class="spinner"></div>
        <p class="loading-text">Processing results...</p>
    </div>

    <div class="center-card hidden" id="resultScreen">
        <p class="subtitle">✦ official result ✦</p>
        <h1>Based on your answers, a date with me has been <span class="approved">approved</span>. 🎊</h1>
        <div class="score-box">
            <span class="score-num"><?= $score ?>%</span>
            <span class="score-label">compatibility</span>
        </div>

        <div class="result-buttons">
            <button class="btn btn-yes" onclick="showScheduler()">
                when are u free. sched a date 📅
            </button>
            <a href="https://instagram.com/sa.loooong.a" target="_blank" class="btn btn-maybe">
                send me a message instead 💌
            </a>
            <a href="index.php" class="btn btn-maybe">
                go back to home page 🏠
            </a>
        </div>

        <div class="scheduler hidden" id="schedulerBox">
            <h3>pick a date & time 🗓️</h3>
            <input type="datetime-local" id="scheduledDate">
            <button class="btn btn-yes" onclick="saveSchedule()">confirm 🌸</button>
            <p id="scheduleConfirm" class="hidden success-msg">yesss noted! see you soon 🎉</p>
        </div>
    </div>

    <script>
    // Show loading first, then result after 3 seconds
    setTimeout(() => {
        document.getElementById('loadingScreen').classList.add('hidden');
        document.getElementById('resultScreen').classList.remove('hidden');
    }, 3000);

    function showScheduler() {
        document.getElementById('schedulerBox').classList.remove('hidden');
    }

    function saveSchedule() {
        const dt = document.getElementById('scheduledDate').value;
        if (!dt) { alert('pick a date first!'); return; }

        fetch('save_schedule.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=<?= $id ?>&date=' + encodeURIComponent(dt)
        }).then(() => {
            document.getElementById('scheduleConfirm').classList.remove('hidden');
        });
    }
    </script>
    <script src="js/main.js"></script>
</body>
</html>
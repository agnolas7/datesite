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
    <script>
        (function() {
            const t = localStorage.getItem('siteTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
</head>
<body class="result-page">

    <div class="center-card" id="loadingScreen">
        <div class="spinner"></div>
        <p class="loading-text">Processing results...</p>
    </div>

    <div class="center-card hidden" id="resultScreen">
        <p class="subtitle">✦ official result ✦</p>
        <h1>Based on your answers, a date with me has been <span class="approved">approved</span>. 🎊</h1>

        <!-- Primary actions -->
        <div class="result-buttons">
            <button class="btn btn-yes" onclick="showScheduler()">
                when are u free. sched a date 📅
            </button>
            <a href="https://instagram.com/sa.loooong.a" target="_blank" class="btn btn-maybe">
                send me a message instead 💌
            </a>
        </div>

        <!-- Scheduler -->
        <div class="scheduler hidden" id="schedulerBox">
            <p class="scheduler-label">pick a date & time 🗓️</p>

            <div class="datetime-row">
                <div class="datetime-field">
                    <label for="schedDate">📅 date</label>
                    <input type="date" id="schedDate">
                </div>
                <div class="datetime-field">
                    <label for="schedTime">🕐 time</label>
                    <input type="time" id="schedTime">
                </div>
            </div>

            <button class="btn btn-yes scheduler-confirm-btn" onclick="saveSchedule()">
                confirm 
            </button>
            <p id="scheduleConfirm" class="hidden success-msg">yesss noted! see you soon</p>
        </div>

        <!-- Optional separator -->
        <div class="result-divider">
            <span>alsoooo, if you're curious</span>
        </div>

        <!-- Optional compatibility -->
        <a href="compatibility.php" class="compat-optional-btn">
            check our compatibility 
        </a>

    </div>

    <script>
    setTimeout(() => {
        document.getElementById('loadingScreen').classList.add('hidden');
        document.getElementById('resultScreen').classList.remove('hidden');
    }, 3000);

    function showScheduler() {
        document.getElementById('schedulerBox').classList.remove('hidden');
        document.getElementById('schedulerBox').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function saveSchedule() {
        const date = document.getElementById('schedDate').value;
        const time = document.getElementById('schedTime').value;

        if (!date || !time) {
            alert('pick both a date and time first!');
            return;
        }

        const combined = date + ' ' + time;

        fetch('save_schedule.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=<?= $id ?>&date=' + encodeURIComponent(combined)
        }).then(() => {
            document.getElementById('scheduleConfirm').classList.remove('hidden');
        });
    }
    </script>
    <script src="js/main.js"></script>
</body>
</html>
<?php
session_start();
require 'includes/db.php';

// Load owner profile
$ownerUsername = $_SESSION['owner'] ?? null;
$profileItems  = [
    '😊 genuinely kind and thoughtful',
    '🎵 good music taste (subjective but trust me)',
    '🍜 will always share food',
    '🌙 good late night company',
    '🗣️ actually listens when you talk',
    '😂 kinda funny naman',
    '🚗 may wheels (important)',
];
$promiseText = 'di ako masamang tao promise, go out with me please';
$whyyyText   = 'okay okay let me make my case first...';

if ($ownerUsername) {
    $stmt = $pdo->prepare("SELECT profile_items, promise_text, whyyy_text FROM site_owners WHERE username = ?");
    $stmt->execute([$ownerUsername]);
    $ownerData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ownerData) {
        if (!empty($ownerData['profile_items'])) {
            $profileItems = json_decode($ownerData['profile_items'], true);
        }
        if (!empty($ownerData['promise_text'])) {
            $promiseText = $ownerData['promise_text'];
        }
        if (!empty($ownerData['whyyy_text'])) {
            $whyyyText = $ownerData['whyyy_text'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>wait wait wait</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        (function() {
            const t = localStorage.getItem('siteTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
</head>
<body class="maybe-page">

    <div class="center-card" id="step-whyyy">
        <h1 class="big-emotion">WHYYY 😭</h1>
        <p><?= htmlspecialchars($whyyyText) ?></p>
        <button class="btn btn-primary" onclick="showProfile()">okay fine, go on</button>
    </div>

    <div class="center-card hidden" id="step-profile">
        <h2>here's what you should know about me 👇</h2>
        <div class="profile-list">
            <?php foreach ($profileItems as $item): ?>
                <div class="profile-item"><?= htmlspecialchars($item) ?></div>
            <?php endforeach; ?>
        </div>
        <p class="promise-text">"<?= htmlspecialchars($promiseText) ?>" 🙏</p>
        <div class="button-group">
            <a href="form.php" class="btn btn-yes">ge kulit mo eh 😄</a>
            <button class="btn btn-maybe" onclick="showAreYouSure()">pass pa rin</button>
        </div>
    </div>

    <div class="center-card hidden" id="step-areyousure">
        <h1 class="big-emotion">ARE YOU SURE? 😨</h1>
        <p>like... really sure?</p>
        <div class="button-group">
            <button class="btn btn-no" onclick="startCountdown()">yes i'm sure</button>
            <a href="form.php" class="btn btn-yes">wait actually no 😅</a>
        </div>
    </div>

    <div class="center-card hidden" id="step-countdown">
        <h2>okay. think hard about it.</h2>
        <div class="countdown-circle" id="countdownDisplay">10</div>
        <p id="countdownMsg">reconsider...</p>
    </div>

    <div class="center-card hidden" id="step-whatAboutNow">
        <h2>what about now? 🥺</h2>
        <div class="button-group">
            <a href="form.php" class="btn btn-yes">okay 😊</a>
            <button class="btn btn-maybe" onclick="showThinkAgain()">still no</button>
        </div>
    </div>

    <div class="center-card hidden" id="step-thinkAgain">
        <h2>think again. 🤨</h2>
        <p>i'll wait...</p>
        <div style="margin-top: 2rem;">
            <a href="form.php" class="btn btn-yes">yes 🌸</a>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>
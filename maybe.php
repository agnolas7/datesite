<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>wait wait wait</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body class="maybe-page">

    <div class="center-card" id="step-whyyy">
        <h1 class="big-emotion">WHYYY 😭</h1>
        <p>okay okay let me make my case first...</p>
        <button class="btn btn-primary" onclick="showProfile()">okay fine, go on</button>
    </div>

    <div class="center-card hidden" id="step-profile">
        <h2>here's what you should know about me 👇</h2>
        <div class="profile-list">
            <div class="profile-item">😊 genuinely kind and thoughtful</div>
            <div class="profile-item">🎵 good music taste (subjective but trust me)</div>
            <div class="profile-item">🍜 will always share food</div>
            <div class="profile-item">🌙 good late night company</div>
            <div class="profile-item">🗣️ actually listens when you talk</div>
            <div class="profile-item">😂 kinda funny naman</div>
            <div class="profile-item">🚗 may wheels (important)</div>
        </div>
        <p class="promise-text">"di ako masamang tao promise, go out with me please" 🙏</p>
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
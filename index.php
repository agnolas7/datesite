<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>a question for you 🌸</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body class="landing-page">

    <!-- Easter egg logo -->
    <div class="logo" id="easterEggLogo">✦</div>
    <div class="easter-egg-msg" id="easterEggMsg">
        I made this para sa crush kong SN na taga Cabanatuan :p
    </div>

    <div class="center-card">
        <p class="subtitle">hey, so...</p>
        <h1 class="main-question">Would you be interested to go out on a date with me?</h1>
        <p class="small-note">think carefully before answering 👀</p>

        <div class="button-group" id="buttonGroup">
            <a href="form.php" class="btn btn-yes">Yes 💌</a>
            <a href="maybe.php" class="btn btn-maybe">Maybe 🤔</a>
            <button class="btn btn-no" id="noBtn" onmouseover="runAway(this)">No 🚪</button>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>
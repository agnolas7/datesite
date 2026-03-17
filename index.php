<?php
session_start();

// Tag which owner this visitor belongs to
if (!empty($_GET['u'])) {
    $_SESSION['owner'] = $_GET['u'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>a question for you 🌸</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        (function() {
            const t = localStorage.getItem('siteTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <style>
        .easter-egg-hint {
            text-align: center;
            font-size: 0.7rem;
            color: var(--muted);
            font-style: italic;
            opacity: 0.6;
            margin-top: 2.5rem;
            letter-spacing: 0.5px;
        }

        .easter-egg-hint:hover {
            opacity: 0.9;
            color: var(--pink);
            cursor: pointer;
        }
    </style>
</head>
<body class="landing-page">

    <!-- Easter egg logo -->
    <div class="logo" id="easterEggLogo">🌸</div>
    <div class="easter-egg-msg" id="easterEggMsg">
        crush ng developer yung SN na taga cab :p
    </div>

    <!-- Theme toggle -->
    <button class="theme-toggle" id="themeBtn" onclick="toggleTheme()">☀️ light</button>

    <div class="center-card">
        <p class="subtitle">hey............</p>
        <h1 class="main-question">Would you be interested to go out with me?</h1>
        <p class="small-note">think carefully before answering</p>

        <div class="button-group" id="buttonGroup">
            <a href="form.php" class="btn btn-yes">Yes 💌</a>
            <a href="maybe.php" class="btn btn-maybe">Maybe 🤔</a>
            <button class="btn btn-no" id="noBtn">No 🚪</button>
        </div>

        <div class="easter-egg-hint">🔍 find the easter egg</div>
    </div>

    <script src="js/main.js"></script>
    <script>
        let easterEggClicks = 0;
        
        document.addEventListener('DOMContentLoaded', function() {
            const easterEggLogo = document.getElementById('easterEggLogo');
            const easterEggHint = document.querySelector('.easter-egg-hint');

            if (easterEggLogo && easterEggHint) {
                easterEggLogo.style.cursor = 'pointer';
                easterEggLogo.addEventListener('click', function() {
                    easterEggClicks++;
                    console.log('Clicks:', easterEggClicks);
                    if (easterEggClicks === 4) {
                        easterEggHint.classList.add('show');
                        console.log('Easter egg hint revealed!');
                    }
                });
            }
        });

        const themeBtn = document.getElementById('themeBtn');
        const saved = localStorage.getItem('siteTheme') || 'dark';
        themeBtn.textContent = saved === 'light' ? '🌙 dark' : '☀️ light';

        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('siteTheme', next);
            themeBtn.textContent = next === 'light' ? '🌙 dark' : '☀️ light';
        }
    </script>
</body>
</html>
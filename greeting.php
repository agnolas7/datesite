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
    <script>
        (function() {
            const t = localStorage.getItem('siteTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <style>
        body.greeting-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .greeting-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 860px;
            width: 100%;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 24px;
            overflow: hidden;
            animation: fadeUp 0.5s ease;
        }

        /* ── Left ── */
        .greeting-left {
            padding: 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid var(--border);
        }

        .greeting-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--pink);
            border: 1px solid var(--pink);
            border-radius: 50px;
            padding: 0.3rem 0.8rem;
            width: fit-content;
            margin-bottom: 2rem;
        }

        .greeting-left h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1.2;
            color: var(--text);
            margin-bottom: 0;
        }

        .greeting-left h1 .name-highlight {
            color: var(--pink);
            font-style: italic;
            display: block;
        }

        .greeting-left-bottom {
            margin-top: 2.5rem;
        }

        .greeting-left-bottom p {
            color: var(--muted);
            font-size: 0.85rem;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        /* ── Right ── */
        .greeting-right {
            padding: 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: var(--bg);
        }

        .greeting-right-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }

        .greeting-step-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
        }

        .greeting-theme-btn {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 0.28rem 0.7rem;
            font-size: 0.72rem;
            font-family: 'DM Sans', sans-serif;
            color: var(--muted);
            cursor: pointer;
            transition: color 0.2s, border-color 0.2s;
        }

        .greeting-theme-btn:hover {
            color: var(--pink);
            border-color: var(--pink);
        }

        .greeting-preview {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
            justify-content: center;
            padding: 1rem 0;
        }

        .greeting-preview-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            margin-bottom: 0.3rem;
        }

        .preview-pill {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.7rem 1rem;
            font-size: 0.82rem;
            color: var(--text);
            transition: background 0.3s;
        }

        .preview-pill-icon {
            font-size: 1rem;
            flex-shrink: 0;
        }

        .preview-pill-text {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }

        .preview-pill-title {
            font-size: 0.78rem;
            color: var(--muted);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .preview-pill-val {
            color: var(--text);
            font-size: 0.85rem;
        }

        .greeting-right-bottom {
            margin-top: 2rem;
        }

        .greeting-right-bottom .btn {
            width: 100%;
            text-align: center;
            color: #fff;
        }

        .greeting-note {
            font-size: 0.75rem;
            color: var(--muted);
            text-align: center;
            margin-top: 0.8rem;
            font-style: italic;
        }

        /* ── Mobile ── */
        @media (max-width: 680px) {
            body.greeting-page {
                padding: 1rem;
                align-items: flex-start;
                padding-top: 2rem;
            }

            .greeting-wrapper {
                grid-template-columns: 1fr;
                border-radius: 18px;
            }

            .greeting-left {
                border-right: none;
                border-bottom: 1px solid var(--border);
                padding: 2rem 1.5rem;
            }

            .greeting-right {
                padding: 2rem 1.5rem;
            }

            .greeting-left h1 {
                font-size: 1.8rem;
            }

            .greeting-preview {
                padding: 0;
            }
        }

        @media (max-width: 400px) {
            .greeting-left,
            .greeting-right {
                padding: 1.5rem 1.2rem;
            }
        }
    </style>
</head>
<body class="greeting-page">

<div class="greeting-wrapper">

    <!-- ── Left ── -->
    <div class="greeting-left">
        <div>
            <div class="greeting-tag">✦ step 2 of 3</div>
            <h1>
                nice to meet you,
                <span class="name-highlight"><?= $name ?> </span>
            </h1>
        </div>

        <div class="greeting-left-bottom">
            <p>
                glad you made it here :D<br>
                just a few more questions and we're good to go.
                 mabilis lang tooo
            </p>
            <a href="preferences.php" class="btn btn-yes" style="color:#fff; display:block; text-align:center;">
                okay let's go baby
            </a>
        </div>
    </div>

    <!-- ── Right ── -->
    <div class="greeting-right">

        <div class="greeting-right-top">
            <span class="greeting-step-label">what's next</span>
            <button class="greeting-theme-btn" id="themeBtn" onclick="toggleTheme()">☀️ light</button>
        </div>

        <div class="greeting-preview">
            <p class="greeting-preview-label">coming up 👇</p>

            <div class="preview-pill">
                <span class="preview-pill-icon">🗓️</span>
                <div class="preview-pill-text">
                    <span class="preview-pill-title">date type</span>
                    <span class="preview-pill-val">what kind of date sounds fun to you</span>
                </div>
            </div>

            <div class="preview-pill">
                <span class="preview-pill-icon">⚡</span>
                <div class="preview-pill-text">
                    <span class="preview-pill-title">vibe check</span>
                    <span class="preview-pill-val">energy, mood, spontaneity level</span>
                </div>
            </div>

            <div class="preview-pill">
                <span class="preview-pill-icon">💬</span>
                <div class="preview-pill-text">
                    <span class="preview-pill-title">conversation</span>
                    <span class="preview-pill-val">how you like to talk and connect</span>
                </div>
            </div>

            <div class="preview-pill">
                <span class="preview-pill-icon">📍</span>
                <div class="preview-pill-text">
                    <span class="preview-pill-title">logistics</span>
                    <span class="preview-pill-val">curfew, parents, distance you're cool with</span>
                </div>
            </div>

            <div class="preview-pill">
                <span class="preview-pill-icon">✨</span>
                <div class="preview-pill-text">
                    <span class="preview-pill-title">activities</span>
                    <span class="preview-pill-val">coffee shop, night drive, arcade...</span>
                </div>
            </div>
        </div>

        <div class="greeting-note">
            (important research purposes 🔬)
        </div>

    </div>

</div>

<script src="js/main.js"></script>
<script>
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
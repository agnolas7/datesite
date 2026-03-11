<?php
session_start();
if (empty($_SESSION['response_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>date preferences 📋</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        (function() {
            const t = localStorage.getItem('siteTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <style>
        body.form-page {
            min-height: 100vh;
            padding: 0;
            align-items: stretch;
            display: flex;
            flex-direction: column;
        }

        .pref-layout {
            display: grid;
            grid-template-columns: 1fr 1.7fr;
            min-height: 100vh;
        }

        /* ── Left panel ── */
        .pref-side {
            background: var(--card);
            border-right: 1px solid var(--border);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
        }

        .pref-side-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pref-side-logo {
            font-size: 1.2rem;
            color: var(--pink);
            font-family: 'Playfair Display', serif;
        }

        .pref-side-nav-right {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .pref-step-label {
            font-size: 0.75rem;
            color: var(--muted);
            letter-spacing: 0.5px;
        }

        .pref-theme-btn {
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

        .pref-theme-btn:hover {
            color: var(--pink);
            border-color: var(--pink);
        }

        .pref-side-heading {
            flex: 0;
        }

        .pref-side-heading h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 2.5vw, 2.6rem);
            line-height: 1.2;
            color: var(--text);
            margin-bottom: 0.8rem;
        }

        .pref-side-heading p {
            color: var(--muted);
            font-size: 0.85rem;
            line-height: 1.7;
        }

        /* category list */
        .pref-side-visual {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 0.5rem;
            padding: 1.5rem 0;
        }

        .pref-category {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.6rem 0.8rem;
            border-radius: 10px;
            transition: background 0.2s;
        }

        .pref-category-icon {
            font-size: 1rem;
            width: 28px;
            text-align: center;
            flex-shrink: 0;
        }

        .pref-category-text {
            font-size: 0.8rem;
            color: var(--muted);
            transition: color 0.2s;
        }

        .pref-category.active .pref-category-text {
            color: var(--pink);
        }

        .pref-category.active {
            background: rgba(244, 167, 185, 0.06);
        }

        /* progress bar */
        .pref-side-bottom {
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }

        .pref-progress-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .pref-progress-label span {
            font-size: 0.72rem;
            color: var(--muted);
        }

        .pref-progress-label .pref-progress-count {
            color: var(--pink);
            font-weight: 500;
        }

        .pref-progress-track {
            width: 100%;
            height: 3px;
            background: var(--border);
            border-radius: 99px;
            overflow: hidden;
        }

        .pref-progress-fill {
            height: 100%;
            background: var(--pink);
            border-radius: 99px;
            width: 0%;
            transition: width 0.4s ease;
        }

        /* ── Right panel ── */
        .pref-main {
            padding: 3rem 3.5rem 5rem;
            overflow-y: auto;
            background: var(--bg);
        }

        .pref-main-inner {
            max-width: 520px;
        }

        .pref-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .pref-top-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            color: var(--text);
        }

        .pref-top-count {
            font-size: 0.78rem;
            color: var(--muted);
        }

        /* section dividers */
        .pref-section-divider {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--muted);
            margin: 2.2rem 0 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .pref-section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* override form-group label color inside here */
        .pref-main .form-group label {
            color: var(--text);
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            .pref-layout {
                grid-template-columns: 1fr;
                min-height: unset;
            }

            .pref-side {
                position: relative;
                height: auto;
                padding: 1.5rem 1.5rem 1.2rem;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .pref-side-visual {
                display: none;
            }

            .pref-side-bottom {
                display: none;
            }

            .pref-side-heading h1 {
                font-size: 1.4rem;
                margin-bottom: 0.3rem;
            }

            .pref-main {
                padding: 2rem 1.5rem 4rem;
            }
        }

        @media (max-width: 480px) {
            .pref-side {
                padding: 1.2rem 1.2rem 1rem;
            }

            .pref-main {
                padding: 1.5rem 1.2rem 4rem;
            }
        }
    </style>
</head>
<body class="form-page">

<div class="pref-layout">

    <!-- ── Left sticky panel ── -->
    <div class="pref-side">

        <div class="pref-side-nav">
            <span class="pref-side-logo">✦</span>
            <div class="pref-side-nav-right">
                <button class="pref-theme-btn" id="themeBtn" onclick="toggleTheme()">☀️ light</button>
                <span class="pref-step-label">step 3 of 3</span>
            </div>
        </div>

        <div class="pref-side-heading">
            <h1>the important questions 🔍</h1>
            <p>tell me how you like things.<br>i'll plan around you.</p>
        </div>

        <div class="pref-side-visual" id="categoryList">
            <div class="pref-category" data-section="date">
                <span class="pref-category-icon">🗓️</span>
                <span class="pref-category-text">date type & spontaneity</span>
            </div>
            <div class="pref-category" data-section="energy">
                <span class="pref-category-icon">🔋</span>
                <span class="pref-category-text">energy & mood</span>
            </div>
            <div class="pref-category" data-section="crowd">
                <span class="pref-category-icon">👥</span>
                <span class="pref-category-text">crowd & setting</span>
            </div>
            <div class="pref-category" data-section="convo">
                <span class="pref-category-icon">💬</span>
                <span class="pref-category-text">conversation style</span>
            </div>
            <div class="pref-category" data-section="vibes">
                <span class="pref-category-icon">✨</span>
                <span class="pref-category-text">vibes & activities</span>
            </div>
        </div>

        <div class="pref-side-bottom">
            <div class="pref-progress-label">
                <span>answered</span>
                <span class="pref-progress-count" id="progressCount">0 / 11</span>
            </div>
            <div class="pref-progress-track">
                <div class="pref-progress-fill" id="progressFill"></div>
            </div>
        </div>

    </div>

    <!-- ── Right main panel ── -->
    <div class="pref-main">
        <div class="pref-main-inner">

            <div class="pref-top-bar">
                <span class="pref-top-title">date preferences</span>
                <span class="pref-top-count">answer all to continue</span>
            </div>

            <form action="save_preferences.php" method="POST" id="prefForm">

                <!-- DATE TYPE -->
                <div class="pref-section-divider" data-section="date">🗓️ the date itself</div>

                <div class="form-group" data-field="date_type">
                    <label>What kind of date sounds best?</label>
                    <div class="radio-group">
                        <?php foreach (['Cozy indoor date','Outdoor adventure','Food trip','Nice view','Random spontaneous hang out','Surprise me'] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="date_type" value="<?= htmlspecialchars($opt) ?>"> <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group" data-field="spontaneity">
                    <label>Spontaneity level ⚡</label>
                    <div class="radio-group">
                        <?php foreach (['Yes please','A little structure',"Let's wing it",'Chaos'] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="spontaneity" value="<?= htmlspecialchars($opt) ?>"> <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- ENERGY & MOOD -->
                <div class="pref-section-divider" data-section="energy">🔋 energy & mood</div>

                <div class="form-group" data-field="energy">
                    <label>Energy level</label>
                    <div class="radio-group">
                        <?php foreach (['Chill','Medium','Active','Illegal activities (joke)'] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="energy" value="<?= htmlspecialchars($opt) ?>"> <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group" data-field="mood">
                    <label>First date mood 🌙</label>
                    <div class="radio-group">
                        <?php foreach (['Relaxed','Playful','Adventurous','Slightly awkward but fun'] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="mood" value="<?= htmlspecialchars($opt) ?>"> <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- CROWD & SETTING -->
                <div class="pref-section-divider" data-section="crowd">👥 crowd & setting</div>

                <div class="form-group" data-field="crowd">
                    <label>Crowd preference</label>
                    <div class="radio-group">
                        <?php foreach (['Quiet','Some people','Busy',"Doesn't matter"] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="crowd" value="<?= htmlspecialchars($opt) ?>"> <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group" data-field="walking">
                    <label>Walking tolerance 👟</label>
                    <div class="radio-group">
                        <?php foreach (['Minimal','Some walking','A lot','If we get lost we get lost'] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="walking" value="<?= htmlspecialchars($opt) ?>"> <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- CONVERSATION -->
                <div class="pref-section-divider" data-section="convo">💬 conversation</div>

                <div class="form-group" data-field="convo_style">
                    <label>Conversation style</label>
                    <div class="radio-group">
                        <?php foreach (['Deep talks','Random funny stuff','Getting to know each other','Bahala na'] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="convo_style" value="<?= htmlspecialchars($opt) ?>"> <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group" data-field="awkwardness">
                    <label>Awkwardness level 😅</label>
                    <div class="radio-group">
                        <?php foreach (['Very','A little','Smooth',"I'll carry the conversation"] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="awkwardness" value="<?= htmlspecialchars($opt) ?>"> <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group" data-field="convo_difficulty">
                    <label>Conversation difficulty 🎮</label>
                    <div class="radio-group">
                        <?php foreach (['Easy mode','Medium difficulty','Hard mode','Legendary boss fight'] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="convo_difficulty" value="<?= htmlspecialchars($opt) ?>"> <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- VIBES -->
                <div class="pref-section-divider" data-section="vibes">✨ vibes & activities</div>

                <div class="form-group" data-field="vibes">
                    <label>Vibe check — pick all that apply</label>
                    <div class="checkbox-group">
                        <?php foreach (['Coffee shop','Night drive','Arcade / games','Watch a movie','Street food crawl','Stroll','Parking lot hangout','Beer and smoke','Nature','Dinner','Lunch','Creative activities'] as $v): ?>
                        <label class="check-item">
                            <input type="checkbox" name="vibes[]" value="<?= htmlspecialchars($v) ?>"> <?= htmlspecialchars($v) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group" data-field="custom_vibe">
                    <label>Your own idea? 💡</label>
                    <input type="text" name="custom_vibe" placeholder="suggest something...">
                </div>

                <button type="submit" class="btn btn-yes" style="width:100%; margin-top:2rem; color:#fff;">
                    done na po
                </button>

            </form>
        </div>
    </div>

</div>

<script src="js/main.js"></script>
<script>
// ── Theme toggle ──
const themeBtn = document.getElementById('themeBtn');
const savedTheme = localStorage.getItem('siteTheme') || 'dark';
themeBtn.textContent = savedTheme === 'light' ? '🌙 dark' : '☀️ light';

function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme');
    const next = current === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('siteTheme', next);
    themeBtn.textContent = next === 'light' ? '🌙 dark' : '☀️ light';
}

// ── Progress tracker ──
const radioFields = [
    'date_type','spontaneity','energy','mood',
    'crowd','walking','convo_style','awkwardness','convo_difficulty'
];
const totalRequired = 9; // 9 radio groups (vibes and custom are optional)

function updateProgress() {
    let answered = 0;

    radioFields.forEach(name => {
        if (document.querySelector(`input[name="${name}"]:checked`)) answered++;
    });

    const pct = Math.round((answered / totalRequired) * 100);
    document.getElementById('progressFill').style.width = pct + '%';
    document.getElementById('progressCount').textContent = answered + ' / ' + totalRequired;
}

// ── Active section highlight on scroll ──
function updateActiveSection() {
    const dividers = document.querySelectorAll('.pref-section-divider');
    const scrollY  = document.querySelector('.pref-main').scrollTop;

    let current = null;
    dividers.forEach(d => {
        const top = d.offsetTop - 100;
        if (scrollY >= top) current = d.dataset.section;
    });

    document.querySelectorAll('.pref-category').forEach(cat => {
        cat.classList.toggle('active', cat.dataset.section === current);
    });
}

document.querySelectorAll('input[type="radio"]').forEach(el => {
    el.addEventListener('change', updateProgress);
});

document.querySelector('.pref-main').addEventListener('scroll', updateActiveSection);

updateProgress();
updateActiveSection();
</script>
</body>
</html>
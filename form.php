<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tell me about yourself ✨</title>
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

        .form-layout {
            display: grid;
            grid-template-columns: 1fr 1.7fr;
            min-height: 100vh;
        }

        .form-side-panel {
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

        .side-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .side-nav-logo {
            font-size: 1.2rem;
            color: var(--pink);
            font-family: 'Playfair Display', serif;
        }

        .side-nav-right {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .side-nav-step {
            font-size: 0.75rem;
            color: var(--muted);
            letter-spacing: 0.5px;
        }

        .side-theme-btn {
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

        .side-theme-btn:hover {
            color: var(--pink);
            border-color: var(--pink);
        }

        .side-heading { flex: 0; }

        .side-heading h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 2.5vw, 2.6rem);
            line-height: 1.2;
            color: var(--text);
            margin-bottom: 0.8rem;
        }

        .side-heading p {
            color: var(--muted);
            font-size: 0.85rem;
            line-height: 1.7;
        }

        .side-visual {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem 0;
            overflow-y: auto;
        }

        .side-steps {
            display: flex;
            flex-direction: column;
            gap: 0;
            width: 100%;
        }

        .side-step-row {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.5rem 0;
            position: relative;
        }

        .side-step-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--input-bg);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            flex-shrink: 0;
            transition: background 0.3s, border-color 0.3s;
            position: relative;
            z-index: 1;
        }

        .side-step-icon.done {
            background: var(--pink);
            border-color: var(--pink);
        }

        .side-step-label {
            font-size: 0.78rem;
            color: var(--muted);
            transition: color 0.3s;
        }

        .side-step-row.done .side-step-label {
            color: var(--text);
        }

        .side-step-row:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 14px;
            top: calc(50% + 15px);
            width: 1px;
            height: calc(100% - 14px);
            background: var(--border);
            z-index: 0;
        }

        .side-bottom {
            padding-top: 1rem;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }

        .side-bottom-quote {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 0.78rem;
            color: var(--pink);
            line-height: 1.6;
            opacity: 0.8;
        }

        .side-bottom-author {
            font-size: 0.7rem;
            color: var(--muted);
            margin-top: 0.3rem;
        }

        .form-main-panel {
            padding: 3rem 3.5rem 5rem;
            overflow-y: auto;
            background: var(--bg);
        }

        .form-main-panel .form-container {
            border: none;
            background: transparent;
            padding: 0;
            max-width: 500px;
            animation: none;
        }

        .form-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2.5rem;
        }

        .form-top-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
        }

        .form-progress-bar-wrap {
            width: 120px;
            height: 3px;
            background: var(--border);
            border-radius: 99px;
            overflow: hidden;
        }

        .form-progress-bar-fill {
            height: 100%;
            background: var(--pink);
            border-radius: 99px;
            width: 0%;
            transition: width 0.4s ease;
        }

        .form-section-divider {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--muted);
            margin: 2rem 0 1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .form-section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .custom-field-input {
            display: none;
            margin-top: 0.8rem;
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .custom-field-input.visible {
            display: block;
            animation: fadeUp 0.2s ease;
        }

        .custom-field-input:focus { border-color: var(--pink); }

        .optional-label {
            color: var(--muted);
            font-size: 0.78rem;
            font-weight: 400;
        }

        @media (max-width: 768px) {
            .form-layout { grid-template-columns: 1fr; min-height: unset; }
            .form-side-panel {
                position: relative; height: auto;
                padding: 1.5rem 1.5rem 1.2rem;
                border-right: none; border-bottom: 1px solid var(--border);
            }
            .side-visual, .side-bottom { display: none; }
            .side-heading h1 { font-size: 1.4rem; margin-bottom: 0.3rem; }
            .form-main-panel { padding: 2rem 1.5rem 4rem; }
        }

        @media (max-width: 480px) {
            .form-side-panel { padding: 1.2rem 1.2rem 1rem; }
            .form-main-panel { padding: 1.5rem 1.2rem 4rem; }
        }
    </style>
</head>
<body class="form-page">

<div class="form-layout">

    <!-- ── Left panel ── -->
    <div class="form-side-panel">
        <div class="side-nav">
            <span class="side-nav-logo">✦</span>
            <div class="side-nav-right">
                <button class="side-theme-btn" id="themeBtn" onclick="toggleTheme()">☀️ light</button>
                <span class="side-nav-step">step 1 of 3</span>
            </div>
        </div>

        <div class="side-heading">
            <h1>let's get to know you 💌</h1>
            <p>fill this out honestly<br>i actually read these :D</p>
        </div>

        <div class="side-visual">
            <div class="side-steps">
                <div class="side-step-row" id="step-row-0">
                    <div class="side-step-icon" id="step-icon-0">👤</div>
                    <span class="side-step-label">name & age</span>
                </div>
                <div class="side-step-row" id="step-row-1">
                    <div class="side-step-icon" id="step-icon-1">📍</div>
                    <span class="side-step-label">your location</span>
                </div>
                <div class="side-step-row" id="step-row-2">
                    <div class="side-step-icon" id="step-icon-2">💬</div>
                    <span class="side-step-label">how to reach you</span>
                </div>
                <div class="side-step-row" id="step-row-3">
                    <div class="side-step-icon" id="step-icon-3">🕐</div>
                    <span class="side-step-label">best time</span>
                </div>
                <div class="side-step-row" id="step-row-4">
                    <div class="side-step-icon" id="step-icon-4">🍜</div>
                    <span class="side-step-label">food & drinks</span>
                </div>
                <div class="side-step-row" id="step-row-5">
                    <div class="side-step-icon" id="step-icon-5">🌸</div>
                    <span class="side-step-label">flower</span>
                </div>
                <div class="side-step-row" id="step-row-6">
                    <div class="side-step-icon" id="step-icon-6">🌡️</div>
                    <span class="side-step-label">temperature</span>
                </div>
                <div class="side-step-row" id="step-row-7">
                    <div class="side-step-icon" id="step-icon-7">🍰</div>
                    <span class="side-step-label">dessert</span>
                </div>
                <div class="side-step-row" id="step-row-8">
                    <div class="side-step-icon" id="step-icon-8">🚩</div>
                    <span class="side-step-label">dealbreaker</span>
                </div>
            </div>
        </div>

        <div class="side-bottom">
            <p class="side-bottom-quote">"every answer helps me plan a better date for us."</p>
            <p class="side-bottom-author">— the person who made this site</p>
        </div>
    </div>

    <!-- ── Right panel ── -->
    <div class="form-main-panel">
        <div class="form-container">

            <div class="form-top-bar">
                <span class="form-top-label">about you</span>
                <div class="form-progress-bar-wrap">
                    <div class="form-progress-bar-fill" id="progressBar"></div>
                </div>
            </div>

            <div class="validation-banner hidden" id="validationBanner">
                <span class="validation-icon">⚠️</span>
                <span>please answer all questions before continuing!</span>
            </div>

            <form id="mainForm" action="save_form.php" method="POST" novalidate>

                <!-- ── THE BASICS ── -->
                <div class="form-section-divider">who are you 👤</div>

                <div class="form-group" id="group-name">
                    <label>Name / Nickname <span class="required-star">*</span></label>
                    <input type="text" name="name" id="field-name" placeholder="what do i call you?">
                    <span class="field-error hidden">this one's required, kahit nickname lang boss</span>
                </div>

                <div class="form-group" id="group-age">
                    <label>Age <span class="required-star">*</span></label>
                    <select name="age" id="field-age">
                        <option value="">-- pick one --</option>
                        <option>19</option>
                        <option>20</option>
                        <option>21</option>
                        <option>22</option>
                        <option>23+</option>
                    </select>
                    <span class="field-error hidden">please pick your age, hard pass sa minor</span>
                </div>

                <div class="form-group" id="group-city">
                    <label>City / Location <span class="required-star">*</span></label>
                    <input type="text" name="city" id="field-city" placeholder="or where should i pick you up?">
                    <span class="field-error hidden">kahit kung san lang magmemeet g</span>
                </div>

                <!-- ── HOW DO WE MEET ── -->
                <div class="form-section-divider">how do we meet 📲</div>

                <div class="form-group" id="group-communication">
                    <label>Preferred communication <span class="required-star">*</span></label>
                    <div class="checkbox-group">
                        <?php
                        $comms = ['Instagram','Messenger','Text','Twitter','Telegram','Shopee message','I\'ll just pull up to your house','liham','In our dreams'];
                        foreach($comms as $c) {
                            echo "<label class='check-item'><input type='checkbox' name='communication[]' value='$c'> $c</label>";
                        }
                        ?>
                    </div>
                    <span class="field-error hidden">pick at least one way to reach you pls</span>
                </div>

                <div class="form-group" id="group-best_time">
                    <label>Best time for a date <span class="required-star">*</span></label>
                    <div class="radio-group">
                        <?php
                        $times = ['Morning coffee','Afternoon hangout','Sunset','Night vibes'];
                        foreach($times as $t) {
                            echo "<label class='radio-item'><input type='radio' name='best_time' value='$t'> $t</label>";
                        }
                        ?>
                    </div>
                    <span class="field-error hidden">pick a time that works for you pls</span>
                </div>

                <!-- ── BEFORE WE MEET ── -->
                <div class="form-section-divider">before we eat 🍜</div>

                <div class="form-group" id="group-food_drink">
                    <label>Favorite food and drink 🍜 <span class="required-star">*</span></label>
                    <input type="text" name="food_drink" id="field-food_drink" placeholder="tell me what you like...">
                    <span class="field-error hidden">food is important!! tell me</span>
                </div>

                <div class="form-group" id="group-flower">
                    <label>If someone randomly handed you a flower, which one would make you smile the most? 🌸 <span class="required-star">*</span></label>
                    <div class="radio-group">
                        <?php
                        $flowers = ['Sunflower 🌻','Tulip 🌷','Rose 🌹','Daisy 🌼','Lily 🤍'];
                        foreach($flowers as $f) {
                            echo "<label class='radio-item'><input type='radio' name='flower' value='$f'> $f</label>";
                        }
                        ?>
                        <label class="radio-item">
                            <input type="radio" name="flower" value="__custom_flower__" id="flower-custom-radio">
                            something else...
                        </label>
                    </div>
                    <input type="text" id="flower-custom-input" class="custom-field-input" placeholder="what flower? 🌿">
                    <span class="field-error hidden">pick one pls 🌸</span>
                </div>

                <div class="form-group" id="group-craving">
                    <label>Anything you're craving lately? 🍰 <span class="optional-label">(optional)</span></label>
                    <input type="text" name="craving" id="field-craving" placeholder="food, dessert, drink, anything...">
                </div>

                <div class="form-group" id="group-temperature">
                    <label>Temperature preference 🌡️ <span class="required-star">*</span></label>
                    <div class="radio-group">
                        <?php
                        $temps = ['I get cold easily 🥶','I\'m usually fine','I like cold places ❄️','I get hot easily 🥵','Doesn\'t matter'];
                        foreach($temps as $t) {
                            echo "<label class='radio-item'><input type='radio' name='temperature' value='$t'> $t</label>";
                        }
                        ?>
                    </div>
                    <span class="field-error hidden">this helps me plan where to take you 🌡️</span>
                </div>

                <div class="form-group" id="group-dislikes">
                    <label>Anything you don't enjoy when going out? <span class="optional-label">(optional)</span></label>
                    <div class="checkbox-group">
                        <?php
                        $dislikes = ['Loud places','Very crowded spots','Long waiting lines','Walking too much','None really'];
                        foreach($dislikes as $d) {
                            echo "<label class='check-item'><input type='checkbox' name='dislikes[]' value='$d'> $d</label>";
                        }
                        ?>
                    </div>
                </div>

                <div class="form-group" id="group-dessert">
                    <label>Dessert situation 🍨 <span class="required-star">*</span></label>
                    <div class="radio-group">
                        <?php
                        $desserts = ['Ice cream 🍦','Cake 🎂','Pastries 🥐','Chocolate 🍫','Not really a dessert person'];
                        foreach($desserts as $d) {
                            echo "<label class='radio-item'><input type='radio' name='dessert' value='$d'> $d</label>";
                        }
                        ?>
                    </div>
                    <span class="field-error hidden">kahit isang sagot lang 😄</span>
                </div>

                <div class="form-group" id="group-dealbreaker">
                    <label>Dealbreaker for you? <span class="required-star">*</span></label>
                    <div class="radio-group">
                        <?php
                        $deals = ['Bad music taste','Pineapple on pizza','Slow walkers','None I\'m chill'];
                        foreach($deals as $d) {
                            echo "<label class='radio-item'><input type='radio' name='dealbreaker' value='$d'> $d</label>";
                        }
                        ?>
                        <label class="radio-item">
                            <input type="radio" name="dealbreaker" value="__custom__" id="dealbreaker-custom-radio">
                            something else...
                        </label>
                    </div>
                    <input type="text" id="dealbreaker-custom-input" class="custom-field-input" placeholder="tell me your dealbreaker...">
                    <span class="field-error hidden">be honest nyahaha</span>
                </div>

                <button type="submit" class="btn btn-yes" style="width:100%; margin-top:2rem; color:#fff;">
                    submit 🌸
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

// ── Custom text inputs ──
const dealbreakerInput = document.getElementById('dealbreaker-custom-input');
const flowerInput      = document.getElementById('flower-custom-input');

function bindCustomRadio(radioName, triggerValue, textInput) {
    document.querySelectorAll(`input[name="${radioName}"]`).forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === triggerValue) {
                textInput.classList.add('visible');
                textInput.focus();
            } else {
                textInput.classList.remove('visible');
                textInput.value = '';
            }
            updateProgress();
        });
    });
}

bindCustomRadio('dealbreaker', '__custom__',        dealbreakerInput);
bindCustomRadio('flower',      '__custom_flower__', flowerInput);

// ── Step tracker config ──
// stepIndex matches the side-step-row IDs in the HTML
const fieldStepMap = [
    { stepIndex: 0, test: () => !!document.getElementById('field-name')?.value.trim() && !!document.getElementById('field-age')?.value },
    { stepIndex: 1, test: () => !!document.getElementById('field-city')?.value.trim() },
    { stepIndex: 2, test: () => document.querySelectorAll('input[name="communication[]"]:checked').length > 0 },
    { stepIndex: 3, test: () => !!document.querySelector('input[name="best_time"]:checked') },
    { stepIndex: 4, test: () => !!document.getElementById('field-food_drink')?.value.trim() },
    { stepIndex: 5, test: () => {
        const c = document.querySelector('input[name="flower"]:checked');
        if (!c) return false;
        if (c.value === '__custom_flower__') return flowerInput.value.trim().length > 0;
        return true;
    }},
    { stepIndex: 6, test: () => !!document.querySelector('input[name="temperature"]:checked') },
    { stepIndex: 7, test: () => !!document.querySelector('input[name="dessert"]:checked') },
    { stepIndex: 8, test: () => {
        const c = document.querySelector('input[name="dealbreaker"]:checked');
        if (!c) return false;
        if (c.value === '__custom__') return dealbreakerInput.value.trim().length > 0;
        return true;
    }},
];

const stepEmojis   = ['👤','📍','💬','🕐','🍜','🌸','🌡️','🍰','🚩'];
const totalRequired = 9; // number of required fields

function updateProgress() {
    let filled = 0;
    fieldStepMap.forEach(item => {
        const done = item.test();
        const icon = document.getElementById('step-icon-' + item.stepIndex);
        const row  = document.getElementById('step-row-' + item.stepIndex);
        if (icon && row) {
            if (done) {
                icon.classList.add('done');
                icon.textContent = '✓';
                row.classList.add('done');
            } else {
                icon.classList.remove('done');
                row.classList.remove('done');
                icon.textContent = stepEmojis[item.stepIndex];
            }
        }
        if (done) filled++;
    });
    document.getElementById('progressBar').style.width =
        Math.round((filled / totalRequired) * 100) + '%';
}

document.querySelectorAll('input, select').forEach(el => el.addEventListener('change', updateProgress));
document.querySelectorAll('input[type="text"]').forEach(el => el.addEventListener('input', updateProgress));
dealbreakerInput.addEventListener('input', updateProgress);
flowerInput.addEventListener('input', updateProgress);
updateProgress();

// ── Validation on submit ──
document.getElementById('mainForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let hasError = false;

    document.querySelectorAll('.form-group').forEach(g => g.classList.remove('has-error'));
    document.querySelectorAll('.field-error').forEach(el => el.classList.add('hidden'));
    document.getElementById('validationBanner').classList.add('hidden');

    const checks = [
        { id: 'group-name',          test: () => document.getElementById('field-name').value.trim() },
        { id: 'group-age',           test: () => document.getElementById('field-age').value },
        { id: 'group-city',          test: () => document.getElementById('field-city').value.trim() },
        { id: 'group-communication', test: () => document.querySelectorAll('input[name="communication[]"]:checked').length > 0 },
        { id: 'group-best_time',     test: () => document.querySelector('input[name="best_time"]:checked') },
        { id: 'group-food_drink',    test: () => document.getElementById('field-food_drink').value.trim() },
        {
            id: 'group-flower',
            test: () => {
                const checked = document.querySelector('input[name="flower"]:checked');
                if (!checked) return false;
                if (checked.value === '__custom_flower__') {
                    const val = flowerInput.value.trim();
                    if (!val) return false;
                    // swap so the text gets submitted instead of "__custom_flower__"
                    flowerInput.setAttribute('name', 'flower');
                    flowerInput.setAttribute('type', 'hidden');
                    checked.removeAttribute('name');
                }
                return true;
            }
        },
        { id: 'group-temperature', test: () => document.querySelector('input[name="temperature"]:checked') },
        { id: 'group-dessert',     test: () => document.querySelector('input[name="dessert"]:checked') },
        {
            id: 'group-dealbreaker',
            test: () => {
                const checked = document.querySelector('input[name="dealbreaker"]:checked');
                if (!checked) return false;
                if (checked.value === '__custom__') {
                    const val = dealbreakerInput.value.trim();
                    if (!val) return false;
                    dealbreakerInput.setAttribute('name', 'dealbreaker');
                    dealbreakerInput.setAttribute('type', 'hidden');
                    checked.removeAttribute('name');
                }
                return true;
            }
        },
    ];

    checks.forEach(({ id, test }) => {
        if (!test()) {
            document.getElementById(id).classList.add('has-error');
            document.getElementById(id).querySelector('.field-error').classList.remove('hidden');
            hasError = true;
        }
    });

    if (hasError) {
        document.getElementById('validationBanner').classList.remove('hidden');
        document.querySelector('.form-group.has-error')
            ?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    this.submit();
});

// ── Live error clearing ──
document.querySelectorAll('input, select').forEach(input => {
    input.addEventListener('change', function() {
        const group = this.closest('.form-group');
        if (group?.classList.contains('has-error')) {
            group.classList.remove('has-error');
            group.querySelector('.field-error').classList.add('hidden');
            if (!document.querySelector('.form-group.has-error')) {
                document.getElementById('validationBanner').classList.add('hidden');
            }
        }
    });
});

document.querySelectorAll('input[type="text"]').forEach(input => {
    input.addEventListener('input', function() {
        const group = this.closest('.form-group');
        if (group?.classList.contains('has-error') && this.value.trim()) {
            group.classList.remove('has-error');
            group.querySelector('.field-error').classList.add('hidden');
            if (!document.querySelector('.form-group.has-error')) {
                document.getElementById('validationBanner').classList.add('hidden');
            }
        }
    });
});
</script>
</body>
</html>
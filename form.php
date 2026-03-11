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

        /* ── Left panel ── */
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
            margin-bottom: 0;
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

        .side-heading {
            flex: 0;
        }

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
            padding: 1.5rem 0;
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
            gap: 1rem;
            padding: 0.7rem 0;
            position: relative;
        }

        .side-step-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--input-bg);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
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
            font-size: 0.82rem;
            color: var(--muted);
            transition: color 0.3s;
        }

        .side-step-row.done .side-step-label {
            color: var(--text);
        }

        .side-step-row:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 17px;
            top: calc(50% + 18px);
            width: 1px;
            height: calc(100% - 18px);
            background: var(--border);
            z-index: 0;
        }

        .side-bottom {
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }

        .side-bottom-quote {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 0.82rem;
            color: var(--pink);
            line-height: 1.7;
            opacity: 0.8;
        }

        .side-bottom-author {
            font-size: 0.72rem;
            color: var(--muted);
            margin-top: 0.3rem;
        }

        /* ── Right panel ── */
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

        /* ── Custom dealbreaker input ── */
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
            transition: border-color 0.2s, opacity 0.2s;
        }

        .custom-field-input.visible {
            display: block;
            animation: fadeUp 0.2s ease;
        }

        .custom-field-input:focus {
            border-color: var(--pink);
        }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            .form-layout {
                grid-template-columns: 1fr;
                min-height: unset;
            }

            .form-side-panel {
                position: relative;
                height: auto;
                padding: 1.5rem 1.5rem 1.2rem;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .side-visual,
            .side-bottom {
                display: none;
            }

            .side-heading h1 {
                font-size: 1.4rem;
                margin-bottom: 0.3rem;
            }

            .side-nav {
                margin-bottom: 0.8rem;
            }

            .form-main-panel {
                padding: 2rem 1.5rem 4rem;
            }
        }

        @media (max-width: 480px) {
            .form-side-panel {
                padding: 1.2rem 1.2rem 1rem;
            }

            .form-main-panel {
                padding: 1.5rem 1.2rem 4rem;
            }
        }
    </style>
</head>
<body class="form-page">

<div class="form-layout">

    <!-- ── Left sticky panel ── -->
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
            <p>fill this out honestly <br>i actually read these :D</p>
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
                    <span class="side-step-label">best time for a date</span>
                </div>
                <div class="side-step-row" id="step-row-4">
                    <div class="side-step-icon" id="step-icon-4">🍜</div>
                    <span class="side-step-label">food & drinks</span>
                </div>
                <div class="side-step-row" id="step-row-5">
                    <div class="side-step-icon" id="step-icon-5">🚩</div>
                    <span class="side-step-label">dealbreakers</span>
                </div>
            </div>
        </div>

        <div class="side-bottom">
            <p class="side-bottom-quote">"every answer helps me plan a better date for us."</p>
            <p class="side-bottom-author">— the person who made this site</p>
        </div>

    </div>

    <!-- ── Right form panel ── -->
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

                <div class="form-section-divider">the basics</div>

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

                <div class="form-section-divider">how do we meet</div>

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

                <div class="form-section-divider">before we meet</div>

                <div class="form-group" id="group-food_drink">
                    <label>Favorite food and drink 🍜 <span class="required-star">*</span></label>
                    <input type="text" name="food_drink" id="field-food_drink" placeholder="tell me what you like...">
                    <span class="field-error hidden">food is important!! tell me</span>
                </div>

                <div class="form-group" id="group-dealbreaker">
                    <label>Dealbreaker for you? <span class="required-star">*</span></label>
                    <div class="radio-group">
                        <?php
                        $deals = ['Bad music taste','Pineapple on pizza','Slow walkers','None I\'m chill'];
                        foreach($deals as $d) {
                            echo "<label class='check-item'><input type='radio' name='dealbreaker' value='$d'> $d</label>";
                        }
                        ?>
                        <label class="check-item">
                            <input type="radio" name="dealbreaker" value="__custom__" id="dealbreaker-custom-radio">
                            something else...
                        </label>
                    </div>
                    <input
                        type="text"
                        id="dealbreaker-custom-input"
                        class="custom-field-input"
                        placeholder="tell me your dealbreaker...">
                    <span class="field-error hidden">be honest nyahaha</span>
                </div>

                <button type="submit" class="btn btn-yes" style="width:100%; margin-top:2rem; color:#fff;">
                    submit 
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

// ── Dealbreaker custom input ──
const customRadio = document.getElementById('dealbreaker-custom-radio');
const customInput = document.getElementById('dealbreaker-custom-input');

document.querySelectorAll('input[name="dealbreaker"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.value === '__custom__') {
            customInput.classList.add('visible');
            customInput.focus();
        } else {
            customInput.classList.remove('visible');
            customInput.value = '';
        }
        updateProgress();
    });
});

// ── Progress & step tracker ──
const fieldStepMap = [
    { fields: ['field-name', 'field-age'],  stepIndex: 0 },
    { fields: ['field-city'],               stepIndex: 1 },
    { fields: ['communication[]'],          stepIndex: 2, type: 'checkbox' },
    { fields: ['best_time'],                stepIndex: 3, type: 'radio' },
    { fields: ['field-food_drink'],         stepIndex: 4 },
    { fields: ['dealbreaker'],              stepIndex: 5, type: 'radio' },
];

const totalFields = 7;
const stepEmojis  = ['👤','📍','💬','🕐','🍜','🚩'];

function getFieldValue(item) {
    if (item.type === 'checkbox') {
        return document.querySelectorAll(`input[name="${item.fields[0]}"]:checked`).length > 0;
    }
    if (item.type === 'radio') {
        const checked = document.querySelector(`input[name="${item.fields[0]}"]:checked`);
        if (!checked) return false;
        // if custom selected, require text too
        if (checked.value === '__custom__') return customInput.value.trim().length > 0;
        return true;
    }
    return item.fields.every(id => {
        const el = document.getElementById(id);
        return el && el.value.trim();
    });
}

function updateProgress() {
    const filled = [
        !!document.getElementById('field-name')?.value.trim(),
        !!document.getElementById('field-age')?.value,
        !!document.getElementById('field-city')?.value.trim(),
        document.querySelectorAll('input[name="communication[]"]:checked').length > 0,
        !!document.querySelector('input[name="best_time"]:checked'),
        !!document.getElementById('field-food_drink')?.value.trim(),
        (() => {
            const checked = document.querySelector('input[name="dealbreaker"]:checked');
            if (!checked) return false;
            if (checked.value === '__custom__') return customInput.value.trim().length > 0;
            return true;
        })(),
    ];

    const count = filled.filter(Boolean).length;
    document.getElementById('progressBar').style.width = Math.round((count / totalFields) * 100) + '%';

    fieldStepMap.forEach((item) => {
        const isDone = getFieldValue(item);
        const icon   = document.getElementById('step-icon-' + item.stepIndex);
        const row    = document.getElementById('step-row-' + item.stepIndex);
        if (!icon || !row) return;

        if (isDone) {
            icon.classList.add('done');
            icon.textContent = '✓';
            row.classList.add('done');
        } else {
            icon.classList.remove('done');
            row.classList.remove('done');
            icon.textContent = stepEmojis[item.stepIndex];
        }
    });
}

document.querySelectorAll('input, select').forEach(el => el.addEventListener('change', updateProgress));
document.querySelectorAll('input[type="text"]').forEach(el => el.addEventListener('input', updateProgress));
customInput.addEventListener('input', updateProgress);
updateProgress();

// ── Validation ──
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
            id: 'group-dealbreaker',
            test: () => {
                const checked = document.querySelector('input[name="dealbreaker"]:checked');
                if (!checked) return false;
                if (checked.value === '__custom__') {
                    const val = customInput.value.trim();
                    if (!val) return false;
                    // inject as hidden so the custom text is what gets submitted
                    customInput.setAttribute('name', 'dealbreaker');
                    customInput.setAttribute('type', 'hidden');
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
        document.querySelector('.form-group.has-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    this.submit();
});

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
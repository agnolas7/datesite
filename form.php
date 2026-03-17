<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tell me about yourself</title>
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
            justify-content: flex-start;
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

        /* ── Time preview message ── */
        .time-preview {
            margin-top: 0.8rem;
            padding: 0.75rem 1rem;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 0.82rem;
            color: var(--muted);
            font-style: italic;
            line-height: 1.6;
            display: none;
            animation: fadeUp 0.25s ease;
        }

        .time-preview.show { display: block; }

        /* ── Flower follow-up sections ── */
        .flower-followup {
            display: none;
            margin-top: 1rem;
            animation: fadeUp 0.25s ease;
        }

        .flower-followup.show { display: block; }

        .flower-response-note {
            padding: 0.75rem 1rem;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 0.82rem;
            color: var(--muted);
            font-style: italic;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        /* ── Scrollbar ── */
        :root[data-theme="dark"] {
            scrollbar-width: thin;
            scrollbar-color: #3a3a3a var(--bg);
        }

        :root[data-theme="dark"] ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        :root[data-theme="dark"] ::-webkit-scrollbar-track {
            background: var(--bg);
        }

        :root[data-theme="dark"] ::-webkit-scrollbar-thumb {
            background: #3a3a3a;
            border-radius: 99px;
        }

        :root[data-theme="dark"] ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* ── Select dropdown ── */
        :root[data-theme="dark"] select {
            background: var(--input-bg);
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23888' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
            cursor: pointer;
            transition: border-color 0.2s;
            width: 100%;
        }

        :root[data-theme="dark"] select:focus {
            border-color: var(--pink);
        }

        :root[data-theme="dark"] select option {
            background: #1f1f1f;
            color: #f0ece4;
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

    <!-- Left panel -->
    <div class="form-side-panel">
        <div class="side-nav">
            <span class="side-nav-logo">✦</span>
            <div class="side-nav-right">
                <button class="side-theme-btn" id="themeBtn" onclick="toggleTheme()">light</button>
                <span class="side-nav-step">step 1 of 3</span>
            </div>
        </div>

        <div class="side-heading">
            <h1>let's get to know you</h1>
            <p>fill this out honestly.<br>i actually read these.</p>
        </div>

        <div class="side-visual">
            <div class="side-steps">
                <div class="side-step-row" id="step-row-0">
                    <div class="side-step-icon" id="step-icon-0">1</div>
                    <span class="side-step-label">name & age</span>
                </div>
                <div class="side-step-row" id="step-row-1">
                    <div class="side-step-icon" id="step-icon-1">2</div>
                    <span class="side-step-label">where you're from</span>
                </div>
                <div class="side-step-row" id="step-row-2">
                    <div class="side-step-icon" id="step-icon-2">3</div>
                    <span class="side-step-label">how to reach you</span>
                </div>
                <div class="side-step-row" id="step-row-3">
                    <div class="side-step-icon" id="step-icon-3">4</div>
                    <span class="side-step-label">best time</span>
                </div>
                <div class="side-step-row" id="step-row-4">
                    <div class="side-step-icon" id="step-icon-4">5</div>
                    <span class="side-step-label">food & drinks</span>
                </div>
                <div class="side-step-row" id="step-row-5">
                    <div class="side-step-icon" id="step-icon-5">6</div>
                    <span class="side-step-label">flower</span>
                </div>
                <div class="side-step-row" id="step-row-6">
                    <div class="side-step-icon" id="step-icon-6">7</div>
                    <span class="side-step-label">temperature</span>
                </div>
                <div class="side-step-row" id="step-row-7">
                    <div class="side-step-icon" id="step-icon-7">8</div>
                    <span class="side-step-label">dessert</span>
                </div>
                <div class="side-step-row" id="step-row-8">
                    <div class="side-step-icon" id="step-icon-8">9</div>
                    <span class="side-step-label">dealbreaker</span>
                </div>
            </div>
        </div>

        <div class="side-bottom">
            <p class="side-bottom-quote">"every answer helps me plan a better date for us"</p>
            <p class="side-bottom-author">— corny ba</p>
        </div>
    </div>

    <!-- Right panel -->
    <div class="form-main-panel">
        <div class="form-container">

            <div class="form-top-bar">
                <span class="form-top-label">about you</span>
                <div class="form-progress-bar-wrap">
                    <div class="form-progress-bar-fill" id="progressBar"></div>
                </div>
            </div>

            <div class="validation-banner hidden" id="validationBanner">
                <span class="validation-icon">!</span>
                <span>fill out the required ones first</span>
            </div>

            <form id="mainForm" action="save_form.php" method="POST" novalidate>

                <!-- WHO ARE YOU -->
                <div class="form-section-divider">who are you</div>

                <div class="form-group" id="group-name">
                    <label>what do i call you? <span class="required-star">*</span></label>
                    <input type="text" name="name" id="field-name"
                        placeholder="name, nickname, whatever you go by">
                    <span class="field-error hidden">kahit nickname lang, need ko ito</span>
                </div>

                <div class="form-group" id="group-age">
                    <label>how old are you? <span class="required-star">*</span></label>
                    <select name="age" id="field-age">
                        <option value="">— select —</option>
                        <option>19</option>
                        <option>20</option>
                        <option>21</option>
                        <option>22</option>
                        <option>23</option>
                        <option>24</option>
                        <option>25+</option>
                    </select>
                    <span class="field-error hidden">hard pass sa minor, sorry</span>
                </div>

                <div class="form-group" id="group-city">
                    <label>wya <span class="required-star">*</span></label>
                    <input type="text" name="city" id="field-city"
                        placeholder="where should i pick u up">
                    <p style="font-size:0.75rem; color:var(--muted); margin-top:0.4rem; line-height:1.5;">
                        just so i know.. no need to give your exact address
                    </p>
                    <span class="field-error hidden">city or area lang</span>
                </div>

                <!-- HOW DO WE MEET -->
                <div class="form-section-divider">how do we meet</div>

                <div class="form-group" id="group-communication">
                    <label>where can i actually reach you? <span class="required-star">*</span></label>
                    <div class="checkbox-group">
                        <?php
                        $comms = [
                            'Instagram', 'Messenger', 'Text', 'Twitter',
                            'Telegram', 'Viber', 'Shopee message',
                            'Handwritten letter', 'In our dreams',
                            'Just read my mind', 'Basta',
                        ];
                        foreach ($comms as $c):
                            $safe = htmlspecialchars($c);
                        ?>
                        <label class="check-item">
                            <input type="checkbox" name="communication[]" value="<?= $safe ?>">
                            <?= $safe ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <span class="field-error hidden">pick at least one</span>
                </div>

                <div class="form-group" id="group-best_time">
                    <label>when's a good time for a date? <span class="required-star">*</span></label>
                    <div class="radio-group">
                        <label class="radio-item">
                            <input type="radio" name="best_time" value="Morning"
                                onchange="showTimePreview(this.value)"> Morning
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="best_time" value="Afternoon"
                                onchange="showTimePreview(this.value)"> Afternoon
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="best_time" value="Sunset"
                                onchange="showTimePreview(this.value)"> Sunset
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="best_time" value="Night"
                                onchange="showTimePreview(this.value)"> Night
                        </label>
                    </div>
                    <div class="time-preview" id="timePreview"></div>
                    <span class="field-error hidden">pick one that works for you</span>
                </div>

                <!-- BEFORE WE EAT -->
                <div class="form-section-divider">before we eat</div>

                <div class="form-group" id="group-food_drink">
                    <label>what's your go-to food and drink? <span class="required-star">*</span></label>
                    <input type="text" name="food_drink" id="field-food_drink"
                        placeholder="anything you always order or really like...">
                    <span class="field-error hidden">food is important, tell me</span>
                </div>

                <!-- FLOWER — two-step -->
                <div class="form-group" id="group-flower">
                    <label>are you the type who likes receiving flowers? <span class="required-star">*</span></label>
                    <p style="font-size:0.78rem; color:var(--muted); margin-bottom:0.6rem;">
                        some people love it, some find it awkward to carry around or not their thing at all, so i wanna know where you stand on this
                    </p>

                    <div class="radio-group">
                        <label class="radio-item">
                            <input type="radio" name="flower_comfort" value="yes"
                                onchange="handleFlowerComfort('yes')">
                            yes, i love flowers!
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="flower_comfort" value="yes_sweet"
                                onchange="handleFlowerComfort('yes_sweet')">
                            that would be sweet, i'd appreciate it
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="flower_comfort" value="yes_shy"
                                onchange="handleFlowerComfort('yes_shy')">
                            i like it but i'd be a bit shy carrying it around
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="flower_comfort" value="yes_not_so_fond"
                                onchange="handleFlowerComfort('yes_not_so_fond')">
                            not so fond but i won't mind receiving them
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="flower_comfort" value="no"
                                onchange="handleFlowerComfort('no')">
                            not really my thing
                        </label>
                    </div>

                    <?php
                    // shared flower list used across all panels
                    $flowers = [
                        'Rose', 'Sunflower', 'Tulip', 'Lily',
                        'Carnation', 'Orchid',
                        'Daisy',
                         
                    ];
                    ?>

                    <!-- yes — loves flowers, match that energy -->
                    <div class="flower-followup" id="flower-yes">
                        <div class="flower-response-note">
                            okayyyyy! which would you want?
                        </div>
                        <p style="font-size:0.78rem; color:var(--muted); margin-bottom:0.6rem;">pick as many as you like</p>
                        <div class="checkbox-group">
                            <?php foreach ($flowers as $f):
                                $safe = htmlspecialchars($f); ?>
                            <label class="check-item">
                                <input type="checkbox" name="flower[]" value="<?= $safe ?>"
                                    class="flower-pick-checkbox" onchange="updateProgress()">
                                <?= $safe ?>
                            </label>
                            <?php endforeach; ?>
                            <label class="check-item">
                                <input type="checkbox" name="flower[]" value="__custom_flower__"
                                    id="flower-custom-checkbox" class="flower-pick-checkbox"
                                    onchange="handleFlowerCustom('flower-custom-checkbox', 'flower-custom-input')">
                                something else
                            </label>
                        </div>
                        <input type="text" id="flower-custom-input" class="custom-field-input"
                            placeholder="what flower po?">
                    </div>

                    <!-- yes_sweet — lowkey shy but appreciates it, keep it chill -->
                    <div class="flower-followup" id="flower-yes-sweet">
                        <div class="flower-response-note">
                            noted, that works. which one would you like?
                        </div>
                        <p style="font-size:0.78rem; color:var(--muted); margin-bottom:0.6rem;">pick as many as you like</p>
                        <div class="checkbox-group">
                            <?php foreach ($flowers as $f):
                                $safe = htmlspecialchars($f); ?>
                            <label class="check-item">
                                <input type="checkbox" name="flower[]" value="<?= $safe ?>"
                                    class="flower-pick-checkbox" onchange="updateProgress()">
                                <?= $safe ?>
                            </label>
                            <?php endforeach; ?>
                            <label class="check-item">
                                <input type="checkbox" name="flower[]" value="__custom_flower__"
                                    id="flower-custom-checkbox-sweet" class="flower-pick-checkbox"
                                    onchange="handleFlowerCustom('flower-custom-checkbox-sweet', 'flower-custom-input-sweet')">
                                something else
                            </label>
                        </div>
                        <input type="text" id="flower-custom-input-sweet" class="custom-field-input"
                            placeholder="what flower?">
                    </div>

                    <!-- yes_shy — likes it but shy to carry -->
                    <div class="flower-followup" id="flower-yes-shy">
                        <div class="flower-response-note">
                            gets, we'll keep it somewhere safe muna so we won't have to carry it the whole time. which one though?
                        </div>
                        <p style="font-size:0.78rem; color:var(--muted); margin-bottom:0.6rem;">pick as many as you like</p>
                        <div class="checkbox-group">
                            <?php foreach ($flowers as $f):
                                $safe = htmlspecialchars($f); ?>
                            <label class="check-item">
                                <input type="checkbox" name="flower[]" value="<?= $safe ?>"
                                    class="flower-pick-checkbox" onchange="updateProgress()">
                                <?= $safe ?>
                            </label>
                            <?php endforeach; ?>
                            <label class="check-item">
                                <input type="checkbox" name="flower[]" value="__custom_flower__"
                                    id="flower-custom-checkbox-shy" class="flower-pick-checkbox"
                                    onchange="handleFlowerCustom('flower-custom-checkbox-shy', 'flower-custom-input-shy')">
                                something else
                            </label>
                        </div>
                        <input type="text" id="flower-custom-input-shy" class="custom-field-input"
                            placeholder="what flower?">
                    </div>

                    <!-- yes_not_so_fond — not really into it but okay with it -->
                    <div class="flower-followup" id="flower-yes-not-so-fond">
                        <div class="flower-response-note">
                            okay po please pick whichever you'd least mind getting
                        </div>
                        <p style="font-size:0.78rem; color:var(--muted); margin-bottom:0.6rem;">pick as many as you like</p>
                        <div class="checkbox-group">
                            <?php foreach ($flowers as $f):
                                $safe = htmlspecialchars($f); ?>
                            <label class="check-item">
                                <input type="checkbox" name="flower[]" value="<?= $safe ?>"
                                    class="flower-pick-checkbox" onchange="updateProgress()">
                                <?= $safe ?>
                            </label>
                            <?php endforeach; ?>
                            <label class="check-item">
                                <input type="checkbox" name="flower[]" value="__custom_flower__"
                                    id="flower-custom-checkbox-nf" class="flower-pick-checkbox"
                                    onchange="handleFlowerCustom('flower-custom-checkbox-nf', 'flower-custom-input-nf')">
                                something else
                            </label>
                        </div>
                        <input type="text" id="flower-custom-input-nf" class="custom-field-input"
                            placeholder="what flower?">
                    </div>

                    <!-- no -->
                    <div class="flower-followup" id="flower-no">
                        <div class="flower-response-note">
                            okay noted, rare breed. we skip the flowers then, no worries                        </div>
                        <input type="hidden" name="flower[]" value="none">
                    </div>

                    <span class="field-error hidden">pick one</span>
                </div>

                <div class="form-group" id="group-craving">
                    <label>anything you've been craving lately? <span class="optional-label">(optional)</span></label>
                    <input type="text" name="craving" id="field-craving"
                        placeholder="food, drink, dessert, anything...">
                </div>

                <div class="form-group" id="group-temperature">
                    <label>how do you usually handle the temperature? <span class="required-star">*</span></label>
                    <p style="font-size:0.78rem; color:var(--muted); margin-bottom:0.6rem;">this helps me figure out where to take you</p>
                    <div class="radio-group">
                        <?php
                        $temps = [
                            'i get cold easily',
                            "i'm usually fine with most places",
                            'i actually like cold places',
                            'i get hot easily',
                            "honestly doesn't matter to me",
                        ];
                        foreach ($temps as $t):
                            $safe = htmlspecialchars($t);
                        ?>
                        <label class="radio-item">
                            <input type="radio" name="temperature" value="<?= $safe ?>">
                            <?= $safe ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <span class="field-error hidden">pick one</span>
                </div>

                <div class="form-group" id="group-dislikes">
                    <label>anything you don't really enjoy when going out? <span class="optional-label">(optional)</span></label>
                    <div class="checkbox-group">
                        <?php
                        $dislikes = [
                            'loud places', 'super crowded spots', 'long waiting lines',
                            'too much walking', 'hot places',
                            'outdoor spots when it\'s too hot', 'late nights',
                            "none really",
                        ];
                        foreach ($dislikes as $d):
                            $safe = htmlspecialchars($d);
                        ?>
                        <label class="check-item">
                            <input type="checkbox" name="dislikes[]" value="<?= $safe ?>">
                            <?= $safe ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group" id="group-dessert">
                    <label>how about dessert? <span class="required-star">*</span></label>
                    <div class="radio-group">
                        <?php
                        $desserts = [
                            'ice cream', 'cake',
                            'pastries and stuff like that', 'chocolate anything',
                            "i'm not a dessert person",
                        ];
                        foreach ($desserts as $d):
                            $safe = htmlspecialchars($d);
                        ?>
                        <label class="radio-item">
                            <input type="radio" name="dessert" value="<?= $safe ?>">
                            <?= $safe ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <span class="field-error hidden">kahit isang sagot lang</span>
                </div>

                <div class="form-group" id="group-dealbreaker">
                    <label>what's your dealbreaker? <span class="optional-label">(optional)</span></label>
                    <div class="radio-group">
                        <?php
                        $deals = [
                            'bad music taste',
                            'pineapple on pizza',
                            'slow walkers',
                            "people who don't read the room",
                            'being on the phone the whole time',
                            'no sense of humor',
                            "none honestly, i'm pretty chill",
                        ];
                        foreach ($deals as $d):
                            $safe = htmlspecialchars($d);
                        ?>
                        <label class="radio-item">
                            <input type="radio" name="dealbreaker" value="<?= $safe ?>">
                            <?= $safe ?>
                        </label>
                        <?php endforeach; ?>
                        <label class="radio-item">
                            <input type="radio" name="dealbreaker" value="__custom__"
                                id="dealbreaker-custom-radio">
                            something else
                        </label>
                    </div>
                    <input type="text" id="dealbreaker-custom-input" class="custom-field-input"
                        placeholder="tell me yours">
                </div>

                <button type="submit" class="btn btn-yes" style="width:100%; margin-top:2rem; color:#fff;">
                    next
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
themeBtn.textContent = savedTheme === 'light' ? 'dark' : 'light';

function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme');
    const next = current === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('siteTheme', next);
    themeBtn.textContent = next === 'light' ? 'dark' : 'light';
}

// ── Time preview ── (your exact wording kept)
const timeMessages = {
    'Morning':   "ok early bird. lowkey nakakatamad but they say the early bird gets the worm so fuck it we BALLLL",
    'Afternoon': "mid but solid choice if we want enough time to do things without rushing and still get home at a decent hour",
    'Sunset':    "good pick. golden hour, nice breeze, good lighting, sun hits diff and all",
    'Night':     "the GOATTT but still depends on your availability and your parents' program bahhahah",
};

function showTimePreview(val) {
    const preview = document.getElementById('timePreview');
    preview.textContent = timeMessages[val] || '';
    preview.classList.toggle('show', !!timeMessages[val]);
    updateProgress();
}

// ── Flower comfort handler — all 5 options ──
const flowerPanelMap = {
    'yes':            'flower-yes',
    'yes_sweet':      'flower-yes-sweet',
    'yes_shy':        'flower-yes-shy',
    'yes_not_so_fond':'flower-yes-not-so-fond',
    'no':             'flower-no',
};

function handleFlowerComfort(val) {
    // hide all panels
    Object.values(flowerPanelMap).forEach(id => {
        document.getElementById(id)?.classList.remove('show');
    });

    // uncheck all flower checkboxes and hide all custom inputs
    document.querySelectorAll('.flower-pick-checkbox').forEach(cb => cb.checked = false);
    ['flower-custom-input', 'flower-custom-input-sweet',
     'flower-custom-input-shy', 'flower-custom-input-nf'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.classList.remove('visible'); el.value = ''; }
    });

    // show the right panel
    const panelId = flowerPanelMap[val];
    if (panelId) document.getElementById(panelId)?.classList.add('show');

    updateProgress();
}

// ── Generic flower custom handler ──
function handleFlowerCustom(checkboxId, inputId) {
    const cb    = document.getElementById(checkboxId);
    const input = document.getElementById(inputId);
    if (!cb || !input) return;
    if (cb.checked) {
        input.classList.add('visible');
        input.focus();
    } else {
        input.classList.remove('visible');
        input.value = '';
    }
    updateProgress();
}

// ── Dealbreaker custom ──
const dealbreakerInput = document.getElementById('dealbreaker-custom-input');

document.querySelectorAll('input[name="dealbreaker"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.value === '__custom__') {
            dealbreakerInput.classList.add('visible');
            dealbreakerInput.focus();
        } else {
            dealbreakerInput.classList.remove('visible');
            dealbreakerInput.value = '';
        }
        updateProgress();
    });
});

// ── Step tracker ──
const fieldStepMap = [
    { stepIndex: 0, test: () => !!document.getElementById('field-name')?.value.trim() && !!document.getElementById('field-age')?.value },
    { stepIndex: 1, test: () => !!document.getElementById('field-city')?.value.trim() },
    { stepIndex: 2, test: () => document.querySelectorAll('input[name="communication[]"]:checked').length > 0 },
    { stepIndex: 3, test: () => !!document.querySelector('input[name="best_time"]:checked') },
    { stepIndex: 4, test: () => !!document.getElementById('field-food_drink')?.value.trim() },
    { stepIndex: 5, test: () => {
        const comfort = document.querySelector('input[name="flower_comfort"]:checked');
        if (!comfort) return false;
        if (comfort.value === 'no') return true;
        const picked = document.querySelectorAll('.flower-pick-checkbox:checked');
        if (picked.length === 0) return false;
        // check if any "something else" checkbox is checked but text is empty
        const customPairs = [
            ['flower-custom-checkbox',      'flower-custom-input'],
            ['flower-custom-checkbox-sweet','flower-custom-input-sweet'],
            ['flower-custom-checkbox-shy',  'flower-custom-input-shy'],
            ['flower-custom-checkbox-nf',   'flower-custom-input-nf'],
        ];
        for (const [cbId, inId] of customPairs) {
            const cb = document.getElementById(cbId);
            const inp = document.getElementById(inId);
            if (cb?.checked && !inp?.value.trim()) return false;
        }
        return true;
    }},
    { stepIndex: 6, test: () => !!document.querySelector('input[name="temperature"]:checked') },
    { stepIndex: 7, test: () => !!document.querySelector('input[name="dessert"]:checked') },
    { stepIndex: 8, test: () => !!document.querySelector('input[name="dealbreaker"]:checked') },
];

const stepNums      = ['1','2','3','4','5','6','7','8','9'];
const totalRequired = 8;

function updateProgress() {
    let requiredFilled = 0;
    fieldStepMap.forEach((item, i) => {
        const done = item.test();
        const icon = document.getElementById('step-icon-' + item.stepIndex);
        const row  = document.getElementById('step-row-'  + item.stepIndex);
        if (icon && row) {
            if (done) {
                icon.classList.add('done');
                icon.textContent = '✓';
                row.classList.add('done');
            } else {
                icon.classList.remove('done');
                row.classList.remove('done');
                icon.textContent = stepNums[item.stepIndex];
            }
        }
        if (i < 8 && done) requiredFilled++;
    });
    document.getElementById('progressBar').style.width =
        Math.round((requiredFilled / totalRequired) * 100) + '%';
}

document.querySelectorAll('input, select').forEach(el => el.addEventListener('change', updateProgress));
document.querySelectorAll('input[type="text"]').forEach(el => el.addEventListener('input', updateProgress));
['dealbreaker-custom-input','flower-custom-input','flower-custom-input-sweet',
 'flower-custom-input-shy','flower-custom-input-nf'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', updateProgress);
});
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
            id: 'group-flower',
            test: () => {
                const comfort = document.querySelector('input[name="flower_comfort"]:checked');
                if (!comfort) return false;
                if (comfort.value === 'no') return true;
                const picked = document.querySelectorAll('.flower-pick-checkbox:checked');
                if (picked.length === 0) return false;
                const customPairs = [
                    ['flower-custom-checkbox',      'flower-custom-input'],
                    ['flower-custom-checkbox-sweet','flower-custom-input-sweet'],
                    ['flower-custom-checkbox-shy',  'flower-custom-input-shy'],
                    ['flower-custom-checkbox-nf',   'flower-custom-input-nf'],
                ];
                for (const [cbId, inId] of customPairs) {
                    const cb = document.getElementById(cbId);
                    const inp = document.getElementById(inId);
                    if (cb?.checked && !inp?.value.trim()) return false;
                }
                return true;
            }
        },
        { id: 'group-temperature', test: () => document.querySelector('input[name="temperature"]:checked') },
        { id: 'group-dessert',     test: () => document.querySelector('input[name="dessert"]:checked') },
    ];

    checks.forEach(({ id, test }) => {
        if (!test()) {
            document.getElementById(id).classList.add('has-error');
            document.getElementById(id).querySelector('.field-error')?.classList.remove('hidden');
            hasError = true;
        }
    });

    if (hasError) {
        document.getElementById('validationBanner').classList.remove('hidden');
        document.querySelector('.form-group.has-error')
            ?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    // Handle all flower custom text inputs
    const customPairs = [
        ['flower-custom-checkbox',      'flower-custom-input'],
        ['flower-custom-checkbox-sweet','flower-custom-input-sweet'],
        ['flower-custom-checkbox-shy',  'flower-custom-input-shy'],
        ['flower-custom-checkbox-nf',   'flower-custom-input-nf'],
    ];
    customPairs.forEach(([cbId, inId]) => {
        const cb  = document.getElementById(cbId);
        const inp = document.getElementById(inId);
        if (cb?.checked && inp?.value.trim()) {
            const h = document.createElement('input');
            h.type = 'hidden'; h.name = 'flower[]';
            h.value = inp.value.trim();
            this.appendChild(h);
            cb.removeAttribute('name');
        }
    });

    // Handle dealbreaker custom
    const dealbreakerChecked = document.querySelector('input[name="dealbreaker"]:checked');
    if (dealbreakerChecked?.value === '__custom__') {
        const val = dealbreakerInput.value.trim();
        if (val) {
            dealbreakerInput.setAttribute('name', 'dealbreaker');
            dealbreakerInput.setAttribute('type', 'hidden');
            dealbreakerChecked.removeAttribute('name');
        }
    }

    this.submit();
});

// ── Live error clearing ──
document.querySelectorAll('input, select').forEach(input => {
    input.addEventListener('change', function() {
        const group = this.closest('.form-group');
        if (group?.classList.contains('has-error')) {
            group.classList.remove('has-error');
            group.querySelector('.field-error')?.classList.add('hidden');
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
            group.querySelector('.field-error')?.classList.add('hidden');
            if (!document.querySelector('.form-group.has-error')) {
                document.getElementById('validationBanner').classList.add('hidden');
            }
        }
    });
});

// ── Auto-save form data to localStorage ──
function saveFormData() {
    const formData = new FormData(document.getElementById('mainForm'));
    const data = {};
    
    // Save text inputs, selects
    formData.forEach((value, key) => {
        if (!data[key]) {
            data[key] = value;
        } else if (Array.isArray(data[key])) {
            data[key].push(value);
        } else {
            data[key] = [data[key], value];
        }
    });
    
    // Save individual radio/checkbox states
    document.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(el => {
        if (el.name) {
            data[el.name + '_' + el.value] = el.checked ? '1' : '0';
        }
    });
    
    localStorage.setItem('formAutoSave', JSON.stringify(data));
}

function restoreFormData() {
    const saved = localStorage.getItem('formAutoSave');
    if (!saved) return;
    
    const data = JSON.parse(saved);
    
    // Restore text inputs, selects
    document.querySelectorAll('input[type="text"], select, textarea').forEach(el => {
        if (data[el.name]) {
            el.value = data[el.name];
        }
    });
    
    // Restore radio/checkbox states
    document.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(el => {
        const key = el.name + '_' + el.value;
        if (data[key] === '1') {
            el.checked = true;
            // Trigger change event for dependent fields (like flower/dealbreaker custom inputs)
            el.dispatchEvent(new Event('change', { bubbles: true }));
        } else {
            el.checked = false;
        }
    });
    
    updateProgress();
}

// Save on any input change
document.addEventListener('change', saveFormData);
document.addEventListener('input', saveFormData);

// Restore on page load
window.addEventListener('DOMContentLoaded', restoreFormData);

// Clear saved data on successful form submit
document.getElementById('mainForm').addEventListener('submit', function(e) {
    if (!e.defaultPrevented) {
        localStorage.removeItem('formAutoSave');
    }
});
</script>
</body>
</html>
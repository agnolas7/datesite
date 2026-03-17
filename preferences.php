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
    <title>date preferences</title>
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

        .pref-side-heading { flex: 0; }

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

        /* ── Category checklist on left ── */
        .pref-side-visual {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 0;
            padding: 1rem 0;
            overflow-y: auto;
        }

        .pref-category {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.55rem 0.8rem;
            border-radius: 10px;
            transition: background 0.2s;
            position: relative;
        }

        .pref-category:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 22px;
            bottom: -4px;
            width: 1px;
            height: 8px;
            background: var(--border);
        }

        .pref-cat-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--input-bg);
            border: 1.5px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            color: var(--muted);
            flex-shrink: 0;
            transition: all 0.3s;
            font-family: 'DM Sans', sans-serif;
        }

        .pref-category.done .pref-cat-icon {
            background: var(--pink);
            border-color: var(--pink);
            color: #1a0a10;
            font-weight: 700;
        }

        .pref-category.active .pref-cat-icon {
            border-color: var(--pink);
            color: var(--pink);
        }

        .pref-cat-info {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }

        .pref-category-text {
            font-size: 0.78rem;
            color: var(--muted);
            transition: color 0.2s;
            line-height: 1.3;
        }

        .pref-cat-sub {
            font-size: 0.68rem;
            color: var(--border);
            transition: color 0.2s;
            line-height: 1.2;
        }

        .pref-category.done .pref-category-text { color: var(--text); }
        .pref-category.done .pref-cat-sub { color: var(--pink); font-size: 0.65rem; }
        .pref-category.active .pref-category-text { color: var(--pink); }
        .pref-category.active { background: rgba(244, 167, 185, 0.05); }

        /* ── Progress ── */
        .pref-side-bottom {
            padding-top: 1.2rem;
            border-top: 1px solid var(--border);
        }

        .pref-progress-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .pref-progress-label span { font-size: 0.72rem; color: var(--muted); }
        .pref-progress-label .pref-progress-count { color: var(--pink); font-weight: 500; }

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

        .pref-main-inner { max-width: 520px; }

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

        .pref-top-count { font-size: 0.78rem; color: var(--muted); }

        /* ── Section divider ── */
        .pref-section-divider {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            color: var(--muted);
            margin: 2.5rem 0 1.5rem;
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

        /* ── Question card ── */
        .pref-question-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.2rem 1.4rem;
            margin-bottom: 1rem;
            transition: border-color 0.2s;
        }

        .pref-question-card:focus-within {
            border-color: rgba(244, 167, 185, 0.3);
        }

        .pref-question-card label.pref-q-label {
            display: block;
            font-size: 0.88rem;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 0.9rem;
            line-height: 1.4;
        }

        .pref-question-card .pref-q-sub {
            font-size: 0.78rem;
            color: var(--muted);
            margin-bottom: 0.8rem;
            line-height: 1.5;
            display: block;
        }

        /* ── Live preview inside card ── */
        .pref-preview {
            margin-top: 0.8rem;
            padding: 0.65rem 0.9rem;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.78rem;
            color: var(--muted);
            font-style: italic;
            line-height: 1.5;
            display: none;
            animation: fadeUp 0.2s ease;
        }

        #preview-place_in_mind {
            border-left: 3px solid var(--pink);
            background: rgba(244, 167, 185, 0.08);
            color: var(--text);
            font-style: normal;
            padding-left: 0.9rem;
            font-weight: 500;
        }

        #preview-place_timing {
            border-left: 3px solid var(--pink);
            background: rgba(244, 167, 185, 0.08);
            color: var(--text);
            font-style: normal;
            padding-left: 0.9rem;
            font-weight: 500;
        }

        .pref-preview.show { display: block; }

        /* ── Radio items inside card ── */
        .pref-question-card .radio-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .pref-question-card .radio-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.55rem 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            color: var(--text);
            transition: background 0.15s;
            border: 1px solid transparent;
        }

        .pref-question-card .radio-item:hover {
            background: var(--input-bg);
        }

        .pref-question-card .radio-item input[type="radio"] {
            accent-color: var(--pink);
            flex-shrink: 0;
        }

        .pref-question-card .radio-item.selected {
            background: rgba(244, 167, 185, 0.06);
            border-color: rgba(244, 167, 185, 0.2);
        }

        /* ── Checkbox pill style (vibes) ── */
        .pref-question-card .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }

        .pref-question-card .check-item {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 0.3rem 0.75rem;
            font-size: 0.8rem;
            color: var(--text);
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }

        .pref-question-card .check-item:has(input:checked) {
            border-color: var(--pink);
            background: rgba(244, 167, 185, 0.08);
            color: var(--pink);
        }

        .pref-question-card .check-item input[type="checkbox"] {
            accent-color: var(--pink);
        }

        /* ── Comfort checkbox list style (vertical) ── */
        .comfort-check-list {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .comfort-check-list .check-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            font-size: 0.84rem;
            color: var(--text);
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            width: 100%;
        }

        .comfort-check-list .check-item:has(input:checked) {
            border-color: rgba(244, 167, 185, 0.35);
            background: rgba(244, 167, 185, 0.06);
            color: var(--text);
        }

        .comfort-check-list .check-item input[type="checkbox"] {
            accent-color: var(--pink);
            flex-shrink: 0;
        }

        /* ── Text inputs ── */
        .pref-question-card input[type="text"] {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.65rem 0.9rem;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.88rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .pref-question-card input[type="text"]:focus {
            border-color: var(--pink);
        }

        .comfort-custom-input {
            display: none;
            margin-top: 0.6rem;
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.65rem 0.9rem;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.88rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .comfort-custom-input.visible { display: block; animation: fadeUp 0.2s ease; }
        .comfort-custom-input:focus { border-color: var(--pink); }

        /* ── Scrollbar styling for dark mode ── */
        :root[data-theme="dark"] {
            scrollbar-width: thin;
            scrollbar-color: #2a2a2a #1a1a1a;
        }

        :root[data-theme="dark"] ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        :root[data-theme="dark"] ::-webkit-scrollbar-track {
            background: var(--bg);
            border-radius: 10px;
        }

        :root[data-theme="dark"] ::-webkit-scrollbar-thumb {
            background: #2a2a2a;
            border-radius: 10px;
            border: 2px solid var(--bg);
        }

        :root[data-theme="dark"] ::-webkit-scrollbar-thumb:hover {
            background: #3a3a3a;
        }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            .pref-layout { grid-template-columns: 1fr; min-height: unset; }
            .pref-side {
                position: relative; height: auto;
                padding: 1.5rem 1.5rem 1.2rem;
                border-right: none; border-bottom: 1px solid var(--border);
            }
            .pref-side-visual { display: none; }
            .pref-side-bottom { display: none; }
            .pref-side-heading h1 { font-size: 1.4rem; margin-bottom: 0.3rem; }
            .pref-main { padding: 2rem 1.5rem 4rem; }
        }

        @media (max-width: 480px) {
            .pref-side { padding: 1.2rem 1.2rem 1rem; }
            .pref-main { padding: 1.5rem 1.2rem 4rem; }
        }
    </style>
</head>
<body class="form-page">

<div class="pref-layout">

    <!-- Left sticky panel -->
    <div class="pref-side">
        <div class="pref-side-nav">
            <span class="pref-side-logo">✦</span>
            <div class="pref-side-nav-right">
                <button class="pref-theme-btn" id="themeBtn" onclick="toggleTheme()">light</button>
                <span class="pref-step-label">step 3 of 3</span>
            </div>
        </div>

        <div class="pref-side-heading">
            <h1>the important questions</h1>
            <p>tell me how you like things.<br>i'll plan around you.</p>
        </div>

        <div class="pref-side-visual" id="categoryList">
            <div class="pref-category" data-section="date" id="cat-date">
                <div class="pref-cat-icon" id="cat-icon-date">1</div>
                <div class="pref-cat-info">
                    <span class="pref-category-text">the date itself</span>
                    <span class="pref-cat-sub" id="cat-sub-date">2 questions</span>
                </div>
            </div>
            <div class="pref-category" data-section="energy" id="cat-energy">
                <div class="pref-cat-icon" id="cat-icon-energy">2</div>
                <div class="pref-cat-info">
                    <span class="pref-category-text">energy and mood</span>
                    <span class="pref-cat-sub" id="cat-sub-energy">2 questions</span>
                </div>
            </div>
            <div class="pref-category" data-section="crowd" id="cat-crowd">
                <div class="pref-cat-icon" id="cat-icon-crowd">3</div>
                <div class="pref-cat-info">
                    <span class="pref-category-text">where and how</span>
                    <span class="pref-cat-sub" id="cat-sub-crowd">2 questions</span>
                </div>
            </div>
            <div class="pref-category" data-section="convo" id="cat-convo">
                <div class="pref-cat-icon" id="cat-icon-convo">4</div>
                <div class="pref-cat-info">
                    <span class="pref-category-text">the talking part</span>
                    <span class="pref-cat-sub" id="cat-sub-convo">3 questions</span>
                </div>
            </div>
            <!-- NEW category -->
            <div class="pref-category" data-section="logistics" id="cat-logistics">
                <div class="pref-cat-icon" id="cat-icon-logistics">5</div>
                <div class="pref-cat-info">
                    <span class="pref-category-text">before we plan</span>
                    <span class="pref-cat-sub" id="cat-sub-logistics">3 questions</span>
                </div>
            </div>
            <div class="pref-category" data-section="vibes" id="cat-vibes">
                <div class="pref-cat-icon" id="cat-icon-vibes">6</div>
                <div class="pref-cat-info">
                    <span class="pref-category-text">vibe and activities</span>
                    <span class="pref-cat-sub" id="cat-sub-vibes">pick what sounds good</span>
                </div>
            </div>
        </div>

        <div class="pref-side-bottom">
            <div class="pref-progress-label">
                <span>answered</span>
                <span class="pref-progress-count" id="progressCount">0 / 12</span>
            </div>
            <div class="pref-progress-track">
                <div class="pref-progress-fill" id="progressFill"></div>
            </div>
        </div>
    </div>

    <!-- Right main panel -->
    <div class="pref-main" id="prefMain">
        <div class="pref-main-inner">

            <div class="pref-top-bar">
                <span class="pref-top-title">date preferences</span>
                <span class="pref-top-count">answer all to continue</span>
            </div>

            <form action="save_preferences.php" method="POST" id="prefForm">

                <!-- THE DATE ITSELF -->
                <div class="pref-section-divider" data-section="date">the date itself</div>

                <div class="pref-question-card" data-field="date_type" data-section="date">
                    <label class="pref-q-label">what kind of date actually sounds good to you?</label>
                    <div class="radio-group">
                        <?php foreach ([
                            'something lowkey',
                            'food trip, drive and eat',
                            'go somewhere with a nice view',
                            'do something, not just sit around',
                            'totally spontaneous, figure it out as we go',
                            'surprise me, i trust you',
                            'crush kita, you plan everything and i just show up',
                        ] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="date_type" value="<?= htmlspecialchars($opt) ?>"
                                onchange="showPreview('date_type', this.value); markSelected(this)">
                            <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="pref-preview" id="preview-date_type"></div>
                </div>

                <div class="pref-question-card" data-field="spontaneity" data-section="date">
                    <label class="pref-q-label">how planned do you want it?</label>
                    <div class="radio-group">
                        <?php foreach ([
                            'i like knowing what we\'re doing beforehand',
                            'as long as we have a direction',
                            'figure it out as we go honestly',
                            'the more chaotic the better',
                        ] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="spontaneity" value="<?= htmlspecialchars($opt) ?>"
                                onchange="showPreview('spontaneity', this.value); markSelected(this)">
                            <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="pref-preview" id="preview-spontaneity"></div>
                </div>

                <!-- ENERGY AND MOOD -->
                <div class="pref-section-divider" data-section="energy">energy and mood</div>

                <div class="pref-question-card" data-field="energy" data-section="energy">
                    <label class="pref-q-label">how should we keep the energy so i don't drain your social battery?</label>
                    <div class="radio-group">
                        <?php foreach ([
                            'lowkey and relaxed',
                            'chill but not boring',
                            'high energy and fun',
                            'depends on my mood that day honestly',
                            'go with the flow',
                        ] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="energy" value="<?= htmlspecialchars($opt) ?>"
                                onchange="showPreview('energy', this.value); markSelected(this)">
                            <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="pref-preview" id="preview-energy"></div>
                </div>

                <div class="pref-question-card" data-field="mood" data-section="energy">
                    <label class="pref-q-label">what's the vibe you're going for?</label>
                    <div class="radio-group">
                        <?php foreach ([
                            'chill and no pressure',
                            'fun and a little chaotic',
                            'we\'re both gonna be kinda awkward and that\'s okay',
                            'whatever happens, happens',
                        ] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="mood" value="<?= htmlspecialchars($opt) ?>"
                                onchange="showPreview('mood', this.value); markSelected(this)">
                            <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="pref-preview" id="preview-mood"></div>
                </div>

                <!-- WHERE AND HOW -->
                <div class="pref-section-divider" data-section="crowd">where and how</div>

                <div class="pref-question-card" data-field="crowd" data-section="crowd">
                    <label class="pref-q-label">how many people around us is acceptable?</label>
                    <div class="radio-group">
                        <?php foreach ([
                            'ideally just us, somewhere quiet',
                            'a few people around is fine',
                            'busy place is okay, i don\'t mind noise',
                            'doesn\'t matter at all',
                            'i\'ll leave it to you',
                        ] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="crowd" value="<?= htmlspecialchars($opt) ?>"
                                onchange="showPreview('crowd', this.value); markSelected(this)">
                            <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="pref-preview" id="preview-crowd"></div>
                </div>

                <div class="pref-question-card" data-field="walking" data-section="crowd">
                    <label class="pref-q-label">how much walking can i make you do?</label>
                    <div class="radio-group">
                        <?php foreach ([
                            'minimal, i\'m not here to exercise',
                            'a little is fine',
                            'walk me around',
                            'depends on the place and how i feel that day',
                            'no walking pls',
                        ] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="walking" value="<?= htmlspecialchars($opt) ?>"
                                onchange="showPreview('walking', this.value); markSelected(this)">
                            <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="pref-preview" id="preview-walking"></div>
                </div>

                <!-- THE TALKING PART -->
                <div class="pref-section-divider" data-section="convo">the talking part</div>

                <div class="pref-question-card" data-field="convo_style" data-section="convo">
                    <label class="pref-q-label">what do you actually want to talk about?</label>
                    <div class="radio-group">
                        <?php foreach ([
                            'get to know each other properly',
                            'keep it light and funny, nothing heavy',
                            'random topics, wherever it goes',
                            'i\'ll talk when i feel like it',
                        ] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="convo_style" value="<?= htmlspecialchars($opt) ?>"
                                onchange="showPreview('convo_style', this.value); markSelected(this)">
                            <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="pref-preview" id="preview-convo_style"></div>
                </div>

                <div class="pref-question-card" data-field="awkwardness" data-section="convo">
                    <label class="pref-q-label">are you more of a yapper, a listener, or somewhere in between?</label>
                    <div class="radio-group">
                        <?php foreach ([
                            'yapper — i will carry the conversation, don\'t worry',
                            'listener — i\'m better at responding than starting',
                            'both — depends',
                            'neither — i communicate through eye contact',
                            'dancer — hawak ko ang beat',
                        ] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="awkwardness" value="<?= htmlspecialchars($opt) ?>"
                                onchange="showPreview('awkwardness', this.value); markSelected(this)">
                            <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="pref-preview" id="preview-awkwardness"></div>
                </div>

                <div class="pref-question-card" data-field="convo_difficulty" data-section="convo">
                    <label class="pref-q-label">anything that would make you more comfortable around me?</label>
                    <span class="pref-q-sub">pick as many as you like — i genuinely want to know</span>
                    <div class="comfort-check-list">
                        <?php foreach ([
                            'don\'t put me on the spot too much at the start',
                            'let me warm up first',
                            'keep the mood light, especially at first',
                            'don\'t make it feel like an interview',
                            'let silences just exist, don\'t fill them awkwardly',
                            'bring food. food helps.',
                            'just be yourself, i\'ll relax if you\'re relaxed',
                            'ask me about things i actually care about',
                            'nothing specific, i\'m usually okay',
                        ] as $tip): ?>
                        <label class="check-item">
                            <input type="checkbox" name="convo_difficulty[]"
                                value="<?= htmlspecialchars($tip) ?>"
                                onchange="updateProgress()">
                            <?= htmlspecialchars($tip) ?>
                        </label>
                        <?php endforeach; ?>
                        <label class="check-item">
                            <input type="checkbox" name="convo_difficulty[]"
                                value="__custom_comfort__"
                                id="comfort-custom-cb"
                                onchange="handleComfortCustom()">
                            something else
                        </label>
                    </div>
                    <input type="text" id="comfort-custom-input" class="comfort-custom-input"
                        placeholder="tell me what helps...">
                </div>

                <!-- BEFORE WE PLAN (new logistics section) -->
                <div class="pref-section-divider" data-section="logistics">before we plan</div>

                <div class="pref-question-card" data-field="curfew" data-section="logistics">
                    <label class="pref-q-label">do you have a curfew?</label>
                    <p class="pref-q-sub">no judgment at all — just need to know so i can plan properly</p>
                    <div class="radio-group">
                        <?php foreach ([
                            'yes, i have a strict curfew',
                            'yes but it\'s flexible depending on the situation',
                            'kind of, i just need to let them know',
                            'no curfew, i\'m free',
                        ] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="curfew" value="<?= htmlspecialchars($opt) ?>"
                                onchange="showPreview('curfew', this.value); markSelected(this)">
                            <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="pref-preview" id="preview-curfew"></div>
                </div>

                <div class="pref-question-card" data-field="parents" data-section="logistics">
                    <label class="pref-q-label">how are your parents about going out?</label>
                    <div class="radio-group">
                        <?php foreach ([
                            'very strict — they need to know everything',
                            'strict but okay if i tell them in advance',
                            'chill, just need to update them',
                            'they don\'t really mind',
                            'i\'m independent, not an issue',
                            'won\'t be allowed, they\'re very strict',
                        ] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="parents" value="<?= htmlspecialchars($opt) ?>"
                                onchange="showPreview('parents', this.value); markSelected(this)">
                            <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="pref-preview" id="preview-parents"></div>
                </div>

                <div class="pref-question-card" data-field="distance" data-section="logistics">
                    <label class="pref-q-label">how far from home are you okay going?</label>
                    <p class="pref-q-sub">just so i know what's realistic to plan</p>
                    <div class="radio-group">
                        <?php foreach ([
                            'close by only, around our area',
                            'nearby cities are fine',
                            'doesn\'t matter, i\'m down wherever',
                            'depends on the day and situation',
                        ] as $opt): ?>
                        <label class="radio-item">
                            <input type="radio" name="distance" value="<?= htmlspecialchars($opt) ?>"
                                onchange="showPreview('distance', this.value); markSelected(this)">
                            <?= htmlspecialchars($opt) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="pref-preview" id="preview-distance"></div>
                </div>

                <!-- VIBE AND ACTIVITIES -->
                <div class="pref-section-divider" data-section="vibes">vibe and activities</div>

                <div class="pref-question-card" data-field="vibes" data-section="vibes">
                    <label class="pref-q-label">pick everything that actually sounds good</label>
                    <div class="checkbox-group">
                        <?php foreach ([
                            'coffee shop', 'night drive', 'arcade or games',
                            'watch a movie', 'street food', 'random stroll',
                            'parking lot hangout', 'drinks and chill', 'picnic',
                            'bookstore or thrift', 'museum date', 'night market/park',
                            'convenience store run at midnight', 'beach or nature',
                            'dinner somewhere nice',
                            'music or a gig', 'drive',
                            'creative stuff — art, crafts, that kind of thing','illegal activity',
                        ] as $v): ?>
                        <label class="check-item">
                            <input type="checkbox" name="vibes[]" value="<?= htmlspecialchars($v) ?>">
                            <?= htmlspecialchars($v) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>

                    <label class="pref-q-label" style="margin-top: 1.2rem; margin-bottom: 0.6rem;">something i didn't list?</label>
                    <input type="text" name="custom_vibe" placeholder="tell me your idea..." style="width: 100%;">
                </div>

                <!-- PLACE SHE WANTS TO GO -->
                <div class="pref-question-card" data-field="place_in_mind" data-section="vibes">
                    <label class="pref-q-label">is there somewhere you want to go that i should know about?</label>
                    <p class="pref-q-sub">not trying to pass planning on you but if you have a spot you want to go to, i'll take you there</p>
                    <div class="radio-group">
                        <label class="radio-item">
                            <input type="radio" name="place_in_mind" value="yes" id="place-yes-radio"
                                onchange="handlePlaceInMind('yes'); markSelected(this)">
                            i do have somewhere in mind
                        </label>
                        <label class="radio-item">
                            <input type="radio" name="place_in_mind" value="no" id="place-no-radio"
                                onchange="handlePlaceInMind('no'); markSelected(this)">
                            not really, i'm down anywhere
                        </label>
                    </div>
                    <div class="pref-preview" id="preview-place_in_mind"></div>
                </div>

                <!-- Place details (hidden until option selected) -->
                <div id="place-details-section" style="display:none;">
                    <div class="pref-question-card">
                        <label class="pref-q-label">what place?</label>
                        <input type="text" name="place_name" id="place_name_input" placeholder="where do you want to go..." style="margin-bottom: 1.5rem;">

                        <label class="pref-q-label">when do you want to go?</label>
                        <div class="radio-group">
                            <label class="radio-item">
                                <input type="radio" name="place_timing" value="planned_date"
                                    onchange="showPreview('place_timing', this.value); markSelected(this)">
                                on our planned date
                            </label>
                            <label class="radio-item">
                                <input type="radio" name="place_timing" value="another_day"
                                    onchange="showPreview('place_timing', this.value); markSelected(this)">
                                another day is cool too
                            </label>
                            <label class="radio-item">
                                <input type="radio" name="place_timing" value="not_sure"
                                    onchange="showPreview('place_timing', this.value); markSelected(this)">
                                not sure yet
                            </label>
                        </div>
                        <div class="pref-preview" id="preview-place_timing"></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-yes"
                    style="width:100%; margin-top:2rem; color:#fff;">
                    done
                </button>

            </form>
        </div>
    </div>

</div>

<script src="js/main.js"></script>
<script>
// ── Theme ──
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

// ── Preview messages ──
const previews = {
    date_type: {
        'something lowkey':               'nice, comfortable energy. i can work with that',
        'food trip, drive and eat':                "aight that's genuinely a nice kind of date",
        'go somewhere with a nice view':                "noted. i know a good spot" ,
        'do something, not just sit around':            'i like that, we\'ll keep it interesting',
        'totally spontaneous, figure it out as we go':  "okay we're winging it then. i'm in",
        'surprise me, i trust you':                     "aight bet i got u",
        'crush kita, you plan everything and i just show up': "ay hehe sure why not 😍 but u still have to answer some questions to make sure i plan something you'll like",
    },
    spontaneity: {
        'i like knowing what we\'re doing beforehand':  "got it, i'll send you a heads up",
        'as long as we have a direction':              "perfect, i like it that way too",
        'figure it out as we go honestly':              'sponty it is',
        'the more chaotic the better':                  "how about we commit a crime together",
    },
    energy: {
        'lowkey and relaxed':                       "on some nonchalant shi",
        'chill but not boring':                     "oh ok coz we\'re js chill like that",
        'high energy and fun':                      "okay we gon turn it upppp",
        'depends on my mood that day honestly':     "fair enough, we'll see",
        'go with the flow':                         "i fw this heavy",
    },
    mood: {
        'chill and no pressure':                            "that's the goal every time honestly",
        'fun and a little chaotic':                         "perfect",
        'we\'re both gonna be kinda awkward and that\'s okay':    "we'll survive",
        'whatever happens, happens':                        "type shiii",
    },
    crowd: {
        'ideally just us, somewhere quiet':         "noted. somewhere we can actually hear each other",
        'a few people around is fine':              "cool, a little background noise never hurt",
        'busy place is okay, i don\'t mind noise':  "okay we have a lot of options then",
        'doesn\'t matter at all':                   'wowz easy to work with',
        'i\'ll leave it to you':                    "basta makasama ka lang 😍",
    },
    walking: {
        'minimal, i\'m not here to exercise':           "we'll find somewhere to sit. noted",
        'a little is fine':                             "a short walk here and there, okay okay",
        'walk me around':                               "takbo ka na lang sa isip ko",
        'depends on the place and how i feel that day': "okay, i'll check in with you",
        'no walking pls':                               "how about running",
    },
    convo_style: {
        'get to know each other properly'   : "okay po let's actually talk",
        'keep it light and funny, nothing heavy':       "alright we'll keep it easy and chill",
        'random topics, wherever it goes':              'always been like this with me',
        'i\'ll talk when i feel like it':  "yes maam",
    },
    awkwardness: {
        'yapper — i will carry the conversation, don\'t worry':  "wow perfect",
        'listener — i\'m better at responding than starting':    "im curious george, i'll ask the questions",
        'both — depends':                       "u da real yappener",
        'neither — i communicate through eye contact':           "ay angas, good thing i love ur eyes",
        'dancer — hawak ko ang beat':                            "ge lods sayaw",
    },
    // new logistics previews
    curfew: {
        'yes, i have a strict curfew':                          "okay noted, we'll make sure you're home on time",
        'yes but it\'s flexible depending on the situation':    "got it, i'll keep that in mind and plan accordingly",
        'kind of, i just need to let them know':                "that works, just update them and we're good",
        'no curfew, i\'m free':                                 "okay we have a lot of time then",
    },
    parents: {
        'very strict — they need to know everything':   "noted, we'll make sure everything is above board",
        'strict but okay if i tell them in advance':    "okay, i'll give you enough notice to sort it out",
        'chill, just need to update them':              "easy, just drop them a message and we're set",
        'they don\'t really mind':                      "nice, that makes planning a lot easier",
        'i\'m independent, not an issue':               "okay we're good then, no worries",
        'won\'t be allowed, they\'re very strict':      "tell me if u want me to sneak u out",
    },
    distance: {
        'close by only, around our area':               "okay i'll keep it local. there's plenty to do nearby",
        'nearby cities are fine':                       "nice, that opens up a lot more options",
        'doesn\'t matter, i\'m down wherever':          "okay anywhere is on the table then",
        'depends on the day and situation':             "okay, i'll check in with you",
    },
    place_in_mind: {
        'yes':  "i'll take you there, just say the word",
        'no':   "alright it's good",
    },
    place_timing: {
        'planned_date':  "perfect, we'll go there on our date",
        'another_day':   "second date secured ",
        'not_sure':      "that's cool, we can figure it out as we go",
    },
};

function showPreview(field, val) {
    const el  = document.getElementById('preview-' + field);
    const msg = previews[field]?.[val];
    if (el && msg) {
        el.textContent = msg;
        el.classList.add('show');
    } else if (el) {
        el.classList.remove('show');
    }
    updateProgress();
}

// ── Highlight selected radio ──
function markSelected(input) {
    const group = input.closest('.radio-group');
    if (!group) return;
    group.querySelectorAll('.radio-item').forEach(l => l.classList.remove('selected'));
    input.closest('.radio-item').classList.add('selected');
}

// ── Comfort custom ──
function handleComfortCustom() {
    const cb    = document.getElementById('comfort-custom-cb');
    const input = document.getElementById('comfort-custom-input');
    if (cb.checked) {
        input.classList.add('visible');
        input.focus();
    } else {
        input.classList.remove('visible');
        input.value = '';
    }
    updateProgress();
}

// ── Handle place in mind ──
function handlePlaceInMind(val) {
    const detailsSection = document.getElementById('place-details-section');
    const placeNameInput = document.getElementById('place_name_input');
    const previewEl = document.getElementById('preview-place_in_mind');
    
    if (val === 'yes') {
        detailsSection.style.display = 'block';
        placeNameInput.focus();
        previewEl.textContent = "i'll take you there, just say the word";
        previewEl.classList.add('show');
    } else {
        detailsSection.style.display = 'none';
        placeNameInput.value = '';
        document.querySelectorAll('input[name="place_timing"]').forEach(r => r.checked = false);
        document.getElementById('preview-place_timing').classList.remove('show');
        previewEl.textContent = "aight it's good, i'll take care of it";
        previewEl.classList.add('show');
    }
    updateProgress();
}

// ── Category checklist config ──
const catFields = {
    date:      ['date_type', 'spontaneity'],
    energy:    ['energy', 'mood'],
    crowd:     ['crowd', 'walking'],
    convo:     ['convo_style', 'awkwardness'],
    logistics: ['curfew', 'parents', 'distance'],
    vibes:     [],
};

const catDoneText = {
    date:      'both answered',
    energy:    'both answered',
    crowd:     'both answered',
    convo:     'all answered',
    logistics: 'all answered',
    vibes:     'picked',
};

function updateCategoryStatus() {
    Object.entries(catFields).forEach(([cat, fields]) => {
        const catEl  = document.getElementById('cat-' + cat);
        const iconEl = document.getElementById('cat-icon-' + cat);
        const subEl  = document.getElementById('cat-sub-' + cat);
        if (!catEl) return;

        let done = false;
        if (cat === 'vibes') {
            done = document.querySelectorAll('input[name="vibes[]"]:checked').length > 0;
        } else {
            done = fields.every(f => document.querySelector(`input[name="${f}"]:checked`));
        }

        if (done) {
            catEl.classList.add('done');
            catEl.classList.remove('active');
            if (iconEl) iconEl.textContent = '✓';
            if (subEl)  subEl.textContent  = catDoneText[cat] || 'done';
        } else {
            catEl.classList.remove('done');
            if (iconEl && !catEl.classList.contains('active')) {
                iconEl.textContent = String(Object.keys(catFields).indexOf(cat) + 1);
            }
        }
    });
}

// ── Progress — now 12 required (added curfew, parents, distance) ──
const radioFields   = [
    'date_type','spontaneity','energy','mood',
    'crowd','walking','convo_style','awkwardness',
    'curfew','parents','distance'
];
const totalRequired = 12;

function updateProgress() {
    let answered = 0;
    radioFields.forEach(name => {
        if (document.querySelector(`input[name="${name}"]:checked`)) answered++;
    });
    if (document.querySelectorAll('input[name="convo_difficulty[]"]:checked').length > 0) answered++;

    const pct = Math.round((answered / totalRequired) * 100);
    document.getElementById('progressFill').style.width = pct + '%';
    document.getElementById('progressCount').textContent = answered + ' / ' + totalRequired;
    updateCategoryStatus();
}

// ── Active section on scroll ──
function updateActiveSection() {
    const dividers = document.querySelectorAll('.pref-section-divider');
    const scrollY  = document.getElementById('prefMain').scrollTop;

    let currentSection = null;
    dividers.forEach(d => {
        if (scrollY >= d.offsetTop - 120) currentSection = d.dataset.section;
    });

    document.querySelectorAll('.pref-category').forEach(cat => {
        const sec    = cat.dataset.section;
        const icon   = document.getElementById('cat-icon-' + sec);
        const isDone = cat.classList.contains('done');

        if (sec === currentSection && !isDone) {
            cat.classList.add('active');
            if (icon) icon.textContent = String(Object.keys(catFields).indexOf(sec) + 1);
        } else {
            cat.classList.remove('active');
        }
    });
}

// ── Submit — inject comfort custom text ──
document.getElementById('prefForm').addEventListener('submit', function() {
    const cb    = document.getElementById('comfort-custom-cb');
    const input = document.getElementById('comfort-custom-input');
    if (cb?.checked && input?.value.trim()) {
        const h = document.createElement('input');
        h.type = 'hidden'; h.name = 'convo_difficulty[]';
        h.value = input.value.trim();
        this.appendChild(h);
        cb.removeAttribute('name');
    }
});

document.querySelectorAll('input[type="radio"]').forEach(el =>
    el.addEventListener('change', updateProgress));
document.querySelectorAll('input[name="vibes[]"]').forEach(el =>
    el.addEventListener('change', updateProgress));
document.querySelectorAll('input[name="convo_difficulty[]"]').forEach(el =>
    el.addEventListener('change', updateProgress));
document.getElementById('comfort-custom-input')?.addEventListener('input', updateProgress);
document.getElementById('prefMain').addEventListener('scroll', updateActiveSection);

updateProgress();
updateActiveSection();
</script>
</body>
</html>
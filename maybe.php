<?php
session_start();
require 'includes/db.php';

$ownerUsername = $_SESSION['owner'] ?? null;

// ── Defaults ──
$profileItems = [
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
$savedExpectations = [
    "i pay attention to the small things you mention. if you say you've been craving something, i'll remember it.",
    "i don't just buy gifts — i make them. cards, playlists, little things that took actual thought and time.",
    "if something needs fixing, i fix it. if you need help carrying something, i'm already carrying it.",
    "i check in. not in an overwhelming way — just a \"how was your day\" kind of way that actually means it.",
    "i'll plan the date so you don't have to think about it. just show up.",
    "i'm the kind of person who stays until the end — of the movie, the conversation, the night.",
    "i'll make sure you get home safe. always.",
];
$savedSkills = [
    '✦ active listener', '✦ gift maker (not just buyer)',
    '✦ remembers what you said', 'drives', 'pays for food',
    '✦ actually funny', 'good playlist curator', '✦ will not ghost',
    'opens doors', '✦ no weird expectations',
    'night drive certified 🚗', '✦ makes the effort',
];

// ── Load from database ──
if ($ownerUsername) {
    $stmt = $pdo->prepare("SELECT * FROM site_owners WHERE username = ?");
    $stmt->execute([$ownerUsername]);
    $ownerData = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($ownerData) {
        if (!empty($ownerData['profile_items']))       $profileItems      = json_decode($ownerData['profile_items'], true);
        if (!empty($ownerData['promise_text']))        $promiseText       = $ownerData['promise_text'];
        if (!empty($ownerData['whyyy_text']))          $whyyyText         = $ownerData['whyyy_text'];
        if (!empty($ownerData['resume_expectations'])) $savedExpectations = json_decode($ownerData['resume_expectations'], true);
        if (!empty($ownerData['resume_skills']))       $savedSkills       = json_decode($ownerData['resume_skills'], true);
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
    <style>
        .why-option-preview {
            margin-top: 0.8rem;
            padding: 0.8rem 1rem;
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

        .why-option-preview.show { display: block; }

        .why-label {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            background: var(--input-bg);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 0.65rem 1rem;
            cursor: pointer;
            font-size: 0.88rem;
            color: var(--text);
            transition: border-color 0.2s, background 0.2s;
        }

        .why-label:hover { border-color: var(--pink); }

        .why-label.selected {
            border-color: var(--pink);
            background: rgba(244, 167, 185, 0.06);
        }

        .why-label input {
            accent-color: var(--pink);
            flex-shrink: 0;
        }

        /* ── Resume styles ── */
        .resume-wrapper { font-family: 'DM Sans', sans-serif; }

        .resume-header {
            text-align: center;
            padding-bottom: 1.2rem;
            margin-bottom: 1.2rem;
            border-bottom: 2px solid var(--pink);
        }

        .resume-name {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.4rem, 4vw, 2rem);
            color: var(--text);
            margin-bottom: 0.2rem;
        }

        .resume-title {
            font-size: 0.82rem;
            color: var(--pink);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .resume-tagline {
            font-size: 0.8rem;
            color: var(--muted);
            margin-top: 0.4rem;
            font-style: italic;
        }

        .resume-section { margin-bottom: 1.3rem; }

        .resume-section-title {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--pink);
            font-weight: 600;
            margin-bottom: 0.6rem;
            padding-bottom: 0.3rem;
            border-bottom: 1px solid var(--border);
        }

        .resume-item {
            display: flex;
            gap: 0.6rem;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            color: var(--text);
            line-height: 1.5;
            align-items: flex-start;
        }

        .resume-item-icon { flex-shrink: 0; margin-top: 0.05rem; }

        .resume-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }

        .resume-skill-tag {
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 0.25rem 0.75rem;
            font-size: 0.78rem;
            color: var(--text);
        }

        .resume-skill-tag.highlighted {
            border-color: var(--pink);
            color: var(--pink);
        }

        .resume-note {
            background: var(--input-bg);
            border-left: 3px solid var(--pink);
            border-radius: 0 10px 10px 0;
            padding: 0.8rem 1rem;
            font-size: 0.82rem;
            color: var(--muted);
            font-style: italic;
            line-height: 1.7;
            margin-bottom: 1.3rem;
        }

        .resume-footer {
            text-align: center;
            font-size: 0.72rem;
            color: var(--muted);
            padding-top: 0.8rem;
            border-top: 1px solid var(--border);
            font-style: italic;
        }

        .resume-handwritten {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 0.95rem;
            color: var(--pink);
            text-align: center;
            padding: 1rem;
            background: var(--input-bg);
            border-radius: 12px;
            border: 1px dashed var(--pink);
            margin-bottom: 1.2rem;
            line-height: 1.7;
            opacity: 0.9;
        }

        /* ── Perks section ── */
        .resume-perks {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.6rem;
            margin-top: 0.4rem;
        }

        .resume-perk {
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.7rem 0.9rem;
            font-size: 0.82rem;
            color: var(--text);
            line-height: 1.4;
        }

        .resume-perk-icon {
            display: block;
            font-size: 1.1rem;
            margin-bottom: 0.3rem;
        }

        .resume-perk-title {
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--pink);
            display: block;
            margin-bottom: 0.15rem;
        }

        .resume-perk-desc {
            font-size: 0.75rem;
            color: var(--muted);
            line-height: 1.4;
        }

        @media (max-width: 480px) {
            .resume-perks { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="maybe-page">

    <!-- Step 1: WHYYY -->
    <div class="center-card" id="step-whyyy">
        <h1 class="big-emotion">WHYYY 😭</h1>
        <p><?= htmlspecialchars($whyyyText) ?></p>
        <button class="btn btn-primary" onclick="showProfile()" style="margin-top:1.5rem;">
            check this out first 👀
        </button>
    </div>

    <!-- Step 2: Profile — resume style -->
    <div class="center-card hidden" id="step-profile" style="max-width:640px; text-align:left;">
        <div class="resume-wrapper">

            <!-- Header -->
            <div class="resume-header">
                <div class="resume-name"><?= htmlspecialchars($ownerUsername ?? 'your future date') ?></div>
                <div class="resume-title">Applicant for: one date with you 💌</div>
                <div class="resume-tagline">open to coffee, food trips, and getting to know each other</div>
            </div>

            <!-- Objective -->
            <div class="resume-section">
                <div class="resume-section-title">objective</div>
                <div class="resume-note">
                    to take you out on one genuinely good date. no pressure beyond that.
                    just good company, good food, and hopefully you laughing at least once.
                    open to second date pending your review. 👀
                </div>
            </div>

            <!-- Core qualifications -->
            <div class="resume-section">
                <div class="resume-section-title">core qualifications</div>
                <?php foreach ($profileItems as $item): ?>
                <div class="resume-item">
                    <span class="resume-item-icon">–</span>
                    <span><?= htmlspecialchars($item) ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- What you can actually expect -->
            <div class="resume-section">
                <div class="resume-section-title">what you can actually expect</div>
                <?php foreach ($savedExpectations as $exp): ?>
                <div class="resume-item">
                    <span class="resume-item-icon">–</span>
                    <span><?= htmlspecialchars($exp) ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Date perks — the "you'll never spend" section -->
            <div class="resume-section">
                <div class="resume-section-title">date package includes 🎁</div>
                <div class="resume-perks">
                    <div class="resume-perk">
                        <span class="resume-perk-icon">💸</span>
                        <span class="resume-perk-title">fully covered</span>
                        <span class="resume-perk-desc">you will never have to spend a single peso. ever. i got it.</span>
                    </div>
                    <div class="resume-perk">
                        <span class="resume-perk-icon">🚗</span>
                        <span class="resume-perk-title">door to door</span>
                        <span class="resume-perk-desc">i'll pick you up and drop you off. you just tell me where.</span>
                    </div>
                    <div class="resume-perk">
                        <span class="resume-perk-icon">🗓️</span>
                        <span class="resume-perk-title">i plan everything</span>
                        <span class="resume-perk-desc">you don't have to think about a single detail. just show up.</span>
                    </div>
                    <div class="resume-perk">
                        <span class="resume-perk-icon">🍜</span>
                        <span class="resume-perk-title">you pick the food</span>
                        <span class="resume-perk-desc">whatever you're craving. your call, no questions asked.</span>
                    </div>
                    <div class="resume-perk">
                        <span class="resume-perk-icon">🌸</span>
                        <span class="resume-perk-title">zero pressure</span>
                        <span class="resume-perk-desc">no weird expectations. just two people getting to know each other.</span>
                    </div>
                    <div class="resume-perk">
                        <span class="resume-perk-icon">🏠</span>
                        <span class="resume-perk-title">home safe, always</span>
                        <span class="resume-perk-desc">i will make sure you get home safe. non-negotiable.</span>
                    </div>
                    <div class="resume-perk">
                        <span class="resume-perk-icon">☕</span>
                        <span class="resume-perk-title">coffee or milk tea</span>
                        <span class="resume-perk-desc">whichever you want. or both. we don't judge here.</span>
                    </div>
                    <div class="resume-perk">
                        <span class="resume-perk-icon">📱</span>
                        <span class="resume-perk-title">phone stays down</span>
                        <span class="resume-perk-desc">you have my full attention. no distractions.</span>
                    </div>
                </div>
            </div>

            <!-- Skills -->
            <div class="resume-section">
                <div class="resume-section-title">skills & competencies</div>
                <div class="resume-skills">
                    <?php foreach ($savedSkills as $skill): ?>
                    <span class="resume-skill-tag <?= str_starts_with(trim($skill), '✦') ? 'highlighted' : '' ?>">
                        <?= htmlspecialchars($skill) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Handwritten note -->
            <div class="resume-handwritten">
                "<?= htmlspecialchars($promiseText) ?>" 🙏
            </div>

            <!-- Footer disclaimer -->
            <div class="resume-footer">
                ✦ this resume was made with genuine effort and zero exaggeration ✦<br>
                references available upon request (just go on the date and find out)
            </div>

        </div>

        <div class="button-group" style="margin-top:1.5rem;">
            <a href="form.php" class="btn btn-yes">okay fine, i'm in 😄</a>
            <button class="btn btn-maybe" onclick="showCountdown1()">still not sure</button>
        </div>
    </div>

    <!-- Step 3: First countdown — 10 seconds -->
    <div class="center-card hidden" id="step-countdown1">
        <h2>okay. just think about it. 🤔</h2>
        <p class="subtitle">no pressure — just actually consider it for a sec</p>
        <div class="countdown-circle" id="countdownDisplay1">10</div>
        <p id="countdownMsg1" class="small-note">it's just one date...</p>
    </div>

    <!-- Step 4: After first countdown -->
    <div class="center-card hidden" id="step-whatAboutNow">
        <h2>what about now? 🥺</h2>
        <p class="subtitle">like genuinely though</p>
        <div class="button-group">
            <a href="form.php" class="btn btn-yes">okay 😊</a>
            <button class="btn btn-maybe" onclick="showCountdown2()">still no</button>
        </div>
    </div>

    <!-- Step 5: Second countdown — 5 seconds -->
    <div class="center-card hidden" id="step-countdown2">
        <h2>think again. 🤨</h2>
        <p class="subtitle">last few seconds i swear</p>
        <div class="countdown-circle" id="countdownDisplay2">5</div>
        <p id="countdownMsg2" class="small-note">...</p>
    </div>

    <!-- Step 6: Why not? -->
    <div class="center-card hidden" id="step-whynot">
        <h2>okay, fair enough 🙌</h2>
        <p style="margin-bottom:1.2rem; font-size:0.9rem;">
            can i ask why though? totally no hard feelings, genuinely just curious 😄
        </p>

        <div style="display:flex; flex-direction:column; gap:0.5rem; text-align:left; margin-bottom:0.5rem;">
            <label class="why-label" onclick="selectWhyNot(this)">
                <input type="radio" name="whynot" value="i don't know you well enough yet">
                i don't know you well enough yet
            </label>
            <label class="why-label" onclick="selectWhyNot(this)">
                <input type="radio" name="whynot" value="the timing isn't right for me">
                the timing isn't right for me
            </label>
            <label class="why-label" onclick="selectWhyNot(this)">
                <input type="radio" name="whynot" value="i'm not looking for anything right now">
                i'm not looking for anything right now
            </label>
            <label class="why-label" onclick="selectWhyNot(this)">
                <input type="radio" name="whynot" value="i'm already talking to someone">
                i'm already talking to someone
            </label>
            <label class="why-label" onclick="selectWhyNot(this)">
                <input type="radio" name="whynot" value="i just don't like you like that">
                i just don't like you like that 😅
            </label>
            <label class="why-label" onclick="selectWhyNot(this)">
                <input type="radio" name="whynot" value="something else">
                something else
            </label>
        </div>

        <div class="why-option-preview" id="whyPreview"></div>

        <div id="customWhyField" style="display:none; margin-top:0.8rem;">
            <input type="text" id="customWhyInput"
                placeholder="tell me honestly..."
                style="width:100%; background:var(--input-bg); border:1px solid var(--border);
                       border-radius:10px; padding:0.75rem 1rem; color:var(--text);
                       font-family:'DM Sans',sans-serif; font-size:0.9rem; outline:none;">
        </div>

        <button class="btn btn-yes" style="width:100%; color:#fff; margin-top:1.2rem;"
                onclick="submitWhyNot()">
            send 🌸
        </button>
    </div>

    <!-- Step 7: Final -->
    <div class="center-card hidden" id="step-final">
        <h1 class="big-emotion" style="font-size:2.5rem;" id="finalEmoji">🙌</h1>
        <h2 id="finalTitle">noted!</h2>
        <p id="finalMsg" class="subtitle" style="margin-top:0.5rem;"></p>
        <p class="small-note" style="margin-top:1rem; line-height:1.8;">
            thanks for being honest. that actually means a lot. 🌸<br>
            hope you have a genuinely good day.
        </p>
    </div>

    <script src="js/main.js"></script>
    <script>

    function showProfile()    { showStep('step-profile'); }

    function showCountdown1() {
        showStep('step-countdown1');
        startCountdownTimer('countdownDisplay1', 'countdownMsg1', 10, [
            { at: 7, msg: "it's just one date..." },
            { at: 4, msg: "i'm actually fun i promise 😄" },
            { at: 1, msg: "almost done thinking?" },
        ], () => showStep('step-whatAboutNow'));
    }

    function showCountdown2() {
        showStep('step-countdown2');
        startCountdownTimer('countdownDisplay2', 'countdownMsg2', 5, [
            { at: 3, msg: "really though?" },
            { at: 1, msg: "last chance 👀" },
        ], () => showStep('step-whynot'));
    }

    function startCountdownTimer(displayId, msgId, seconds, messages, onDone) {
        let count = seconds;
        const display = document.getElementById(displayId);
        const msgEl   = document.getElementById(msgId);
        const timer   = setInterval(() => {
            count--;
            if (display) display.textContent = count;
            messages.forEach(m => {
                if (count === m.at && msgEl) msgEl.textContent = m.msg;
            });
            if (count <= 0) {
                clearInterval(timer);
                if (onDone) onDone();
            }
        }, 1000);
    }

    const previewMessages = {
        "i don't know you well enough yet":       "that's completely fair. hopefully this helped a little at least 😊",
        "the timing isn't right for me":          "timing is real — i get that. no rush on anything 🕐",
        "i'm not looking for anything right now": "that's a valid place to be. take your time 🫂",
        "i'm already talking to someone":         "oh okay! hope that's going well for you 😊",
        "i just don't like you like that":        "respect for being honest about it. that actually takes guts 😄",
        "something else":                         "tell me below — no pressure to explain but i appreciate it 🌸",
    };

    const sendMessages = {
        "i don't know you well enough yet": {
            emoji: '😊', title: 'totally fair!',
            msg: 'makes complete sense. no pressure at all.'
        },
        "the timing isn't right for me": {
            emoji: '🕐', title: 'timing is everything.',
            msg: 'understood. maybe some other time 🌸'
        },
        "i'm not looking for anything right now": {
            emoji: '🫂', title: 'all good!',
            msg: "that's a completely valid place to be. take care of yourself 🌸"
        },
        "i'm already talking to someone": {
            emoji: '😅', title: 'oh okay okay.',
            msg: 'got it! hope it goes well for you 🌸'
        },
        "i just don't like you like that": {
            emoji: '😭', title: 'okay ouch —',
            msg: 'but honestly? respect for being straight up. appreciated 😂'
        },
        "something else": {
            emoji: '🙌', title: 'got it!',
            msg: 'thanks for sharing. no hard feelings at all 🌸'
        },
    };

    function selectWhyNot(label) {
        document.querySelectorAll('.why-label').forEach(l => l.classList.remove('selected'));
        label.classList.add('selected');
        const radio = label.querySelector('input');
        radio.checked = true;
        const val = radio.value;
        document.getElementById('customWhyField').style.display =
            val === 'something else' ? 'block' : 'none';
        const preview = document.getElementById('whyPreview');
        preview.textContent = previewMessages[val] || '';
        preview.classList.add('show');
    }

    function submitWhyNot() {
        const selected = document.querySelector('input[name="whynot"]:checked');
        if (!selected) { alert('pick an option 🥺'); return; }

        let reason = selected.value;
        if (reason === 'something else') {
            const custom = document.getElementById('customWhyInput').value.trim();
            reason = custom || 'something else';
        }

        fetch('save_maybe_reason.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'reason=' + encodeURIComponent(reason)
        });

        const key      = selected.value;
        const response = sendMessages[key] || {
            emoji: '🙌', title: 'got it!',
            msg: 'thanks for being real. no hard feelings at all 🌸'
        };

        document.getElementById('finalEmoji').textContent = response.emoji;
        document.getElementById('finalTitle').textContent = response.title;
        document.getElementById('finalMsg').textContent   = response.msg;
        showStep('step-final');
    }

    function showStep(id) {
        document.querySelectorAll('.center-card').forEach(el => el.classList.add('hidden'));
        const el = document.getElementById(id);
        if (el) {
            el.classList.remove('hidden');
            el.style.animation = 'none';
            void el.offsetWidth;
            el.style.animation = 'fadeUp 0.4s ease';
        }
    }
    </script>

    <footer style="margin-top:4rem; padding:2rem 1rem; text-align:center;">
        <a href="feedback.php" style="
            display: inline-block;
            color: var(--muted);
            text-decoration: none;
            font-size: 0.8rem;
            padding: 0.8rem 1.2rem;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            transition: all 0.2s ease;
        " 
        onmouseover="this.style.backgroundColor='rgba(244,167,185,0.08)'; this.style.borderColor='rgba(244,167,185,0.3)'; this.style.color='var(--pink)'"
        onmouseout="this.style.backgroundColor='transparent'; this.style.borderColor='rgba(255,255,255,0.1)'; this.style.color='var(--muted)'">
            🐛 found a bug or have feedback?
        </a>
    </footer>
</body>
</html>
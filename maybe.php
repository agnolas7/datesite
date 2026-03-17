<?php
session_start();
require 'includes/db.php';

$ownerUsername = $_SESSION['owner'] ?? null;

// ── Defaults ──
$profileItems = [
    'kind and thoughtful.........',
    'good music taste (trust)',
    'will always share food',
    'good late night company',
    'actually listens  when you talk',
    'kinda funny naman',
    'may pang hatid sundo',
    'i will pay syempre',
];
$promiseText = 'matino naman ako halata ba..? 😇';
$whyyyText   = 'PLEASEEE give me a chance';
$savedExpectations = [
    "i pay attention",
    "i don't just buy gifts, i make them. things that took actual thought, effort, and time",
    "i'll make sure you get home safe. always",
    "i respect your time, your space, and your boundaries",
    "i'll treat you like a princess",
    "i don't rush things",
];
$savedPerks = [
    ['title' => 'fully covered', 'desc' => 'you will never have to spend a single peso. ever. i got it'],
    ['title' => 'door to door', 'desc' => "i'll pick you up and drop you off. you just tell me where"],
    ['title' => 'you pick the food', 'desc' => "whatever you're craving. your call, no questions asked"],
    ['title' => 'zero pressure', 'desc' => "no weird expectations. just two people getting to know each other"],
    ['title' => 'home safe, always', 'desc' => "i will make sure you get home safe. non-negotiable"],
    ['title' => 'phone stays down', 'desc' => "you have my full attention. no distractions"],
];
$savedSkills = [
    'active listener',
    'remembers what you said', 'driver',  'handy',
    'funny', 'good playlist curator', 'no weird expectations', 'makes the effort',
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
        if (!empty($ownerData['resume_perks']))        $savedPerks        = json_decode($ownerData['resume_perks'], true);
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
        <h1 class="big-emotion">WHY DI SURE</h1>
        <p><?= htmlspecialchars($whyyyText) ?></p>
        <button class="btn btn-primary" onclick="showProfile()" style="margin-top:1.5rem;">
            check this out first 
        </button>
    </div>

    <!-- Step 2: Profile — resume style -->
    <div class="center-card hidden" id="step-profile" style="max-width:640px; text-align:left;">
        <div class="resume-wrapper">

            <!-- Header -->
            <div class="resume-header">
                <div class="resume-name"><?= htmlspecialchars($ownerUsername ?? 'your future date') ?></div>
                <div class="resume-title">Applicant for: one date with you</div>
            </div>

            <!-- Objective -->
            <div class="resume-section">
                <div class="resume-section-title">objective</div>
                <div class="resume-note">
                    to take you out, make it a good experience for you, and see if we actually get along.
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
                <div class="resume-section-title">date package includes</div>
                <div class="resume-perks">
                    <?php foreach ($savedPerks as $perk): ?>
                    <div class="resume-perk">
                        <span class="resume-perk-icon"></span>
                        <span class="resume-perk-title"><?= htmlspecialchars($perk['title'] ?? '') ?></span>
                        <span class="resume-perk-desc"><?= htmlspecialchars($perk['desc'] ?? '') ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Skills -->
            <div class="resume-section">
                <div class="resume-section-title">skills & competencies</div>
                <div class="resume-skills">
                    <?php foreach ($savedSkills as $skill): ?>
                    <span class="resume-skill-tag">
                        <?= htmlspecialchars($skill) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Handwritten note -->
            <div class="resume-handwritten">
                "<?= htmlspecialchars($promiseText) ?>"
            </div>

            <!-- Footer disclaimer -->
            <div class="resume-footer">
                this resume was made with genuine effort and zero exaggeration<br>
                references available upon request (just  go on the date and find out)
            </div>

        </div>

        <div class="button-group" style="margin-top:1.5rem;">
            <a href="form.php" class="btn btn-yes">okay fine, i'm in </a>
            <button class="btn btn-maybe" onclick="showCountdown1()">still not sure</button>
        </div>
    </div>

    <!-- Step 3: First countdown — 10 seconds -->
    <div class="center-card hidden" id="step-countdown1">
        <h2> think about it pleaseeeeeeeeee</h2>
        <p class="subtitle">just actually consider it, im giving you 10 seconds</p>
        <div class="countdown-circle" id="countdownDisplay1">10</div>
        <p id="countdownMsg1" class="small-note">it's just one date...</p>
    </div>

    <!-- Step 4: After first countdown -->
    <div class="center-card hidden" id="step-whatAboutNow">
        <h2>what about now? </h2>
        <p class="subtitle">hehehehe</p>
        <div class="button-group">
            <a href="form.php" class="btn btn-yes">sige na nga</a>
            <button class="btn btn-maybe" onclick="showCountdown2()">still no</button>
        </div>
    </div>

    <!-- Step 5: Second countdown — 5 seconds -->
    <div class="center-card hidden" id="step-countdown2">
        <h2>think again</h2>
        <p class="subtitle">HAHAHAH last na i swear</p>
        <div class="countdown-circle" id="countdownDisplay2">5</div>
        <p id="countdownMsg2" class="small-note">...</p>
    </div>

    <!-- Step 6: Why not? -->
    <div class="center-card hidden" id="step-whynot">
        <h2>okay pooo, had to shoot my shot </h2>
        <p style="margin-bottom:1.2rem; font-size:0.9rem;">
            can i ask why though? totally no hard feelings, genuinely just curious..
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
                i just don't like you like that 
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
            send 
        </button>
    </div>

    <!-- Step 7: Final -->
    <div class="center-card hidden" id="step-final">
        <h1 class="big-emotion" style="font-size:2.5rem;" id="finalEmoji">🙌</h1>
        <h2 id="finalTitle">noted!</h2>
        <p id="finalMsg" class="subtitle" style="margin-top:0.5rem;"></p>
        <p class="small-note" style="margin-top:1rem; line-height:1.8;">
            thanks for being honest. that actually means a lot<br>
            hope you have a genuinely good dayyy
        </p>
    </div>

    <script src="js/main.js"></script>
    <script>

    function showProfile()    { showStep('step-profile'); }

    function showCountdown1() {
        showStep('step-countdown1');
        startCountdownTimer('countdownDisplay1', 'countdownMsg1', 10, [
            { at: 7, msg: "it's just one date..." },
            { at: 4, msg: "i'm actually fun i promise " },
            { at: 1, msg: "almost done thinking?" },
        ], () => showStep('step-whatAboutNow'));
    }

    function showCountdown2() {
        showStep('step-countdown2');
        startCountdownTimer('countdownDisplay2', 'countdownMsg2', 5, [
            { at: 3, msg: "really though?" },
            { at: 1, msg: "last chance " },
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
        "i don't know you well enough yet":       "that's completely fair. hopefully this helped a little at least",
        "the timing isn't right for me":          "i respect it, no rush pressure at all. maybe some other time",
        "i'm not looking for anything right now": "fair enough. focusing on yourself is always valid. take care of yourself :)",
        "i'm already talking to someone":         "aray kooo! hahahhjk hope that's going well for you ",
        "i just don't like you like that":        "ARAYYYY",
        "something else":                         "tell me below — no pressure to explain but i appreciate it",
    };

    const sendMessages = {
        "i don't know you well enough yet": {
            emoji: '', title: 'totally fair!',
            msg: 'makes complete sense. no pressure at all.'
        },
        "the timing isn't right for me": {
            emoji: '', title: 'timing is everything.',
            msg: 'understood. maybe some other time'
        },
        "i'm not looking for anything right now": {
            emoji: '', title: 'all good!',
            msg: "take care of yourself :)"
        },
        "i'm already talking to someone": {
            emoji: '', title: 'sino hahah jk',
            msg: 'got it! hope it goes well for you'
        },
        "i just don't like you like that": {
            emoji: '', title: 'di naman makasakit, parang kagat lang ng dinosaur',
            msg: 'respect for being straight up though, no hard feelings at all :) '
        },
        "something else": {
            emoji: '', title: 'got it!',
            msg: 'thanks for sharing :) hope you have a good day!'
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
        if (!selected) { alert('pick an option'); return; }

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
            msg: 'thanks for being real. no hard feelings at all'
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

</body>
</html>
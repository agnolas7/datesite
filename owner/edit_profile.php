<?php
session_start();
if (empty($_SESSION['owner'])) {
    header('Location: login.php');
    exit;
}
require '../includes/db.php';

$username = $_SESSION['owner'];
$success  = false;
$error    = '';

$stmt  = $pdo->prepare("SELECT * FROM site_owners WHERE username = ?");
$stmt->execute([$username]);
$owner = $stmt->fetch(PDO::FETCH_ASSOC);

// Defaults
$defaultItems = [
    'kind and thoughtful.........',
    'good music taste (trust)',
    'will always share food',
    'good late night company',
    'actually listens  when you talk',
    'kinda funny naman',
    'may pang hatid sundo',
    'i will pay syempre',
];

$defaultExpectations = [
    "i pay attention",
    "i don't just buy gifts, i make them and take actual thought, effort, and time",
    "i'll make sure you get home safe always",
    "i'll respect your time, your space, and your boundaries",
    "i'll treat you like a princess",
    "i don't rush things",
];

$defaultSkills = [
    'active listener',
    'remembers what you said', 'driver',  'handy',
    'funny', 'good playlist curator', 'no weird expectations', 'makes the effort',
];

$defaultPerks = [
    ['title' => 'fully covered', 'desc' => 'you will never have to spend a single peso. ever. i got it'],
    ['title' => 'door to door', 'desc' => "i'll pick you up and drop you off. you just tell me where"],
    ['title' => 'you pick the food', 'desc' => "whatever you're craving. your call, no questions asked"],
    ['title' => 'zero pressure', 'desc' => "no weird expectations. just two people getting to know each other"],
    ['title' => 'home safe, always', 'desc' => "i will make sure you get home safe. non-negotiable"],
    ['title' => 'phone stays down', 'desc' => "you have my full attention. no distractions"],
];

$savedItems        = $owner['profile_items']         ? json_decode($owner['profile_items'], true)         : $defaultItems;
$savedExpectations = $owner['resume_expectations']   ? json_decode($owner['resume_expectations'], true)   : $defaultExpectations;
$savedSkills       = $owner['resume_skills']         ? json_decode($owner['resume_skills'], true)         : $defaultSkills;
$savedPerks        = $owner['resume_perks']          ? json_decode($owner['resume_perks'], true)          : $defaultPerks;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items        = array_values(array_filter(array_map('trim', $_POST['items'] ?? [])));
    $expectations = array_values(array_filter(array_map('trim', $_POST['expectations'] ?? [])));
    $skills       = array_values(array_filter(array_map('trim', $_POST['skills'] ?? [])));
    $instaLink    = trim($_POST['instagram_link'] ?? '');
    
    // Validate Instagram link
    if (empty($instaLink)) {
        $error = 'Instagram profile link is required!';
    } elseif (!preg_match('#^https://instagram\.com/[a-zA-Z0-9_.]+/?$#', $instaLink)) {
        $error = 'Instagram link must be in format: https://instagram.com/username';
    } else {
        // Process perks
        $perk_titles = $_POST['perk_title'] ?? [];
        $perk_descs  = $_POST['perk_desc'] ?? [];
        $perks       = [];
        foreach ($perk_titles as $idx => $title) {
            $title = trim($title);
            $desc  = trim($perk_descs[$idx] ?? '');
            if ($title && $desc) {
                $perks[] = ['title' => $title, 'desc' => $desc];
            }
        }

        if (empty($items)) {
            $error = 'add at least one item in core qualifications!';
        } else {
            $promise = trim($_POST['promise_text'] ?? '');
            $whyyy   = trim($_POST['whyyy_text'] ?? '');

            $pdo->prepare("UPDATE site_owners SET
                profile_items = ?, promise_text = ?, whyyy_text = ?,
                resume_expectations = ?, resume_skills = ?, resume_perks = ?, instagram_link = ?
                WHERE username = ?")
                ->execute([
                    json_encode($items),
                    $promise,
                    $whyyy,
                    json_encode($expectations),
                    json_encode($skills),
                    json_encode($perks),
                    $instaLink,
                    $username
                ]);

            $stmt  = $pdo->prepare("SELECT * FROM site_owners WHERE username = ?");
            $stmt->execute([$username]);
            $owner             = $stmt->fetch(PDO::FETCH_ASSOC);
            $savedItems        = json_decode($owner['profile_items'], true);
            $savedExpectations = json_decode($owner['resume_expectations'], true);
            $savedSkills       = json_decode($owner['resume_skills'], true);
            $savedPerks        = json_decode($owner['resume_perks'], true);
            $success           = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>edit my profile</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        (function() {
            const t = localStorage.getItem('ownerTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <style>
        .profile-editor {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .profile-editor-row {
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
        }

        .profile-editor-row input,
        .profile-editor-row textarea {
            flex: 1;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.65rem 1rem;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.88rem;
            outline: none;
            transition: border-color 0.2s;
            resize: none;
            line-height: 1.5;
        }

        .profile-editor-row input:focus,
        .profile-editor-row textarea:focus {
            border-color: var(--pink);
        }

        .remove-btn {
            background: transparent;
            border: 1px solid #c87a7a44;
            border-radius: 8px;
            color: #c87a7a;
            padding: 0.5rem 0.7rem;
            cursor: pointer;
            font-size: 0.82rem;
            transition: border-color 0.2s;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .remove-btn:hover { border-color: #c87a7a; }

        .add-item-btn {
            background: transparent;
            border: 1.5px dashed var(--border);
            border-radius: 10px;
            color: var(--muted);
            padding: 0.6rem 1rem;
            cursor: pointer;
            font-size: 0.82rem;
            font-family: 'DM Sans', sans-serif;
            width: 100%;
            transition: border-color 0.2s, color 0.2s;
            margin-top: 0.3rem;
        }

        .add-item-btn:hover {
            border-color: var(--pink);
            color: var(--pink);
        }

        .section-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--pink);
            font-weight: 500;
            margin-bottom: 0.4rem;
            display: block;
        }

        .section-desc {
            color: var(--muted);
            font-size: 0.78rem;
            margin-bottom: 0.8rem;
            line-height: 1.5;
        }

        .resume-section-divider {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--muted);
            margin: 2rem 0 1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .resume-section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .success-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.2s ease;
        }

        .success-modal.show {
            display: flex;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-content {
            background: var(--input-bg);
            border: 1.5px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            max-width: 450px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-content h2 {
            color: var(--text);
            margin-bottom: 0.5rem;
            font-size: 1.4rem;
        }

        .modal-content p {
            color: var(--muted);
            margin-bottom: 1.2rem;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .profile-link-box {
            background: var(--border);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.8rem 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            word-break: break-all;
        }

        .profile-link-box a {
            color: var(--pink);
            text-decoration: none;
            font-size: 0.85rem;
            flex: 1;
            text-align: left;
            transition: opacity 0.2s;
        }

        .profile-link-box a:hover {
            opacity: 0.8;
        }

        .copy-btn {
            background: var(--pink);
            border: none;
            border-radius: 8px;
            color: #fff;
            padding: 0.5rem 0.8rem;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 500;
            transition: opacity 0.2s;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .copy-btn:hover {
            opacity: 0.9;
        }

        .copy-btn.copied {
            background: #6dc88a;
        }

        .modal-actions {
            display: flex;
            gap: 0.8rem;
            justify-content: center;
        }

        .modal-actions button {
            flex: 1;
            padding: 0.75rem 1.2rem;
            border-radius: 8px;
            border: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .modal-actions .btn-primary {
            background: var(--pink);
            color: #fff;
        }

        .modal-actions .btn-primary:hover {
            opacity: 0.9;
        }

        .modal-actions .btn-secondary {
            background: transparent;
            border: 1.5px solid var(--border);
            color: var(--text);
        }

        .modal-actions .btn-secondary:hover {
            border-color: var(--pink);
            color: var(--pink);
        }
    </style>
</head>
<body class="form-page">
<div class="form-container" style="max-width:640px;">

    <div class="owner-topbar">
        <a href="dashboard.php" style="color:var(--muted); text-decoration:none; font-size:0.85rem;">← back</a>
        <a href="logout.php" class="topbar-btn topbar-logout">logout</a>
    </div>

    <h1 style="margin-top:1rem;">edit my profile</h1>
    <p class="subtitle">this is your "resume" shown on the page when your crush is deciding, galingan mo goodluck godbless na lang talaga</p>

    <?php if ($success): ?>
    <div style="background:rgba(100,200,140,0.1); border:1px solid rgba(100,200,140,0.4);
                border-radius:10px; padding:0.85rem 1.2rem; color:#6dc88a; margin:1rem 0; font-size:0.9rem;">
        profile saved!
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div style="background:rgba(224,122,138,0.1); border:1px solid rgba(224,122,138,0.4);
                border-radius:10px; padding:0.85rem 1.2rem; color:#e07a8a; margin:1rem 0; font-size:0.9rem;">
        <?= $error ?>
    </div>
    <?php endif; ?>

    <form method="POST" id="profileForm">

        <!-- ── WHYYY text ── -->
        <div class="resume-section-divider">header text</div>

        <div class="form-group">
            <span class="section-label">text under "WHYYY"</span>
            <input type="text" name="whyyy_text"
                value="<?= htmlspecialchars($owner['whyyy_text'] ?? 'magmakaawa ka dapat dito') ?>"
                placeholder="magmakaawa ka dapat dito"
                style="width:100%; background:var(--input-bg); border:1px solid var(--border);
                       border-radius:10px; padding:0.75rem 1rem; color:var(--text);
                       font-family:'DM Sans',sans-serif; font-size:0.9rem; outline:none;">
        </div>

        <!-- ── Core qualifications ── -->
        <div class="resume-section-divider">core qualifications</div>

        <div class="form-group">
            <span class="section-label">your selling points</span>
            <p class="section-desc">
                these show as bullet points under "core qualifications". ikaw na bahala syempre ako pa ba gagawa nyan
            </p>
            <div class="profile-editor" id="itemsList">
                <?php foreach ($savedItems as $item): ?>
                <div class="profile-editor-row">
                    <input type="text" name="items[]"
                        value="<?= htmlspecialchars($item) ?>"
                        placeholder="add something about yourself...">
                    <button type="button" class="remove-btn" onclick="removeItem(this, 'itemsList')">✕</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="add-item-btn" onclick="addItem('itemsList', 'items[]', 'add something about yourself...')">
                + add another point
            </button>
        </div>

        <!-- ── What you can expect ── -->
        <div class="resume-section-divider">what they can actually expect</div>

        <div class="form-group">
            <span class="section-label">specific things you'd actually do</span>
            <p class="section-desc">
                be specific and genuine. basta nasa baba yung examples, bahala ka na
            </p>
            <div class="profile-editor" id="expectationsList">
                <?php foreach ($savedExpectations as $exp): ?>
                <div class="profile-editor-row">
                    <textarea name="expectations[]" rows="2"
                        placeholder="something specific you'd do..."><?= htmlspecialchars($exp) ?></textarea>
                    <button type="button" class="remove-btn" onclick="removeItem(this, 'expectationsList')">✕</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="add-item-btn" onclick="addTextarea('expectationsList')">
                + add another thing
            </button>
        </div>

        <!-- ── Skills ── -->
        <div class="resume-section-divider">skills & competencies</div>

        <div class="form-group">
            <span class="section-label">your tags / skills</span>
            <p class="section-desc">
                these show as small pill tags. keep them short — 2 to 5 words each. tip: wala, lupitan mo
            </p>
            <div class="profile-editor" id="skillsList">
                <?php foreach ($savedSkills as $skill): ?>
                <div class="profile-editor-row">
                    <input type="text" name="skills[]"
                        value="<?= htmlspecialchars($skill) ?>"
                        placeholder="e.g. ✦ will not ghost">
                    <button type="button" class="remove-btn" onclick="removeItem(this, 'skillsList')">✕</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="add-item-btn" onclick="addItem('skillsList', 'skills[]', 'e.g. ✦ will not ghost')">
                + add skill
            </button>
        </div>

        <!-- ── Date Package Perks ── -->
        <div class="resume-section-divider">date package includes</div>

        <div class="form-group">
            <span class="section-label">perks you're offering</span>
            <p class="section-desc">
                these show as card boxes in the "date package includes" section. add title + description for each.
            </p>
            <div id="perksList" style="display:grid; grid-template-columns:1fr; gap:1rem; margin-bottom:0.5rem;">
                <?php foreach ($savedPerks as $idx => $perk): ?>
                <div style="background:var(--input-bg); border:1.5px solid var(--border); border-radius:12px; 
                            padding:1rem; transition:all 0.2s ease; position:relative;">
                    <div style="display:flex; gap:0.5rem; margin-bottom:0.8rem; align-items:flex-start;">
                        <input type="text" name="perk_title[]"
                            value="<?= htmlspecialchars($perk['title'] ?? '') ?>"
                            placeholder="perk title (e.g. fully covered)"
                            style="flex:1; background:var(--input-bg); border:1px solid var(--border); 
                                   border-radius:8px; padding:0.7rem 0.9rem; color:var(--text); 
                                   font-family:'DM Sans',sans-serif; font-size:0.9rem; font-weight:500; outline:none;">
                        <button type="button" class="remove-btn" onclick="removeItem(this, 'perksList')"
                                style="align-self:center;">✕</button>
                    </div>
                    <textarea name="perk_desc[]" rows="2" placeholder="describe this perk..."
                        style="width:100%; background:var(--input-bg); border:1px solid var(--border); 
                               border-radius:8px; padding:0.7rem 0.9rem; color:var(--text); 
                               font-family:'DM Sans',sans-serif; font-size:0.85rem; resize:none; 
                               outline:none; transition:border-color 0.2s;"><?= htmlspecialchars($perk['desc'] ?? '') ?></textarea>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="add-item-btn" onclick="addPerk('perksList')" style="margin-top:0.5rem;">
                + add perk
            </button>
        </div>

        <!-- ── Promise text ── -->
        <div class="resume-section-divider">closing line</div>

        <div class="form-group">
            <span class="section-label">your promise / closing line</span>
            <input type="text" name="promise_text"
                value="<?= htmlspecialchars($owner['promise_text'] ?? 'di ako masamang tao promise, go out with me please') ?>"
                placeholder="di ako masamang tao promise..."
                style="width:100%; background:var(--input-bg); border:1px solid var(--border);
                       border-radius:10px; padding:0.75rem 1rem; color:var(--text);
                       font-family:'DM Sans',sans-serif; font-size:0.9rem; outline:none;">
            <p style="color:var(--muted); font-size:0.75rem; margin-top:0.4rem;">
                shows at the bottom of your resume
            </p>
        </div>

        <!-- ── Instagram Link ── -->
        <div class="resume-section-divider">contact</div>

        <div class="form-group">
            <span class="section-label">your instagram profile</span>
            <p class="section-desc">
                Please update this to your own Instagram username. This link is used for the “send me a message” button, so make sure it’s correct.<br>

Your link should look like this:
https://instagram.com/sa.loooong.a
            </p>
            <input type="text" name="instagram_link"
                value="<?= htmlspecialchars($owner['instagram_link'] ?? '') ?>"
                placeholder="https://instagram.com/username"
                pattern="https://instagram\.com/[a-zA-Z0-9_.]+"
                title="Must be a full Instagram link: https://instagram.com/username"
                required
                style="width:100%; background:var(--input-bg); border:1px solid var(--border);
                       border-radius:10px; padding:0.75rem 1rem; color:var(--text);
                       font-family:'DM Sans',sans-serif; font-size:0.9rem; outline:none; box-sizing:border-box;">
        </div>

        <button type="submit" class="btn btn-yes" style="width:100%; margin-top:1.5rem; color:#fff;">
            save profile
        </button>
    </form>
</div>

<!-- Success Modal -->
<div class="success-modal" id="successModal">
    <div class="modal-content">
        <h2>Great!</h2>
        <p>Your profile has been saved. Now check it out here:</p>
        <div class="profile-link-box">
            <a id="profileLink" href="" target="_blank"></a>
            <button type="button" class="copy-btn" onclick="copyProfileLink()">copy</button>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-primary" onclick="openProfileLink()">view profile</button>
            <button type="button" class="btn-secondary" onclick="closeSuccessModal()">close</button>
        </div>
    </div>
</div>

<script>
function addItem(listId, fieldName, placeholder) {
    const list = document.getElementById(listId);
    const row  = document.createElement('div');
    row.className = 'profile-editor-row';
    row.innerHTML = `
        <input type="text" name="${fieldName}" placeholder="${placeholder}">
        <button type="button" class="remove-btn" onclick="removeItem(this, '${listId}')">✕</button>
    `;
    list.appendChild(row);
    row.querySelector('input').focus();
}

function addPerk(listId) {
    const list = document.getElementById(listId);
    const row  = document.createElement('div');
    row.style.cssText = `background:var(--input-bg); border:1.5px solid var(--border); border-radius:12px;
                         padding:1rem; transition:all 0.2s ease; position:relative;`;
    row.innerHTML = `
        <div style="display:flex; gap:0.5rem; margin-bottom:0.8rem; align-items:flex-start;">
            <input type="text" name="perk_title[]" placeholder="perk title (e.g. fully covered)"
                   style="flex:1; background:var(--input-bg); border:1px solid var(--border); 
                          border-radius:8px; padding:0.7rem 0.9rem; color:var(--text); 
                          font-family:'DM Sans',sans-serif; font-size:0.9rem; font-weight:500; outline:none;">
            <button type="button" class="remove-btn" onclick="removeItem(this, '${listId}')"
                    style="align-self:center;">✕</button>
        </div>
        <textarea name="perk_desc[]" rows="2" placeholder="describe this perk..."
                  style="width:100%; background:var(--input-bg); border:1px solid var(--border); 
                         border-radius:8px; padding:0.7rem 0.9rem; color:var(--text); 
                         font-family:'DM Sans',sans-serif; font-size:0.85rem; resize:none; 
                         outline:none; transition:border-color 0.2s;"></textarea>
    `;
    list.appendChild(row);
    row.querySelector('input').focus();
}

function addTextarea(listId) {
    const list = document.getElementById(listId);
    const row  = document.createElement('div');
    row.className = 'profile-editor-row';
    row.innerHTML = `
        <textarea name="expectations[]" rows="2"
            placeholder="something specific you'd do..."></textarea>
        <button type="button" class="remove-btn" onclick="removeItem(this, '${listId}')">✕</button>
    `;
    list.appendChild(row);
    row.querySelector('textarea').focus();
}

function removeItem(btn, listId) {
    const list = document.getElementById(listId);
    const rows = list.querySelectorAll('.profile-editor-row');
    if (rows.length <= 1) return;
    btn.closest('.profile-editor-row').remove();
}

function showSuccessModal() {
    const username = '<?= htmlspecialchars($username) ?>';
    // Build the profile URL relative to current page
    const baseUrl = window.location.href.split('/owner/')[0];
    const profileUrl = baseUrl + '/maybe.php?username=' + encodeURIComponent(username);
    const profileLink = document.getElementById('profileLink');
    profileLink.href = profileUrl;
    profileLink.textContent = profileUrl;
    document.getElementById('successModal').classList.add('show');
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.remove('show');
}

function openProfileLink() {
    document.getElementById('profileLink').click();
    closeSuccessModal();
}

function copyProfileLink() {
    const url = document.getElementById('profileLink').href;
    navigator.clipboard.writeText(url).then(() => {
        const btn = event.target;
        btn.textContent = 'copied!';
        btn.classList.add('copied');
        setTimeout(() => {
            btn.textContent = 'copy';
            btn.classList.remove('copied');
        }, 2000);
    }).catch(() => {
        alert('Failed to copy link');
    });
}

<?php if ($success): ?>
window.addEventListener('DOMContentLoaded', showSuccessModal);
<?php endif; ?>
</script>
</body>
</html>
<?php
require 'includes/db.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $type = $_POST['type'] ?? 'general'; // general, bug, suggestion
    
    if (empty($message)) {
        $error = 'please say something! 💭';
    } else {
        $stmt = $pdo->prepare("INSERT INTO feedback (name, message, type, submitted_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$name ?: 'Anonymous', $message, $type]);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>send feedback</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        (function() {
            const t = localStorage.getItem('siteTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .feedback-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 2.5rem;
            max-width: 460px;
            width: 100%;
            animation: fadeUp 0.5s ease;
        }

        .feedback-card h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            margin-bottom: 0.5rem;
            color: var(--text);
        }

        .feedback-card p {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            color: var(--muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.4rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.8rem 1rem;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: var(--pink);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
            font-family: 'DM Sans', sans-serif;
        }

        .form-hint {
            color: var(--muted);
            font-size: 0.75rem;
            margin-top: 0.3rem;
        }

        .success-message {
            background: rgba(100,200,140,0.1);
            border: 1px solid rgba(100,200,140,0.4);
            border-radius: 10px;
            padding: 1rem;
            color: #6dc88a;
            margin-bottom: 1.2rem;
            font-size: 0.9rem;
            text-align: center;
        }

        .error-message {
            background: rgba(224,122,138,0.1);
            border: 1px solid rgba(224,122,138,0.4);
            border-radius: 10px;
            padding: 1rem;
            color: #e07a8a;
            margin-bottom: 1.2rem;
            font-size: 0.9rem;
            text-align: center;
        }

        .button-group {
            display: flex;
            gap: 0.8rem;
        }

        .btn-send {
            flex: 1;
            background: var(--pink);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.9rem;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-send:hover {
            opacity: 0.9;
        }

        .btn-back {
            flex: 1;
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.9rem;
            font-size: 0.9rem;
            cursor: pointer;
            transition: border-color 0.2s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-back:hover {
            border-color: var(--pink);
            color: var(--pink);
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="feedback-card">
        <h1>send feedback 💭</h1>
        <p>found a bug? have a suggestion? just want to say hi? totally anonymous if you'd like.</p>

        <?php if ($success): ?>
        <div class="success-message">
            ✨ thanks for the feedback! means a lot 🙏
        </div>
        <div style="text-align: center; margin-top: 1.5rem;">
            <p style="color: var(--muted); font-size: 0.9rem; margin-bottom: 1rem;">
                appreciate you! 💕
            </p>
            <div style="display: flex; gap: 0.8rem; flex-direction: column;">
                <a href="result.php" class="btn-back" style="width: 100%; text-decoration: none; text-align: center;">← back to result</a>
                <a href="https://instagram.com/sa.loooong.a" target="_blank" class="btn-send" style="text-decoration: none; display: flex; align-items: center; justify-content: center; width: 100%; margin: 0;">
                    💬 message me on instagram
                </a>
            </div>
        </div>
        <?php else: ?>

        <?php if ($error): ?>
        <div class="error-message">
            ⚠️ <?= $error ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="type">what's this about?</label>
                <select name="type" id="type">
                    <option value="general">general feedback</option>
                    <option value="bug">found a bug</option>
                    <option value="suggestion">feature suggestion</option>
                </select>
            </div>

            <div class="form-group">
                <label for="message">your message</label>
                <textarea name="message" id="message" placeholder="be as detailed as you want..." required></textarea>
                <div class="form-hint">this is the important part ✍️</div>
            </div>

            <div class="form-group">
                <label for="name">your name (optional)</label>
                <input type="text" name="name" id="name" placeholder="leave blank to stay anonymous">
                <div class="form-hint">only if you want credit or want me to follow up!</div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn-send">send feedback 📬</button>
                <a href="result.php" class="btn-back" style="text-decoration: none; text-align: center;">back to result</a>
            </div>

            <div style="margin-top: 1rem; text-align: center;">
                <p style="color: var(--muted); font-size: 0.8rem; margin-bottom: 0.8rem;">
                    or message the developer
                </p>
                <a href="https://instagram.com/sa.loooong.a" target="_blank" style="
                    display: inline-block;
                    color: var(--pink);
                    text-decoration: none;
                    font-size: 0.8rem;
                    padding: 0.6rem 1rem;
                    border: 1px solid var(--pink);
                    border-radius: 8px;
                    transition: all 0.2s;
                "
                onmouseover="this.style.backgroundColor='rgba(244,167,185,0.1)'"
                onmouseout="this.style.backgroundColor='transparent'">
                    💬 instagram DM
                </a>
            </div>
        </form>

        <?php endif; ?>
    </div>

    <script>
        function goBack() {
            history.back();
        }
    </script>
</body>
</html>

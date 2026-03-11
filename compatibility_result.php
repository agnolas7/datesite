<?php
session_start();
require 'includes/db.php';
require 'includes/compatibility_questions.php';

if (empty($_SESSION['compat_score']) || empty($_SESSION['compat_answers'])) {
    header('Location: index.php');
    exit;
}

$score            = $_SESSION['compat_score'];
$responder_answers = $_SESSION['compat_answers'];
$response_id      = $_SESSION['response_id'];

// Fetch admin answers for comparison
$adminStmt = $pdo->query("SELECT * FROM admin_compatibility_answers ORDER BY id DESC LIMIT 1");
$admin     = $adminStmt->fetch(PDO::FETCH_ASSOC);

// Score message
if ($score >= 90)      $msg = "Okay this is suspiciously compatible. 👀";
elseif ($score >= 70)  $msg = "This might actually work. 🌸";
elseif ($score >= 50)  $msg = "Some differences but interesting. 🤔";
else                   $msg = "We might argue about movie choices. 😅";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>compatibility results 💘</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script>
        (function() {
            const t = localStorage.getItem('siteTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
</head>
<body class="form-page">
<div class="form-container" style="max-width:780px;">

    <p class="subtitle" style="text-align:center;">the results are in...</p>
    <h1 style="text-align:center; margin-bottom:0.5rem;">compatibility score</h1>

    <!-- Big score display -->
    <div class="compat-score-display">
        <div class="compat-score-circle">
            <span class="compat-score-num"><?= $score ?>%</span>
        </div>
        <p class="compat-score-msg"><?= $msg ?></p>
    </div>

    <!-- Comparison table -->
    <div class="compat-table-wrapper">
        <table class="compat-table">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Your answer</th>
                    <th>My answer</th>
                    <th>Match</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($compatibility_questions as $name => $q):
                $respRaw   = $responder_answers[$name] ?? '';
                $adminRaw  = $admin[$name] ?? '';

                if ($q['type'] === 'checkbox') {
                    $respArr  = array_map('trim', explode(', ', $respRaw));
                    $adminArr = array_map('trim', explode(', ', $adminRaw));
                    $shared   = array_intersect($respArr, $adminArr);
                    $isMatch  = count($shared) > 0;

                    // Highlight shared items
                    $respFormatted = implode(', ', array_map(function($item) use ($shared) {
                        $item = trim($item);
                        return in_array($item, $shared)
                            ? "<span class='match-highlight'>$item</span>"
                            : $item;
                    }, $respArr));

                    $adminFormatted = implode(', ', array_map(function($item) use ($shared) {
                        $item = trim($item);
                        return in_array($item, $shared)
                            ? "<span class='match-highlight'>$item</span>"
                            : $item;
                    }, $adminArr));
                } else {
                    $isMatch        = trim($respRaw) === trim($adminRaw);
                    $respFormatted  = htmlspecialchars($respRaw);
                    $adminFormatted = htmlspecialchars($adminRaw);
                }
            ?>
            <tr class="<?= $isMatch ? 'row-match' : 'row-nomatch' ?>">
                <td class="compat-question-label"><?= htmlspecialchars($q['label']) ?></td>
                <td><?= $respFormatted ?: '<span class="no-answer">—</span>' ?></td>
                <td><?= $adminFormatted ?: '<span class="no-answer">—</span>' ?></td>
                <td class="match-cell">
                    <?php if ($isMatch): ?>
                        <span class="match-yes">✔</span>
                    <?php else: ?>
                        <span class="match-no">✘</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="text-align:center; margin-top:2rem;">
        <a href="result.php" class="btn btn-maybe">← back to result</a>
    </div>

</div>
<script src="js/main.js"></script>
</body>
</html>
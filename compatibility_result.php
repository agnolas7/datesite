<?php
session_start();
require 'includes/db.php';
require 'includes/compatibility_questions.php';

if (empty($_SESSION['compat_score']) || empty($_SESSION['compat_answers'])) {
    header('Location: index.php');
    exit;
}

$responder_answers = $_SESSION['compat_answers'];
$response_id       = $_SESSION['response_id'];

// Fetch the right admin answers for this owner
$ownerStmt = $pdo->prepare("SELECT owner_username FROM responses WHERE id = ?");
$ownerStmt->execute([$response_id]);
$ownerRow  = $ownerStmt->fetch(PDO::FETCH_ASSOC);
$owner_username = $ownerRow['owner_username'] ?? null;

$adminStmt = $pdo->prepare("SELECT * FROM admin_compatibility_answers WHERE owner_username = ? ORDER BY id DESC LIMIT 1");
$adminStmt->execute([$owner_username]);
$admin = $adminStmt->fetch(PDO::FETCH_ASSOC);

// ── Per-question scoring ──
$radio_points      = 10;
$rank_top1_points  = 10;
$rank_top2_points  = 7;
$rank_top3_points  = 4;
$rank_shared_points = 3;

$question_results = []; // store per-question breakdown
$total_points     = 0;
$total_possible   = 0;

foreach ($compatibility_questions as $name => $q) {
    $respRaw  = $responder_answers[$name] ?? '';
    $adminRaw = $admin[$name] ?? '';

    $result = [
        'label'          => $q['label'],
        'type'           => $q['type'],
        'resp_raw'       => $respRaw,
        'admin_raw'      => $adminRaw,
        'points'         => 0,
        'possible'       => 0,
        'percent'        => 0,
        'shared'         => [],
        'resp_formatted' => '',
        'admin_formatted'=> '',
        'is_match'       => false,
    ];

    if ($q['type'] === 'rank') {
        $respArr  = array_filter(array_map('trim', explode(', ', $respRaw)));
        $adminArr = array_filter(array_map('trim', explode(', ', $adminRaw)));

        // possible = top3 slots worth of points
        $result['possible'] = $rank_top1_points + $rank_top2_points + $rank_top3_points;

        // Exact position matches + shared bonus
        foreach ($respArr as $pos => $val) {
            if (!$val) continue;
            if (isset($adminArr[$pos]) && $adminArr[$pos] === $val) {
                if ($pos === 0)      $result['points'] += $rank_top1_points;
                elseif ($pos === 1)  $result['points'] += $rank_top2_points;
                elseif ($pos === 2)  $result['points'] += $rank_top3_points;
                else                 $result['points'] += $rank_shared_points;
                $result['shared'][] = $val;
            } elseif (in_array($val, $adminArr)) {
                $result['points'] += $rank_shared_points;
                $result['shared'][] = $val;
            }
        }

        $result['points']  = min($result['points'], $result['possible']);
        $result['percent'] = $result['possible'] > 0
            ? round(($result['points'] / $result['possible']) * 100)
            : 0;
        $result['is_match'] = count($result['shared']) > 0;

        // Format with rank numbers + highlight shared
        $formatRanked = function($arr, $shared) {
            $out = [];
            foreach (array_values($arr) as $i => $val) {
                $num  = $i + 1;
                $pill = "<span class='rank-num'>#{$num}</span> " . htmlspecialchars($val);
                if (in_array($val, $shared)) {
                    $pill = "<span class='match-highlight'>{$pill}</span>";
                }
                $out[] = $pill;
            }
            return implode('<br>', $out);
        };

        $result['resp_formatted']  = $formatRanked(array_values($respArr),  $result['shared']);
        $result['admin_formatted'] = $formatRanked(array_values($adminArr), $result['shared']);

    } elseif ($q['type'] === 'radio') {
        $result['possible']  = $radio_points;
        $result['is_match']  = trim($respRaw) === trim($adminRaw);
        $result['points']    = $result['is_match'] ? $radio_points : 0;
        $result['percent']   = $result['is_match'] ? 100 : 0;
        $result['resp_formatted']  = htmlspecialchars($respRaw);
        $result['admin_formatted'] = htmlspecialchars($adminRaw);

    } elseif ($q['type'] === 'checkbox') {
        $respArr  = array_filter(array_map('trim', explode(', ', $respRaw)));
        $adminArr = array_filter(array_map('trim', explode(', ', $adminRaw)));
        $shared   = array_intersect($respArr, $adminArr);
        $result['shared']   = array_values($shared);
        $result['possible'] = count($q['options']) * 5;
        $result['points']   = count($shared) * 5;
        $result['percent']  = $result['possible'] > 0
            ? round(($result['points'] / $result['possible']) * 100)
            : 0;
        $result['is_match'] = count($shared) > 0;

        $fmt = function($arr, $shared) {
            return implode(', ', array_map(function($item) use ($shared) {
                $item = trim($item);
                return in_array($item, $shared)
                    ? "<span class='match-highlight'>" . htmlspecialchars($item) . "</span>"
                    : htmlspecialchars($item);
            }, $arr));
        };
        $result['resp_formatted']  = $fmt($respArr, $result['shared']);
        $result['admin_formatted'] = $fmt($adminArr, $result['shared']);
    }

    $total_points   += $result['points'];
    $total_possible += $result['possible'];
    $question_results[$name] = $result;
}

$final_score = $total_possible > 0
    ? min(100, round(($total_points / $total_possible) * 100, 1))
    : 0;

// ── Smart summary builder ──
function buildSummary($question_results, $final_score) {
    $matches   = [];
    $mismatches = [];
    $partials  = [];

    foreach ($question_results as $name => $r) {
        if ($r['percent'] >= 80) $matches[]    = $r['label'];
        elseif ($r['percent'] >= 40) $partials[] = $r['label'];
        else $mismatches[] = $r['label'];
    }

    $lines = [];

    if ($final_score >= 85) {
        $lines[] = "you two are genuinely well-matched across almost everything.";
    } elseif ($final_score >= 70) {
        $lines[] = "there's real overlap here — this could actually work.";
    } elseif ($final_score >= 50) {
        $lines[] = "you're different in some ways but compatible in others.";
    } else {
        $lines[] = "you two have some differences, but different isn't always bad.";
    }

    if (!empty($matches)) {
        $top = array_slice($matches, 0, 3);
        $lines[] = "you're most aligned on: " . implode(', ', $top) . ".";
    }

    if (!empty($mismatches)) {
        $top = array_slice($mismatches, 0, 2);
        $lines[] = "you differ most on: " . implode(', ', $top) . ".";
    }

    if (!empty($partials)) {
        $lines[] = "there's some middle ground on: " . implode(', ', array_slice($partials, 0, 2)) . ".";
    }

    return $lines;
}

$summaryLines = buildSummary($question_results, $final_score);

// Group questions into categories for display
$categories = [
    'taste & interests 🎵'   => ['music_genres','movie_genres','humor_style'],
    'lifestyle & habits 🌙'  => ['energy_level','planning_style','sleep_type','phone_habits','social_battery'],
    'food & preferences 🍜'  => ['food_preference','coffee_preference'],
    'date & social vibes 💬' => ['weekend_activities','crowd_preference','conversation_style','spontaneity_level','getting_to_know','first_date_priority'],
];
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
    <style>
        body.form-page { padding: 2rem 1rem 5rem; }

        .compat-result-wrapper {
            max-width: 760px;
            margin: 0 auto;
            width: 100%;
        }

        /* ── Header ── */
        .compat-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .compat-header-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--pink);
            border: 1px solid var(--pink);
            border-radius: 50px;
            padding: 0.25rem 0.75rem;
            margin-bottom: 1rem;
        }

        .compat-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 5vw, 3rem);
            color: var(--text);
            margin-bottom: 0.3rem;
        }

        /* ── Score circle ── */
        .compat-score-big {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 2rem 0;
        }

        .compat-score-ring {
            position: relative;
            width: 160px;
            height: 160px;
            margin-bottom: 1.2rem;
        }

        .compat-score-ring svg {
            transform: rotate(-90deg);
            width: 160px;
            height: 160px;
        }

        .compat-score-ring circle {
            fill: none;
            stroke-width: 10;
        }

        .compat-ring-bg   { stroke: var(--border); }
        .compat-ring-fill { stroke: var(--pink); stroke-linecap: round; transition: stroke-dashoffset 1s ease; }

        .compat-score-text {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .compat-score-num-big {
            font-family: 'Playfair Display', serif;
            font-size: 2.4rem;
            color: var(--pink);
            line-height: 1;
        }

        .compat-score-sub {
            font-size: 0.7rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Summary box ── */
        .compat-summary {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem 1.8rem;
            margin-bottom: 2.5rem;
        }

        .compat-summary-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--muted);
            margin-bottom: 0.8rem;
        }

        .compat-summary-line {
            font-size: 0.88rem;
            color: var(--text);
            line-height: 1.7;
            padding: 0.3rem 0;
            border-bottom: 1px solid var(--border);
        }

        .compat-summary-line:last-child { border-bottom: none; }

        /* ── Category section ── */
        .compat-category {
            margin-bottom: 2rem;
        }

        .compat-category-header {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.3px;
            color: var(--muted);
            margin-bottom: 0.8rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .compat-category-score {
            font-size: 0.72rem;
            color: var(--pink);
            font-weight: 500;
        }

        /* ── Question card ── */
        .compat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            margin-bottom: 0.8rem;
            overflow: hidden;
            transition: border-color 0.2s;
        }

        .compat-card.card-match    { border-color: rgba(109, 200, 138, 0.3); }
        .compat-card.card-partial  { border-color: rgba(244, 167, 185, 0.2); }
        .compat-card.card-nomatch  { border-color: var(--border); }

        .compat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.8rem 1.2rem;
            border-bottom: 1px solid var(--border);
            gap: 1rem;
        }

        .compat-card-question {
            font-size: 0.8rem;
            color: var(--text);
            font-weight: 500;
            flex: 1;
        }

        .compat-card-bar-wrap {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-shrink: 0;
        }

        .compat-mini-bar {
            width: 80px;
            height: 4px;
            background: var(--border);
            border-radius: 99px;
            overflow: hidden;
        }

        .compat-mini-bar-fill {
            height: 100%;
            border-radius: 99px;
            transition: width 0.8s ease;
        }

        .fill-high   { background: #6dc88a; }
        .fill-mid    { background: var(--pink); }
        .fill-low    { background: #c87a7a; }

        .compat-card-pct {
            font-size: 0.72rem;
            color: var(--muted);
            width: 32px;
            text-align: right;
        }

        .compat-card-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        .compat-answer-col {
            padding: 0.85rem 1.2rem;
        }

        .compat-answer-col:first-child {
            border-right: 1px solid var(--border);
        }

        .compat-answer-who {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--muted);
            margin-bottom: 0.4rem;
        }

        .compat-answer-val {
            font-size: 0.82rem;
            color: var(--text);
            line-height: 1.6;
        }

        .rank-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.6rem;
            color: var(--muted);
            flex-shrink: 0;
            margin-right: 2px;
        }

        /* ── Back button ── */
        .compat-back {
            text-align: center;
            margin-top: 2.5rem;
        }

        @media (max-width: 540px) {
            .compat-card-body { grid-template-columns: 1fr; }
            .compat-answer-col:first-child { border-right: none; border-bottom: 1px solid var(--border); }
            .compat-mini-bar { width: 50px; }
        }
    </style>
</head>
<body class="form-page">
<div class="compat-result-wrapper">

    <!-- Header -->
    <div class="compat-header">
        <div class="compat-header-tag">💘 results</div>
        <h1>compatibility check</h1>
    </div>

    <!-- Score ring -->
    <div class="compat-score-big">
        <?php
        $circumference = 2 * M_PI * 70; // r=70
        $offset = $circumference - ($final_score / 100) * $circumference;
        ?>
        <div class="compat-score-ring">
            <svg viewBox="0 0 160 160">
                <circle class="compat-ring-bg" cx="80" cy="80" r="70"/>
                <circle class="compat-ring-fill" cx="80" cy="80" r="70"
                    stroke-dasharray="<?= $circumference ?>"
                    stroke-dashoffset="<?= $circumference ?>"
                    id="scoreRing"/>
            </svg>
            <div class="compat-score-text">
                <span class="compat-score-num-big"><?= $final_score ?>%</span>
                <span class="compat-score-sub">overall</span>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="compat-summary">
        <div class="compat-summary-label">✦ summary</div>
        <?php foreach ($summaryLines as $line): ?>
        <div class="compat-summary-line"><?= htmlspecialchars($line) ?></div>
        <?php endforeach; ?>
    </div>

    <!-- Per-category breakdown -->
    <?php foreach ($categories as $catLabel => $questionKeys): ?>
    <?php
    // Calculate category score
    $catPoints   = 0;
    $catPossible = 0;
    foreach ($questionKeys as $key) {
        if (isset($question_results[$key])) {
            $catPoints   += $question_results[$key]['points'];
            $catPossible += $question_results[$key]['possible'];
        }
    }
    $catPct = $catPossible > 0 ? round(($catPoints / $catPossible) * 100) : 0;
    ?>
    <div class="compat-category">
        <div class="compat-category-header">
            <span><?= $catLabel ?></span>
            <span class="compat-category-score"><?= $catPct ?>% compatible</span>
        </div>

        <?php foreach ($questionKeys as $key):
            if (!isset($question_results[$key])) continue;
            $r = $question_results[$key];

            if ($r['percent'] >= 70)     $cardClass = 'card-match';
            elseif ($r['percent'] >= 35) $cardClass = 'card-partial';
            else                         $cardClass = 'card-nomatch';

            if ($r['percent'] >= 70)     $fillClass = 'fill-high';
            elseif ($r['percent'] >= 35) $fillClass = 'fill-mid';
            else                         $fillClass = 'fill-low';
        ?>
        <div class="compat-card <?= $cardClass ?>">
            <div class="compat-card-header">
                <span class="compat-card-question"><?= htmlspecialchars($r['label']) ?></span>
                <div class="compat-card-bar-wrap">
                    <div class="compat-mini-bar">
                        <div class="compat-mini-bar-fill <?= $fillClass ?>"
                             style="width:0%"
                             data-width="<?= $r['percent'] ?>%"></div>
                    </div>
                    <span class="compat-card-pct"><?= $r['percent'] ?>%</span>
                </div>
            </div>
            <div class="compat-card-body">
                <div class="compat-answer-col">
                    <div class="compat-answer-who">your answer</div>
                    <div class="compat-answer-val">
                        <?= $r['resp_formatted'] ?: '<span style="color:var(--border)">—</span>' ?>
                    </div>
                </div>
                <div class="compat-answer-col">
                    <div class="compat-answer-who">my answer</div>
                    <div class="compat-answer-val">
                        <?= $r['admin_formatted'] ?: '<span style="color:var(--border)">—</span>' ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

    <!-- Any questions not in a category -->
    <?php
    $categorized = array_merge(...array_values($categories));
    $uncategorized = array_diff(array_keys($question_results), $categorized);
    if (!empty($uncategorized)): ?>
    <div class="compat-category">
        <div class="compat-category-header">
            <span>everything else ✨</span>
        </div>
        <?php foreach ($uncategorized as $key):
            $r = $question_results[$key];
            if ($r['percent'] >= 70)     $cardClass = 'card-match';
            elseif ($r['percent'] >= 35) $cardClass = 'card-partial';
            else                         $cardClass = 'card-nomatch';
            if ($r['percent'] >= 70)     $fillClass = 'fill-high';
            elseif ($r['percent'] >= 35) $fillClass = 'fill-mid';
            else                         $fillClass = 'fill-low';
        ?>
        <div class="compat-card <?= $cardClass ?>">
            <div class="compat-card-header">
                <span class="compat-card-question"><?= htmlspecialchars($r['label']) ?></span>
                <div class="compat-card-bar-wrap">
                    <div class="compat-mini-bar">
                        <div class="compat-mini-bar-fill <?= $fillClass ?>"
                             style="width:0%"
                             data-width="<?= $r['percent'] ?>%"></div>
                    </div>
                    <span class="compat-card-pct"><?= $r['percent'] ?>%</span>
                </div>
            </div>
            <div class="compat-card-body">
                <div class="compat-answer-col">
                    <div class="compat-answer-who">your answer</div>
                    <div class="compat-answer-val"><?= $r['resp_formatted'] ?: '—' ?></div>
                </div>
                <div class="compat-answer-col">
                    <div class="compat-answer-who">my answer</div>
                    <div class="compat-answer-val"><?= $r['admin_formatted'] ?: '—' ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="compat-back">
        <a href="result.php" class="btn btn-maybe">← back to result</a>
    </div>

</div>

<script src="js/main.js"></script>
<script>
// Animate score ring on load
window.addEventListener('load', () => {
    const ring = document.getElementById('scoreRing');
    const circumference = <?= $circumference ?>;
    const score = <?= $final_score ?>;
    const targetOffset = circumference - (score / 100) * circumference;

    setTimeout(() => {
        ring.style.strokeDashoffset = targetOffset;
    }, 200);

    // Animate mini bars
    document.querySelectorAll('.compat-mini-bar-fill').forEach(bar => {
        setTimeout(() => {
            bar.style.width = bar.dataset.width;
        }, 400);
    });
});
</script>
</body>
</html>
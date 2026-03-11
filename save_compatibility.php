<?php
session_start();
require 'includes/db.php';
require 'includes/compatibility_questions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['response_id'])) {
    header('Location: index.php');
    exit;
}

$response_id = $_SESSION['response_id'];

// ── Collect answers ──
$data = [];
foreach ($compatibility_questions as $name => $q) {
    if ($q['type'] === 'rank') {
        // comes in as ordered array
        $data[$name] = isset($_POST[$name]) ? implode(', ', $_POST[$name]) : '';
    } elseif ($q['type'] === 'checkbox') {
        $data[$name] = isset($_POST[$name]) ? implode(', ', $_POST[$name]) : '';
    } else {
        $data[$name] = $_POST[$name] ?? '';
    }
}

// ── Fetch admin answers ──
$ownerStmt = $pdo->prepare("SELECT owner_username FROM responses WHERE id = ?");
$ownerStmt->execute([$response_id]);
$ownerRow       = $ownerStmt->fetch(PDO::FETCH_ASSOC);
$owner_username = $ownerRow['owner_username'] ?? null;

$adminStmt = $pdo->prepare("SELECT * FROM admin_compatibility_answers WHERE owner_username = ? ORDER BY id DESC LIMIT 1");
$adminStmt->execute([$owner_username]);
$admin = $adminStmt->fetch(PDO::FETCH_ASSOC);

// ── Scoring ──
$points_matched = 0;
$total_possible = 0;

// Points per question type
$radio_points     = 10;
$rank_top1_points = 10; // #1 pick matches = 10pts
$rank_top2_points = 7;  // #2 pick matches = 7pts
$rank_top3_points = 4;  // #3 pick matches = 4pts
// shared item anywhere in list = bonus
$rank_shared_points = 3;

if ($admin) {
    foreach ($compatibility_questions as $name => $q) {

        if ($q['type'] === 'radio') {
            $total_possible += $radio_points;
            if (trim($admin[$name] ?? '') === trim($data[$name])) {
                $points_matched += $radio_points;
            }
        }

        elseif ($q['type'] === 'rank') {
            $adminRanked    = array_map('trim', explode(', ', $admin[$name] ?? ''));
            $responderRanked = array_map('trim', explode(', ', $data[$name]));

            // Max possible: top1 + top2 + top3 + shared bonus for each rank slot
            $slots = min(count($adminRanked), count($responderRanked), 3);
            $total_possible += $rank_top1_points + $rank_top2_points + $rank_top3_points;

            // Exact position matches
            foreach ($responderRanked as $pos => $val) {
                if (!$val) continue;
                if (isset($adminRanked[$pos]) && $adminRanked[$pos] === $val) {
                    if ($pos === 0) $points_matched += $rank_top1_points;
                    elseif ($pos === 1) $points_matched += $rank_top2_points;
                    elseif ($pos === 2) $points_matched += $rank_top3_points;
                } elseif (in_array($val, $adminRanked)) {
                    // in list but different position — partial credit
                    $points_matched += $rank_shared_points;
                }
            }
        }
    }
}

// Clamp to 100
$score = $total_possible > 0
    ? min(100, round(($points_matched / $total_possible) * 100, 1))
    : 0;

// ── Save ──
$pdo->prepare("DELETE FROM responder_compatibility_answers WHERE response_id = ?")
    ->execute([$response_id]);

$cols         = implode(', ', array_keys($data));
$placeholders = implode(', ', array_fill(0, count($data), '?'));
$vals         = array_values($data);

$stmt = $pdo->prepare("INSERT INTO responder_compatibility_answers
    (response_id, $cols, compatibility_score)
    VALUES (?, $placeholders, ?)");
$stmt->execute(array_merge([$response_id], $vals, [$score]));

$_SESSION['compat_score']   = $score;
$_SESSION['compat_answers'] = $data;

header('Location: compatibility_result.php');
exit;
?>
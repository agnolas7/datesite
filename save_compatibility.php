<?php
session_start();
require 'includes/db.php';
require 'includes/compatibility_questions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['response_id'])) {
    header('Location: index.php');
    exit;
}

$response_id = $_SESSION['response_id'];

// Collect answers
$data = [];
foreach ($compatibility_questions as $name => $q) {
    if ($q['type'] === 'checkbox') {
        $data[$name] = isset($_POST[$name]) ? implode(', ', $_POST[$name]) : '';
    } else {
        $data[$name] = $_POST[$name] ?? '';
    }
}

// ── Fetch admin answers ──
$adminStmt = $pdo->query("SELECT * FROM admin_compatibility_answers ORDER BY id DESC LIMIT 1");
$admin = $adminStmt->fetch(PDO::FETCH_ASSOC);

// ── Score calculation ──
$points_matched   = 0;
$total_possible   = 0;
$checkbox_points  = 5;
$radio_points     = 10;

if ($admin) {
    foreach ($compatibility_questions as $name => $q) {
        if ($q['type'] === 'checkbox') {
            $adminOpts     = array_map('trim', explode(', ', $admin[$name] ?? ''));
            $responderOpts = array_map('trim', explode(', ', $data[$name]));
            $allOptions    = count($q['options']);
            $total_possible += $allOptions * $checkbox_points;
            $shared = array_intersect($adminOpts, $responderOpts);
            $points_matched += count($shared) * $checkbox_points;
        } else {
            $total_possible += $radio_points;
            if (trim($admin[$name] ?? '') === trim($data[$name])) {
                $points_matched += $radio_points;
            }
        }
    }
}

$score = $total_possible > 0 ? round(($points_matched / $total_possible) * 100, 1) : 0;

// ── Save to DB ──
// Delete old answer for this response_id if they retake it
$pdo->prepare("DELETE FROM responder_compatibility_answers WHERE response_id = ?")->execute([$response_id]);

$cols = implode(', ', array_keys($data));
$placeholders = implode(', ', array_fill(0, count($data), '?'));
$vals = array_values($data);

$stmt = $pdo->prepare("INSERT INTO responder_compatibility_answers 
    (response_id, $cols, compatibility_score) 
    VALUES (?, $placeholders, ?)");

$stmt->execute(array_merge([$response_id], $vals, [$score]));

$_SESSION['compat_score'] = $score;
$_SESSION['compat_answers'] = $data;

header('Location: compatibility_result.php');
exit;
?>
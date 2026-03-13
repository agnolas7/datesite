<?php
session_start();
require 'includes/db.php';

if (empty($_SESSION['response_id'])) {
    header('Location: index.php');
    exit;
}

$id = $_SESSION['response_id'];
$stmt = $pdo->prepare("SELECT * FROM responses WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    die("Response not found");
}

$name = htmlspecialchars($_SESSION['name']);

// Fetch compatibility score
$compatStmt = $pdo->prepare("SELECT compatibility_score FROM responder_compatibility_answers WHERE response_id = ? ORDER BY id DESC LIMIT 1");
$compatStmt->execute([$id]);
$compatRow = $compatStmt->fetch(PDO::FETCH_ASSOC);
$compatScore = $compatRow['compatibility_score'] ?? null;

// Helper to format empty values
function val($v) {
    return trim($v) ?: '—';
}

// Format scheduled date
$displayDate = '';
if (!empty($row['scheduled_date'])) {
    try {
        $d = new DateTime($row['scheduled_date']);
        $displayDate = $d->format('l, F j, Y · g:i A');
    } catch (Exception $e) {
        $displayDate = $row['scheduled_date'];
    }
}

// Build HTML
$html = <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Date Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f5f5f5 0%, #fafafa 100%);
            padding: 2rem;
            color: #333;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #f4a7b9 0%, #f4a7b9 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }

        .header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .header p {
            font-size: 0.95rem;
            opacity: 0.95;
        }

        .content {
            padding: 2.5rem 2rem;
        }

        .meta-info {
            background: #f9f9f9;
            border-radius: 12px;
            padding: 1rem 1.2rem;
            margin-bottom: 2rem;
            font-size: 0.85rem;
            color: #666;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .meta-item {
            display: flex;
            justify-content: space-between;
        }

        .meta-label {
            font-weight: 500;
            color: #999;
        }

        .section {
            margin-bottom: 2.5rem;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.2rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #f4a7b9;
        }

        .section-icon {
            font-size: 1.4rem;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            color: #333;
            font-weight: 700;
        }

        .section-subtitle {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #f4a7b9;
            font-weight: 600;
        }

        .field {
            margin-bottom: 1.2rem;
        }

        .field-label {
            font-weight: 600;
            color: #f4a7b9;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.4rem;
        }

        .field-value {
            font-size: 1rem;
            color: #333;
            line-height: 1.6;
            padding: 0.6rem;
            background: #f9f9f9;
            border-radius: 8px;
            border-left: 3px solid #f4a7b9;
            padding-left: 0.9rem;
        }

        .field-value.empty {
            color: #bbb;
            font-style: italic;
        }

        .field-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0.6rem;
            background: #f9f9f9;
            border-radius: 8px;
            border-left: 3px solid #f4a7b9;
            padding-left: 0.9rem;
        }

        .tag {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 50px;
            padding: 0.35rem 0.8rem;
            font-size: 0.8rem;
            color: #333;
        }

        .banner {
            background: rgba(244, 167, 185, 0.08);
            border: 2px solid #f4a7b9;
            border-radius: 12px;
            padding: 1.2rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .banner-icon {
            font-size: 2rem;
            flex-shrink: 0;
        }

        .banner-content {
            flex: 1;
        }

        .banner-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #f4a7b9;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .banner-value {
            font-size: 1rem;
            color: #333;
            font-weight: 600;
        }

        .score-badge {
            display: inline-block;
            background: linear-gradient(135deg, #f4a7b9 0%, #e89aae 100%);
            color: white;
            padding: 0.8rem 1.6rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .footer {
            background: #f9f9f9;
            padding: 1.5rem 2rem;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            color: #999;
            font-size: 0.85rem;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.2rem;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .container {
                box-shadow: none;
                border-radius: 0;
            }
        }

        @media (max-width: 600px) {
            .header {
                padding: 2rem 1.5rem;
            }
            .header h1 {
                font-size: 1.8rem;
            }
            .content {
                padding: 1.5rem;
            }
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ You're In! 🎉</h1>
            <p>Your Date Profile</p>
        </div>

        <div class="content">
            <!-- Meta Info -->
            <div class="meta-info">
                <div class="meta-item">
                    <span class="meta-label">Response ID:</span>
                    <span>#HTML_ID</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Downloaded:</span>
                    <span>HTML_DATE</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Submitted:</span>
                    <span>HTML_SUBMITTED</span>
                </div>
            </div>

            <!-- Compatibility Banner -->
            HTML_COMPAT_BANNER

            <!-- Scheduled Date Banner -->
            HTML_SCHEDULED_BANNER

            <!-- About You -->
            <div class="section">
                <div class="section-header">
                    <span class="section-icon">👤</span>
                    <div>
                        <div class="section-subtitle">Section 1</div>
                        <div class="section-title">About You</div>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="field">
                        <div class="field-label">Name / Nickname</div>
                        <div class="field-value">HTML_NAME</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Age</div>
                        <div class="field-value">HTML_AGE</div>
                    </div>
                    <div class="field" style="grid-column: 1 / -1;">
                        <div class="field-label">City</div>
                        <div class="field-value">HTML_CITY</div>
                    </div>
                </div>
            </div>

            <!-- Your Preferences -->
            <div class="section">
                <div class="section-header">
                    <span class="section-icon">💭</span>
                    <div>
                        <div class="section-subtitle">Section 2</div>
                        <div class="section-title">Your Preferences</div>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="field">
                        <div class="field-label">Favorite food/drink</div>
                        <div class="field-value">HTML_FOOD</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Favorite flower</div>
                        <div class="field-value">HTML_FLOWER</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Craving right now</div>
                        <div class="field-value">HTML_CRAVING</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Temperature preference</div>
                        <div class="field-value">HTML_TEMP</div>
                    </div>
                </div>
                <div class="field">
                    <div class="field-label">Things you don't enjoy</div>
                    <div class="field-value">HTML_DONT_ENJOY</div>
                </div>
                <div class="field">
                    <div class="field-label">Dessert situation</div>
                    <div class="field-value">HTML_DESSERT</div>
                </div>
                <div class="field">
                    <div class="field-label">Dealbreaker</div>
                    <div class="field-value">HTML_DEALBREAKER</div>
                </div>
            </div>

            <!-- How to Reach You -->
            <div class="section">
                <div class="section-header">
                    <span class="section-icon">📞</span>
                    <div>
                        <div class="section-subtitle">Section 3</div>
                        <div class="section-title">How to Reach You</div>
                    </div>
                </div>
                <div class="field">
                    <div class="field-label">Communication</div>
                    <div class="field-value">HTML_COMM</div>
                </div>
                <div class="field">
                    <div class="field-label">Best time to contact</div>
                    <div class="field-value">HTML_BEST_TIME</div>
                </div>
            </div>

            <!-- Date Preferences -->
            <div class="section">
                <div class="section-header">
                    <span class="section-icon">📅</span>
                    <div>
                        <div class="section-subtitle">Section 4</div>
                        <div class="section-title">Your Date Preferences</div>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="field">
                        <div class="field-label">Date type</div>
                        <div class="field-value">HTML_DATE_TYPE</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Spontaneity</div>
                        <div class="field-value">HTML_SPONTANEITY</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Energy level</div>
                        <div class="field-value">HTML_ENERGY</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Mood</div>
                        <div class="field-value">HTML_MOOD</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Crowd comfort</div>
                        <div class="field-value">HTML_CROWD</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Walking/activity</div>
                        <div class="field-value">HTML_WALKING</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Conversation style</div>
                        <div class="field-value">HTML_CONVO_STYLE</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Awkwardness level</div>
                        <div class="field-value">HTML_AWKWARD</div>
                    </div>
                    <div class="field" style="grid-column: 1 / -1;">
                        <div class="field-label">Conversation difficulty</div>
                        <div class="field-value">HTML_CONVO_DIFF</div>
                    </div>
                </div>
            </div>

            <!-- Vibe -->
            <div class="section">
                <div class="section-header">
                    <span class="section-icon">✨</span>
                    <div>
                        <div class="section-subtitle">Section 5</div>
                        <div class="section-title">Your Vibe</div>
                    </div>
                </div>
                <div class="field">
                    <div class="field-label">Vibes</div>
                    <div class="field-value">HTML_VIBES</div>
                </div>
                HTML_CUSTOM_VIBE
            </div>
        </div>

        <div class="footer">
            <p>This is your personal copy of your profile answers.</p>
            <p style="margin-top: 0.5rem; opacity: 0.7;">Keep it safe! 🌸</p>
        </div>
    </div>
</body>
</html>
HTML;

// Replace placeholders
$html = str_replace('HTML_ID', $id, $html);
$html = str_replace('HTML_DATE', date('F j, Y \a\t g:i A'), $html);
$html = str_replace('HTML_SUBMITTED', htmlspecialchars($row['submitted_at']), $html);

// Compatibility banner
if ($compatScore !== null) {
    $html = str_replace('HTML_COMPAT_BANNER', '
            <div class="banner">
                <span class="banner-icon">💘</span>
                <div class="banner-content">
                    <div class="banner-label">Compatibility Score</div>
                    <div><span class="score-badge">' . round($compatScore) . '%</span></div>
                </div>
            </div>
    ', $html);
} else {
    $html = str_replace('HTML_COMPAT_BANNER', '', $html);
}

// Scheduled date banner
if (!empty($displayDate)) {
    $html = str_replace('HTML_SCHEDULED_BANNER', '
            <div class="banner">
                <span class="banner-icon">🗓️</span>
                <div class="banner-content">
                    <div class="banner-label">Date Scheduled</div>
                    <div class="banner-value">' . htmlspecialchars($displayDate) . '</div>
                </div>
            </div>
    ', $html);
} else {
    $html = str_replace('HTML_SCHEDULED_BANNER', '', $html);
}

// Replace field values
$html = str_replace('HTML_NAME', htmlspecialchars($row['name']), $html);
$html = str_replace('HTML_AGE', htmlspecialchars($row['age']), $html);
$html = str_replace('HTML_CITY', htmlspecialchars($row['city']), $html);
$html = str_replace('HTML_FOOD', htmlspecialchars(val($row['food_drink'])), $html);
$html = str_replace('HTML_FLOWER', htmlspecialchars(val($row['flower'])), $html);
$html = str_replace('HTML_CRAVING', htmlspecialchars(val($row['craving'])), $html);
$html = str_replace('HTML_TEMP', htmlspecialchars(val($row['temperature'])), $html);
$html = str_replace('HTML_DONT_ENJOY', htmlspecialchars(val($row['dont_enjoy'])), $html);
$html = str_replace('HTML_DESSERT', htmlspecialchars(val($row['dessert'])), $html);
$html = str_replace('HTML_DEALBREAKER', htmlspecialchars(val($row['dealbreaker'])), $html);
$html = str_replace('HTML_COMM', htmlspecialchars(val($row['communication'])), $html);
$html = str_replace('HTML_BEST_TIME', htmlspecialchars(val($row['best_time'])), $html);
$html = str_replace('HTML_DATE_TYPE', htmlspecialchars(val($row['date_type'])), $html);
$html = str_replace('HTML_SPONTANEITY', htmlspecialchars(val($row['spontaneity'])), $html);
$html = str_replace('HTML_ENERGY', htmlspecialchars(val($row['energy'])), $html);
$html = str_replace('HTML_MOOD', htmlspecialchars(val($row['mood'])), $html);
$html = str_replace('HTML_CROWD', htmlspecialchars(val($row['crowd'])), $html);
$html = str_replace('HTML_WALKING', htmlspecialchars(val($row['walking'])), $html);
$html = str_replace('HTML_CONVO_STYLE', htmlspecialchars(val($row['convo_style'])), $html);
$html = str_replace('HTML_AWKWARD', htmlspecialchars(val($row['awkwardness'])), $html);
$html = str_replace('HTML_CONVO_DIFF', htmlspecialchars(val($row['convo_difficulty'])), $html);
$html = str_replace('HTML_VIBES', htmlspecialchars(val($row['vibes'])), $html);

// Custom vibe
if (!empty($row['custom_vibe'])) {
    $html = str_replace('HTML_CUSTOM_VIBE', '
                <div class="field">
                    <div class="field-label">Custom Vibe</div>
                    <div class="field-value">' . htmlspecialchars($row['custom_vibe']) . '</div>
                </div>
    ', $html);
} else {
    $html = str_replace('HTML_CUSTOM_VIBE', '', $html);
}

// Send as download
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: attachment; filename="date_profile_' . $id . '.html"');
echo $html;
?>


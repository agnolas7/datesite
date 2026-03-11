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
    <title>date preferences 📋</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body class="form-page">
<div class="form-container">
    <h1>the important questions 🔍</h1>
    <p class="subtitle">what kind of date are we talking?</p>

    <form action="save_preferences.php" method="POST">

        <?php
        $questions = [
            ['label' => 'What kind of date sounds best? 🗓️', 'name' => 'date_type', 'options' => ['Cozy indoor date','Outdoor adventure','Food trip','Nice view','Random spontaneous hang out','Surprise me']],
            ['label' => 'Spontaneity level ⚡', 'name' => 'spontaneity', 'options' => ['Yes please','A little structure',"Let's wing it",'Chaos']],
            ['label' => 'Energy level 🔋', 'name' => 'energy', 'options' => ['Chill','Medium','Active','Illegal activities (joke)']],
            ['label' => 'First date mood 🌙', 'name' => 'mood', 'options' => ['Relaxed','Playful','Adventurous','Slightly awkward but fun']],
            ['label' => 'Crowd preference 👥', 'name' => 'crowd', 'options' => ['Quiet','Some people','Busy',"Doesn't matter"]],
            ['label' => 'Conversation style 💬', 'name' => 'convo_style', 'options' => ['Deep talks','Random funny stuff','Getting to know each other','Bahala na']],
            ['label' => 'Walking tolerance 👟', 'name' => 'walking', 'options' => ['Minimal','Some walking','A lot','If we get lost we get lost']],
            ['label' => 'Awkwardness level 😅', 'name' => 'awkwardness', 'options' => ['Very','A little','Smooth',"I'll carry the conversation"]],
            ['label' => 'Conversation difficulty 🎮', 'name' => 'convo_difficulty', 'options' => ['Easy mode','Medium difficulty','Hard mode','Legendary boss fight']],
        ];

        foreach ($questions as $q) {
            echo "<div class='form-group'>";
            echo "<label>{$q['label']}</label>";
            echo "<div class='radio-group'>";
            foreach ($q['options'] as $opt) {
                $safe = htmlspecialchars($opt);
                echo "<label class='radio-item'><input type='radio' name='{$q['name']}' value='$safe'> $safe</label>";
            }
            echo "</div></div>";
        }
        ?>

        <div class="form-group">
            <label>Vibe check ✨ (pick all that apply)</label>
            <div class="checkbox-group">
                <?php
                $vibes = ['Coffee shop','Night drive','Arcade / games','Watch a movie','Street food crawl','Stroll','Parking lot hangout','Beer and smoke','Nature','Dinner','Lunch','Creative activities'];
                foreach ($vibes as $v) {
                    echo "<label class='check-item'><input type='checkbox' name='vibes[]' value='$v'> $v</label>";
                }
                ?>
            </div>
        </div>

        <div class="form-group">
            <label>Your own idea? 💡</label>
            <input type="text" name="custom_vibe" placeholder="suggest something...">
        </div>

        <button type="submit" class="btn btn-yes" style="width:100%; margin-top:1rem;">
            done! 🌸
        </button>
    </form>
</div>
<script src="js/main.js"></script>
</body>
</html>
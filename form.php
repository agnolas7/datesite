<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tell me about yourself ✨</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body class="form-page">
<div class="form-container">
    <h1>let's get to know you 💌</h1>
    <p class="subtitle">fill this out honestly ha</p>

    <form action="save_form.php" method="POST">

        <div class="form-group">
            <label>Name / Nickname *</label>
            <input type="text" name="name" required placeholder="what do i call you?">
        </div>

        <div class="form-group">
            <label>Age</label>
            <select name="age">
                <option value="">-- pick one --</option>
                <option>19</option>
                <option>20</option>
                <option>21</option>
                <option>22</option>
                <option>23+</option>
            </select>
        </div>

        <div class="form-group">
            <label>City / Location</label>
            <input type="text" name="city" placeholder="where are you from?">
        </div>

        <div class="form-group">
            <label>Preferred communication</label>
            <div class="checkbox-group">
                <?php
                $comms = ['Instagram','Messenger','Text','Twitter','Telegram','Shopee message','I\'ll just pull up to your house','liham','In our dreams'];
                foreach($comms as $c) {
                    echo "<label class='check-item'><input type='checkbox' name='communication[]' value='$c'> $c</label>";
                }
                ?>
            </div>
        </div>

        <div class="form-group">
            <label>Best time for a date</label>
            <div class="radio-group">
                <?php
                $times = ['Morning coffee','Afternoon hangout','Sunset','Night vibes'];
                foreach($times as $t) {
                    echo "<label class='radio-item'><input type='radio' name='best_time' value='$t'> $t</label>";
                }
                ?>
            </div>
        </div>

        <div class="form-group">
            <label>Favorite food and drink 🍜</label>
            <input type="text" name="food_drink" placeholder="tell me what you like...">
        </div>

        <div class="form-group">
            <label>Dealbreaker for you?</label>
            <div class="radio-group">
                <?php
                $deals = ['Bad music taste','Pineapple on pizza','Slow walkers','None I\'m chill'];
                foreach($deals as $d) {
                    echo "<label class='radio-item'><input type='radio' name='dealbreaker' value='$d'> $d</label>";
                }
                ?>
            </div>
        </div>

        <button type="submit" class="btn btn-yes" style="width:100%; margin-top:1rem;">
            submit 🌸
        </button>

    </form>
</div>
<script src="js/main.js"></script>
</body>
</html>
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tell me about yourself ✨</title>
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
<div class="form-container">
    <h1>let's get to know you 💌</h1>
    <p class="subtitle">fill this out honestly !!</p>

    <!-- validation error banner -->
    <div class="validation-banner hidden" id="validationBanner">
        <span class="validation-icon">⚠️</span>
        <span id="validationMsg">please answer all questions before continuing!</span>
    </div>

    <form id="mainForm" action="save_form.php" method="POST" novalidate>

        <div class="form-group" id="group-name">
            <label>Name / Nickname <span class="required-star">*</span></label>
            <input type="text" name="name" id="field-name" placeholder="what do i call you?">
            <span class="field-error hidden">this one's required, kahit nickname lang boss</span>
        </div>

        <div class="form-group" id="group-age">
            <label>Age <span class="required-star">*</span></label>
            <select name="age" id="field-age">
                <option value="">-- pick one --</option>
                <option>19</option>
                <option>20</option>
                <option>21</option>
                <option>22</option>
                <option>23+</option>
            </select>
            <span class="field-error hidden">please pick your age, hard pass sa minor</span>
        </div>

        <div class="form-group" id="group-city">
            <label>City / Location <span class="required-star">*</span></label>
            <input type="text" name="city" id="field-city" placeholder="or where should i pick you up?">
            <span class="field-error hidden">kahit kung san lang magmemeet g</span>
        </div>

        <div class="form-group" id="group-communication">
            <label>Preferred communication <span class="required-star">*</span></label>
            <div class="checkbox-group" id="field-communication">
                <?php
                $comms = ['Instagram','Messenger','Text','Twitter','Telegram','Shopee message','I\'ll just pull up to your house','liham','In our dreams'];
                foreach($comms as $c) {
                    echo "<label class='check-item'><input type='checkbox' name='communication[]' value='$c'> $c</label>";
                }
                ?>
            </div>
            <span class="field-error hidden">pick at least one way to reach you pls</span>
        </div>

        <div class="form-group" id="group-best_time">
            <label>Best time for a date <span class="required-star">*</span></label>
            <div class="radio-group">
                <?php
                $times = ['Morning coffee','Afternoon hangout','Sunset','Night vibes'];
                foreach($times as $t) {
                    echo "<label class='radio-item'><input type='radio' name='best_time' value='$t'> $t</label>";
                }
                ?>
            </div>
            <span class="field-error hidden">pick a time that works for you pls</span>
        </div>

        <div class="form-group" id="group-food_drink">
            <label>Favorite food and drink 🍜 <span class="required-star">*</span></label>
            <input type="text" name="food_drink" id="field-food_drink" placeholder="tell me what you like...">
            <span class="field-error hidden">food is important!! tell me</span>
        </div>

        <div class="form-group" id="group-dealbreaker">
            <label>Dealbreaker for you? <span class="required-star">*</span></label>
            <div class="radio-group">
                <?php
                $deals = ['Bad music taste','Pineapple on pizza','Slow walkers','None I\'m chill'];
                foreach($deals as $d) {
                    echo "<label class='radio-item'><input type='radio' name='dealbreaker' value='$d'> $d</label>";
                }
                ?>
            </div>
            <span class="field-error hidden">be honest nyahaha</span>
        </div>

        <button type="submit" class="btn btn-yes" style="width:100%; margin-top:1rem;">
            submit
        </button>

    </form>
</div>
<script src="js/main.js"></script>
<script>
document.getElementById('mainForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let hasError = false;

    // Clear all previous errors
    document.querySelectorAll('.form-group').forEach(g => g.classList.remove('has-error'));
    document.querySelectorAll('.field-error').forEach(el => el.classList.add('hidden'));
    document.getElementById('validationBanner').classList.add('hidden');

    // Name
    const name = document.getElementById('field-name').value.trim();
    if (!name) {
        markError('group-name');
        hasError = true;
    }

    // Age
    const age = document.getElementById('field-age').value;
    if (!age) {
        markError('group-age');
        hasError = true;
    }

    // City
    const city = document.getElementById('field-city').value.trim();
    if (!city) {
        markError('group-city');
        hasError = true;
    }

    // Communication (at least one checkbox)
    const commsChecked = document.querySelectorAll('input[name="communication[]"]:checked');
    if (commsChecked.length === 0) {
        markError('group-communication');
        hasError = true;
    }

    // Best time (radio)
    const bestTime = document.querySelector('input[name="best_time"]:checked');
    if (!bestTime) {
        markError('group-best_time');
        hasError = true;
    }

    // Food & drink
    const food = document.getElementById('field-food_drink').value.trim();
    if (!food) {
        markError('group-food_drink');
        hasError = true;
    }

    // Dealbreaker (radio)
    const deal = document.querySelector('input[name="dealbreaker"]:checked');
    if (!deal) {
        markError('group-dealbreaker');
        hasError = true;
    }

    if (hasError) {
        // Show banner and scroll to first error
        const banner = document.getElementById('validationBanner');
        banner.classList.remove('hidden');
        const firstError = document.querySelector('.form-group.has-error');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
    }

    // All good — submit
    this.submit();
});

function markError(groupId) {
    const group = document.getElementById(groupId);
    group.classList.add('has-error');
    group.querySelector('.field-error').classList.remove('hidden');
}

// Live clear error when user interacts with a field
document.querySelectorAll('input, select').forEach(input => {
    input.addEventListener('change', function() {
        const group = this.closest('.form-group');
        if (group && group.classList.contains('has-error')) {
            group.classList.remove('has-error');
            group.querySelector('.field-error').classList.add('hidden');
            // Hide banner if no more errors
            if (!document.querySelector('.form-group.has-error')) {
                document.getElementById('validationBanner').classList.add('hidden');
            }
        }
    });
});

document.querySelectorAll('input[type="text"]').forEach(input => {
    input.addEventListener('input', function() {
        const group = this.closest('.form-group');
        if (group && group.classList.contains('has-error') && this.value.trim()) {
            group.classList.remove('has-error');
            group.querySelector('.field-error').classList.add('hidden');
            if (!document.querySelector('.form-group.has-error')) {
                document.getElementById('validationBanner').classList.add('hidden');
            }
        }
    });
});
</script>
</body>
</html>
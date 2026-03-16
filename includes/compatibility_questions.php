<?php
$compatibility_questions = [

    'music_genres' => [
        'label'   => 'Music genres you actually listen to — rank your favorites',
        'type'    => 'rank',
        'max'     => 99,
        'min'     => 2,
        'options' => ['OPM','Pop','Indie','R&B','Hip-hop / Rap','Rock','Pop Rock','Alternative','Jazz','Classical','Electronic / EDM','Metal','Country','K-pop'],
    ],
    'movie_genres' => [
        'label'   => 'Movie / series genres — rank what you actually watch',
        'type'    => 'rank',
        'max'     => 99,
        'min'     => 2,
        'options' => ['Romance','Romcom','Comedy','Horror','Thriller','Action','Sci-fi','Fantasy','Documentary','Anime','K-drama','Crime / Mystery','Animation'],
    ],
    'weekend_activities' => [
        'label'   => 'Ideal weekend activities — rank what you\'d actually do',
        'type'    => 'rank',
        'max'     => 99,
        'min'     => 2,
        'options' => ['Food trips','Coffee shop hopping','Night drives','Watching movies at home','Going to the mall','Nature / outdoors','Arcade / games','Art galleries','Concerts / events','Just staying in bed','Random drives','Chill night out','Bar/Clubbing'],
    ],
    'humor_style' => [
        'label'   => 'Your humor style — rank what actually fits you',
        'type'    => 'rank',
        'max'     => 99,
        'min'     => 2,
        'options' => ['Wholesome','Witty','Brainrot','Chronically online humor','Dark humor','Sarcasm'],
    ],

    // ── Radios ──
    'energy_level' => [
        'label'   => 'Your general energy level',
        'type'    => 'radio',
        'options' => ['Low — chill lang talaga','Medium — depends on the day','High — always doing something','Chaotic'],
    ],
    'planning_style' => [
        'label'   => 'Planning style',
        'type'    => 'radio',
        'options' => ['I plan everything in advance','I have a rough idea','I just wing it','What\'s a plan?'],
    ],
    'food_preference' => [
        'label'   => 'Food preference',
        'type'    => 'radio',
        'options' => ['Filipino food always','Anything as long as it\'s good','I like trying new stuff','I eat the same 5 things','Vegan', 'Spicy food enojyer', 'Sweet tooth'],
    ],
    'coffee_preference' => [
        'label'   => 'What do you usually drink?',
        'type'    => 'radio',
        'options' => ['Coffee','Milk tea','Juice / smoothies','Soft drinks enjoyer','Water mostly','Beer / alcohol','Anything as long as it\'s cold'],
    ],
    'crowd_preference' => [
        'label'   => 'Crowd preference',
        'type'    => 'radio',
        'options' => ['Very quiet, few people','Small group is fine','Doesn\'t matter','The more the merrier'],
    ],
    'conversation_style' => [
        'label'   => 'Conversation style',
        'type'    => 'radio',
        'options' => ['Deep / meaningful talks','Funny and random','Getting to know each other slowly','Bahala na ang daloy'],
    ],
    'spontaneity_level' => [
        'label'   => 'Spontaneity level',
        'type'    => 'radio',
        'options' => ['Very spontaneous','A little structure please','Balanced','I need an itinerary'],
    ],
    'sleep_type' => [
        'label'   => 'Night owl or morning person?',
        'type'    => 'radio',
        'options' => ['Night owl — 12am+','Somewhere in between','Morning person','I sleep at random hours'],
    ],
    'phone_habits' => [
        'label'   => 'During a hangout, phone usage should be...',
        'type'    => 'radio',
        'options' => ['Barely touched','Checked occasionally','Doesn\'t really matter','We\'ll probably both check it'],
    ],
    'social_battery' => [
        'label'   => 'After a long day, your ideal hangout is...',
        'type'    => 'radio',
        'options' => ['Quiet place, just talking','Chill food trip','Watching something together','Going out somewhere lively'],
    ],
    'getting_to_know' => [
        'label'   => 'When meeting someone new, I usually...',
        'type'    => 'radio',
        'options' => ['Open up quickly','Take some time to warm up','Mostly listen first','Depends on the vibe'],
    ],
    'first_date_priority' => [
        'label'   => 'What matters most on a first date?',
        'type'    => 'radio',
        'options' => ['Good conversation','Good food','Good atmosphere','Just good company'],
    ],
];
?>
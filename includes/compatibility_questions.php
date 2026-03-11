<?php
$compatibility_questions = [

    // ── Checkboxes → now ranking (min 2, max 3) ──
    'music_genres' => [
        'label'   => 'Music genres you actually listen to',
        'type'    => 'rank',
        'max'     => 3,
        'min'     => 2,
        'options' => ['OPM','Pop','Indie','R&B','Hip-hop / Rap','Rock','Alternative','Jazz','Classical','Electronic / EDM','Metal','Country','K-pop'],
    ],
    'movie_genres' => [
        'label'   => 'Movie / series genres',
        'type'    => 'rank',
        'max'     => 3,
        'min'     => 2,
        'options' => ['Romance','Comedy','Horror','Thriller','Action','Sci-fi','Fantasy','Documentary','Anime','K-drama','Crime / Mystery','Animation'],
    ],
    'weekend_activities' => [
        'label'   => 'Ideal weekend activities — pick your top 3',
        'type'    => 'rank',
        'max'     => 3,
        'min'     => 2,
        'options' => ['Food trips','Coffee shop hopping','Night drives','Watching movies at home','Going to the mall','Nature / outdoors','Arcade / games','Art galleries','Concerts / events','Just staying in bed','Random drives','Bar / chill night out'],
    ],
    'humor_style' => [
        'label'   => 'Your humor style — pick your top 3',
        'type'    => 'rank',
        'max'     => 3,
        'min'     => 2,
        'options' => ['Dry humor','Dark humor','Stupid jokes','Sarcasm','Wholesome','Self-deprecating','Witty / clever','Physical comedy','Memes only'],
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
        'options' => ['Filipino food always','Anything as long as it\'s good','I like trying new stuff','I eat the same 5 things'],
    ],
    'coffee_preference' => [
        'label'   => 'Coffee or milk tea?',
        'type'    => 'radio',
        'options' => ['Coffee, always','Milk tea, always','Both honestly','Neither'],
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

    // ── New questions ──
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
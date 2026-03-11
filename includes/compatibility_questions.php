<?php
$compatibility_questions = [

    // ── Checkboxes ──
    'music_genres' => [
        'label'   => 'Music genres you actually listen to',
        'type'    => 'checkbox',
        'options' => ['OPM','Pop','Indie','R&B','Hip-hop / Rap','Rock','Alternative','Jazz','Classical','Electronic / EDM','Metal','Country','K-pop'],
    ],
    'movie_genres' => [
        'label'   => 'Movie / series genres',
        'type'    => 'checkbox',
        'options' => ['Romance','Comedy','Horror','Thriller','Action','Sci-fi','Fantasy','Documentary','Anime','K-drama','Crime / Mystery','Animation'],
    ],
    'weekend_activities' => [
        'label'   => 'Ideal weekend activities',
        'type'    => 'checkbox',
        'options' => ['Food trips','Coffee shop hopping','Night drives','Watching movies at home','Going to the mall','Nature / outdoors','Arcade / games','Art galleries','Concerts / events','Just staying in bed','Random drives','Bar / chill night out'],
    ],
    'humor_style' => [
        'label'   => 'Your humor style',
        'type'    => 'checkbox',
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
];
?>
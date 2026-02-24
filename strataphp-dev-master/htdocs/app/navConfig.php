<?php
// Navigation config for labels, order, and visibility
return [
    'Home' => [
        'label' => 'Home',
        'show' => true,
        'order' => 1,
        'new_tab' => false,
        'url' => '/',
    ],
    'About' => [
        'label' => 'About',
        'show' => true,
        'order' => 2,
        'new_tab' => false,
        'url' => '/about',
    ],
    'strata_home' => [
        'label' => 'StrataPHP.org',
        'show' => true,
        'order' => 2,
        'new_tab' => true,
        'url' => 'https://strataphp.org',
    ],
    'links' => [
        'label' => 'Links',
        'show' => true,
        'order' => 4,
        'new_tab' => false,
        'url' => '/links',
    ],
    'Contact' => [
        'label' => 'Contact Us',
        'url' => '/contact',
        'show' => true,
        'order' => 3,
        'new_tab' => false
    ]
    // ...other items
];

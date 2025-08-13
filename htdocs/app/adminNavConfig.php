<?php
// Navigation config for labels, order, and visibility
return [
    'Home' => [
        'label' => 'Home',
        'show' => true,
        'order' => 1,
        'url' => '/',
    ],
    'user_management' => [
        'label' => 'User Management',
        'show' => true,
        'order' => 2,
        'url' => '/admin/users/',
    ],
    'admin_links' => [
        'label' => 'Links',
        'show' => true,
        'order' => 3,
        'url' => '/admin/links',
    ],
    'modules' => [
        'label' => 'Modules',
        'show' => true,
        'order' => 4,
        'url' => '/admin/modules',
    ],
    'item2' => [
        'label' => 'Item 2',
        'url' => '/contact',
        'show' => true,
        'order' => 4
    ]
    // ...other items
];

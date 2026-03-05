<?php
// Navigation config for labels, order, and visibility
return [
    'Home' => [
        'label' => 'Home',
        'show' => true,
        'order' => 1,
        'url' => '/admin/dashboard',
    ],
    'user_management' => [
        'label' => 'User Management',
        'show' => true,
        'order' => 2,
        'url' => '/admin/users/',
    ],
    'modules' => [
        'label' => 'Modules',
        'show' => true,
        'order' => 4,
        'url' => '/admin/modules',
        'children' => [
            'manage' => [
                'label' => 'Manage Modules',
                'url' => '/admin/modules',
                'show' => true
            ],
            'installer' => [
                'label' => 'Install New Module',
                'url' => '/admin/module-installer',
                'show' => true
            ]
        ]
    ],
    'links' => [
        'label' => 'Links',
        'show' => true,
        'order' => 3,
        'url' => '/admin/links/',
    ],
    // CMS menu example
    'StrataCms' => [
        'label' => 'StrataCms',
        'show' => true,
        'order' => 5,
        'url' => '/admin/strata-cms',
        'children' => [
            'dashboard' => [
                'label' => 'CMS Dashboard',
                'url' => '/admin/strata-cms',
                'show' => true
            ],
            'pages' => [
                'label' => 'Manage Pages',
                'url' => '/admin/strata-cms/pages',
                'show' => true
            ],
            'create' => [
                'label' => 'Create Page',
                'url' => '/admin/strata-cms/pages/create',
                'show' => true
            ]
        ]
    ],
    'GoogleAnalytics' => [
        'label' => 'Analytics',
        'show' => true,
        'order' => 21,
        'url' => '/admin/google-analytics-settings'
    ]
    // ...other items
];

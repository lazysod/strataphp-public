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
    // CMS menu example
    // 'cms' => [
    //     'label' => 'CMS',
    //     'show' => true,
    //     'order' => 5,
    //     'url' => '/admin/cms',
    //     'children' => [
    //         'dashboard' => [
    //             'label' => 'CMS Dashboard',
    //             'url' => '/admin/cms',
    //             'show' => true
    //         ],
    //         'pages' => [
    //             'label' => 'Manage Pages',
    //             'url' => '/admin/cms/pages',
    //             'show' => true
    //         ],
    //         'create' => [
    //             'label' => 'Create Page',
    //             'url' => '/admin/cms/pages/create',
    //             'show' => true
    //         ]
    //     ]
    // ],
    'example_link' => [
        'label' => 'Example link',
        'url' => '#example',
        'show' => true,
        'order' => 4
    ],
    'blog' => [
        'label' => 'Blog',
        'icon' => 'fa-blog',
        'url' => '/admin/blog',
        'visible' => isset($modules['modules']['blog']['enabled']) ? $modules['modules']['blog']['enabled'] : false,
    ]
    // ...other items
];

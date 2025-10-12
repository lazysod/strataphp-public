<?php
// Module metadata for ModuleDirectory module
return [
    'name' => 'Module Directory',
    'slug' => 'module-directory',
    'version' => '1.0.0',
    'description' => 'Community module marketplace with submission, review, and approval system for StrataPHP modules.',
    'author' => 'StrataPHP Framework',
    'category' => 'Admin',
    'license' => 'MIT',
    'homepage' => 'https://github.com/strataphp/module-directory-module',
    'repository' => 'https://github.com/strataphp/module-directory-module.git',
    'support_url' => 'https://github.com/strataphp/module-directory-module/issues',
    'update_url' => '', // Optional: URL to check for updates
    'enabled' => false,
    'suitable_as_default' => false,
    'dependencies' => [
        'user' => '^1.0'
    ], // Requires user management system
    'permissions' => [
        'module-directory.view', 
        'module-directory.submit', 
        'module-directory.approve', 
        'module-directory.manage'
    ],
    'requirements' => [
        'php' => '>=7.4',
        'mysql' => '>=5.7'
    ],
    'tags' => ['marketplace', 'community', 'modules', 'submissions', 'directory'],
    'features' => [
        'Module submission system',
        'Author verification',
        'Automated validation',
        'Review and approval workflow',
        'Rating and reviews',
        'Download tracking',
        'Category management'
    ]
];
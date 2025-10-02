<?php
// Module metadata for Cms module
return [
    'name' => 'Cms',
    'slug' => 'cms',
    'version' => '1.0.0',
    'description' => 'A comprehensive cms management module with CRUD operations, search, and pagination.',
    'author' => 'StrataPHP Framework',
    'category' => 'Content',
    'license' => 'MIT',
    'homepage' => 'https://github.com/strataphp/cms-module',
    'repository' => 'https://github.com/strataphp/cms-module.git',
    'support_url' => 'https://github.com/strataphp/cms-module/issues',
    'update_url' => '', // Optional: URL to check for updates
    'enabled' => false,
    'suitable_as_default' => false,
    'dependencies' => [], // Other modules this depends on
    'permissions' => ['cms.create', 'cms.read', 'cms.update', 'cms.delete'], // Required permissions
    'requirements' => [
        'php' => '>=7.4',
        'mysql' => '>=5.7'
    ],
    'tags' => ['cms', 'content', 'cms', 'crud'],
    'screenshots' => [
        '/modules/cms/assets/screenshots/dashboard.png',
        '/modules/cms/assets/screenshots/editor.png'
    ]
];
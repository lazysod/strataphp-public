<?php
// Module metadata for GoogleAnalytics module
return [
    'name' => 'GoogleAnalytics',
    'slug' => 'google-analytics',
    'version' => '1.0.0',
    'description' => 'A comprehensive google_analytics management module with CRUD operations, search, and pagination.',
    'author' => 'StrataPHP Framework',
    'category' => 'Analytics',
    'license' => 'MIT',
    'homepage' => 'https://github.com/strataphp/google_analytics-module',
    'repository' => 'https://github.com/strataphp/google_analytics-module.git',
    'support_url' => 'https://github.com/strataphp/google_analytics-module/issues',
    'update_url' => '', // Optional: URL to check for updates
    'enabled' => false,
    'suitable_as_default' => false,
    'dependencies' => [], // Other modules this depends on
    'permissions' => ['google_analytics.create', 'google_analytics.read', 'google_analytics.update', 'google_analytics.delete'], // Required permissions
    'requirements' => [
        'php' => '>=7.4',
        'mysql' => '>=5.7'
    ],
    'tags' => ['google_analytics', 'content', 'cms', 'crud'],
    'screenshots' => [
        '/modules/google_analytics/assets/screenshots/dashboard.png',
        '/modules/google_analytics/assets/screenshots/editor.png'
    ]
];
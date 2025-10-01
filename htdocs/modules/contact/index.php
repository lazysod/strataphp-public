<?php
// Module metadata for Contact Form module
return [
    'name' => 'Contact Form',
    'slug' => 'contact-form',
    'version' => '1.0.0',
    'description' => 'A module for managing user contact requests.',
    'author' => 'StrataPHP Framework',
    'category' => 'Social',
    'license' => 'MIT',
    'framework_version' => '1.0.0',
    'repository' => 'https://github.com/lazysod/strataphp-public',
    'homepage' => 'https://github.com/lazysod/strataphp-public',
    'support_url' => 'https://github.com/lazysod/strataphp-public/issues',
    'structure_requirements' => [
        'controllers' => true,  // Needs controllers for form handling
        'views' => true,        // Needs views for form display
        'models' => true        // Needs models for contact data
    ],
    'update_url' => '', // Optional: URL to check for updates
    'enabled' => true
];
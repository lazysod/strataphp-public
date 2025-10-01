# StrataPHP Module Standards Specification

**Version:** 1.0.0  
**Date:** October 1, 2025  

## Module Structure Requirements

### Required Files

```
module-name/
├── index.php              # Module metadata (REQUIRED)
├── README.md             # Documentation (REQUIRED)
├── CHANGELOG.md          # Version history (RECOMMENDED)
├── LICENSE               # License file (RECOMMENDED)
├── routes.php            # Route definitions (OPTIONAL)
├── controllers/          # PSR-4 Controllers (OPTIONAL)
├── models/              # PSR-4 Models (OPTIONAL)
├── views/               # Template files (OPTIONAL)
├── assets/              # CSS/JS/Images (OPTIONAL)
│   ├── css/
│   ├── js/
│   └── images/
├── migrations/          # Database migrations (OPTIONAL)
├── seeds/               # Database seeds (OPTIONAL)
├── config/              # Module configuration (OPTIONAL)
├── tests/               # Unit tests (RECOMMENDED)
├── docs/                # Additional documentation (OPTIONAL)
└── install.php          # Installation script (OPTIONAL)
```

### Module Metadata Format (`index.php`)

```php
<?php
/**
 * StrataPHP Module Metadata
 * 
 * This file defines the module's metadata, dependencies, and configuration.
 * All fields marked as REQUIRED must be present for module validation.
 */
return [
    // REQUIRED FIELDS
    'name' => 'Module Display Name',
    'slug' => 'module-slug',
    'version' => '1.0.0',                    // Semantic versioning
    'description' => 'Brief module description',
    'author' => 'Developer Name',
    'category' => 'Content',                 // See categories below
    
    // RECOMMENDED FIELDS
    'license' => 'MIT',                      // MIT, GPL, Commercial, etc.
    'homepage' => 'https://example.com',
    'repository' => 'https://github.com/user/module',
    'support_url' => 'https://example.com/support',
    'documentation_url' => 'https://example.com/docs',
    'icon' => 'assets/images/icon.png',     // Module icon
    'screenshot' => 'assets/images/screenshot.png',
    
    // FRAMEWORK INTEGRATION
    'enabled' => false,                      // Default disabled
    'suitable_as_default' => false,         // Can be site homepage
    'admin_menu' => true,                   // Show in admin menu
    'public_routes' => true,                // Has public-facing routes
    
    // REQUIREMENTS & COMPATIBILITY
    'php_version' => '>=8.0',               // Minimum PHP version
    'strataphp_version' => '>=1.0.0',       // Minimum framework version
    'dependencies' => [                     // Other required modules
        'user' => '>=1.0.0',
        'messaging' => '>=2.0.0'
    ],
    'conflicts' => [                        // Incompatible modules
        'old-forum' => '*'
    ],
    
    // DATABASE REQUIREMENTS
    'requires_database' => true,            // Needs database tables
    'database_tables' => [                  // Tables this module creates
        'forum_posts',
        'forum_threads',
        'forum_categories'
    ],
    'migrations' => true,                   // Has migration files
    
    // PERMISSIONS & SECURITY
    'permissions' => [                      // Required permissions
        'forum.create_post',
        'forum.moderate',
        'forum.admin'
    ],
    'requires_ssl' => false,                // Needs HTTPS
    'csrf_protection' => true,              // Uses CSRF tokens
    
    // FEATURES & CAPABILITIES
    'features' => [                         // Module capabilities
        'Posts and Threads',
        'User Moderation',
        'File Attachments',
        'Real-time Notifications'
    ],
    'api_endpoints' => true,                // Provides API
    'theme_support' => true,                // Supports theming
    'widget_support' => false,              // Provides widgets
    'shortcode_support' => false,           // Provides shortcodes
    
    // PRICING & LICENSING (for marketplace)
    'pricing' => [
        'type' => 'free',                   // free, paid, freemium
        'price' => 0,                       // Price in USD
        'subscription' => false,            // Recurring billing
        'trial_days' => 0                   // Free trial period
    ],
    
    // UPDATE & MAINTENANCE
    'update_url' => '',                     // Update check URL
    'auto_update' => false,                 // Allow automatic updates
    'last_updated' => '2025-10-01',        // ISO date format
    'stability' => 'stable',               // alpha, beta, stable
    
    // DEVELOPER INFO
    'author_email' => 'dev@example.com',
    'author_website' => 'https://developer.com',
    'contributors' => [                     // Additional contributors
        'John Doe <john@example.com>',
        'Jane Smith <jane@example.com>'
    ],
    
    // TAGS & CLASSIFICATION
    'tags' => [                            // Search keywords
        'forum',
        'discussion',
        'community',
        'social'
    ],
    'keywords' => 'forum discussion community social',
    
    // INSTALLATION NOTES
    'installation_notes' => 'Requires manual configuration of email settings.',
    'post_install_message' => 'Visit /admin/forum to complete setup.',
    'uninstall_warning' => 'This will permanently delete all forum data.',
    
    // CUSTOM CONFIGURATION
    'config' => [                          // Module-specific settings
        'posts_per_page' => 20,
        'allow_attachments' => true,
        'max_attachment_size' => '10MB'
    ]
];
```

## Standard Categories

```php
const MODULE_CATEGORIES = [
    'Content'      => 'Content Management',
    'E-commerce'   => 'Online Store & Shopping',
    'Social'       => 'Social & Community',
    'Utility'      => 'Tools & Utilities',
    'Analytics'    => 'Analytics & Reporting',
    'Security'     => 'Security & Privacy',
    'SEO'          => 'Search Engine Optimization',
    'Media'        => 'Media & Files',
    'API'          => 'API & Integration',
    'Admin'        => 'Administration',
    'Development'  => 'Developer Tools',
    'Marketing'    => 'Marketing & Promotion'
];
```

## README.md Format

```markdown
# Module Name

Brief description of what the module does.

![Screenshot](assets/images/screenshot.png)

## Features

- Feature 1
- Feature 2
- Feature 3

## Installation

```bash
# Via StrataPHP CLI
php bin/install-module.php https://github.com/user/module

# Via Admin Interface
Upload the ZIP file through /admin/module-installer
```

## Configuration

Describe any configuration steps needed.

## Usage

### Basic Usage
Code examples and usage instructions.

### Advanced Features
More complex usage scenarios.

## API Reference

Document any API endpoints or hooks.

## Troubleshooting

Common issues and solutions.

## Contributing

Guidelines for contributors.

## License

License information.

## Support

Contact information and support channels.
```

## Validation Rules

### Required Validations
1. **File Structure**: Must have `index.php` and `README.md`
2. **Metadata**: All required fields must be present
3. **Version Format**: Must follow semantic versioning (x.y.z)
4. **Slug Format**: Lowercase, alphanumeric, hyphens only
5. **Dependencies**: Must reference existing modules
6. **PHP Compatibility**: Version requirements must be valid

### Security Validations
1. **No Dangerous Functions**: Scan for `eval()`, `exec()`, etc.
2. **SQL Injection Prevention**: Check for prepared statements
3. **XSS Prevention**: Validate output escaping
4. **File Upload Security**: Secure file handling
5. **CSRF Protection**: Proper token usage

### Quality Standards
1. **Code Style**: PSR-12 compliance
2. **Documentation**: Minimum 80% coverage
3. **Testing**: Unit tests recommended
4. **Performance**: No obvious bottlenecks
5. **Error Handling**: Proper exception management

## Migration Guide

For existing modules to meet new standards:

1. Update `index.php` with new metadata format
2. Add required README.md file
3. Follow PSR-4 autoloading structure
4. Add proper error handling
5. Implement security best practices

## Version History

- **1.0.0** (2025-10-01): Initial specification
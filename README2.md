# StrataPHP

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-8.2%2B-blue.svg)](https://php.net)

StrataPHP is a modular MVC framework for PHP 8.0+. PSR-native, explicit architecture with optional modules for CMS, admin, and auth.

Build APIs, dashboards, and CMS-driven sites without Laravel's magic. Take what you need. Delete what you don't.

## Table of Contents
- Installation
- Quick Start
- Features
- Structure
- Security
- User & Admin System
- Extending & Modules
- Links Management
- API Module Management
- Database Migrations & Seeding
- Optional Twig Template Engine Support
- CSRF Protection
- Session Management & Device Tracking
- Planned Features

1. Clone the repository:
```sh
- Release Notes
- Requirements
- License
```

## Installation


3. Configure environment:
```sh
1. Clone the repository:
git clone https://github.com/lazysod/strataphp-public.git your-app
Edit .env with your database, mail, and other settings.
cd your-app
composer install


4. Install database:
```sh
2. Point your web server to public_html as the document root.

This creates essential tables only. No demo data.
3. Configure environment:
cp .env.example .env
Edit .env with your database, mail, and other settings.

5. Run migrations:
```sh

4. Install database:
Migrations ensure your schema matches the latest features. Always run after updating.
php bin/install.php
This creates essential tables only. No demo data.

6. Create admin account:
```sh

5. Run migrations:
php bin/migrate.php

After v1.0.0 is on Packagist:
```sh
Migrations ensure your schema matches the latest features. Always run after updating.

6. Create admin account:
php bin/create_admin.php

7. Visit your site and log in at /admin.


Configuration:
```php
// public_html/app/config.php
'modules' => [
    'cms' => ['enabled' => true]  // Change to false to disable
];
```
After v1.0.0 is on Packagist: composer create-project lazysod/strataphp your-app

Prevent new signups while allowing existing logins:
```php
'registration_enabled' => false,
```
- "Schema file not found" -> Check mysql/db_instal.sql exists

Create production-ready modules with validation and security:
```sh
php bin/create-module.php invoices
```
## Quick Start

Generated structure:
```
invoices/
├── index.php          Module metadata — required
├── routes.php         Routes definition — required
├── README.md          Documentation — required
├── CHANGELOG.md       Change history — required
├── controllers/       Controller classes
├── models/            Model classes
├── views/             Template files
└── assets/            CSS, JS, images
```
- Modular architecture: Add/remove modules — user system, CMS, forum, API, etc.
- CMS Toggle System: Enable/disable CMS module without breaking site functionality
- CLI Module Generator: Create production-ready modules with php bin/create-module.php
- Advanced Module Management: Dual-view interface with validation, safe deletion, bulk operations
- Professional CMS: Content management with image uploads, SEO optimization, modern theming
- Graceful Fallbacks: Automatic fallback to default themes when CMS is disabled
- Smart Redirects: Context-aware redirects based on module availability and user roles
- Unified DB class: All database access uses the PDO-based DB class
- Independent admin system: Admin profile/login works even if user module is disabled
- User authentication: Registration, login, profile, password reset with token expiry
- Email integration: PHPMailer. Configure in .env or app/config.php
- CSRF protection: Automatic for all forms via TokenManager
- Session management: Device-based tracking, multi-device support, revokable sessions
- Logging: Security/auth events logged to storage/logs/ via Logger
- Dynamic navigation: Adapts to config and session state

```php
<?php
return [
    'name' => 'Invoices',
    'slug' => 'invoices',
    'version' => '1.0.0',
    'description' => 'Invoice management for StrataPHP',
    'author' => 'Your Name',
    'category' => 'Utility',
    'license' => 'MIT',
    'framework_version' => '1.0.0',
    'enabled' => true
];
```
- Links management: Linktree-style pages with drag & drop ordering
- Module validation: Security and quality checks for all modules

## Structure


```sh
php bin/migrate.php                # Apply new migrations
php bin/rollback.php 2             # Roll back last 2 migrations
php bin/migration_status.php       # Show status
php bin/test_migrations.php        # Validate + test rollback
php bin/create_migration.php AddUsersTable   # Scaffold migration
```
    public_html/
    ├── app/              Config, core classes, utilities

```sh
php bin/seed.php                   # Run all seeds in seeds/
php bin/seed.php --down            # Remove seeded data
```
    ├── controllers/      Controllers — one per route
    ├── models/           Data models
    ├── views/            View templates and partials
    ├── themes/           Theme folders — assets, custom views

To enable:
1. Install: `composer require twig/twig`
2. Set in `.env`: `USE_TWIG=true`
3. Create templates in `public_html/views` with `.twig` extension

Conditional rendering example:
```php
if ($config['use_twig']) {
    echo $twig->render('about.twig', $data);
} else {
    include 'views/about.php';
}
```
- Session: Started automatically in app/start.php. Device-tracked
- Password reset: Secure tokens with expiry

In forms:
```html
<input type="hidden" name="token" value="<?= TokenManager::csrf() ?>">
```

In controllers:
```php
$tm = new TokenManager();
$result = $tm->verify($_POST['token']);
if ($result['status'] === 'success') {
    // Valid
}
```
StrataPHP allows you to enable/disable the CMS module without breaking your site:

When CMS enabled 'enabled' => true:
- Modern CMS themes for auth pages
- Admin users redirect to /admin/cms
- Dynamic page routing and content management
- All CMS features: pages, media library, SEO tools

When CMS disabled 'enabled' => false:
- Graceful fallback to default StrataPHP themes
- Admin users redirect to /admin
- Standard framework routing
- Zero data loss — all CMS content preserved
- All core functionality maintained

Configuration:
// public_html/app/config.php
'modules' => [
    'cms' => ['enabled' => true]  // Change to false to disable
]

Benefits: Risk-free adoption, easy testing, professional degradation, instant revert.

### User Registration Toggle

Prevent new signups while allowing existing logins:

'registration_enabled' => false,

### Independence

- Admin login and profile work even if user module is disabled
- Navigation adapts to user state and config

## Extending & Modules

### Module Generator

Create production-ready modules with validation and security:

php bin/create-module.php invoices

Generated structure:

    invoices/
    ├── index.php          Module metadata — required
    ├── routes.php         Routes definition — required
    ├── README.md          Documentation — required
    ├── CHANGELOG.md       Change history — required
    ├── controllers/       Controller classes
    ├── models/            Model classes
    ├── views/             Template files
    └── assets/            CSS, JS, images

Features: CSRF protection, prepared statements, input validation, error handling, PHPDoc, framework integration.

### Module Metadata — index.php

```
<?php
return [
    'name' => 'Invoices',
    'slug' => 'invoices',
    'version' => '1.0.0',
    'description' => 'Invoice management for StrataPHP',
    'author' => 'Your Name',
    'category' => 'Utility',
    'license' => 'MIT',
    'framework_version' => '1.0.0',
    'enabled' => true
];
```

Valid categories: Content, E-commerce, Social, Utility, Analytics, Security, SEO, Media, API, Admin, Development, Marketing

### Module Validation

Visit /admin/modules to see validation status:

- Valid — Passes all checks. Ready for production
- Warnings — Works but has recommendations
- Invalid — Critical issues. Missing files or security vulnerabilities

Checks: Required files, no eval/exec, SQL injection prevention, error handling, PHPDoc comments.

### Module Management UI

/admin/modules provides:
- Table/Card view switching
- Enable/disable checkboxes
- Bulk operations
- Safe deletion with automatic backups
- Module details and installation status

### Development Guidelines
1. Database: Use DB class only. Never custom connections
2. Error handling: Wrap all controller methods in try-catch
3. Documentation: PHPDoc for all classes/methods
4. Security: TokenManager for CSRF. Validate all input
5. Session: Use PREFIX for session variables
6. Logging: Use Logger class for events

## Links Management

Linktree-style link pages with admin interface.

Admin: /admin/links
- CRUD operations
- Drag & drop ordering
- Icon auto-detection
- URL validation
- Preview mode

Public: /links
- Responsive design
- Fast loading
- Social media optimized

Properties: Title, URL, description, icon, order, status

Database: links table with id, title, url, description, icon, order, timestamps

## API Module Management

The API module can be enabled/disabled from /admin/modules like any other module.

- When disabled: All API endpoints inaccessible, routes not loaded
- When enabled: REST endpoints active

This improves security and flexibility.
## Database Migrations & Seeding

StrataPHP includes a robust migration and seeding system.

### Migrations

php bin/migrate.php                # Apply new migrations
php bin/rollback.php 2             # Roll back last 2 migrations
php bin/migration_status.php       # Show status
php bin/test_migrations.php        # Validate + test rollback
php bin/create_migration.php AddUsersTable   # Scaffold migration

Features:
- Forward/rollback migrations
- Migration locking — prevents concurrent runs
- Migration logging — tracks who/when
- Dual format support — array or .down.php files

### Seeding

php bin/seed.php                   # Run all seeds in seeds/
php bin/seed.php --down            # Remove seeded data

Best practices:
- Always create .down.php for each migration/seed
- Never run .down.php as forward migration
- Test with php bin/test_migrations.php before production

See bin/ and migrations/ folders for examples.

## Optional Twig Template Engine Support

Twig is optional. Classic PHP views used by default.

To enable:
1. Install: composer require twig/twig
2. Set in .env: USE_TWIG=true
3. Create templates in public_html/views with .twig extension

Conditional rendering example:

if ($config['use_twig']) {
    echo $twig->render('about.twig', $data);
} else {
    include 'views/about.php';
}

See public_html/controllers/AboutController.php for implementation.

## CSRF Protection

Built-in CSRF protection:

In forms:
<input type="hidden" name="token" value="<?= TokenManager::csrf() ?>">

In controllers:
$tm = new TokenManager();
$result = $tm->verify($_POST['token']);
if ($result['status'] === 'success') {
    // Valid
}

Enabled by default if 'csrf_token' => true in config.

## Session Management & Device Tracking

2025 Update: Modern, secure session system with device-based tracking.

### Key Changes
- Device-based tracking: Each login creates a session tied to device + IP
- Persistent login: "Remember Me" via secure cookies
- Session dashboard: Users/admins view/manage active sessions
- Unified table: All sessions in user_sessions
- IP logging: Each session records IP for auditing
- Legacy removed: Old tables no longer used

### Usage
- Users/admins manage sessions from dashboards
- Edit device names
- Revoke sessions remotely
- Only latest active session per device shown

See public_html/app/SessionManager.php and session dashboard controllers.

## Planned Features

- Forum module — modular, installable
- Install script for new modules
- Additional admin themes

## Release Notes

v1.0.0 — August 2025:
- Unified DB class used everywhere
- Admin and user systems fully independent
- TokenManager and Logger classes autoloaded
- Contact model and all modules updated to new DB
- Modular structure and config loading improved
- First stable release

For details, see code comments and explore public_html/.

## Requirements

- PHP 8.2.0+
- MySQL 5.7+ or MariaDB 10.2+
- Composer 2.0+

## License

MIT License. See LICENSE

---

**StrataPHP Framework** — Professional PHP development made simple.

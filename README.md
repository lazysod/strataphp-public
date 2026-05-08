StrataPHP
[License](LICENSE)
[PHP Version](https://php.net)

StrataPHP is a modular MVC framework for PHP 8.0+. PSR-native, explicit architecture with optional modules for CMS, admin, and auth.

Build APIs, dashboards, and CMS-driven sites without Laravel's magic. Take what you need. Delete what you don't.

Table of Contents
Installation
Quick Start
Features
Structure
Security
User & Admin System
Extending & Modules
Links Management
API Module Management
Database Migrations & Seeding
<h1 align="center">StrataPHP</h1>
├── modules/          # Modular features — user, cms, forum, etc.
├── storage/          # Logs, uploads, runtime files
├── vendor/           # Composer dependencies
└── bin/              # CLI tools — migrate.php, create-module.php, etc.
Security
CSRF tokens: Auto-generated and checked for all forms
Session: Started automatically in app/start.php. Device-tracked
Logging: Security/auth events logged
SQL injection: PDO prepared statements only
All core functionality maintained
// public_html/app/config.php
'modules' => [
'cms' => ['enabled' => true] // Change to false to disable
]

Benefits: Risk-free adoption, easy testing, professional degradation, instant revert.

User Registration Toggle
Prevent new signups while allowing existing logins:

'registration_enabled' => false,

Independence
Admin login and profile work even if user module is disabled
Navigation adapts to user state and config
Extending & Modules
Module Generator
Create production-ready modules with validation and security:

php bin/create-module.php invoices

Generated structure:

invoices/
├── index.php          # Module metadata — required
├── routes.php         # Routes definition — required
├── README.md          # Documentation — required
├── CHANGELOG.md       # Change history — required
├── controllers/       # Controller classes
├── models/            # Model classes
In controllers:
$tm = new TokenManager();
$result = $tm->verify($_POST['token']);
if ($result['status'] === 'success') {
// Valid
}
├── views/             # Template files
└── assets/            # CSS, JS, images

Features: CSRF protection, prepared statements, input validation, error handling, PHPDoc, framework integration.

---

## Module Metadata — index.php

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
**Valid categories:** Content, E-commerce, Social, Utility, Analytics, Security, SEO, Media, API, Admin, Development, Marketing

---

## Module Validation

Visit `/admin/modules` to see validation status:
- **Valid** — Passes all checks. Ready for production
- **Warnings** — Works but has recommendations
- **Invalid** — Critical issues. Missing files or security vulnerabilities

**Checks:** Required files, no eval/exec, SQL injection prevention, error handling, PHPDoc comments.

---

## Module Management UI

`/admin/modules` provides:
- Table/Card view switching
- Enable/disable checkboxes
- Bulk operations
- Safe deletion with automatic backups
- Module details and installation status

---

## Development Guidelines
1. **Database**: Use DB class only. Never custom connections
2. **Error handling**: Wrap all controller methods in try-catch
3. **Documentation**: PHPDoc for all classes/methods
4. **Security**: TokenManager for CSRF. Validate all input
5. **Session**: Use PREFIX for session variables
6. **Logging**: Use Logger class for events

---

## Links Management

Linktree-style link pages with admin interface.

**Admin:** `/admin/links`
- CRUD operations
- Drag & drop ordering
- Icon auto-detection
- URL validation
- Preview mode

**Public:** `/links`
- Responsive design
- Fast loading
- Social media optimized

**Properties:** Title, URL, description, icon, order, status

**Database:** `links` table with id, title, url, description, icon, order, timestamps

---

## API Module Management

The API module can be enabled/disabled from `/admin/modules` like any other module.
- **When disabled:** All API endpoints inaccessible, routes not loaded
- **When enabled:** REST endpoints active

This improves security and flexibility.

---

## Database Migrations & Seeding

StrataPHP includes a robust migration and seeding system.

### Migrations
```sh
php bin/migrate.php         # Apply new migrations
php bin/rollback.php 2      # Roll back last 2 migrations
php bin/migration_status.php # Show status
php bin/test_migrations.php # Validate + test rollback
php bin/create_migration.php AddUsersTable # Scaffold migration
```
**Features:**
- Forward/rollback migrations
- Migration locking — prevents concurrent runs
- Migration logging — tracks who/when
- Dual format support — array or .down.php files

### Seeding
```sh
php bin/seed.php         # Run all seeds in seeds/
php bin/seed.php --down  # Remove seeded data
```
**Best practices:**
- Always create .down.php for each migration/seed
- Never run .down.php as forward migration
- Test with `php bin/test_migrations.php` before production

See `bin/` and `migrations/` folders for examples.

---

## Optional Twig Template Engine Support

Twig is optional. Classic PHP views used by default.

**To enable:**
1. Install: `composer require twig/twig`
2. Set in `.env`: `USE_TWIG=true`
3. Create templates in `public_html/views` with `.twig` extension

**Conditional rendering example:**
```php
if ($config['use_twig']) {
	echo $twig->render('about.twig', $data);
} else {
	include 'views/about.php';
}
```
See `public_html/controllers/AboutController.php` for implementation.

---

## CSRF Protection

Built-in CSRF protection:

**In forms:**
```html
<input type="hidden" name="token" value="<?= TokenManager::csrf() ?>">
```
**In controllers:**
```php
$tm = new TokenManager();
$result = $tm->verify($_POST['token']);
if ($result['status'] === 'success') {
	// Valid
}
```
Enabled by default if `'csrf_token' => true` in config.

---

## Session Management & Device Tracking

**2025 Update:** Modern, secure session system with device-based tracking.

### Key Changes
- Device-based tracking: Each login creates a session tied to device + IP
- Persistent login: "Remember Me" via secure cookies
- Session dashboard: Users/admins view/manage active sessions
- Unified table: All sessions in `user_sessions`
- IP logging: Each session records IP for auditing
- Legacy removed: Old tables no longer used

### Usage
- Users/admins manage sessions from dashboards
- Edit device names
- Revoke sessions remotely
- Only latest active session per device shown

See `public_html/app/SessionManager.php` and session dashboard controllers.

---

## Planned Features
- Forum module — modular, installable
- Install script for new modules
- Additional admin themes

---

## Release Notes

**v1.0.0 — August 2025:**
- Unified DB class used everywhere
- Admin and user systems fully independent
- TokenManager and Logger classes autoloaded
- Contact model and all modules updated to new DB
- Modular structure and config loading improved
- First stable release

For details, see code comments and explore `public_html/`.

---

## Requirements
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.2+
- Composer 2.0+

---

## License
MIT License. See LICENSE.

---

StrataPHP Framework — Professional PHP development made simple.
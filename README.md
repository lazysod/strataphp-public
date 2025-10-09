# StrataPHP Framework

## Table of Contents
- [Quick Start](#quick-start)
- [Features](#features)
- [Structure](#structure)
- [Security](#security)
- [User & Admin System](#user--admin-system)
- [Extending & Modules](#extending--modules)
- [Links Management](#links-management)
- [API Module Management](#api-module-management)
- [Planned Features](#planned-features)
- [Release Notes](#release-notes)
- [Database Migrations & Seeding](#database-migrations--seeding)

---


## Installation

_How to set up StrataPHP for the first time (2025 update)._ 

1. Clone or download this repository.
2. Change directory to the project root.
3. Install Composer dependencies:
  ```sh
  cd htdocs
  composer install
  ```
4. Set up your web server to use `htdocs` as the document root.
5. Copy `.env.example` to `.env` and fill in your actual database, mail, and other settings:
  ```sh
  cp .env.example .env
  ```
  Edit `.env` with your real credentials.
6. Run the initial install script to import the database schema:
  ```sh
  php bin/install.php
  ```
  This will create only essential tables (no users/admins/demo data). Session management is now device-based and legacy tables are not included.
7. Run migrations to apply any new schema changes:
  ```sh
  php bin/migrate.php
  ```
  Migrations ensure your database schema is up to date with the latest features and changes. Always run migrations after installing or updating the framework.
8. Create your admin account:
  ```sh
  php bin/create_admin.php
  ```
9. Visit your site in the browser!

**2025 Session Management Update:**
- StrataPHP now uses device-based session tracking for users and admins. See `INSTALL.md` and `docs/db_cleanup_2025-09-12.md` for details.
- Legacy tables (ban_ip, cookie_login, error_log, ip_log, login_sessions, login_tracker) are not included in new installs. For upgrades, see the cleanup doc.

**Troubleshooting:**
- If you see "Schema file not found", check that `mysql/db_instal.sql` exists.
- If you get a database connection error, verify your `.env` or `app/config.php` settings.

---

## Quick Start
_Step-by-step instructions to get your project running after installation._

Once installed and the admin account is created, you can log in and begin configuring modules, users, and content from the admin panel.

---

## Features
_A summary of the framework's core capabilities._

- **Modular architecture**: Easily add or remove modules (user system, CMS, forum, etc.)
- **CMS Toggle System**: Enable/disable the CMS module without breaking site functionality
- **CLI Module Generator**: Create production-ready modules with `php bin/create-module.php`
- **Advanced Module Management**: Dual-view interface with validation, safe deletion, and bulk operations
- **Professional CMS**: Content management with image uploads, SEO optimization, and modern theming
- **Graceful Fallbacks**: Automatic fallback to default themes when CMS is disabled
- **Smart Redirects**: Context-aware user redirects based on module availability and user roles
- **Unified DB class**: All database access uses the PDO-based `DB` class (no legacy `dbcon`)
- **Admin & user systems are independent**: Admin profile and login work even if the user module is disabled
- **User authentication**: Registration, login, profile, password reset (with token expiry), all via modular, class-based controllers
- **Admin login & profile**: Separate admin authentication, dashboard, and profile page, with secure session and logging
- **Email integration**: Uses PHPMailer, configure mail settings in `app/config.php`
- **CSRF protection**: Automatic for all forms via the `TokenManager` class
- **Session management**: Robust, secure, auto-started in `app/start.php`
- **Logging**: Security/auth events logged to `storage/logs/` via the `Logger` class
- **Dynamic navigation**: Shows login/register/user menu based on config and session
- **Extensible**: Add new modules in `/htdocs/modules/` (see user module for example)
- **Links management**: Complete Linktree-style link management with drag & drop ordering
- **Module validation system**: Comprehensive security and quality checks for all modules
- **Module enable/disable UI**: Admin panel allows enabling/disabling modules and selecting the default module for the root page.

---

## Structure
_Overview of the project directory and file organization._

- `htdocs/` — All PHP code, configs, and assets live here
  - `app/` — Config, core classes, utilities
  - `controllers/` — Controllers (one per route)
  - `models/` — Data models
  - `views/` — View templates and partials
  - `themes/` — Theme folders (assets, custom views)
  - `storage/` — Logs, uploads, and other runtime files
  - `vendor/` — Composer dependencies (auto-generated)
- `_framework_old_backup_*` — Archived legacy code (safe to delete)

- `htdocs/modules/` — Modular features (user system, forum, etc.)

---

## Security
_Important security features and best practices._

- CSRF tokens are auto-generated and checked for all forms.
- Session is started automatically in `app/start.php`.

- Password reset uses secure tokens and expiry (see user module)
- Logging for security/auth events

---

## User & Admin System
_Managing users, admins, and authentication._

### CMS Toggle Feature

StrataPHP includes a revolutionary **CMS Toggle System** that allows you to enable/disable the CMS module without breaking your site:

#### **When CMS Module is Enabled** (`'enabled' => true`)
- Modern CMS themes for all user authentication pages (login, register, password reset)
- Admin users redirect to `/admin/cms` (professional CMS dashboard)
- Regular users redirect to `/user/profile` after login
- Dynamic page routing and rich content management interface
- All CMS features available (page management, image uploads, SEO tools)

#### **When CMS Module is Disabled** (`'enabled' => false`)  
- **Graceful fallback** to default StrataPHP themes
- Admin users redirect to `/admin` (basic admin panel)
- Regular users redirect to `/user/profile` after login
- Standard framework routing with no CMS dependencies
- **Zero data loss** - all CMS content safely preserved
- All core StrataPHP functionality maintained

#### **Toggle Configuration**
```php
// In htdocs/app/config.php
'modules' => [
    'cms' => [
        'enabled' => true,  // Change to false to disable CMS
        'suitable_as_default' => false,
    ],
]
```

#### **Benefits**
- ✅ **Risk-free adoption** - try CMS features without commitment
- ✅ **Developer confidence** - easy testing between modes  
- ✅ **Professional degradation** - site never breaks
- ✅ **Zero breaking changes** - instant revert capability

### Disable User Registration

To prevent new users from registering (while still allowing existing users to log in), set the following in your `htdocs/app/config.php`:

```php
'registration_enabled' => false,
```

When disabled, the registration page will show a message and block new signups.

- Enable or disable user/admin modules in `app/config.php` via the `modules` array
- User registration, login, profile, password reset, email test page (all modular)
- **Admin login and profile are fully independent**: Admins can log in and manage their profile even if the user module is disabled
- Navigation adapts to user state and config

---

## Extending & Modules
_How to add, enable, or disable modules, and create your own._

To extend the framework, you can create your own modules in the `htdocs/modules/` directory. Here are some guidelines to help you build robust, maintainable modules:

### Module Structure Requirements

Each module must have specific files and follow certain conventions to pass validation:

#### Required Files:
- **`index.php`** - Module metadata and configuration
- **`routes.php`** - Module routing definitions  
- **`README.md`** - Module documentation
- **`CHANGELOG.md`** - Version history and changes

#### Directory Structure:
```
your-module/
├── index.php          # Module metadata (required)
├── routes.php          # Routes definition (required) 
├── README.md           # Documentation (required)
├── CHANGELOG.md        # Change history (required)
├── controllers/        # Controller classes (if needed)
├── models/            # Model classes (if needed)
├── views/             # Template files (if needed)
└── assets/            # CSS, JS, images (optional)
```

### Module Generator

StrataPHP includes a powerful CLI module generator that creates production-ready modules with proper validation and security features:

```sh
php bin/create-module.php your-module-name
```

The generator creates:
- **Complete directory structure** with all required files
- **Validation-ready code** with proper error handling and security
- **Database models** with prepared statements and input validation
- **Controllers** with try-catch blocks and proper error logging
- **Template views** with consistent styling and structure
- **Routing configuration** with proper namespacing
- **Documentation** with usage examples and API reference

#### Generated Module Features:
- **Security First**: CSRF protection, prepared statements, input validation
- **Error Handling**: Comprehensive try-catch blocks and logging
- **Code Quality**: PHPDoc comments, consistent formatting, best practices
- **Framework Integration**: Uses StrataPHP DB class, routing, and conventions

### Module Management Interface

Access the module management interface at `/admin/modules` to:

**Dual-View Interface:**
- **Table View**: Compact list with enable/disable checkboxes
- **Card View**: Visual cards showing module details and metadata
- **JavaScript View Switching**: Seamless switching between display modes

**Management Features:**
- **Enable/Disable Modules**: Toggle module activation with real-time feedback
- **Module Validation**: Built-in quality and security checks
- **Safe Module Deletion**: Automatic backups before deletion
- **Module Details**: View comprehensive information about each module
- **Installation Status**: See which modules are installed vs. configured

**Bulk Operations:**
- **Select Multiple**: Use checkboxes to select multiple modules
- **Batch Enable/Disable**: Change status of multiple modules at once
- **Save Changes**: Apply all modifications with a single action

#### Directory Structure:
```
```

#### Module Metadata (`index.php`)
Your module's `index.php` must return an array with metadata:

```php
<?php
return [
    'name' => 'Your Module Name',
    'slug' => 'your-module',
    'version' => '1.0.0',
    'description' => 'Brief description of your module',
    'author' => 'Your Name',
    'category' => 'Utility', // See valid categories below
    'license' => 'MIT',
    'framework_version' => '1.0.0',
    'repository' => 'https://github.com/your-repo',
    'homepage' => 'https://your-homepage.com',
    'support_url' => 'https://github.com/your-repo/issues',
    'structure_requirements' => [
        'controllers' => true,  // Set to false if no controllers needed
        'views' => true,        // Set to false if no views needed  
        'models' => false       // Set to false if no models needed
    ],
    'enabled' => true
];
?>
```

#### Valid Categories:
- `Content`, `E-commerce`, `Social`, `Utility`, `Analytics`
- `Security`, `SEO`, `Media`, `API`, `Admin`, `Development`, `Marketing`

### Module Validation System

StrataPHP includes a comprehensive validation system that ensures modules meet quality and security standards:

#### Validation Checks:

**Structure Validation:**
- ✅ Required files exist (index.php, routes.php, README.md)
- ✅ Directory structure matches declared requirements
- ✅ Proper PSR-4 namespace compliance

**Security Validation:**
- ✅ No dangerous functions (eval, exec, system) except in admin modules
- ✅ SQL injection prevention (parameterized queries)
- ✅ XSS protection (proper output escaping)
- ✅ File access safety (path validation)

**Code Quality Validation:**
- ✅ Error handling (try-catch blocks in all methods)
- ✅ Documentation (PHPDoc comments for classes and methods)
- ✅ Convention compliance (naming, structure)
- ✅ Performance optimization (efficient queries, minimal dependencies)

#### Validation Levels:

**✅ Valid (Green)** - Module passes all critical checks
- All required files present
- No security vulnerabilities  
- Proper error handling and documentation
- Ready for production use

**⚠️ Warnings (Yellow)** - Module works but has recommendations
- Missing optional files (CHANGELOG.md)
- Non-standard categories
- Performance suggestions

**❌ Invalid (Red)** - Module has critical issues
- Missing required files or structure
- Security vulnerabilities
- Missing error handling or documentation

#### Using the Validation System:

1. **Admin Interface:**
   - Visit `/admin/modules` to see validation status
   - Click module names to view detailed validation results
   - Use "Validate" buttons to re-run checks

2. **Command Line:**
   ```bash
   php -r "
   require_once 'vendor/autoload.php';
   require_once 'htdocs/app/start.php';
   \$validator = new \App\Services\ModuleValidator();
   \$results = \$validator->validateModule('htdocs/modules/your-module');
   echo 'Valid: ' . (\$results['valid'] ? 'YES' : 'NO') . PHP_EOL;
   "
   ```

### Development Guidelines

1. **Directory Structure**
  - Each module should have its own folder under `htdocs/modules/yourmodule/`.
  - Organize your module with subfolders for `controllers/`, `models/`, `views/`, and `assets/` as needed.

2. **Autoloading**
  - Place your controllers in `controllers/`, models in `models/`, and views in `views/`.
  - The framework's autoloader will automatically load classes from these locations if you follow the naming conventions.

3. **Database Access**
  - Use the provided `DB` class for all database operations. Do not use legacy or custom DB connection code.
  - Example:
    ```php
    $db = new DB($config);
    $result = $db->fetch("SELECT * FROM mytable WHERE id = ?", [$id]);
    ```

4. **Error Handling**
  - Wrap all controller methods in try-catch blocks:
    ```php
    public function index() {
        try {
            // Your code here
        } catch (\Exception $e) {
            error_log("Error in YourController: " . $e->getMessage());
            // Handle error appropriately
        }
    }
    ```

5. **Documentation**
  - Add PHPDoc comments to all classes and methods:
    ```php
    /**
     * Your Controller Description
     * 
     * Handles functionality for your module
     */
    class YourController {
        /**
         * Display the main page
         * 
         * @return void
         */
        public function index() {
            // Implementation
        }
    }
    ```

6. **Configuration**
  - Add any module-specific config to `app/config.php` under the `'modules'` array or as a separate config key.
  - Access config via the global `$config` array.

7. **Security**
  - Use the `TokenManager` class for CSRF protection in all forms.
  - Validate and sanitize all user input.

8. **Session & Auth**
  - Use the session prefix (`PREFIX`) for any session variables to avoid conflicts.
  - Check authentication/authorization as needed for your module's routes.

9. **Views & Assets**
  - Place your module's views in the module's `views/` folder.
  - Store CSS, JS, and images in the module's `assets/` folder if needed.

10. **Logging**
  - Use the `Logger` class for logging important events or errors.

11. **Enable/Disable Modules**
  - Control module activation via the `'modules'` array in `app/config.php`.

12. **Documentation**
   - Document your module's usage, routes, and configuration in a `README.md` inside your module folder.

By following these guidelines, your modules will be consistent with the framework and easy for others to use or extend.

---

## Links Management
_Managing Linktree-style links through the admin interface._

StrataPHP includes a comprehensive links management system for creating Linktree-style pages:

### Admin Interface (`/admin/links`)

**Features:**
- **CRUD Operations**: Create, read, update, and delete links
- **Drag & Drop Ordering**: Reorder links with intuitive interface
- **Icon Auto-Detection**: Automatic icon assignment for popular platforms
- **URL Validation**: Ensures all links are properly formatted
- **Preview Mode**: See how links appear to visitors

**Link Properties:**
- **Title**: Display name for the link
- **URL**: Target destination (with validation)
- **Description**: Optional subtitle or description
- **Icon**: Auto-detected or custom icon selection
- **Order**: Display position (drag to reorder)
- **Status**: Enable/disable individual links

### Public Display (`/links`)

**User Experience:**
- **Responsive Design**: Works on all devices and screen sizes
- **Fast Loading**: Optimized for quick page loads
- **Analytics Ready**: Easy integration with tracking services
- **Social Media Optimized**: Proper meta tags and sharing support

**Customization:**
- **Themes**: Customize appearance through CSS
- **Branding**: Add logos and custom styling
- **Adult Content Warning**: Optional age gate for sensitive content

### Database Schema

The links system uses the `links` table with these fields:
- `id` - Primary key
- `title` - Link display text
- `url` - Target URL
- `description` - Optional subtitle
- `icon` - Icon identifier
- `order` - Display order
- `created_at` - Creation timestamp
- `updated_at` - Last modification

### Integration

Links can be integrated into other modules or used as:
- **Homepage**: Set links as the default module
- **Navigation**: Include in site menus
- **Sidebar**: Display in widget areas
- **API Endpoints**: Access via REST API (when API module enabled)

---

## API Module Management
_Enabling, disabling, and managing the API module from the admin interface._

- The API module can now be enabled or disabled from the admin interface, just like other modules.
- When disabled, all API endpoints are inaccessible and their routes are not loaded.
- Module route loading is now strictly controlled by the config, ensuring only enabled modules are accessible.

This improves security and flexibility for managing your API and other modules.

---

## Planned Features
_Upcoming improvements and modules._

- Forum module (modular, installable)
- Install script for new modules

---

## Release Notes
_Recent changes and version history._

- v1.0.0 (August 2025):
  - Unified DB class (`DB`) used everywhere (no more `dbcon`)
  - Admin and user systems are fully independent
  - TokenManager and Logger classes autoloaded and used for CSRF and logging
  - Contact model and all modules updated to use new DB class
  - Modular structure and config loading improved
  - First stable release

For more details, see the code comments and explore the `htdocs/` directory.

---

## Database Migrations & Seeding
_How to manage your database schema and seed data._

The Strata Framework includes a robust migration and seeding system for managing your database schema and test/demo data.

### Migration Features
- **Forward migrations**: Apply all new migrations in order with `php bin/migrate.php`.
- **Rollback**: Undo the latest (or multiple) migrations with `php bin/rollback.php [steps]`.
- **Migration status**: See which migrations are applied or pending with `php bin/migration_status.php`.
- **Migration testing**: Validate all migrations and test rollback capability with `php bin/test_migrations.php`.
- **Migration locking**: Prevents concurrent migration runs; shows who/when set the lock.
- **Migration logging**: Tracks who ran each migration and when (`applied_by`, `applied_at`).
- **Migration generator**: Create new migration and rollback templates with `php bin/create_migration.php MigrationName`.
- **Down migrations**: Each migration can have a `.down.php` file for rollback support.
- **Dual format support**: Supports both array format and separate `.down.php` files with automatic detection.

### Seeding Features
- **Seeding**: Populate your database with test/demo data using `php bin/seed.php` (runs all seeds in `seeds/`).
- **De-seeding**: Remove seeded data with `php bin/seed.php --down` (runs all `.down.php` seed files in reverse order).
- **Seed generator**: Create your own seed and down seed files in the `seeds/` directory.

### Example Usage
```sh
php bin/migrate.php                # Apply all new migrations
php bin/rollback.php 2             # Roll back the last 2 migrations
php bin/migration_status.php       # Show migration status
php bin/test_migrations.php        # Test all migrations and rollback capability
php bin/create_migration.php AddUsersTable   # Scaffold new migration and down file
php bin/seed.php                   # Run all seed files
php bin/seed.php --down            # Remove all seeded data
```

### Best Practices
- Always create a `.down.php` file for each migration/seed to support rollback.
- Never run `.down.php` files as forward migrations.
- Use the migration lock to avoid concurrent schema changes in teams/CI.
- Test migrations with `php bin/test_migrations.php` before deploying to production.
- Both array format and separate `.down.php` files are supported for maximum flexibility.

See the `bin/` and `migrations/` folders for more details and examples. For comprehensive migration documentation, see `docs/MIGRATION_SYSTEM_GUIDE.md`.

---

## Optional Twig Template Engine Support

This framework supports Twig as an optional template engine. By default, classic PHP views are used. If you want to use Twig:

1. Install Twig via Composer:
   ```bash
   composer require twig/twig
   ```
2. Set `use_twig` to `true` in `htdocs/app/config.php` or your `.env` file:
   ```env
   USE_TWIG=true
   ```
3. Create your templates in the `htdocs/views` directory with the `.twig` extension (e.g., `about.twig`).
4. Use Twig syntax in your templates. You can use template inheritance and includes for headers, footers, etc.

If `use_twig` is set to `false`, the framework will use classic PHP views instead.

## Example: Conditional Twig/PHP Rendering in Controllers

The `AboutController` demonstrates how to conditionally render either a Twig template or a classic PHP view based on the configuration setting (`use_twig`).

- If Twig is enabled in `htdocs/app/config.php` or your `.env` file, the controller will render `about.twig`.
- If Twig is disabled, it will render `about.php` using standard PHP includes.

This setup allows you to keep Twig as an optional feature and provides a clear example for other controllers.

See `htdocs/controllers/AboutController.php` for implementation details.

---

## CSRF Protection

This framework includes built-in CSRF protection:

- A unique CSRF token is generated for each user session.
- To include the token in your forms, use:
  ```php
  <input type="hidden" name="token" value="<?= TokenManager::csrf() ?>">
  ```
- To verify the token on form submission in your controller:
  ```php
  $tm = new TokenManager();
  $result = $tm->verify($_POST['token']);
  if ($result['status'] === 'success') {
      // Token is valid, process the form
  } else {
      // Token is invalid or expired
  }
  ```

CSRF protection is enabled by default if `'csrf_token' => true` is set in your config.

---

# Session Management & Device Tracking (2025 Update)

## Overview
StrataPHP now uses a modern, secure session management system with device-based tracking for both users and admins. Legacy session tables have been removed and all session logic is unified under the `user_sessions` table.

### Key Changes
- **Device-based session tracking**: Each login creates a session tied to a device, with device name and IP address logged.
- **Persistent login**: "Remember Me" functionality via secure cookies, with session restoration after browser close.
- **Session dashboard**: Users and admins can view and manage active sessions/devices, edit device names, and revoke sessions.
- **Unified session table**: All sessions (user and admin) are stored in `user_sessions`.
- **IP address logging**: Each session records the IP address for auditing and security.
- **Legacy tables removed**: Old tables (`ban_ip`, `cookie_login`, `error_log`, `ip_log`, `login_sessions`, `login_tracker`) are no longer used.

## Migration & Install
- New installs use the trimmed `db_instal.sql` (no legacy tables).
- Existing installs should drop unused tables (see `docs/db_cleanup_2025-09-12.md`).
- Migration `009_add_ip_address_to_user_sessions.php` adds IP logging to sessions.

## Usage
- Users and admins can manage their sessions/devices from their dashboards.
- Device name can be edited for the current session.
- Only the latest active session per device is shown.

## References
- See `docs/db_cleanup_2025-09-12.md` for details on database cleanup.
- See migration files for schema changes.

---
For more, see the code in `htdocs/app/SessionManager.php`, `User.php`, and session dashboard controllers/views.

---

## 📚 Documentation

### **Complete Guides**
- **[Migration System Guide](docs/MIGRATION_SYSTEM_GUIDE.md)** - Complete migration and rollback documentation
- **[Theme System Guide](docs/THEME_SYSTEM_GUIDE.md)** - Framework and CMS theme architecture
- **[Template Customization](docs/TEMPLATE_CUSTOMIZATION.md)** - Quick start for customizing themes
- **[Module Development](MODULE_SYSTEM.md)** - Creating and managing modules
- **[Module Standards](MODULE_STANDARDS.md)** - Module development best practices

### **Setup & Installation**
- **[Installation Guide](INSTALL.md)** - Database setup and configuration
- **[CMS Toggle System](CHANGELOG_CMS_TOGGLE.md)** - CMS enable/disable functionality
- **[Database Cleanup](docs/db_cleanup_2025-09-12.md)** - Legacy table removal guide

### **Framework Reference**
- **[Changelog](CHANGELOG.md)** - Version history and feature updates
- **[Migration Status](bin/migration_status.php)** - Check applied migrations
- **[Test Migrations](bin/test_migrations.php)** - Validate migration system

**StrataPHP Framework** - Professional PHP development made simple. 🚀

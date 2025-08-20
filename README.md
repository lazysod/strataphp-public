# Documentation Index

  - Quick Start
  - Features
  - Structure
  - Security
  - User & Admin System
  - Extending & Modules
  - Creating Your Own Modules (guidelines)
  - AJAX Endpoints
  - Planned Features
  - Release Notes

  - Directory structure
  - Example controller, model, view, and asset
  - Usage notes

  - Template for user configuration (not a doc, but referenced in README)

# Documentation Index

- [Project README (Main)](README.md)
  - Quick Start
  - Features
  - Structure
  - Security
  - User & Admin System
  - Extending & Modules
  - Creating Your Own Modules (guidelines)
  - AJAX Endpoints
  - Planned Features
  - Release Notes

- [Example Module: HelloWorld](htdocs/modules/helloworld/README.md)
  - Directory structure
  - Example controller, model, view, and asset
  - Usage notes

- [config-example.php](htdocs/app/config-example.php)
  - Template for user configuration (not a doc, but referenced in README)

## Creating Your Own Modules

To extend the framework, you can create your own modules in the `htdocs/modules/` directory. Here are some guidelines to help you build robust, maintainable modules:

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

4. **Configuration**
  - Add any module-specific config to `app/config.php` under the `'modules'` array or as a separate config key.
  - Access config via the global `$config` array.

5. **Security**
  - Use the `TokenManager` class for CSRF protection in all forms.
  - Validate and sanitize all user input.

6. **Session & Auth**
  - Use the session prefix (`PREFIX`) for any session variables to avoid conflicts.
  - Check authentication/authorization as needed for your module's routes.

7. **Views & Assets**
  - Place your module's views in the module's `views/` folder.
  - Store CSS, JS, and images in the module's `assets/` folder if needed.

8. **Logging**
  - Use the `Logger` class for logging important events or errors.

9. **Enable/Disable Modules**
  - Control module activation via the `'modules'` array in `app/config.php`.

10. **Documentation**
   - Document your module's usage, routes, and configuration in a `README.md` inside your module folder.

By following these guidelines, your modules will be consistent with the framework and easy for others to use or extend.
## AJAX Endpoints

You can add AJAX handler scripts in `htdocs/ajax/` (e.g., `/ajax/ping.php`).

- Use `header('Content-Type: application/json');` for JSON responses.
- Always check authentication and CSRF tokens for sensitive actions.
- Organize by feature if needed (e.g., `ajax/user/`, `ajax/admin/`).
- Example endpoint: `htdocs/ajax/ping.php` (returns `{ status: 'success', message: 'pong' }` on POST).


# Strata Framework

## Version 1.0.0 (August 2025)

This is the first full release of the Strata Framework, featuring a modular architecture, unified database access, robust security, and clear separation between admin and user systems.

## Quick Start

1. Clone or download this repository.
2. Change directory to the project root.
3. Install Composer dependencies:
   ```sh
   cd htdocs
   composer install
   ```
4. Set up your web server to use `htdocs` as the document root.
5. Copy or edit `htdocs/app/config.php` for your environment (database, mail, theme, etc).
6. Visit your site in the browser!

---
# Database Installation Guide

## Quick Install

1. **Create a new MySQL database**  
  Example: `1f_test`

2. **Import the schema**
  - Use phpMyAdmin, MySQL Workbench, or the command line:
    ```sh
    mysql -u username -p database_name < mysql/db_instal.sql
    ```
  - This will create all required tables with minimal data (only rank, no users/admins).

3. **Run migrations (optional but recommended)**
  - To apply any new schema changes or updates:
    ```sh
    php bin/migrate.php
    ```

4. **Create your first admin user**
  - Use the CLI tool:
    ```sh
    php bin/create_admin.php
    ```
  - Follow prompts or use command-line arguments for automation.

## What’s Included

- Only essential tables for a fresh install
- No user, admin, or demo data—users start with a clean slate
- Ready for migrations and admin creation

## Next Steps

- Log in as admin and configure your site
- Add modules, users, and content as needed

## Features

 **Modular architecture**: Easily add or remove modules (user system, forum, etc.)
 **Unified DB class**: All database access uses the PDO-based `DB` class (no legacy `dbcon`)
 **Admin & user systems are independent**: Admin profile and login work even if the user module is disabled
 **User authentication**: Registration, login, profile, password reset (with token expiry), all via modular, class-based controllers
 **Admin login & profile**: Separate admin authentication, dashboard, and profile page, with secure session and logging
 **Email integration**: Uses PHPMailer, configure mail settings in `app/config.php`
 **CSRF protection**: Automatic for all forms via the `TokenManager` class
 **Session management**: Robust, secure, auto-started in `app/start.php`
 **Logging**: Security/auth events logged to `storage/logs/` via the `Logger` class
 **Dynamic navigation**: Shows login/register/user menu based on config and session
 **Extensible**: Add new modules in `/htdocs/modules/` (see user module for example)
 **Admin links management**: Add, edit, delete, and reorder links in the admin panel, with FontAwesome icon auto-detection and NSFW marking.
 **NSFW support for links**: Mark links as NSFW in the admin panel; public users must confirm before visiting NSFW links.
 **Module enable/disable UI**: Admin panel allows enabling/disabling modules and selecting the default module for the root page.
+ **Modular architecture**: Easily add or remove modules (user system, forum, etc.)
+ **Unified DB class**: All database access uses the PDO-based `DB` class (no legacy `dbcon`)
+ **Admin & user systems are independent**: Admin profile and login work even if the user module is disabled
+ **User authentication**: Registration, login, profile, password reset (with token expiry), all via modular, class-based controllers
+ **Admin login & profile**: Separate admin authentication, dashboard, and profile page, with secure session and logging
+ **Email integration**: Uses PHPMailer, configure mail settings in `app/config.php`
+ **CSRF protection**: Automatic for all forms via the `TokenManager` class
+ **Session management**: Robust, secure, auto-started in `app/start.php`
+ **Logging**: Security/auth events logged to `storage/logs/` via the `Logger` class
+ **Dynamic navigation**: Shows login/register/user menu based on config and session
+ **Extensible**: Add new modules in `/htdocs/modules/` (see user module for example)
+ **Admin links management**: Add, edit, delete, and reorder links in the admin panel, with FontAwesome icon auto-detection and NSFW marking.
+ **NSFW support for links**: Mark links as NSFW in the admin panel; public users must confirm before visiting NSFW links.
+ **Module enable/disable UI**: Admin panel allows enabling/disabling modules and selecting the default module for the root page.

## Planned

- Forum module (modular, installable)
- Install script for new modules

## Structure

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

## Composer

- All dependencies are managed with Composer. After cloning, run `composer install` in `htdocs/`.
- Do not commit the `vendor/` folder; it is auto-generated.

## Theming

- Set your theme in `app/config.php` (`theme` and `theme_path`).
- Place CSS, JS, and images in `themes/[theme]/assets/`.
- Reference assets in views using:
  ```php
  <link rel="stylesheet" href="<?php echo App::config('theme_path'); ?>/assets/style.css">
  ```

## Security

- CSRF tokens are auto-generated and checked for all forms.
- Session is started automatically in `app/start.php`.

- Password reset uses secure tokens and expiry (see user module)
- Logging for security/auth events


## User & Admin System

- Enable or disable user/admin modules in `app/config.php` via the `modules` array
- User registration, login, profile, password reset, email test page (all modular)
- **Admin login and profile are fully independent**: Admins can log in and manage their profile even if the user module is disabled
- Navigation adapts to user state and config

## Email

- Configure mail settings in `app/config.php`
- Uses PHPMailer for robust email delivery


## Extending & Modules

- Add new modules in `htdocs/modules/` (see user system for example)
- All modules should use the unified `DB` class for database access
- Planned: forum module, install script for modules

## Customization

- Add controllers for new routes.
- Add models for new data types.
- Add or override views and partials as needed.

- Add modules for new features (user, forum, etc.)

## Updating

- To update Composer packages, run `composer update` in `htdocs/`.
- To add a new package, run `composer require vendor/package` in `htdocs/`.

---

## 404 Handling

Unmatched routes display a styled, user-friendly 404 page (`/views/system/404.php`).

---

## Important Changes (v1.0.0+)

### Database Class
- The only supported DB class is `DB.php` in `htdocs/app/class/`. Remove any legacy or duplicate DB class files.
- All modules and scripts must use this class for database access.

### macOS System Files
- `.DS_Store` files are ignored by default via `.gitignore`. You can safely delete any that appear in your project folders.

### Creating the First Admin User (CLI)
- After setup, run the CLI script to create your first admin user:
  ```sh
  php bin/create_admin.php
  ```
- The script supports both interactive prompts and command-line arguments:
  ```sh
  php bin/create_admin.php --first="Jane" --second="Doe" --email="jane@example.com" --password="secret" --display="Jane"
  ```
- This will insert the first admin user into your database securely.

### Database Connection (macOS)
- If you get a `No such file or directory` error, set your DB host to `127.0.0.1` (not `localhost`) in `app/config.php`.

### Navigation
- Main site navigation is configured in `app/navConfig.php`.
- Admin navigation is configured in `app/adminNavConfig.php`.
- All navigation links should use absolute paths (e.g., `/admin/users/`).

### Obsolete Files
- Remove `.DS_Store`, `TRASH/`, and `Temp Files/` folders if not needed.
- There is no `Db.php` or `db.php` in the framework; only use `DB.php`.

---

## Release Notes

- v1.0.0 (August 2025):
  - Unified DB class (`DB`) used everywhere (no more `dbcon`)
  - Admin and user systems are fully independent
  - TokenManager and Logger classes autoloaded and used for CSRF and logging
  - Contact model and all modules updated to use new DB class
  - Modular structure and config loading improved
  - First stable release

For more details, see the code comments and explore the `htdocs/` directory.

## Database Migrations & Seeding
## NSFW Links & Admin Links Management

### NSFW Links
- Mark any link as NSFW in the admin panel (checkbox in add/edit forms).
- NSFW links show a badge in admin and public views.
- Public users must confirm before visiting NSFW links (JS confirmation dialog).

### Admin Links Management
- Add, edit, delete, and reorder links from the admin panel.
- FontAwesome icon auto-detection for popular domains.
- NSFW marking and badge support.

### Module Enable/Disable
- Enable or disable modules from the admin panel UI.
- Select the default module for the root page.


The Strata Framework includes a robust migration and seeding system for managing your database schema and test/demo data.

### Migration Features
- **Forward migrations**: Apply all new migrations in order with `php bin/migrate.php`.
- **Rollback**: Undo the latest (or multiple) migrations with `php bin/rollback.php [steps]`.
- **Migration status**: See which migrations are applied or pending with `php bin/migration_status.php`.
- **Migration locking**: Prevents concurrent migration runs; shows who/when set the lock.
- **Migration logging**: Tracks who ran each migration and when (`applied_by`, `applied_at`).
- **Migration generator**: Create new migration and rollback templates with `php bin/create_migration.php MigrationName`.
- **Down migrations**: Each migration can have a `.down.php` file for rollback support.

### Seeding Features
- **Seeding**: Populate your database with test/demo data using `php bin/seed.php` (runs all seeds in `seeds/`).
- **De-seeding**: Remove seeded data with `php bin/seed.php --down` (runs all `.down.php` seed files in reverse order).
- **Seed generator**: Create your own seed and down seed files in the `seeds/` directory.

### Example Usage
```sh
php bin/migrate.php                # Apply all new migrations
php bin/rollback.php 2             # Roll back the last 2 migrations
php bin/migration_status.php       # Show migration status
php bin/create_migration.php AddUsersTable   # Scaffold new migration and down file
php bin/seed.php                   # Run all seed files
php bin/seed.php --down            # Remove all seeded data
```

### Best Practices
- Always create a `.down.php` file for each migration/seed to support rollback.
- Never run `.down.php` files as forward migrations.
- Use the migration lock to avoid concurrent schema changes in teams/CI.

See the `bin/` and `migrations/` folders for more details and examples.
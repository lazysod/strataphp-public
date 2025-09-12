# Strata Framework: Database Installation Guide

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

# Session Management & Device Tracking (2025 Update)

StrataPHP now uses a unified, device-based session management system for both users and admins. All sessions are tracked in the `user_sessions` table, with device name and IP address logging. Legacy session tables have been removed for database hygiene.

## Key Features
- Device-based session tracking for users and admins
- Persistent login with secure cookies
- Session restoration after browser close
- Session dashboard for users/admins to manage devices
- Device name editing for current session
- IP address logging for each session
- Only latest active session per device is shown
- Legacy tables removed: `ban_ip`, `cookie_login`, `error_log`, `ip_log`, `login_sessions`, `login_tracker`

## Migration & Install
- New installs use the trimmed `db_instal.sql` (no legacy tables)
- Existing installs should drop unused tables (see `docs/db_cleanup_2025-09-12.md`)
- Migration `009_add_ip_address_to_user_sessions.php` adds IP logging to sessions

## References
- See `docs/db_cleanup_2025-09-12.md` for database cleanup details
- See migration files for schema changes

---
For more, see the code in `htdocs/app/SessionManager.php`, `User.php`, and session dashboard controllers/views.

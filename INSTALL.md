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

---

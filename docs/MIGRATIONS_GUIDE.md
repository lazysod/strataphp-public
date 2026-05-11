# Migrations Guide

Database migrations in StrataPHP help you manage schema changes safely and consistently. Use migrations to add, modify, or remove tables and columns as your application evolves.

## What Are Migrations?
Migrations are PHP scripts that describe changes to your database schema. Each migration is versioned and can be applied or rolled back using CLI tools.

## When to Use Migrations
- Adding new tables or columns
- Modifying existing schema
- Removing obsolete tables
- Deploying updates to production

## When Not to Use Migrations
- For demo/test data (use seeds instead)
- For manual, one-off changes (prefer migration scripts for repeatability)

## Using Migrations
1. **Create a migration:**
   ```sh
   php bin/create_migration.php AddUsersTable
   ```
   This generates a new migration file in `migrations/`.

2. **Apply migrations:**
   ```sh
   php bin/migrate.php
   ```
   Applies all pending migrations to your database.

3. **Rollback migrations:**
   ```sh
   php bin/rollback.php
   ```
   Rolls back the last applied migration.

4. **Check migration status:**
   ```sh
   php bin/migration_status.php
   ```
   Shows which migrations have been applied.

## Best Practices
- Write clear, descriptive migration names
- Test migrations on a development database first
- Use down scripts for safe rollbacks
- Keep migrations atomic (one change per migration)

## Seeding Data
- Use `php bin/seed.php` to populate demo or test data
- Seed scripts are stored in `seeds/`

For advanced migration scenarios, see the full framework documentation.

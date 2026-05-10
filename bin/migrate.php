#!/usr/bin/env php
<?php
// Simple migration runner for Strata Framework
require_once __DIR__ . '/../public_html/app/config.php';
require_once __DIR__ . '/../public_html/app/DB.php';
use App\DB;

$config = $config ?? require __DIR__ . '/../public_html/app/config.php';
$db = new DB($config);

$migrationsDir = __DIR__ . '/../migrations/';
$migrationFiles = glob($migrationsDir . '*.php');
sort($migrationFiles);

// Ensure migrations table exists
$init = include $migrationsDir . '001_create_migrations_table.php';
$init($db);
// Ensure migration_lock table exists
if (file_exists($migrationsDir . '005_create_migration_lock_table.php')) {
    $lockInit = include $migrationsDir . '005_create_migration_lock_table.php';
    $lockInit($db);
}
// Check for lock
$lock = $db->fetchAll('SELECT locked, locked_by, locked_at FROM migration_lock WHERE id = 1');
if ($lock && $lock[0]['locked']) {
    echo "Migrations are locked by {$lock[0]['locked_by']} at {$lock[0]['locked_at']}.\n";
    exit(1);
}
// Set lock
$locked_by = get_current_user() . '@' . gethostname();
$db->query('UPDATE migration_lock SET locked = 1, locked_at = NOW(), locked_by = ? WHERE id = 1', [$locked_by]);

// Get applied migrations
$rows = $db->fetchAll('SELECT migration FROM migrations');
$applied = array_column($rows, 'migration');

if ($argc >= 3 && $argv[1] === 'down') {
    $target = $argv[2];
    $file = $migrationsDir . $target;
    if (!file_exists($file)) {
        echo "Migration file not found: $target\n";
        exit(1);
    }
    // Check if migration is applied
    $isApplied = in_array($target, $applied);
    if (!$isApplied) {
        echo "Migration not applied: $target\n";
        exit(1);
    }
    echo "Rolling back: $target... ";
    $migration = include $file;
    if (is_array($migration) && isset($migration['down']) && is_callable($migration['down'])) {
        $migration['down']($db);
    } else {
        echo "No valid down() method for $target\n";
        exit(1);
    }
    $db->query('DELETE FROM migrations WHERE migration = ?', [$target]);
    echo "done.\n";
    // Clear lock and exit
    $db->query('UPDATE migration_lock SET locked = 0, locked_at = NULL, locked_by = NULL WHERE id = 1');
    exit(0);
}

if ($argc >= 2 && ($argv[1] === 'down' || $argv[1] === 'up')) {
    $direction = $argv[1];
    $target = $argc >= 3 ? $argv[2] : null;
    $force = in_array('--force', $argv, true);
    if ($direction === 'down') {
        if ($target) {
            $file = $migrationsDir . $target;
            if (!file_exists($file)) {
                echo "Migration file not found: $target\n";
                exit(1);
            }
            $isApplied = in_array($target, $applied);
            if (!$isApplied) {
                echo "Migration not applied: $target\n";
                exit(1);
            }
            $migration = include $file;
            if (is_array($migration) && !empty($migration['core']) && !$force) {
                echo "Refusing to roll back core migration: $target. Use --force to override.\n";
                exit(1);
            }
            echo "Rolling back: $target... ";
            if (is_array($migration) && isset($migration['down']) && is_callable($migration['down'])) {
                $migration['down']($db);
            } else {
                echo "No valid down() method for $target\n";
                exit(1);
            }
            $db->query('DELETE FROM migrations WHERE migration = ?', [$target]);
            echo "done.\n";
        } else {
            // Rollback all applied migrations in reverse order
            $appliedMigrations = array_reverse($applied);
            foreach ($appliedMigrations as $migrationName) {
                $file = $migrationsDir . $migrationName;
                if (!file_exists($file)) {
                    echo "Migration file not found: $migrationName\n";
                    continue;
                }
                $migration = include $file;
                if (is_array($migration) && !empty($migration['core']) && !$force) {
                    echo "Refusing to roll back core migration: $migrationName. Use --force to override.\n";
                    continue;
                }
                echo "Rolling back: $migrationName... ";
                if (is_array($migration) && isset($migration['down']) && is_callable($migration['down'])) {
                    $migration['down']($db);
                } else {
                    echo "No valid down() method for $migrationName\n";
                    continue;
                }
                $db->query('DELETE FROM migrations WHERE migration = ?', [$migrationName]);
                echo "done.\n";
            }
        }
        $db->query('UPDATE migration_lock SET locked = 0, locked_at = NULL, locked_by = NULL WHERE id = 1');
        exit(0);
    } elseif ($direction === 'up') {
        if ($target) {
            // Apply single migration
            $file = $migrationsDir . $target;
            if (!file_exists($file)) {
                echo "Migration file not found: $target\n";
                exit(1);
            }
            if (in_array($target, $applied)) {
                echo "Already applied: $target\n";
                exit(0);
            }
            echo "Applying: $target... ";
            $migration = include $file;
            if (is_array($migration) && isset($migration['up']) && is_callable($migration['up'])) {
                $migration['up']($db);
            } elseif (is_callable($migration)) {
                $migration($db);
            } else {
                echo "Invalid migration format for $target\n";
                exit(1);
            }
            $applied_by = get_current_user() . '@' . gethostname();
            $db->query('INSERT INTO migrations (migration, applied_by) VALUES (?, ?)', [$target, $applied_by]);
            echo "done.\n";
        } else {
            // Run all unapplied migrations (default up behavior)
            foreach ($migrationFiles as $file) {
                $name = basename($file);
                if ($name === '001_create_migrations_table.php') continue;
                if (substr($name, -9) === '.down.php') continue;
                if (in_array($name, $applied)) {
                    echo "Already applied: $name\n";
                    continue;
                }
                echo "Applying: $name... ";
                $migration = include $file;
                if (is_array($migration) && isset($migration['up']) && is_callable($migration['up'])) {
                    $migration['up']($db);
                } elseif (is_callable($migration)) {
                    $migration($db);
                } else {
                    echo "Invalid migration format for $name\n";
                    continue;
                }
                $applied_by = get_current_user() . '@' . gethostname();
                $db->query('INSERT INTO migrations (migration, applied_by) VALUES (?, ?)', [$name, $applied_by]);
                echo "done.\n";
            }
            echo "All migrations complete.\n";
        }
        $db->query('UPDATE migration_lock SET locked = 0, locked_at = NULL, locked_by = NULL WHERE id = 1');
        exit(0);
    }
}

if ($argc >= 2 && $argv[1] === 'status') {
    echo "Migration Status:\n";
    $allMigrations = array_map('basename', $migrationFiles);
    // Fetch applied migrations with dates
    $rows = $db->fetchAll('SELECT migration, applied_at FROM migrations');
    $appliedInfo = [];
    foreach ($rows as $row) {
        $appliedInfo[$row['migration']] = $row['applied_at'] ?? '';
    }
    $maxLen = max(array_map('strlen', $allMigrations));
    foreach ($allMigrations as $name) {
        if (isset($appliedInfo[$name])) {
            $date = $appliedInfo[$name] ? date('Y-m-d', strtotime($appliedInfo[$name])) : '';
            printf("%-{$maxLen}s [x] Applied %s\n", $name, $date);
        } else {
            printf("%-{$maxLen}s [ ] Pending\n", $name);
        }
    }
    echo "\nTotal: ".count($allMigrations)." migrations, ".count($appliedInfo)." applied, ".(count($allMigrations)-count($appliedInfo))." pending.\n";
    $db->query('UPDATE migration_lock SET locked = 0, locked_at = NULL, locked_by = NULL WHERE id = 1');
    exit(0);
}

try {
    foreach ($migrationFiles as $file) {
        $name = basename($file);
        if ($name === '001_create_migrations_table.php') continue;
        if (substr($name, -9) === '.down.php') continue; // skip down migrations
        if (in_array($name, $applied)) {
            echo "Already applied: $name\n";
            continue;
        }
        echo "Applying: $name... ";
        $migration = include $file;
        if (is_array($migration) && isset($migration['up']) && is_callable($migration['up'])) {
            $migration['up']($db);
        } elseif (is_callable($migration)) {
            $migration($db);
        } else {
            echo "Invalid migration format for $name\n";
            continue;
        }
        $applied_by = get_current_user() . '@' . gethostname();
        $db->query('INSERT INTO migrations (migration, applied_by) VALUES (?, ?)', [$name, $applied_by]);
        echo "done.\n";
    }
    echo "All migrations complete.\n";
} finally {
    // Always clear lock
    $db->query('UPDATE migration_lock SET locked = 0, locked_at = NULL, locked_by = NULL WHERE id = 1');
}

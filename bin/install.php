#!/usr/bin/env php
<?php

// Initial install script for StrataPHP Framework

$projectRoot = dirname(__DIR__); // /Users/barry/myapp - THIS IS THE FIX

$envFile = $projectRoot . '/.env';
$envExample = $projectRoot . '/env.example';
$configFile = $projectRoot . '/public_html/app/config.php';
$schemaFile = $projectRoot . '/mysql/db_instal.sql';
$migrationsDir = $projectRoot . '/migrations';

// Auto-create .env if missing
if (!file_exists($envFile)) {
    if (file_exists($envExample)) {
        copy($envExample, $envFile);
        echo "✓ Created .env from env.example\n";
        echo "⚠  Edit .env with your database credentials, then re-run this installer:\n";
        echo "   php bin/install.php\n";
        exit(1);
    }
    echo "ERROR: .env not found and no env.example. Re-run composer install.\n";
    exit(1);
}

if (!file_exists($configFile)) {
    echo "ERROR: config.php not found at: $configFile\n";
    echo "Make sure you ran: composer create-project lazysod/strataphp myapp\n";
    exit(1);
}

if (!file_exists($schemaFile)) {
    echo "Schema file not found: $schemaFile\n";
    exit(1);
}

require_once $configFile;
$config = $config ?? require $configFile;
$dbConfig = $config['db'];

/**
 * Record migrations already represented by mysql/db_instal.sql so migrate.php
 * only applies migrations that are genuinely newer than the bundled schema.
 */
function syncSchemaBackedMigrations(PDO $pdo, string $migrationsDir): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        applied_by VARCHAR(255) DEFAULT NULL
    ) ENGINE=InnoDB;");

    $schemaBackedMigrations = [
        '001_core.php',
        '003_drop_display_name_from_users.php',
        '004_add_applied_by_to_migrations.php',
        '005_create_migration_lock_table.php',
        '006_create_links_table.php',
        '008_create_user_sessions_table.php',
        '009_add_ip_address_to_user_sessions.php',
        '010_add_device_info_to_user_sessions.php',
        '021_add_sites_table_and_site_id_to_cms_pages.php',
        '022_add_social_seo_fields_to_cms_pages.php',
        '023_create_oauth_tables.php',
        '024_add_session_lookup_index.php',
        '025_oauth_security_fields.php',
        '026_create_google_analytics_settings_table.php',
        '027_add_display_name_to_users.php',
        '028_add_order_column_to_links_table.php',
        '029_create_user_activation_table.php'
    ];

    $appliedBy = 'install.php schema import';
    $stmt = $pdo->prepare('INSERT IGNORE INTO migrations (migration, applied_by) VALUES (?, ?)');
    foreach ($schemaBackedMigrations as $migrationName) {
        if (file_exists($migrationsDir . '/' . $migrationName)) {
            $stmt->execute([$migrationName, $appliedBy]);
        }
    }
}

try {
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents($schemaFile);
    $pdo->exec($sql);
    echo "✓ Database schema imported successfully.\n";

    syncSchemaBackedMigrations($pdo, $migrationsDir);
    echo "✓ Schema-backed migrations registered successfully.\n";

    $migrateCommand = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__DIR__ . '/migrate.php');
    exec($migrateCommand . ' 2>&1', $migrationOutput, $migrationExitCode);

    if ($migrationExitCode !== 0) {
        echo "Error applying migrations after schema import:\n";
        echo implode("\n", $migrationOutput) . "\n";
        exit(1);
    }

    echo "✓ Pending migrations applied successfully.\n";
} catch (Exception $e) {
    echo "Error importing schema: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✓ StrataPHP installed successfully!\n";
echo "Next step: Create your admin account:\n";
echo "php bin/create_admin.php\n";
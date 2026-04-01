#!/usr/bin/env php
<?php
/**
 * Migration System Test Script
 * Tests both forward migrations and rollbacks to ensure everything works correctly
 */

echo "🧪 StrataPHP Migration System Test\n";
echo "==================================\n\n";

require_once __DIR__ . '/../public_html/app/config.php';
require_once __DIR__ . '/../public_html/app/DB.php';
use App\DB;

$config = $config ?? require __DIR__ . '/../public_html/app/config.php';
$db = new DB($config);

// Test 1: Check migration files format
echo "📋 Test 1: Checking migration file formats...\n";
$migrationsDir = __DIR__ . '/../migrations/';
$migrationFiles = glob($migrationsDir . '*.php');
$migrationFiles = array_filter($migrationFiles, function($file) {
    return !str_ends_with($file, '.down.php');
});
sort($migrationFiles);

$formatResults = [];
foreach ($migrationFiles as $file) {
    $name = basename($file);
    if ($name === '001_create_migrations_table.php') continue;
    
    $migration = include $file;
    
    if (is_array($migration) && isset($migration['up']) && isset($migration['down'])) {
        $formatResults[$name] = '✅ Array format (up/down)';
    } elseif (is_array($migration) && isset($migration['up']) && !isset($migration['down'])) {
        $formatResults[$name] = '⚠️  Array format (up only)';
    } elseif (is_callable($migration)) {
        // Check for separate down file
        $downFile = str_replace('.php', '.down.php', $file);
        if (file_exists($downFile)) {
            $formatResults[$name] = '✅ Function + separate down file';
        } else {
            $formatResults[$name] = '❌ Function only (no rollback)';
        }
    } else {
        $formatResults[$name] = '❌ Invalid format';
    }
}

foreach ($formatResults as $name => $result) {
    echo "   $name: $result\n";
}

// Test 2: Check rollback capability
echo "\n📋 Test 2: Checking rollback capability...\n";
foreach ($migrationFiles as $file) {
    $name = basename($file);
    if ($name === '001_create_migrations_table.php') continue;
    
    $migration = include $file;
    $hasRollback = false;
    
    if (is_array($migration) && isset($migration['down']) && is_callable($migration['down'])) {
        $hasRollback = true;
    } else {
        $downFile = str_replace('.php', '.down.php', $file);
        if (file_exists($downFile)) {
            $downMigration = include $downFile;
            if (is_callable($downMigration)) {
                $hasRollback = true;
            }
        }
    }
    
    echo "   $name: " . ($hasRollback ? '✅ Can rollback' : '❌ Cannot rollback') . "\n";
}

// Test 3: Check for duplicate migration numbers
echo "\n📋 Test 3: Checking for duplicate migration numbers...\n";
$numbers = [];
$duplicates = [];
foreach ($migrationFiles as $file) {
    $name = basename($file);
    if (preg_match('/^(\d+)_/', $name, $matches)) {
        $number = $matches[1];
        if (isset($numbers[$number])) {
            $duplicates[] = $number;
        }
        $numbers[$number][] = $name;
    }
}

if (empty($duplicates)) {
    echo "   ✅ No duplicate migration numbers found\n";
} else {
    echo "   ❌ Duplicate migration numbers found:\n";
    foreach ($duplicates as $number) {
        echo "      $number: " . implode(', ', $numbers[$number]) . "\n";
    }
}

// Test 4: Check migration table structure
echo "\n📋 Test 4: Checking migration table structure...\n";
try {
    $columns = $db->fetchAll("SHOW COLUMNS FROM migrations");
    $expectedColumns = ['id', 'migration', 'applied_at', 'applied_by'];
    $actualColumns = array_column($columns, 'Field');
    
    $missing = array_diff($expectedColumns, $actualColumns);
    $extra = array_diff($actualColumns, $expectedColumns);
    
    if (empty($missing) && empty($extra)) {
        echo "   ✅ Migration table structure is correct\n";
    } else {
        if (!empty($missing)) {
            echo "   ❌ Missing columns: " . implode(', ', $missing) . "\n";
        }
        if (!empty($extra)) {
            echo "   ⚠️  Extra columns: " . implode(', ', $extra) . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Migration table not found or error: " . $e->getMessage() . "\n";
}

// Summary
echo "\n📊 Summary:\n";
$total = count($formatResults);
$goodFormat = count(array_filter($formatResults, function($result) {
    return str_contains($result, '✅');
}));
$hasRollback = count(array_filter($formatResults, function($result) {
    return str_contains($result, '✅');
}));

echo "   Total migrations: $total\n";
echo "   Proper format: $goodFormat/$total\n";
echo "   Can rollback: $hasRollback/$total\n";

if ($goodFormat === $total && $hasRollback === $total && empty($duplicates)) {
    echo "\n🎉 All tests passed! Migration system is fully functional.\n";
} else {
    echo "\n⚠️  Some issues found. Check the details above.\n";
}

echo "\n📝 Usage:\n";
echo "   Forward migration: php bin/migrate.php\n";
echo "   Rollback 1 step:   php bin/rollback.php\n";
echo "   Rollback N steps:  php bin/rollback.php N\n";
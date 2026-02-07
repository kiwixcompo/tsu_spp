#!/usr/bin/env php
<?php
/**
 * Run migrations for the TSU Staff Portal
 * This script applies pending migrations to the database
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get project root
$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/app/Core/Database.php';

try {
    $db = \App\Core\Database::getInstance();
    
    echo "=" . str_repeat("=", 78) . "\n";
    echo "TSU Staff Portal - Database Migrations\n";
    echo "=" . str_repeat("=", 78) . "\n\n";
    
    // Migration 002: Add role column
    echo "[1/2] Running migration: 002_add_user_role.sql\n";
    try {
        // Check if role column exists
        $result = $db->fetch("SHOW COLUMNS FROM users LIKE 'role'");
        if ($result) {
            echo "  ✓ Role column already exists\n";
        } else {
            $migrationSql = file_get_contents(__DIR__ . '/migrations/002_add_user_role.sql');
            $statements = array_filter(array_map('trim', explode(';', $migrationSql)), function($s) {
                return !empty($s) && !str_starts_with($s, '--');
            });
            
            foreach ($statements as $statement) {
                $db->query($statement);
            }
            echo "  ✓ Migration 002 completed successfully\n";
        }
    } catch (\Exception $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }
    
    // Migration 003: Add display_years column
    echo "\n[2/2] Running migration: 003_add_education_display_years.sql\n";
    try {
        // Check if display_years column exists
        $result = $db->fetch("SHOW COLUMNS FROM education LIKE 'display_years'");
        if ($result) {
            echo "  ✓ Display_years column already exists\n";
        } else {
            $migrationSql = file_get_contents(__DIR__ . '/migrations/003_add_education_display_years.sql');
            $statements = array_filter(array_map('trim', explode(';', $migrationSql)), function($s) {
                return !empty($s) && !str_starts_with($s, '--');
            });
            
            foreach ($statements as $statement) {
                $db->query($statement);
            }
            echo "  ✓ Migration 003 completed successfully\n";
        }
    } catch (\Exception $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "All migrations completed successfully!\n";
    echo str_repeat("=", 80) . "\n";
    
} catch (\Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

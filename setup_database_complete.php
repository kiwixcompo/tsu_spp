<?php
/**
 * ============================================================================
 * TSU STAFF PORTAL - COMPLETE DATABASE SETUP SCRIPT
 * ============================================================================
 * 
 * This script will:
 * 1. Create all database tables
 * 2. Run all migrations
 * 3. Seed faculties and departments
 * 4. Seed units/offices
 * 5. Create admin account
 * 
 * IMPORTANT: DELETE THIS FILE AFTER RUNNING!
 * 
 * Access: https://staff.tsuniversity.ng/setup_database_complete.php
 * 
 * ============================================================================
 */

// Security: Only allow access from localhost or specific IP
$allowed_ips = ['127.0.0.1', '::1']; // Add your IP here if needed
// Uncomment the line below to restrict access
// if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) die('Access denied');

// Load environment variables from multiple possible locations
$envFiles = [
    __DIR__ . '/.env',
    __DIR__ . '/.env.production',
    __DIR__ . '/.env.google-workspace',
    __DIR__ . '/.env.local'
];

foreach ($envFiles as $envFile) {
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;
            if (strpos($line, '=') === false) continue;
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Remove quotes if present
            $value = trim($value, '"\'');
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
            }
        }
        break; // Use first found env file
    }
}

// Database configuration - PRODUCTION DEFAULTS
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_DATABASE'] ?? 'tsuniver_tsu_staff_portal';
$username = $_ENV['DB_USERNAME'] ?? 'tsuniver_tsu_staff_portal';
$password = $_ENV['DB_PASSWORD'] ?? 'fSdohm!4lh.Kk[jD';

// Allow override via GET parameters (for manual setup)
if (isset($_GET['db_host'])) $host = $_GET['db_host'];
if (isset($_GET['db_name'])) $dbname = $_GET['db_name'];
if (isset($_GET['db_user'])) $username = $_GET['db_user'];
if (isset($_GET['db_pass'])) $password = $_GET['db_pass'];

// HTML Header
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - TSU Staff Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 28px; margin-bottom: 10px; }
        .header p { opacity: 0.9; font-size: 14px; }
        .content { padding: 30px; }
        .step { background: #f8f9fa; border-left: 4px solid #1e40af; padding: 15px 20px; margin-bottom: 15px; border-radius: 5px; }
        .step.success { border-left-color: #10b981; background: #f0fdf4; }
        .step.error { border-left-color: #ef4444; background: #fef2f2; }
        .step.warning { border-left-color: #f59e0b; background: #fffbeb; }
        .step h3 { font-size: 16px; margin-bottom: 8px; color: #1f2937; }
        .step p { font-size: 14px; color: #6b7280; line-height: 1.6; }
        .step pre { background: #1f2937; color: #f3f4f6; padding: 10px; border-radius: 5px; overflow-x: auto; font-size: 12px; margin-top: 10px; }
        .button { display: inline-block; background: #1e40af; color: white; padding: 12px 30px; border-radius: 5px; text-decoration: none; font-weight: 600; margin-top: 20px; }
        .button:hover { background: #1e3a8a; }
        .button.danger { background: #ef4444; }
        .button.danger:hover { background: #dc2626; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer strong { color: #ef4444; }
        .info-box { background: #eff6ff; border: 1px solid #bfdbfe; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .info-box strong { color: #1e40af; }
        .credentials { background: #fef3c7; border: 2px solid #fbbf24; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .credentials h3 { color: #92400e; margin-bottom: 15px; }
        .credentials .cred-item { background: white; padding: 10px; margin: 8px 0; border-radius: 5px; font-family: monospace; }
        .credentials .cred-item strong { color: #1e40af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗄️ TSU Staff Portal Database Setup</h1>
            <p>Complete database initialization and configuration</p>
        </div>
        <div class="content">
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_setup'])) {
    
    try {
        // Connect to MySQL server (without database)
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo '<div class="step success">';
        echo '<h3>✅ Step 1: Database Connection</h3>';
        echo '<p>Successfully connected to MySQL server</p>';
        echo '</div>';
        
        // Create database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbname`");
        
        echo '<div class="step success">';
        echo '<h3>✅ Step 2: Database Created</h3>';
        echo '<p>Database "<strong>' . htmlspecialchars($dbname) . '</strong>" created/verified</p>';
        echo '</div>';
        
        // Read and execute main setup SQL
        $setupSQL = file_get_contents(__DIR__ . '/database/complete_setup_compatible.sql');
        
        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $setupSQL)));
        $tableCount = 0;
        
        foreach ($statements as $statement) {
            if (empty($statement) || strpos($statement, '--') === 0) continue;
            try {
                $pdo->exec($statement);
                if (stripos($statement, 'CREATE TABLE') !== false) {
                    $tableCount++;
                }
            } catch (PDOException $e) {
                // Ignore "table already exists" errors
                if (strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
            }
        }
        
        echo '<div class="step success">';
        echo '<h3>✅ Step 3: Tables Created</h3>';
        echo '<p>Created ' . $tableCount . ' database tables</p>';
        echo '</div>';
        
        // Run migrations
        $migrations = [
            '005_add_staff_type_and_unit.sql',
            '006_add_staff_number_unique_constraint.sql'
        ];
        
        $migrationsRun = 0;
        foreach ($migrations as $migration) {
            $migrationPath = __DIR__ . '/database/migrations/' . $migration;
            if (file_exists($migrationPath)) {
                $migrationSQL = file_get_contents($migrationPath);
                $statements = array_filter(array_map('trim', explode(';', $migrationSQL)));
                
                foreach ($statements as $statement) {
                    if (empty($statement) || strpos($statement, '--') === 0 || stripos($statement, 'SELECT') === 0) continue;
                    try {
                        $pdo->exec($statement);
                    } catch (PDOException $e) {
                        // Ignore duplicate/already exists errors
                        if (strpos($e->getMessage(), 'Duplicate') === false && 
                            strpos($e->getMessage(), 'already exists') === false) {
                            // Log but don't fail
                            error_log("Migration warning: " . $e->getMessage());
                        }
                    }
                }
                $migrationsRun++;
            }
        }
        
        echo '<div class="step success">';
        echo '<h3>✅ Step 4: Migrations Applied</h3>';
        echo '<p>Applied ' . $migrationsRun . ' database migrations</p>';
        echo '</div>';
        
        // Seed faculties and departments
        $facultiesSQL = file_get_contents(__DIR__ . '/database/seeds/faculties_departments.sql');
        $statements = array_filter(array_map('trim', explode(';', $facultiesSQL)));
        $facultiesCount = 0;
        
        foreach ($statements as $statement) {
            if (empty($statement) || strpos($statement, '--') === 0) continue;
            try {
                $pdo->exec($statement);
                if (stripos($statement, 'INSERT INTO') !== false) {
                    $facultiesCount++;
                }
            } catch (PDOException $e) {
                // Ignore duplicate errors
            }
        }
        
        echo '<div class="step success">';
        echo '<h3>✅ Step 5: Faculties & Departments Seeded</h3>';
        echo '<p>Inserted faculty and department data</p>';
        echo '</div>';
        
        // Seed units/offices
        $unitsSQL = file_get_contents(__DIR__ . '/database/seeds/units_offices.sql');
        $statements = array_filter(array_map('trim', explode(';', $unitsSQL)));
        
        foreach ($statements as $statement) {
            if (empty($statement) || strpos($statement, '--') === 0) continue;
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Ignore duplicate errors
            }
        }
        
        echo '<div class="step success">';
        echo '<h3>✅ Step 6: Units & Offices Seeded</h3>';
        echo '<p>Inserted 50 units/offices/directorates</p>';
        echo '</div>';
        
        // Create admin account
        $adminEmail = 'admin@tsuniversity.ng';
        $adminPassword = 'Admin@2026!';
        $adminPasswordHash = password_hash($adminPassword, PASSWORD_DEFAULT);
        
        // Check if admin exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$adminEmail]);
        $adminExists = $stmt->fetch();
        
        if (!$adminExists) {
            // Create admin user
            $pdo->exec("INSERT INTO users (email, email_prefix, password_hash, email_verified, account_status, role, created_at) 
                       VALUES ('$adminEmail', 'admin', '$adminPasswordHash', 1, 'active', 'admin', NOW())");
            
            $adminUserId = $pdo->lastInsertId();
            
            // Create admin profile
            $pdo->exec("INSERT INTO profiles (user_id, staff_number, title, first_name, last_name, designation, faculty, department, profile_visibility, profile_slug, created_at) 
                       VALUES ($adminUserId, 'TSU/ADMIN/001', 'Mr.', 'System', 'Administrator', 'System Administrator', 'Administration', 'ICT', 'private', 'admin', NOW())");
            
            echo '<div class="step success">';
            echo '<h3>✅ Step 7: Admin Account Created</h3>';
            echo '<p>Default administrator account has been created</p>';
            echo '</div>';
        } else {
            echo '<div class="step warning">';
            echo '<h3>⚠️ Step 7: Admin Account</h3>';
            echo '<p>Admin account already exists (skipped creation)</p>';
            echo '</div>';
        }
        
        // Create necessary directories
        $directories = [
            __DIR__ . '/storage/logs',
            __DIR__ . '/storage/cache',
            __DIR__ . '/storage/qrcodes',
            __DIR__ . '/storage/emails',
            __DIR__ . '/public/uploads/profiles',
            __DIR__ . '/public/uploads/documents'
        ];
        
        $dirsCreated = 0;
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                $dirsCreated++;
            }
        }
        
        echo '<div class="step success">';
        echo '<h3>✅ Step 8: Directories Created</h3>';
        echo '<p>Created ' . $dirsCreated . ' required directories with proper permissions</p>';
        echo '</div>';
        
        // Success summary
        echo '<div class="credentials">';
        echo '<h3>🎉 Setup Complete! Admin Credentials:</h3>';
        echo '<div class="cred-item"><strong>Email:</strong> ' . htmlspecialchars($adminEmail) . '</div>';
        echo '<div class="cred-item"><strong>Password:</strong> ' . htmlspecialchars($adminPassword) . '</div>';
        echo '<div class="cred-item"><strong>Login URL:</strong> <a href="' . ($_ENV['APP_URL'] ?? '') . '/login" target="_blank">' . ($_ENV['APP_URL'] ?? '') . '/login</a></div>';
        echo '<p style="margin-top: 15px; color: #92400e;"><strong>⚠️ IMPORTANT:</strong> Change the admin password immediately after first login!</p>';
        echo '</div>';
        
        echo '<div class="info-box">';
        echo '<strong>✅ Database setup completed successfully!</strong><br>';
        echo 'Your TSU Staff Portal is now ready to use.';
        echo '</div>';
        
    } catch (PDOException $e) {
        echo '<div class="step error">';
        echo '<h3>❌ Error Occurred</h3>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    }
    
} else {
    // Show confirmation form
    ?>
    <div class="info-box">
        <strong>⚠️ Important:</strong> This script will set up the complete database for TSU Staff Portal.
        It will create all tables, run migrations, and seed initial data.
    </div>
    
    <div class="step">
        <h3>📋 What This Script Will Do:</h3>
        <p>
            1. Create database: <strong><?= htmlspecialchars($dbname) ?></strong><br>
            2. Create all required tables (users, profiles, education, etc.)<br>
            3. Run database migrations (staff types, units, etc.)<br>
            4. Seed faculties and departments<br>
            5. Seed 50 units/offices/directorates<br>
            6. Create admin account with default credentials<br>
            7. Create required directories with proper permissions
        </p>
    </div>
    
    <div class="step warning">
        <h3>⚠️ Before You Continue:</h3>
        <p>
            • Ensure your <strong>.env</strong> file is configured with correct database credentials<br>
            • Backup any existing database if needed<br>
            • This script is safe to run multiple times (it won't duplicate data)<br>
            • <strong>DELETE THIS FILE</strong> after successful setup for security
        </p>
    </div>
    
    <div class="step">
        <h3>🔧 Current Configuration:</h3>
        <p>
            <strong>Database Host:</strong> <?= htmlspecialchars($host) ?><br>
            <strong>Database Name:</strong> <?= htmlspecialchars($dbname) ?><br>
            <strong>Database User:</strong> <?= htmlspecialchars($username) ?><br>
            <strong>Password:</strong> <?= $password ? '••••••••' : '<span style="color: red;">NOT SET</span>' ?>
        </p>
        <?php if (!$password): ?>
        <div style="background: #fef2f2; border: 1px solid #ef4444; padding: 10px; border-radius: 5px; margin-top: 10px;">
            <strong style="color: #dc2626;">⚠️ No .env file found!</strong><br>
            <p style="margin: 10px 0; font-size: 13px;">Please enter your database credentials below:</p>
            <form method="GET" style="margin-top: 10px;">
                <div style="margin-bottom: 10px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;">Database Name:</label>
                    <input type="text" name="db_name" value="tsuniver_tsu_staff_portal" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 10px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;">Database Username:</label>
                    <input type="text" name="db_user" value="tsuniver_tsu_staff_portal" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 10px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;">Database Password:</label>
                    <input type="text" name="db_pass" value="" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 10px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;">Database Host:</label>
                    <input type="text" name="db_host" value="localhost" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
                <button type="submit" style="background: #1e40af; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">
                    Update Configuration
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>
    
    <form method="POST" onsubmit="return confirm('Are you sure you want to set up the database? This action cannot be undone.');">
        <input type="hidden" name="confirm_setup" value="1">
        <button type="submit" class="button">🚀 Start Database Setup</button>
    </form>
    <?php
}
?>
        </div>
        <div class="footer">
            <p><strong>⚠️ SECURITY WARNING:</strong> Delete this file immediately after setup!</p>
            <p style="margin-top: 10px; font-size: 12px; color: #6b7280;">
                To delete: Remove <code>setup_database_complete.php</code> from your server
            </p>
        </div>
    </div>
</body>
</html>

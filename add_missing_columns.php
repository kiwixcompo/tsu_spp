<?php
/**
 * Add Missing Database Columns
 * Run this script once to add missing columns to production database
 * Access: https://staff.tsuniversity.edu.ng/add_missing_columns.php
 * 
 * IMPORTANT: Delete this file after running!
 */

// Load environment variables
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || $line[0] === '#') continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (strlen($value) > 0) {
                if (($value[0] === '"' && substr($value, -1) === '"') || 
                    ($value[0] === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
            }
            $_ENV[$name] = $value;
        }
    }
}

// Database connection
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_DATABASE'] ?? '';
$username = $_ENV['DB_USERNAME'] ?? '';
$password = $_ENV['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<!DOCTYPE html><html><head><title>Add Missing Columns</title>";
    echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;}";
    echo ".success{color:green;background:#d4edda;padding:10px;border-radius:5px;margin:5px 0;}";
    echo ".error{color:red;background:#f8d7da;padding:10px;border-radius:5px;margin:5px 0;}";
    echo ".info{color:#004085;background:#cce5ff;padding:10px;border-radius:5px;margin:5px 0;}";
    echo ".warning{color:#856404;background:#fff3cd;padding:15px;border-radius:5px;margin:10px 0;}";
    echo "h1{color:#333;}</style></head><body>";
    echo "<h1>Adding Missing Database Columns</h1>";
    
    // Check and add missing columns
    echo "<h2>Checking profiles table...</h2>";
    
    // First, get all existing columns
    $stmt = $pdo->query("SHOW COLUMNS FROM profiles");
    $existingColumns = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $existingColumns[] = $row['Field'];
    }
    
    echo "<div class='info'>Existing columns: " . implode(', ', $existingColumns) . "</div>";
    
    // Find the last column to use as reference
    $lastColumn = end($existingColumns);
    
    // Define columns to add (without AFTER clause, will be added at end)
    $columnsToAdd = [
        'phone' => "ALTER TABLE profiles ADD COLUMN phone VARCHAR(20) NULL",
        'bio' => "ALTER TABLE profiles ADD COLUMN bio TEXT NULL",
        'allow_contact' => "ALTER TABLE profiles ADD COLUMN allow_contact TINYINT(1) DEFAULT 1",
        'qr_code_path' => "ALTER TABLE profiles ADD COLUMN qr_code_path VARCHAR(255) NULL"
    ];
    
    foreach ($columnsToAdd as $columnName => $sql) {
        try {
            // Check if column exists
            $stmt = $pdo->query("SHOW COLUMNS FROM profiles LIKE '$columnName'");
            $exists = $stmt->fetch();
            
            if (!$exists) {
                echo "<div class='info'>Adding column: $columnName...</div>";
                $pdo->exec($sql);
                echo "<div class='success'>✓ Column '$columnName' added successfully!</div>";
            } else {
                echo "<div class='info'>Column '$columnName' already exists. Skipping.</div>";
            }
        } catch (PDOException $e) {
            echo "<div class='error'>Error adding '$columnName': " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
    echo "<h2 class='success'>✓ Database Update Complete!</h2>";
    echo "<div class='warning'>";
    echo "<p><strong>IMPORTANT: Delete this file (add_missing_columns.php) now for security!</strong></p>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='error'><strong>Database Connection Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</body></html>";

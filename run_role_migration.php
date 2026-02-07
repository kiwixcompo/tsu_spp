<?php
/**
 * Add role column to users table and assign admin role
 */

require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    
    echo "Starting migration...\n";
    
    // Check if role column already exists
    $result = $db->query("SHOW COLUMNS FROM users LIKE 'role'");
    $columnExists = $result->rowCount() > 0;
    
    if (!$columnExists) {
        echo "Adding role column to users table...\n";
        $db->query("ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' AFTER account_status");
        echo "✓ Role column added\n";
        
        echo "Adding index for role column...\n";
        $db->query("ALTER TABLE users ADD INDEX idx_role (role)");
        echo "✓ Index added\n";
    } else {
        echo "✓ Role column already exists\n";
    }
    
    // Update admin user
    echo "Assigning admin role to admin@tsuniversity.edu.ng...\n";
    $result = $db->update('users', ['role' => 'admin'], "email = ?", ['admin@tsuniversity.edu.ng']);
    
    if ($result) {
        echo "✓ Admin role assigned successfully\n";
    } else {
        echo "⚠ Admin user not found or already has admin role\n";
    }
    
    // Verify
    $admin = $db->fetch("SELECT email, role FROM users WHERE email = ?", ['admin@tsuniversity.edu.ng']);
    if ($admin) {
        echo "\nVerification:\n";
        echo "Email: {$admin['email']}\n";
        echo "Role: {$admin['role']}\n";
    }
    
    echo "\n✅ Migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

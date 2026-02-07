<?php
/**
 * Check Admin Password in Database
 */

// Load environment
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Simple autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

echo "<h1>Admin Password Check</h1>";

try {
    $db = App\Core\Database::getInstance();
    
    // Get admin user
    $admin = $db->fetch(
        "SELECT * FROM users WHERE email = ?",
        ['admin@tsuniversity.edu.ng']
    );
    
    if ($admin) {
        echo "<h2>✅ Admin User Found</h2>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($admin['email']) . "</p>";
        echo "<p><strong>Email Verified:</strong> " . ($admin['email_verified'] ? 'Yes' : 'No') . "</p>";
        echo "<p><strong>Account Status:</strong> " . htmlspecialchars($admin['account_status']) . "</p>";
        echo "<p><strong>Password Hash:</strong> " . htmlspecialchars(substr($admin['password_hash'], 0, 50)) . "...</p>";
        
        // Test password verification
        $testPasswords = ['Admin123!', 'password', 'admin', 'admin123'];
        
        echo "<h3>Password Tests:</h3>";
        foreach ($testPasswords as $testPassword) {
            $isValid = password_verify($testPassword, $admin['password_hash']);
            $status = $isValid ? '✅ VALID' : '❌ Invalid';
            echo "<p><strong>{$testPassword}:</strong> {$status}</p>";
        }
        
        // Generate new password hash for Admin123!
        echo "<h3>Generate New Hash:</h3>";
        $newHash = password_hash('Admin123!', PASSWORD_DEFAULT);
        echo "<p>New hash for 'Admin123!': <code>{$newHash}</code></p>";
        
        echo "<h3>Update Query:</h3>";
        echo "<pre>UPDATE users SET password_hash = '{$newHash}' WHERE email = 'admin@tsuniversity.edu.ng';</pre>";
        
    } else {
        echo "<h2>❌ Admin User Not Found</h2>";
        echo "<p>The admin user doesn't exist in the database.</p>";
        
        // Show all users
        $users = $db->fetchAll("SELECT email, account_status, email_verified FROM users");
        echo "<h3>All Users in Database:</h3>";
        foreach ($users as $user) {
            echo "<p>Email: {$user['email']}, Status: {$user['account_status']}, Verified: " . ($user['email_verified'] ? 'Yes' : 'No') . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Database Error</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='/tsu_spp/public/'>← Back to Homepage</a></p>";
?>
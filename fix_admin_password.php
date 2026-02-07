<?php
/**
 * Fix Admin Password
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

echo "<h1>Fix Admin Password</h1>";

try {
    $db = App\Core\Database::getInstance();
    
    // Generate correct password hash for Admin123!
    $correctHash = password_hash('Admin123!', PASSWORD_DEFAULT);
    
    // Update admin password
    $updated = $db->update(
        'users',
        ['password_hash' => $correctHash],
        'email = ?',
        ['admin@tsuniversity.edu.ng']
    );
    
    if ($updated > 0) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;'>";
        echo "<h3>✅ Admin Password Updated Successfully!</h3>";
        echo "<p><strong>Email:</strong> admin@tsuniversity.edu.ng</p>";
        echo "<p><strong>Password:</strong> Admin123!</p>";
        echo "<p>You can now login with these credentials.</p>";
        echo "</div>";
        
        // Verify the password works
        $admin = $db->fetch("SELECT * FROM users WHERE email = ?", ['admin@tsuniversity.edu.ng']);
        $isValid = password_verify('Admin123!', $admin['password_hash']);
        
        echo "<h3>Verification Test:</h3>";
        echo "<p>Password 'Admin123!' verification: " . ($isValid ? '✅ SUCCESS' : '❌ FAILED') . "</p>";
        
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
        echo "<h3>❌ Failed to Update Password</h3>";
        echo "<p>No rows were updated. The admin user might not exist.</p>";
        echo "</div>";
        
        // Check if admin user exists
        $admin = $db->fetch("SELECT * FROM users WHERE email = ?", ['admin@tsuniversity.edu.ng']);
        if (!$admin) {
            echo "<h3>Creating Admin User...</h3>";
            
            $userId = $db->insert('users', [
                'email' => 'admin@tsuniversity.edu.ng',
                'email_prefix' => 'admin',
                'password_hash' => $correctHash,
                'email_verified' => true,
                'account_status' => 'active'
            ]);
            
            if ($userId) {
                echo "<p>✅ Admin user created with ID: {$userId}</p>";
                
                // Create admin profile
                $profileId = $db->insert('profiles', [
                    'user_id' => $userId,
                    'title' => 'Dr.',
                    'first_name' => 'System',
                    'last_name' => 'Administrator',
                    'faculty' => 'Administration',
                    'department' => 'IT Department',
                    'designation' => 'System Administrator',
                    'profile_slug' => 'admin',
                    'professional_summary' => 'System administrator for TSU Staff Portal'
                ]);
                
                echo "<p>✅ Admin profile created with ID: {$profileId}</p>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Database Error</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='/tsu_spp/public/login'>Try Login Again</a> | <a href='/tsu_spp/public/'>Homepage</a></p>";
?>
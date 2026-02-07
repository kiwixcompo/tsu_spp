<?php
echo "⚙️ Creating System Settings Infrastructure\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    $config = require 'config/database.php';
    $db = $config['connections']['mysql'];
    $pdo = new PDO(
        "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']};charset={$db['charset']}",
        $db['username'],
        $db['password']
    );
    
    // Check if settings table exists
    $tableExists = $pdo->query("SHOW TABLES LIKE 'system_settings'")->rowCount() > 0;
    
    if (!$tableExists) {
        echo "Creating system_settings table...\n";
        $pdo->exec("
            CREATE TABLE system_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(100) NOT NULL UNIQUE,
                setting_value TEXT,
                setting_type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_key (setting_key)
            )
        ");
        echo "✅ Table created\n\n";
    } else {
        echo "✅ Settings table already exists\n\n";
    }
    
    // Insert default settings
    echo "Inserting default settings...\n";
    
    $defaultSettings = [
        ['site_name', 'TSU Staff Profile Portal', 'string', 'Portal name displayed across the site'],
        ['site_description', 'Taraba State University Staff Profile Management System', 'string', 'Brief description of the portal'],
        ['admin_email', 'admin@tsuniversity.edu.ng', 'string', 'Primary admin contact email'],
        ['allow_registration', '1', 'boolean', 'Allow new user registrations'],
        ['require_email_verification', '1', 'boolean', 'Require email verification for new accounts'],
        ['auto_approve_users', '0', 'boolean', 'Automatically approve users after verification'],
        ['default_profile_visibility', 'public', 'string', 'Default visibility for new profiles'],
        ['require_profile_photo', '0', 'boolean', 'Make profile photo mandatory'],
        ['max_photo_size', '2', 'integer', 'Maximum photo size in MB'],
        ['session_timeout', '120', 'integer', 'Session timeout in minutes'],
        ['password_min_length', '8', 'integer', 'Minimum password length'],
        ['enable_2fa', '0', 'boolean', 'Enable two-factor authentication'],
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO system_settings (setting_key, setting_value, setting_type, description)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            setting_value = VALUES(setting_value),
            description = VALUES(description)
    ");
    
    foreach ($defaultSettings as $setting) {
        $stmt->execute($setting);
        echo "  ✅ {$setting[0]}\n";
    }
    
    echo "\n✅ Default settings configured!\n";
    echo "\n📊 Total Settings: " . count($defaultSettings) . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
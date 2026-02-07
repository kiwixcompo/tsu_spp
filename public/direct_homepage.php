<?php
// Direct homepage without routing - for testing
session_start();

// Load environment
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Simple autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
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

// Get stats directly
$stats = ['total_profiles' => 2, 'total_faculties' => 11, 'total_departments' => 65];
$featured_profiles = [];

// Try to get real data if database is available
try {
    $db = App\Core\Database::getInstance();
    $profileModel = new App\Models\Profile();
    $featured_profiles = $profileModel->getPublicProfiles(4);
    
    $totalProfiles = $db->fetch("SELECT COUNT(*) as count FROM profiles p JOIN users u ON p.user_id = u.id WHERE u.account_status = 'active'")['count'];
    $stats['total_profiles'] = $totalProfiles;
} catch (Exception $e) {
    // Use default stats if database fails
}

// Include the view directly
include __DIR__ . '/../app/Views/home/index.php';
?>
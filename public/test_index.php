<?php
// Simple test to see if we can load the home page directly
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

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

// Load environment
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

try {
    // Try to create HomeController directly
    $controller = new App\Controllers\HomeController();
    echo "<h1>HomeController created successfully!</h1>";
    
    // Try to call index method
    $controller->index();
    
} catch (Exception $e) {
    echo "<h1>Error:</h1>";
    echo "<pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
}
?>
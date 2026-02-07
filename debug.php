<?php
// Debug file to check what's happening
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Information</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script Name: " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Request Method: " . $_SERVER['REQUEST_METHOD'] . "</p>";

// Check if files exist
$files_to_check = [
    'config/app.php',
    'app/Core/Router.php',
    'app/Controllers/HomeController.php',
    'routes/web.php',
    'routes/api.php'
];

echo "<h2>File Existence Check</h2>";
foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    echo "<p>{$file}: " . (file_exists($full_path) ? "EXISTS" : "NOT FOUND") . "</p>";
}

// Test autoloader
echo "<h2>Testing Autoloader</h2>";
spl_autoload_register(function ($class) {
    echo "<p>Trying to load class: $class</p>";
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        echo "<p>Class doesn't match App namespace</p>";
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    echo "<p>Looking for file: $file</p>";
    
    if (file_exists($file)) {
        echo "<p>File found, requiring...</p>";
        require $file;
        echo "<p>File loaded successfully!</p>";
    } else {
        echo "<p>File not found!</p>";
    }
});

// Test loading config
echo "<h2>Testing Configuration</h2>";
try {
    $config = require __DIR__ . '/config/app.php';
    echo "<p>Config loaded successfully!</p>";
    echo "<pre>" . print_r($config, true) . "</pre>";
} catch (Exception $e) {
    echo "<p>Error loading config: " . $e->getMessage() . "</p>";
}

// Test loading Router
echo "<h2>Testing Router</h2>";
try {
    $router = new App\Core\Router();
    echo "<p>Router loaded successfully!</p>";
} catch (Exception $e) {
    echo "<p>Error loading Router: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Test loading routes
echo "<h2>Testing Routes</h2>";
try {
    if (file_exists(__DIR__ . '/routes/web.php')) {
        require_once __DIR__ . '/routes/web.php';
        echo "<p>Web routes loaded successfully!</p>";
    } else {
        echo "<p>Web routes file not found!</p>";
    }
} catch (Exception $e) {
    echo "<p>Error loading routes: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
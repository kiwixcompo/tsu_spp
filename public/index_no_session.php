<?php
/**
 * TSU Staff Profile Portal - No Session Version
 * Entry Point without session start
 */

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DON'T start session immediately - this might be causing issues

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Simple autoloader for our classes
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

// Load configuration
$config = require __DIR__ . '/../config/app.php';

try {
    // Initialize router
    $router = new App\Core\Router();

    // Define routes
    require_once __DIR__ . '/../routes/web.php';
    require_once __DIR__ . '/../routes/api.php';

    // Start session only when needed
    if (!session_id()) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        session_start();
    }

    // Handle request
    $router->dispatch();
} catch (Exception $e) {
    // Always show detailed error for debugging
    echo '<h1>Application Error</h1>';
    echo '<div style="background: #f8f9fa; padding: 20px; border-left: 4px solid #dc3545; margin: 20px;">';
    echo '<h3>Error Message:</h3>';
    echo '<p><strong>' . htmlspecialchars($e->getMessage()) . '</strong></p>';
    echo '<h3>File:</h3>';
    echo '<p>' . htmlspecialchars($e->getFile()) . ' (Line: ' . $e->getLine() . ')</p>';
    echo '<h3>Stack Trace:</h3>';
    echo '<pre style="background: white; padding: 10px; overflow: auto;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
    
    // Also show some debug info
    echo '<h2>Debug Information</h2>';
    echo '<div style="background: #e9ecef; padding: 15px; margin: 20px;">';
    echo '<p><strong>Request URI:</strong> ' . ($_SERVER['REQUEST_URI'] ?? 'N/A') . '</p>';
    echo '<p><strong>Request Method:</strong> ' . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . '</p>';
    echo '<p><strong>Script Name:</strong> ' . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . '</p>';
    echo '<p><strong>Document Root:</strong> ' . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . '</p>';
    echo '</div>';
}
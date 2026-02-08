<?php
/**
 * TSU Staff Profile Portal
 * Front Controller - Entry Point
 */

// ============================================================================
// LOAD ENVIRONMENT VARIABLES (FIXED VERSION)
// ============================================================================
// Detect if we're running locally or in production
// Check multiple indicators to determine environment
$isLocalEnvironment = (
    // Check if running on localhost
    (isset($_SERVER['HTTP_HOST']) && (
        strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
        strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
        strpos($_SERVER['HTTP_HOST'], '::1') !== false
    )) ||
    // Check if document root contains typical local paths
    (isset($_SERVER['DOCUMENT_ROOT']) && (
        strpos($_SERVER['DOCUMENT_ROOT'], 'xampp') !== false ||
        strpos($_SERVER['DOCUMENT_ROOT'], 'wamp') !== false ||
        strpos($_SERVER['DOCUMENT_ROOT'], 'mamp') !== false ||
        strpos($_SERVER['DOCUMENT_ROOT'], 'laragon') !== false
    ))
);

// Choose the appropriate .env file based on environment
if ($isLocalEnvironment) {
    // Local development - use .env.local if it exists, otherwise .env
    $envFile = dirname(__DIR__) . '/.env.local';
    if (!file_exists($envFile)) {
        $envFile = dirname(__DIR__) . '/.env';
    }
} else {
    // Production - always use .env
    $envFile = dirname(__DIR__) . '/.env';
}

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Skip comments and empty lines
        if (empty($line) || $line[0] === '#') {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Remove surrounding quotes
            if (strlen($value) > 0) {
                if (($value[0] === '"' && substr($value, -1) === '"') || 
                    ($value[0] === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
            }
            
            // Set environment variable in ALL places
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
            putenv("$name=$value");
        }
    }
}

// ============================================================================
// ERROR REPORTING & LOGGING
// ============================================================================
$isLocal = ($_ENV['APP_ENV'] ?? 'production') === 'local';

// Debug: Log environment variables (only in debug mode)
if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    error_log("ENV loaded - DB_HOST: " . ($_ENV['DB_HOST'] ?? 'NOT SET'));
    error_log("ENV loaded - DB_DATABASE: " . ($_ENV['DB_DATABASE'] ?? 'NOT SET'));
    error_log("ENV loaded - DB_USERNAME: " . ($_ENV['DB_USERNAME'] ?? 'NOT SET'));
    error_log("ENV loaded - DB_PASSWORD: " . (isset($_ENV['DB_PASSWORD']) ? 'SET' : 'NOT SET'));
}
$isDebug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';

error_reporting(E_ALL);
ini_set('display_errors', $isLocal ? '1' : '0'); // Show errors in local
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/error.log');

// Initialize custom error logger (only in production)
if (!$isLocal) {
    require_once __DIR__ . '/../app/Core/ErrorLogger.php';
    \App\Core\ErrorLogger::init();
}

// ============================================================================
// SESSION CONFIGURATION
// ============================================================================
// Start session with secure settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================================
// AUTOLOADER
// ============================================================================
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

// ============================================================================
// LOAD PHPMAILER (OPTIONAL)
// ============================================================================
$phpmailerPath = __DIR__ . '/../vendor/phpmailer/PHPMailer/src';
if (file_exists($phpmailerPath . '/PHPMailer.php')) {
    require_once $phpmailerPath . '/Exception.php';
    require_once $phpmailerPath . '/PHPMailer.php';
    require_once $phpmailerPath . '/SMTP.php';
    // PHPMailer loaded - will be used for emails
} else {
    // PHPMailer not found - will use PHP mail() function as fallback
}

// ============================================================================
// LOAD HELPERS
// ============================================================================
// Load URL helper
if (file_exists(__DIR__ . '/../app/Helpers/UrlHelper.php')) {
    require_once __DIR__ . '/../app/Helpers/UrlHelper.php';
}

// ============================================================================
// ROUTE PARSING
// ============================================================================
// Get the current path
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);

// Debug information (only in debug mode)
if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    error_log("TSU Debug - Request URI: " . $requestUri);
    error_log("TSU Debug - Parsed path: " . $path);
    error_log("TSU Debug - Script name: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A'));
}

// Remove project folder from path if present
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptName !== '/' && strpos($path, $scriptName) === 0) {
    $path = substr($path, strlen($scriptName));
}

// Clean up path
if (empty($path) || $path === '/') {
    $path = '/';
} else {
    $path = '/' . trim($path, '/');
}

if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    error_log("TSU Debug - Final path: " . $path);
}

// ============================================================================
// ROUTER FOR ALL OTHER ROUTES
// ============================================================================
// For all other routes, use the router
try {
    // Load configuration
    $configFile = __DIR__ . '/../config/app.php';
    if (file_exists($configFile)) {
        $config = require $configFile;
    }
    
    // Initialize router
    $router = new App\Core\Router();

    // Define routes
    if (file_exists(__DIR__ . '/../routes/web.php')) {
        require_once __DIR__ . '/../routes/web.php';
    }
    
    if (file_exists(__DIR__ . '/../routes/api.php')) {
        require_once __DIR__ . '/../routes/api.php';
    }

    // Handle request
    $router->dispatch();
    
} catch (Exception $e) {
    // Show detailed error for debugging
    if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
        echo '<!DOCTYPE html><html><head><title>Application Error</title></head><body>';
        echo '<h1>Application Error</h1>';
        echo '<div style="background: #f8f9fa; padding: 20px; border-left: 4px solid #dc3545; margin: 20px;">';
        echo '<h3>Error Message:</h3>';
        echo '<p><strong>' . htmlspecialchars($e->getMessage()) . '</strong></p>';
        echo '<h3>File:</h3>';
        echo '<p>' . htmlspecialchars($e->getFile()) . ' (Line: ' . $e->getLine() . ')</p>';
        echo '<h3>Stack Trace:</h3>';
        echo '<pre style="background: white; padding: 10px; overflow: auto;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
        
        echo '<h2>Debug Information</h2>';
        echo '<div style="background: #e9ecef; padding: 15px; margin: 20px;">';
        echo '<p><strong>Request URI:</strong> ' . htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'N/A') . '</p>';
        echo '<p><strong>Request Method:</strong> ' . htmlspecialchars($_SERVER['REQUEST_METHOD'] ?? 'N/A') . '</p>';
        echo '<p><strong>Parsed Path:</strong> ' . htmlspecialchars($path) . '</p>';
        echo '<p><strong>Script Name:</strong> ' . htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'N/A') . '</p>';
        echo '</div>';
        echo '</body></html>';
    } else {
        http_response_code(500);
        echo '<!DOCTYPE html><html><head><title>Error</title></head><body>';
        echo '<h1>Internal Server Error</h1>';
        echo '<p>Something went wrong. Please try again later.</p>';
        echo '</body></html>';
    }
}
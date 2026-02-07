<?php
/**
 * TSU Staff Profile Portal
 * Front Controller - Entry Point
 */

// ============================================================================
// LOAD ENVIRONMENT VARIABLES (FIXED VERSION)
// ============================================================================
$envFile = dirname(__DIR__) . '/.env';
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
            
            // Set environment variable
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

// ============================================================================
// ERROR REPORTING
// ============================================================================
error_reporting(E_ALL);
// Don't display errors directly - log them instead to avoid breaking JSON responses
ini_set('display_errors', 0);
ini_set('log_errors', 1);

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
// LOAD PHPMAILER
// ============================================================================
$phpmailerPath = __DIR__ . '/../vendor/phpmailer/phpmailer/src';
if (file_exists($phpmailerPath . '/PHPMailer.php')) {
    require_once $phpmailerPath . '/Exception.php';
    require_once $phpmailerPath . '/PHPMailer.php';
    require_once $phpmailerPath . '/SMTP.php';
    error_log("✓ PHPMailer loaded successfully");
} else {
    error_log("✗ PHPMailer files not found at: " . $phpmailerPath);
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
// HOMEPAGE FAST PATH
// ============================================================================
// Handle homepage directly (fast path)
if ($path === '/' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get stats directly for homepage
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
        if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
            error_log("Homepage database error: " . $e->getMessage());
        }
    }

    // Include the simple homepage
    if (file_exists(__DIR__ . '/simple_homepage.php')) {
        include __DIR__ . '/simple_homepage.php';
    } else {
        echo '<h1>TSU Staff Portal</h1><p>Welcome to the TSU Staff Portal</p>';
    }
    exit;
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
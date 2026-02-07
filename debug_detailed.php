<?php
/**
 * Detailed Debug Script for TSU Staff Portal
 */

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>TSU Staff Portal - Detailed Debug</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .section { background: #f5f5f5; padding: 15px; margin: 10px 0; border-left: 4px solid #007cba; }
    pre { background: #f8f8f8; padding: 10px; overflow: auto; }
</style>";

// Step 1: Basic PHP Info
echo "<div class='section'>";
echo "<h2>1. PHP Environment</h2>";
echo "<p>PHP Version: <span class='success'>" . phpversion() . "</span></p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";
echo "<p>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "</p>";
echo "<p>Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</p>";
echo "<p>Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "</p>";
echo "</div>";

// Step 2: File System Check
echo "<div class='section'>";
echo "<h2>2. File System Check</h2>";
$critical_files = [
    '.env' => 'Environment configuration',
    'config/app.php' => 'Application configuration',
    'config/database.php' => 'Database configuration',
    'app/Core/Router.php' => 'Router class',
    'app/Core/Controller.php' => 'Base controller',
    'app/Core/Database.php' => 'Database class',
    'app/Controllers/HomeController.php' => 'Home controller',
    'app/Models/Profile.php' => 'Profile model',
    'app/Views/home/index.php' => 'Home view',
    'routes/web.php' => 'Web routes',
    'routes/api.php' => 'API routes'
];

foreach ($critical_files as $file => $description) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "<p>✅ <strong>{$file}</strong> - {$description}</p>";
    } else {
        echo "<p class='error'>❌ <strong>{$file}</strong> - {$description} (MISSING)</p>";
    }
}
echo "</div>";

// Step 3: Environment Variables
echo "<div class='section'>";
echo "<h2>3. Environment Variables</h2>";
if (file_exists(__DIR__ . '/.env')) {
    echo "<p class='success'>✅ .env file found</p>";
    $env = parse_ini_file(__DIR__ . '/.env');
    if ($env) {
        echo "<p class='success'>✅ .env file parsed successfully</p>";
        echo "<p>APP_NAME: " . ($env['APP_NAME'] ?? 'Not set') . "</p>";
        echo "<p>APP_DEBUG: " . ($env['APP_DEBUG'] ?? 'Not set') . "</p>";
        echo "<p>DB_HOST: " . ($env['DB_HOST'] ?? 'Not set') . "</p>";
        echo "<p>DB_DATABASE: " . ($env['DB_DATABASE'] ?? 'Not set') . "</p>";
    } else {
        echo "<p class='error'>❌ Failed to parse .env file</p>";
    }
} else {
    echo "<p class='error'>❌ .env file not found</p>";
}
echo "</div>";

// Step 4: Autoloader Test
echo "<div class='section'>";
echo "<h2>4. Autoloader Test</h2>";

spl_autoload_register(function ($class) {
    echo "<p>🔍 Attempting to load class: <strong>{$class}</strong></p>";
    
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        echo "<p class='warning'>⚠️ Class doesn't match App namespace</p>";
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    echo "<p>📁 Looking for file: <code>{$file}</code></p>";
    
    if (file_exists($file)) {
        echo "<p class='success'>✅ File found, requiring...</p>";
        require $file;
        echo "<p class='success'>✅ Class loaded successfully!</p>";
    } else {
        echo "<p class='error'>❌ File not found!</p>";
    }
});

// Test loading core classes
$test_classes = [
    'App\\Core\\Router',
    'App\\Core\\Controller',
    'App\\Core\\Database',
    'App\\Controllers\\HomeController'
];

foreach ($test_classes as $class) {
    echo "<h3>Testing: {$class}</h3>";
    try {
        if (class_exists($class)) {
            echo "<p class='success'>✅ {$class} loaded successfully</p>";
        } else {
            echo "<p class='error'>❌ {$class} failed to load</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error loading {$class}: " . $e->getMessage() . "</p>";
    }
}
echo "</div>";

// Step 5: Configuration Test
echo "<div class='section'>";
echo "<h2>5. Configuration Test</h2>";
try {
    $config = require __DIR__ . '/config/app.php';
    echo "<p class='success'>✅ App configuration loaded</p>";
    echo "<pre>" . print_r($config, true) . "</pre>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Error loading app config: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Step 6: Database Connection Test
echo "<div class='section'>";
echo "<h2>6. Database Connection Test</h2>";
try {
    // Load environment first
    if (file_exists(__DIR__ . '/.env')) {
        $env = parse_ini_file(__DIR__ . '/.env');
        foreach ($env as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
    
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $database = $_ENV['DB_DATABASE'] ?? 'tsu_staff_portal';
    $username = $_ENV['DB_USERNAME'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';

    echo "<p>Attempting connection to: {$username}@{$host}:{$port}/{$database}</p>";

    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<p class='success'>✅ Database connection successful</p>";
    
    // Test if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<p class='success'>✅ Users table exists</p>";
        
        // Count users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "<p>Users in database: {$result['count']}</p>";
    } else {
        echo "<p class='error'>❌ Users table not found - please import database schema</p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Database test error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Step 7: Router Test
echo "<div class='section'>";
echo "<h2>7. Router Test</h2>";
try {
    $router = new App\Core\Router();
    echo "<p class='success'>✅ Router created successfully</p>";
    
    // Test adding a route
    $router->get('/', 'HomeController@index');
    echo "<p class='success'>✅ Route added successfully</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Router test failed: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
echo "</div>";

// Step 8: HomeController Test
echo "<div class='section'>";
echo "<h2>8. HomeController Test</h2>";
try {
    $controller = new App\Controllers\HomeController();
    echo "<p class='success'>✅ HomeController created successfully</p>";
    
    // Test if we can call a method (but don't actually call index to avoid output)
    if (method_exists($controller, 'index')) {
        echo "<p class='success'>✅ HomeController::index method exists</p>";
    } else {
        echo "<p class='error'>❌ HomeController::index method not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ HomeController test failed: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
echo "</div>";

echo "<hr>";
echo "<h2>Debug Complete</h2>";
echo "<p><a href='/'>← Try Homepage Again</a> | <a href='/test_db.php'>Database Test</a></p>";
?>
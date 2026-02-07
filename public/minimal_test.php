<?php
/**
 * Minimal Test - Bypass all complex logic
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Minimal Test - TSU Staff Portal</h1>";

// Test 1: Basic PHP
echo "<h2>✅ PHP is working</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test 2: File access
echo "<h2>File Access Test</h2>";
$test_files = [
    '../app/Core/Router.php',
    '../app/Controllers/HomeController.php',
    '../config/app.php',
    '../.env'
];

foreach ($test_files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "<p>✅ {$file} - Found</p>";
    } else {
        echo "<p>❌ {$file} - Missing</p>";
    }
}

// Test 3: Simple autoloader
echo "<h2>Autoloader Test</h2>";
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
        return true;
    }
    return false;
});

// Test 4: Load environment
echo "<h2>Environment Test</h2>";
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    if ($env) {
        foreach ($env as $key => $value) {
            $_ENV[$key] = $value;
        }
        echo "<p>✅ Environment loaded</p>";
    } else {
        echo "<p>❌ Environment parse failed</p>";
    }
} else {
    echo "<p>❌ .env file not found</p>";
}

// Test 5: Try to load Router
echo "<h2>Router Test</h2>";
try {
    $router = new App\Core\Router();
    echo "<p>✅ Router loaded successfully</p>";
} catch (Exception $e) {
    echo "<p>❌ Router failed: " . $e->getMessage() . "</p>";
}

// Test 6: Try to load HomeController
echo "<h2>Controller Test</h2>";
try {
    $controller = new App\Controllers\HomeController();
    echo "<p>✅ HomeController loaded successfully</p>";
} catch (Exception $e) {
    echo "<p>❌ HomeController failed: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Test 7: Simple HTML output
echo "<h2>Simple Homepage Test</h2>";
?>
<!DOCTYPE html>
<html>
<head>
    <title>TSU Staff Portal - Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-success">
            <h4>✅ Basic Application Structure Working!</h4>
            <p>If you can see this, the basic PHP and file structure is working correctly.</p>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Next Steps</h5>
                    </div>
                    <div class="card-body">
                        <ol>
                            <li>Import database schema</li>
                            <li>Test database connection</li>
                            <li>Fix any remaining issues</li>
                            <li>Test full application</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Debug Links</h5>
                    </div>
                    <div class="card-body">
                        <a href="/debug_detailed.php" class="btn btn-info btn-sm">Detailed Debug</a>
                        <a href="/test_db.php" class="btn btn-warning btn-sm">Database Test</a>
                        <a href="/" class="btn btn-primary btn-sm">Try Homepage</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
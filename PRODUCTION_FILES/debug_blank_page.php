<?php
/**
 * Debug Blank Page Issue
 * Upload this to your public folder if you see a blank page
 * Visit: https://staff.tsuniversity.edu.ng/debug_blank_page.php
 * DELETE after fixing the issue!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debugging Blank Page Issue</h1>";
echo "<p>Server: " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<hr>";

// Test 1: PHP is working
echo "<h2>✅ Test 1: PHP is Working</h2>";
echo "<p>If you see this, PHP is executing correctly.</p>";

// Test 2: Check if index.php exists
echo "<h2>Test 2: Check index.php</h2>";
if (file_exists('index.php')) {
    echo "<p>✅ index.php exists</p>";
    echo "<p>Size: " . filesize('index.php') . " bytes</p>";
} else {
    echo "<p>❌ index.php NOT FOUND!</p>";
    echo "<p>Current directory: " . __DIR__ . "</p>";
}

// Test 3: Check .htaccess
echo "<h2>Test 3: Check .htaccess</h2>";
if (file_exists('.htaccess')) {
    echo "<p>✅ .htaccess exists</p>";
    echo "<pre>" . htmlspecialchars(file_get_contents('.htaccess')) . "</pre>";
} else {
    echo "<p>❌ .htaccess NOT FOUND!</p>";
    echo "<p>This is likely the problem. Upload the .htaccess file!</p>";
}

// Test 4: Check app directory
echo "<h2>Test 4: Check App Directory</h2>";
if (is_dir('../app')) {
    echo "<p>✅ app directory exists</p>";
    $files = scandir('../app');
    echo "<p>Contents: " . implode(', ', array_diff($files, ['.', '..'])) . "</p>";
} else {
    echo "<p>❌ app directory NOT FOUND!</p>";
}

// Test 5: Try to load Router
echo "<h2>Test 5: Try to Load Router</h2>";
try {
    if (file_exists('../app/Core/Router.php')) {
        echo "<p>✅ Router.php exists</p>";
        require_once '../app/Core/Router.php';
        echo "<p>✅ Router loaded successfully</p>";
    } else {
        echo "<p>❌ Router.php NOT FOUND!</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error loading Router: " . $e->getMessage() . "</p>";
}

// Test 6: Database connection
echo "<h2>Test 6: Database Connection</h2>";
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=tsuniity_tsu_staff_portal',
        'tsuniity',
        '?8I;#ETBGvrU'
    );
    echo "<p>✅ Database connected!</p>";
} catch (PDOException $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

// Test 7: URL Helper
echo "<h2>Test 7: URL Helper</h2>";
if (file_exists('../app/Helpers/UrlHelper.php')) {
    echo "<p>✅ UrlHelper.php exists</p>";
    require_once '../app/Helpers/UrlHelper.php';
    echo "<p>Base URL: " . getBaseUrl() . "</p>";
    echo "<p>Test URL: " . url('login') . "</p>";
} else {
    echo "<p>❌ UrlHelper.php NOT FOUND!</p>";
}

// Test 8: Try to access homepage
echo "<h2>Test 8: Try Homepage</h2>";
echo "<p><a href='/'>Click here to try homepage</a></p>";

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p>If all tests pass but homepage is blank:</p>";
echo "<ol>";
echo "<li>Check server error logs</li>";
echo "<li>Add error_reporting to top of index.php</li>";
echo "<li>Verify document root points to public folder</li>";
echo "<li>Check file permissions</li>";
echo "</ol>";

echo "<p><strong>⚠️ DELETE THIS FILE after fixing!</strong></p>";

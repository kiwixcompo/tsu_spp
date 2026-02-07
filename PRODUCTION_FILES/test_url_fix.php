<?php
/**
 * Simple URL Test - Upload to public folder
 * Visit: https://staff.tsuniversity.edu.ng/test_url_fix.php
 * DELETE after testing!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>URL Fix Test</h1>";
echo "<p><strong>Server:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<hr>";

// Test 1: Check if UrlHelper file exists
echo "<h2>Test 1: UrlHelper File</h2>";
$helperPath = '../app/Helpers/UrlHelper.php';
if (file_exists($helperPath)) {
    echo "<p>✅ UrlHelper.php exists</p>";
    
    // Check file modification time
    $modTime = filemtime($helperPath);
    echo "<p><strong>Last modified:</strong> " . date('Y-m-d H:i:s', $modTime) . "</p>";
    
    // Check if file contains the fix
    $content = file_get_contents($helperPath);
    $hasFix = strpos($content, "str_replace('/tsu_spp'") !== false;
    
    if ($hasFix) {
        echo "<p style='color: green; font-weight: bold;'>✅ File contains the tsu_spp removal fix!</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ File does NOT contain the fix! You need to upload the updated file.</p>";
    }
} else {
    echo "<p style='color: red;'>❌ UrlHelper.php not found!</p>";
}

echo "<hr>";

// Test 2: Load and test the helper
echo "<h2>Test 2: URL Generation</h2>";
if (file_exists($helperPath)) {
    require_once $helperPath;
    
    $baseUrl = getBaseUrl();
    $loginUrl = url('login');
    $registerUrl = url('register');
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Function</th><th>Result</th><th>Status</th></tr>";
    
    // Check base URL
    $baseHasTsuSpp = strpos($baseUrl, 'tsu_spp') !== false;
    echo "<tr>";
    echo "<td><code>getBaseUrl()</code></td>";
    echo "<td><code>" . htmlspecialchars($baseUrl) . "</code></td>";
    echo "<td style='color: " . ($baseHasTsuSpp ? 'red' : 'green') . "; font-weight: bold;'>";
    echo $baseHasTsuSpp ? "❌ Contains tsu_spp" : "✅ Clean";
    echo "</td>";
    echo "</tr>";
    
    // Check login URL
    $loginHasTsuSpp = strpos($loginUrl, 'tsu_spp') !== false;
    echo "<tr>";
    echo "<td><code>url('login')</code></td>";
    echo "<td><code>" . htmlspecialchars($loginUrl) . "</code></td>";
    echo "<td style='color: " . ($loginHasTsuSpp ? 'red' : 'green') . "; font-weight: bold;'>";
    echo $loginHasTsuSpp ? "❌ Contains tsu_spp" : "✅ Clean";
    echo "</td>";
    echo "</tr>";
    
    // Check register URL
    $registerHasTsuSpp = strpos($registerUrl, 'tsu_spp') !== false;
    echo "<tr>";
    echo "<td><code>url('register')</code></td>";
    echo "<td><code>" . htmlspecialchars($registerUrl) . "</code></td>";
    echo "<td style='color: " . ($registerHasTsuSpp ? 'red' : 'green') . "; font-weight: bold;'>";
    echo $registerHasTsuSpp ? "❌ Contains tsu_spp" : "✅ Clean";
    echo "</td>";
    echo "</tr>";
    
    echo "</table>";
}

echo "<hr>";

// Test 3: Server path info
echo "<h2>Test 3: Server Path Information</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>Variable</th><th>Value</th></tr>";
echo "<tr><td>SCRIPT_NAME</td><td><code>" . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "</code></td></tr>";
echo "<tr><td>DOCUMENT_ROOT</td><td><code>" . ($_SERVER['DOCUMENT_ROOT'] ?? 'NOT SET') . "</code></td></tr>";
echo "<tr><td>PHP_SELF</td><td><code>" . ($_SERVER['PHP_SELF'] ?? 'NOT SET') . "</code></td></tr>";
echo "</table>";

echo "<hr>";

// Final verdict
echo "<h2>Verdict</h2>";
if (file_exists($helperPath)) {
    $content = file_get_contents($helperPath);
    $hasFix = strpos($content, "str_replace('/tsu_spp'") !== false;
    
    if ($hasFix) {
        $baseUrl = getBaseUrl();
        $hasTsuSpp = strpos($baseUrl, 'tsu_spp') !== false;
        
        if ($hasTsuSpp) {
            echo "<div style='background: #fee; padding: 20px; border-left: 4px solid red;'>";
            echo "<h3 style='color: red;'>❌ URLs Still Have tsu_spp</h3>";
            echo "<p><strong>Problem:</strong> The fix is in the file, but URLs still contain tsu_spp.</p>";
            echo "<p><strong>This means:</strong> Your files are in a folder structure that includes tsu_spp in the path.</p>";
            echo "<p><strong>Solution:</strong> Move your files out of the tsu_spp folder to the root, OR update your document root.</p>";
            echo "</div>";
        } else {
            echo "<div style='background: #d1fae5; padding: 20px; border-left: 4px solid green;'>";
            echo "<h3 style='color: green;'>✅ SUCCESS! URLs are Clean!</h3>";
            echo "<p>The fix is working correctly. All URLs are clean without tsu_spp.</p>";
            echo "<p><strong>Delete this test file now for security.</strong></p>";
            echo "</div>";
        }
    } else {
        echo "<div style='background: #fff3cd; padding: 20px; border-left: 4px solid orange;'>";
        echo "<h3 style='color: orange;'>⚠️ File Not Updated</h3>";
        echo "<p>The UrlHelper.php file on your server does NOT contain the fix.</p>";
        echo "<p><strong>Action Required:</strong> Upload the updated app/Helpers/UrlHelper.php file to your server.</p>";
        echo "</div>";
    }
} else {
    echo "<div style='background: #fee; padding: 20px; border-left: 4px solid red;'>";
    echo "<h3 style='color: red;'>❌ UrlHelper.php Not Found</h3>";
    echo "<p>The file is missing from your server.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><strong>⚠️ DELETE THIS FILE (test_url_fix.php) after testing!</strong></p>";
?>

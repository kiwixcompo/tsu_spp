<?php
/**
 * Find PHPMailer Installation
 * Locates PHPMailer files on the server
 */

echo "<!DOCTYPE html><html><head><title>Find PHPMailer</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;}";
echo ".success{color:green;background:#d4edda;padding:10px;border-radius:5px;margin:5px 0;}";
echo ".error{color:red;background:#f8d7da;padding:10px;border-radius:5px;margin:5px 0;}";
echo ".info{color:#004085;background:#cce5ff;padding:10px;border-radius:5px;margin:5px 0;}";
echo "pre{background:#f8f9fa;padding:10px;border-radius:5px;overflow-x:auto;}";
echo "</style></head><body>";

echo "<h1>🔍 Find PHPMailer Installation</h1>";

// Check possible locations
$possiblePaths = [
    'vendor/phpmailer/phpmailer/src/PHPMailer.php',
    'vendor/phpmailer/PHPMailer/src/PHPMailer.php',
    'vendor/PHPMailer/PHPMailer/src/PHPMailer.php',
    '../vendor/phpmailer/phpmailer/src/PHPMailer.php',
    '../vendor/phpmailer/PHPMailer/src/PHPMailer.php',
];

echo "<h2>Checking Possible Locations:</h2>";
$found = false;
$foundPath = '';

foreach ($possiblePaths as $path) {
    $fullPath = __DIR__ . '/' . $path;
    $exists = file_exists($fullPath);
    
    echo "<div class='" . ($exists ? 'success' : 'error') . "'>";
    echo ($exists ? '✓' : '✗') . " $path";
    echo "</div>";
    
    if ($exists && !$found) {
        $found = true;
        $foundPath = $path;
    }
}

if ($found) {
    echo "<div class='success'><h3>✓ PHPMailer Found!</h3>";
    echo "<p><strong>Location:</strong> $foundPath</p></div>";
} else {
    echo "<div class='error'><h3>✗ PHPMailer Not Found</h3>";
    echo "<p>PHPMailer is not installed on the server.</p></div>";
}

// Check vendor directory
echo "<h2>Vendor Directory Contents:</h2>";
$vendorDir = __DIR__ . '/vendor';

if (is_dir($vendorDir)) {
    echo "<div class='info'>Vendor directory exists</div>";
    echo "<pre>";
    
    // List vendor contents
    $items = scandir($vendorDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        echo "$item\n";
        
        // If it's a directory, list its contents
        $subDir = $vendorDir . '/' . $item;
        if (is_dir($subDir)) {
            $subItems = scandir($subDir);
            foreach ($subItems as $subItem) {
                if ($subItem === '.' || $subItem === '..') continue;
                echo "  └─ $subItem\n";
            }
        }
    }
    echo "</pre>";
} else {
    echo "<div class='error'>Vendor directory does not exist at: $vendorDir</div>";
    echo "<div class='info'>";
    echo "<h3>Solution:</h3>";
    echo "<p>You need to install Composer dependencies. Options:</p>";
    echo "<ol>";
    echo "<li><strong>Via SSH:</strong> Run <code>composer install</code> in your project root</li>";
    echo "<li><strong>Via cPanel Terminal:</strong> Navigate to your project and run <code>composer install</code></li>";
    echo "<li><strong>Upload vendor folder:</strong> Run <code>composer install</code> locally and upload the entire vendor folder</li>";
    echo "</ol>";
    echo "</div>";
}

// Check composer.json
echo "<h2>Composer Configuration:</h2>";
$composerFile = __DIR__ . '/composer.json';
if (file_exists($composerFile)) {
    echo "<div class='success'>composer.json exists</div>";
    echo "<pre>" . htmlspecialchars(file_get_contents($composerFile)) . "</pre>";
} else {
    echo "<div class='error'>composer.json not found</div>";
}

// Check if composer is available
echo "<h2>Composer Availability:</h2>";
$composerCheck = shell_exec('which composer 2>&1');
if ($composerCheck) {
    echo "<div class='success'>Composer is available: " . htmlspecialchars(trim($composerCheck)) . "</div>";
    
    // Try to get composer version
    $composerVersion = shell_exec('composer --version 2>&1');
    if ($composerVersion) {
        echo "<div class='info'>Version: " . htmlspecialchars(trim($composerVersion)) . "</div>";
    }
} else {
    echo "<div class='error'>Composer command not found on server</div>";
}

echo "<div class='error' style='margin-top:30px;'>";
echo "<h3>⚠️ Delete this file after checking!</h3>";
echo "</div>";

echo "</body></html>";

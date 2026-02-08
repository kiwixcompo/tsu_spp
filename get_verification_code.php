<?php
/**
 * Verification Code Retrieval
 * Temporary solution while email is being configured
 * Access: https://staff.tsuniversity.edu.ng/get_verification_code.php?email=your@email.com
 */

// Load environment variables
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || $line[0] === '#') continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (strlen($value) > 0) {
                if (($value[0] === '"' && substr($value, -1) === '"') || 
                    ($value[0] === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
            }
            $_ENV[$name] = $value;
        }
    }
}

// Only allow in debug mode
if (($_ENV['APP_DEBUG'] ?? 'false') !== 'true') {
    die('This feature is only available in debug mode');
}

$email = $_GET['email'] ?? '';

echo "<!DOCTYPE html><html><head><title>Get Verification Code</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:600px;margin:50px auto;padding:20px;}";
echo ".code{font-size:32px;font-weight:bold;color:#1e40af;text-align:center;background:#f0f0f0;padding:20px;border-radius:8px;margin:20px 0;letter-spacing:4px;}";
echo ".info{color:#004085;background:#cce5ff;padding:15px;border-radius:5px;margin:10px 0;}";
echo ".error{color:red;background:#f8d7da;padding:15px;border-radius:5px;margin:10px 0;}";
echo "</style></head><body>";

echo "<h1>🔐 Get Verification Code</h1>";

if (empty($email)) {
    echo "<div class='info'>";
    echo "<p>Enter your email address in the URL:</p>";
    echo "<p><code>get_verification_code.php?email=your@email.com</code></p>";
    echo "</div>";
} else {
    $codesFile = __DIR__ . '/public/verification_codes.txt';
    
    if (!file_exists($codesFile)) {
        echo "<div class='error'>No verification codes found. Please register first.</div>";
    } else {
        $codes = file($codesFile);
        $found = false;
        
        // Search for the most recent code for this email
        for ($i = count($codes) - 1; $i >= 0; $i--) {
            $line = $codes[$i];
            if (stripos($line, $email) !== false) {
                $parts = explode('|', $line);
                if (count($parts) === 3) {
                    $timestamp = trim($parts[0]);
                    $code = trim($parts[2]);
                    
                    echo "<div class='info'>";
                    echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
                    echo "<p><strong>Generated:</strong> $timestamp</p>";
                    echo "</div>";
                    
                    echo "<div class='code'>$code</div>";
                    
                    echo "<div class='info'>";
                    echo "<p>Copy this code and paste it in the verification page.</p>";
                    echo "</div>";
                    
                    $found = true;
                    break;
                }
            }
        }
        
        if (!$found) {
            echo "<div class='error'>No verification code found for: " . htmlspecialchars($email) . "</div>";
            echo "<div class='info'>Please make sure you've registered with this email address.</div>";
        }
    }
}

echo "</body></html>";

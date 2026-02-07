<?php
/**
 * Email Configuration Test
 * Tests email sending and shows diagnostic information
 * Access: https://staff.tsuniversity.edu.ng/test_email.php
 * 
 * IMPORTANT: Delete this file after testing!
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

echo "<!DOCTYPE html><html><head><title>Email Test</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:900px;margin:50px auto;padding:20px;}";
echo ".success{color:green;background:#d4edda;padding:10px;border-radius:5px;margin:5px 0;}";
echo ".error{color:red;background:#f8d7da;padding:10px;border-radius:5px;margin:5px 0;}";
echo ".info{color:#004085;background:#cce5ff;padding:10px;border-radius:5px;margin:5px 0;}";
echo ".warning{color:#856404;background:#fff3cd;padding:10px;border-radius:5px;margin:5px 0;}";
echo "table{width:100%;border-collapse:collapse;margin:20px 0;}";
echo "th,td{border:1px solid #ddd;padding:12px;text-align:left;}";
echo "th{background:#f8f9fa;}</style></head><body>";

echo "<h1>📧 Email Configuration Test</h1>";

// Display current configuration
echo "<h2>Current Email Configuration</h2>";
echo "<table>";
echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";

$mailHost = $_ENV['MAIL_HOST'] ?? 'not set';
$mailPort = $_ENV['MAIL_PORT'] ?? 'not set';
$mailUsername = $_ENV['MAIL_USERNAME'] ?? 'not set';
$mailPassword = $_ENV['MAIL_PASSWORD'] ?? 'not set';
$mailEncryption = $_ENV['MAIL_ENCRYPTION'] ?? 'not set';
$mailFrom = $_ENV['MAIL_FROM_ADDRESS'] ?? 'not set';

echo "<tr><td>MAIL_HOST</td><td>$mailHost</td><td>" . ($mailHost !== 'not set' ? '✓' : '✗') . "</td></tr>";
echo "<tr><td>MAIL_PORT</td><td>$mailPort</td><td>" . ($mailPort !== 'not set' ? '✓' : '✗') . "</td></tr>";
echo "<tr><td>MAIL_USERNAME</td><td>$mailUsername</td><td>" . ($mailUsername !== 'not set' ? '✓' : '✗') . "</td></tr>";
echo "<tr><td>MAIL_PASSWORD</td><td>" . ($mailPassword !== 'not set' ? '***SET***' : 'not set') . "</td><td>" . ($mailPassword !== 'not set' ? '✓' : '✗') . "</td></tr>";
echo "<tr><td>MAIL_ENCRYPTION</td><td>$mailEncryption</td><td>" . ($mailEncryption !== 'not set' ? '✓' : '✗') . "</td></tr>";
echo "<tr><td>MAIL_FROM_ADDRESS</td><td>$mailFrom</td><td>" . ($mailFrom !== 'not set' ? '✓' : '✗') . "</td></tr>";
echo "</table>";

// Test email sending
if (isset($_POST['test_email'])) {
    $testEmail = $_POST['email'] ?? '';
    
    if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='error'>Please enter a valid email address</div>";
    } else {
        echo "<h2>Sending Test Email...</h2>";
        
        $subject = "TSU Staff Portal - Email Test";
        $message = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>Email Test Successful!</h2>
            <p>This is a test email from the TSU Staff Portal.</p>
            <p><strong>Sent at:</strong> " . date('Y-m-d H:i:s') . "</p>
            <p><strong>Server:</strong> " . $_SERVER['SERVER_NAME'] . "</p>
            <p>If you received this email, your email configuration is working correctly.</p>
        </body>
        </html>";
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . $mailFrom . "\r\n";
        
        $result = @mail($testEmail, $subject, $message, $headers);
        
        if ($result) {
            echo "<div class='success'>✓ Test email sent successfully to: $testEmail</div>";
            echo "<div class='info'>Check your inbox (and spam folder) for the test email.</div>";
        } else {
            echo "<div class='error'>✗ Failed to send test email</div>";
            $lastError = error_get_last();
            if ($lastError) {
                echo "<div class='error'>Error: " . htmlspecialchars($lastError['message']) . "</div>";
            }
        }
    }
}

// Test form
echo "<h2>Send Test Email</h2>";
echo "<form method='POST'>";
echo "<p><input type='email' name='email' placeholder='Enter your email address' style='width:300px;padding:10px;' required></p>";
echo "<p><button type='submit' name='test_email' style='padding:10px 20px;background:#1e40af;color:white;border:none;border-radius:5px;cursor:pointer;'>Send Test Email</button></p>";
echo "</form>";

// Check verification codes file
echo "<h2>Recent Verification Codes</h2>";
$codesFile = __DIR__ . '/public/verification_codes.txt';
if (file_exists($codesFile)) {
    $codes = file($codesFile);
    $recentCodes = array_slice(array_reverse($codes), 0, 10);
    if (!empty($recentCodes)) {
        echo "<table>";
        echo "<tr><th>Date/Time</th><th>Email</th><th>Code</th></tr>";
        foreach ($recentCodes as $line) {
            $parts = explode('|', $line);
            if (count($parts) === 3) {
                echo "<tr><td>" . htmlspecialchars(trim($parts[0])) . "</td>";
                echo "<td>" . htmlspecialchars(trim($parts[1])) . "</td>";
                echo "<td><strong>" . htmlspecialchars(trim($parts[2])) . "</strong></td></tr>";
            }
        }
        echo "</table>";
    } else {
        echo "<div class='info'>No verification codes found</div>";
    }
} else {
    echo "<div class='info'>Verification codes file not found</div>";
}

echo "<div class='warning'>";
echo "<h3>⚠️ SECURITY WARNING</h3>";
echo "<p><strong>Delete this file (test_email.php) immediately after testing!</strong></p>";
echo "</div>";

echo "</body></html>";

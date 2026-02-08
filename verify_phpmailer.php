<?php
/**
 * PHPMailer Installation and Configuration Verification
 * Tests PHPMailer setup with Google Workspace SMTP
 * Access: https://staff.tsuniversity.edu.ng/verify_phpmailer.php
 * 
 * IMPORTANT: Delete this file after verification!
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

echo "<!DOCTYPE html><html><head><title>PHPMailer Verification</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:900px;margin:50px auto;padding:20px;}";
echo ".success{color:green;background:#d4edda;padding:10px;border-radius:5px;margin:5px 0;}";
echo ".error{color:red;background:#f8d7da;padding:10px;border-radius:5px;margin:5px 0;}";
echo ".info{color:#004085;background:#cce5ff;padding:10px;border-radius:5px;margin:5px 0;}";
echo ".warning{color:#856404;background:#fff3cd;padding:10px;border-radius:5px;margin:5px 0;}";
echo "table{width:100%;border-collapse:collapse;margin:20px 0;}";
echo "th,td{border:1px solid #ddd;padding:12px;text-align:left;}";
echo "th{background:#f8f9fa;font-weight:bold;}";
echo "pre{background:#f8f9fa;padding:10px;border-radius:5px;overflow-x:auto;}";
echo "</style></head><body>";

echo "<h1>📧 PHPMailer Installation & Configuration Verification</h1>";

// Check 1: PHPMailer Files
echo "<h2>1. PHPMailer Installation Check</h2>";

$phpmailerPaths = [
    'vendor/phpmailer/PHPMailer/src/PHPMailer.php',
    'vendor/phpmailer/PHPMailer/src/SMTP.php',
    'vendor/phpmailer/PHPMailer/src/Exception.php',
];

$allFilesExist = true;
echo "<table>";
echo "<tr><th>File</th><th>Status</th></tr>";
foreach ($phpmailerPaths as $path) {
    $exists = file_exists(__DIR__ . '/' . $path);
    $allFilesExist = $allFilesExist && $exists;
    echo "<tr><td>$path</td><td>" . ($exists ? '<span class="success">✓ Found</span>' : '<span class="error">✗ Missing</span>') . "</td></tr>";
}
echo "</table>";

if ($allFilesExist) {
    echo "<div class='success'>✓ All PHPMailer files are present</div>";
} else {
    echo "<div class='error'>✗ Some PHPMailer files are missing. Run: composer install</div>";
}

// Check 2: Load PHPMailer
echo "<h2>2. PHPMailer Class Loading</h2>";

try {
    require_once __DIR__ . '/vendor/phpmailer/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/vendor/phpmailer/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/vendor/phpmailer/PHPMailer/src/SMTP.php';
    
    echo "<div class='success'>✓ PHPMailer classes loaded successfully</div>";
    
    // Check if classes are available
    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        echo "<div class='success'>✓ PHPMailer\\PHPMailer\\PHPMailer class is available</div>";
    } else {
        echo "<div class='error'>✗ PHPMailer class not found</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>✗ Error loading PHPMailer: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Check 3: Google Workspace SMTP Configuration
echo "<h2>3. Google Workspace SMTP Configuration</h2>";

$smtpConfig = [
    'MAIL_HOST' => $_ENV['MAIL_HOST'] ?? 'not set',
    'MAIL_PORT' => $_ENV['MAIL_PORT'] ?? 'not set',
    'MAIL_USERNAME' => $_ENV['MAIL_USERNAME'] ?? 'not set',
    'MAIL_PASSWORD' => isset($_ENV['MAIL_PASSWORD']) && !empty($_ENV['MAIL_PASSWORD']) ? '***SET*** (length: ' . strlen($_ENV['MAIL_PASSWORD']) . ')' : 'not set',
    'MAIL_ENCRYPTION' => $_ENV['MAIL_ENCRYPTION'] ?? 'not set',
    'MAIL_FROM_ADDRESS' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'not set',
];

echo "<table>";
echo "<tr><th>Setting</th><th>Value</th><th>Expected</th><th>Status</th></tr>";

$checks = [
    'MAIL_HOST' => ['value' => $smtpConfig['MAIL_HOST'], 'expected' => 'smtp.gmail.com'],
    'MAIL_PORT' => ['value' => $smtpConfig['MAIL_PORT'], 'expected' => '587'],
    'MAIL_ENCRYPTION' => ['value' => $smtpConfig['MAIL_ENCRYPTION'], 'expected' => 'tls'],
    'MAIL_USERNAME' => ['value' => $smtpConfig['MAIL_USERNAME'], 'expected' => 'your@tsuniversity.edu.ng'],
    'MAIL_PASSWORD' => ['value' => $smtpConfig['MAIL_PASSWORD'], 'expected' => '16-char app password'],
];

foreach ($checks as $key => $check) {
    $isCorrect = ($key === 'MAIL_USERNAME' || $key === 'MAIL_PASSWORD') ? 
        ($check['value'] !== 'not set') : 
        ($check['value'] === $check['expected']);
    
    $status = $isCorrect ? '<span class="success">✓</span>' : '<span class="error">✗</span>';
    echo "<tr><td>$key</td><td>{$check['value']}</td><td>{$check['expected']}</td><td>$status</td></tr>";
}
echo "</table>";

// Check 4: Test Email Sending
if (isset($_POST['send_test'])) {
    echo "<h2>4. Test Email Sending</h2>";
    
    $testEmail = $_POST['test_email'] ?? '';
    
    if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='error'>Please enter a valid email address</div>";
    } else {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            // Enable verbose debug output
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = function($str, $level) {
                echo "<div class='info'><small>[$level] " . htmlspecialchars($str) . "</small></div>";
            };
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
            $mail->Port = $_ENV['MAIL_PORT'];
            
            // Recipients
            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], 'TSU Staff Portal');
            $mail->addAddress($testEmail);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'PHPMailer Test - TSU Staff Portal';
            $mail->Body = '
                <html>
                <body style="font-family: Arial, sans-serif;">
                    <h2>✓ PHPMailer Test Successful!</h2>
                    <p>This email was sent using PHPMailer with Google Workspace SMTP.</p>
                    <p><strong>Sent at:</strong> ' . date('Y-m-d H:i:s') . '</p>
                    <p><strong>Configuration:</strong></p>
                    <ul>
                        <li>SMTP Host: ' . $_ENV['MAIL_HOST'] . '</li>
                        <li>SMTP Port: ' . $_ENV['MAIL_PORT'] . '</li>
                        <li>Encryption: ' . $_ENV['MAIL_ENCRYPTION'] . '</li>
                    </ul>
                    <p>Your email system is working correctly!</p>
                </body>
                </html>
            ';
            $mail->AltBody = 'PHPMailer test successful! Sent at: ' . date('Y-m-d H:i:s');
            
            $mail->send();
            
            echo "<div class='success'><h3>✓ Email Sent Successfully!</h3>";
            echo "<p>Test email sent to: <strong>$testEmail</strong></p>";
            echo "<p>Check your inbox (and spam folder) for the test email.</p></div>";
            
        } catch (Exception $e) {
            echo "<div class='error'><h3>✗ Email Sending Failed</h3>";
            echo "<p><strong>Error:</strong> " . htmlspecialchars($mail->ErrorInfo) . "</p>";
            echo "<p><strong>Exception:</strong> " . htmlspecialchars($e->getMessage()) . "</p></div>";
            
            echo "<div class='warning'><h4>Common Issues:</h4><ul>";
            echo "<li>App Password incorrect or not generated</li>";
            echo "<li>2-Step Verification not enabled on Google Workspace account</li>";
            echo "<li>Server firewall blocking outgoing SMTP connections</li>";
            echo "<li>Wrong email address or password in .env file</li>";
            echo "</ul></div>";
        }
    }
}

// Test form
echo "<h2>Send Test Email</h2>";
echo "<form method='POST'>";
echo "<div style='margin:20px 0;'>";
echo "<input type='email' name='test_email' placeholder='Enter recipient email' style='width:300px;padding:10px;border:1px solid #ddd;border-radius:5px;' required>";
echo "<button type='submit' name='send_test' style='padding:10px 20px;background:#1e40af;color:white;border:none;border-radius:5px;cursor:pointer;margin-left:10px;'>Send Test Email</button>";
echo "</div>";
echo "</form>";

// Instructions
echo "<div class='info'>";
echo "<h3>📝 Setup Instructions</h3>";
echo "<ol>";
echo "<li><strong>Enable 2-Step Verification:</strong> Go to <a href='https://myaccount.google.com/security' target='_blank'>myaccount.google.com/security</a></li>";
echo "<li><strong>Generate App Password:</strong> Go to <a href='https://myaccount.google.com/apppasswords' target='_blank'>myaccount.google.com/apppasswords</a></li>";
echo "<li>Select 'Mail' and 'Other (Custom name)', enter 'TSU Staff Portal'</li>";
echo "<li>Copy the 16-character password (no spaces)</li>";
echo "<li>Update MAIL_PASSWORD in your .env file with this app password</li>";
echo "<li>Make sure MAIL_HOST=smtp.gmail.com, MAIL_PORT=587, MAIL_ENCRYPTION=tls</li>";
echo "</ol>";
echo "</div>";

echo "<div class='warning'>";
echo "<h3>⚠️ SECURITY WARNING</h3>";
echo "<p><strong>Delete this file (verify_phpmailer.php) immediately after verification!</strong></p>";
echo "</div>";

echo "</body></html>";

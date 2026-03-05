<?php
/**
 * Debug Forgot Password Issue
 * 
 * This script helps diagnose why password reset emails aren't being sent
 */

// Start session
session_start();

// Load environment
require_once __DIR__ . '/vendor/autoload.php';

// Load .env
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        $_ENV[$name] = $value;
        putenv("$name=$value");
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f5f5f5; }
        .debug-card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug Forgot Password</h1>
        
        <?php
        $testEmail = 'social@tsuniversity.edu.ng';
        
        echo '<div class="debug-card">';
        echo '<h3>1. Check User Exists</h3>';
        
        try {
            require_once __DIR__ . '/app/Core/Database.php';
            require_once __DIR__ . '/app/Models/User.php';
            
            $db = \App\Core\Database::getInstance();
            $userModel = new \App\Models\User();
            
            $user = $userModel->findByEmail($testEmail);
            
            if ($user) {
                echo '<p class="success">✓ User found: ' . htmlspecialchars($testEmail) . '</p>';
                echo '<pre>';
                echo 'ID: ' . $user['id'] . "\n";
                echo 'Email: ' . $user['email'] . "\n";
                echo 'Email Verified: ' . ($user['email_verified'] ? 'Yes' : 'No') . "\n";
                echo 'Account Status: ' . $user['account_status'] . "\n";
                echo '</pre>';
                
                if (!$user['email_verified']) {
                    echo '<p class="warning">⚠ Email is NOT verified - password reset will not be sent!</p>';
                }
            } else {
                echo '<p class="error">✗ User NOT found: ' . htmlspecialchars($testEmail) . '</p>';
            }
        } catch (Exception $e) {
            echo '<p class="error">✗ Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        echo '</div>';
        
        echo '<div class="debug-card">';
        echo '<h3>2. Check Email Helper</h3>';
        
        try {
            require_once __DIR__ . '/app/Helpers/EmailHelper.php';
            $emailHelper = new \App\Helpers\EmailHelper();
            echo '<p class="success">✓ EmailHelper loaded successfully</p>';
            
            // Check if PHPMailer is available
            if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                echo '<p class="success">✓ PHPMailer is available</p>';
            } else {
                echo '<p class="warning">⚠ PHPMailer not available - using PHP mail()</p>';
            }
        } catch (Exception $e) {
            echo '<p class="error">✗ Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        echo '</div>';
        
        echo '<div class="debug-card">';
        echo '<h3>3. Check File Permissions</h3>';
        
        $publicDir = __DIR__ . '/public';
        $storageDir = __DIR__ . '/storage/emails';
        
        // Check public directory
        if (is_dir($publicDir)) {
            if (is_writable($publicDir)) {
                echo '<p class="success">✓ public/ directory is writable</p>';
            } else {
                echo '<p class="error">✗ public/ directory is NOT writable</p>';
            }
        } else {
            echo '<p class="error">✗ public/ directory does not exist</p>';
        }
        
        // Check storage/emails directory
        if (is_dir($storageDir)) {
            if (is_writable($storageDir)) {
                echo '<p class="success">✓ storage/emails/ directory is writable</p>';
            } else {
                echo '<p class="error">✗ storage/emails/ directory is NOT writable</p>';
            }
        } else {
            echo '<p class="warning">⚠ storage/emails/ directory does not exist - will be created</p>';
            if (mkdir($storageDir, 0755, true)) {
                echo '<p class="success">✓ Created storage/emails/ directory</p>';
            } else {
                echo '<p class="error">✗ Failed to create storage/emails/ directory</p>';
            }
        }
        echo '</div>';
        
        echo '<div class="debug-card">';
        echo '<h3>4. Check Error Logs</h3>';
        
        $errorLog = __DIR__ . '/error.log';
        if (file_exists($errorLog)) {
            $lines = file($errorLog);
            $recentLines = array_slice($lines, -20);
            
            echo '<p>Last 20 lines from error.log:</p>';
            echo '<pre style="max-height: 300px; overflow-y: auto;">';
            foreach ($recentLines as $line) {
                if (stripos($line, 'password') !== false || stripos($line, 'reset') !== false || stripos($line, 'email') !== false) {
                    echo '<strong>' . htmlspecialchars($line) . '</strong>';
                } else {
                    echo htmlspecialchars($line);
                }
            }
            echo '</pre>';
        } else {
            echo '<p class="warning">⚠ error.log file not found</p>';
        }
        echo '</div>';
        
        echo '<div class="debug-card">';
        echo '<h3>5. Test Email Sending</h3>';
        
        if (isset($_POST['test_email'])) {
            try {
                require_once __DIR__ . '/app/Helpers/EmailHelper.php';
                $emailHelper = new \App\Helpers\EmailHelper();
                
                $testToken = bin2hex(random_bytes(32));
                echo '<p>Attempting to send test email to: ' . htmlspecialchars($testEmail) . '</p>';
                echo '<p>Test token: ' . htmlspecialchars($testToken) . '</p>';
                
                $result = $emailHelper->sendPasswordResetEmail($testEmail, $testToken);
                
                if ($result) {
                    echo '<p class="success">✓ Email sent successfully!</p>';
                } else {
                    echo '<p class="error">✗ Email sending failed</p>';
                }
                
                // Check if files were created
                $resetLinksFile = __DIR__ . '/public/reset_links.txt';
                if (file_exists($resetLinksFile)) {
                    $content = file_get_contents($resetLinksFile);
                    $lines = explode("\n", $content);
                    $lastLine = end($lines);
                    if (strpos($lastLine, $testEmail) !== false) {
                        echo '<p class="success">✓ Reset link saved to public/reset_links.txt</p>';
                        echo '<pre>' . htmlspecialchars($lastLine) . '</pre>';
                    }
                }
                
                // Check storage/emails
                $emailsDir = __DIR__ . '/storage/emails';
                if (is_dir($emailsDir)) {
                    $files = glob($emailsDir . '/*.html');
                    rsort($files);
                    if (!empty($files)) {
                        $latestFile = $files[0];
                        echo '<p class="success">✓ Email saved to storage/emails/</p>';
                        echo '<p>Latest file: ' . basename($latestFile) . '</p>';
                    }
                }
                
            } catch (Exception $e) {
                echo '<p class="error">✗ Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            }
        } else {
            echo '<form method="POST">';
            echo '<button type="submit" name="test_email" class="btn btn-primary">Send Test Email</button>';
            echo '</form>';
        }
        echo '</div>';
        
        echo '<div class="debug-card">';
        echo '<h3>6. Recommendations</h3>';
        echo '<ul>';
        echo '<li>Check that the user email is verified (email_verified = 1)</li>';
        echo '<li>Check error.log for detailed error messages</li>';
        echo '<li>Verify public/ and storage/emails/ directories are writable</li>';
        echo '<li>Check SMTP configuration in .env file</li>';
        echo '<li>Try the "Send Test Email" button above</li>';
        echo '</ul>';
        echo '</div>';
        ?>
    </div>
</body>
</html>

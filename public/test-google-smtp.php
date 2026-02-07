<?php
/**
 * Google Workspace SMTP Test
 * 
 * This file tests email sending through Google Workspace SMTP
 * Upload to your server and visit: https://staff.tsuniversity.edu.ng/test-google-smtp.php
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment variables
$envFile = __DIR__ . '/../.env';
$envLoaded = false;
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value, '"\'');
    }
    $envLoaded = true;
}

// Try to load PHPMailer
$phpmailerAvailable = false;
$phpmailerPath = __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';

if (file_exists($phpmailerPath)) {
    require_once $phpmailerPath;
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    $phpmailerAvailable = true;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Google Workspace SMTP Test - TSU Staff Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1e40af;
            border-bottom: 3px solid #1e40af;
            padding-bottom: 10px;
        }
        .config {
            background: #f9fafb;
            padding: 15px;
            border-left: 4px solid #1e40af;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #17a2b8;
        }
        pre {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #1e40af;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .btn:hover {
            background: #1e3a8a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Google Workspace SMTP Test</h1>
        
        <div class="info">
            <strong>📋 Current Configuration:</strong>
            <pre>Host: <?= $_ENV['MAIL_HOST'] ?? 'Not set' ?>
Port: <?= $_ENV['MAIL_PORT'] ?? 'Not set' ?>
Username: <?= $_ENV['MAIL_USERNAME'] ?? 'Not set' ?>
Password: <?= !empty($_ENV['MAIL_PASSWORD']) ? '****' . substr($_ENV['MAIL_PASSWORD'], -4) : 'Not set' ?>
Encryption: <?= $_ENV['MAIL_ENCRYPTION'] ?? 'Not set' ?>
From: <?= $_ENV['MAIL_FROM_ADDRESS'] ?? 'Not set' ?></pre>
        </div>

        <?php
        // Check if form submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
            $testEmail = filter_var($_POST['test_email'], FILTER_VALIDATE_EMAIL);
            
            if (!$testEmail) {
                echo '<div class="error">❌ Invalid email address provided.</div>';
            } else {
                $mail = new PHPMailer(true);
                
                try {
                    // Server settings
                    $mail->SMTPDebug = 0;  // Disable verbose output for clean display
                    $mail->isSMTP();
                    $mail->Host = $_ENV['MAIL_HOST'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $_ENV['MAIL_USERNAME'];
                    $mail->Password = $_ENV['MAIL_PASSWORD'];
                    $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
                    $mail->Port = $_ENV['MAIL_PORT'];
                    $mail->Timeout = 10;
                    
                    // Recipients
                    $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME'] ?? 'TSU Staff Portal');
                    $mail->addAddress($testEmail);
                    
                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Test Email from TSU Staff Portal - ' . date('Y-m-d H:i:s');
                    $mail->Body = '
                    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                        <div style="background: #1e40af; color: white; padding: 20px; text-align: center;">
                            <h1>✅ Email Test Successful!</h1>
                        </div>
                        <div style="padding: 30px; background: #f9fafb;">
                            <h2>Congratulations!</h2>
                            <p>Your Google Workspace SMTP configuration is working correctly.</p>
                            <p><strong>Test Details:</strong></p>
                            <ul>
                                <li>Sent at: ' . date('Y-m-d H:i:s') . '</li>
                                <li>SMTP Host: ' . $_ENV['MAIL_HOST'] . '</li>
                                <li>SMTP Port: ' . $_ENV['MAIL_PORT'] . '</li>
                                <li>From: ' . $_ENV['MAIL_FROM_ADDRESS'] . '</li>
                                <li>To: ' . htmlspecialchars($testEmail) . '</li>
                            </ul>
                            <p>Your TSU Staff Portal can now send emails reliably!</p>
                        </div>
                        <div style="padding: 20px; text-align: center; color: #666; font-size: 14px;">
                            <p>TSU Staff Profile Portal<br>Taraba State University</p>
                        </div>
                    </div>';
                    $mail->AltBody = 'Email Test Successful! Your Google Workspace SMTP is working correctly.';
                    
                    $mail->send();
                    
                    echo '<div class="success">';
                    echo '<h2>✅ Email Sent Successfully!</h2>';
                    echo '<p><strong>Test email sent to:</strong> ' . htmlspecialchars($testEmail) . '</p>';
                    echo '<p>Check your inbox (and spam folder) for the test email.</p>';
                    echo '<p><strong>Sent at:</strong> ' . date('Y-m-d H:i:s') . '</p>';
                    echo '</div>';
                    
                } catch (Exception $e) {
                    echo '<div class="error">';
                    echo '<h2>❌ Email Sending Failed</h2>';
                    echo '<p><strong>Error:</strong> ' . htmlspecialchars($mail->ErrorInfo) . '</p>';
                    echo '<p><strong>Exception:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '<h3>Troubleshooting Tips:</h3>';
                    echo '<ul>';
                    echo '<li>Verify your Google Workspace App Password is correct</li>';
                    echo '<li>Check that SMTP is enabled in Google Workspace Admin</li>';
                    echo '<li>Ensure 2-Step Verification is enabled for the account</li>';
                    echo '<li>Try using port 465 with SSL instead of 587 with TLS</li>';
                    echo '<li>Check server firewall isn\'t blocking SMTP ports</li>';
                    echo '</ul>';
                    echo '</div>';
                }
            }
        }
        ?>

        <h2>📧 Send Test Email</h2>
        <form method="POST">
            <div style="margin: 20px 0;">
                <label for="test_email" style="display: block; margin-bottom: 5px; font-weight: bold;">
                    Enter your email address to receive a test email:
                </label>
                <input type="email" 
                       id="test_email" 
                       name="test_email" 
                       required 
                       placeholder="your.email@tsuniversity.edu.ng"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">
            </div>
            <button type="submit" class="btn">📨 Send Test Email</button>
        </form>

        <h2>📚 Setup Instructions</h2>
        <div class="config">
            <h3>For Google Workspace SMTP:</h3>
            <p>Update your <code>.env</code> file with these settings:</p>
            <pre>MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@tsuniversity.edu.ng
MAIL_PASSWORD=your_app_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tsuniversity.edu.ng
MAIL_FROM_NAME="TSU Staff Profile Portal"</pre>
            
            <h3>How to get App Password:</h3>
            <ol>
                <li>Go to <a href="https://myaccount.google.com" target="_blank">Google Account</a></li>
                <li>Navigate to Security → 2-Step Verification</li>
                <li>Scroll to "App passwords"</li>
                <li>Generate new app password for "Mail"</li>
                <li>Copy the 16-character password to .env</li>
            </ol>
        </div>

        <h2>🔍 System Check</h2>
        <div class="info">
            <strong>PHPMailer Status:</strong> 
            <?php if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')): ?>
                <span style="color: green;">✅ Installed</span>
            <?php else: ?>
                <span style="color: red;">❌ Not Found</span>
            <?php endif; ?>
            <br>
            
            <strong>PHP Version:</strong> <?= phpversion() ?>
            <br>
            
            <strong>OpenSSL:</strong> 
            <?php if (extension_loaded('openssl')): ?>
                <span style="color: green;">✅ Enabled</span>
            <?php else: ?>
                <span style="color: red;">❌ Disabled</span>
            <?php endif; ?>
            <br>
            
            <strong>SMTP Functions:</strong>
            <?php if (function_exists('fsockopen')): ?>
                <span style="color: green;">✅ Available</span>
            <?php else: ?>
                <span style="color: red;">❌ Blocked</span>
            <?php endif; ?>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>📖 Full Setup Guide:</strong> See <code>GOOGLE_WORKSPACE_EMAIL_SETUP.md</code></p>
            <p><strong>🔙 Return to:</strong> <a href="/">Home</a> | <a href="/login">Login</a></p>
        </div>
    </div>
</body>
</html>

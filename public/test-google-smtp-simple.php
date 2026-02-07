<?php
/**
 * Simple Email Test - No PHPMailer Required
 * Tests basic email sending using PHP mail() function
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value, '"\'');
    }
}

$testResult = null;
$testEmail = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
    $testEmail = filter_var($_POST['test_email'], FILTER_VALIDATE_EMAIL);
    
    if ($testEmail) {
        $subject = 'Test Email from TSU Staff Portal - ' . date('Y-m-d H:i:s');
        $message = '
        <html>
        <head><title>Test Email</title></head>
        <body style="font-family: Arial, sans-serif; padding: 20px;">
            <div style="max-width: 600px; margin: 0 auto; background: #f9fafb; padding: 30px; border-radius: 10px;">
                <h1 style="color: #1e40af;">✅ Email Test Successful!</h1>
                <p>Your email configuration is working correctly.</p>
                <p><strong>Test Details:</strong></p>
                <ul>
                    <li>Sent at: ' . date('Y-m-d H:i:s') . '</li>
                    <li>From: ' . ($_ENV['MAIL_FROM_ADDRESS'] ?? 'not set') . '</li>
                    <li>To: ' . htmlspecialchars($testEmail) . '</li>
                    <li>Method: PHP mail() function</li>
                </ul>
                <p>Your TSU Staff Portal can now send emails!</p>
            </div>
        </body>
        </html>';
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . ($_ENV['MAIL_FROM_NAME'] ?? 'TSU Staff Portal') . " <" . ($_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@tsuniversity.edu.ng') . ">" . "\r\n";
        
        $result = @mail($testEmail, $subject, $message, $headers);
        
        $testResult = [
            'success' => $result,
            'email' => $testEmail,
            'time' => date('Y-m-d H:i:s')
        ];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Test - TSU Staff Portal</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
            margin: 20px 0;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #17a2b8;
            margin: 20px 0;
        }
        .config {
            background: #f9fafb;
            padding: 15px;
            border-left: 4px solid #1e40af;
            margin: 20px 0;
        }
        pre {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #1e40af;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #1e3a8a;
        }
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            margin: 10px 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Email Configuration Test</h1>
        
        <div class="info">
            <strong>📋 Current Configuration:</strong>
            <pre>Mail Host: <?= $_ENV['MAIL_HOST'] ?? 'Not set' ?>
Mail Port: <?= $_ENV['MAIL_PORT'] ?? 'Not set' ?>
Mail Username: <?= $_ENV['MAIL_USERNAME'] ?? 'Not set' ?>
Mail Password: <?= !empty($_ENV['MAIL_PASSWORD']) ? '****' . substr($_ENV['MAIL_PASSWORD'], -4) : 'Not set' ?>
Mail Encryption: <?= $_ENV['MAIL_ENCRYPTION'] ?? 'Not set' ?>
From Address: <?= $_ENV['MAIL_FROM_ADDRESS'] ?? 'Not set' ?>
From Name: <?= $_ENV['MAIL_FROM_NAME'] ?? 'Not set' ?></pre>
        </div>

        <?php if ($testResult): ?>
            <?php if ($testResult['success']): ?>
                <div class="success">
                    <h2>✅ Email Sent Successfully!</h2>
                    <p><strong>Test email sent to:</strong> <?= htmlspecialchars($testResult['email']) ?></p>
                    <p><strong>Sent at:</strong> <?= $testResult['time'] ?></p>
                    <p>Check your inbox (and spam folder) for the test email.</p>
                    <p><strong>Note:</strong> The mail() function reported success, but actual delivery depends on server mail configuration.</p>
                </div>
            <?php else: ?>
                <div class="error">
                    <h2>❌ Email Sending Failed</h2>
                    <p>The mail() function returned false. This could mean:</p>
                    <ul>
                        <li>Mail server is not configured on this server</li>
                        <li>PHP mail() function is disabled</li>
                        <li>Invalid email address</li>
                    </ul>
                    <p><strong>Recommendation:</strong> Use SendGrid or another SMTP service instead.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <h2>📨 Send Test Email</h2>
        <form method="POST">
            <label for="test_email">Enter your email address:</label>
            <input type="email" 
                   id="test_email" 
                   name="test_email" 
                   required 
                   placeholder="your.email@tsuniversity.edu.ng"
                   value="<?= htmlspecialchars($testEmail) ?>">
            <button type="submit" class="btn">📨 Send Test Email</button>
        </form>

        <h2>🔍 System Information</h2>
        <div class="config">
            <strong>PHP Version:</strong> <?= phpversion() ?><br>
            <strong>Mail Function:</strong> <?= function_exists('mail') ? '✅ Available' : '❌ Not Available' ?><br>
            <strong>OpenSSL:</strong> <?= extension_loaded('openssl') ? '✅ Enabled' : '❌ Disabled' ?><br>
            <strong>.env File:</strong> <?= file_exists($envFile) ? '✅ Found' : '❌ Not Found' ?><br>
        </div>

        <h2>📚 Next Steps</h2>
        <div class="info">
            <h3>If Email Sending Works:</h3>
            <ol>
                <li>✅ Your email configuration is correct</li>
                <li>✅ Test user registration to verify verification emails</li>
                <li>✅ Test password reset functionality</li>
            </ol>

            <h3>If Email Sending Fails:</h3>
            <ol>
                <li>Check .env file has correct SMTP settings</li>
                <li>Verify Google App Password is correct</li>
                <li>Consider using SendGrid (more reliable)</li>
                <li>Check server error logs for details</li>
            </ol>

            <h3>Recommended: Use SendGrid</h3>
            <p>For production, SendGrid is more reliable than PHP mail():</p>
            <ul>
                <li>Free tier: 100 emails/day</li>
                <li>99.9% delivery rate</li>
                <li>Easy setup: 5 minutes</li>
                <li>See: ALTERNATIVE_EMAIL_SOLUTIONS.md</li>
            </ul>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>🔙 Return to:</strong> <a href="/">Home</a> | <a href="/login">Login</a> | <a href="/register">Register</a></p>
        </div>
    </div>
</body>
</html>

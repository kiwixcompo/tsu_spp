<?php
/**
 * Simple Google Workspace SMTP Test
 * Based on the cPanel guide for PHPMailer
 * Access: https://staff.tsuniversity.edu.ng/test_google_smtp.php
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

// Load PHPMailer
require("vendor/phpmailer/PHPMailer/src/PHPMailer.php");
require("vendor/phpmailer/PHPMailer/src/SMTP.php");
require("vendor/phpmailer/PHPMailer/src/Exception.php");

echo "<!DOCTYPE html><html><head><title>Google SMTP Test</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;}";
echo ".success{color:green;background:#d4edda;padding:15px;border-radius:5px;margin:10px 0;}";
echo ".error{color:red;background:#f8d7da;padding:15px;border-radius:5px;margin:10px 0;}";
echo ".info{color:#004085;background:#cce5ff;padding:15px;border-radius:5px;margin:10px 0;}";
echo ".debug{background:#f8f9fa;padding:10px;border-radius:5px;margin:5px 0;font-size:12px;}";
echo "</style></head><body>";

echo "<h1>📧 Google Workspace SMTP Test</h1>";

// Display current configuration
echo "<div class='info'>";
echo "<h3>Current Configuration:</h3>";
echo "<p><strong>SMTP Host:</strong> " . ($_ENV['MAIL_HOST'] ?? 'not set') . "</p>";
echo "<p><strong>SMTP Port:</strong> " . ($_ENV['MAIL_PORT'] ?? 'not set') . "</p>";
echo "<p><strong>Username:</strong> " . ($_ENV['MAIL_USERNAME'] ?? 'not set') . "</p>";
echo "<p><strong>Password:</strong> " . (isset($_ENV['MAIL_PASSWORD']) && !empty($_ENV['MAIL_PASSWORD']) ? '***SET*** (length: ' . strlen($_ENV['MAIL_PASSWORD']) . ')' : 'NOT SET') . "</p>";
echo "<p><strong>Encryption:</strong> " . ($_ENV['MAIL_ENCRYPTION'] ?? 'not set') . "</p>";
echo "</div>";

if (isset($_POST['send_test'])) {
    $recipientEmail = $_POST['recipient_email'] ?? '';
    
    if (empty($recipientEmail) || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='error'>Please enter a valid email address</div>";
    } else {
        echo "<h2>Sending Test Email...</h2>";
        
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['MAIL_PORT'];
            
            // SSL options for cPanel compatibility
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => false
                )
            );
            
            // Enable debug output
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = function($str, $level) {
                echo "<div class='debug'>[$level] " . htmlspecialchars($str) . "</div>";
            };
            
            // Recipients
            $mail->setFrom($_ENV['MAIL_USERNAME'], 'TSU Staff Portal');
            $mail->addAddress($recipientEmail, 'Test Recipient');
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Test Email from TSU Staff Portal';
            $mail->Body = '
                <html>
                <body style="font-family: Arial, sans-serif;">
                    <h2>✓ Email Test Successful!</h2>
                    <p>This email was sent using PHPMailer with Google Workspace SMTP.</p>
                    <p><strong>Sent at:</strong> ' . date('Y-m-d H:i:s') . '</p>
                    <p><strong>From:</strong> ' . $_ENV['MAIL_USERNAME'] . '</p>
                    <p><strong>Configuration:</strong></p>
                    <ul>
                        <li>SMTP Host: ' . $_ENV['MAIL_HOST'] . '</li>
                        <li>SMTP Port: ' . $_ENV['MAIL_PORT'] . '</li>
                        <li>Encryption: STARTTLS</li>
                    </ul>
                    <p>If you received this email, your Google Workspace SMTP is working correctly!</p>
                </body>
                </html>
            ';
            $mail->AltBody = 'Email test successful! Sent at: ' . date('Y-m-d H:i:s');
            
            $mail->send();
            
            echo "<div class='success'>";
            echo "<h3>✓ Message has been sent successfully!</h3>";
            echo "<p>Test email sent to: <strong>$recipientEmail</strong></p>";
            echo "<p>Check your inbox (and spam folder) for the test email.</p>";
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "<h3>✗ Message could not be sent</h3>";
            echo "<p><strong>Mailer Error:</strong> " . htmlspecialchars($mail->ErrorInfo) . "</p>";
            echo "<p><strong>Exception:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
            
            echo "<div class='info'>";
            echo "<h4>Troubleshooting Steps:</h4>";
            echo "<ol>";
            echo "<li>Verify your Google Workspace email: " . ($_ENV['MAIL_USERNAME'] ?? 'NOT SET') . "</li>";
            echo "<li>Ensure 2-Step Verification is enabled on your Google account</li>";
            echo "<li>Generate a new App Password at: <a href='https://myaccount.google.com/apppasswords' target='_blank'>myaccount.google.com/apppasswords</a></li>";
            echo "<li>Update MAIL_PASSWORD in .env with the 16-character app password (no spaces)</li>";
            echo "<li>Make sure your server allows outgoing connections on port 587</li>";
            echo "</ol>";
            echo "</div>";
        }
    }
}

// Test form
echo "<h2>Send Test Email</h2>";
echo "<form method='POST'>";
echo "<div style='margin:20px 0;'>";
echo "<label style='display:block;margin-bottom:5px;font-weight:bold;'>Recipient Email:</label>";
echo "<input type='email' name='recipient_email' placeholder='Enter recipient email address' style='width:100%;max-width:400px;padding:10px;border:1px solid #ddd;border-radius:5px;' required>";
echo "</div>";
echo "<button type='submit' name='send_test' style='padding:12px 24px;background:#1e40af;color:white;border:none;border-radius:5px;cursor:pointer;font-size:16px;'>Send Test Email</button>";
echo "</form>";

echo "<div class='error' style='margin-top:30px;'>";
echo "<h3>⚠️ SECURITY WARNING</h3>";
echo "<p><strong>Delete this file (test_google_smtp.php) immediately after testing!</strong></p>";
echo "</div>";

echo "</body></html>";

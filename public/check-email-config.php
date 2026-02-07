<?php
/**
 * Email Configuration Diagnostic
 * This checks your email setup and shows what's configured
 */

// Load environment
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

?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Configuration Check</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #1e40af; border-bottom: 3px solid #1e40af; padding-bottom: 10px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .status-ok { color: #28a745; font-weight: bold; }
        .status-error { color: #dc3545; font-weight: bold; }
        .status-warning { color: #ffc107; font-weight: bold; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Email Configuration Diagnostic</h1>
        
        <h2>📋 Environment Variables</h2>
        <table>
            <tr>
                <th>Variable</th>
                <th>Value</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>MAIL_HOST</td>
                <td><?= htmlspecialchars($_ENV['MAIL_HOST'] ?? 'NOT SET') ?></td>
                <td class="<?= !empty($_ENV['MAIL_HOST']) ? 'status-ok' : 'status-error' ?>">
                    <?= !empty($_ENV['MAIL_HOST']) ? '✅ Set' : '❌ Missing' ?>
                </td>
            </tr>
            <tr>
                <td>MAIL_PORT</td>
                <td><?= htmlspecialchars($_ENV['MAIL_PORT'] ?? 'NOT SET') ?></td>
                <td class="<?= !empty($_ENV['MAIL_PORT']) ? 'status-ok' : 'status-error' ?>">
                    <?= !empty($_ENV['MAIL_PORT']) ? '✅ Set' : '❌ Missing' ?>
                </td>
            </tr>
            <tr>
                <td>MAIL_USERNAME</td>
                <td><?= htmlspecialchars($_ENV['MAIL_USERNAME'] ?? 'NOT SET') ?></td>
                <td class="<?= !empty($_ENV['MAIL_USERNAME']) ? 'status-ok' : 'status-error' ?>">
                    <?= !empty($_ENV['MAIL_USERNAME']) ? '✅ Set' : '❌ Missing' ?>
                </td>
            </tr>
            <tr>
                <td>MAIL_PASSWORD</td>
                <td><?= !empty($_ENV['MAIL_PASSWORD']) ? '****' . substr($_ENV['MAIL_PASSWORD'], -4) : 'NOT SET' ?></td>
                <td class="<?= !empty($_ENV['MAIL_PASSWORD']) ? 'status-ok' : 'status-error' ?>">
                    <?= !empty($_ENV['MAIL_PASSWORD']) ? '✅ Set' : '❌ Missing' ?>
                </td>
            </tr>
            <tr>
                <td>MAIL_ENCRYPTION</td>
                <td><?= htmlspecialchars($_ENV['MAIL_ENCRYPTION'] ?? 'NOT SET') ?></td>
                <td class="<?= !empty($_ENV['MAIL_ENCRYPTION']) ? 'status-ok' : 'status-error' ?>">
                    <?= !empty($_ENV['MAIL_ENCRYPTION']) ? '✅ Set' : '❌ Missing' ?>
                </td>
            </tr>
            <tr>
                <td>MAIL_FROM_ADDRESS</td>
                <td><?= htmlspecialchars($_ENV['MAIL_FROM_ADDRESS'] ?? 'NOT SET') ?></td>
                <td class="<?= !empty($_ENV['MAIL_FROM_ADDRESS']) ? 'status-ok' : 'status-error' ?>">
                    <?= !empty($_ENV['MAIL_FROM_ADDRESS']) ? '✅ Set' : '❌ Missing' ?>
                </td>
            </tr>
        </table>

        <h2>🔧 System Checks</h2>
        <table>
            <tr>
                <th>Component</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
            <tr>
                <td>PHP Version</td>
                <td class="status-ok">✅</td>
                <td><?= phpversion() ?></td>
            </tr>
            <tr>
                <td>OpenSSL Extension</td>
                <td class="<?= extension_loaded('openssl') ? 'status-ok' : 'status-error' ?>">
                    <?= extension_loaded('openssl') ? '✅ Enabled' : '❌ Disabled' ?>
                </td>
                <td><?= extension_loaded('openssl') ? 'Required for SMTP/TLS' : 'REQUIRED - Contact hosting' ?></td>
            </tr>
            <tr>
                <td>mail() Function</td>
                <td class="<?= function_exists('mail') ? 'status-ok' : 'status-error' ?>">
                    <?= function_exists('mail') ? '✅ Available' : '❌ Disabled' ?>
                </td>
                <td><?= function_exists('mail') ? 'Fallback available' : 'Not available' ?></td>
            </tr>
            <tr>
                <td>PHPMailer</td>
                <td class="<?= file_exists(__DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php') ? 'status-ok' : 'status-error' ?>">
                    <?= file_exists(__DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php') ? '✅ Installed' : '❌ Not Found' ?>
                </td>
                <td><?= file_exists(__DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php') ? 'Ready for SMTP' : 'REQUIRED - Install PHPMailer' ?></td>
            </tr>
            <tr>
                <td>.env File</td>
                <td class="<?= file_exists($envFile) ? 'status-ok' : 'status-error' ?>">
                    <?= file_exists($envFile) ? '✅ Found' : '❌ Missing' ?>
                </td>
                <td><?= file_exists($envFile) ? 'Configuration loaded' : 'Create .env file' ?></td>
            </tr>
        </table>

        <h2>📊 Configuration Analysis</h2>
        
        <?php
        $issues = [];
        $warnings = [];
        
        // Check for common issues
        if (empty($_ENV['MAIL_HOST'])) {
            $issues[] = "MAIL_HOST is not set in .env file";
        } elseif ($_ENV['MAIL_HOST'] === 'localhost') {
            $warnings[] = "MAIL_HOST is set to 'localhost' - this won't work for external email delivery";
        } elseif ($_ENV['MAIL_HOST'] === 'mail.tsuniversity.edu.ng') {
            $warnings[] = "Using cPanel mail server - this has routing issues with Google Workspace";
        }
        
        if (empty($_ENV['MAIL_USERNAME']) || empty($_ENV['MAIL_PASSWORD'])) {
            $issues[] = "SMTP credentials (username/password) are not set";
        }
        
        if (!file_exists(__DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php')) {
            $issues[] = "PHPMailer is not installed - SMTP won't work";
        }
        
        if (!extension_loaded('openssl')) {
            $issues[] = "OpenSSL extension is not enabled - TLS/SSL won't work";
        }
        
        if ($_ENV['MAIL_HOST'] === 'smtp.gmail.com' && strlen($_ENV['MAIL_PASSWORD'] ?? '') !== 16) {
            $warnings[] = "Google App Password should be 16 characters (no spaces)";
        }
        ?>
        
        <?php if (empty($issues) && empty($warnings)): ?>
            <div class="success">
                <h3>✅ Configuration Looks Good!</h3>
                <p>All required settings are configured. Email sending should work.</p>
                <p><strong>Next step:</strong> Test email sending with a real registration or password reset.</p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($issues)): ?>
            <div class="error">
                <h3>❌ Critical Issues Found:</h3>
                <ul>
                    <?php foreach ($issues as $issue): ?>
                        <li><?= htmlspecialchars($issue) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($warnings)): ?>
            <div class="warning">
                <h3>⚠️ Warnings:</h3>
                <ul>
                    <?php foreach ($warnings as $warning): ?>
                        <li><?= htmlspecialchars($warning) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <h2>📝 Recommended Configuration</h2>
        <div class="info">
            <p><strong>For Google Workspace SMTP:</strong></p>
            <pre>MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@tsuniversity.edu.ng
MAIL_PASSWORD=your_16_char_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@tsuniversity.edu.ng
MAIL_FROM_NAME="TSU Staff Profile Portal"</pre>
            
            <p><strong>Steps to get App Password:</strong></p>
            <ol>
                <li>Go to: https://myaccount.google.com/apppasswords</li>
                <li>Select: Mail → Other (Custom name) → "TSU Staff Portal"</li>
                <li>Copy the 16-character password (remove spaces)</li>
                <li>Update .env file with this password</li>
            </ol>
        </div>

        <h2>🔗 Quick Links</h2>
        <p>
            <a href="/">← Back to Home</a> | 
            <a href="/register">Test Registration</a> | 
            <a href="/test-google-smtp-simple.php">Simple Email Test</a>
        </p>
    </div>
</body>
</html>

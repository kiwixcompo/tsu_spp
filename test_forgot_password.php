<?php
/**
 * Forgot Password Functionality Test Script
 * 
 * This script helps verify that the forgot password feature is working correctly
 * Usage: Access via browser at /test_forgot_password.php
 */

// Load environment
require_once __DIR__ . '/vendor/autoload.php';

// Load .env file
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password Test - TSU Staff Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; padding: 20px; }
        .test-card { background: white; border-radius: 10px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
        .code-block { background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff; margin: 10px 0; }
        pre { margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-card">
            <h1 class="mb-4"><i class="fas fa-key me-2"></i>Forgot Password Functionality Test</h1>
            <p class="lead">This page tests the forgot password feature and shows configuration status.</p>
            
            <?php
            // Debug: Show if .env was loaded
            $envFile = __DIR__ . '/.env';
            if (file_exists($envFile)) {
                echo '<div class="alert alert-info">';
                echo '<i class="fas fa-info-circle me-2"></i>';
                echo '<strong>.env file found:</strong> ' . $envFile . '<br>';
                echo '<strong>File size:</strong> ' . filesize($envFile) . ' bytes<br>';
                echo '<strong>Readable:</strong> ' . (is_readable($envFile) ? 'Yes' : 'No');
                echo '</div>';
            } else {
                echo '<div class="alert alert-warning">';
                echo '<i class="fas fa-exclamation-triangle me-2"></i>';
                echo '<strong>.env file NOT found at:</strong> ' . $envFile;
                echo '</div>';
            }
            ?>
        </div>

        <?php
        // Test 1: Check Email Configuration
        echo '<div class="test-card">';
        echo '<h3><i class="fas fa-envelope me-2"></i>1. Email Configuration</h3>';
        
        $mailHost = $_ENV['MAIL_HOST'] ?? 'not set';
        $mailPort = $_ENV['MAIL_PORT'] ?? 'not set';
        $mailUsername = $_ENV['MAIL_USERNAME'] ?? 'not set';
        $mailPassword = $_ENV['MAIL_PASSWORD'] ?? 'not set';
        $mailFrom = $_ENV['MAIL_FROM_ADDRESS'] ?? 'not set';
        
        $emailConfigured = !empty($mailHost) && $mailHost !== 'not set' && 
                          !empty($mailUsername) && $mailUsername !== 'not set' &&
                          !empty($mailPassword) && $mailPassword !== 'not set';
        
        if ($emailConfigured) {
            echo '<p class="status-ok"><i class="fas fa-check-circle me-2"></i>Email is configured</p>';
        } else {
            echo '<p class="status-error"><i class="fas fa-times-circle me-2"></i>Email is NOT configured</p>';
        }
        
        echo '<div class="code-block">';
        echo '<strong>Configuration:</strong><br>';
        echo 'MAIL_HOST: ' . htmlspecialchars($mailHost) . '<br>';
        echo 'MAIL_PORT: ' . htmlspecialchars($mailPort) . '<br>';
        echo 'MAIL_USERNAME: ' . htmlspecialchars($mailUsername) . '<br>';
        echo 'MAIL_PASSWORD: ' . (($mailPassword !== 'not set' && !empty($mailPassword)) ? '****** (set)' : 'not set') . '<br>';
        echo 'MAIL_FROM_ADDRESS: ' . htmlspecialchars($mailFrom) . '<br>';
        echo '</div>';
        echo '</div>';

        // Test 2: Check PHPMailer
        echo '<div class="test-card">';
        echo '<h3><i class="fas fa-paper-plane me-2"></i>2. PHPMailer Library</h3>';
        
        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            echo '<p class="status-ok"><i class="fas fa-check-circle me-2"></i>PHPMailer is installed</p>';
            
            try {
                $reflection = new ReflectionClass('PHPMailer\\PHPMailer\\PHPMailer');
                $filename = $reflection->getFileName();
                echo '<div class="code-block">';
                echo '<strong>Location:</strong> ' . htmlspecialchars(dirname($filename)) . '<br>';
                echo '</div>';
            } catch (Exception $e) {
                // Ignore
            }
        } else {
            echo '<p class="status-warning"><i class="fas fa-exclamation-triangle me-2"></i>PHPMailer not found - will use PHP mail() function</p>';
            echo '<div class="code-block">';
            echo '<strong>Note:</strong> The system will fall back to PHP\'s built-in mail() function.<br>';
            echo 'This should still work, but SMTP is more reliable.';
            echo '</div>';
        }
        echo '</div>';

        // Test 3: Check Database Connection
        echo '<div class="test-card">';
        echo '<h3><i class="fas fa-database me-2"></i>3. Database Connection</h3>';
        
        try {
            // Get database config from environment variables
            $dbHost = $_ENV['DB_HOST'] ?? 'localhost';
            $dbPort = $_ENV['DB_PORT'] ?? '3306';
            $dbName = $_ENV['DB_DATABASE'] ?? '';
            $dbUser = $_ENV['DB_USERNAME'] ?? '';
            $dbPass = $_ENV['DB_PASSWORD'] ?? '';
            
            if (empty($dbName) || empty($dbUser)) {
                throw new Exception('Database credentials not found in .env file');
            }
            
            $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbUser, $dbPass);
            
            echo '<p class="status-ok"><i class="fas fa-check-circle me-2"></i>Database connection successful</p>';
            
            echo '<div class="code-block">';
            echo '<strong>Configuration:</strong><br>';
            echo 'Host: ' . htmlspecialchars($dbHost) . '<br>';
            echo 'Port: ' . htmlspecialchars($dbPort) . '<br>';
            echo 'Database: ' . htmlspecialchars($dbName) . '<br>';
            echo 'Username: ' . htmlspecialchars($dbUser) . '<br>';
            echo '</div>';
            
            // Check if users table has reset token columns
            $stmt = $pdo->query("DESCRIBE users");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $hasResetToken = in_array('reset_token', $columns);
            $hasResetExpires = in_array('reset_token_expires', $columns);
            
            if ($hasResetToken && $hasResetExpires) {
                echo '<p class="status-ok"><i class="fas fa-check-circle me-2"></i>Password reset columns exist in database</p>';
            } else {
                echo '<p class="status-error"><i class="fas fa-times-circle me-2"></i>Password reset columns missing!</p>';
                echo '<div class="code-block">';
                echo '<strong>Missing columns:</strong><br>';
                if (!$hasResetToken) echo '- reset_token<br>';
                if (!$hasResetExpires) echo '- reset_token_expires<br>';
                echo '<br><strong>Run this SQL to fix:</strong><br>';
                echo '<pre>ALTER TABLE users 
ADD COLUMN reset_token VARCHAR(64) NULL,
ADD COLUMN reset_token_expires DATETIME NULL;</pre>';
                echo '</div>';
            }
            
        } catch (PDOException $e) {
            echo '<p class="status-error"><i class="fas fa-times-circle me-2"></i>Database connection failed</p>';
            echo '<div class="code-block">';
            echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '<br><br>';
            echo '<strong>Attempted Configuration:</strong><br>';
            echo 'Host: ' . htmlspecialchars($dbHost ?? 'not set') . '<br>';
            echo 'Port: ' . htmlspecialchars($dbPort ?? 'not set') . '<br>';
            echo 'Database: ' . htmlspecialchars($dbName ?? 'not set') . '<br>';
            echo 'Username: ' . htmlspecialchars($dbUser ?? 'not set') . '<br>';
            echo 'Password: ' . ((!empty($dbPass)) ? '****** (set)' : 'NOT SET') . '<br><br>';
            echo '<strong>Check:</strong><br>';
            echo '1. Verify .env file exists in project root<br>';
            echo '2. Verify DB_* variables are set in .env<br>';
            echo '3. Verify database credentials are correct<br>';
            echo '4. Verify MySQL server is running<br>';
            echo '</div>';
        } catch (Exception $e) {
            echo '<p class="status-error"><i class="fas fa-times-circle me-2"></i>Configuration error</p>';
            echo '<div class="code-block">';
            echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        }
        echo '</div>';

        // Test 4: Check Routes
        echo '<div class="test-card">';
        echo '<h3><i class="fas fa-route me-2"></i>4. Routes Configuration</h3>';
        
        $routesFile = __DIR__ . '/routes/web.php';
        if (file_exists($routesFile)) {
            $routesContent = file_get_contents($routesFile);
            
            $hasForgotPasswordGet = strpos($routesContent, "'/forgot-password'") !== false && 
                                   strpos($routesContent, 'showForgotPassword') !== false;
            $hasForgotPasswordPost = strpos($routesContent, "'/forgot-password'") !== false && 
                                    strpos($routesContent, 'forgotPassword') !== false;
            $hasResetPasswordGet = strpos($routesContent, "'/reset-password'") !== false && 
                                  strpos($routesContent, 'showResetPassword') !== false;
            $hasResetPasswordPost = strpos($routesContent, "'/reset-password'") !== false && 
                                   strpos($routesContent, 'resetPassword') !== false;
            
            if ($hasForgotPasswordGet && $hasForgotPasswordPost && $hasResetPasswordGet && $hasResetPasswordPost) {
                echo '<p class="status-ok"><i class="fas fa-check-circle me-2"></i>All password reset routes are configured</p>';
            } else {
                echo '<p class="status-warning"><i class="fas fa-exclamation-triangle me-2"></i>Some routes may be missing</p>';
            }
            
            echo '<div class="code-block">';
            echo '<strong>Expected routes:</strong><br>';
            echo ($hasForgotPasswordGet ? '✓' : '✗') . ' GET /forgot-password<br>';
            echo ($hasForgotPasswordPost ? '✓' : '✗') . ' POST /forgot-password<br>';
            echo ($hasResetPasswordGet ? '✓' : '✗') . ' GET /reset-password<br>';
            echo ($hasResetPasswordPost ? '✓' : '✗') . ' POST /reset-password<br>';
            echo '</div>';
        } else {
            echo '<p class="status-error"><i class="fas fa-times-circle me-2"></i>Routes file not found</p>';
        }
        echo '</div>';

        // Test 5: Check Reset Links File
        echo '<div class="test-card">';
        echo '<h3><i class="fas fa-link me-2"></i>5. Password Reset Links</h3>';
        
        $resetLinksFile = __DIR__ . '/public/reset_links.txt';
        if (file_exists($resetLinksFile)) {
            $links = file($resetLinksFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $recentLinks = array_slice(array_reverse($links), 0, 5);
            
            if (!empty($recentLinks)) {
                echo '<p class="status-ok"><i class="fas fa-check-circle me-2"></i>Recent password reset links found</p>';
                echo '<div class="code-block">';
                echo '<strong>Last 5 reset links:</strong><br><br>';
                foreach ($recentLinks as $link) {
                    $parts = explode(' | ', $link);
                    if (count($parts) >= 3) {
                        echo '<strong>Date:</strong> ' . htmlspecialchars($parts[0]) . '<br>';
                        echo '<strong>Email:</strong> ' . htmlspecialchars($parts[1]) . '<br>';
                        echo '<strong>Link:</strong> <a href="' . htmlspecialchars($parts[2]) . '" target="_blank">' . htmlspecialchars($parts[2]) . '</a><br><br>';
                    }
                }
                echo '</div>';
            } else {
                echo '<p class="status-warning"><i class="fas fa-exclamation-triangle me-2"></i>No reset links found yet</p>';
            }
        } else {
            echo '<p class="status-warning"><i class="fas fa-exclamation-triangle me-2"></i>No reset links file yet (will be created when first email is sent)</p>';
        }
        echo '</div>';

        // Test 6: Test Form
        echo '<div class="test-card">';
        echo '<h3><i class="fas fa-vial me-2"></i>6. Test Forgot Password</h3>';
        echo '<p>Use the form below to test the forgot password functionality:</p>';
        ?>
        
        <form id="testForm" class="mt-3">
            <div class="mb-3">
                <label for="testEmail" class="form-label">Enter a registered email:</label>
                <input type="email" class="form-control" id="testEmail" placeholder="user@tsuniversity.edu.ng" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Send Test Reset Link
            </button>
        </form>
        
        <div id="testResult" class="mt-3"></div>
        
        <?php
        echo '</div>';
        ?>

        <div class="test-card">
            <h3><i class="fas fa-info-circle me-2"></i>Summary</h3>
            <p>If all tests pass, the forgot password functionality should work correctly.</p>
            <p><strong>To use:</strong></p>
            <ol>
                <li>Go to <a href="/login">/login</a></li>
                <li>Click "Forgot Password?"</li>
                <li>Enter your @tsuniversity.edu.ng email</li>
                <li>Check your email for the reset link</li>
                <li>If email doesn't arrive, check <code>public/reset_links.txt</code> for the link</li>
            </ol>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('testForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('testEmail').value;
            const resultDiv = document.getElementById('testResult');
            
            resultDiv.innerHTML = '<div class="alert alert-info">Sending reset link...</div>';
            
            const formData = new FormData();
            formData.append('email', email);
            
            fetch('/forgot-password', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>${data.message}
                            <br><br>
                            <strong>Check:</strong>
                            <ul>
                                <li>Your email inbox</li>
                                <li>The file <code>public/reset_links.txt</code> for the reset link</li>
                                <li>The file <code>storage/emails/</code> for saved email content</li>
                            </ul>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>${data.error || 'Failed to send reset link'}
                        </div>
                    `;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i>Error: ${error.message}
                    </div>
                `;
            });
        });
    </script>
</body>
</html>

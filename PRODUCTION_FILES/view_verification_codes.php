<?php
/**
 * Temporary Verification Codes Viewer
 * Upload to public folder for testing
 * DELETE after email is configured!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/Core/Database.php';

try {
    $db = App\Core\Database::getInstance();
    
    $users = $db->fetchAll(
        "SELECT email, verification_code, verification_expires, created_at 
         FROM users 
         WHERE email_verified = 0 
         ORDER BY created_at DESC"
    );
    
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Verification Codes - TSU Staff Portal</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 1000px;
                margin: 20px auto;
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
                margin-bottom: 10px;
            }
            .warning {
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                padding: 15px;
                margin: 20px 0;
                border-radius: 5px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            th, td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            th {
                background: #1e40af;
                color: white;
                font-weight: bold;
            }
            tr:hover {
                background: #f8f9fa;
            }
            .code {
                font-size: 24px;
                font-weight: bold;
                color: #1e40af;
                font-family: monospace;
            }
            .expired {
                color: #dc3545;
            }
            .valid {
                color: #28a745;
            }
            .no-data {
                text-align: center;
                padding: 40px;
                color: #666;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔐 Verification Codes</h1>
            <p><strong>Server:</strong> <?= $_SERVER['HTTP_HOST'] ?></p>
            
            <div class="warning">
                <strong>⚠️ SECURITY WARNING:</strong> This page shows sensitive verification codes. 
                DELETE this file immediately after configuring email!
            </div>
            
            <?php if (count($users) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Verification Code</th>
                            <th>Expires</th>
                            <th>Status</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): 
                            $isExpired = strtotime($user['verification_expires']) < time();
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td class="code"><?= htmlspecialchars($user['verification_code']) ?></td>
                            <td><?= htmlspecialchars($user['verification_expires']) ?></td>
                            <td class="<?= $isExpired ? 'expired' : 'valid' ?>">
                                <?= $isExpired ? '❌ Expired' : '✅ Valid' ?>
                            </td>
                            <td><?= htmlspecialchars($user['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin-top: 20px;">
                    <h3>How to Use:</h3>
                    <ol>
                        <li>Copy the 6-digit verification code</li>
                        <li>Go to the verify-email page</li>
                        <li>Enter the code</li>
                        <li>Submit to verify account</li>
                    </ol>
                </div>
                
            <?php else: ?>
                <div class="no-data">
                    <h3>No Pending Verifications</h3>
                    <p>All users have verified their emails, or no users have registered yet.</p>
                </div>
            <?php endif; ?>
            
            <hr style="margin: 30px 0;">
            
            <div style="background: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;">
                <h3 style="color: #721c24; margin-top: 0;">⚠️ Important: Configure Email</h3>
                <p>This page is a temporary workaround. To send real emails:</p>
                <ol>
                    <li>Update your <code>.env</code> file with SMTP settings</li>
                    <li>Test email sending</li>
                    <li><strong>DELETE THIS FILE</strong> for security</li>
                </ol>
                
                <p><strong>Example .env configuration:</strong></p>
                <pre style="background: white; padding: 10px; border-radius: 5px;">
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@tsuniversity.edu.ng
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls</pre>
            </div>
            
            <hr style="margin: 30px 0;">
            
            <p style="text-align: center;">
                <a href="/" style="padding: 10px 20px; background: #1e40af; color: white; text-decoration: none; border-radius: 5px;">
                    ← Back to Homepage
                </a>
            </p>
        </div>
    </body>
    </html>
    <?php
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

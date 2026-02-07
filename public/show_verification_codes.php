<?php
/**
 * Development Helper - Show Verification Codes
 * This page shows verification codes from the database for testing
 */

// Only allow in development
if (($_ENV['APP_ENV'] ?? 'production') !== 'development') {
    die('This page is only available in development mode.');
}

// Load environment
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Simple autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

try {
    $db = App\Core\Database::getInstance();
    
    // Get users with pending verification
    $pendingUsers = $db->fetchAll(
        "SELECT id, email, email_prefix, verification_code, verification_expires, created_at 
         FROM users 
         WHERE email_verified = FALSE AND verification_code IS NOT NULL 
         ORDER BY created_at DESC"
    );
    
    // Get recent activity logs for email debugging
    $emailLogs = [];
    if (file_exists(__DIR__ . '/../storage/logs/error.log')) {
        $logContent = file_get_contents(__DIR__ . '/../storage/logs/error.log');
        $lines = explode("\n", $logContent);
        foreach (array_reverse($lines) as $line) {
            if (strpos($line, 'EMAIL DEBUG') !== false) {
                $emailLogs[] = $line;
                if (count($emailLogs) >= 10) break; // Show last 10 email logs
            }
        }
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Codes - TSU Staff Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .code-display {
            font-size: 2rem;
            font-weight: bold;
            color: #0d6efd;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            letter-spacing: 0.2em;
        }
        .expired {
            color: #dc3545;
            text-decoration: line-through;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-key me-2"></i>Verification Codes (Development)</h1>
                    <a href="/tsu_spp/public/" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Back to Homepage
                    </a>
                </div>

                <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <h4>Database Error</h4>
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
                <?php endif; ?>

                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle me-2"></i>Development Mode</h5>
                    <p>In development mode, emails are not actually sent. Instead, verification codes are stored in the database and logged. Use this page to get verification codes for testing.</p>
                </div>

                <?php if (!empty($pendingUsers)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-clock me-2"></i>Pending Email Verifications</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($pendingUsers as $user): ?>
                        <div class="row mb-3 p-3 border rounded">
                            <div class="col-md-4">
                                <strong>Email:</strong><br>
                                <?= htmlspecialchars($user['email']) ?>
                                <br><small class="text-muted">Registered: <?= date('M d, Y H:i', strtotime($user['created_at'])) ?></small>
                            </div>
                            <div class="col-md-4">
                                <strong>Verification Code:</strong><br>
                                <?php 
                                $isExpired = strtotime($user['verification_expires']) < time();
                                $codeClass = $isExpired ? 'code-display expired' : 'code-display';
                                ?>
                                <div class="<?= $codeClass ?>">
                                    <?= htmlspecialchars($user['verification_code']) ?>
                                </div>
                                <?php if ($isExpired): ?>
                                <small class="text-danger">Expired</small>
                                <?php else: ?>
                                <small class="text-success">Valid until: <?= date('M d, Y H:i', strtotime($user['verification_expires'])) ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <strong>Actions:</strong><br>
                                <a href="/tsu_spp/public/verify-email" class="btn btn-sm btn-primary">
                                    <i class="fas fa-check me-1"></i>Verify Email
                                </a>
                                <button class="btn btn-sm btn-outline-secondary" onclick="copyCode('<?= $user['verification_code'] ?>')">
                                    <i class="fas fa-copy me-1"></i>Copy Code
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>No Pending Verifications</h5>
                    <p>There are no users waiting for email verification. <a href="/tsu_spp/public/register">Register a new account</a> to test the verification process.</p>
                </div>
                <?php endif; ?>

                <?php if (!empty($emailLogs)): ?>
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-envelope me-2"></i>Recent Email Logs</h5>
                    </div>
                    <div class="card-body">
                        <div class="bg-dark text-light p-3 rounded" style="font-family: monospace; font-size: 0.9rem; max-height: 300px; overflow-y: auto;">
                            <?php foreach ($emailLogs as $log): ?>
                            <div><?= htmlspecialchars($log) ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-4">
                    <h5>Quick Links</h5>
                    <div class="btn-group" role="group">
                        <a href="/tsu_spp/public/register" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-1"></i>Register New Account
                        </a>
                        <a href="/tsu_spp/public/verify-email" class="btn btn-outline-success">
                            <i class="fas fa-check-circle me-1"></i>Verify Email
                        </a>
                        <a href="/tsu_spp/public/login" class="btn btn-outline-info">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                        <a href="/tsu_spp/test_db.php" class="btn btn-outline-secondary">
                            <i class="fas fa-database me-1"></i>Database Test
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(function() {
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
                toast.style.zIndex = '9999';
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            Verification code copied to clipboard!
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                document.body.appendChild(toast);
                
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
                
                // Remove toast after it's hidden
                toast.addEventListener('hidden.bs.toast', () => {
                    document.body.removeChild(toast);
                });
            });
        }

        // Auto-refresh every 30 seconds
        setTimeout(() => {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>
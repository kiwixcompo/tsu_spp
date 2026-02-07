<?php
/**
 * Verification Codes Viewer
 * Shows verification codes saved to file (similar to password reset links)
 */

$codesFile = __DIR__ . '/verification_codes.txt';
$dbCodes = [];

// Get codes from database
try {
    require_once __DIR__ . '/../app/Core/Database.php';
    $db = App\Core\Database::getInstance();
    
    $dbCodes = $db->fetchAll(
        "SELECT email, verification_code, verification_expires, created_at 
         FROM users 
         WHERE email_verified = 0 
         AND verification_code IS NOT NULL
         ORDER BY created_at DESC"
    );
} catch (Exception $e) {
    // Database not available
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
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .code {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
            font-family: 'Courier New', monospace;
            letter-spacing: 3px;
        }
        .expired {
            color: #dc3545;
            text-decoration: line-through;
        }
        .valid {
            color: #28a745;
        }
        .badge-custom {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <div class="card">
                    <div class="card-body text-center py-4">
                        <h1 class="mb-2">
                            <i class="fas fa-key text-primary me-2"></i>
                            Verification Codes
                        </h1>
                        <p class="text-muted mb-0">TSU Staff Portal - Development Mode</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert -->
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Development Mode:</strong> Verification codes are saved to a file for easy access. 
            In production with SMTP configured, codes will be sent via email.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <!-- Database Codes -->
        <?php if (!empty($dbCodes)): ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-database me-2"></i>
                    Active Verification Codes (From Database)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
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
                            <?php foreach ($dbCodes as $user): 
                                $isExpired = strtotime($user['verification_expires']) < time();
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="code <?= $isExpired ? 'expired' : '' ?>">
                                        <?= htmlspecialchars($user['verification_code']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($user['verification_expires']) ?></td>
                                <td>
                                    <?php if ($isExpired): ?>
                                        <span class="badge bg-danger badge-custom">
                                            <i class="fas fa-times-circle me-1"></i>Expired
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success badge-custom">
                                            <i class="fas fa-check-circle me-1"></i>Valid
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y H:i', strtotime($user['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- File Codes -->
        <?php if (file_exists($codesFile)): ?>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i>
                    Recent Verification Codes (From File)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Email</th>
                                <th>Verification Code</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $lines = file($codesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                            $lines = array_reverse($lines); // Show newest first
                            $lines = array_slice($lines, 0, 20); // Show last 20
                            
                            foreach ($lines as $line):
                                $parts = explode(' | ', $line);
                                if (count($parts) >= 3):
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($parts[0]) ?></td>
                                <td><?= htmlspecialchars($parts[1]) ?></td>
                                <td>
                                    <span class="code"><?= htmlspecialchars($parts[2]) ?></span>
                                </td>
                            </tr>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="card mb-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h4>No Verification Codes Yet</h4>
                <p class="text-muted">Register a new account to see verification codes here.</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Instructions -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    How to Use
                </h5>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2">Register a new account on the portal</li>
                    <li class="mb-2">The verification code will appear in the table above</li>
                    <li class="mb-2">Copy the 6-digit code</li>
                    <li class="mb-2">Go to the verification page and enter the code</li>
                    <li class="mb-0">Your account will be verified!</li>
                </ol>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center">
            <a href="/" class="btn btn-light btn-lg">
                <i class="fas fa-home me-2"></i>Back to Homepage
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Load URL helper if not already loaded
if (!function_exists('url')) {
    require_once __DIR__ . '/../../Helpers/UrlHelper.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Link Expired - TSU Staff Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .auth-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }
        .auth-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="auth-container d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="auth-card p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                            <h2 class="fw-bold">Reset Link Expired</h2>
                            <p class="text-muted">
                                The password reset link you used has expired or is invalid. 
                                Reset links are only valid for 1 hour for security reasons.
                            </p>
                        </div>

                        <div class="text-center">
                            <a href="<?= url('/forgot-password') ?>" class="btn btn-primary btn-lg mb-3">
                                <i class="fas fa-redo me-2"></i>
                                Request New Reset Link
                            </a>
                            
                            <div>
                                <a href="<?= url('/login') ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to Login
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="<?= url('/') ?>" class="text-white text-decoration-none">
                            <i class="fas fa-home me-2"></i>Back to Homepage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
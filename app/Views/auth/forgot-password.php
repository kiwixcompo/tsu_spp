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
    <title>Forgot Password - TSU Staff Portal</title>
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
                            <img src="<?= asset('assets/images/tsu-logo.png') ?>" 
                                 alt="TSU Logo" 
                                 style="width: 80px; height: 80px; object-fit: contain;" 
                                 class="mb-3">
                            <h2 class="fw-bold">Forgot Password?</h2>
                            <p class="text-muted">
                                Enter your university email address and we'll send you a link to reset your password.
                            </p>
                        </div>

                        <form id="forgotForm" method="POST" action="<?= url('/forgot-password') ?>">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            
                            <div class="mb-4">
                                <label for="email" class="form-label">University Email</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="your.name@tsuniversity.edu.ng" required>
                                </div>
                                <div class="form-text">Enter your @tsuniversity.edu.ng email address</div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Send Reset Link
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="text-muted mb-2">Remember your password?</p>
                                <a href="<?= url('/login') ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to Login
                                </a>
                            </div>
                        </form>
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

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mb-0">Sending reset link...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form submission
        document.getElementById('forgotForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            
            // Validate TSU email
            if (!email.endsWith('@tsuniversity.edu.ng')) {
                showAlert('danger', 'Please enter a valid @tsuniversity.edu.ng email address');
                return;
            }

            // Show loading modal
            const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
            loadingModal.show();

            // Submit form
            const formData = new FormData(this);
            
            fetch('<?= url('/forgot-password') ?>', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                try {
                    const data = await response.json();
                    return data;
                } catch (e) {
                    loadingModal.hide();
                    throw new Error('Invalid server response');
                }
            })
            .then(data => {
                // Always hide modal first
                loadingModal.hide();
                
                if (data.success) {
                    showAlert('success', data.message);
                    // Show additional info for development
                    if (window.location.hostname === 'localhost') {
                        setTimeout(() => {
                            showAlert('info', 'Development Mode: Check <a href="<?= url('/dev-emails.php') ?>" target="_blank">dev-emails.php</a> for the reset link.');
                        }, 2000);
                    }
                } else {
                    showAlert('danger', data.error || 'Failed to send reset link. Please try again.');
                }
            })
            .catch(error => {
                // Ensure modal is hidden
                loadingModal.hide();
                console.error('Forgot password error:', error);
                showAlert('danger', 'Network error. Please check your connection and try again.');
            });
        });

        function showAlert(type, message) {
            // Remove existing alerts
            document.querySelectorAll('.alert').forEach(alert => alert.remove());
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const form = document.getElementById('forgotForm');
            form.parentNode.insertBefore(alertDiv, form);
            
            // Auto-dismiss success/info alerts after 8 seconds
            if (type === 'success' || type === 'info') {
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 8000);
            }
        }
    </script>
</body>
</html>
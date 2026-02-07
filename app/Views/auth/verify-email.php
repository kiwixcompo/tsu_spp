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
    <title>Verify Email - TSU Staff Portal</title>
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
        .verification-code {
            font-size: 2rem;
            letter-spacing: 0.5rem;
            text-align: center;
            font-weight: bold;
        }
        .code-input {
            width: 60px;
            height: 60px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0 5px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
        }
        .code-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
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
                            <h2 class="fw-bold">Verify Your Email</h2>
                            <p class="text-muted">
                                We've sent a 6-digit verification code to your university email address. 
                                Enter the code below to activate your account.
                            </p>
                            <?php if (isset($_GET['new_code'])): ?>
                                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>New Code Sent!</strong> A fresh verification code has been sent to 
                                    <?php if (isset($_GET['email'])): ?>
                                        <strong><?= htmlspecialchars($_GET['email']) ?></strong>
                                    <?php else: ?>
                                        your email
                                    <?php endif; ?>. 
                                    Please check your inbox.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <form id="verifyForm" method="POST" action="<?= url('verify-email') ?>">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <?php if (isset($_GET['email'])): ?>
                                <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email']) ?>">
                            <?php endif; ?>
                            
                            <div class="mb-4">
                                <label class="form-label text-center d-block mb-3">Verification Code</label>
                                <div class="d-flex justify-content-center">
                                    <input type="text" class="code-input" maxlength="1" data-index="0">
                                    <input type="text" class="code-input" maxlength="1" data-index="1">
                                    <input type="text" class="code-input" maxlength="1" data-index="2">
                                    <input type="text" class="code-input" maxlength="1" data-index="3">
                                    <input type="text" class="code-input" maxlength="1" data-index="4">
                                    <input type="text" class="code-input" maxlength="1" data-index="5">
                                </div>
                                <input type="hidden" name="verification_code" id="verification_code">
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                    <i class="fas fa-check-circle me-2"></i>
                                    Verify Email
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="text-muted mb-2">Didn't receive the code?</p>
                                <button type="button" class="btn btn-outline-primary" id="resendBtn">
                                    <i class="fas fa-redo me-2"></i>
                                    Resend Code
                                </button>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        Code expires in <span id="countdown">24:00:00</span>
                                    </small>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="text-center mt-4">
                        <a href="<?= url('login') ?>" class="text-white text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>Back to Login
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
                    <p class="mb-0" id="loadingText">Verifying code...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Helper function to forcefully hide modal and remove backdrop
        function hideModalCompletely(modal) {
            console.log('Hiding modal completely...');
            
            // Hide the modal immediately
            if (modal && modal.hide) {
                modal.hide();
            }
            
            // Immediately start cleanup
            const cleanup = () => {
                // Remove modal backdrop
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                    backdrop.remove();
                });
                
                // Remove modal-open class from body
                document.body.classList.remove('modal-open');
                
                // Reset body style
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                
                // Hide the modal element itself
                const modalElement = document.getElementById('loadingModal');
                if (modalElement) {
                    modalElement.style.display = 'none';
                    modalElement.classList.remove('show');
                    modalElement.setAttribute('aria-hidden', 'true');
                }
                
                console.log('Modal cleanup completed');
            };
            
            // Run cleanup immediately and again after delays
            cleanup();
            setTimeout(cleanup, 50);
            setTimeout(cleanup, 200);
        }

        // Code input handling
        const codeInputs = document.querySelectorAll('.code-input');
        const submitBtn = document.getElementById('submitBtn');
        const verificationCodeInput = document.getElementById('verification_code');

        codeInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                const value = e.target.value;
                
                // Only allow numbers
                if (!/^\d$/.test(value)) {
                    e.target.value = '';
                    return;
                }

                // Move to next input
                if (value && index < codeInputs.length - 1) {
                    codeInputs[index + 1].focus();
                }

                updateVerificationCode();
            });

            input.addEventListener('keydown', function(e) {
                // Handle backspace
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    codeInputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = e.clipboardData.getData('text');
                const digits = paste.replace(/\D/g, '').slice(0, 6);
                
                digits.split('').forEach((digit, i) => {
                    if (codeInputs[i]) {
                        codeInputs[i].value = digit;
                    }
                });
                
                updateVerificationCode();
            });
        });

        function updateVerificationCode() {
            const code = Array.from(codeInputs).map(input => input.value).join('');
            verificationCodeInput.value = code;
            submitBtn.disabled = code.length !== 6;
        }

        // Form submission
        document.getElementById('verifyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const code = verificationCodeInput.value;
            if (code.length !== 6) {
                showAlert('danger', 'Please enter the complete 6-digit code');
                return;
            }

            // Show loading modal
            const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
            loadingModal.show();

            // Submit form
            const formData = new FormData(this);
            
            fetch('<?= url('verify-email') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Success - hide modal immediately
                    hideModalCompletely(loadingModal);
                    showAlert('success', data.message);
                    setTimeout(() => {
                        window.location.href = '<?= url('/') ?>' + (data.redirect || 'profile/setup');
                    }, 2000);
                } else {
                    // Error - hide modal with slight delay
                    setTimeout(() => {
                        hideModalCompletely(loadingModal);
                    }, 300);
                    showAlert('danger', data.error || 'Verification failed. Please try again.');
                }
            })
            .catch(error => {
                // Network error - hide modal with delay
                setTimeout(() => {
                    hideModalCompletely(loadingModal);
                }, 300);
                showAlert('danger', 'Network error. Please check your connection and try again.');
            });
        });

        // Resend code
        document.getElementById('resendBtn').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
            
            document.getElementById('loadingText').textContent = 'Sending new code...';
            const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
            loadingModal.show();

            fetch('<?= url('resend-verification') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    csrf_token: '<?= $csrf_token ?>'
                })
            })
            .then(async response => {
                console.log('Resend response status:', response.status);
                const data = await response.json();
                console.log('Resend response data:', data);
                return data;
            })
            .then(data => {
                if (data.success) {
                    // Success - hide modal immediately
                    hideModalCompletely(loadingModal);
                    showAlert('success', data.message);
                    // Clear current inputs
                    codeInputs.forEach(input => input.value = '');
                    codeInputs[0].focus();
                    updateVerificationCode();
                } else {
                    // Error - hide modal with delay
                    setTimeout(() => {
                        hideModalCompletely(loadingModal);
                    }, 300);
                    showAlert('danger', data.error || 'Failed to resend code. Please try again.');
                }
            })
            .catch(error => {
                // Network error - hide modal with delay
                console.error('Resend error:', error);
                setTimeout(() => {
                    hideModalCompletely(loadingModal);
                }, 300);
                showAlert('danger', 'Network error. Please try again.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
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
            
            const form = document.getElementById('verifyForm');
            form.parentNode.insertBefore(alertDiv, form);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Countdown timer (24 hours)
        let timeLeft = 24 * 60 * 60; // 24 hours in seconds
        
        function updateCountdown() {
            const hours = Math.floor(timeLeft / 3600);
            const minutes = Math.floor((timeLeft % 3600) / 60);
            const seconds = timeLeft % 60;
            
            document.getElementById('countdown').textContent = 
                `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft > 0) {
                timeLeft--;
                setTimeout(updateCountdown, 1000);
            } else {
                document.getElementById('countdown').textContent = 'Expired';
                showAlert('warning', 'Verification code has expired. Please request a new one.');
            }
        }
        
        updateCountdown();

        // Focus first input on load
        codeInputs[0].focus();

        // Auto-check if admin has verified the account
        let checkInterval = setInterval(function() {
            fetch('<?= url('check-verification-status') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    csrf_token: '<?= $csrf_token ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.verified) {
                    clearInterval(checkInterval);
                    showAlert('success', 'Your account has been verified by an administrator!');
                    setTimeout(() => {
                        window.location.href = '<?= url('profile/setup') ?>';
                    }, 2000);
                }
            })
            .catch(error => {
                // Silently fail - don't interrupt user experience
                console.log('Status check failed:', error);
            });
        }, 10000); // Check every 10 seconds
    </script>
</body>
</html>
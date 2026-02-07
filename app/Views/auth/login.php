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
    <title>Login - TSU Staff Portal</title>
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
                <div class="col-md-6 col-lg-4">
                    <div class="auth-card p-5">
                        <div class="text-center mb-4">
                            <img src="<?= asset('assets/images/tsu-logo.png') ?>" 
                                 alt="TSU Logo" 
                                 style="width: 80px; height: 80px; object-fit: contain;" 
                                 class="mb-3">
                            <h2 class="fw-bold">Welcome Back</h2>
                            <p class="text-muted">Sign in to your TSU Staff Portal</p>
                        </div>

                        <form id="loginForm" method="POST" action="<?= url('login') ?>">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">University Email</label>
                                <input type="email" 
                                       class="form-control form-control-lg" 
                                       id="email" 
                                       name="email" 
                                       placeholder="your.name@tsuniversity.edu.ng"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control form-control-lg" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Enter your password"
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="remember_me" 
                                           name="remember_me">
                                    <label class="form-check-label" for="remember_me">
                                        Remember me
                                    </label>
                                </div>
                                <a href="<?= url('forgot-password') ?>" class="text-primary text-decoration-none">
                                    Forgot password?
                                </a>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Sign In
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="mb-0">Don't have an account? 
                                    <a href="<?= url('register') ?>" class="text-primary fw-bold">Create Profile</a>
                                </p>
                            </div>
                        </form>
                    </div>

                    <div class="text-center mt-4">
                        <a href="<?= url() ?>" class="text-white text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>Back to Homepage
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
                    <p class="mb-0">Signing you in...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

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

        // Form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            // Show loading modal
            const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
            loadingModal.show();

            // Submit form
            const formData = new FormData(this);
            
            fetch('<?= url('login') ?>', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                try {
                    const data = await response.json();
                    return { status: response.status, data: data };
                } catch (e) {
                    // If JSON parsing fails, hide modal and show error
                    hideModalCompletely(loadingModal);
                    throw new Error('Invalid server response');
                }
            })
            .then(({status, data}) => {
                if (data.success) {
                    // Success - hide modal immediately and show success message
                    hideModalCompletely(loadingModal);
                    showAlert('success', data.message);
                    setTimeout(() => {
                        const redirectPath = data.redirect || 'dashboard';
                        const baseUrl = '<?= rtrim(url(''), '/') ?>';
                        window.location.href = baseUrl + '/' + redirectPath;
                    }, 1500);
                } else {
                    // Error - hide modal with slight delay so user sees it was processing
                    setTimeout(() => {
                        hideModalCompletely(loadingModal);
                    }, 300);
                    
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            showFieldError(field, data.errors[field]);
                        });
                    } else {
                        // Handle error messages (including 401 for invalid credentials)
                        const errorMsg = data.error || 'Login failed. Please try again.';
                        
                        // If redirect is provided (like for email verification)
                        if (data.redirect) {
                            // For verification redirects, go immediately without showing error
                            hideModalCompletely(loadingModal);
                            const baseUrl = '<?= rtrim(url(''), '/') ?>';
                            const fullUrl = baseUrl + '/' + data.redirect;
                            window.location.href = fullUrl;
                        } else {
                            // For other errors, show the message
                            showAlert('danger', errorMsg);
                        }
                    }
                }
            })
            .catch(error => {
                // Network error - hide modal with delay and show error
                setTimeout(() => {
                    hideModalCompletely(loadingModal);
                }, 300);
                console.error('Login error:', error);
                showAlert('danger', 'Network error or server issue. Please try again.');
            });
        });

        function showFieldError(fieldName, message) {
            const field = document.getElementById(fieldName);
            field.classList.add('is-invalid');
            
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = message;
            field.parentNode.appendChild(feedback);
        }

        function showAlert(type, message) {
            // Remove any existing alerts first
            document.querySelectorAll('.alert').forEach(alert => alert.remove());
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const form = document.getElementById('loginForm');
            form.parentNode.insertBefore(alertDiv, form);
            
            // Auto-dismiss success alerts
            if (type === 'success') {
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 3000);
            }
        }
    </script>
</body>
</html>
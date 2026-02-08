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
    <title>Register - TSU Staff Portal</title>
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
        .email-input-group {
            position: relative;
        }
        .email-domain {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
        }
        .form-control.with-domain {
            padding-right: 200px;
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
                            <h2 class="fw-bold">Create Your Profile</h2>
                            <p class="text-muted">Join the TSU Staff Profile Portal</p>
                        </div>

                        <form id="registerForm" method="POST" action="<?= url('register') ?>">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            
                            <div class="mb-3">
                                <label for="email_prefix" class="form-label">University Email</label>
                                <div class="email-input-group">
                                    <input type="text" 
                                           class="form-control form-control-lg with-domain" 
                                           id="email_prefix" 
                                           name="email_prefix" 
                                           placeholder="Enter your email prefix"
                                           required>
                                    <span class="email-domain">@tsuniversity.edu.ng</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Use your official TSU email address
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control form-control-lg" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Create a strong password"
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Must contain 8+ characters with uppercase, lowercase, and number
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control form-control-lg" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           placeholder="Confirm your password"
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Staff ID</label>
                                <div class="input-group">
                                    <select class="form-select form-select-lg" id="staff_prefix" name="staff_prefix" style="max-width: 140px;" required>
                                        <option value="">Prefix</option>
                                        <option value="TSU/SP/">TSU/SP/</option>
                                        <option value="TSU/JP/">TSU/JP/</option>
                                    </select>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="staff_number" 
                                           name="staff_number" 
                                           placeholder="Enter numbers e.g., 00123"
                                           required>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-id-card me-1"></i>
                                    Staff ID will be saved as TSU/SP/{number} or TSU/JP/{number}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="faculty" class="form-label">Faculty</label>
                                <select class="form-select form-select-lg" 
                                        id="faculty" 
                                        name="faculty" 
                                        required>
                                    <option value="">Select your faculty</option>
                                </select>
                                <div class="form-text">
                                    <i class="fas fa-building me-1"></i>
                                    Choose the faculty you belong to
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-select form-select-lg" 
                                        id="department" 
                                        name="department" 
                                        required 
                                        disabled>
                                    <option value="">Select faculty first</option>
                                </select>
                                <div class="form-text">
                                    <i class="fas fa-graduation-cap me-1"></i>
                                    Choose your specific department
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="terms" 
                                           name="terms" 
                                           required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="#" class="text-primary">Terms of Service</a> 
                                        and <a href="#" class="text-primary">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Create Account
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="mb-0">Already have an account? 
                                    <a href="<?= url('login') ?>" class="text-primary fw-bold">Sign In</a>
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
                    <p class="mb-0">Creating your account...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Faculty and Department data
        let facultyDepartmentData = {};

        // Load faculty and department data
        async function loadFacultyData() {
            try {
                const response = await fetch('<?= url('faculties-departments') ?>');
                const data = await response.json();
                
                if (data.status === 'success') {
                    facultyDepartmentData = data.data;
                    populateFacultyDropdown();
                }
            } catch (error) {
                console.error('Error loading faculty data:', error);
            }
        }

        // Populate faculty dropdown
        function populateFacultyDropdown() {
            const facultySelect = document.getElementById('faculty');
            facultySelect.innerHTML = '<option value="">Select your faculty</option>';
            
            Object.keys(facultyDepartmentData).forEach(faculty => {
                const option = document.createElement('option');
                option.value = faculty;
                option.textContent = faculty;
                facultySelect.appendChild(option);
            });
        }

        // Handle faculty selection change
        document.getElementById('faculty').addEventListener('change', function() {
            const selectedFaculty = this.value;
            const departmentSelect = document.getElementById('department');
            
            if (selectedFaculty && facultyDepartmentData[selectedFaculty]) {
                // Enable department dropdown and populate it
                departmentSelect.disabled = false;
                departmentSelect.innerHTML = '<option value="">Select your department</option>';
                
                facultyDepartmentData[selectedFaculty].forEach(department => {
                    const option = document.createElement('option');
                    option.value = department;
                    option.textContent = department;
                    departmentSelect.appendChild(option);
                });
            } else {
                // Disable department dropdown
                departmentSelect.disabled = true;
                departmentSelect.innerHTML = '<option value="">Select faculty first</option>';
            }
        });

        // Load faculty data when page loads
        document.addEventListener('DOMContentLoaded', loadFacultyData);

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

        // Toggle confirm password visibility
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const confirmPassword = document.getElementById('confirm_password');
            const icon = this.querySelector('i');
            
            if (confirmPassword.type === 'password') {
                confirmPassword.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                confirmPassword.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Helper function to forcefully hide modal and remove backdrop
        function hideModalCompletely(modal) {
            // Hide the modal
            modal.hide();
            
            // Force remove everything after a tiny delay to ensure Bootstrap finishes
            setTimeout(() => {
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
                }
            }, 10);
        }

        // Form validation and submission
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailPrefix = document.getElementById('email_prefix').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const faculty = document.getElementById('faculty').value;
            const department = document.getElementById('department').value;
            const staffPrefix = document.getElementById('staff_prefix').value;
            const staffNumber = document.getElementById('staff_number').value.trim();
            const terms = document.getElementById('terms').checked;

            // Clear previous errors
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            let hasErrors = false;

            // Validate email prefix
            if (!emailPrefix) {
                showFieldError('email_prefix', 'Email prefix is required');
                hasErrors = true;
            } else if (!/^[a-zA-Z0-9._-]+$/.test(emailPrefix)) {
                showFieldError('email_prefix', 'Email prefix can only contain letters, numbers, dots, and hyphens');
                hasErrors = true;
            }

            // Validate password
            if (!password) {
                showFieldError('password', 'Password is required');
                hasErrors = true;
            } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(password)) {
                showFieldError('password', 'Password must contain at least 8 characters with uppercase, lowercase, and number');
                hasErrors = true;
            }

            // Validate confirm password
            if (password !== confirmPassword) {
                showFieldError('confirm_password', 'Passwords do not match');
                hasErrors = true;
            }

            // Validate faculty
            if (!faculty) {
                showFieldError('faculty', 'Please select your faculty');
                hasErrors = true;
            }

            // Validate department
            if (!department) {
                showFieldError('department', 'Please select your department');
                hasErrors = true;
            }

            // Validate staff ID
            if (!staffPrefix) {
                showFieldError('staff_prefix', 'Select a staff ID prefix (SP or JP)');
                hasErrors = true;
            }
            if (!staffNumber) {
                showFieldError('staff_number', 'Enter your staff ID numbers');
                hasErrors = true;
            } else if (!/^[0-9]+$/.test(staffNumber)) {
                showFieldError('staff_number', 'Staff ID should contain numbers only');
                hasErrors = true;
            }

            // Validate terms
            if (!terms) {
                showFieldError('terms', 'You must agree to the terms and conditions');
                hasErrors = true;
            }

            if (hasErrors) {
                return;
            }

            // Show loading modal
            const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
            loadingModal.show();

            // Submit form
            const formData = new FormData(this);
            
            fetch('<?= url('register') ?>', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                try {
                    const data = await response.json();
                    return data;
                } catch (e) {
                    hideModalCompletely(loadingModal);
                    throw new Error('Invalid server response');
                }
            })
            .then(data => {
                // Always hide modal first - forcefully
                hideModalCompletely(loadingModal);
                
                if (data.success) {
                    // Show success message and redirect
                    showAlert('success', data.message);
                    setTimeout(() => {
                        const redirectPath = data.redirect || 'verify-email';
                        const baseUrl = '<?= rtrim(url(''), '/') ?>';
                        window.location.href = baseUrl + '/' + redirectPath;
                    }, 2000);
                } else if (data.errors) {
                    // Show field errors
                    Object.keys(data.errors).forEach(field => {
                        showFieldError(field, data.errors[field]);
                    });
                } else {
                    showAlert('danger', data.error || 'Registration failed. Please try again.');
                }
            })
            .catch(error => {
                // Ensure modal is hidden forcefully
                hideModalCompletely(loadingModal);
                console.error('Registration error:', error);
                showAlert('danger', 'Network error. Please check your connection and try again.');
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
            
            const form = document.getElementById('registerForm');
            form.parentNode.insertBefore(alertDiv, form);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
</body>
</html>
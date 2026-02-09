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
    <title>Profile Setup - TSU Staff Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .setup-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }
        .setup-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .step-indicator {
            background: #e9ecef;
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
        }
        .step-progress {
            background: #0d6efd;
            height: 100%;
            width: 33%;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="setup-container d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="setup-card p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-cog fa-3x text-primary mb-3"></i>
                            <h2 class="fw-bold">Complete Your Profile</h2>
                            <p class="text-muted">Let's set up your professional profile</p>
                            
                            <!-- Progress indicator -->
                            <div class="step-indicator mb-3">
                                <div class="step-progress"></div>
                            </div>
                            <small class="text-muted">Step 1 of 3: Basic Information</small>
                        </div>

                        <form id="setupForm" method="POST" action="<?= url('profile/setup') ?>" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <?php
                                $regStaffNumber = $registration_data['staff_number'] ?? '';
                                $prefillPrefix = '';
                                $prefillNumber = '';
                                if (preg_match('/^(TSU\/SP\/|TSU\/JP\/)(.+)$/', $regStaffNumber, $matches)) {
                                    $prefillPrefix = $matches[1];
                                    $prefillNumber = $matches[2];
                                }
                            ?>
                            
                            <div class="mb-3">
                                <label for="staff_number" class="form-label">Staff ID</label>
                                <div class="input-group">
                                    <select class="form-select" id="staff_prefix" name="staff_prefix" style="max-width: 140px;" required>
                                        <option value="">Prefix</option>
                                        <option value="TSU/SP/" <?= $prefillPrefix === 'TSU/SP/' ? 'selected' : '' ?>>TSU/SP/</option>
                                        <option value="TSU/JP/" <?= $prefillPrefix === 'TSU/JP/' ? 'selected' : '' ?>>TSU/JP/</option>
                                    </select>
                                    <input type="text" 
                                           class="form-control" 
                                           id="staff_number" 
                                           name="staff_number" 
                                           value="<?= htmlspecialchars($prefillNumber) ?>"
                                           placeholder="Enter numbers e.g., 00123"
                                           required>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-id-card me-1"></i>
                                    Your Staff ID will be saved as TSU/SP/{number} or TSU/JP/{number}
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <select class="form-select" id="title" name="title" required>
                                        <option value="">Select title</option>
                                        <option value="Prof.">Prof.</option>
                                        <option value="Dr.">Dr.</option>
                                        <option value="Mr.">Mr.</option>
                                        <option value="Mrs.">Mrs.</option>
                                        <option value="Ms.">Ms.</option>
                                        <option value="Engr.">Engr.</option>
                                        <option value="Arc.">Arc.</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="first_name" 
                                           name="first_name" 
                                           placeholder="Enter first name"
                                           required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="last_name" 
                                           name="last_name" 
                                           placeholder="Enter last name"
                                           required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="middle_name" class="form-label">Middle Name <small class="text-muted">(Optional)</small></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="middle_name" 
                                       name="middle_name" 
                                       placeholder="Enter middle name">
                            </div>

                            <!-- Profile Photo Upload -->
                            <div class="mb-3">
                                <label for="profile_photo" class="form-label">Profile Photo <small class="text-muted">(Optional)</small></label>
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div id="photo-preview" class="bg-light border rounded d-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px;">
                                            <i class="fas fa-camera fa-2x text-muted"></i>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <input type="file" 
                                               class="form-control" 
                                               id="profile_photo" 
                                               name="profile_photo" 
                                               accept="image/jpeg,image/jpg,image/png">
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Upload a professional photo (JPG, PNG, max 2MB)
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Faculty and Department are already set from registration -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Faculty</label>
                                    <div class="form-control-plaintext bg-light rounded p-2">
                                        <i class="fas fa-building me-2 text-primary"></i>
                                        <?= htmlspecialchars($registration_data['faculty'] ?? 'Not specified') ?>
                                    </div>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Selected during registration
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Department</label>
                                    <div class="form-control-plaintext bg-light rounded p-2">
                                        <i class="fas fa-graduation-cap me-2 text-primary"></i>
                                        <?= htmlspecialchars($registration_data['department'] ?? 'Not specified') ?>
                                    </div>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Selected during registration
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="designation" class="form-label">Designation/Position</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="designation" 
                                       name="designation" 
                                       placeholder="e.g., Senior Lecturer, Professor, Administrative Officer"
                                       required>
                                <div class="form-text">
                                    <i class="fas fa-briefcase me-1"></i>
                                    Your current position at TSU
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="blood_group" class="form-label">Blood Group</label>
                                <select class="form-select" id="blood_group" name="blood_group" required>
                                    <option value="">Select Blood Group</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                                <div class="form-text">
                                    <i class="fas fa-tint me-1"></i>
                                    Required for ID card and emergency purposes
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="office_location" class="form-label">Office Location <small class="text-muted">(Optional)</small></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="office_location" 
                                           name="office_location" 
                                           placeholder="e.g., Room 101, Faculty Building">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="office_phone" class="form-label">Office Phone <small class="text-muted">(Optional)</small></label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="office_phone" 
                                           name="office_phone" 
                                           placeholder="e.g., +234 xxx xxx xxxx">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="professional_summary" class="form-label">Professional Summary <small class="text-muted">(Optional)</small></label>
                                <textarea class="form-control" 
                                          id="professional_summary" 
                                          name="professional_summary" 
                                          rows="4" 
                                          placeholder="Brief description of your expertise, research interests, and professional background..."></textarea>
                                <div class="form-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    This will be displayed on your public profile
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Complete Profile Setup
                                </button>
                            </div>

                            <div class="text-center">
                                <small class="text-muted">
                                    You can add more details like education, experience, and publications later from your dashboard.
                                </small>
                            </div>
                        </form>
                    </div>

                    <div class="text-center mt-4">
                        <a href="<?= url('logout') ?>" class="text-white text-decoration-none">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
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
                    <p class="mb-0">Setting up your profile...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Photo preview functionality
        document.getElementById('profile_photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('photo-preview');
            
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPG, PNG)');
                    this.value = '';
                    return;
                }
                
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    this.value = '';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="rounded" style="width: 100%; height: 100%; object-fit: cover;">`;
                };
                reader.readAsDataURL(file);
            } else {
                // Reset preview
                preview.innerHTML = '<i class="fas fa-camera fa-2x text-muted"></i>';
            }
        });

        // Form submission
        document.getElementById('setupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            // Show loading modal
            const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
            loadingModal.show();

            // Safety timeout to hide modal after 30 seconds
            const safetyTimeout = setTimeout(() => {
                loadingModal.hide();
                showAlert('warning', 'Request is taking longer than expected. Please check your connection.');
            }, 30000);

            // Submit form
            const formData = new FormData(this);
            
            fetch('<?= url('profile/setup') ?>', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                try {
                    const contentType = response.headers.get('content-type');
                    
                    // Check if response is JSON
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        console.error('Non-JSON response received:', text.substring(0, 500));
                        loadingModal.hide();
                        throw new Error('Server returned invalid response. Please check server logs or contact administrator.');
                    }
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        loadingModal.hide();
                        throw new Error(data.error || 'Server error occurred');
                    }
                    
                    return data;
                } catch (parseError) {
                    loadingModal.hide();
                    throw parseError;
                }
            })
            .then(data => {
                clearTimeout(safetyTimeout);
                loadingModal.hide();
                
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        const redirectPath = data.redirect || 'dashboard';
                        const baseUrl = '<?= rtrim(url(''), '/') ?>';
                        window.location.href = baseUrl + '/' + redirectPath;
                    }, 2000);
                } else if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        showFieldError(field, data.errors[field]);
                    });
                } else {
                    showAlert('danger', data.error || 'Profile setup failed. Please try again.');
                }
            })
            .catch(error => {
                clearTimeout(safetyTimeout);
                loadingModal.hide();
                console.error('Setup error:', error);
                showAlert('danger', error.message || 'Network error or server issue. Please try again.');
            });
        });

        function showFieldError(fieldName, message) {
            const field = document.getElementById(fieldName);
            if (field) {
                field.classList.add('is-invalid');
                
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = message;
                field.parentNode.appendChild(feedback);
            }
        }

        function showAlert(type, message) {
            // Remove existing alerts
            document.querySelectorAll('.alert').forEach(alert => alert.remove());
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const form = document.getElementById('setupForm');
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
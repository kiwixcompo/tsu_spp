<?php
// Load helpers if not already loaded
if (!function_exists('url')) {
    require_once __DIR__ . '/../../Helpers/UrlHelper.php';
}
if (!function_exists('escape_html')) {
    require_once __DIR__ . '/../../Helpers/TextHelper.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - TSU Staff Profile Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #1e40af;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 0;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        .main-content {
            background: #f8fafc;
            min-height: 100vh;
        }
        .form-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .section-header {
            background: #f8f9fa;
            border-radius: 12px 12px 0 0;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <div class="text-center mb-4">
                        <i class="fas fa-university fa-2x text-white mb-2"></i>
                        <h5 class="text-white mb-0">TSU Staff Profile Portal</h5>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link" href="<?= url('dashboard') ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link active" href="<?= url('profile/edit') ?>">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </a>
                        <a class="nav-link" href="<?= url('profile/education') ?>">
                            <i class="fas fa-graduation-cap me-2"></i>Education
                        </a>
                        <a class="nav-link" href="<?= url('profile/experience') ?>">
                            <i class="fas fa-briefcase me-2"></i>Experience
                        </a>
                        <a class="nav-link" href="<?= url('profile/skills') ?>">
                            <i class="fas fa-cogs me-2"></i>Skills
                        </a>
                        <a class="nav-link" href="<?= url('settings') ?>">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                        <hr class="text-white-50">
                        <a class="nav-link" href="<?= url('logout') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header -->
                <div class="bg-white border-bottom p-3 mb-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="mb-0">Edit Profile</h4>
                            <p class="text-muted mb-0">Update your professional information</p>
                        </div>
                        <div class="col-auto">
                            <a href="<?= url('dashboard') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <!-- Alert Container -->
                    <div id="alert-container"></div>

                    <form id="profileForm" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                        <!-- Profile Photo & CV -->
                        <div class="form-section">
                            <div class="section-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-image me-2"></i>Profile Photo & Documents
                                </h5>
                            </div>
                            <div class="p-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Current Profile Photo</label>
                                        <div class="text-center mb-3">
                                            <div id="currentPhotoPreview">
                                                <?php if (!empty($profile['profile_photo'])): ?>
                                                    <img src="<?= url('storage/uploads/' . $profile['profile_photo']) ?>" 
                                                         alt="Current Photo" 
                                                         class="rounded-circle" 
                                                         style="width: 120px; height: 120px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light border rounded-circle d-inline-flex align-items-center justify-content-center" 
                                                         style="width: 120px; height: 120px;">
                                                        <i class="fas fa-user fa-3x text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="profile_photo" class="form-label">Update Profile Photo</label>
                                            <input type="file" class="form-control" id="profile_photo" name="profile_photo" 
                                                   accept="image/jpeg,image/jpg,image/png">
                                            <div class="form-text">Accepted formats: JPG, JPEG, PNG. Max size: 2MB</div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Current CV</label>
                                        <div class="mb-3">
                                            <?php if (!empty($profile['cv_file'])): ?>
                                                <div class="alert alert-info">
                                                    <i class="fas fa-file-pdf me-2"></i>
                                                    <strong>Current CV:</strong> <?= htmlspecialchars($profile['cv_file']) ?>
                                                    <br>
                                                    <a href="<?= url('storage/uploads/' . $profile['cv_file']) ?>" 
                                                       target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                                        <i class="fas fa-download me-1"></i>Download Current CV
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-light">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    No CV uploaded yet
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mb-3">
                                            <label for="cv_file" class="form-label">Upload New CV</label>
                                            <input type="file" class="form-control" id="cv_file" name="cv_file" 
                                                   accept=".pdf,.doc,.docx">
                                            <div class="form-text">Accepted formats: PDF, DOC, DOCX. Max size: 5MB</div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="form-section">
                            <div class="section-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user me-2"></i>Basic Information
                                </h5>
                            </div>
                            <div class="p-4">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="title" class="form-label">Title *</label>
                                        <select class="form-select" id="title" name="title" required>
                                            <option value="">Select Title</option>
                                            <option value="Prof." <?= ($profile['title'] ?? '') === 'Prof.' ? 'selected' : '' ?>>Prof.</option>
                                            <option value="Dr." <?= ($profile['title'] ?? '') === 'Dr.' ? 'selected' : '' ?>>Dr.</option>
                                            <option value="Mr." <?= ($profile['title'] ?? '') === 'Mr.' ? 'selected' : '' ?>>Mr.</option>
                                            <option value="Mrs." <?= ($profile['title'] ?? '') === 'Mrs.' ? 'selected' : '' ?>>Mrs.</option>
                                            <option value="Ms." <?= ($profile['title'] ?? '') === 'Ms.' ? 'selected' : '' ?>>Ms.</option>
                                            <option value="Engr." <?= ($profile['title'] ?? '') === 'Engr.' ? 'selected' : '' ?>>Engr.</option>
                                            <option value="Arc." <?= ($profile['title'] ?? '') === 'Arc.' ? 'selected' : '' ?>>Arc.</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="first_name" class="form-label">First Name *</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" 
                                               value="<?= safe_output($profile['first_name'] ?? '') ?>" 
                                               placeholder="First Name" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="middle_name" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" id="middle_name" name="middle_name" 
                                               value="<?= safe_output($profile['middle_name'] ?? '') ?>" 
                                               placeholder="Middle Name (Optional)">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="last_name" class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" 
                                               value="<?= safe_output($profile['last_name'] ?? '') ?>" 
                                               placeholder="Last Name" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="staff_number" class="form-label">Staff Number *</label>
                                        <div class="input-group">
                                            <select class="form-select" id="staff_prefix" name="staff_prefix" style="max-width: 120px;" required>
                                                <?php
                                                $currentStaffNumber = $profile['staff_number'] ?? '';
                                                $currentPrefix = '';
                                                $currentNumber = '';
                                                if (preg_match('/^(TSU\/SP\/|TSU\/JP\/)(.+)$/', $currentStaffNumber, $matches)) {
                                                    $currentPrefix = $matches[1];
                                                    $currentNumber = $matches[2];
                                                }
                                                ?>
                                                <option value="TSU/SP/" <?= $currentPrefix === 'TSU/SP/' ? 'selected' : '' ?>>TSU/SP/</option>
                                                <option value="TSU/JP/" <?= $currentPrefix === 'TSU/JP/' ? 'selected' : '' ?>>TSU/JP/</option>
                                            </select>
                                            <input type="text" class="form-control" id="staff_number" name="staff_number" 
                                                   value="<?= htmlspecialchars($currentNumber) ?>" 
                                                   placeholder="e.g., 001" required>
                                        </div>
                                        <div class="form-text">Your official staff ID number</div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="designation" class="form-label">Job Title *</label>
                                        <input type="text" class="form-control" id="designation" name="designation" 
                                               value="<?= safe_output($profile['designation'] ?? '') ?>" 
                                               placeholder="e.g., Senior Lecturer, Professor" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="blood_group" class="form-label">Blood Group *</label>
                                        <select class="form-select" id="blood_group" name="blood_group" required>
                                            <option value="">Select Blood Group</option>
                                            <option value="A+" <?= ($profile['blood_group'] ?? '') === 'A+' ? 'selected' : '' ?>>A+</option>
                                            <option value="A-" <?= ($profile['blood_group'] ?? '') === 'A-' ? 'selected' : '' ?>>A-</option>
                                            <option value="B+" <?= ($profile['blood_group'] ?? '') === 'B+' ? 'selected' : '' ?>>B+</option>
                                            <option value="B-" <?= ($profile['blood_group'] ?? '') === 'B-' ? 'selected' : '' ?>>B-</option>
                                            <option value="AB+" <?= ($profile['blood_group'] ?? '') === 'AB+' ? 'selected' : '' ?>>AB+</option>
                                            <option value="AB-" <?= ($profile['blood_group'] ?? '') === 'AB-' ? 'selected' : '' ?>>AB-</option>
                                            <option value="O+" <?= ($profile['blood_group'] ?? '') === 'O+' ? 'selected' : '' ?>>O+</option>
                                            <option value="O-" <?= ($profile['blood_group'] ?? '') === 'O-' ? 'selected' : '' ?>>O-</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="faculty" class="form-label">Faculty *</label>
                                        <select class="form-select" id="faculty" name="faculty" required>
                                            <option value="">Select Faculty</option>
                                            <?php foreach ($faculties as $faculty): ?>
                                                <option value="<?= htmlspecialchars($faculty['name']) ?>" 
                                                        <?= ($profile['faculty'] ?? '') === $faculty['name'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($faculty['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="department" class="form-label">Department *</label>
                                        <select class="form-select" id="department" name="department" required>
                                            <option value="">Select Department</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="office_location" class="form-label">Office Location</label>
                                        <input type="text" class="form-control" id="office_location" name="office_location" 
                                               value="<?= htmlspecialchars($profile['office_location'] ?? '') ?>">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="office_phone" class="form-label">Office Phone</label>
                                        <input type="tel" class="form-control" id="office_phone" name="office_phone" 
                                               value="<?= htmlspecialchars($profile['office_phone'] ?? '') ?>">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Summary -->
                        <div class="form-section">
                            <div class="section-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-align-left me-2"></i>Professional Summary
                                </h5>
                            </div>
                            <div class="p-4">
                                <div class="mb-3">
                                    <label for="professional_summary" class="form-label">Professional Summary</label>
                                    <textarea class="form-control" id="professional_summary" name="professional_summary" rows="4" 
                                              placeholder="Describe your professional background, expertise, and career highlights..."><?= safe_output($profile['professional_summary'] ?? '') ?></textarea>
                                    <div class="form-text">Maximum 1000 characters</div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="research_interests" class="form-label">Research Interests</label>
                                        <textarea class="form-control" id="research_interests" name="research_interests" rows="3" 
                                                  placeholder="e.g., Machine Learning, Data Science, Artificial Intelligence"><?= htmlspecialchars($profile['research_interests'] ?? '') ?></textarea>
                                        <div class="form-text">Separate each interest with a comma</div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="expertise_keywords" class="form-label">Areas of Expertise</label>
                                        <textarea class="form-control" id="expertise_keywords" name="expertise_keywords" rows="3" 
                                                  placeholder="e.g., Python Programming, Statistical Analysis, Research Methodology"><?= htmlspecialchars($profile['expertise_keywords'] ?? '') ?></textarea>
                                        <div class="form-text">Separate each expertise with a comma</div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Faculty and Department data
        const facultiesData = <?= json_encode($faculties) ?>;
        
        // Populate departments based on selected faculty
        function populateDepartments(selectedFaculty, selectedDepartment = '') {
            const departmentSelect = document.getElementById('department');
            departmentSelect.innerHTML = '<option value="">Select Department</option>';
            
            const faculty = facultiesData.find(f => f.name === selectedFaculty);
            if (faculty && faculty.departments) {
                faculty.departments.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept;
                    option.textContent = dept;
                    if (dept === selectedDepartment) {
                        option.selected = true;
                    }
                    departmentSelect.appendChild(option);
                });
            }
        }

        // Initialize departments on page load
        document.addEventListener('DOMContentLoaded', function() {
            const facultySelect = document.getElementById('faculty');
            const selectedFaculty = facultySelect.value;
            const selectedDepartment = <?= json_encode($profile['department'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
            
            if (selectedFaculty) {
                populateDepartments(selectedFaculty, selectedDepartment);
            }
        });

        // Handle faculty change
        document.getElementById('faculty').addEventListener('change', function() {
            populateDepartments(this.value);
        });

        // Photo preview functionality
        document.getElementById('profile_photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('currentPhotoPreview').innerHTML = 
                        `<img src="${e.target.result}" alt="Preview" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">`;
                };
                reader.readAsDataURL(file);
            }
        });

        // Alert functions
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alert-container');
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.appendChild(alertDiv);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        function clearFieldErrors() {
            document.querySelectorAll('.is-invalid').forEach(field => {
                field.classList.remove('is-invalid');
            });
        }

        function showFieldError(fieldName, message) {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.add('is-invalid');
                const feedback = field.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = message;
                }
            }
        }

        // Form submission
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            // Clear previous errors
            clearFieldErrors();
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
            
            // Prepare form data
            const formData = new FormData(this);
            
            // Submit form
            fetch('<?= url('profile/update') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    // Scroll to top to show success message
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        showFieldError(field, data.errors[field]);
                    });
                    showAlert('danger', 'Please correct the errors below.');
                } else {
                    showAlert('danger', data.error || 'An error occurred while updating your profile.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An unexpected error occurred. Please try again.');
            })
            .finally(() => {
                // Restore button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    </script>
</body>
</html>
<?php
// Load URL helper and Security helper
if (!function_exists('url')) {
    require_once __DIR__ . '/../../Helpers/UrlHelper.php';
}
require_once __DIR__ . '/../../Helpers/SecurityHelper.php';
use App\Helpers\SecurityHelper;

// Set security headers
SecurityHelper::setSecurityHeaders();
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
            padding: 40px 0;
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
        .staff-type-card {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .staff-type-card:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        .staff-type-card.selected {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        .staff-type-card input[type="radio"] {
            width: 20px;
            height: 20px;
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .toggle-slider {
            background-color: #3b82f6;
        }
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        .field-group {
            display: none;
        }
        .field-group.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="auth-container d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-7">
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
                            
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email_prefix" class="form-label">University Email *</label>
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
                            <!-- Staff Number -->
<div class="mb-3">
    <label class="form-label">Staff Number *</label>
    <div class="row">
        <div class="col-md-4">
            <select class="form-select form-select-lg" 
                    id="staff_prefix" 
                    name="staff_prefix" 
                    required>
                <option value="">Prefix</option>
                <option value="TSU/SP/">TSU/SP/</option>
                <option value="TSU/JP/">TSU/JP/</option>
            </select>
            <div class="form-text">
                <small>SP or JP</small>
            </div>
        </div>
        <div class="col-md-8">
            <input type="text" 
                   class="form-control form-control-lg" 
                   id="staff_number" 
                   name="staff_number" 
                   placeholder="Enter staff number (numbers only)"
                   pattern="[0-9]+"
                   title="Please enter numbers only"
                   required>
            <div class="form-text">
                <small>Example: For TSU/SP/654, enter 654</small>
            </div>
        </div>
    </div>
</div>

                            <!-- Password -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password *</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control form-control-lg" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Create password"
                                               required>
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        8+ chars, uppercase, lowercase, number
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password *</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control form-control-lg" 
                                               id="confirm_password" 
                                               name="confirm_password" 
                                               placeholder="Confirm password"
                                               required>
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                id="toggleConfirmPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Staff Type Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Staff Type *</label>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="staff-type-card" onclick="selectStaffType('teaching')">
                                            <div class="d-flex align-items-center">
                                                <input type="radio" 
                                                       name="staff_type" 
                                                       id="staff_type_teaching" 
                                                       value="teaching" 
                                                       class="me-3"
                                                       checked
                                                       required>
                                                <div>
                                                    <h6 class="mb-1"><i class="fas fa-chalkboard-teacher me-2"></i>Teaching Staff</h6>
                                                    <small class="text-muted">Academic/Lecturing staff</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="staff-type-card" onclick="selectStaffType('non-teaching')">
                                            <div class="d-flex align-items-center">
                                                <input type="radio" 
                                                       name="staff_type" 
                                                       id="staff_type_non_teaching" 
                                                       value="non-teaching" 
                                                       class="me-3"
                                                       required>
                                                <div>
                                                    <h6 class="mb-1"><i class="fas fa-briefcase me-2"></i>Non-Teaching Staff</h6>
                                                    <small class="text-muted">Administrative/Support staff</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Teaching Staff Fields -->
                            <div id="teaching-fields" class="field-group active">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="faculty" class="form-label">Faculty *</label>
                                        <select class="form-select form-select-lg" 
                                                id="faculty" 
                                                name="faculty">
                                            <option value="">Select your faculty</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="department" class="form-label">Department *</label>
                                        <select class="form-select form-select-lg" 
                                                id="department" 
                                                name="department" 
                                                disabled>
                                            <option value="">Select faculty first</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Non-Teaching Staff Fields -->
                            <div id="non-teaching-fields" class="field-group">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Non-Teaching Staff:</strong> Select your Directorate, then choose your Unit within it.
                                </div>

                                <div class="mb-3">
                                    <label for="directorate" class="form-label">Directorate *</label>
                                    <select class="form-select form-select-lg" id="directorate" name="directorate">
                                        <option value="">Select your directorate</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="directorate_unit" class="form-label">Unit <span class="text-muted">(Optional)</span></label>
                                    <select class="form-select form-select-lg" id="directorate_unit" name="directorate_unit" disabled>
                                        <option value="">Select directorate first</option>
                                    </select>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Some directorates have a single unit — select it if applicable.
                                    </div>
                                </div>

                                <div id="non-teaching-error" class="alert alert-danger" style="display:none;">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Please select a Directorate.
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Profile Visibility -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <label class="form-label fw-bold mb-1">Profile Visibility</label>
                                        <div class="form-text">
                                            <i class="fas fa-eye me-1"></i>
                                            <span id="visibility-text">Your profile will be visible in the staff directory</span>
                                        </div>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" 
                                               id="profile_visibility" 
                                               name="profile_visibility" 
                                               value="public"
                                               checked
                                               onchange="updateVisibilityText()">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="alert alert-info mt-2" id="visibility-info">
                                    <small>
                                        <strong>Public:</strong> Your profile will appear in the staff directory and can be viewed by anyone.<br>
                                        <strong>Private:</strong> Your profile will be hidden from the directory and only visible to you.
                                    </small>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="terms" 
                                           name="terms" 
                                           required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="<?= url('terms') ?>" target="_blank">Terms and Conditions</a> and <a href="<?= url('privacy-policy') ?>" target="_blank">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>

                            <div class="text-center">
                                <p class="mb-0">
                                    Already have an account? 
                                    <a href="<?= url('login') ?>" class="text-decoration-none">Login here</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Faculty and Department data
        let facultyDepartmentData = {};
        // Directorate → Units data
        let directoratesData = {};

        document.addEventListener('DOMContentLoaded', function() {
            loadFacultyData();
            loadDirectoratesData();
        });

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

        async function loadDirectoratesData() {
            try {
                const response = await fetch('<?= url('directorates-units') ?>');
                const data = await response.json();
                if (data.status === 'success') {
                    directoratesData = data.data;
                    populateDirectoratesDropdown();
                }
            } catch (error) {
                console.error('Error loading directorates data:', error);
            }
        }

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

        function populateDirectoratesDropdown() {
            const dirSelect = document.getElementById('directorate');
            dirSelect.innerHTML = '<option value="">Select your directorate</option>';
            Object.keys(directoratesData).forEach(name => {
                const option = document.createElement('option');
                option.value = name;
                option.textContent = name;
                dirSelect.appendChild(option);
            });
        }

        document.getElementById('faculty').addEventListener('change', function() {
            const departmentSelect = document.getElementById('department');
            if (this.value && facultyDepartmentData[this.value]) {
                departmentSelect.disabled = false;
                departmentSelect.innerHTML = '<option value="">Select your department</option>';
                facultyDepartmentData[this.value].forEach(dept => {
                    const opt = document.createElement('option');
                    opt.value = dept; opt.textContent = dept;
                    departmentSelect.appendChild(opt);
                });
            } else {
                departmentSelect.disabled = true;
                departmentSelect.innerHTML = '<option value="">Select faculty first</option>';
            }
        });

        document.getElementById('directorate').addEventListener('change', function() {
            const unitSelect = document.getElementById('directorate_unit');
            const units = directoratesData[this.value]?.units || [];
            if (this.value && units.length > 0) {
                unitSelect.disabled = false;
                unitSelect.innerHTML = '<option value="">Select your unit</option>';
                units.forEach(u => {
                    const opt = document.createElement('option');
                    opt.value = u; opt.textContent = u;
                    unitSelect.appendChild(opt);
                });
            } else {
                unitSelect.disabled = true;
                unitSelect.innerHTML = '<option value="">No units available</option>';
            }
        });

        function selectStaffType(type) {
            if (type === 'teaching') {
                document.getElementById('staff_type_teaching').checked = true;
            } else {
                document.getElementById('staff_type_non_teaching').checked = true;
            }
            document.querySelectorAll('.staff-type-card').forEach(card => card.classList.remove('selected'));
            event.currentTarget.classList.add('selected');

            const teachingFields = document.getElementById('teaching-fields');
            const nonTeachingFields = document.getElementById('non-teaching-fields');
            if (type === 'teaching') {
                teachingFields.classList.add('active');
                nonTeachingFields.classList.remove('active');
                document.getElementById('faculty').required = true;
                document.getElementById('department').required = true;
                document.getElementById('directorate').required = false;
            } else {
                teachingFields.classList.remove('active');
                nonTeachingFields.classList.add('active');
                document.getElementById('faculty').required = false;
                document.getElementById('department').required = false;
                document.getElementById('directorate').required = false;
            }
        }

        function updateVisibilityText() {
            const checkbox = document.getElementById('profile_visibility');
            const text = document.getElementById('visibility-text');
            if (checkbox.checked) {
                text.innerHTML = '<i class="fas fa-eye me-1"></i>Your profile will be visible in the staff directory';
                checkbox.value = 'public';
            } else {
                text.innerHTML = '<i class="fas fa-eye-slash me-1"></i>Your profile will be hidden from the staff directory';
                checkbox.value = 'private';
            }
        }

        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            password.type = password.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye'); icon.classList.toggle('fa-eye-slash');
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const password = document.getElementById('confirm_password');
            const icon = this.querySelector('i');
            password.type = password.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye'); icon.classList.toggle('fa-eye-slash');
        });

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }

            const staffType = document.querySelector('input[name="staff_type"]:checked').value;
            if (staffType === 'teaching') {
                if (!document.getElementById('faculty').value || !document.getElementById('department').value) {
                    alert('Please select both faculty and department for teaching staff.');
                    return;
                }
            } else {
                if (!document.getElementById('directorate').value) {
                    document.getElementById('non-teaching-error').style.display = 'block';
                    alert('Please select a Directorate.');
                    return;
                }
                document.getElementById('non-teaching-error').style.display = 'none';
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Account...';

            const formData = new FormData(this);

            fetch('<?= url('register') ?>', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `<i class="fas fa-check-circle me-2"></i><strong>Success!</strong> ${data.message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                    this.insertBefore(alertDiv, this.firstChild);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    setTimeout(() => { window.location.href = data.redirect; }, 2000);
                } else if (data.errors) {
                    let errorMessage = '<ul class="mb-0">';
                    for (const [, message] of Object.entries(data.errors)) {
                        errorMessage += `<li>${message}</li>`;
                    }
                    errorMessage += '</ul>';
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                    alertDiv.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i><strong>Validation Error:</strong>${errorMessage}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                    this.insertBefore(alertDiv, this.firstChild);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    submitBtn.disabled = false; submitBtn.innerHTML = originalBtnText;
                } else {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                    alertDiv.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i><strong>Error:</strong> ${data.error || 'Registration failed. Please try again.'}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                    this.insertBefore(alertDiv, this.firstChild);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    submitBtn.disabled = false; submitBtn.innerHTML = originalBtnText;
                }
            })
            .catch(() => {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                alertDiv.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i><strong>Error:</strong> An unexpected error occurred. Please try again.<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                this.insertBefore(alertDiv, this.firstChild);
                window.scrollTo({ top: 0, behavior: 'smooth' });
                submitBtn.disabled = false; submitBtn.innerHTML = originalBtnText;
            });
        });
    </script>
</body>
</html>
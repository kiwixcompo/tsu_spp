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
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 0.5rem;
            font-weight: bold;
            color: #6c757d;
        }
        .step.active {
            background: #0d6efd;
            color: white;
        }
        .step.completed {
            background: #198754;
            color: white;
        }
        .form-section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .section-title {
            color: #0d6efd;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .photo-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .photo-upload-area:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }
        .photo-upload-area.dragover {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }
        .current-photo {
            max-width: 150px;
            max-height: 150px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .photo-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            margin-top: 1rem;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= url('/') ?>">
                <i class="fas fa-university me-2"></i>TSU Staff Portal
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= url('/dashboard') ?>">Dashboard</a>
                <a class="nav-link" href="<?= url('/auth/logout') ?>">Logout</a>
            </div>
        </div>
    </nav>

    <div class="setup-container">
        <div class="text-center mb-4">
            <h1 class="h2">Complete Your Profile</h1>
            <p class="text-muted">Please provide your professional information to create your staff profile.</p>
        </div>

        <div class="step-indicator">
            <div class="step active">1</div>
            <div class="step">2</div>
            <div class="step">3</div>
        </div>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('/profile/setup') ?>" id="profileForm" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            
            <!-- Profile Photo -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-camera me-2"></i>Profile Photo
                </h3>
                
                <div class="row">
                    <div class="col-md-4">
                        <?php if (!empty($profile['profile_photo'])): ?>
                            <div class="text-center mb-3">
                                <img src="<?= url('/' . $profile['profile_photo']) ?>" alt="Current Profile Photo" class="current-photo">
                                <p class="text-muted mt-2">Current Photo</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <div class="photo-upload-area" id="photoUploadArea">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <h5>Upload Profile Photo</h5>
                            <p class="text-muted mb-3">Drag and drop your photo here, or click to browse</p>
                            <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="d-none">
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('profile_photo').click()">
                                <i class="fas fa-folder-open me-2"></i>Choose File
                            </button>
                            <div class="mt-2">
                                <small class="text-muted">Supported formats: JPG, JPEG, PNG, GIF (Max: 2MB)</small>
                            </div>
                        </div>
                        <div id="photoPreview" class="text-center" style="display: none;">
                            <img id="previewImage" class="photo-preview">
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removePhotoPreview()">
                                    <i class="fas fa-times me-1"></i>Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-user me-2"></i>Basic Information
                </h3>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
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
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name" 
                                   value="<?= htmlspecialchars($profile['middle_name'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="staff_id" class="form-label">Staff ID</label>
                            <input type="text" class="form-control" id="staff_id" name="staff_id" 
                                   value="<?= htmlspecialchars($profile['staff_id'] ?? '') ?>" 
                                   placeholder="e.g., TSU/2023/001">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="office_phone" class="form-label">Office Phone</label>
                            <input type="tel" class="form-control" id="office_phone" name="office_phone" 
                                   value="<?= htmlspecialchars($profile['office_phone'] ?? '') ?>" 
                                   placeholder="e.g., +234-xxx-xxx-xxxx">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-briefcase me-2"></i>Professional Information
                </h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="faculty" class="form-label">Faculty *</label>
                            <select class="form-select" id="faculty" name="faculty" required>
                                <option value="">Select Faculty</option>
                                <!-- Faculty options will be populated by JavaScript -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="department" class="form-label">Department *</label>
                            <select class="form-select" id="department" name="department" required>
                                <option value="">Select Department</option>
                                <!-- Department options will be populated by JavaScript -->
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="designation" class="form-label">Designation/Position *</label>
                            <input type="text" class="form-control" id="designation" name="designation" 
                                   value="<?= htmlspecialchars($profile['designation'] ?? '') ?>" 
                                   placeholder="e.g., Senior Lecturer, Professor, etc." required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="office_location" class="form-label">Office Location</label>
                            <input type="text" class="form-control" id="office_location" name="office_location" 
                                   value="<?= htmlspecialchars($profile['office_location'] ?? '') ?>" 
                                   placeholder="e.g., Room 101, Faculty Building">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Summary -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-file-text me-2"></i>Professional Summary
                </h3>
                
                <div class="mb-3">
                    <label for="professional_summary" class="form-label">Professional Summary</label>
                    <textarea class="form-control" id="professional_summary" name="professional_summary" 
                              rows="4" placeholder="Brief overview of your professional background and expertise..."><?= htmlspecialchars($profile['professional_summary'] ?? '') ?></textarea>
                    <div class="form-text">Provide a brief overview of your professional background, expertise, and career highlights.</div>
                </div>

                <div class="mb-3">
                    <label for="research_interests" class="form-label">Research Interests</label>
                    <textarea class="form-control" id="research_interests" name="research_interests" 
                              rows="3" placeholder="Your research areas and interests..."><?= htmlspecialchars($profile['research_interests'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="expertise_keywords" class="form-label">Expertise Keywords</label>
                    <input type="text" class="form-control" id="expertise_keywords" name="expertise_keywords" 
                           value="<?= htmlspecialchars($profile['expertise_keywords'] ?? '') ?>" 
                           placeholder="e.g., Machine Learning, Data Science, Software Engineering">
                    <div class="form-text">Enter keywords separated by commas to help others find your expertise.</div>
                </div>
            </div>

            <!-- Profile Settings -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-cog me-2"></i>Profile Settings
                </h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="profile_visibility" class="form-label">Profile Visibility</label>
                            <select class="form-select" id="profile_visibility" name="profile_visibility">
                                <option value="public" <?= ($profile['profile_visibility'] ?? 'public') === 'public' ? 'selected' : '' ?>>Public - Visible to everyone</option>
                                <option value="university" <?= ($profile['profile_visibility'] ?? '') === 'university' ? 'selected' : '' ?>>University Only - Visible to TSU community</option>
                                <option value="private" <?= ($profile['profile_visibility'] ?? '') === 'private' ? 'selected' : '' ?>>Private - Only visible to you</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="allow_contact" name="allow_contact" 
                                       value="1" <?= ($profile['allow_contact'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="allow_contact">
                                    Allow others to contact me through the portal
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-save me-2"></i>Save Profile
                </button>
                <a href="<?= url('/dashboard') ?>" class="btn btn-outline-secondary btn-lg px-5 ms-3">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Faculty and Department data - will be loaded via AJAX
        let facultiesData = [];
        
        // Load faculties and departments
        async function loadFacultiesData() {
            try {
                const response = await fetch('<?= url('/api/faculties') ?>');
                if (response.ok) {
                    facultiesData = await response.json();
                    populateFaculties();
                } else {
                    console.error('Failed to load faculties data');
                }
            } catch (error) {
                console.error('Error loading faculties:', error);
                // Fallback to static data if API fails
                loadStaticFaculties();
            }
        }
        
        // Fallback static data
        function loadStaticFaculties() {
            facultiesData = [
                {
                    name: 'Faculty of Sciences',
                    departments: ['B.Sc. Computer Science', 'B.Sc. Mathematics', 'B.Sc. Physics', 'B.Sc. Chemistry']
                },
                {
                    name: 'Faculty of Engineering',
                    departments: ['B.Eng. Civil Engineering', 'B.Eng. Electrical Engineering', 'B.Eng. Mechanical Engineering']
                }
            ];
            populateFaculties();
        }
        
        // Photo upload functionality
        const photoUploadArea = document.getElementById('photoUploadArea');
        const photoInput = document.getElementById('profile_photo');
        const photoPreview = document.getElementById('photoPreview');
        const previewImage = document.getElementById('previewImage');
        
        // Handle drag and drop
        photoUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        photoUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });
        
        photoUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handlePhotoUpload(files[0]);
            }
        });
        
        // Handle file input change
        photoInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handlePhotoUpload(e.target.files[0]);
            }
        });
        
        // Handle photo upload
        function handlePhotoUpload(file) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (JPG, JPEG, PNG, or GIF).');
                return;
            }
            
            // Validate file size (2MB)
            if (file.size > 2097152) {
                alert('File size must be less than 2MB.');
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                photoPreview.style.display = 'block';
                photoUploadArea.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
        
        // Remove photo preview
        function removePhotoPreview() {
            photoPreview.style.display = 'none';
            photoUploadArea.style.display = 'block';
            photoInput.value = '';
        }
        
        // Populate faculty dropdown
        function populateFaculties() {
            const facultySelect = document.getElementById('faculty');
            const currentFaculty = '<?= htmlspecialchars($profile['faculty'] ?? '') ?>';
            
            facultiesData.forEach(faculty => {
                const option = document.createElement('option');
                option.value = faculty.name;
                option.textContent = faculty.name;
                if (faculty.name === currentFaculty) {
                    option.selected = true;
                }
                facultySelect.appendChild(option);
            });
            
            // Populate departments if faculty is already selected
            if (currentFaculty) {
                populateDepartments(currentFaculty);
            }
        }
        
        // Populate department dropdown based on selected faculty
        function populateDepartments(selectedFaculty) {
            const departmentSelect = document.getElementById('department');
            const currentDepartment = '<?= htmlspecialchars($profile['department'] ?? '') ?>';
            
            // Clear existing options
            departmentSelect.innerHTML = '<option value="">Select Department</option>';
            
            const faculty = facultiesData.find(f => f.name === selectedFaculty);
            if (faculty) {
                faculty.departments.forEach(department => {
                    const option = document.createElement('option');
                    option.value = department;
                    option.textContent = department;
                    if (department === currentDepartment) {
                        option.selected = true;
                    }
                    departmentSelect.appendChild(option);
                });
            }
        }
        
        // Handle faculty change
        document.getElementById('faculty').addEventListener('change', function() {
            populateDepartments(this.value);
        });
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadFacultiesData();
        });
        
        // Form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const requiredFields = ['title', 'first_name', 'last_name', 'faculty', 'department', 'designation'];
            let isValid = true;
            
            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    </script>
</body>
</html>
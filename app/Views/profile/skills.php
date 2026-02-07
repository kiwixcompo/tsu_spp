<?php
// Load helper functions if not already loaded
if (!function_exists('url')) {
    require_once __DIR__ . '/../../Helpers/UrlHelper.php';
}
if (!function_exists('escape_attr')) {
    require_once __DIR__ . '/../../Helpers/TextHelper.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills - TSU Staff Profile Portal</title>
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
        .skill-card {
            margin-bottom: 15px;
            transition: transform 0.2s ease;
        }
        .skill-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        .skill-level {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        .skill-level-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997, #17a2b8, #007bff, #6f42c1);
            transition: width 0.3s ease;
        }
        .skill-category {
            background: #f8f9fa;
            color: #6c757d;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 500;
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
                        <a class="nav-link" href="<?= url('profile/edit') ?>">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </a>
                        <a class="nav-link" href="<?= url('profile/education') ?>">
                            <i class="fas fa-graduation-cap me-2"></i>Education
                        </a>
                        <a class="nav-link" href="<?= url('profile/experience') ?>">
                            <i class="fas fa-briefcase me-2"></i>Experience
                        </a>
                        <a class="nav-link active" href="<?= url('profile/skills') ?>">
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
                            <h4 class="mb-0">Skills & Expertise</h4>
                            <p class="text-muted mb-0">Manage your professional skills and competencies</p>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                                <i class="fas fa-plus me-2"></i>Add Skill
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <!-- Alert Container -->
                    <div id="alert-container"></div>

                    <!-- Skills List -->
                    <div id="skills-list">
                        <?php if (empty($skills)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-cogs fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No Skills Added Yet</h5>
                                <p class="text-muted">Add your skills and expertise to showcase your capabilities.</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                                    <i class="fas fa-plus me-2"></i>Add Your First Skill
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($skills as $skill): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="skill-card card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="mb-0"><?= htmlspecialchars($skill['skill_name']) ?></h6>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                                type="button"
                                                                id="skillDropdown<?= $skill['id'] ?>"
                                                                data-bs-toggle="dropdown"
                                                                aria-expanded="false"
                                                                aria-haspopup="true">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="skillDropdown<?= $skill['id'] ?>">
                                                            <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); editSkill(<?= $skill['id'] ?>)" data-skill='<?= escape_attr(json_encode($skill)) ?>'>
                                                                <i class="fas fa-edit me-2"></i>Edit
                                                            </a></li>
                                                            <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); deleteSkill(<?= $skill['id'] ?>)">
                                                                <i class="fas fa-trash me-2"></i>Delete
                                                            </a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                
                                                <?php if (!empty($skill['skill_category'])): ?>
                                                    <span class="skill-category mb-2 d-inline-block"><?= htmlspecialchars(ucfirst($skill['skill_category'])) ?></span>
                                                <?php endif; ?>
                                                
                                                <div class="mb-2">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <small class="text-muted">Proficiency</small>
                                                        <small class="fw-bold"><?= ucfirst($skill['proficiency_level']) ?></small>
                                                    </div>
                                                    <?php 
                                                    $levelMap = ['beginner' => 25, 'intermediate' => 50, 'advanced' => 75, 'expert' => 100];
                                                    $width = $levelMap[$skill['proficiency_level']] ?? 50;
                                                    ?>
                                                    <div class="skill-level">
                                                        <div class="skill-level-fill" style="width: <?= $width ?>%"></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="text-center">
                                                    <?php
                                                    $levelStars = ['beginner' => 1, 'intermediate' => 3, 'advanced' => 4, 'expert' => 5];
                                                    $starCount = $levelStars[$skill['proficiency_level']] ?? 3;
                                                    $stars = '';
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        if ($i <= $starCount) {
                                                            $stars .= '<i class="fas fa-star text-warning"></i>';
                                                        } else {
                                                            $stars .= '<i class="far fa-star text-muted"></i>';
                                                        }
                                                    }
                                                    echo $stars;
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Skill Modal -->
    <div class="modal fade" id="addSkillModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Skill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addSkillForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        
                        <div class="mb-3">
                            <label for="skill_name" class="form-label">Skill Name *</label>
                            <input type="text" class="form-control" id="skill_name" name="skill_name" 
                                   placeholder="e.g., Python Programming, Data Analysis" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="skill_category" class="form-label">Category</label>
                            <select class="form-select" id="skill_category" name="skill_category">
                                <option value="">Select Category</option>
                                <option value="technical">Technical</option>
                                <option value="research">Research</option>
                                <option value="teaching">Teaching</option>
                                <option value="administrative">Administrative</option>
                                <option value="language">Language</option>
                                <option value="software">Software</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="proficiency_level" class="form-label">Proficiency Level *</label>
                                <select class="form-select" id="proficiency_level" name="proficiency_level" required>
                                    <option value="">Select Level</option>
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                    <option value="expert">Expert</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="years_experience" class="form-label">Years of Experience</label>
                                <input type="number" class="form-control" id="years_experience" name="years_experience" 
                                       min="0" max="50" placeholder="e.g., 5">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <small>
                                <strong>Proficiency Levels:</strong><br>
                                <strong>1 - Beginner:</strong> Basic understanding<br>
                                <strong>2 - Basic:</strong> Limited experience<br>
                                <strong>3 - Intermediate:</strong> Some experience<br>
                                <strong>4 - Advanced:</strong> Extensive experience<br>
                                <strong>5 - Expert:</strong> Recognized expertise
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="addSkillBtn">
                            <i class="fas fa-plus me-2"></i>Add Skill
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

        // Add skill form submission
        document.getElementById('addSkillForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('addSkillBtn');
            const originalText = submitBtn.innerHTML;
            
            // Clear previous errors
            clearFieldErrors();
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
            
            // Prepare form data
            const formData = new FormData(this);
            
            // Submit form
            fetch('<?= url('profile/skills') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    // Close modal and reset form
                    bootstrap.Modal.getInstance(document.getElementById('addSkillModal')).hide();
                    this.reset();
                    // Reload page to show new skill
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        showFieldError(field, data.errors[field]);
                    });
                } else {
                    showAlert('danger', data.error || 'An error occurred while adding skill.');
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

        // Add skill form submission
        document.getElementById('addSkillForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.querySelector('#addSkillForm .btn-primary');
            const originalText = submitBtn.innerHTML;
            
            // Clear previous errors
            clearFieldErrors();
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
            
            // Get form data
            const formData = new FormData(this);
            const isEditMode = this.getAttribute('data-mode') === 'edit';
            const editId = this.getAttribute('data-edit-id');
            
            // Determine URL and method
            let url = '<?= url('profile/skills') ?>';
            let method = 'POST';
            let body = formData;
            let headers = {};
            
            if (isEditMode && editId) {
                url = url + '/' + editId;
                method = 'PUT';
                
                // Convert FormData to JSON for PUT request
                const data = {
                    csrf_token: formData.get('csrf_token'),
                    skill_name: formData.get('skill_name'),
                    skill_category: formData.get('skill_category'),
                    proficiency_level: formData.get('proficiency_level'),
                    years_experience: formData.get('years_experience')
                };
                
                body = JSON.stringify(data);
                headers = {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= htmlspecialchars($csrf_token) ?>'
                };
            } else {
                headers = {
                    'X-CSRF-Token': '<?= htmlspecialchars($csrf_token) ?>'
                };
            }
            
            // Submit form
            fetch(url, {
                method: method,
                body: body,
                headers: headers
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    // Close modal and reset form
                    bootstrap.Modal.getInstance(document.getElementById('addSkillModal')).hide();
                    this.reset();
                    this.removeAttribute('data-mode');
                    this.removeAttribute('data-edit-id');
                    // Reload page to show changes
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        showFieldError(field, data.errors[field]);
                    });
                } else {
                    showAlert('danger', data.error || 'An error occurred.');
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

        // Edit skill function
        function editSkill(id) {
            const dropdownItem = event.target.closest('a');
            const skillData = JSON.parse(dropdownItem.getAttribute('data-skill'));
            
            // Populate form with existing data
            document.getElementById('skill_name').value = skillData.skill_name || '';
            document.getElementById('skill_category').value = skillData.skill_category || '';
            document.getElementById('proficiency_level').value = skillData.proficiency_level || '';
            document.getElementById('years_experience').value = skillData.years_experience || '';
            
            // Change modal title and form action
            document.querySelector('#addSkillModal .modal-title').textContent = 'Edit Skill';
            document.getElementById('addSkillForm').setAttribute('data-edit-id', id);
            document.getElementById('addSkillForm').setAttribute('data-mode', 'edit');
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('addSkillModal'));
            modal.show();
        }

        // Delete skill function
        function deleteSkill(id) {
            if (confirm('Are you sure you want to delete this skill?')) {
                fetch('<?= url('profile/skills') ?>/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-Token': '<?= htmlspecialchars($csrf_token) ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showAlert('danger', data.error || 'Failed to delete skill.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An unexpected error occurred.');
                });
            }
        }

        // Reset modal when closed
        document.getElementById('addSkillModal').addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('addSkillForm');
            form.reset();
            form.removeAttribute('data-mode');
            form.removeAttribute('data-edit-id');
            document.querySelector('#addSkillModal .modal-title').textContent = 'Add Skill';
            clearFieldErrors();
        });
    </script>
</body>
</html>
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
    <title>Education - TSU Staff Profile Portal</title>
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
        .education-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.2s ease;
        }
        .education-card:hover {
            transform: translateY(-2px);
        }
        .degree-badge {
            background: #e3f2fd;
            color: #1976d2;
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
                        <a class="nav-link active" href="<?= url('profile/education') ?>">
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
                            <h4 class="mb-0">Education</h4>
                            <p class="text-muted mb-0">Manage your academic qualifications</p>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEducationModal">
                                <i class="fas fa-plus me-2"></i>Add Education
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <!-- Alert Container -->
                    <div id="alert-container"></div>

                    <!-- Education List -->
                    <div id="education-list">
                        <?php if (empty($education)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-graduation-cap fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No Education Added Yet</h5>
                                <p class="text-muted">Add your academic qualifications to showcase your educational background.</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEducationModal">
                                    <i class="fas fa-plus me-2"></i>Add Your First Education
                                </button>
                            </div>
                        <?php else: ?>
                            <?php foreach ($education as $edu): ?>
                                <div class="education-card card">
                                    <div class="card-body">
                                        <div class="row align-items-start">
                                            <div class="col">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="degree-badge me-3"><?= htmlspecialchars($edu['degree_type']) ?></span>
                                                    <h6 class="mb-0"><?= htmlspecialchars($edu['field_of_study']) ?></h6>
                                                </div>
                                                <h5 class="mb-2"><?= htmlspecialchars($edu['institution']) ?></h5>
                                                <p class="text-muted mb-2">
                                                    <i class="fas fa-calendar me-2"></i>
                                                    <?= $edu['start_year'] ?> - <?= $edu['end_year'] ?: 'Present' ?>
                                                </p>
                                                <?php if (!empty($edu['description'])): ?>
                                                    <p class="mb-0"><?= nl2br(htmlspecialchars($edu['description'])) ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-auto">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                            type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" onclick="editEducation(<?= $edu['id'] ?>, event)" data-education='<?= escape_attr(json_encode($edu)) ?>'>
                                                            <i class="fas fa-edit me-2"></i>Edit
                                                        </a></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteEducation(<?= $edu['id'] ?>, event)">
                                                            <i class="fas fa-trash me-2"></i>Delete
                                                        </a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Education Modal -->
    <div class="modal fade" id="addEducationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Education</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addEducationForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="degree_type" class="form-label">Degree Type *</label>
                                <select class="form-select" id="degree_type" name="degree_type" required>
                                    <option value="">Select Degree Type</option>
                                    <option value="PhD">PhD</option>
                                    <option value="M.Sc">M.Sc</option>
                                    <option value="M.A">M.A</option>
                                    <option value="M.Ed">M.Ed</option>
                                    <option value="M.Tech">M.Tech</option>
                                    <option value="M.Eng">M.Eng</option>
                                    <option value="MBA">MBA</option>
                                    <option value="B.Sc">B.Sc</option>
                                    <option value="B.A">B.A</option>
                                    <option value="B.Ed">B.Ed</option>
                                    <option value="B.Eng">B.Eng</option>
                                    <option value="B.Tech">B.Tech</option>
                                    <option value="HND">HND</option>
                                    <option value="OND">OND</option>
                                    <option value="Others">Others</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="field_of_study" class="form-label">Field of Study *</label>
                                <input type="text" class="form-control" id="field_of_study" name="field_of_study" 
                                       placeholder="e.g., Computer Science, Mathematics" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="institution" class="form-label">Institution *</label>
                                <input type="text" class="form-control" id="institution" name="institution" 
                                       placeholder="e.g., University of Lagos" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" name="country" 
                                       placeholder="e.g., Nigeria" value="Nigeria">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_year" class="form-label">Start Year *</label>
                                <input type="number" class="form-control" id="start_year" name="start_year" 
                                       min="1950" max="<?= date('Y') + 10 ?>" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_year" class="form-label">End Year</label>
                                <input type="number" class="form-control" id="end_year" name="end_year" 
                                       min="1950" max="<?= date('Y') + 10 ?>">
                                <div class="form-text">Leave empty if currently studying</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_current" name="is_current">
                                <label class="form-check-label" for="is_current">
                                    I am currently studying here
                                </label>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Additional details about your studies, achievements, etc."></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="addEducationBtn">
                            <i class="fas fa-plus me-2"></i>Add Education
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

        // Handle current study checkbox
        document.getElementById('is_current').addEventListener('change', function() {
            const endYearField = document.getElementById('end_year');
            if (this.checked) {
                endYearField.disabled = true;
                endYearField.value = '';
            } else {
                endYearField.disabled = false;
            }
        });

        // Add/Edit education form submission
        document.getElementById('addEducationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('addEducationBtn');
            const originalText = submitBtn.innerHTML;
            const isEditMode = this.getAttribute('data-mode') === 'edit';
            const editId = this.getAttribute('data-edit-id');
            
            // Clear previous errors
            clearFieldErrors();
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = isEditMode ? 
                '<i class="fas fa-spinner fa-spin me-2"></i>Updating...' : 
                '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
            
            let url, method, body;
            
            if (isEditMode) {
                url = '<?= url('profile/education') ?>/' + editId;
                method = 'PUT';
                
                // Convert FormData to JSON for PUT request
                const formData = new FormData(this);
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });
                
                body = JSON.stringify(data);
                
                fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?= $csrf_token ?>'
                    },
                    body: body
                })
                .then(response => {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        return response.text().then(text => {
                            throw new Error('Server returned non-JSON response. Check server logs.');
                        });
                    }
                })
                .then(handleResponse.bind(this))
                .catch(handleError)
                .finally(() => restoreButton(submitBtn, originalText));
            } else {
                url = '<?= url('profile/education') ?>';
                method = 'POST';
                body = new FormData(this);
                
                fetch(url, {
                    method: method,
                    body: body
                })
                .then(response => {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        return response.text().then(text => {
                            throw new Error('Server returned non-JSON response. Check server logs.');
                        });
                    }
                })
                .then(handleResponse.bind(this))
                .catch(handleError)
                .finally(() => restoreButton(submitBtn, originalText));
            }
            
            function handleResponse(data) {
                if (data.success) {
                    showAlert('success', data.message);
                    // Close modal and reset form
                    bootstrap.Modal.getInstance(document.getElementById('addEducationModal')).hide();
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
            }
            
            function handleError(error) {
                console.error('Error:', error);
                showAlert('danger', 'An unexpected error occurred. Please try again.');
            }
            
            function restoreButton(btn, text) {
                // Restore button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });

        // Edit education function
        function editEducation(id, event) {
            event.preventDefault();
            const dropdownItem = event.target.closest('a');
            const educationData = JSON.parse(dropdownItem.getAttribute('data-education'));
            
            // Populate form with existing data
            document.getElementById('degree_type').value = educationData.degree_type || '';
            document.getElementById('field_of_study').value = educationData.field_of_study || '';
            document.getElementById('institution').value = educationData.institution || '';
            document.getElementById('start_year').value = educationData.start_year || '';
            document.getElementById('end_year').value = educationData.end_year || '';
            document.getElementById('is_current').checked = educationData.is_current == 1;
            document.getElementById('description').value = educationData.description || '';
            
            // Change modal title and form action
            document.querySelector('#addEducationModal .modal-title').textContent = 'Update Education';
            document.getElementById('addEducationForm').setAttribute('data-edit-id', id);
            document.getElementById('addEducationForm').setAttribute('data-mode', 'edit');
            document.getElementById('addEducationBtn').innerHTML = '<i class="fas fa-save me-2"></i>Update Education';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('addEducationModal'));
            modal.show();
        }

        // Delete education function
        function deleteEducation(id, event) {
            event.preventDefault();
            if (confirm('Are you sure you want to delete this education entry?')) {
                fetch('<?= url('profile/education') ?>/' + id, {
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
                        showAlert('danger', data.error || 'Failed to delete education.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An unexpected error occurred.');
                });
            }
        }

        // Reset modal when closed
        document.getElementById('addEducationModal').addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('addEducationForm');
            form.reset();
            form.removeAttribute('data-mode');
            form.removeAttribute('data-edit-id');
            document.querySelector('#addEducationModal .modal-title').textContent = 'Add Education';
            clearFieldErrors();
        });
    </script>
</body>
</html>
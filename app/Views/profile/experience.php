<?php
// Load helpers if not already loaded
if (!function_exists('url')) {
    require_once __DIR__ . '/../../Helpers/UrlHelper.php';
}
if (!function_exists('safe_output')) {
    require_once __DIR__ . '/../../Helpers/TextHelper.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Experience - TSU Staff Profile Portal</title>
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
        .experience-card {
            margin-bottom: 20px;
            transition: transform 0.2s ease;
        }
        .experience-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        .current-badge {
            background: #d4edda;
            color: #155724;
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
                        <a class="nav-link active" href="<?= url('profile/experience') ?>">
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
                            <h4 class="mb-0">Work Experience</h4>
                            <p class="text-muted mb-0">Manage your professional experience</p>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExperienceModal">
                                <i class="fas fa-plus me-2"></i>Add Experience
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <!-- Alert Container -->
                    <div id="alert-container"></div>

                    <!-- Experience List -->
                    <div id="experience-list">
                        <?php if (empty($experience)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-briefcase fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No Experience Added Yet</h5>
                                <p class="text-muted">Add your work experience to showcase your professional background.</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExperienceModal">
                                    <i class="fas fa-plus me-2"></i>Add Your First Experience
                                </button>
                            </div>
                        <?php else: ?>
                            <?php foreach ($experience as $exp): ?>
                                <div class="experience-card card">
                                    <div class="card-body">
                                        <div class="row align-items-start">
                                            <div class="col">
                                                <div class="d-flex align-items-center mb-2">
                                                    <h5 class="mb-0 me-3"><?= safe_output($exp['job_title']) ?></h5>
                                                    <?php if ($exp['is_current']): ?>
                                                        <span class="current-badge">Current</span>
                                                    <?php endif; ?>
                                                </div>
                                                <h6 class="text-primary mb-2"><?= safe_output($exp['organization']) ?></h6>
                                                <p class="text-muted mb-2">
                                                    <i class="fas fa-calendar me-2"></i>
                                                    <?= date('M Y', strtotime($exp['start_date'])) ?> - 
                                                    <?= $exp['end_date'] ? date('M Y', strtotime($exp['end_date'])) : 'Present' ?>
                                                </p>
                                                <?php if (!empty($exp['description'])): ?>
                                                    <p class="mb-0"><?= nl2br(safe_output($exp['description'])) ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-auto">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                            type="button"
                                                            id="experienceDropdown<?= $exp['id'] ?>"
                                                            data-bs-toggle="dropdown"
                                                            aria-expanded="false"
                                                            aria-haspopup="true">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="experienceDropdown<?= $exp['id'] ?>">
                                                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); editExperience(<?= $exp['id'] ?>)" data-experience='<?= escape_attr(json_encode($exp)) ?>'>
                                                            <i class="fas fa-edit me-2"></i>Edit
                                                        </a></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); deleteExperience(<?= $exp['id'] ?>)">
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

    <!-- Add Experience Modal -->
    <div class="modal fade" id="addExperienceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Experience</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addExperienceForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="job_title" class="form-label">Job Title *</label>
                                <input type="text" class="form-control" id="job_title" name="job_title" 
                                       placeholder="e.g., Senior Lecturer, Research Assistant" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="organization" class="form-label">Organization *</label>
                                <input type="text" class="form-control" id="organization" name="organization" 
                                       placeholder="e.g., University of Lagos" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   placeholder="e.g., Lagos, Nigeria">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date *</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_current" name="is_current">
                                <label class="form-check-label" for="is_current">
                                    This is my current position
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" 
                                      placeholder="Describe your responsibilities, achievements, and key contributions..."></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="addExperienceBtn">
                            <i class="fas fa-plus me-2"></i>Add Experience
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle current position checkbox
        document.getElementById('is_current').addEventListener('change', function() {
            const endDateField = document.getElementById('end_date');
            if (this.checked) {
                endDateField.disabled = true;
                endDateField.value = '';
            } else {
                endDateField.disabled = false;
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

        // Add experience form submission
        document.getElementById('addExperienceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('addExperienceBtn');
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
            let url = '<?= url('profile/experience') ?>';
            let method = 'POST';
            let body = formData;
            let headers = {};
            
            if (isEditMode && editId) {
                url = url + '/' + editId;
                method = 'PUT';
                
                // Convert FormData to JSON for PUT request
                const data = {
                    csrf_token: formData.get('csrf_token'),
                    job_title: formData.get('job_title'),
                    organization: formData.get('organization'),
                    location: formData.get('location'),
                    start_date: formData.get('start_date'),
                    end_date: formData.get('end_date'),
                    is_current: formData.get('is_current') ? 1 : 0,
                    description: formData.get('description')
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
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // Response is not JSON, likely an error page
                    return response.text().then(text => {
                        throw new Error('Server returned non-JSON response. Check server logs.');
                    });
                }
            })
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    // Close modal and reset form
                    bootstrap.Modal.getInstance(document.getElementById('addExperienceModal')).hide();
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

        // Edit experience function (placeholder)
        function editExperience(id) {
            const dropdownItem = event.target.closest('a');
            const experienceData = JSON.parse(dropdownItem.getAttribute('data-experience'));
            
            // Populate form with existing data
            document.getElementById('job_title').value = experienceData.job_title || '';
            document.getElementById('organization').value = experienceData.organization || '';
            document.getElementById('location').value = experienceData.location || '';
            document.getElementById('start_date').value = experienceData.start_date || '';
            document.getElementById('end_date').value = experienceData.end_date || '';
            document.getElementById('is_current').checked = experienceData.is_current == 1;
            document.getElementById('description').value = experienceData.description || '';
            
            // Update end_date field disabled state based on is_current
            const endDateField = document.getElementById('end_date');
            if (experienceData.is_current == 1) {
                endDateField.disabled = true;
            } else {
                endDateField.disabled = false;
            }
            
            // Change modal title and form action
            document.querySelector('#addExperienceModal .modal-title').textContent = 'Update Experience';
            document.getElementById('addExperienceForm').setAttribute('data-edit-id', id);
            document.getElementById('addExperienceForm').setAttribute('data-mode', 'edit');
            document.getElementById('addExperienceBtn').innerHTML = '<i class="fas fa-save me-2"></i>Update Experience';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('addExperienceModal'));
            modal.show();
        }

        // Delete experience function
        function deleteExperience(id) {
            if (confirm('Are you sure you want to delete this experience entry?')) {
                fetch('<?= url('profile/experience') ?>/' + id, {
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
                        showAlert('danger', data.error || 'Failed to delete experience.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An unexpected error occurred.');
                });
            }
        }

        // Reset modal when closed
        document.getElementById('addExperienceModal').addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('addExperienceForm');
            form.reset();
            form.removeAttribute('data-mode');
            form.removeAttribute('data-edit-id');
            document.querySelector('#addExperienceModal .modal-title').textContent = 'Add Experience';
            document.getElementById('addExperienceBtn').innerHTML = '<i class="fas fa-plus me-2"></i>Add Experience';
            clearFieldErrors();
            // Reset end_date disabled state
            document.getElementById('end_date').disabled = false;
        });
    </script>
</body>
</html>
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
    <title>Publications - TSU Staff Profile Portal</title>
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
        .publication-card {
            margin-bottom: 20px;
            transition: transform 0.2s ease;
        }
        .publication-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        .publication-type-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
        }
        .citation-info {
            font-size: 0.9rem;
            color: #6c757d;
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
                        <a class="nav-link active" href="<?= url('profile/publications') ?>">
                            <i class="fas fa-book me-2"></i>Publications
                        </a>
                        <a class="nav-link" href="<?= url('profile/skills') ?>">
                            <i class="fas fa-cogs me-2"></i>Skills
                        </a>
                        <a class="nav-link" href="<?= url('settings') ?>">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                        <hr class="text-white-50">
                        <?php if (!empty($user['profile_slug'])): ?>
                        <a class="nav-link" href="<?= url('profile/' . htmlspecialchars($user['profile_slug'])) ?>" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>View Public Profile
                        </a>
                        <?php endif; ?>
                        <a class="nav-link" href="<?= url('directory') ?>">
                            <i class="fas fa-users me-2"></i>Staff Directory
                        </a>
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
                            <h4 class="mb-0">
                                <i class="fas fa-book text-primary me-2"></i>Publications
                            </h4>
                            <p class="text-muted mb-0">Manage your research publications and scholarly works</p>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" onclick="showAddModal()">
                                <i class="fas fa-plus me-2"></i>Add Publication
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <!-- Publications List -->
                    <?php if (empty($publications)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-book fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No Publications Yet</h5>
                            <p class="text-muted mb-4">Start building your academic profile by adding your research publications.</p>
                            <button type="button" class="btn btn-primary" onclick="showAddModal()">
                                <i class="fas fa-plus me-2"></i>Add Your First Publication
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($publications as $publication): ?>
                            <div class="col-12">
                                <div class="card publication-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <span class="badge bg-primary publication-type-badge me-2">
                                                    <?= ucfirst($publication['publication_type']) ?>
                                                </span>
                                                <?php if ($publication['year']): ?>
                                                <span class="badge bg-secondary publication-type-badge">
                                                    <?= $publication['year'] ?>
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                        type="button"
                                                        id="publicationDropdown<?= $publication['id'] ?>"
                                                        data-bs-toggle="dropdown"
                                                        aria-expanded="false"
                                                        aria-haspopup="true">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="publicationDropdown<?= $publication['id'] ?>">
                                                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); editPublication(<?= $publication['id'] ?>)" data-publication='<?= escape_attr(json_encode($publication)) ?>'>
                                                        <i class="fas fa-edit me-2"></i>Edit
                                                    </a></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); deletePublication(<?= $publication['id'] ?>)">
                                                        <i class="fas fa-trash me-2"></i>Delete
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <h5 class="card-title mb-2"><?= safe_output($publication['title']) ?></h5>
                                        
                                        <?php if ($publication['authors']): ?>
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-users me-1"></i>
                                            <?= safe_output($publication['authors']) ?>
                                        </p>
                                        <?php endif; ?>
                                        
                                        <div class="citation-info mb-2">
                                            <?php if ($publication['journal_conference_name']): ?>
                                            <span class="me-3">
                                                <i class="fas fa-journal-whills me-1"></i>
                                                <?= safe_output($publication['journal_conference_name']) ?>
                                            </span>
                                            <?php endif; ?>
                                            
                                            <?php if ($publication['volume'] || $publication['issue'] || $publication['pages']): ?>
                                            <span class="me-3">
                                                <?php if ($publication['volume']): ?>Vol. <?= safe_output($publication['volume']) ?><?php endif; ?>
                                                <?php if ($publication['issue']): ?>(<?= safe_output($publication['issue']) ?>)<?php endif; ?>
                                                <?php if ($publication['pages']): ?>, pp. <?= safe_output($publication['pages']) ?><?php endif; ?>
                                            </span>
                                            <?php endif; ?>
                                            
                                            <?php if ($publication['publisher']): ?>
                                            <span class="me-3">
                                                <i class="fas fa-building me-1"></i>
                                                <?= safe_output($publication['publisher']) ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($publication['abstract']): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <?= safe_output(substr($publication['abstract'], 0, 200)) ?>
                                                <?php if (strlen($publication['abstract']) > 200): ?>...<?php endif; ?>
                                            </small>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <?php if ($publication['doi']): ?>
                                                <a href="https://doi.org/<?= safe_output($publication['doi']) ?>" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="fas fa-external-link-alt me-1"></i>DOI
                                                </a>
                                                <?php endif; ?>
                                                
                                                <?php if ($publication['url']): ?>
                                                <a href="<?= safe_output($publication['url']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-2">
                                                    <i class="fas fa-link me-1"></i>Link
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <?php if ($publication['citation_count'] > 0): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-quote-right me-1"></i>
                                                <?= $publication['citation_count'] ?> citations
                                            </small>
                                            <?php endif; ?>
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

    <!-- Add/Edit Publication Modal -->
    <div class="modal fade" id="publicationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="publicationModalTitle">
                        <i class="fas fa-plus me-2"></i>Add Publication
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="publicationForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">Title *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="publication_type" class="form-label">Type *</label>
                                <select class="form-select" id="publication_type" name="publication_type" required>
                                    <option value="">Select type</option>
                                    <option value="journal">Journal Article</option>
                                    <option value="conference">Conference Paper</option>
                                    <option value="book">Book</option>
                                    <option value="chapter">Book Chapter</option>
                                    <option value="report">Report</option>
                                    <option value="thesis">Thesis</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="authors" class="form-label">Authors</label>
                            <input type="text" class="form-control" id="authors" name="authors" 
                                   placeholder="e.g., Smith, J., Doe, A., Johnson, B.">
                            <div class="form-text">List all authors in citation format</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="journal_conference_name" class="form-label">Journal/Conference Name</label>
                                <input type="text" class="form-control" id="journal_conference_name" name="journal_conference_name">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="year" class="form-label">Year</label>
                                <input type="number" class="form-control" id="year" name="year" min="1900" max="<?= date('Y') + 5 ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="publisher" class="form-label">Publisher</label>
                                <input type="text" class="form-control" id="publisher" name="publisher">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="volume" class="form-label">Volume</label>
                                <input type="text" class="form-control" id="volume" name="volume">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="issue" class="form-label">Issue</label>
                                <input type="text" class="form-control" id="issue" name="issue">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="pages" class="form-label">Pages</label>
                                <input type="text" class="form-control" id="pages" name="pages" placeholder="e.g., 123-145">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="citation_count" class="form-label">Citations</label>
                                <input type="number" class="form-control" id="citation_count" name="citation_count" min="0">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="doi" class="form-label">DOI</label>
                                <input type="text" class="form-control" id="doi" name="doi" placeholder="10.1000/xyz123">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="url" class="form-label">URL</label>
                                <input type="url" class="form-control" id="url" name="url">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="abstract" class="form-label">Abstract</label>
                            <textarea class="form-control" id="abstract" name="abstract" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Publication
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let isEditMode = false;
        let editingId = null;

        // Show add modal
        function showAddModal() {
            isEditMode = false;
            editingId = null;
            document.getElementById('publicationModalTitle').innerHTML = '<i class="fas fa-plus me-2"></i>Add Publication';
            document.getElementById('publicationForm').reset();
            clearFormErrors();
            
            const modal = new bootstrap.Modal(document.getElementById('publicationModal'));
            modal.show();
        }

        // Show edit modal
        function editPublication(id) {
            const dropdownItem = event.target.closest('a');
            const publicationData = JSON.parse(dropdownItem.getAttribute('data-publication'));
            
            isEditMode = true;
            editingId = id;
            document.getElementById('publicationModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Publication';
            
            // Populate form with existing data
            document.getElementById('title').value = publicationData.title || '';
            document.getElementById('publication_type').value = publicationData.publication_type || '';
            document.getElementById('authors').value = publicationData.authors || '';
            document.getElementById('journal_conference_name').value = publicationData.journal_conference_name || '';
            document.getElementById('year').value = publicationData.year || '';
            document.getElementById('publisher').value = publicationData.publisher || '';
            document.getElementById('volume').value = publicationData.volume || '';
            document.getElementById('issue').value = publicationData.issue || '';
            document.getElementById('pages').value = publicationData.pages || '';
            document.getElementById('citation_count').value = publicationData.citation_count || '';
            document.getElementById('doi').value = publicationData.doi || '';
            document.getElementById('url').value = publicationData.url || '';
            document.getElementById('abstract').value = publicationData.abstract || '';
            
            clearFormErrors();
            
            const modal = new bootstrap.Modal(document.getElementById('publicationModal'));
            modal.show();
        }

        // Form submission
        document.getElementById('publicationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            let url, method;
            if (isEditMode) {
                url = '<?= url('profile/publications') ?>/' + editingId;
                method = 'PUT';
                
                // Convert FormData to regular object for PUT request
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });
                
                fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?= $csrf_token ?>'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(handleResponse)
                .catch(handleError);
            } else {
                url = '<?= url('profile/publications/add') ?>';
                method = 'POST';
                
                fetch(url, {
                    method: method,
                    body: formData
                })
                .then(response => response.json())
                .then(handleResponse)
                .catch(handleError);
            }
        });

        function handleResponse(data) {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    showFieldError(field, data.errors[field]);
                });
            } else {
                showAlert('danger', data.error || 'Operation failed');
            }
        }

        function handleError(error) {
            showAlert('danger', 'Network error. Please try again.');
        }

        function deletePublication(id) {
            if (confirm('Are you sure you want to delete this publication?')) {
                fetch('<?= url('profile/publications/delete') ?>/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showAlert('danger', data.error || 'Failed to delete publication');
                    }
                })
                .catch(error => {
                    showAlert('danger', 'Network error. Please try again.');
                });
            }
        }

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

        function clearFormErrors() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
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
            
            const container = document.querySelector('.main-content .p-4');
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto-dismiss success alerts
            if (type === 'success') {
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 3000);
            }
        }

        // Clear form errors when modal is closed
        document.getElementById('publicationModal').addEventListener('hidden.bs.modal', function() {
            clearFormErrors();
            document.getElementById('publicationForm').reset();
            isEditMode = false;
            editingId = null;
        });
    </script>
</body>
</html>
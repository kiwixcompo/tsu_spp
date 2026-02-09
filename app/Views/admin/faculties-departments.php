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
    <title>Faculties & Departments - Admin Panel</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?php include __DIR__ . '/partials/styles.php'; ?>
    <style>
        .faculty-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .department-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            margin: 4px;
            display: inline-block;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <?php $currentPage = 'faculties-departments'; include __DIR__ . '/partials/sidebar.php'; ?>

            <div class="main-content" id="mainContent">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3">
                                <i class="fas fa-building me-2"></i>Faculties & Departments Management
                            </h1>
                            <p class="text-muted">Manage university faculties and their departments</p>
                        </div>
                        <div>
                            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addFacultyModal">
                                <i class="fas fa-plus me-2"></i>Add Faculty
                            </button>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                                <i class="fas fa-plus me-2"></i>Add Department
                            </button>
                        </div>
                    </div>

                    <!-- Alert Container -->
                    <div id="alert-container"></div>

                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3 class="text-primary"><?= count($faculties) ?></h3>
                                    <p class="mb-0">Total Faculties</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3 class="text-success">
                                        <?php 
                                        $totalDepts = 0;
                                        foreach ($faculties as $faculty) {
                                            $totalDepts += count($faculty['departments']);
                                        }
                                        echo $totalDepts;
                                        ?>
                                    </h3>
                                    <p class="mb-0">Total Departments</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Faculties List -->
                    <div class="row">
                        <?php foreach ($faculties as $faculty): ?>
                        <div class="col-12">
                            <div class="faculty-card card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-building me-2"></i>
                                        <?= htmlspecialchars($faculty['name']) ?>
                                        <span class="badge bg-light text-primary float-end">
                                            <?= count($faculty['departments']) ?> Departments
                                        </span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($faculty['departments'])): ?>
                                        <p class="text-muted">No departments in this faculty</p>
                                    <?php else: ?>
                                        <?php foreach ($faculty['departments'] as $dept): ?>
                                            <span class="department-badge">
                                                <?= htmlspecialchars($dept['name']) ?>
                                                <button class="btn btn-sm btn-link text-danger p-0 ms-2" 
                                                        onclick="deleteDepartment(<?= $dept['id'] ?>, '<?= htmlspecialchars($dept['name']) ?>')"
                                                        title="Delete department">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Faculty Modal -->
    <div class="modal fade" id="addFacultyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Faculty</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addFacultyForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <div class="mb-3">
                            <label for="faculty_name" class="form-label">Faculty Name *</label>
                            <input type="text" class="form-control" id="faculty_name" name="faculty_name" required>
                            <div class="form-text">e.g., Faculty of Science</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Faculty
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Department Modal -->
    <div class="modal fade" id="addDepartmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addDepartmentForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <div class="mb-3">
                            <label for="faculty" class="form-label">Faculty *</label>
                            <select class="form-select" id="faculty" name="faculty" required>
                                <option value="">Select Faculty</option>
                                <?php foreach ($faculties as $faculty): ?>
                                    <option value="<?= htmlspecialchars($faculty['name']) ?>">
                                        <?= htmlspecialchars($faculty['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="department" class="form-label">Department Name *</label>
                            <input type="text" class="form-control" id="department" name="department" required>
                            <div class="form-text">e.g., Computer Science</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Add Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = '<?= $csrf_token ?>';

        // Add Faculty
        document.getElementById('addFacultyForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('<?= url('/admin/add-faculty') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('danger', result.error || 'Failed to add faculty');
                }
            } catch (error) {
                showAlert('danger', 'Network error. Please try again.');
            }
        });

        // Add Department
        document.getElementById('addDepartmentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('<?= url('/admin/add-department') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('danger', result.error || 'Failed to add department');
                }
            } catch (error) {
                showAlert('danger', 'Network error. Please try again.');
            }
        });

        // Delete Department
        async function deleteDepartment(id, name) {
            if (!confirm(`Are you sure you want to delete "${name}"?`)) {
                return;
            }
            
            try {
                const response = await fetch('<?= url('/admin/delete-department') ?>', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    body: JSON.stringify({ id: id, csrf_token: csrfToken })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('danger', result.error || 'Failed to delete department');
                }
            } catch (error) {
                showAlert('danger', 'Network error. Please try again.');
            }
        }

        // Show Alert
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alert-container');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }
    </script>
    <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
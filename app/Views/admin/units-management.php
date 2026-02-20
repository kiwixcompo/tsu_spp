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
    <title>Units Management - Admin Panel</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?php include __DIR__ . '/partials/styles.php'; ?>
</head>
<body class="bg-light">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <?php $currentPage = 'units'; include __DIR__ . '/partials/sidebar.php'; ?>

            <div class="main-content" id="mainContent">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3">
                                <i class="fas fa-sitemap me-2"></i>Units & Offices Management
                            </h1>
                            <p class="text-muted">Manage university units, offices, and directorates</p>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                            <i class="fas fa-plus me-2"></i>Add Unit/Office
                        </button>
                    </div>

                    <!-- Alert Container -->
                    <div id="alert-container"></div>

                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3 class="text-primary"><?= count($units) ?></h3>
                                    <p class="mb-0">Total Units/Offices</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Units Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($units as $unit): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($unit['name']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $unit['type'] === 'directorate' ? 'primary' : ($unit['type'] === 'office' ? 'success' : 'info') ?>">
                                                    <?= ucfirst($unit['type']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($unit['created_at'])) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick="editUnit(<?= $unit['id'] ?>, '<?= htmlspecialchars($unit['name'], ENT_QUOTES) ?>', '<?= $unit['type'] ?>')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteUnit(<?= $unit['id'] ?>, '<?= htmlspecialchars($unit['name'], ENT_QUOTES) ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Unit Modal -->
    <div class="modal fade" id="addUnitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Unit/Office</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addUnitForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type *</label>
                            <select class="form-select" name="type" required>
                                <option value="unit">Unit</option>
                                <option value="office">Office</option>
                                <option value="directorate">Directorate</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Unit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Unit Modal -->
    <div class="modal fade" id="editUnitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Unit/Office</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUnitForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="id" id="edit_unit_id">
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" class="form-control" name="name" id="edit_unit_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type *</label>
                            <select class="form-select" name="type" id="edit_unit_type" required>
                                <option value="unit">Unit</option>
                                <option value="office">Office</option>
                                <option value="directorate">Directorate</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Unit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include __DIR__ . '/partials/scripts.php'; ?>
    <script>
        // Add Unit
        document.getElementById('addUnitForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('<?= url('admin/units/add') ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    showAlert('success', data.message);
                    bootstrap.Modal.getInstance(document.getElementById('addUnitModal')).hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('danger', data.error);
                }
            } catch (error) {
                showAlert('danger', 'An error occurred');
            }
        });

        // Edit Unit
        function editUnit(id, name, type) {
            document.getElementById('edit_unit_id').value = id;
            document.getElementById('edit_unit_name').value = name;
            document.getElementById('edit_unit_type').value = type;
            new bootstrap.Modal(document.getElementById('editUnitModal')).show();
        }

        document.getElementById('editUnitForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('<?= url('admin/units/update') ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    showAlert('success', data.message);
                    bootstrap.Modal.getInstance(document.getElementById('editUnitModal')).hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('danger', data.error);
                }
            } catch (error) {
                showAlert('danger', 'An error occurred');
            }
        });

        // Delete Unit
        async function deleteUnit(id, name) {
            if (!confirm(`Are you sure you want to delete "${name}"?`)) return;
            
            const formData = new FormData();
            formData.append('csrf_token', '<?= $csrf_token ?>');
            formData.append('id', id);
            
            try {
                const response = await fetch('<?= url('admin/units/delete') ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('danger', data.error);
                }
            } catch (error) {
                showAlert('danger', 'An error occurred');
            }
        }

        function showAlert(type, message) {
            const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
            document.getElementById('alert-container').innerHTML = alertHtml;
        }
    </script>
</body>
</html>

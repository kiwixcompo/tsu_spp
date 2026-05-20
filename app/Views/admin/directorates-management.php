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
    <title>Directorates & Units Management - Admin Panel</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?php include __DIR__ . '/partials/styles.php'; ?>
    <style>
        .directorate-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #ffffff;
            margin-bottom: 25px;
            overflow: hidden;
            border-left: 5px solid #1e40af;
        }
        .directorate-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(30, 64, 175, 0.1);
        }
        .directorate-card.inactive-dir {
            border-left: 5px solid #9ca3af;
            opacity: 0.85;
        }
        .card-header-gradient {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
        }
        .unit-badge {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.9em;
            margin: 6px;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        .unit-badge:hover {
            background: #dbeafe;
            transform: scale(1.03);
        }
        .unit-badge.inactive-unit {
            background: #f3f4f6;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }
        .unit-badge.inactive-unit:hover {
            background: #e5e7eb;
        }
        .stat-glow-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            background: #fff;
            position: relative;
            overflow: hidden;
        }
        .stat-glow-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }
        .stat-glow-primary::after { background: #1e40af; }
        .stat-glow-success::after { background: #10b981; }
        .btn-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            transition: all 0.2s ease;
        }
        .btn-circle:hover {
            transform: scale(1.1);
        }
        .badge-status {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 12px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <?php $currentPage = 'directorates'; include __DIR__ . '/partials/sidebar.php'; ?>

            <div class="main-content" id="mainContent">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <div>
                            <h1 class="h3 fw-bold text-dark mb-1">
                                <i class="fas fa-sitemap text-primary me-2"></i>Directorates & Units Management
                            </h1>
                            <p class="text-muted mb-0">Administer directorates and nested units for non-teaching staff members.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary px-3 shadow-sm" id="btn-add-directorate-trigger" data-bs-toggle="modal" data-bs-target="#addDirectorateModal">
                                <i class="fas fa-plus me-2"></i>Add Directorate
                            </button>
                            <button class="btn btn-success px-3 shadow-sm" id="btn-add-unit-trigger" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                                <i class="fas fa-plus me-2"></i>Add Unit
                            </button>
                        </div>
                    </div>

                    <!-- Alert Container -->
                    <div id="alert-container">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Statistics -->
                    <div class="row mb-4 g-3">
                        <div class="col-md-6">
                            <div class="stat-glow-card stat-glow-primary card py-3 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted uppercase fw-semibold mb-1">Total Directorates</h6>
                                        <h3 class="fw-bold text-dark mb-0" id="stat-total-directorates"><?= count($directorates) ?></h3>
                                    </div>
                                    <div class="bg-blue-50 text-primary p-3 rounded-3" style="background: #eff6ff;">
                                        <i class="fas fa-university fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-glow-card stat-glow-success card py-3 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted uppercase fw-semibold mb-1">Total Active Units</h6>
                                        <h3 class="fw-bold text-dark mb-0" id="stat-total-units">
                                            <?php 
                                            $totalUnits = 0;
                                            foreach ($directorates as $dir) {
                                                foreach ($dir['units'] as $unit) {
                                                    if ($unit['is_active']) $totalUnits++;
                                                }
                                            }
                                            echo $totalUnits;
                                            ?>
                                        </h3>
                                    </div>
                                    <div class="bg-green-50 text-success p-3 rounded-3" style="background: #ecfdf5;">
                                        <i class="fas fa-layer-group fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Directorates List -->
                    <div class="row">
                        <?php if (empty($directorates)): ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm text-center py-5">
                                    <div class="card-body">
                                        <i class="fas fa-folder-open text-muted fa-3x mb-3"></i>
                                        <h5 class="text-secondary fw-semibold">No Directorates Found</h5>
                                        <p class="text-muted">Create a directorate above to start structuring non-teaching staff units.</p>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($directorates as $dir): ?>
                                <div class="col-12" id="directorate-container-<?= $dir['id'] ?>">
                                    <div class="directorate-card card <?= !$dir['is_active'] ? 'inactive-dir' : '' ?>">
                                        <div class="card-header-gradient d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div>
                                                <h5 class="mb-1 fw-bold text-dark d-flex align-items-center flex-wrap gap-2">
                                                    <span><?= htmlspecialchars($dir['name']) ?></span>
                                                    <?php if ($dir['is_active']): ?>
                                                        <span class="badge bg-success-soft text-success badge-status" style="background: #d1fae5; color: #065f46;">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary-soft text-secondary badge-status" style="background: #f3f4f6; color: #374151;">Inactive</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <small class="text-muted d-flex align-items-center gap-2">
                                                    <span><i class="fas fa-sort me-1"></i>Order: <?= $dir['display_order'] ?></span>
                                                    <span>•</span>
                                                    <span><i class="fas fa-layer-group me-1"></i><?= count($dir['units']) ?> Units</span>
                                                </small>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <button class="btn btn-sm btn-outline-primary btn-circle" 
                                                        onclick="openEditDirectorateModal(<?= $dir['id'] ?>, '<?= htmlspecialchars($dir['name'], ENT_QUOTES) ?>', <?= $dir['display_order'] ?>, <?= $dir['is_active'] ?>)" 
                                                        title="Edit Directorate" id="btn-edit-dir-<?= $dir['id'] ?>">
                                                    <i class="fas fa-pencil-alt text-xs"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger btn-circle" 
                                                        onclick="deleteDirectorate(<?= $dir['id'] ?>, '<?= htmlspecialchars($dir['name'], ENT_QUOTES) ?>')" 
                                                        title="Delete Directorate" id="btn-delete-dir-<?= $dir['id'] ?>">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body bg-white p-3">
                                            <?php if (empty($dir['units'])): ?>
                                                <p class="text-muted small mb-0 px-2 py-1"><i class="fas fa-info-circle me-2"></i>No units created in this directorate yet.</p>
                                            <?php else: ?>
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <?php foreach ($dir['units'] as $unit): ?>
                                                        <span class="unit-badge <?= !$unit['is_active'] ? 'inactive-unit' : '' ?>" id="unit-badge-<?= $unit['id'] ?>">
                                                            <i class="fas fa-circle-nodes me-2 <?= $unit['is_active'] ? 'text-primary' : 'text-muted' ?>" style="font-size: 0.75rem;"></i>
                                                            <strong class="me-2"><?= htmlspecialchars($unit['unit_name']) ?></strong>
                                                            
                                                            <?php if ($unit['display_order'] > 0): ?>
                                                                <small class="text-muted me-2">(#<?= $unit['display_order'] ?>)</small>
                                                            <?php endif; ?>

                                                            <button class="btn btn-link text-primary p-0 border-0 me-2" 
                                                                    onclick="openEditUnitModal(<?= $unit['id'] ?>, <?= $unit['directorate_id'] ?>, '<?= htmlspecialchars($unit['unit_name'], ENT_QUOTES) ?>', <?= $unit['display_order'] ?>, <?= $unit['is_active'] ?>)" 
                                                                    title="Edit Unit" style="font-size: 0.8rem;" id="btn-edit-unit-<?= $unit['id'] ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            
                                                            <button class="btn btn-link text-danger p-0 border-0" 
                                                                    onclick="deleteUnit(<?= $unit['id'] ?>, '<?= htmlspecialchars($unit['unit_name'], ENT_QUOTES) ?>')" 
                                                                    title="Delete Unit" style="font-size: 0.8rem;" id="btn-delete-unit-<?= $unit['id'] ?>">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
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

    <!-- Add Directorate Modal -->
    <div class="modal fade" id="addDirectorateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-university me-2 text-primary"></i>Add New Directorate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addDirectorateForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <div class="mb-3">
                            <label for="add_dir_name" class="form-label fw-semibold">Directorate Name *</label>
                            <input type="text" class="form-control rounded-3" id="add_dir_name" name="name" required placeholder="e.g., Directorate of Strategic Innovation">
                        </div>
                        <div class="mb-3">
                            <label for="add_dir_order" class="form-label fw-semibold">Display Order</label>
                            <input type="number" class="form-control rounded-3" id="add_dir_order" name="display_order" value="0" min="0">
                            <div class="form-text">Used for sorting the order on registration and profile forms.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-3" id="btn-add-dir-submit">
                            <i class="fas fa-plus me-2"></i>Add Directorate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Directorate Modal -->
    <div class="modal fade" id="editDirectorateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit me-2 text-primary"></i>Edit Directorate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editDirectorateForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="id" id="edit_dir_id">
                        <div class="mb-3">
                            <label for="edit_dir_name" class="form-label fw-semibold">Directorate Name *</label>
                            <input type="text" class="form-control rounded-3" id="edit_dir_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_dir_order" class="form-label fw-semibold">Display Order</label>
                            <input type="number" class="form-control rounded-3" id="edit_dir_order" name="display_order" min="0">
                        </div>
                        <div class="mb-3">
                            <label for="edit_dir_active" class="form-label fw-semibold">Status</label>
                            <select class="form-select rounded-3" id="edit_dir_active" name="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <div class="form-text">Inactive directorates are hidden from registration/profile pages.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-3" id="btn-edit-dir-submit">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Directorate Unit Modal -->
    <div class="modal fade" id="addUnitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-layer-group me-2 text-success"></i>Add New Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addUnitForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <div class="mb-3">
                            <label for="add_unit_dir" class="form-label fw-semibold">Parent Directorate *</label>
                            <select class="form-select rounded-3" id="add_unit_dir" name="directorate_id" required>
                                <option value="">Select Directorate</option>
                                <?php foreach ($directorates as $dir): ?>
                                    <option value="<?= $dir['id'] ?>"><?= htmlspecialchars($dir['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="add_unit_name" class="form-label fw-semibold">Unit Name *</label>
                            <input type="text" class="form-control rounded-3" id="add_unit_name" name="unit_name" required placeholder="e.g., Software Systems Development Unit">
                        </div>
                        <div class="mb-3">
                            <label for="add_unit_order" class="form-label fw-semibold">Display Order</label>
                            <input type="number" class="form-control rounded-3" id="add_unit_order" name="display_order" value="0" min="0">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4 rounded-3" id="btn-add-unit-submit">
                            <i class="fas fa-plus me-2"></i>Add Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Directorate Unit Modal -->
    <div class="modal fade" id="editUnitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit me-2 text-success"></i>Edit Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUnitForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="id" id="edit_unit_id">
                        <div class="mb-3">
                            <label for="edit_unit_dir" class="form-label fw-semibold">Parent Directorate *</label>
                            <select class="form-select rounded-3" id="edit_unit_dir" name="directorate_id" required>
                                <?php foreach ($directorates as $dir): ?>
                                    <option value="<?= $dir['id'] ?>"><?= htmlspecialchars($dir['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Allows moving this unit to a different directorate.</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_unit_name" class="form-label fw-semibold">Unit Name *</label>
                            <input type="text" class="form-control rounded-3" id="edit_unit_name" name="unit_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_unit_order" class="form-label fw-semibold">Display Order</label>
                            <input type="number" class="form-control rounded-3" id="edit_unit_order" name="display_order" min="0">
                        </div>
                        <div class="mb-3">
                            <label for="edit_unit_active" class="form-label fw-semibold">Status</label>
                            <select class="form-select rounded-3" id="edit_unit_active" name="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4 rounded-3" id="btn-edit-unit-submit">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include __DIR__ . '/partials/scripts.php'; ?>
    <script>
        // Alert generator Helper
        function showAlert(type, message) {
            const container = document.getElementById('alert-container');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show shadow-sm rounded-3`;
            alert.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            container.appendChild(alert);
            window.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(() => {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 300);
            }, 4000);
        }

        // Add Directorate AJAX
        document.getElementById('addDirectorateForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = document.getElementById('btn-add-dir-submit');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';

            try {
                const response = await fetch('<?= url('/admin/directorates/add') ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    showAlert('success', result.message);
                    bootstrap.Modal.getInstance(document.getElementById('addDirectorateModal')).hide();
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showAlert('danger', result.error || 'Failed to add directorate');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-plus me-2"></i>Add Directorate';
                }
            } catch (error) {
                showAlert('danger', 'An error occurred during network communication');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-plus me-2"></i>Add Directorate';
            }
        });

        // Edit Directorate triggers
        function openEditDirectorateModal(id, name, displayOrder, isActive) {
            document.getElementById('edit_dir_id').value = id;
            document.getElementById('edit_dir_name').value = name;
            document.getElementById('edit_dir_order').value = displayOrder;
            document.getElementById('edit_dir_active').value = isActive;
            new bootstrap.Modal(document.getElementById('editDirectorateModal')).show();
        }

        // Edit Directorate AJAX
        document.getElementById('editDirectorateForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = document.getElementById('btn-edit-dir-submit');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

            try {
                const response = await fetch('<?= url('/admin/directorates/update') ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    showAlert('success', result.message);
                    bootstrap.Modal.getInstance(document.getElementById('editDirectorateModal')).hide();
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showAlert('danger', result.error || 'Failed to update directorate');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
                }
            } catch (error) {
                showAlert('danger', 'An error occurred during network communication');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
            }
        });

        // Delete Directorate AJAX
        async function deleteDirectorate(id, name) {
            if (!confirm(`CAUTION: Deleting "${name}" will delete all of its nested units. Are you sure you want to proceed?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('csrf_token', '<?= $csrf_token ?>');
            formData.append('id', id);

            try {
                const response = await fetch('<?= url('/admin/directorates/delete') ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    showAlert('success', result.message);
                    const container = document.getElementById(`directorate-container-${id}`);
                    if (container) {
                        container.style.transition = 'all 0.5s ease';
                        container.style.opacity = '0';
                        container.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            container.remove();
                            // Decrement Total Directorates count on page UI
                            const statTotalDir = document.getElementById('stat-total-directorates');
                            if (statTotalDir) {
                                statTotalDir.innerText = parseInt(statTotalDir.innerText) - 1;
                            }
                        }, 500);
                    } else {
                        setTimeout(() => location.reload(), 1000);
                    }
                } else {
                    showAlert('danger', result.error || 'Failed to delete directorate');
                }
            } catch (error) {
                showAlert('danger', 'An error occurred during network communication');
            }
        }

        // Add Unit AJAX
        document.getElementById('addUnitForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = document.getElementById('btn-add-unit-submit');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';

            try {
                const response = await fetch('<?= url('/admin/directorates/unit-add') ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    showAlert('success', result.message);
                    bootstrap.Modal.getInstance(document.getElementById('addUnitModal')).hide();
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showAlert('danger', result.error || 'Failed to add unit');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-plus me-2"></i>Add Unit';
                }
            } catch (error) {
                showAlert('danger', 'An error occurred during network communication');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-plus me-2"></i>Add Unit';
            }
        });

        // Edit Unit trigger
        function openEditUnitModal(id, directorateId, name, displayOrder, isActive) {
            document.getElementById('edit_unit_id').value = id;
            document.getElementById('edit_unit_dir').value = directorateId;
            document.getElementById('edit_unit_name').value = name;
            document.getElementById('edit_unit_order').value = displayOrder;
            document.getElementById('edit_unit_active').value = isActive;
            new bootstrap.Modal(document.getElementById('editUnitModal')).show();
        }

        // Edit Unit AJAX
        document.getElementById('editUnitForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = document.getElementById('btn-edit-unit-submit');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

            try {
                const response = await fetch('<?= url('/admin/directorates/unit-update') ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    showAlert('success', result.message);
                    bootstrap.Modal.getInstance(document.getElementById('editUnitModal')).hide();
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showAlert('danger', result.error || 'Failed to update unit');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
                }
            } catch (error) {
                showAlert('danger', 'An error occurred during network communication');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
            }
        });

        // Delete Unit AJAX
        async function deleteUnit(id, name) {
            if (!confirm(`Are you sure you want to delete unit "${name}"?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('csrf_token', '<?= $csrf_token ?>');
            formData.append('id', id);

            try {
                const response = await fetch('<?= url('/admin/directorates/unit-delete') ?>', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    showAlert('success', result.message);
                    const badge = document.getElementById(`unit-badge-${id}`);
                    if (badge) {
                        badge.style.transition = 'all 0.4s ease';
                        badge.style.opacity = '0';
                        badge.style.transform = 'scale(0.8)';
                        setTimeout(() => {
                            badge.remove();
                            // Decrement Total Active Units if relevant
                            const statTotalUnits = document.getElementById('stat-total-units');
                            if (statTotalUnits && !badge.classList.contains('inactive-unit')) {
                                statTotalUnits.innerText = Math.max(0, parseInt(statTotalUnits.innerText) - 1);
                            }
                        }, 400);
                    } else {
                        setTimeout(() => location.reload(), 1000);
                    }
                } else {
                    showAlert('danger', result.error || 'Failed to delete unit');
                }
            } catch (error) {
                showAlert('danger', 'An error occurred during network communication');
            }
        }
    </script>
</body>
</html>

<?php
if (!function_exists('url')) {
    require_once __DIR__ . '/../../Helpers/UrlHelper.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff List - TSU</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .table-responsive { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= url('nominal-role/dashboard') ?>">
                <i class="fas fa-users me-2"></i>Staff List Manager
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($_SESSION['email'] ?? '') ?>
                </span>
                <a href="<?= url('logout') ?>" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-list me-2"></i>Filtered Staff List</h2>
                <p class="text-muted mb-0"><?= count($staff) ?> staff member(s) found</p>
            </div>
            <div>
                <a href="<?= url('nominal-role/dashboard') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
                <button type="button" class="btn btn-success" onclick="exportData('csv')">
                    <i class="fas fa-file-csv me-2"></i>Export CSV
                </button>
                <button type="button" class="btn btn-success" onclick="exportData('excel')">
                    <i class="fas fa-file-excel me-2"></i>Export Excel
                </button>
            </div>
        </div>

        <!-- Active Filters -->
        <?php 
        $activeFilters = array_filter($filters);
        if (!empty($activeFilters)): 
        ?>
        <div class="alert alert-info">
            <strong><i class="fas fa-filter me-2"></i>Active Filters:</strong>
            <?php foreach ($activeFilters as $key => $value): ?>
                <span class="badge bg-primary ms-2"><?= ucfirst(str_replace('_', ' ', $key)) ?>: <?= htmlspecialchars($value) ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Staff Table -->
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Staff Number</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Designation</th>
                        <th>Staff Type</th>
                        <th>Faculty/Unit</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($staff)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fas fa-search fa-3x mb-3 d-block"></i>
                                No staff found matching the selected filters
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($staff as $index => $s): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><strong><?= htmlspecialchars($s['staff_number'] ?? 'N/A') ?></strong></td>
                            <td>
                                <?= htmlspecialchars(trim(($s['title'] ?? '') . ' ' . ($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? ''))) ?>
                            </td>
                            <td>
                                <?php if (!empty($s['gender'])): ?>
                                    <span class="badge bg-<?= $s['gender'] === 'Male' ? 'primary' : ($s['gender'] === 'Female' ? 'danger' : 'secondary') ?>">
                                        <?= htmlspecialchars($s['gender']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Not set</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($s['designation'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge bg-<?= $s['staff_type'] === 'teaching' ? 'success' : 'info' ?>">
                                    <?= ucfirst($s['staff_type'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($s['unit'])): ?>
                                    <span class="badge bg-info"><?= htmlspecialchars($s['unit']) ?></span>
                                <?php elseif (!empty($s['faculty'])): ?>
                                    <?= htmlspecialchars($s['faculty']) ?>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($s['department'] ?? 'N/A') ?></td>
                            <td><small><?= htmlspecialchars($s['email']) ?></small></td>
                            <td>
                                <span class="badge bg-<?= $s['account_status'] === 'active' ? 'success' : ($s['account_status'] === 'pending' ? 'warning' : 'danger') ?>">
                                    <?= ucfirst($s['account_status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportData(format) {
            const params = new URLSearchParams(window.location.search);
            params.set('format', format);
            window.location.href = '<?= url('nominal-role/export') ?>?' + params.toString();
        }
    </script>
</body>
</html>

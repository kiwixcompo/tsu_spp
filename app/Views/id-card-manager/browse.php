<?php if (!function_exists('url')) {
    require_once __DIR__ . '/../../Helpers/UrlHelper.php';
}
if (!function_exists('escape_html')) {
    require_once __DIR__ . '/../../Helpers/TextHelper.php';
} ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Profiles - ID Card Manager</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= url('app/Views/id-card-manager/dashboard.php') ?>" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Browse Profiles</h2>
                <p class="text-muted mb-0">Search and select profiles for ID card printing</p>
            </div>
            <a href="<?= url('id-card-manager/dashboard') ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="<?= url('id-card-manager/browse') ?>" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, staff number, email..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="staff_type" class="form-select">
                            <option value="">All Staff Types</option>
                            <option value="teaching" <?= $selectedStaffType === 'teaching' ? 'selected' : '' ?>>Teaching</option>
                            <option value="non-teaching" <?= $selectedStaffType === 'non-teaching' ? 'selected' : '' ?>>Non-Teaching</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="faculty" class="form-select">
                            <option value="">All Faculties</option>
                            <?php foreach ($faculties as $fac): ?>
                                <option value="<?= htmlspecialchars($fac) ?>" <?= $selectedFaculty === $fac ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($fac) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                        <a href="<?= url('id-card-manager/browse') ?>" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="<?= url('id-card-manager/bulk-print') ?>" id="bulkPrintForm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span id="selectedCount">0</span> profile(s) selected
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                <i class="fas fa-check-square me-1"></i>Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                                <i class="fas fa-square me-1"></i>Deselect All
                            </button>
                            <button type="submit" class="btn btn-sm btn-success" id="bulkPrintBtn" disabled>
                                <i class="fas fa-print me-1"></i>Print Selected
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Profiles Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="selectAllCheckbox" onchange="toggleAll(this)">
                                </th>
                                <th>Photo</th>
                                <th>Staff Number</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Faculty/Unit</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($profiles)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-search fa-3x mb-3 d-block"></i>
                                        No profiles found
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($profiles as $profile): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="profile-checkbox" name="profile_ids[]" value="<?= $profile['id'] ?>" form="bulkPrintForm" onchange="updateSelectedCount()">
                                    </td>
                                    <td>
                                        <?php if ($profile['profile_photo']): ?>
                                            <img src="<?= url($profile['profile_photo']) ?>" alt="Photo" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <?= strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($profile['staff_number']) ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($profile['email']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($profile['designation'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php if ($profile['faculty']): ?>
                                            <?= htmlspecialchars($profile['faculty']) ?>
                                            <?php if ($profile['department']): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($profile['department']) ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $profile['account_status'] === 'active' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($profile['account_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= url('admin/id-card-generator?user_id=' . $profile['user_id']) ?>" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="<?= url('directory/profile/' . $profile['profile_slug']) ?>" class="btn btn-sm btn-info" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('.profile-checkbox:checked');
            const count = checkboxes.length;
            document.getElementById('selectedCount').textContent = count;
            document.getElementById('bulkPrintBtn').disabled = count === 0;
        }

        function toggleAll(checkbox) {
            const checkboxes = document.querySelectorAll('.profile-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateSelectedCount();
        }

        function selectAll() {
            document.querySelectorAll('.profile-checkbox').forEach(cb => cb.checked = true);
            document.getElementById('selectAllCheckbox').checked = true;
            updateSelectedCount();
        }

        function deselectAll() {
            document.querySelectorAll('.profile-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAllCheckbox').checked = false;
            updateSelectedCount();
        }
    </script>
</body>
</html>

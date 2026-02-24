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
    <title>Staff List Manager - TSU</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .stat-card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px; }
        .stat-icon.blue { background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); color: white; }
        .stat-icon.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
        .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white; }
        .stat-icon.orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }
        .stat-icon.pink { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); color: white; }
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
        <div class="row mb-4">
            <div class="col">
                <h2><i class="fas fa-chart-bar me-2"></i>Staff Statistics Dashboard</h2>
                <p class="text-muted">View, filter, and export staff information</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="mb-1"><?= number_format($stats['total_staff']) ?></h3>
                    <p class="text-muted mb-0">Total Staff</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3 class="mb-1"><?= number_format($stats['teaching_staff']) ?></h3>
                    <p class="text-muted mb-0">Teaching Staff</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3 class="mb-1"><?= number_format($stats['non_teaching_staff']) ?></h3>
                    <p class="text-muted mb-0">Non-Teaching Staff</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-venus-mars"></i>
                    </div>
                    <h3 class="mb-1"><?= number_format($stats['male_staff']) ?> / <?= number_format($stats['female_staff']) ?></h3>
                    <p class="text-muted mb-0">Male / Female</p>
                </div>
            </div>
        </div>

        <!-- Filter and Export Section -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter & Export Staff List</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= url('nominal-role/staff-list') ?>" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">All Genders</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                                <option value="Prefer not to say">Prefer not to say</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Staff Type</label>
                            <select name="staff_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="teaching">Teaching</option>
                                <option value="non-teaching">Non-Teaching</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Faculty</label>
                            <select name="faculty" class="form-select" id="facultySelect">
                                <option value="">All Faculties</option>
                                <?php foreach ($faculties as $fac): ?>
                                    <option value="<?= htmlspecialchars($fac['faculty']) ?>">
                                        <?= htmlspecialchars($fac['faculty']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Department</label>
                            <select name="department" class="form-select" id="departmentSelect">
                                <option value="">All Departments</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Unit</label>
                            <select name="unit" class="form-select">
                                <option value="">All Units</option>
                                <?php foreach ($units as $unit): ?>
                                    <option value="<?= htmlspecialchars($unit['name']) ?>">
                                        <?= htmlspecialchars($unit['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Account Status</label>
                            <select name="account_status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>View Staff List
                                </button>
                                <button type="button" class="btn btn-success" onclick="exportData('csv')">
                                    <i class="fas fa-file-csv me-2"></i>Export CSV
                                </button>
                                <button type="button" class="btn btn-success" onclick="exportData('excel')">
                                    <i class="fas fa-file-excel me-2"></i>Export Excel
                                </button>
                                <a href="<?= url('nominal-role/dashboard') ?>" class="btn btn-secondary">
                                    <i class="fas fa-redo me-2"></i>Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Info -->
        <div class="alert alert-info mt-4">
            <h6><i class="fas fa-info-circle me-2"></i>How to Use</h6>
            <ul class="mb-0">
                <li>Select filters above to narrow down staff list</li>
                <li>Click "View Staff List" to see filtered results in a table</li>
                <li>Click "Export CSV" or "Export Excel" to download the filtered data</li>
                <li>Leave all filters empty to export/view all staff</li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load departments when faculty changes
        document.getElementById('facultySelect').addEventListener('change', function() {
            const faculty = this.value;
            const deptSelect = document.getElementById('departmentSelect');
            
            deptSelect.innerHTML = '<option value="">All Departments</option>';
            
            if (faculty) {
                fetch('<?= url('departments') ?>/' + encodeURIComponent(faculty))
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(dept => {
                            const option = document.createElement('option');
                            option.value = dept.department;
                            option.textContent = dept.department;
                            deptSelect.appendChild(option);
                        });
                    });
            }
        });

        function exportData(format) {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            params.append('format', format);
            
            window.location.href = '<?= url('nominal-role/export') ?>?' + params.toString();
        }
    </script>
</body>
</html>

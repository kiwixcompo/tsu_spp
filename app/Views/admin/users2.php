<?php
// Load URL helper
if (!function_exists('url')) {
    require_once __DIR__ . '/../../Helpers/UrlHelper.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - Admin Panel</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            overflow-x: hidden;
        }
        .admin-sidebar {
            background: #2c3e50;
            min-height: 100vh;
            padding-top: 2rem;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }
        .admin-sidebar.collapsed {
            width: 70px;
        }
        .admin-sidebar.collapsed .sidebar-text {
            display: none;
        }
        .admin-sidebar.collapsed .text-center h4 {
            font-size: 0;
        }
        .admin-sidebar.collapsed .text-center h4 i {
            font-size: 1.5rem;
        }
        .admin-sidebar .nav-link {
            color: #ecf0f1;
            padding: 1rem 1.5rem;
            border-radius: 0;
            margin-bottom: 0.5rem;
            white-space: nowrap;
            transition: all 0.3s ease;
        }
        .admin-sidebar.collapsed .nav-link {
            padding: 1rem 0.5rem;
            text-align: center;
        }
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            background: #34495e;
            color: white;
        }
        .sidebar-toggle {
            position: absolute;
            top: 10px;
            right: -15px;
            background: #2c3e50;
            color: white;
            border: 2px solid #34495e;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1001;
            transition: all 0.3s ease;
        }
        .sidebar-toggle:hover {
            background: #34495e;
        }
        .main-content {
            margin-left: 250px;
            transition: all 0.3s ease;
            width: calc(100% - 250px);
        }
        .main-content.expanded {
            margin-left: 70px;
            width: calc(100% - 70px);
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        /* Modal Preview Styles */
        .preview-modal-dialog {
            max-width: 900px;
        }
        .id-card {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 70px;
            }
            .admin-sidebar .sidebar-text {
                display: none;
            }
            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="admin-sidebar" id="adminSidebar">
                <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
                    <i class="fas fa-chevron-left" id="toggleIcon"></i>
                </button>
                <div class="text-center mb-4">
                    <h4 class="text-white">
                        <i class="fas fa-shield-alt me-2"></i><span class="sidebar-text">Admin Panel</span>
                    </h4>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link" href="<?= url('/admin/dashboard') ?>">
                        <i class="fas fa-tachometer-alt me-2"></i><span class="sidebar-text">Dashboard</span>
                    </a>
                    <a class="nav-link active" href="<?= url('/admin/users') ?>">
                        <i class="fas fa-users me-2"></i><span class="sidebar-text">Users Management</span>
                    </a>
                    <a class="nav-link" href="<?= url('/admin/publications') ?>">
                        <i class="fas fa-book me-2"></i><span class="sidebar-text">Publications</span>
                    </a>
                    <a class="nav-link" href="<?= url('/admin/analytics') ?>">
                        <i class="fas fa-chart-line me-2"></i><span class="sidebar-text">Analytics</span>
                    </a>
                    <a class="nav-link" href="<?= url('/admin/activity-logs') ?>">
                        <i class="fas fa-history me-2"></i><span class="sidebar-text">Activity Logs</span>
                    </a>
                    <a class="nav-link" href="<?= url('/admin/faculties-departments') ?>">
                        <i class="fas fa-building me-2"></i><span class="sidebar-text">Faculties & Departments</span>
                    </a>
                    <a class="nav-link" href="<?= url('/admin/settings') ?>">
                        <i class="fas fa-cog me-2"></i><span class="sidebar-text">System Settings</span>
                    </a>
                    <hr class="text-white">
                    <a class="nav-link" href="<?= url('/logout') ?>">
                        <i class="fas fa-sign-out-alt me-2"></i><span class="sidebar-text">Logout</span>
                    </a>
                </nav>
            </div>

            <div class="main-content" id="mainContent">
                <div class="p-4">
                    <h1 class="h3 mb-4">
                        <i class="fas fa-users me-2"></i>Users Management
                    </h1>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3 class="text-primary"><?= $total_users ?? 0 ?></h3>
                                    <p class="mb-0">Total Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3 class="text-success"><?= count(array_filter($users ?? [], fn($u) => $u['account_status'] === 'active')) ?></h3>
                                    <p class="mb-0">Active Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3 class="text-warning"><?= count(array_filter($users ?? [], fn($u) => $u['account_status'] === 'pending')) ?></h3>
                                    <p class="mb-0">Pending Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3 class="text-danger"><?= count(array_filter($users ?? [], fn($u) => !$u['email_verified'])) ?></h3>
                                    <p class="mb-0">Unverified Emails</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label">Filter by Status</label>
                                    <select class="form-select" id="filterStatus">
                                        <option value="">All Statuses</option>
                                        <option value="active">Active</option>
                                        <option value="pending">Pending</option>
                                        <option value="suspended">Suspended</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Email Verification</label>
                                    <select class="form-select" id="filterVerification">
                                        <option value="">All</option>
                                        <option value="verified">Verified</option>
                                        <option value="unverified">Unverified</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">ID Card Status</label>
                                    <select class="form-select" id="filterIDCard">
                                        <option value="">All</option>
                                        <option value="has-idcard">Has ID Card</option>
                                        <option value="no-idcard">No ID Card</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Filter by Faculty</label>
                                    <select class="form-select" id="filterFaculty">
                                        <option value="">All Faculties</option>
                                        <?php
                                        $faculties = array_unique(array_column($users ?? [], 'faculty'));
                                        sort($faculties);
                                        foreach ($faculties as $faculty):
                                            if ($faculty):
                                        ?>
                                            <option value="<?= htmlspecialchars($faculty) ?>"><?= htmlspecialchars($faculty) ?></option>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Registered Date</label>
                                    <select class="form-select" id="filterDate">
                                        <option value="">All Time</option>
                                        <option value="today">Today</option>
                                        <option value="week">Last 7 Days</option>
                                        <option value="month">Last 30 Days</option>
                                        <option value="3months">Last 3 Months</option>
                                        <option value="6months">Last 6 Months</option>
                                        <option value="year">Last Year</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary w-100" onclick="applyFilters()">
                                        <i class="fas fa-filter me-2"></i>Apply
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button class="btn btn-primary" onclick="bulkGenerateIDCards()" id="bulkIDCardBtn" disabled>
                                            <i class="fas fa-id-card me-2"></i>Generate & Preview ID Cards
                                        </button>
                                        <button class="btn btn-info" onclick="bulkVerify()" id="bulkVerifyBtn" disabled>
                                            <i class="fas fa-envelope-circle-check me-2"></i>Verify Selected
                                        </button>
                                        <button class="btn btn-success" onclick="bulkActivate()" id="bulkActivateBtn" disabled>
                                            <i class="fas fa-check me-2"></i>Activate Selected
                                        </button>
                                        <button class="btn btn-warning" onclick="bulkSuspend()" id="bulkSuspendBtn" disabled>
                                            <i class="fas fa-ban me-2"></i>Suspend Selected
                                        </button>
                                        <button class="btn btn-danger" onclick="bulkDelete()" id="bulkDeleteBtn" disabled>
                                            <i class="fas fa-trash me-2"></i>Delete Selected
                                        </button>
                                        <span class="badge bg-secondary align-self-center ms-2 fs-6">
                                            <span id="selectedCount">0</span> selected
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <input type="text" id="userSearch" class="form-control" placeholder="Search by name, email, staff ID, faculty, or department...">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                            </th>
                                            <th>Name</th>
                                            <th>Staff ID</th>
                                            <th>Email</th>
                                            <th>Faculty</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                            <th>Email Verified</th>
                                            <th>Registered</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($users)): ?>
                                            <tr>
                                                <td colspan="10" class="text-center text-muted">No users found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($users as $user): ?>
                                                <?php
                                                    $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                                                    $staffId = $user['staff_number'] ?? '';
                                                ?>
                                                <tr class="user-row" 
                                                    data-status="<?= $user['account_status'] ?>" 
                                                    data-verified="<?= $user['email_verified'] ? 'verified' : 'unverified' ?>"
                                                    data-idcard="<?= ($user['account_status'] === 'active' && !empty($user['profile_slug'])) ? 'has-idcard' : 'no-idcard' ?>"
                                                    data-faculty="<?= htmlspecialchars($user['faculty'] ?? '') ?>" 
                                                    data-department="<?= htmlspecialchars($user['department'] ?? '') ?>"
                                                    data-name="<?= htmlspecialchars($fullName) ?>"
                                                    data-email="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                                    data-staff="<?= htmlspecialchars($staffId) ?>"
                                                    data-date="<?= $user['created_at'] ?>">
                                                    <td>
                                                        <?php if (($user['role'] ?? 'user') !== 'admin'): ?>
                                                            <input type="checkbox" class="user-checkbox" value="<?= $user['id'] ?>" onchange="updateBulkButtons()">
                                                        <?php else: ?>
                                                            <span class="text-muted" title="Admin users cannot be bulk selected">
                                                                <i class="fas fa-shield-alt"></i>
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'No Name' ?>
                                                    </td>
                                                    <td class="text-nowrap fw-bold text-primary">
                                                        <?= htmlspecialchars($staffId) ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                                    <td><?= htmlspecialchars($user['faculty'] ?? 'N/A') ?></td>
                                                    <td><?= htmlspecialchars($user['department'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= $user['account_status'] === 'active' ? 'success' : ($user['account_status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                            <?= ucfirst($user['account_status']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($user['email_verified']): ?>
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i> Verified
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i> Unverified
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <?php if ($user['account_status'] === 'active' && !empty($user['profile_slug'])): ?>
                                                                <button class="btn btn-primary" onclick="generateIDCard(<?= $user['id'] ?>)" title="Generate ID Card">
                                                                    <i class="fas fa-id-card"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                            <?php if (!$user['email_verified']): ?>
                                                                <button class="btn btn-info" onclick="verifyUser(<?= $user['id'] ?>)" title="Verify Email">
                                                                    <i class="fas fa-envelope-circle-check"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                            <?php if ($user['account_status'] === 'pending'): ?>
                                                                <button class="btn btn-success" onclick="activateUser(<?= $user['id'] ?>)" title="Activate">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            <?php elseif ($user['account_status'] === 'active' && ($user['role'] ?? 'user') !== 'admin'): ?>
                                                                <button class="btn btn-warning" onclick="suspendUser(<?= $user['id'] ?>)" title="Suspend">
                                                                    <i class="fas fa-ban"></i>
                                                                </button>
                                                            <?php elseif ($user['account_status'] === 'suspended'): ?>
                                                                <button class="btn btn-info" onclick="reinstateUser(<?= $user['id'] ?>)" title="Reinstate">
                                                                    <i class="fas fa-undo"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                            <?php if (($user['role'] ?? 'user') !== 'admin'): ?>
                                                                <button class="btn btn-danger" onclick="deleteUser(<?= $user['id'] ?>)" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if (($total_pages ?? 1) > 1): ?>
                                <nav>
                                    <ul class="pagination justify-content-center">
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?= $i === ($current_page ?? 1) ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog preview-modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk ID Card Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light" style="max-height: 70vh; overflow-y: auto;">
                    <div id="previewContainer" class="d-flex flex-wrap justify-content-center gap-4">
                        </div>
                </div>
                <div class="modal-footer">
                    <div class="me-auto text-muted small">
                        Note: Images may take a moment to generate.
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="downloadAllBtn" onclick="downloadAllPDFs()">
                        <i class="fas fa-download me-2"></i>Download All (PDFs)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    
    <script>
        // CSRF Token for AJAX
        const csrfToken = '<?= $_SESSION['csrf_token'] ?? '' ?>';

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkButtons();
        }

        function updateBulkButtons() {
            const checkboxes = document.querySelectorAll('.user-checkbox:checked');
            const count = checkboxes.length;
            document.getElementById('selectedCount').textContent = count;
            document.getElementById('bulkIDCardBtn').disabled = count === 0;
            document.getElementById('bulkDeleteBtn').disabled = count === 0;
            document.getElementById('bulkSuspendBtn').disabled = count === 0;
            document.getElementById('bulkActivateBtn').disabled = count === 0;
            document.getElementById('bulkVerifyBtn').disabled = count === 0;
        }

        function getSelectedUserIds() {
            const checkboxes = document.querySelectorAll('.user-checkbox:checked');
            return Array.from(checkboxes).map(cb => cb.value);
        }

        function applyFilters() {
            const status = document.getElementById('filterStatus').value;
            const verification = document.getElementById('filterVerification').value;
            const idcard = document.getElementById('filterIDCard').value;
            const faculty = document.getElementById('filterFaculty').value;
            const dateFilter = document.getElementById('filterDate').value;
            const searchTerm = (document.getElementById('userSearch')?.value || '').toLowerCase().trim();
            
            const rows = document.querySelectorAll('.user-row');
            
            rows.forEach(row => {
                let show = true;
                const haystack = [
                    row.dataset.name || '',
                    row.dataset.email || '',
                    row.dataset.staff || '',
                    row.dataset.faculty || '',
                    row.dataset.department || ''
                ].join(' ').toLowerCase();
                
                // Text search
                if (searchTerm && !haystack.includes(searchTerm)) {
                    show = false;
                }
                
                // Status filter
                if (status && row.dataset.status !== status) {
                    show = false;
                }
                
                // Verification filter
                if (verification && row.dataset.verified !== verification) {
                    show = false;
                }
                
                // ID Card filter
                if (idcard && row.dataset.idcard !== idcard) {
                    show = false;
                }
                
                // Faculty filter
                if (faculty && row.dataset.faculty !== faculty) {
                    show = false;
                }
                
                // Date filter (Logic as before...)
                if (dateFilter) {
                    const rowDate = new Date(row.dataset.date);
                    const now = new Date();
                    let cutoffDate = new Date();
                    switch(dateFilter) {
                        case 'today': cutoffDate.setHours(0, 0, 0, 0); break;
                        case 'week': cutoffDate.setDate(now.getDate() - 7); break;
                        case 'month': cutoffDate.setDate(now.getDate() - 30); break;
                        case '3months': cutoffDate.setMonth(now.getMonth() - 3); break;
                        case '6months': cutoffDate.setMonth(now.getMonth() - 6); break;
                        case 'year': cutoffDate.setFullYear(now.getFullYear() - 1); break;
                    }
                    if (rowDate < cutoffDate) show = false;
                }
                
                row.style.display = show ? '' : 'none';
            });
        }

        document.getElementById('userSearch')?.addEventListener('input', applyFilters);

        // Action Functions Implementation
        async function deleteUser(userId) {
            if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch('<?= url('admin/delete-user') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ user_id: userId })
                });

                const data = await response.json();
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to delete user'));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        async function bulkDelete() {
            const userIds = getSelectedUserIds();
            if (userIds.length === 0) {
                alert('Please select users to delete.');
                return;
            }

            if (!confirm(`Are you sure you want to delete ${userIds.length} user(s)? This action cannot be undone.`)) {
                return;
            }

            try {
                const response = await fetch('<?= url('admin/bulk-delete-users') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ user_ids: userIds })
                });

                const data = await response.json();
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to delete users'));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        async function suspendUser(userId) {
            const reason = prompt('Please enter a reason for suspension (optional):');
            if (reason === null) return; // User cancelled

            try {
                const response = await fetch('<?= url('admin/suspend-user') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ user_id: userId, reason: reason })
                });

                const data = await response.json();
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to suspend user'));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        async function bulkSuspend() {
            const userIds = getSelectedUserIds();
            if (userIds.length === 0) {
                alert('Please select users to suspend.');
                return;
            }

            const reason = prompt('Please enter a reason for suspension (optional):');
            if (reason === null) return; // User cancelled

            try {
                const response = await fetch('<?= url('admin/bulk-suspend-users') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ user_ids: userIds, reason: reason })
                });

                const data = await response.json();
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to suspend users'));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        async function activateUser(userId) {
            if (!confirm('Are you sure you want to activate this user?')) {
                return;
            }

            try {
                const response = await fetch('<?= url('admin/activate-user') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ user_id: userId })
                });

                const data = await response.json();
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to activate user'));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        async function bulkActivate() {
            const userIds = getSelectedUserIds();
            if (userIds.length === 0) {
                alert('Please select users to activate.');
                return;
            }

            if (!confirm(`Are you sure you want to activate ${userIds.length} user(s)?`)) {
                return;
            }

            try {
                const response = await fetch('<?= url('admin/bulk-activate-users') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ user_ids: userIds })
                });

                const data = await response.json();
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to activate users'));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        async function reinstateUser(userId) {
            if (!confirm('Are you sure you want to reinstate this user?')) {
                return;
            }

            try {
                const response = await fetch('<?= url('admin/reinstate-user') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ user_id: userId })
                });

                const data = await response.json();
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to reinstate user'));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        async function verifyUser(userId) {
            if (!confirm('Are you sure you want to manually verify this user\'s email?')) {
                return;
            }

            try {
                const response = await fetch('<?= url('admin/verify-user') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ user_id: userId })
                });

                const data = await response.json();
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to verify user'));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        async function bulkVerify() {
            const userIds = getSelectedUserIds();
            if (userIds.length === 0) {
                alert('Please select users to verify.');
                return;
            }

            if (!confirm(`Are you sure you want to verify ${userIds.length} user(s)?`)) {
                return;
            }

            try {
                const response = await fetch('<?= url('admin/bulk-verify-users') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ user_ids: userIds })
                });

                const data = await response.json();
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to verify users'));
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        // --- NEW: Bulk ID Card Generation & Modal Preview ---

        function generateIDCard(userId) {
            // Single generation: redirect to the preview page
            window.open('<?= url('admin/id-cards/preview') ?>/' + userId, '_blank');
        }

        async function bulkGenerateIDCards() {
            const userIds = getSelectedUserIds();
            if (userIds.length === 0) {
                alert('Please select users to generate ID cards for.');
                return;
            }

            const btn = document.getElementById('bulkIDCardBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
            btn.disabled = true;

            try {
                // Call API to ensure QR codes exist and get profile data
                const response = await fetch('<?= url('admin/id-cards/bulk-generate') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ user_ids: userIds })
                });

                const data = await response.json();
                if (!data.success) throw new Error(data.error || 'Failed to generate ID cards');

                // Build HTML for Modal
                const container = document.getElementById('previewContainer');
                container.innerHTML = ''; // Clear previous
                
                // Store profiles for download function
                window.generatedProfiles = data.profiles;

                data.profiles.forEach(profile => {
                    container.innerHTML += buildCardHtml(profile);
                });

                // Show Modal
                const modal = new bootstrap.Modal(document.getElementById('previewModal'));
                modal.show();

            } catch (err) {
                alert('Error: ' + err.message);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        function buildCardHtml(profile) {
            const staffId = profile.staff_number || ('TSU-' + String(profile.id).padStart(5, '0'));
            const nameParts = [profile.title, profile.first_name, profile.middle_name, profile.last_name].filter(Boolean);
            const fullName = nameParts.join(' ');
            const bloodGroup = profile.blood_group || '';
            
            // Fix image URL logic
            let photoUrl = '';
            if (profile.profile_photo) {
                photoUrl = (profile.profile_photo.startsWith('http')) 
                    ? profile.profile_photo 
                    : (profile.profile_photo.startsWith('uploads') || profile.profile_photo.startsWith('/uploads'))
                        ? '<?= url('') ?>/' + profile.profile_photo.replace(/^\//, '')
                        : '<?= asset('uploads/profiles') ?>/' + profile.profile_photo;
            }
            
            const qrUrl = profile.qr_code_url || '';

            return `
                <div style="display:inline-block; margin: 10px;" class="id-card-wrapper">
                    <!-- FRONT OF CARD -->
                    <div class="id-card" style="width:350px;height:550px;background:linear-gradient(to bottom, rgba(232,238,245,1) 0%, rgba(245,248,252,1) 100%);border-radius:15px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.2);position:relative;page-break-after:always;">
                        <div class="id-card-front" style="height:100%;position:relative;">
                            <!-- Building Background -->
                            <div style="position:absolute;top:0;left:0;right:0;bottom:0;background-image:url('<?= asset('assets/images/tsu-building.jpg') ?>');background-size:cover;background-position:center;opacity:0.25;z-index:0;"></div>
                            <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:linear-gradient(to bottom, rgba(255,255,255,0.75) 0%, rgba(255,255,255,0.85) 40%, rgba(255,255,255,0.90) 100%);z-index:1;"></div>
                            
                            <!-- Vertical Staff ID Card Text -->
                            <div style="position:absolute;left:0;top:320px;height:170px;width:50px;background:#1e3a8a;display:flex;align-items:center;justify-content:center;z-index:3;">
                                <span style="writing-mode:vertical-lr;transform:rotate(180deg);color:white;font-weight:bold;font-size:13px;letter-spacing:2px;white-space:nowrap;">STAFF ID CARD</span>
                            </div>
                            
                            <!-- Header with Logo -->
                            <div style="padding:20px 20px 15px;text-align:center;position:relative;z-index:2;">
                                <img src="<?= asset('assets/images/tsu-logo.png') ?>" style="width:80px;height:80px;margin-bottom:10px;">
                                <h5 style="font-size:18px;font-weight:bold;color:#1e3a8a;margin:0 0 3px 0;letter-spacing:0.5px;">TARABA STATE UNIVERSITY</h5>
                                <div style="font-size:14px;color:#1e3a8a;font-weight:600;margin:0;border-top:2px solid #1e3a8a;border-bottom:2px solid #1e3a8a;padding:3px 0;display:inline-block;">JALINGO</div>
                            </div>
                            
                            <!-- Photo -->
                            <div style="text-align:center;padding:15px 60px 15px 20px;position:relative;z-index:2;">
                                ${photoUrl ? 
                                    `<img src="${photoUrl}" crossorigin="anonymous" style="width:180px;height:220px;object-fit:cover;border-radius:8px;border:4px solid #1e3a8a;box-shadow:0 4px 12px rgba(0,0,0,0.15);" loading="lazy" decoding="async">` : 
                                    `<div style="width:180px;height:220px;background:#1e3a8a;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:60px;font-weight:bold;border-radius:8px;border:4px solid #1e3a8a;">${(profile.first_name[0]||'')}${(profile.last_name[0]||'')}</div>`
                                }
                            </div>
                            
                            <!-- Name and Designation -->
                            <div style="padding:10px 60px 10px 20px;text-align:center;position:relative;z-index:2;">
                                <h4 style="font-size:20px;font-weight:bold;color:#1e3a8a;margin:0 0 5px 0;line-height:1.2;">${fullName}</h4>
                                <div style="font-size:13px;color:#374151;margin-bottom:15px;font-weight:500;">${profile.designation || ''}</div>
                            </div>
                            
                            <!-- Info Section -->
                            <div style="padding:0 60px 15px 20px;font-size:13px;position:relative;z-index:2;">
                                <div style="display:flex;margin-bottom:8px;background:rgba(255,255,255,0.85);padding:8px 12px;border-radius:4px;border-left:3px solid #1e3a8a;">
                                    <b style="color:#1e3a8a;min-width:80px;">Staff ID:</b>
                                    <span style="color:#1f2937;font-weight:500;">${staffId}</span>
                                </div>
                                <div style="display:flex;margin-bottom:8px;background:rgba(255,255,255,0.85);padding:8px 12px;border-radius:4px;border-left:3px solid #1e3a8a;">
                                    <b style="color:#1e3a8a;min-width:80px;">Faculty:</b>
                                    <span style="color:#1f2937;font-weight:500;">${profile.faculty || ''}</span>
                                </div>
                                <div style="display:flex;margin-bottom:8px;background:rgba(255,255,255,0.85);padding:8px 12px;border-radius:4px;border-left:3px solid #1e3a8a;">
                                    <b style="color:#1e3a8a;min-width:80px;">Dept:</b>
                                    <span style="color:#1f2937;font-weight:500;">${profile.department || ''}</span>
                                </div>
                            </div>
                            
                            <!-- Footer -->
                            <div style="position:absolute;bottom:0;left:0;right:0;background:#1e3a8a;color:#fff;text-align:center;padding:12px;font-size:11px;font-weight:500;z-index:2;">
                                Issued: <?= date('F Y') ?>
                            </div>
                        </div>
                    </div>

                    <!-- BACK OF CARD -->
                    <div class="id-card" style="width:350px;height:550px;background:linear-gradient(135deg,#e8eef5 0%,#f5f8fc 100%);border-radius:15px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.2);margin-top:20px;position:relative;">
                        <div class="id-card-back" style="height:100%;display:flex;flex-direction:column;position:relative;">
                            <!-- TSU Watermark -->
                            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:180px;font-weight:900;color:rgba(30,58,138,0.08);font-family:Arial Black,sans-serif;letter-spacing:-10px;z-index:1;user-select:none;pointer-events:none;">TSU</div>
                            
                            <!-- Header -->
                            <div style="background:#1e3a8a;color:#fff;padding:15px;text-align:center;position:relative;z-index:2;">
                                <h6 style="margin:0;font-size:14px;font-weight:bold;letter-spacing:1px;">SCAN FOR PROFILE</h6>
                            </div>
                            
                            <!-- QR Code, Blood Group and Footer -->
                            <div style="flex-grow:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;padding:25px 20px 20px;position:relative;z-index:2;">
                                <div style="text-align:center;flex-shrink:0;margin-bottom:auto;">
                                    ${qrUrl ? 
                                        `<img src="${qrUrl}" crossorigin="anonymous" style="width:180px;height:180px;border:4px solid #1e3a8a;border-radius:12px;background:#fff;padding:10px;box-shadow:0 4px 12px rgba(0,0,0,0.15);" loading="lazy" decoding="async">` : 
                                        '<div style="width:180px;height:180px;border:4px solid #1e3a8a;border-radius:12px;background:#fff;display:flex;align-items:center;justify-content:center;color:#999;">QR Missing</div>'
                                    }
                                    <p style="margin-top:12px;font-size:11px;color:#374151;font-weight:500;">Scan to view profile</p>
                                </div>
                                
                                ${bloodGroup ? `
                                <div style="text-align:center;margin-top:15px;margin-bottom:15px;padding:12px 20px;background:rgba(255,255,255,0.8);border-radius:8px;border:2px solid #1e3a8a;width:100%;max-width:200px;">
                                    <div style="font-size:10px;color:#6b7280;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Blood Group</div>
                                    <div style="font-size:24px;color:#1e3a8a;font-weight:bold;margin-top:3px;">${bloodGroup}</div>
                                </div>
                                ` : ''}
                                
                                <div style="text-align:center;font-size:10px;color:#1f2937;padding:15px;background:rgba(255,255,255,0.75);border-radius:8px;width:100%;max-width:280px;margin-top:auto;">
                                    <p style="margin:4px 0;line-height:1.4;"><strong style="color:#1e3a8a;font-weight:700;">IMPORTANT</strong></p>
                                    <p style="margin:4px 0;line-height:1.4;">This card is property of TSU</p>
                                    <p style="margin:4px 0;line-height:1.4;">If found, please return to:</p>
                                    <p style="margin:4px 0;line-height:1.4;"><strong style="color:#1e3a8a;font-weight:700;">Security Unit</strong></p>
                                    <p style="margin:4px 0;line-height:1.4;">Taraba State University, Jalingo</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        async function downloadAllPDFs() {
            if (!window.generatedProfiles || window.generatedProfiles.length === 0) return;

            const btn = document.getElementById('downloadAllBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            btn.disabled = true;

            try {
                const zip = new JSZip();
                const { jsPDF } = window.jspdf;
                
                // Use a hidden container to render cards clean of modal styles if needed
                // But typically html2canvas works on visible elements. 
                // We'll iterate through the rendered cards in the modal.
                const wrappers = document.querySelectorAll('#previewContainer .id-card-wrapper');
                
                for (let i = 0; i < wrappers.length; i++) {
                    const wrapper = wrappers[i];
                    const cards = wrapper.querySelectorAll('.id-card'); // [0] = front, [1] = back
                    const profile = window.generatedProfiles[i];

                    btn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>Card ${i+1}/${wrappers.length}...`;

                    const pdf = new jsPDF({ orientation: 'portrait', unit: 'in', format: [3.5, 5.5] });

                    // Front
                    const frontCanvas = await html2canvas(cards[0], { scale: 2, useCORS: true, allowTaint: true, backgroundColor: '#ffffff' });
                    pdf.addImage(frontCanvas.toDataURL('image/png'), 'PNG', 0, 0, 3.5, 5.5);
                    
                    // Back
                    pdf.addPage();
                    const backCanvas = await html2canvas(cards[1], { scale: 2, useCORS: true, allowTaint: true, backgroundColor: '#f8f9fa' });
                    pdf.addImage(backCanvas.toDataURL('image/png'), 'PNG', 0, 0, 3.5, 5.5);

                    // Add to ZIP
                    const staffId = (profile.staff_number || `TSU-${profile.id}`).replace(/[^a-zA-Z0-9]/g, '');
                    const name = `${profile.first_name}_${profile.last_name}`.replace(/[^a-zA-Z0-9]/g, '');
                    zip.file(`${staffId}_${name}.pdf`, pdf.output('blob'));
                }

                btn.innerHTML = '<i class="fas fa-file-archive me-2"></i>Zipping...';
                const content = await zip.generateAsync({type: "blob"});
                saveAs(content, "TSU_Staff_ID_Cards.zip");

            } catch (err) {
                console.error(err);
                alert('Error generating PDF batch: ' + err.message);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        // Sidebar Toggle Function
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('toggleIcon');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            if (sidebar.classList.contains('collapsed')) {
                toggleIcon.classList.remove('fa-chevron-left');
                toggleIcon.classList.add('fa-chevron-right');
                localStorage.setItem('sidebarCollapsed', 'true');
            } else {
                toggleIcon.classList.remove('fa-chevron-right');
                toggleIcon.classList.add('fa-chevron-left');
                localStorage.setItem('sidebarCollapsed', 'false');
            }
        }

        // Restore sidebar state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (sidebarCollapsed) {
                document.getElementById('adminSidebar').classList.add('collapsed');
                document.getElementById('mainContent').classList.add('expanded');
                document.getElementById('toggleIcon').classList.remove('fa-chevron-left');
                document.getElementById('toggleIcon').classList.add('fa-chevron-right');
            }
        });
    </script>
</body>
</html>
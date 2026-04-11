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
        body { overflow-x: hidden; }
        .admin-sidebar { background: #2c3e50; min-height: 100vh; padding-top: 2rem; position: fixed; left: 0; top: 0; width: 220px; transition: all 0.3s ease; z-index: 1000; overflow-y: auto; }
        .admin-sidebar.collapsed { width: 60px; }
        .admin-sidebar.collapsed .sidebar-text { display: none; }
        .admin-sidebar.collapsed .text-center h4 .sidebar-text { display: none; }
        .admin-sidebar .nav-link { color: #ecf0f1; padding: 0.75rem 1.25rem; border-radius: 0; margin-bottom: 0.25rem; white-space: nowrap; transition: all 0.3s ease; font-size: 0.875rem; }
        .admin-sidebar.collapsed .nav-link { padding: 0.75rem 0; text-align: center; }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active { background: #34495e; color: white; }
        .sidebar-toggle { position: absolute; top: 12px; right: -14px; background: #2c3e50; color: white; border: 2px solid #34495e; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 1001; transition: all 0.3s ease; font-size: 12px; }
        .sidebar-toggle:hover { background: #34495e; }
        .main-content { margin-left: 220px; transition: all 0.3s ease; width: calc(100% - 220px); min-width: 0; }
        .main-content.expanded { margin-left: 60px; width: calc(100% - 60px); }
        /* Compact table */
        .table th, .table td { font-size: 0.78rem; padding: 0.4rem 0.5rem; vertical-align: middle; white-space: nowrap; }
        .table th { font-size: 0.75rem; }
        .btn-group-sm .btn { padding: 0.2rem 0.4rem; font-size: 0.75rem; }
        .badge { font-size: 0.7rem; }
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .preview-modal-dialog { max-width: 900px; }
        @media (max-width: 768px) { .admin-sidebar { width: 60px; } .admin-sidebar .sidebar-text { display: none; } .main-content { margin-left: 60px; width: calc(100% - 60px); } }
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
                    <h4 class="text-white"><i class="fas fa-shield-alt me-2"></i><span class="sidebar-text">Admin Panel</span></h4>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="<?= url('/admin/dashboard') ?>"><i class="fas fa-tachometer-alt me-2"></i><span class="sidebar-text">Dashboard</span></a>
                    <a class="nav-link active" href="<?= url('/admin/users') ?>"><i class="fas fa-users me-2"></i><span class="sidebar-text">Users Management</span></a>
                    <a class="nav-link" href="<?= url('/admin/publications') ?>"><i class="fas fa-book me-2"></i><span class="sidebar-text">Publications</span></a>
                    <a class="nav-link" href="<?= url('/admin/analytics') ?>"><i class="fas fa-chart-line me-2"></i><span class="sidebar-text">Analytics</span></a>
                    <a class="nav-link" href="<?= url('/admin/activity-logs') ?>"><i class="fas fa-history me-2"></i><span class="sidebar-text">Activity Logs</span></a>
                    <a class="nav-link" href="<?= url('/admin/faculties-departments') ?>"><i class="fas fa-building me-2"></i><span class="sidebar-text">Faculties & Departments</span></a>
                    <a class="nav-link" href="<?= url('/admin/settings') ?>"><i class="fas fa-cog me-2"></i><span class="sidebar-text">System Settings</span></a>
                    <hr class="text-white">
                    <a class="nav-link" href="<?= url('/logout') ?>"><i class="fas fa-sign-out-alt me-2"></i><span class="sidebar-text">Logout</span></a>
                </nav>
            </div>

            <div class="main-content" id="mainContent">
                <div class="p-4">
                    <h1 class="h3 mb-4"><i class="fas fa-users me-2"></i>Users Management</h1>

                    <div class="row mb-4">
                        <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-primary"><?= $total_users ?? 0 ?></h3><p class="mb-0">Total Users</p></div></div></div>
                        <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-success"><?= count(array_filter($users ?? [], fn($u) => $u['account_status'] === 'active')) ?></h3><p class="mb-0">Active Users</p></div></div></div>
                        <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-warning"><?= count(array_filter($users ?? [], fn($u) => $u['account_status'] === 'pending')) ?></h3><p class="mb-0">Pending Users</p></div></div></div>
                        <div class="col-md-3"><div class="card"><div class="card-body text-center"><h3 class="text-danger"><?= count(array_filter($users ?? [], fn($u) => !$u['email_verified'])) ?></h3><p class="mb-0">Unverified Emails</p></div></div></div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex gap-2 flex-wrap mb-3">
                                <button class="btn btn-primary" onclick="bulkGenerateIDCards()" id="bulkIDCardBtn" disabled><i class="fas fa-id-card me-2"></i>Generate ID Cards</button>
                                <button class="btn btn-info" onclick="bulkVerify()" id="bulkVerifyBtn" disabled><i class="fas fa-envelope-circle-check me-2"></i>Verify</button>
                                <button class="btn btn-success" onclick="bulkActivate()" id="bulkActivateBtn" disabled><i class="fas fa-check me-2"></i>Activate</button>
                                <button class="btn btn-warning" onclick="bulkSuspend()" id="bulkSuspendBtn" disabled><i class="fas fa-ban me-2"></i>Suspend</button>
                                <button class="btn btn-danger" onclick="bulkDelete()" id="bulkDeleteBtn" disabled><i class="fas fa-trash me-2"></i>Delete</button>
                                <button class="btn btn-secondary" onclick="bulkSendPhotoReminder()" id="bulkPhotoReminderBtn" disabled title="Send photo update reminder email"><i class="fas fa-camera me-2"></i>Send Photo Reminder</button>
                                <button class="btn btn-outline-success" onclick="exportToExcel()"><i class="fas fa-file-excel me-2"></i>Export to Excel</button>
                                <span class="badge bg-secondary align-self-center ms-2 fs-6"><span id="selectedCount">0</span> selected</span>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <input type="text" id="userSearch" class="form-control" placeholder="Search by Name, Staff ID, Email, Faculty, or Unit...">
                                </div>
                                <div class="col-md-6 text-end">
                                    <span class="text-muted">Showing <span id="visibleCount"><?= count($users ?? []) ?></span> of <span id="totalCount"><?= $total_users ?? 0 ?></span> users</span>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-2">
                                    <select id="staffTypeFilter" class="form-select" onchange="performSearch()">
                                        <option value="">All Staff Types</option>
                                        <option value="teaching">Teaching Staff</option>
                                        <option value="non-teaching">Non-Teaching Staff</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="genderFilter" class="form-select" onchange="performSearch()">
                                        <option value="">All Genders</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="facultyFilter" class="form-select" onchange="performSearch()">
                                        <option value="">All Faculties</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="unitFilter" class="form-select" onchange="performSearch()">
                                        <option value="">All Units</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="idCardFilter" class="form-select" onchange="performSearch()">
                                        <option value="">All ID Card Status</option>
                                        <option value="printed">ID Card Printed</option>
                                        <option value="not_printed">Not Yet Printed</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="noPhotoFilter" class="form-select" onchange="performSearch()">
                                        <option value="">All Photo Status</option>
                                        <option value="1">No Photo Uploaded</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination Container -->
                    <nav aria-label="Page navigation" class="mb-3" id="paginationTop">
                    </nav>

                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                                            <th>Name</th>
                                            <th>Staff ID</th>
                                            <th>Email</th>
                                            <th>Faculty/Unit</th>
                                            <th>Status</th>
                                            <th>ID Card</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="usersTableBody">
                                        <?php if (empty($users)): ?>
                                            <tr><td colspan="7" class="text-center p-4 text-muted">No users found</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($users as $user): ?>
                                                <tr class="user-row">
                                                    <td>
                                                        <?php if (($user['role'] ?? 'user') !== 'admin'): ?>
                                                            <input type="checkbox" class="user-checkbox" value="<?= $user['id'] ?>" onchange="updateBulkButtons()">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></td>
                                                    <td class="fw-bold text-primary"><?= htmlspecialchars($user['staff_number'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                                    <td>
                                                        <?php 
                                                        if (!empty($user['unit'])) {
                                                            echo '<span class="badge bg-info">' . htmlspecialchars($user['unit']) . '</span>';
                                                        } elseif (!empty($user['faculty'])) {
                                                            echo htmlspecialchars($user['faculty']);
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><span class="badge bg-<?= $user['account_status'] === 'active' ? 'success' : ($user['account_status'] === 'pending' ? 'warning' : 'danger') ?>"><?= ucfirst($user['account_status']) ?></span></td>
                                                    <td>
                                                        <?php if (!empty($user['id_card_generated'])): ?>
                                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Printed</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Not Printed</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-outline-primary" onclick="generateIDCard(<?= $user['id'] ?>)" title="Generate ID"><i class="fas fa-id-card"></i></button>
                                                            <button class="btn btn-outline-success" onclick="activateUser(<?= $user['id'] ?>)" title="Activate"><i class="fas fa-check"></i></button>
                                                            <button class="btn btn-outline-danger" onclick="deleteUser(<?= $user['id'] ?>)" title="Delete"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Pagination -->
                    <nav aria-label="Page navigation" class="mt-3" id="paginationBottom">
                    </nav>
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
                    <div id="previewContainer" class="d-flex flex-wrap justify-content-center gap-4"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="downloadAllBtn" onclick="downloadAllPDFs()"><i class="fas fa-download me-2"></i>Download All</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = '<?= $_SESSION['csrf_token'] ?? '' ?>';
        let searchTimeout = null;
        let currentPage = 1;

        // ── Sidebar ──────────────────────────────────────────────────────────
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const content = document.getElementById('mainContent');
            const icon    = document.getElementById('toggleIcon');
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('expanded');
            icon.classList.toggle('fa-chevron-left');
            icon.classList.toggle('fa-chevron-right');
        }

        // ── Search / Filters ─────────────────────────────────────────────────
        document.getElementById('userSearch').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => { currentPage = 1; performSearch(); }, 400);
        });

        // All filter selects auto-trigger search on change (already have onchange in HTML)
        function performSearch(page = 1) {
            currentPage = page;
            const params = new URLSearchParams({
                query:          document.getElementById('userSearch').value,
                staff_type:     document.getElementById('staffTypeFilter').value,
                gender:         document.getElementById('genderFilter').value,
                faculty:        document.getElementById('facultyFilter').value,
                unit:           document.getElementById('unitFilter').value,
                id_card_filter: document.getElementById('idCardFilter').value,
                no_photo:       document.getElementById('noPhotoFilter').value,
                page
            });

            fetch('<?= url('/admin/users/search') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-Token': csrfToken },
                body: params
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    updateUserTable(data.users);
                    updatePagination(data.pagination);
                    document.getElementById('visibleCount').textContent = data.users.length;
                    document.getElementById('totalCount').textContent   = data.pagination.total_users;
                }
            })
            .catch(err => console.error('Search error:', err));
        }

        function updateUserTable(users) {
            const tbody = document.getElementById('usersTableBody');
            if (!users.length) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center p-4 text-muted">No users found</td></tr>';
                return;
            }
            tbody.innerHTML = users.map(user => {
                const fullName    = escapeHtml(`${user.first_name || ''} ${user.last_name || ''}`.trim());
                const staffNumber = escapeHtml(user.staff_number || '-');
                const email       = escapeHtml(user.email || '');
                const facultyUnit = user.unit
                    ? `<span class="badge bg-info">${escapeHtml(user.unit)}</span>`
                    : (user.faculty ? escapeHtml(user.faculty) : '-');
                const statusClass = user.account_status === 'active' ? 'success' : (user.account_status === 'pending' ? 'warning' : 'danger');
                const statusText  = (user.account_status || 'unknown').charAt(0).toUpperCase() + (user.account_status || '').slice(1);
                const idBadge     = parseInt(user.id_card_generated) === 1
                    ? `<span class="badge bg-success"><i class="fas fa-check me-1"></i>Printed</span>`
                    : `<span class="badge bg-secondary">Not Printed</span>`;
                const checkbox    = user.role !== 'admin'
                    ? `<input type="checkbox" class="user-checkbox" value="${user.id}" onchange="updateBulkButtons()">`
                    : '';
                return `
                <tr class="user-row">
                    <td>${checkbox}</td>
                    <td>${fullName}</td>
                    <td class="fw-bold text-primary">${staffNumber}</td>
                    <td>${email}</td>
                    <td>${facultyUnit}</td>
                    <td><span class="badge bg-${statusClass}">${statusText}</span></td>
                    <td>${idBadge}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="generateIDCard(${user.id})" title="ID Card"><i class="fas fa-id-card"></i></button>
                            <button class="btn btn-outline-success" onclick="activateUser(${user.id})" title="Activate"><i class="fas fa-check"></i></button>
                            <button class="btn btn-outline-danger"  onclick="deleteUser(${user.id})"   title="Delete"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>`;
            }).join('');
            updateBulkButtons();
        }

        function updatePagination(pagination) {
            const { current_page, total_pages } = pagination;
            const html = total_pages > 1 ? generatePaginationHTML(current_page, total_pages) : '';
            document.getElementById('paginationTop').innerHTML    = html;
            document.getElementById('paginationBottom').innerHTML = html;
        }

        function generatePaginationHTML(cur, total) {
            const prev = cur <= 1 ? 'disabled' : '';
            const next = cur >= total ? 'disabled' : '';
            let pages = '';
            const start = Math.max(1, cur - 2), end = Math.min(total, cur + 2);
            if (start > 1) { pages += `<li class="page-item"><a class="page-link" href="#" onclick="performSearch(1);return false;">1</a></li>`; if (start > 2) pages += '<li class="page-item disabled"><span class="page-link">…</span></li>'; }
            for (let i = start; i <= end; i++) pages += `<li class="page-item ${i===cur?'active':''}"><a class="page-link" href="#" onclick="performSearch(${i});return false;">${i}</a></li>`;
            if (end < total) { if (end < total-1) pages += '<li class="page-item disabled"><span class="page-link">…</span></li>'; pages += `<li class="page-item"><a class="page-link" href="#" onclick="performSearch(${total});return false;">${total}</a></li>`; }
            return `<ul class="pagination justify-content-center mb-0">
                <li class="page-item ${prev}"><a class="page-link" href="#" onclick="performSearch(${cur-1});return false;">Prev</a></li>
                ${pages}
                <li class="page-item ${next}"><a class="page-link" href="#" onclick="performSearch(${cur+1});return false;">Next</a></li>
            </ul>`;
        }

        function escapeHtml(text) {
            const d = document.createElement('div');
            d.textContent = String(text ?? '');
            return d.innerHTML;
        }

        function exportToExcel() {
            const params = new URLSearchParams({
                staff_type: document.getElementById('staffTypeFilter').value,
                gender:     document.getElementById('genderFilter').value,
                faculty:    document.getElementById('facultyFilter').value,
                unit:       document.getElementById('unitFilter').value
            });
            window.location.href = '<?= url('/admin/users/export') ?>?' + params;
        }

        // Load filter dropdowns from API
        function loadFilterOptions() {
            fetch('<?= url('/faculties-departments') ?>')
                .then(r => r.json())
                .then(data => {
                    const sel = document.getElementById('facultyFilter');
                    Object.keys(data.data || data).forEach(f => {
                        const o = document.createElement('option');
                        o.value = o.textContent = f;
                        sel.appendChild(o);
                    });
                })
                .catch(() => {});
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadFilterOptions();
            updatePagination({ current_page: <?= $current_page ?? 1 ?>, total_pages: <?= $total_pages ?? 1 ?> });
        });

        // ── Checkboxes ───────────────────────────────────────────────────────
        function toggleSelectAll() {
            const checked = document.getElementById('selectAll').checked;
            document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = checked);
            updateBulkButtons();
        }

        function updateBulkButtons() {
            const count = document.querySelectorAll('.user-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = count;
            ['bulkIDCardBtn','bulkVerifyBtn','bulkActivateBtn','bulkSuspendBtn','bulkDeleteBtn','bulkPhotoReminderBtn']
                .forEach(id => { const b = document.getElementById(id); if (b) b.disabled = count === 0; });
        }

        function getSelectedUserIds() {
            return Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
        }

        // ── Core action helper ───────────────────────────────────────────────
        async function performAction(url, payload, confirmMsg) {
            if (confirmMsg && !confirm(confirmMsg)) return;
            try {
                const r    = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify(payload) });
                const data = await r.json();
                if (data.success) { alert(data.message || 'Success'); location.reload(); }
                else              { alert('Error: ' + (data.error || 'Action failed')); }
            } catch (e) { alert('Network request failed'); }
        }

        // ── Bulk actions ─────────────────────────────────────────────────────
        function bulkDelete()    { const ids = getSelectedUserIds(); if (ids.length) performAction('<?= url('admin/bulk-delete-users') ?>', { user_ids: ids }, 'Delete selected users? Cannot be undone.'); }
        function bulkVerify()    { const ids = getSelectedUserIds(); if (ids.length) performAction('<?= url('admin/bulk-verify-users') ?>',  { user_ids: ids }, 'Verify selected users?'); }
        function bulkActivate()  { const ids = getSelectedUserIds(); if (ids.length) performAction('<?= url('admin/bulk-activate-users') ?>', { user_ids: ids }, 'Activate selected users?'); }
        function bulkSuspend()   {
            const ids = getSelectedUserIds();
            if (!ids.length) return;
            const reason = prompt('Enter suspension reason:', 'Policy Violation');
            if (reason) performAction('<?= url('admin/bulk-suspend-users') ?>', { user_ids: ids, reason }, null);
        }
        function bulkSendPhotoReminder() {
            const ids = getSelectedUserIds();
            if (!ids.length) return;
            if (!confirm(`Send photo reminder to ${ids.length} user(s)?`)) return;
            fetch('<?= url('admin/users/send-photo-reminder') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ user_ids: ids })
            }).then(r => r.json()).then(d => alert(d.success ? d.message : 'Error: ' + d.error)).catch(() => alert('Network error.'));
        }

        // ── Single actions ───────────────────────────────────────────────────
        function deleteUser(id)    { performAction('<?= url('admin/delete-user') ?>',   { user_id: id }, 'Delete this user?'); }
        function activateUser(id)  { performAction('<?= url('admin/activate-user') ?>', { user_id: id }, 'Activate this user?'); }
        function generateIDCard(id){ window.open('<?= url('admin/id-cards/preview') ?>/' + id, '_blank'); }

        // ── Bulk ID card generation ──────────────────────────────────────────
        async function bulkGenerateIDCards() {
            const userIds = getSelectedUserIds();
            const btn = document.getElementById('bulkIDCardBtn');
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;
            try {
                const r    = await fetch('<?= url('admin/id-cards/bulk-generate') ?>', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ user_ids: userIds }) });
                const data = await r.json();
                if (data.success) {
                    window.generatedProfiles = data.profiles;
                    document.getElementById('previewContainer').innerHTML = data.profiles.map(buildCardHtml).join('');
                    new bootstrap.Modal(document.getElementById('previewModal')).show();
                } else { alert('Error: ' + data.error); }
            } catch (e) { alert('Request failed'); }
            finally { btn.innerHTML = orig; btn.disabled = false; }
        }

        function buildCardHtml(profile) {
            const staffId  = profile.staff_number || ('TSU-' + String(profile.id).padStart(5,'0'));
            const fullName = [profile.title, profile.first_name, profile.last_name].filter(Boolean).join(' ');
            const photoUrl = profile.profile_photo_url || '';
            const qrUrl    = profile.qr_code_url || '';
            const logoUrl  = '<?= asset('assets/images/tsu-logo.png') ?>';
            const bgUrl    = '<?= asset('assets/images/tsu-building.jpg') ?>';
            const dirLabel = (profile.directorate || '').replace(/^(Directorate of |Faculty of )/i, '');
            const isNT     = profile.staff_type === 'non-teaching';
            const detailRows = isNT
                ? `${dirLabel ? `<tr><td style="font-weight:700;color:#1e40af;width:55px;vertical-align:top;padding-bottom:5px;white-space:nowrap;">Directorate:</td><td style="color:#111;font-weight:600;vertical-align:top;">${escapeHtml(dirLabel)}</td></tr>` : ''}
                   ${profile.unit ? `<tr><td style="font-weight:700;color:#1e40af;width:55px;vertical-align:top;padding-bottom:5px;white-space:nowrap;">Unit:</td><td style="color:#111;font-weight:600;vertical-align:top;">${escapeHtml(profile.unit)}</td></tr>` : ''}`
                : `${profile.faculty ? `<tr><td style="font-weight:700;color:#1e40af;width:55px;vertical-align:top;padding-bottom:5px;white-space:nowrap;">Faculty:</td><td style="color:#111;font-weight:600;vertical-align:top;">${escapeHtml(profile.faculty)}</td></tr>` : ''}
                   ${profile.department ? `<tr><td style="font-weight:700;color:#1e40af;width:55px;vertical-align:top;padding-bottom:5px;white-space:nowrap;">Dept:</td><td style="color:#111;font-weight:600;vertical-align:top;">${escapeHtml(profile.department)}</td></tr>` : ''}`;
            return `
            <div class="id-card-wrapper" style="margin:10px;">
                <div class="id-card" style="width:350px;height:550px;background:#fff;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,.1);overflow:hidden;position:relative;margin-bottom:10px;">
                    <div style="height:100%;position:relative;background:#f8f9fa;">
                        <div style="position:absolute;top:0;left:0;width:100%;height:100%;background-image:url('${bgUrl}');background-size:cover;background-position:center;opacity:.15;z-index:0;"></div>
                        <div style="position:absolute;left:20px;bottom:40px;height:180px;width:40px;background:#1e40af;color:white;display:flex;align-items:center;justify-content:center;border-radius:8px 8px 0 0;z-index:3;">
                            <div style="transform:rotate(-90deg);white-space:nowrap;font-weight:800;letter-spacing:2px;text-transform:uppercase;font-size:13px;">STAFF ID CARD</div>
                        </div>
                        <div style="text-align:center;padding-top:20px;position:relative;z-index:2;">
                            <img src="${logoUrl}" style="width:65px;height:65px;margin-bottom:3px;">
                            <h2 style="color:#1e40af;font-weight:800;font-size:15px;text-transform:uppercase;margin:0;line-height:1.1;">TARABA STATE UNIVERSITY</h2>
                            <div style="display:inline-block;color:#1e40af;font-weight:600;font-size:12px;text-transform:uppercase;border-top:1px solid #1e40af;border-bottom:1px solid #1e40af;padding:1px 8px;margin-top:2px;">JALINGO</div>
                        </div>
                        <div style="text-align:center;margin-top:15px;position:relative;z-index:2;height:170px;display:flex;justify-content:center;align-items:center;">
                            ${photoUrl ? `<img src="${photoUrl}" style="width:140px;height:165px;object-fit:cover;border-radius:8px;border:3px solid #1e40af;" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">` : ''}
                            <div style="width:140px;height:165px;background:#e2e8f0;color:#64748b;display:${photoUrl?'none':'flex'};align-items:center;justify-content:center;font-size:50px;border-radius:8px;border:3px solid #1e40af;">${(profile.first_name||'U').charAt(0).toUpperCase()}</div>
                        </div>
                        <div style="text-align:center;margin-top:10px;position:relative;z-index:2;padding:0 10px;">
                            <h3 style="color:#1e3a8a;font-weight:800;font-size:19px;margin:0;line-height:1.1;">${escapeHtml(fullName)}</h3>
                            <div style="color:#4b5563;font-size:13px;font-weight:600;margin-top:3px;">${escapeHtml(profile.designation||'')}</div>
                        </div>
                        <div style="margin-top:15px;margin-left:70px;margin-right:15px;position:relative;z-index:2;font-size:12px;">
                            <table style="width:100%;border-collapse:collapse;">
                                <tr><td style="font-weight:700;color:#1e40af;width:55px;vertical-align:top;padding-bottom:5px;white-space:nowrap;">Staff ID:</td><td style="color:#111;font-weight:600;vertical-align:top;">${escapeHtml(staffId)}</td></tr>
                                ${detailRows}
                            </table>
                        </div>
                        <div style="position:absolute;bottom:0;left:0;right:0;height:40px;background:#1e40af;color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:500;z-index:2;">Issued: <?= date('F Y') ?></div>
                    </div>
                </div>
                <div class="id-card" style="width:350px;height:550px;background:#fff;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,.1);overflow:hidden;position:relative;">
                    <div style="height:100%;position:relative;background:#fff;display:flex;flex-direction:column;">
                        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-30deg);font-size:120px;font-weight:900;color:rgba(30,64,175,.05);z-index:0;pointer-events:none;">TSU</div>
                        <div style="position:relative;z-index:2;flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:20px;text-align:center;">
                            <div style="margin-bottom:20px;">
                                <div style="font-size:14px;color:#1e40af;font-weight:800;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px;">SCAN THIS TO VERIFY</div>
                                ${qrUrl ? `<img src="${qrUrl}" style="width:220px;height:220px;border:4px solid #1e3a8a;border-radius:12px;padding:5px;background:white;">` : ''}
                            </div>
                            ${profile.blood_group ? `<div style="border:3px solid #dc2626;border-radius:10px;padding:8px 30px;margin-bottom:20px;background:rgba(255,255,255,.95);min-width:140px;"><div style="font-size:12px;text-transform:uppercase;color:#dc2626;font-weight:800;letter-spacing:1px;">Blood Group</div><div style="font-size:32px;font-weight:900;color:#333;line-height:1.1;">${escapeHtml(profile.blood_group)}</div></div>` : ''}
                            <div style="font-size:11px;color:#4b5563;line-height:1.4;margin-top:auto;margin-bottom:5px;"><p style="margin:0;">If found, please return to:</p><strong style="color:#1e40af;display:block;font-size:13px;margin:2px 0;">SECURITY UNIT</strong><p style="margin:0;">Taraba State University<br>Jalingo, Nigeria</p></div>
                        </div>
                        <div style="height:40px;background:#1e40af;color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:500;z-index:2;">Property of Taraba State University</div>
                    </div>
                </div>
            </div>`;
        }

        async function downloadAllPDFs() {
            const { jsPDF } = window.jspdf;
            const zip = new JSZip();
            const btn = document.getElementById('downloadAllBtn');
            btn.disabled = true; btn.innerHTML = 'Processing...';
            const wrappers = document.querySelectorAll('.id-card-wrapper');
            for (let i = 0; i < wrappers.length; i++) {
                const cards = wrappers[i].querySelectorAll('.id-card');
                const pdf   = new jsPDF({ orientation: 'portrait', unit: 'mm', format: [54, 85.6] });
                pdf.addImage((await html2canvas(cards[0], { scale: 2, useCORS: true })).toDataURL('image/jpeg'), 'JPEG', 0, 0, 54, 85.6);
                pdf.addPage();
                pdf.addImage((await html2canvas(cards[1], { scale: 2, useCORS: true })).toDataURL('image/jpeg'), 'JPEG', 0, 0, 54, 85.6);
                const p = window.generatedProfiles[i];
                zip.file(`ID_${p.staff_number || p.id}.pdf`, pdf.output('blob'));
            }
            saveAs(await zip.generateAsync({ type: 'blob' }), 'TSU_IDs.zip');
            btn.disabled = false; btn.innerHTML = '<i class="fas fa-download me-2"></i>Download All';
        }
    </script>
</body>
</html>

        // --- ID CARD GENERATION (JS) ---

        async function bulkGenerateIDCards() {
            const userIds = getSelectedUserIds();
            const btn = document.getElementById('bulkIDCardBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            try {
                const response = await fetch('<?= url('admin/id-cards/bulk-generate') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ user_ids: userIds })
                });
                const data = await response.json();
                
                if (data.success) {
                    window.generatedProfiles = data.profiles;
                    const container = document.getElementById('previewContainer');
                    container.innerHTML = '';
                    data.profiles.forEach(profile => {
                        container.innerHTML += buildCardHtml(profile);
                    });
                    new bootstrap.Modal(document.getElementById('previewModal')).show();
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (e) {
                alert('Request failed');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        function buildCardHtml(profile) {
            const staffId = profile.staff_number || ('TSU-' + String(profile.id).padStart(5, '0'));
            const fullName = [profile.title, profile.first_name, profile.last_name].filter(Boolean).join(' ');
            const bloodGroup = profile.blood_group || '';
            const logoUrl = '<?= asset('assets/images/tsu-logo.png') ?>';
            const bgUrl = '<?= asset('assets/images/tsu-building.jpg') ?>';
            
            // Use the profile_photo_url that was constructed server-side
            const photoUrl = profile.profile_photo_url || '';

            const qrUrl = profile.qr_code_url || '';

            return `
            <div class="id-card-wrapper" style="margin: 10px;">
                <div class="id-card" style="width: 350px; height: 550px; background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden; position: relative; margin-bottom: 10px;">
                    <div style="height: 100%; position: relative; background: #f8f9fa;">
                        <div style="position: absolute; top:0; left:0; width:100%; height:100%; background-image: url('${bgUrl}'); background-size: cover; background-position: center; opacity: 0.15; z-index: 0;"></div>
                        
                        <div style="position: absolute; left: 20px; bottom: 40px; height: 180px; width: 40px; background: #1e40af; color: white; display: flex; align-items: center; justify-content: center; border-radius: 8px 8px 0 0; z-index: 3; box-shadow: 2px -2px 5px rgba(0,0,0,0.1);">
                            <div style="transform: rotate(-90deg); white-space: nowrap; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; font-size: 13px;">STAFF ID CARD</div>
                        </div>

                        <div style="text-align: center; padding-top: 20px; position: relative; z-index: 2;">
                            <img src="${logoUrl}" style="width: 65px; height: 65px; margin-bottom: 3px;">
                            <h2 style="color: #1e40af; font-weight: 800; font-size: 15px; text-transform: uppercase; margin: 0; line-height: 1.1;">TARABA STATE UNIVERSITY</h2>
                            <div style="display: inline-block; color: #1e40af; font-weight: 600; font-size: 12px; text-transform: uppercase; border-top: 1px solid #1e40af; border-bottom: 1px solid #1e40af; padding: 1px 8px; margin-top: 2px;">JALINGO</div>
                        </div>

                        <div style="text-align: center; margin-top: 15px; position: relative; z-index: 2; height: 170px; display: flex; justify-content: center; align-items: center;">
                            ${photoUrl ? 
                                `<img src="${photoUrl}" style="width: 140px; height: 165px; object-fit: cover; border-radius: 8px; border: 3px solid #1e40af; box-shadow: 0 3px 6px rgba(0,0,0,0.15);" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">` : ''
                            }
                            <div style="width: 140px; height: 165px; background: #e2e8f0; color: #64748b; display: ${photoUrl ? 'none' : 'flex'}; align-items: center; justify-content: center; font-size: 50px; border-radius: 8px; border: 3px solid #1e40af;">${(profile.first_name || 'U').charAt(0).toUpperCase()}</div>
                        </div>

                        <div style="text-align: center; margin-top: 10px; position: relative; z-index: 2; padding: 0 10px;">
                            <h3 style="color: #1e3a8a; font-weight: 800; font-size: 19px; margin: 0; line-height: 1.1;">${fullName}</h3>
                            <div style="color: #4b5563; font-size: 13px; font-weight: 600; margin-top: 3px;">${profile.designation || ''}</div>
                        </div>

                        <div style="margin-top: 15px; margin-left: 70px; margin-right: 15px; position: relative; z-index: 2; font-size: 12px;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr><td style="font-weight: 700; color: #1e40af; width: 55px; vertical-align: top; padding-bottom: 5px;">Staff ID:</td><td style="color: #111; font-weight: 600; vertical-align: top;">${staffId}</td></tr>
                                ${profile.unit && !profile.faculty ? 
                                    `<tr><td style="font-weight: 700; color: #1e40af; width: 55px; vertical-align: top; padding-bottom: 5px;">Unit:</td><td style="color: #111; font-weight: 600; vertical-align: top;">${profile.unit}</td></tr>` :
                                    `<tr><td style="font-weight: 700; color: #1e40af; width: 55px; vertical-align: top; padding-bottom: 5px;">Faculty:</td><td style="color: #111; font-weight: 600; vertical-align: top;">${profile.faculty || ''}</td></tr>
                                     <tr><td style="font-weight: 700; color: #1e40af; width: 55px; vertical-align: top; padding-bottom: 5px;">Dept:</td><td style="color: #111; font-weight: 600; vertical-align: top;">${profile.department || ''}</td></tr>`
                                }
                            </table>
                        </div>

                        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 40px; background: #1e40af; color: white; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 500; z-index: 2;">
                            Issued: <?= date('F Y') ?>
                        </div>
                    </div>
                </div>

                <div class="id-card" style="width: 350px; height: 550px; background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden; position: relative;">
                    <div style="height: 100%; position: relative; background: #fff; display: flex; flex-direction: column;">
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 120px; font-weight: 900; color: rgba(30, 64, 175, 0.05); z-index: 0; pointer-events: none;">TSU</div>
                        
                        <div style="position: relative; z-index: 2; flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; text-align: center;">
                            <div style="margin-bottom: 20px;">
                                <div style="font-size: 14px; color: #1e40af; font-weight: 800; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">SCAN THIS TO VERIFY</div>
                                ${qrUrl ? `<img src="${qrUrl}" style="width: 220px; height: 220px; border: 4px solid #1e3a8a; border-radius: 12px; padding: 5px; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">` : ''}
                            </div>

                            ${bloodGroup ? `
                            <div style="border: 3px solid #dc2626; border-radius: 10px; padding: 8px 30px; margin-bottom: 20px; background: rgba(255, 255, 255, 0.95); min-width: 140px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                <div style="font-size: 12px; text-transform: uppercase; color: #dc2626; font-weight: 800; letter-spacing: 1px;">Blood Group</div>
                                <div style="font-size: 32px; font-weight: 900; color: #333; line-height: 1.1;">${bloodGroup}</div>
                            </div>` : ''}

                            <div style="font-size: 11px; color: #4b5563; line-height: 1.4; margin-top: auto; margin-bottom: 5px;">
                                <p style="margin:0;">If found, please return to:</p>
                                <strong style="color: #1e40af; display: block; font-size: 13px; margin: 2px 0;">SECURITY UNIT</strong>
                                <p style="margin:0;">Taraba State University<br>Jalingo, Nigeria</p>
                            </div>
                        </div>

                        <div style="height: 40px; background: #1e40af; color: white; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 500; z-index: 2;">
                            Property of Taraba State University
                        </div>
                    </div>
                </div>
            </div>`;
        }

        async function downloadAllPDFs() {
            const { jsPDF } = window.jspdf;
            const zip = new JSZip();
            const btn = document.getElementById('downloadAllBtn');
            btn.disabled = true;
            btn.innerHTML = 'Processing...';

            const wrappers = document.querySelectorAll('.id-card-wrapper');
            
            for (let i = 0; i < wrappers.length; i++) {
                const wrapper = wrappers[i];
                const cards = wrapper.querySelectorAll('.id-card'); // 0=Front, 1=Back
                const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: [54, 85.6] });

                const frontCanvas = await html2canvas(cards[0], { scale: 2, useCORS: true });
                pdf.addImage(frontCanvas.toDataURL('image/jpeg'), 'JPEG', 0, 0, 54, 85.6);
                
                pdf.addPage();
                const backCanvas = await html2canvas(cards[1], { scale: 2, useCORS: true });
                pdf.addImage(backCanvas.toDataURL('image/jpeg'), 'JPEG', 0, 0, 54, 85.6);

                const profile = window.generatedProfiles[i];
                zip.file(`ID_${profile.staff_number || profile.id}.pdf`, pdf.output('blob'));
            }

            const content = await zip.generateAsync({type:"blob"});
            saveAs(content, "TSU_IDs.zip");
            btn.disabled = false;
            btn.innerHTML = 'Download All';
        }
    </script>
</body>
</html>
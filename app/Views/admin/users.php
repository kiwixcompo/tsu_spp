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
        .admin-sidebar { background: #2c3e50; min-height: 100vh; padding-top: 2rem; position: fixed; left: 0; top: 0; width: 250px; transition: all 0.3s ease; z-index: 1000; overflow-y: auto; }
        .admin-sidebar.collapsed { width: 70px; }
        .admin-sidebar.collapsed .sidebar-text { display: none; }
        .admin-sidebar.collapsed .text-center h4 { font-size: 0; }
        .admin-sidebar.collapsed .text-center h4 i { font-size: 1.5rem; }
        .admin-sidebar .nav-link { color: #ecf0f1; padding: 1rem 1.5rem; border-radius: 0; margin-bottom: 0.5rem; white-space: nowrap; transition: all 0.3s ease; }
        .admin-sidebar.collapsed .nav-link { padding: 1rem 0.5rem; text-align: center; }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active { background: #34495e; color: white; }
        .sidebar-toggle { position: absolute; top: 10px; right: -15px; background: #2c3e50; color: white; border: 2px solid #34495e; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 1001; transition: all 0.3s ease; }
        .sidebar-toggle:hover { background: #34495e; }
        .main-content { margin-left: 250px; transition: all 0.3s ease; width: calc(100% - 250px); }
        .main-content.expanded { margin-left: 70px; width: calc(100% - 70px); }
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .preview-modal-dialog { max-width: 900px; }
        @media (max-width: 768px) { .admin-sidebar { width: 70px; } .admin-sidebar .sidebar-text { display: none; } .main-content { margin-left: 70px; width: calc(100% - 70px); } }
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
                                <span class="badge bg-secondary align-self-center ms-2 fs-6"><span id="selectedCount">0</span> selected</span>
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-md-4"><input type="text" id="userSearch" class="form-control" placeholder="Search by Name, Staff ID, or Email..."></div>
                            </div>
                        </div>
                    </div>

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
                                            <th>Faculty</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($users)): ?>
                                            <tr><td colspan="7" class="text-center p-4 text-muted">No users found</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($users as $user): ?>
                                                <tr class="user-row" data-name="<?= strtolower(($user['first_name']??'').' '.($user['last_name']??'').' '.($user['email']??'').' '.($user['staff_number']??'')) ?>">
                                                    <td>
                                                        <?php if (($user['role'] ?? 'user') !== 'admin'): ?>
                                                            <input type="checkbox" class="user-checkbox" value="<?= $user['id'] ?>" onchange="updateBulkButtons()">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></td>
                                                    <td class="fw-bold text-primary"><?= htmlspecialchars($user['staff_number'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                                    <td><?= htmlspecialchars($user['faculty'] ?? '-') ?></td>
                                                    <td><span class="badge bg-<?= $user['account_status'] === 'active' ? 'success' : ($user['account_status'] === 'pending' ? 'warning' : 'danger') ?>"><?= ucfirst($user['account_status']) ?></span></td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    
    <script>
        const csrfToken = '<?= $_SESSION['csrf_token'] ?? '' ?>';

        // Sidebar Toggle
        function toggleSidebar() {
            document.getElementById('adminSidebar').classList.toggle('collapsed');
            document.getElementById('mainContent').classList.toggle('expanded');
        }

        // Search Filter
        document.getElementById('userSearch').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('.user-row');
            rows.forEach(row => {
                let text = row.getAttribute('data-name');
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });

        // Checkbox Logic
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkButtons();
        }

        function updateBulkButtons() {
            const count = document.querySelectorAll('.user-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = count;
            
            const ids = ['bulkIDCardBtn', 'bulkVerifyBtn', 'bulkActivateBtn', 'bulkSuspendBtn', 'bulkDeleteBtn'];
            ids.forEach(id => {
                const btn = document.getElementById(id);
                if(btn) btn.disabled = count === 0;
            });
        }

        function getSelectedUserIds() {
            return Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
        }

        // --- CORE ACTION FUNCTIONS ---

        async function performAction(url, payload, confirmMsg) {
            if (confirmMsg && !confirm(confirmMsg)) return;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message || 'Success');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Action failed'));
                }
            } catch (e) {
                console.error(e);
                alert('Network request failed');
            }
        }

        // --- BULK ACTIONS ---

        function bulkDelete() {
            const ids = getSelectedUserIds();
            if(ids.length) performAction('<?= url('admin/bulk-delete-users') ?>', { user_ids: ids }, 'Delete selected users? Cannot be undone.');
        }

        function bulkVerify() {
            const ids = getSelectedUserIds();
            if(ids.length) performAction('<?= url('admin/bulk-verify-users') ?>', { user_ids: ids }, 'Verify selected users?');
        }

        function bulkActivate() {
            const ids = getSelectedUserIds();
            if(ids.length) performAction('<?= url('admin/bulk-activate-users') ?>', { user_ids: ids }, 'Activate selected users?');
        }

        function bulkSuspend() {
            const ids = getSelectedUserIds();
            if(!ids.length) return;
            const reason = prompt("Enter suspension reason:", "Policy Violation");
            if (reason) performAction('<?= url('admin/bulk-suspend-users') ?>', { user_ids: ids, reason: reason }, null);
        }

        // --- SINGLE ACTIONS ---
        
        function deleteUser(userId) {
            performAction('<?= url('admin/delete-user') ?>', { user_id: userId }, 'Delete this user?');
        }

        function activateUser(userId) {
            performAction('<?= url('admin/activate-user') ?>', { user_id: userId }, 'Activate this user?');
        }

        function generateIDCard(userId) {
            window.open('<?= url('admin/id-cards/preview') ?>/' + userId, '_blank');
        }

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
            const fullName = [profile.title, profile.first_name, profile.last_name].join(' ');
            const bloodGroup = profile.blood_group || '';
            const logoUrl = '<?= asset('assets/images/tsu-logo.png') ?>';
            const bgUrl = '<?= asset('assets/images/tsu-building.jpg') ?>';
            
            let photoUrl = '';
            if (profile.profile_photo) {
                photoUrl = (profile.profile_photo.startsWith('http')) 
                    ? profile.profile_photo 
                    : '<?= url('uploads/profiles/') ?>' + profile.profile_photo;
            }

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
                            <div style="width: 140px; height: 165px; background: #e2e8f0; color: #64748b; display: ${photoUrl ? 'none' : 'flex'}; align-items: center; justify-content: center; font-size: 50px; border-radius: 8px; border: 3px solid #1e40af;">${profile.first_name.charAt(0)}</div>
                        </div>

                        <div style="text-align: center; margin-top: 10px; position: relative; z-index: 2; padding: 0 10px;">
                            <h3 style="color: #1e3a8a; font-weight: 800; font-size: 19px; margin: 0; line-height: 1.1;">${fullName}</h3>
                            <div style="color: #4b5563; font-size: 13px; font-weight: 600; margin-top: 3px;">${profile.designation || ''}</div>
                        </div>

                        <div style="margin-top: 15px; margin-left: 70px; margin-right: 15px; position: relative; z-index: 2; font-size: 12px;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr><td style="font-weight: 700; color: #1e40af; width: 55px; vertical-align: top; padding-bottom: 5px;">Staff ID:</td><td style="color: #111; font-weight: 600; vertical-align: top;">${staffId}</td></tr>
                                <tr><td style="font-weight: 700; color: #1e40af; width: 55px; vertical-align: top; padding-bottom: 5px;">Faculty:</td><td style="color: #111; font-weight: 600; vertical-align: top;">${profile.faculty || ''}</td></tr>
                                <tr><td style="font-weight: 700; color: #1e40af; width: 55px; vertical-align: top; padding-bottom: 5px;">Dept:</td><td style="color: #111; font-weight: 600; vertical-align: top;">${profile.department || ''}</td></tr>
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
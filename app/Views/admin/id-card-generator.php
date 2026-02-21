<?php
// Load helpers
if (!function_exists('url')) {
    require_once __DIR__ . '/../../Helpers/UrlHelper.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card Generator - TSU Staff Portal</title>
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
        .user-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .user-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .user-photo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .search-box {
            max-width: 500px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <div class="text-center mb-4">
                        <img src="<?= asset('assets/images/tsu-logo.png') ?>" alt="TSU Logo" style="width: 50px; height: 50px;" class="mb-2">
                        <h6 class="text-white mb-0">TSU Admin</h6>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link" href="<?= url('admin/dashboard') ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="<?= url('admin/users') ?>">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                        <a class="nav-link active" href="<?= url('admin/id-cards') ?>">
                            <i class="fas fa-id-card me-2"></i>ID Cards
                        </a>
                        <a class="nav-link" href="<?= url('admin/faculties-departments') ?>">
                            <i class="fas fa-building me-2"></i>Faculties
                        </a>
                        <a class="nav-link" href="<?= url('admin/settings') ?>">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                        <hr class="text-white-50">
                        <a class="nav-link" href="<?= url('dashboard') ?>">
                            <i class="fas fa-arrow-left me-2"></i>Back to Portal
                        </a>
                        <a class="nav-link" href="<?= url('logout') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>

            <div class="col-md-9 col-lg-10 main-content">
                <div class="bg-white border-bottom p-3 mb-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="mb-0">
                                <i class="fas fa-id-card me-2"></i>ID Card Generator
                            </h4>
                            <p class="text-muted mb-0">Generate staff ID cards with QR codes</p>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="search-box">
                                <input type="text" 
                                       id="searchInput" 
                                       class="form-control" 
                                       placeholder="🔍 Search by name, email, faculty, or department...">
                            </div>
                            <div class="mt-3 d-flex align-items-center gap-2 flex-wrap">
                                <button class="btn btn-outline-secondary btn-sm" id="selectAllBtn" type="button">Select All</button>
                                <button class="btn btn-outline-secondary btn-sm" id="clearSelectionBtn" type="button">Clear</button>
                                <button class="btn btn-primary btn-sm" id="bulkDownloadBtn" type="button">
                                    <i class="fas fa-download me-1"></i>Download Selected ID Cards
                                </button>
                                <span class="badge bg-info text-dark" id="selectionCount">0 selected</span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>How it works:</strong> Click on any staff member to generate their ID card. 
                        The ID card includes their photo, details, and a QR code that links to their profile.
                    </div>

                    <div class="row" id="usersGrid">
                        <?php if (empty($users)): ?>
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No users with profiles found.
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <div class="col-md-6 col-lg-4 mb-3 user-item" 
                                     data-name="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>"
                                     data-email="<?= htmlspecialchars($user['email']) ?>"
                                     data-faculty="<?= htmlspecialchars($user['faculty']) ?>"
                                     data-department="<?= htmlspecialchars($user['department']) ?>">
                                    <div class="card user-card h-100" onclick="generateIDCard(<?= $user['id'] ?>)">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="form-check me-3">
                                                    <input class="form-check-input select-user" type="checkbox" value="<?= $user['id'] ?>" onclick="event.stopPropagation(); handleSelect(this, <?= $user['id'] ?>);">
                                                </div>
                                                <div class="me-3">
                                                    <?php if (!empty($user['profile_photo'])): ?>
                                                        <?php
                                                        $photoPath = $user['profile_photo'];
                                                        if (strpos($photoPath, 'uploads/') === 0 || strpos($photoPath, '/uploads/') === 0) {
                                                            $photoUrl = url($photoPath);
                                                        } else {
                                                            $photoUrl = asset('uploads/profiles/' . $photoPath);
                                                        }
                                                        $initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
                                                        ?>
                                                        <img src="<?= $photoUrl ?>" 
                                                             alt="Photo" 
                                                             class="user-photo"
                                                             onerror="this.replaceWith(createPlaceholder('<?= $initials ?>'))">
                                                    <?php else: ?>
                                                        <div class="user-photo bg-primary text-white d-flex align-items-center justify-content-center">
                                                            <strong><?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?></strong>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        <?= htmlspecialchars($user['title'] . ' ' . $user['first_name'] . ' ' . $user['last_name']) ?>
                                                    </h6>
                                                    <p class="text-muted small mb-1"><?= htmlspecialchars($user['designation']) ?></p>
                                                    <p class="text-muted small mb-0">
                                                        <i class="fas fa-building me-1"></i><?= htmlspecialchars($user['department']) ?>
                                                    </p>
                                                </div>
                                                <div>
                                                    <?php if (!empty($user['qr_code_path'])): ?>
                                                        <i class="fas fa-qrcode text-success fa-2x"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-qrcode text-muted fa-2x"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        const csrfToken = '<?= $csrf_token ?>';
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const userItems = document.querySelectorAll('.user-item');
            
            userItems.forEach(item => {
                const name = item.dataset.name.toLowerCase();
                const email = item.dataset.email.toLowerCase();
                const faculty = item.dataset.faculty.toLowerCase();
                const department = item.dataset.department.toLowerCase();
                
                const matches = name.includes(searchTerm) || 
                               email.includes(searchTerm) || 
                               faculty.includes(searchTerm) || 
                               department.includes(searchTerm);
                
                item.style.display = matches ? '' : 'none';
            });
        });

        // Generate ID card
        function generateIDCard(userId) {
            // Redirect to ID card preview page
            window.location.href = '<?= url('admin/id-cards/preview') ?>/' + userId;
        }

        // Fallback placeholder for broken images
        function createPlaceholder(initials) {
            const div = document.createElement('div');
            div.className = 'user-photo bg-primary text-white d-flex align-items-center justify-content-center';
            div.innerHTML = '<strong>' + initials + '</strong>';
            return div;
        }

        // Selection handling
        const selectedIds = new Set();
        const selectionCountEl = document.getElementById('selectionCount');
        const selectAllBtn = document.getElementById('selectAllBtn');
        const clearSelectionBtn = document.getElementById('clearSelectionBtn');
        const bulkDownloadBtn = document.getElementById('bulkDownloadBtn');

        function updateSelectionCount() {
            selectionCountEl.textContent = `${selectedIds.size} selected`;
        }

        function handleSelect(checkbox, userId) {
            if (checkbox.checked) {
                selectedIds.add(userId);
            } else {
                selectedIds.delete(userId);
            }
            updateSelectionCount();
        }

        selectAllBtn.addEventListener('click', () => {
            document.querySelectorAll('.select-user').forEach(cb => {
                cb.checked = true;
                selectedIds.add(parseInt(cb.value, 10));
            });
            updateSelectionCount();
        });

        clearSelectionBtn.addEventListener('click', () => {
            document.querySelectorAll('.select-user').forEach(cb => cb.checked = false);
            selectedIds.clear();
            updateSelectionCount();
        });

        bulkDownloadBtn.addEventListener('click', async () => {
            if (selectedIds.size === 0) {
                alert('Select at least one profile to download ID cards.');
                return;
            }
            bulkDownloadBtn.disabled = true;
            bulkDownloadBtn.innerText = 'Preparing...';
            try {
                await downloadSelectedCards(Array.from(selectedIds));
            } catch (err) {
                console.error(err);
                alert('Failed to generate ID cards. Please try again.');
            } finally {
                bulkDownloadBtn.disabled = false;
                bulkDownloadBtn.innerText = 'Download Selected ID Cards';
            }
        });

        async function downloadSelectedCards(userIds) {
            const response = await fetch('<?= url('admin/id-cards/bulk-generate') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ user_ids: userIds })
            });

            const data = await response.json();
            if (!data.success || !Array.isArray(data.profiles)) {
                throw new Error(data.error || 'Bulk generation failed');
            }

            const { jsPDF } = window.jspdf;
            for (const profile of data.profiles) {
                await renderAndDownload(profile);
            }
        }

        function buildCardHtml(profile) {
            const staffId = profile.staff_number || ('TSU-' + String(profile.id).padStart(5, '0'));
            const nameParts = [profile.title, profile.first_name, profile.middle_name, profile.last_name].filter(Boolean);
            const fullName = nameParts.join(' ');
            const photoUrl = profile.profile_photo
                ? ((profile.profile_photo.startsWith('uploads/') || profile.profile_photo.startsWith('/uploads/'))
                    ? '<?= url('') ?>/' + profile.profile_photo.replace(/^\/?/, '')
                    : '<?= asset('uploads/profiles') ?>/' + profile.profile_photo)
                : '';
            const qrUrl = profile.qr_code_url || '';

            // Calculate dynamic font size based on name length
            const nameLength = fullName.length;
            let nameFontSize = '18px';
            if (nameLength > 30) {
                nameFontSize = '14px';
            } else if (nameLength > 25) {
                nameFontSize = '15px';
            } else if (nameLength > 20) {
                nameFontSize = '16px';
            }

            return `
                <div class="id-card" style="width:350px;height:550px;border-radius:15px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.2);">
                    <div class="id-card-front" style="height:100%;display:flex;flex-direction:column;">
                        <div style="background:linear-gradient(135deg,#1e40af 0%,#3b82f6 100%);color:#fff;padding:20px;text-align:center;">
                            <img src="<?= asset('assets/images/tsu-logo.png') ?>" alt="TSU Logo" style="width:60px;height:60px;margin-bottom:10px;">
                            <h5 style="font-size:16px;font-weight:bold;margin:0;">TARABA STATE UNIVERSITY</h5>
                            <p style="font-size:11px;margin:5px 0 0 0;opacity:.9;">STAFF IDENTIFICATION CARD</p>
                        </div>
                        <div style="text-align:center;padding:15px 20px;background:#f8f9fa;flex-shrink:0;">
                            ${photoUrl ? `<img src="${photoUrl}" onerror="this.style.display='none'" style="width:150px;height:150px;object-fit:cover;border-radius:10px;border:4px solid #fff;box-shadow:0 4px 8px rgba(0,0,0,0.2);">` : `<div style="width:150px;height:150px;background:#1e40af;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:48px;font-weight:bold;border-radius:10px;border:4px solid #fff;box-shadow:0 4px 8px rgba(0,0,0,0.2);">${(profile.first_name[0] || '')}${(profile.last_name[0] || '')}</div>`}
                        </div>
                        <div style="padding:15px 20px;flex-grow:1;display:flex;flex-direction:column;justify-content:space-between;">
                            <div style="text-align:center;">
                                <h4 style="font-size:${nameFontSize};font-weight:bold;color:#1e40af;margin-bottom:5px;line-height:1.3;word-wrap:break-word;word-break:break-word;hyphens:auto;">${fullName}</h4>
                                <div style="color:#666;font-size:12px;margin-bottom:12px;font-weight:500;line-height:1.4;word-wrap:break-word;">${profile.designation || ''}</div>
                            </div>
                            <div style="font-size:11px;line-height:1.6;">
                                <div style="display:flex;margin-bottom:6px;align-items:flex-start;"><div style="font-weight:bold;color:#666;width:85px;flex-shrink:0;">Staff ID:</div><div style="color:#333;flex-grow:1;word-wrap:break-word;line-height:1.4;">${staffId}</div></div>
                                ${(() => {
                                    const hasUnit = profile.unit && profile.unit.trim() !== '';
                                    const hasFaculty = profile.faculty && profile.faculty.trim() !== '';
                                    const hasDepartment = profile.department && profile.department.trim() !== '';
                                    
                                    // Show Unit if it exists and no faculty/department
                                    if (hasUnit && !hasFaculty && !hasDepartment) {
                                        return `
                                        <div style="text-align:center; margin-top: 6px; margin-bottom: 2px;">
                                            <span style="font-weight:bold;color:#666;">Unit:</span>
                                        </div>
                                        <div style="display:flex;justify-content:center;margin-bottom:8px;padding:2px 10px;">
                                            <div style="font-size:14px;font-weight:800;color:#1e3a8a;text-align:center;word-wrap:break-word;line-height:1.2;text-transform:uppercase;">${profile.unit}</div>
                                        </div>`;
                                    } else {
                                        // Show Faculty/Department only if they exist
                                        let output = '';
                                        if (hasFaculty) {
                                            output += `<div style="display:flex;margin-bottom:6px;align-items:flex-start;"><div style="font-weight:bold;color:#666;width:85px;flex-shrink:0;">Faculty:</div><div style="color:#333;flex-grow:1;word-wrap:break-word;line-height:1.4;">${profile.faculty}</div></div>`;
                                        }
                                        if (hasDepartment) {
                                            output += `<div style="display:flex;margin-bottom:6px;align-items:flex-start;"><div style="font-weight:bold;color:#666;width:85px;flex-shrink:0;">Department:</div><div style="color:#333;flex-grow:1;word-wrap:break-word;line-height:1.4;">${profile.department}</div></div>`;
                                        }
                                        return output;
                                    }
                                })()}
                            </div>
                        </div>
                        <div style="background:#1e40af;color:#fff;text-align:center;padding:10px;font-size:10px;">
                            Issued: <?= date('F Y') ?>
                        </div>
                    </div>
                </div>
                <div class="id-card" style="width:350px;height:550px;border-radius:15px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.2);">
                    <div class="id-card-back" style="height:100%;display:flex;flex-direction:column;background:linear-gradient(135deg,#f8f9fa 0%,#e9ecef 100%);">
                        <div style="background:#1e40af;color:#fff;padding:15px;text-align:center;">
                            <h6 style="margin:0;">SCAN FOR PROFILE</h6>
                        </div>
                        <div style="flex-grow:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;padding:25px 20px;overflow:hidden;">
                            <div style="text-align:center;margin-bottom:15px;flex-shrink:0;">
                                ${qrUrl ? `<img src="${qrUrl}" alt="QR Code" style="width:180px;height:180px;border:3px solid #1e40af;border-radius:10px;background:#fff;padding:8px;">` : '<div style="width:180px;height:180px;border:3px dashed #1e40af;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#666;">QR missing</div>'}
                                <p style="margin-top:12px;font-size:11px;color:#666;line-height:1.4;padding:0 10px;">Scan this QR code to view real-time profile</p>
                                <div style="font-size:10px;color:#1e40af;word-break:break-all;margin-top:8px;padding:0 10px;line-height:1.3;"><?= url('') ?>/profile/${profile.profile_slug}</div>
                            </div>
                            <div style="margin-top:auto;text-align:center;font-size:10px;color:#666;padding-top:15px;">
                                <p style="margin:3px 0;"><strong>Important:</strong></p>
                                <p style="margin:3px 0;">This card is property of TSU</p>
                                <p style="margin:3px 0;">If found, please return to:</p>
                                <p style="margin:3px 0;"><strong>Security Unit</strong></p>
                                <p style="margin:3px 0;">Taraba State University, Jalingo</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        async function renderAndDownload(profile) {
            const renderArea = document.getElementById('card-render-area');
            renderArea.innerHTML = buildCardHtml(profile);

            const cards = renderArea.querySelectorAll('.id-card');
            if (cards.length !== 2) {
                throw new Error('Card render failed');
            }

            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'in',
                format: [3.5, 5.5]
            });

            // Front
            const frontCanvas = await html2canvas(cards[0], { scale: 3, useCORS: true, allowTaint: true, backgroundColor: '#ffffff' });
            const frontImg = frontCanvas.toDataURL('image/png');
            pdf.addImage(frontImg, 'PNG', 0, 0, 3.5, 5.5);

            // Back
            pdf.addPage();
            const backCanvas = await html2canvas(cards[1], { scale: 3, useCORS: true, allowTaint: true, backgroundColor: '#f8f9fa' });
            const backImg = backCanvas.toDataURL('image/png');
            pdf.addImage(backImg, 'PNG', 0, 0, 3.5, 5.5);

            const staffId = profile.staff_number || ('TSU-' + String(profile.id).padStart(5, '0'));
            const fileSafeName = `${staffId}_${(profile.first_name || '').trim()}_${(profile.last_name || '').trim()}`.replace(/\s+/g, '_').replace(/[^A-Za-z0-9_\-]/g, '');
            pdf.save(`IDCard_${fileSafeName}.pdf`);
        }

        // Hidden render area for bulk download
        const hiddenDiv = document.createElement('div');
        hiddenDiv.id = 'card-render-area';
        hiddenDiv.style.position = 'fixed';
        hiddenDiv.style.left = '-9999px';
        hiddenDiv.style.top = '-9999px';
        document.body.appendChild(hiddenDiv);
    </script>
</body>
</html>
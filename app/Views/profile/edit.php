<?php
if (!function_exists('url')) { require_once __DIR__ . '/../../Helpers/UrlHelper.php'; }
if (!function_exists('escape_html')) { require_once __DIR__ . '/../../Helpers/TextHelper.php'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - TSU Staff Profile Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar { min-height: 100vh; background: #1e40af; }
        .sidebar .nav-link { color: rgba(255, 255, 255, 0.8); padding: 12px 20px; border-radius: 8px; margin: 2px 0; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255, 255, 255, 0.1); }
        .main-content { background: #f8fafc; min-height: 100vh; }
        .form-section { background: white; border-radius: 12px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
        .section-header { background: #f8f9fa; border-radius: 12px 12px 0 0; padding: 20px; border-bottom: 1px solid #e9ecef; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <div class="text-center mb-4"><i class="fas fa-university fa-2x text-white mb-2"></i><h5 class="text-white mb-0">TSU Staff Portal</h5></div>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="<?= url('dashboard') ?>"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                        <a class="nav-link active" href="<?= url('profile/edit') ?>"><i class="fas fa-user-edit me-2"></i>Edit Profile</a>
                        <a class="nav-link" href="<?= url('profile/education') ?>"><i class="fas fa-graduation-cap me-2"></i>Education</a>
                        <a class="nav-link" href="<?= url('profile/experience') ?>"><i class="fas fa-briefcase me-2"></i>Experience</a>
                        <a class="nav-link" href="<?= url('profile/skills') ?>"><i class="fas fa-cogs me-2"></i>Skills</a>
                        <a class="nav-link" href="<?= url('settings') ?>"><i class="fas fa-cog me-2"></i>Settings</a>
                        <hr class="text-white-50">
                        <a class="nav-link" href="<?= url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </nav>
                </div>
            </div>

            <div class="col-md-9 col-lg-10 main-content">
                <div class="bg-white border-bottom p-3 mb-4">
                    <div class="row align-items-center">
                        <div class="col"><h4 class="mb-0">Edit Profile</h4></div>
                        <div class="col-auto"><a href="<?= url('dashboard') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a></div>
                    </div>
                </div>

                <div class="p-4">
                    <div id="alert-container"></div>
                    <form id="profileForm" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                        <div class="form-section">
                            <div class="section-header"><h5 class="mb-0"><i class="fas fa-image me-2"></i>Profile Photo & Documents</h5></div>
                            <div class="p-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Current Photo</label>
                                        <div class="text-center mb-3">
                                            <div id="currentPhotoPreview">
                                                <?php if (!empty($profile['profile_photo'])): ?>
                                                    <img src="<?= url('storage/uploads/' . $profile['profile_photo']) ?>" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light border rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;"><i class="fas fa-user fa-3x text-muted"></i></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="profile_photo" class="form-label">Update Photo</label>
                                            <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/jpeg,image/jpg,image/png">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">CV</label>
                                        <div class="mb-3">
                                            <?php if (!empty($profile['cv_file'])): ?>
                                                <div class="alert alert-info py-2"><i class="fas fa-file-pdf me-2"></i>Current CV Uploaded</div>
                                            <?php endif; ?>
                                            <label for="cv_file" class="form-label">Upload New CV</label>
                                            <input type="file" class="form-control" id="cv_file" name="cv_file" accept=".pdf,.doc,.docx">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header"><h5 class="mb-0"><i class="fas fa-user me-2"></i>Basic Information</h5></div>
                            <div class="p-4">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Title</label>
                                        <select class="form-select" name="title" required>
                                            <option value="">Select</option>
                                            <?php foreach(['Prof.','Dr.','Mr.','Mrs.','Ms.','Engr.','Arc.'] as $t): ?>
                                                <option value="<?=$t?>" <?=($profile['title']??'')===$t?'selected':''?>><?=$t?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3"><label class="form-label">First Name</label><input type="text" class="form-control" name="first_name" value="<?=safe_output($profile['first_name']??'')?>" required></div>
                                    <div class="col-md-3 mb-3"><label class="form-label">Middle Name</label><input type="text" class="form-control" name="middle_name" value="<?=safe_output($profile['middle_name']??'')?>"></div>
                                    <div class="col-md-3 mb-3"><label class="form-label">Last Name</label><input type="text" class="form-control" name="last_name" value="<?=safe_output($profile['last_name']??'')?>" required></div>
                                </div>
                                <div class="row">
                                    <?php
                                        $currentStaffNumber = $profile['staff_number'] ?? '';
                                        $currentPrefix = ''; $currentNumber = '';
                                        if (preg_match('/^(TSU\/SP\/|TSU\/JP\/)(.+)$/', $currentStaffNumber, $matches)) {
                                            $currentPrefix = $matches[1]; $currentNumber = $matches[2];
                                        }
                                    ?>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Staff Number</label>
                                        <div class="input-group">
                                            <select class="form-select" name="staff_prefix" style="max-width: 120px;" required>
                                                <option value="TSU/SP/" <?=$currentPrefix==='TSU/SP/'?'selected':''?>>TSU/SP/</option>
                                                <option value="TSU/JP/" <?=$currentPrefix==='TSU/JP/'?'selected':''?>>TSU/JP/</option>
                                            </select>
                                            <input type="text" class="form-control" name="staff_number" value="<?=htmlspecialchars($currentNumber)?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Job Title</label>
                                        <input type="text" class="form-control" name="designation" value="<?=safe_output($profile['designation']??'')?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Blood Group *</label>
                                        <select class="form-select" name="blood_group" required>
                                            <option value="">Select</option>
                                            <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                                                <option value="<?=$bg?>" <?=($profile['blood_group']??'')===$bg?'selected':''?>><?=$bg?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Faculty</label>
                                        <select class="form-select" id="faculty" name="faculty" required>
                                            <option value="">Select</option>
                                            <?php foreach ($faculties as $f): ?>
                                                <option value="<?=htmlspecialchars($f['name'])?>" <?=($profile['faculty']??'')===$f['name']?'selected':''?>><?=htmlspecialchars($f['name'])?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Department</label>
                                        <select class="form-select" id="department" name="department" required><option value="">Select Department</option></select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3"><label class="form-label">Office Location</label><input type="text" class="form-control" name="office_location" value="<?=htmlspecialchars($profile['office_location']??'')?>"></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Office Phone</label><input type="tel" class="form-control" name="office_phone" value="<?=htmlspecialchars($profile['office_phone']??'')?>"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header"><h5 class="mb-0"><i class="fas fa-align-left me-2"></i>Professional Summary</h5></div>
                            <div class="p-4">
                                <div class="mb-3">
                                    <label class="form-label">Summary</label>
                                    <textarea class="form-control" name="professional_summary" rows="4"><?=safe_output($profile['professional_summary']??'')?></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3"><label class="form-label">Research Interests</label><textarea class="form-control" name="research_interests" rows="3"><?=htmlspecialchars($profile['research_interests']??'')?></textarea></div>
                                    <div class="col-md-6 mb-3"><label class="form-label">Expertise Keywords</label><textarea class="form-control" name="expertise_keywords" rows="3"><?=htmlspecialchars($profile['expertise_keywords']??'')?></textarea></div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn"><i class="fas fa-save me-2"></i>Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const facultiesData = <?= json_encode($faculties) ?>;
        
        function populateDepartments(selectedFaculty, selectedDepartment = '') {
            const departmentSelect = document.getElementById('department');
            departmentSelect.innerHTML = '<option value="">Select Department</option>';
            const faculty = facultiesData.find(f => f.name === selectedFaculty);
            if (faculty && faculty.departments) {
                faculty.departments.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept;
                    option.textContent = dept;
                    if (dept === selectedDepartment) option.selected = true;
                    departmentSelect.appendChild(option);
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const facultySelect = document.getElementById('faculty');
            const selectedDepartment = <?= json_encode($profile['department'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
            if (facultySelect.value) populateDepartments(facultySelect.value, selectedDepartment);
        });

        document.getElementById('faculty').addEventListener('change', function() {
            populateDepartments(this.value);
        });

        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
            
            fetch('<?= url('profile/update') ?>', { method: 'POST', body: new FormData(this) })
            .then(response => response.json())
            .then(data => {
                const alertContainer = document.getElementById('alert-container');
                const type = data.success ? 'success' : 'danger';
                alertContainer.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show">${data.message || data.error || 'Error occurred'}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
                if(data.success) window.scrollTo({ top: 0, behavior: 'smooth' });
            })
            .catch(() => alert('An error occurred'))
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Profile';
            });
        });
    </script>
</body>
</html>
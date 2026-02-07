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
    <title>System Settings - Admin Panel</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-sidebar {
            background: #2c3e50;
            min-height: 100vh;
            padding-top: 2rem;
        }
        .admin-sidebar .nav-link {
            color: #ecf0f1;
            padding: 1rem 1.5rem;
            border-radius: 0;
            margin-bottom: 0.5rem;
        }
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            background: #34495e;
            color: white;
        }
        .settings-section {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="admin-sidebar">
                    <div class="text-center mb-4">
                        <h4 class="text-white">
                            <i class="fas fa-shield-alt me-2"></i>Admin Panel
                        </h4>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link" href="<?= url('/admin/dashboard') ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="<?= url('/admin/users') ?>">
                            <i class="fas fa-users me-2"></i>Users Management
                        </a>
                        <a class="nav-link" href="<?= url('/admin/publications') ?>">
                            <i class="fas fa-book me-2"></i>Publications
                        </a>
                        <a class="nav-link" href="<?= url('/admin/analytics') ?>">
                            <i class="fas fa-chart-line me-2"></i>Analytics
                        </a>
                        <a class="nav-link" href="<?= url('/admin/activity-logs') ?>">
                            <i class="fas fa-history me-2"></i>Activity Logs
                        </a>
                        <a class="nav-link" href="<?= url('/admin/faculties-departments') ?>">
                            <i class="fas fa-building me-2"></i>Faculties & Departments
                        </a>
                        <a class="nav-link active" href="<?= url('/admin/settings') ?>">
                            <i class="fas fa-cog me-2"></i>System Settings
                        </a>
                        <hr class="text-white">
                        <a class="nav-link" href="<?= url('/logout') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <h1 class="h3 mb-4">
                        <i class="fas fa-cog me-2"></i>System Settings
                    </h1>

                    <!-- Alert Container -->
                    <div id="alert-container"></div>

                    <!-- General Settings -->
                    <div class="settings-section card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>General Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="generalSettingsForm">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                
                                <div class="mb-3">
                                    <label for="site_name" class="form-label">Site Name</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name" 
                                           value="<?= htmlspecialchars($settings['site_name'] ?? 'TSU Staff Profile Portal') ?>">
                                    <div class="form-text">The name of your portal displayed across the site</div>
                                </div>

                                <div class="mb-3">
                                    <label for="site_description" class="form-label">Site Description</label>
                                    <textarea class="form-control" id="site_description" name="site_description" rows="3"><?= htmlspecialchars($settings['site_description'] ?? 'Taraba State University Staff Profile Management System') ?></textarea>
                                    <div class="form-text">Brief description of your portal</div>
                                </div>

                                <div class="mb-3">
                                    <label for="admin_email" class="form-label">Admin Email</label>
                                    <input type="email" class="form-control" id="admin_email" name="admin_email" 
                                           value="<?= htmlspecialchars($settings['admin_email'] ?? 'admin@tsuniversity.edu.ng') ?>">
                                    <div class="form-text">Primary contact email for system notifications</div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save General Settings
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Registration Settings -->
                    <div class="settings-section card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-user-plus me-2"></i>Registration Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="registrationSettingsForm">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="allow_registration" 
                                               name="allow_registration" <?= ($settings['allow_registration'] ?? true) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="allow_registration">
                                            <strong>Allow New Registrations</strong>
                                        </label>
                                    </div>
                                    <div class="form-text">Enable or disable new user registrations</div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="require_email_verification" 
                                               name="require_email_verification" <?= ($settings['require_email_verification'] ?? true) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="require_email_verification">
                                            <strong>Require Email Verification</strong>
                                        </label>
                                    </div>
                                    <div class="form-text">Users must verify their email before accessing the portal</div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="auto_approve_users" 
                                               name="auto_approve_users" <?= ($settings['auto_approve_users'] ?? false) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="auto_approve_users">
                                            <strong>Auto-Approve New Users</strong>
                                        </label>
                                    </div>
                                    <div class="form-text">Automatically activate accounts after email verification</div>
                                </div>

                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Save Registration Settings
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Profile Settings -->
                    <div class="settings-section card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-id-card me-2"></i>Profile Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="profileSettingsForm">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                
                                <div class="mb-3">
                                    <label for="default_profile_visibility" class="form-label">Default Profile Visibility</label>
                                    <select class="form-select" id="default_profile_visibility" name="default_profile_visibility">
                                        <option value="public" <?= ($settings['default_profile_visibility'] ?? 'public') === 'public' ? 'selected' : '' ?>>
                                            Public - Visible to everyone
                                        </option>
                                        <option value="university" <?= ($settings['default_profile_visibility'] ?? 'public') === 'university' ? 'selected' : '' ?>>
                                            University - Visible to logged-in users only
                                        </option>
                                        <option value="private" <?= ($settings['default_profile_visibility'] ?? 'public') === 'private' ? 'selected' : '' ?>>
                                            Private - Visible only to profile owner
                                        </option>
                                    </select>
                                    <div class="form-text">Default visibility for new profiles</div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="require_profile_photo" 
                                               name="require_profile_photo" <?= ($settings['require_profile_photo'] ?? false) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="require_profile_photo">
                                            <strong>Require Profile Photo</strong>
                                        </label>
                                    </div>
                                    <div class="form-text">Make profile photo mandatory during setup</div>
                                </div>

                                <div class="mb-3">
                                    <label for="max_photo_size" class="form-label">Maximum Photo Size (MB)</label>
                                    <input type="number" class="form-control" id="max_photo_size" name="max_photo_size" 
                                           value="<?= $settings['max_photo_size'] ?? 2 ?>" min="1" max="10">
                                    <div class="form-text">Maximum file size for profile photos</div>
                                </div>

                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-save me-2"></i>Save Profile Settings
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="settings-section card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-shield-alt me-2"></i>Security Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="securitySettingsForm">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                
                                <div class="mb-3">
                                    <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                                    <input type="number" class="form-control" id="session_timeout" name="session_timeout" 
                                           value="<?= $settings['session_timeout'] ?? 120 ?>" min="15" max="1440">
                                    <div class="form-text">Automatically log out inactive users after this duration</div>
                                </div>

                                <div class="mb-3">
                                    <label for="password_min_length" class="form-label">Minimum Password Length</label>
                                    <input type="number" class="form-control" id="password_min_length" name="password_min_length" 
                                           value="<?= $settings['password_min_length'] ?? 8 ?>" min="6" max="20">
                                    <div class="form-text">Minimum characters required for passwords</div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="enable_2fa" 
                                               name="enable_2fa" <?= ($settings['enable_2fa'] ?? false) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="enable_2fa">
                                            <strong>Enable Two-Factor Authentication</strong>
                                        </label>
                                    </div>
                                    <div class="form-text">Allow users to enable 2FA for their accounts</div>
                                </div>

                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-2"></i>Save Security Settings
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- System Maintenance -->
                    <div class="settings-section card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-tools me-2"></i>System Maintenance
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Warning:</strong> These actions affect the entire system. Use with caution.
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Clear Cache</h6>
                                            <p class="card-text small">Clear all cached data to improve performance</p>
                                            <button class="btn btn-sm btn-outline-primary" onclick="clearCache()">
                                                <i class="fas fa-broom me-2"></i>Clear Cache
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Database Backup</h6>
                                            <p class="card-text small">Create a backup of the database</p>
                                            <button class="btn btn-sm btn-outline-success" onclick="backupDatabase()">
                                                <i class="fas fa-database me-2"></i>Backup Now
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">System Info</h6>
                                            <p class="card-text small">View system information and diagnostics</p>
                                            <button class="btn btn-sm btn-outline-info" onclick="viewSystemInfo()">
                                                <i class="fas fa-info-circle me-2"></i>View Info
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Maintenance Mode</h6>
                                            <p class="card-text small">Put the site in maintenance mode</p>
                                            <button class="btn btn-sm btn-outline-warning" onclick="toggleMaintenance()">
                                                <i class="fas fa-wrench me-2"></i>Toggle Mode
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = '<?= $_SESSION['csrf_token'] ?? '' ?>';

        // Handle all settings forms
        ['generalSettingsForm', 'registrationSettingsForm', 'profileSettingsForm', 'securitySettingsForm'].forEach(formId => {
            document.getElementById(formId).addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const data = {};
                formData.forEach((value, key) => {
                    if (key !== 'csrf_token') {
                        data[key] = value;
                    }
                });
                
                // Handle checkboxes
                this.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                    data[checkbox.name] = checkbox.checked ? 1 : 0;
                });
                
                try {
                    const response = await fetch('<?= url('/admin/settings') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': csrfToken
                        },
                        body: JSON.stringify({ ...data, csrf_token: csrfToken })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showAlert('success', result.message || 'Settings saved successfully!');
                    } else {
                        showAlert('danger', result.error || 'Failed to save settings');
                    }
                } catch (error) {
                    showAlert('danger', 'Network error. Please try again.');
                }
            });
        });

        // Maintenance functions
        function clearCache() {
            if (confirm('Clear all cached data?')) {
                showAlert('info', 'Cache clearing functionality will be implemented');
            }
        }

        function backupDatabase() {
            if (confirm('Create a database backup?')) {
                showAlert('info', 'Database backup functionality will be implemented');
            }
        }

        function viewSystemInfo() {
            showAlert('info', 'System: PHP ' + '<?= PHP_VERSION ?>' + ' | Database: MySQL');
        }

        function toggleMaintenance() {
            if (confirm('Toggle maintenance mode?')) {
                showAlert('info', 'Maintenance mode functionality will be implemented');
            }
        }

        // Show Alert
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alert-container');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }
    </script>
</body>
</html>
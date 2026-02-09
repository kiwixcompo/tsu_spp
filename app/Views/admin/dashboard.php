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
    <title>Admin Dashboard - TSU Staff Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?php include __DIR__ . '/partials/styles.php'; ?>
    <style>
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .stat-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stat-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <?php $currentPage = 'dashboard'; include __DIR__ . '/partials/sidebar.php'; ?>

            <div class="main-content" id="mainContent">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3">Admin Dashboard</h1>
                        <div class="text-muted">
                            <i class="fas fa-calendar me-2"></i><?= date('F j, Y') ?>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number"><?= $stats['total_users'] ?? 0 ?></div>
                                <div class="stat-label">Total Users</div>
                                <small><i class="fas fa-users me-1"></i>All registered users</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card success">
                                <div class="stat-number"><?= $stats['active_users'] ?? 0 ?></div>
                                <div class="stat-label">Active Users</div>
                                <small><i class="fas fa-check-circle me-1"></i>Verified accounts</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card warning">
                                <div class="stat-number"><?= $stats['pending_users'] ?? 0 ?></div>
                                <div class="stat-label">Pending Users</div>
                                <small><i class="fas fa-clock me-1"></i>Awaiting verification</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card info">
                                <div class="stat-number"><?= $stats['total_profiles'] ?? 0 ?></div>
                                <div class="stat-label">Total Profiles</div>
                                <small><i class="fas fa-id-card me-1"></i>Created profiles</small>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Complete Profiles</h5>
                                    <div class="display-6 text-success"><?= $stats['complete_profiles'] ?? 0 ?></div>
                                    <small class="text-muted">With photo & summary</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Recent Registrations</h5>
                                    <div class="display-6 text-primary"><?= $stats['recent_registrations'] ?? 0 ?></div>
                                    <small class="text-muted">Last 7 days</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Completion Rate</h5>
                                    <div class="display-6 text-info">
                                        <?php 
                                        $total = $stats['total_profiles'] ?? 0;
                                        $complete = $stats['complete_profiles'] ?? 0;
                                        $rate = $total > 0 ? round(($complete / $total) * 100) : 0;
                                        echo $rate . '%';
                                        ?>
                                    </div>
                                    <small class="text-muted">Profile completion</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Publications</h5>
                                    <div class="display-6 text-warning"><?= $stats['total_publications'] ?? 0 ?></div>
                                    <small class="text-muted">Research output</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bolt me-2"></i>Quick Actions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <a href="<?= url('/admin/users') ?>" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-users me-2"></i>Manage Users
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <a href="<?= url('/admin/publications') ?>" class="btn btn-outline-success w-100">
                                                <i class="fas fa-book me-2"></i>View Publications
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <a href="<?= url('/admin/analytics') ?>" class="btn btn-outline-info w-100">
                                                <i class="fas fa-chart-line me-2"></i>View Analytics
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <a href="<?= url('/admin/activity-logs') ?>" class="btn btn-outline-warning w-100">
                                                <i class="fas fa-history me-2"></i>Activity Logs
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Recent Users -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Recent Users</h5>
                                    <a href="<?= url('/admin/users') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($recent_users)): ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach ($recent_users as $user): ?>
                                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'No Name' ?>
                                                        </h6>
                                                        <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                                        <?php if (!empty($user['department'])): ?>
                                                            <br><small class="text-info"><?= htmlspecialchars($user['department']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-<?= $user['account_status'] === 'active' ? 'success' : ($user['account_status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                            <?= ucfirst($user['account_status']) ?>
                                                        </span>
                                                        <br><small class="text-muted"><?= date('M j', strtotime($user['created_at'])) ?></small>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted text-center">No recent users found.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Verifications -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Pending Verifications</h5>
                                    <span class="badge bg-warning"><?= count($pending_users ?? []) ?></span>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($pending_users)): ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach (array_slice($pending_users, 0, 5) as $user): ?>
                                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'No Name' ?>
                                                        </h6>
                                                        <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                                    </div>
                                                    <div class="text-end">
                                                        <button class="btn btn-sm btn-success" onclick="activateUser(<?= $user['id'] ?>)">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <br><small class="text-muted"><?= date('M j', strtotime($user['created_at'])) ?></small>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php if (count($pending_users) > 5): ?>
                                            <div class="text-center mt-3">
                                                <a href="<?= url('/admin/users?status=pending') ?>" class="btn btn-sm btn-outline-primary">
                                                    View All Pending (<?= count($pending_users) ?>)
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-muted text-center">No pending verifications.</p>
                                    <?php endif; ?>
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
        // CSRF token for AJAX requests
        const csrfToken = '<?= $_SESSION['csrf_token'] ?? '' ?>';
        
        // Activate user function
        async function activateUser(userId) {
            if (!confirm('Are you sure you want to activate this user?')) {
                return;
            }
            
            try {
                const response = await fetch('<?= url('/admin/activate-user') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        csrf_token: csrfToken
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('User activated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.error || 'Failed to activate user'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
    </script>
    <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
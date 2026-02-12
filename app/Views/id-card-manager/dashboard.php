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
    <title>ID Card Manager Dashboard - TSU Staff Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, #1e3a8a 100%);
            color: white;
            position: fixed;
            width: 260px;
            padding: 0;
        }
        
        .sidebar-header {
            padding: 20px;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left: 4px solid var(--warning-color);
        }
        
        .sidebar-menu a i {
            width: 25px;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .stat-icon.blue { background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); color: white; }
        .stat-icon.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
        .stat-icon.orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }
        .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white; }
        
        .recent-activity {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .activity-item {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .quick-action-btn {
            padding: 15px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>ID Card Manager</h5>
            <small class="text-white-50"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></small>
        </div>
        <div class="sidebar-menu">
            <a href="<?= url('id-card-manager/dashboard') ?>" class="active">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="<?= url('id-card-manager/browse') ?>">
                <i class="fas fa-search"></i> Browse Profiles
            </a>
            <a href="<?= url('id-card-manager/print-history') ?>">
                <i class="fas fa-history"></i> Print History
            </a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="<?= url('id-card-manager/settings') ?>">
                <i class="fas fa-cog"></i> Settings
            </a>
            <a href="<?= url('admin/dashboard') ?>">
                <i class="fas fa-shield-alt"></i> Admin Panel
            </a>
            <?php endif; ?>
            <a href="<?= url('dashboard') ?>">
                <i class="fas fa-user"></i> My Profile
            </a>
            <a href="<?= url('logout') ?>">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">ID Card Management Dashboard</h2>
                <p class="text-muted mb-0">Manage and print staff ID cards</p>
            </div>
            <div>
                <span class="badge bg-success fs-6"><i class="fas fa-circle me-1"></i>Online</span>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="mb-1"><?= number_format($stats['total_profiles']) ?></h3>
                    <p class="text-muted mb-0">Total Profiles</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-print"></i>
                    </div>
                    <h3 class="mb-1"><?= number_format($stats['prints_today']) ?></h3>
                    <p class="text-muted mb-0">Prints Today</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="mb-1"><?= number_format($stats['prints_this_month']) ?></h3>
                    <p class="text-muted mb-0">This Month</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="mb-1"><?= number_format($stats['total_prints']) ?></h3>
                    <p class="text-muted mb-0">Total Prints</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-4 mb-4">
            <div class="col-md-12">
                <div class="recent-activity">
                    <h5 class="mb-4"><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="<?= url('id-card-manager/browse') ?>" class="btn btn-primary w-100 quick-action-btn">
                                <i class="fas fa-search me-2"></i>Browse & Print
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= url('admin/id-card-generator') ?>" class="btn btn-success w-100 quick-action-btn">
                                <i class="fas fa-id-card me-2"></i>Print Single Card
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= url('id-card-manager/print-history') ?>" class="btn btn-info w-100 quick-action-btn">
                                <i class="fas fa-history me-2"></i>View History
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= url('directory') ?>" class="btn btn-secondary w-100 quick-action-btn">
                                <i class="fas fa-address-book me-2"></i>Staff Directory
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recent Print Activity -->
            <div class="col-md-8">
                <div class="recent-activity">
                    <h5 class="mb-4"><i class="fas fa-clock me-2 text-primary"></i>Recent Print Activity</h5>
                    <?php if (empty($recentPrints)): ?>
                        <p class="text-muted text-center py-4">No print activity yet</p>
                    <?php else: ?>
                        <?php foreach ($recentPrints as $print): ?>
                        <div class="activity-item">
                            <div class="activity-avatar">
                                <?= strtoupper(substr($print['first_name'], 0, 1) . substr($print['last_name'], 0, 1)) ?>
                            </div>
                            <div class="flex-grow-1">
                                <strong><?= htmlspecialchars($print['first_name'] . ' ' . $print['last_name']) ?></strong>
                                <span class="text-muted">(<?= htmlspecialchars($print['staff_number']) ?>)</span>
                                <br>
                                <small class="text-muted">
                                    Printed by <?= htmlspecialchars($print['printer_email']) ?> • 
                                    <?= date('M d, Y h:i A', strtotime($print['created_at'])) ?>
                                </small>
                            </div>
                            <span class="badge bg-<?= $print['print_type'] === 'bulk' ? 'warning' : 'success' ?>">
                                <?= ucfirst($print['print_type']) ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pending Profiles -->
            <div class="col-md-4">
                <div class="recent-activity">
                    <h5 class="mb-4"><i class="fas fa-exclamation-circle me-2 text-warning"></i>Pending ID Cards</h5>
                    <?php if (empty($pendingProfiles)): ?>
                        <p class="text-muted text-center py-4">All profiles have ID cards</p>
                    <?php else: ?>
                        <div style="max-height: 400px; overflow-y: auto;">
                            <?php foreach (array_slice($pendingProfiles, 0, 10) as $profile): ?>
                            <div class="activity-item">
                                <div class="activity-avatar">
                                    <?= strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1)) ?>
                                </div>
                                <div class="flex-grow-1">
                                    <strong><?= htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($profile['staff_number']) ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="<?= url('id-card-manager/browse') ?>" class="btn btn-sm btn-outline-primary w-100 mt-3">
                            View All Pending
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Activity Chart -->
        <div class="row g-4 mt-2">
            <div class="col-md-12">
                <div class="recent-activity">
                    <h5 class="mb-4"><i class="fas fa-chart-line me-2 text-success"></i>Print Activity (Last 7 Days)</h5>
                    <canvas id="activityChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Activity Chart
        const activityData = <?= json_encode($stats['activity_chart']) ?>;
        const labels = activityData.map(d => new Date(d.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
        const data = activityData.map(d => d.count);

        const ctx = document.getElementById('activityChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'ID Cards Printed',
                    data: data,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

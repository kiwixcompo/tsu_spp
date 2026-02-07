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
    <title>Analytics - Admin Panel</title>
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
                        <a class="nav-link active" href="<?= url('/admin/analytics') ?>">
                            <i class="fas fa-chart-line me-2"></i>Analytics
                        </a>
                        <a class="nav-link" href="<?= url('/admin/activity-logs') ?>">
                            <i class="fas fa-history me-2"></i>Activity Logs
                        </a>
                        <a class="nav-link" href="<?= url('/admin/faculties-departments') ?>">
                            <i class="fas fa-building me-2"></i>Faculties & Departments
                        </a>
                        <a class="nav-link" href="<?= url('/admin/settings') ?>">
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
                        <i class="fas fa-chart-line me-2"></i>Analytics & Reports
                    </h1>

                    <!-- Profile Completion -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Profile Completion Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="text-center p-3">
                                                <h3 class="text-primary"><?= $analytics['profile_completion']['with_photo'] ?? 0 ?></h3>
                                                <p class="mb-0">With Profile Photo</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center p-3">
                                                <h3 class="text-success"><?= $analytics['profile_completion']['with_summary'] ?? 0 ?></h3>
                                                <p class="mb-0">With Summary</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center p-3">
                                                <h3 class="text-info"><?= $analytics['profile_completion']['with_publications'] ?? 0 ?></h3>
                                                <p class="mb-0">With Publications</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Faculty Distribution -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Faculty Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($analytics['faculty_distribution'])): ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Faculty</th>
                                                        <th class="text-end">Staff Count</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($analytics['faculty_distribution'] as $faculty): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($faculty['faculty']) ?></td>
                                                            <td class="text-end">
                                                                <span class="badge bg-primary"><?= $faculty['count'] ?></span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted text-center">No data available</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Publication Statistics -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Publication Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h3 class="text-primary"><?= $analytics['publication_stats']['total'] ?? 0 ?></h3>
                                        <p class="mb-0">Total Publications</p>
                                    </div>
                                    <div class="mb-3">
                                        <h4 class="text-success"><?= $analytics['publication_stats']['recent'] ?? 0 ?></h4>
                                        <p class="mb-0">Last 30 Days</p>
                                    </div>
                                    <?php if (!empty($analytics['publication_stats']['by_type'])): ?>
                                        <hr>
                                        <h6>By Type:</h6>
                                        <?php foreach ($analytics['publication_stats']['by_type'] as $type): ?>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span><?= ucfirst($type['publication_type']) ?></span>
                                                <span class="badge bg-secondary"><?= $type['count'] ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Contributors -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Top Contributors</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($analytics['top_contributors'])): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Faculty</th>
                                                        <th>Department</th>
                                                        <th class="text-center">Publications</th>
                                                        <th class="text-center">Education</th>
                                                        <th class="text-center">Experience</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($analytics['top_contributors'] as $contributor): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars(($contributor['first_name'] ?? '') . ' ' . ($contributor['last_name'] ?? '')) ?></td>
                                                            <td><?= htmlspecialchars($contributor['faculty'] ?? 'N/A') ?></td>
                                                            <td><?= htmlspecialchars($contributor['department'] ?? 'N/A') ?></td>
                                                            <td class="text-center">
                                                                <span class="badge bg-primary"><?= $contributor['publication_count'] ?? 0 ?></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-success"><?= $contributor['education_count'] ?? 0 ?></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-info"><?= $contributor['experience_count'] ?? 0 ?></span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted text-center">No data available</p>
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
</body>
</html>
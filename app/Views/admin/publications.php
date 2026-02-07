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
    <title>Publications - Admin Panel</title>
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
                        <a class="nav-link active" href="<?= url('/admin/publications') ?>">
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
                        <i class="fas fa-book me-2"></i>Publications Management
                    </h1>

                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3 class="text-primary"><?= $total_publications ?? 0 ?></h3>
                                    <p class="mb-0">Total Publications</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3 class="text-success"><?= count(array_filter($publications ?? [], fn($p) => $p['publication_type'] === 'journal')) ?></h3>
                                    <p class="mb-0">Journal Articles</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3 class="text-info"><?= count(array_filter($publications ?? [], fn($p) => $p['publication_type'] === 'conference')) ?></h3>
                                    <p class="mb-0">Conference Papers</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3 class="text-warning"><?= count(array_filter($publications ?? [], fn($p) => $p['publication_type'] === 'book')) ?></h3>
                                    <p class="mb-0">Books</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Publications Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th>Type</th>
                                            <th>Year</th>
                                            <th>Faculty</th>
                                            <th>Citations</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($publications)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No publications found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($publications as $pub): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars(substr($pub['title'], 0, 80)) ?><?= strlen($pub['title']) > 80 ? '...' : '' ?></strong>
                                                        <?php if ($pub['doi']): ?>
                                                            <br><small class="text-muted">DOI: <?= htmlspecialchars($pub['doi']) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars(($pub['first_name'] ?? '') . ' ' . ($pub['last_name'] ?? '')) ?>
                                                        <br><small class="text-muted"><?= htmlspecialchars($pub['email'] ?? '') ?></small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary"><?= ucfirst($pub['publication_type']) ?></span>
                                                    </td>
                                                    <td><?= $pub['year'] ?? 'N/A' ?></td>
                                                    <td><?= htmlspecialchars($pub['faculty'] ?? 'N/A') ?></td>
                                                    <td><?= $pub['citation_count'] ?? 0 ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
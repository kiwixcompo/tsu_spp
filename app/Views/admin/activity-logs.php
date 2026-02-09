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
    <title>Activity Logs - Admin Panel</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?php include __DIR__ . '/partials/styles.php'; ?>
    <style>
        .log-entry {
            border-left: 3px solid #dee2e6;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        .log-entry.recent {
            border-left-color: #0d6efd;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <?php $currentPage = 'activity-logs'; include __DIR__ . '/partials/sidebar.php'; ?>

            <div class="main-content" id="mainContent">
                <div class="p-4">
                    <h1 class="h3 mb-4">
                        <i class="fas fa-history me-2"></i>Activity Logs
                    </h1>

                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3 class="text-primary"><?= $total_logs ?? 0 ?></h3>
                                    <p class="mb-0">Total Activities</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3 class="text-success"><?= count(array_filter($logs ?? [], fn($l) => strtotime($l['created_at']) > strtotime('-24 hours'))) ?></h3>
                                    <p class="mb-0">Last 24 Hours</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3 class="text-info"><?= count(array_unique(array_column($logs ?? [], 'user_id'))) ?></h3>
                                    <p class="mb-0">Active Users</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Logs -->
                    <div class="card">
                        <div class="card-body">
                            <?php if (empty($logs)): ?>
                                <p class="text-center text-muted">No activity logs found</p>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <div class="log-entry <?= strtotime($log['created_at']) > strtotime('-1 hour') ? 'recent' : '' ?>">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong><?= htmlspecialchars(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? '')) ?: 'Unknown User' ?></strong>
                                                <small class="text-muted">(<?= htmlspecialchars($log['email'] ?? 'N/A') ?>)</small>
                                                <br>
                                                <span class="badge bg-secondary"><?= ucwords(str_replace('_', ' ', $log['action'])) ?></span>
                                                <?php if (!empty($log['details'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($log['details']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted"><?= date('M j, Y H:i', strtotime($log['created_at'])) ?></small>
                                                <?php if (!empty($log['ip_address'])): ?>
                                                    <br><small class="text-muted">IP: <?= htmlspecialchars($log['ip_address']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Pagination -->
                            <?php if (($total_pages ?? 1) > 1): ?>
                                <nav class="mt-4">
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
    <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
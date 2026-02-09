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
    <?php include __DIR__ . '/partials/styles.php'; ?>
</head>
<body class="bg-light">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <?php $currentPage = 'publications'; include __DIR__ . '/partials/sidebar.php'; ?>

            <div class="main-content" id="mainContent">
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
    <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
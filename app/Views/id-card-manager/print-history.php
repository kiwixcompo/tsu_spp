<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print History - ID Card Manager</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="main-content">
        <h2 class="mb-4">Print History</h2>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Staff Number</th>
                                <th>Name</th>
                                <th>Printed By</th>
                                <th>Type</th>
                                <th>Format</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= date('M d, Y h:i A', strtotime($log['created_at'])) ?></td>
                                <td><?= htmlspecialchars($log['staff_number']) ?></td>
                                <td><?= htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) ?></td>
                                <td><?= htmlspecialchars($log['printer_email']) ?></td>
                                <td><span class="badge bg-<?= $log['print_type'] === 'bulk' ? 'warning' : 'info' ?>"><?= ucfirst($log['print_type']) ?></span></td>
                                <td><?= strtoupper($log['print_format']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

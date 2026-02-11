<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - TSU Staff Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 text-center">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <i class="fas fa-server fa-5x text-danger mb-4"></i>
                        <h1 class="display-4 fw-bold text-primary">500</h1>
                        <h2 class="h4 mb-3">Internal Server Error</h2>
                        <p class="text-muted mb-4">
                            Something went wrong on our end. We're working to fix this issue. 
                            Please try again later.
                        </p>
                        <div class="d-flex gap-3 justify-content-center">
                            <a href="<?= url() ?>" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>Go Home
                            </a>
                            <button onclick="window.location.reload()" class="btn btn-outline-primary">
                                <i class="fas fa-redo me-2"></i>Try Again
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
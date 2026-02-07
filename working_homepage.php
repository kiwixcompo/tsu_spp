<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TSU Staff Profile Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-university me-2"></i>TSU Staff Portal
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-4 text-primary mb-4">Welcome to TSU Staff Profile Portal</h1>
                <p class="lead mb-4">Taraba State University Staff Profile Management System</p>
                
                <div class="alert alert-success">
                    <h4>✅ Application is Working!</h4>
                    <p>The basic structure is set up correctly. Now let's complete the setup.</p>
                </div>

                <div class="row mt-5">
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-database fa-3x text-primary mb-3"></i>
                                <h5>Database Setup</h5>
                                <p>Import the database schema to get started.</p>
                                <a href="/test_db.php" class="btn btn-outline-primary">Test Database</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-bug fa-3x text-warning mb-3"></i>
                                <h5>Debug System</h5>
                                <p>Check system status and troubleshoot issues.</p>
                                <a href="/debug_detailed.php" class="btn btn-outline-warning">Run Debug</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-3x text-success mb-3"></i>
                                <h5>User Registration</h5>
                                <p>Create your staff profile account.</p>
                                <a href="/register" class="btn btn-outline-success">Register</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <h3>Quick Setup Guide</h3>
                    <ol class="text-start">
                        <li><strong>Database:</strong> Import <code>database/simple_setup.sql</code> in phpMyAdmin</li>
                        <li><strong>Test:</strong> Visit <code>/test_db.php</code> to verify database connection</li>
                        <li><strong>Debug:</strong> If issues persist, run <code>/debug_detailed.php</code></li>
                        <li><strong>Login:</strong> Use <code>admin@tsuniversity.edu.ng</code> / <code>Admin123!</code></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
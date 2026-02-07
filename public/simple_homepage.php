<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TSU Staff Profile Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?= url('') ?>">
                <img src="<?= asset('assets/images/tsu-logo.png') ?>" alt="TSU Logo" height="40" class="me-2">
                TSU Staff Portal
            </a>
            <div class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="<?= url('dashboard') ?>">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                    <a class="nav-link" href="<?= url('logout') ?>">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-4 text-primary">Welcome to TSU Staff Profile Portal</h1>
                <p class="lead">Taraba State University Staff Profile Management System</p>
                


                <div class="row mt-5">
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                                <h5>Create Profile</h5>
                                <p>Register and create your professional profile.</p>
                                <a href="<?= url('register') ?>" class="btn btn-primary">Register Now</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-sign-in-alt fa-3x text-success mb-3"></i>
                                <h5>Staff Login</h5>
                                <p>Access your existing staff profile.</p>
                                <a href="<?= url('login') ?>" class="btn btn-success">Login</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-3x text-info mb-3"></i>
                                <h5>Browse Directory</h5>
                                <p>Explore staff profiles and expertise.</p>
                                <a href="<?= url('directory') ?>" class="btn btn-info">Browse</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <h3 class="mb-4">Key Features</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <i class="fas fa-user-circle me-2"></i>Professional Profiles
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>Comprehensive profile management</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Education & experience tracking</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Publications showcase</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Skills & expertise display</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <i class="fas fa-search me-2"></i>Advanced Search
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>Search by faculty & department</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Filter by designation</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Research interests matching</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Expertise-based discovery</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <i class="fas fa-shield-alt me-2"></i>Secure & Private
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>Email verification system</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Privacy controls</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Secure authentication</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Profile visibility settings</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 mb-5">
                    <div class="card bg-light">
                        <div class="card-body text-center py-4">
                            <h4 class="mb-3">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Getting Started
                            </h4>
                            <p class="lead mb-4">
                                Join the TSU Staff Profile Portal to showcase your academic achievements, 
                                connect with colleagues, and enhance collaboration across the university.
                            </p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="<?= url('register') ?>" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Create Your Profile
                                </a>
                                <a href="<?= url('directory') ?>" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-users me-2"></i>Explore Directory
                                </a>
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
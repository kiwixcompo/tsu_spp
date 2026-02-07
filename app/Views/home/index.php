<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TSU Staff Profile Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 80px 0;
        }
        .feature-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .stats-section {
            background: #f8fafc;
            padding: 60px 0;
        }
        .profile-card {
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .profile-card:hover {
            transform: translateY(-3px);
        }
        .profile-photo {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?= url() ?>">
                <img src="<?= url('assets/images/tsu-logo.png') ?>" alt="TSU Logo" height="40" class="me-2">
                TSU Staff Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= url() ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('directory') ?>">Directory</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('search') ?>">Search</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('about') ?>">About</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('dashboard') ?>">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('logout') ?>">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        Welcome to TSU Staff Profile Portal
                    </h1>
                    <p class="lead mb-4">
                        Discover and connect with academic and administrative staff at Taraba State University. 
                        Showcase your expertise, research interests, and professional achievements.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="<?= url('register') ?>" class="btn btn-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Create Profile
                        </a>
                        <a href="<?= url('directory') ?>" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-search me-2"></i>Browse Profiles
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="<?= asset('assets/images/tsu-logo.png') ?>" 
                         alt="TSU Logo" 
                         style="max-width: 400px; width: 100%; height: auto; opacity: 0.9;" 
                         class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100 p-4">
                        <div class="card-body">
                            <i class="fas fa-users fa-3x text-primary mb-3"></i>
                            <h3 class="fw-bold"><?= $stats['total_profiles'] ?? 0 ?></h3>
                            <p class="text-muted">Active Profiles</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100 p-4">
                        <div class="card-body">
                            <i class="fas fa-building fa-3x text-primary mb-3"></i>
                            <h3 class="fw-bold"><?= $stats['total_faculties'] ?? 0 ?></h3>
                            <p class="text-muted">Faculties</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100 p-4">
                        <div class="card-body">
                            <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
                            <h3 class="fw-bold"><?= $stats['total_departments'] ?? 0 ?></h3>
                            <p class="text-muted">Departments</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-5 fw-bold mb-3">Why Choose TSU Staff Portal?</h2>
                    <p class="lead text-muted">
                        A comprehensive platform designed specifically for university staff to showcase their 
                        professional achievements and connect with colleagues.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-user-circle fa-2x text-primary me-3"></i>
                                <h5 class="fw-bold mb-0">Professional Profiles</h5>
                            </div>
                            <p class="text-muted">
                                Create comprehensive profiles showcasing your education, experience, 
                                publications, and research interests.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-search fa-2x text-primary me-3"></i>
                                <h5 class="fw-bold mb-0">Advanced Search</h5>
                            </div>
                            <p class="text-muted">
                                Find colleagues by faculty, department, research interests, 
                                or expertise using our powerful search engine.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-shield-alt fa-2x text-primary me-3"></i>
                                <h5 class="fw-bold mb-0">Secure & Private</h5>
                            </div>
                            <p class="text-muted">
                                Control your profile visibility and manage your privacy settings 
                                with our secure authentication system.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Profiles Section -->
    <?php if (!empty($featured_profiles)): ?>
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h2 class="display-6 fw-bold mb-3">Featured Staff Profiles</h2>
                    <p class="lead text-muted">Meet some of our distinguished faculty and staff members</p>
                </div>
            </div>
            <div class="row">
                <?php foreach (array_slice($featured_profiles, 0, 4) as $profile): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card profile-card h-100">
                        <div class="card-body text-center p-4">
                            <?php if ($profile['profile_photo']): ?>
                                <img src="/storage/uploads/<?= htmlspecialchars($profile['profile_photo']) ?>" 
                                     alt="Profile Photo" class="profile-photo mb-3">
                            <?php else: ?>
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 80px; height: 80px; font-size: 2rem;">
                                    <?= strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <h6 class="fw-bold mb-1">
                                <?= htmlspecialchars($profile['title'] . ' ' . $profile['first_name'] . ' ' . $profile['last_name']) ?>
                            </h6>
                            <p class="text-muted small mb-2"><?= htmlspecialchars($profile['designation'] ?? 'Staff Member') ?></p>
                            <p class="text-muted small mb-3"><?= htmlspecialchars($profile['department'] ?? '') ?></p>
                            <a href="<?= url('profile/' . htmlspecialchars($profile['profile_slug'])) ?>" 
                               class="btn btn-outline-primary btn-sm">
                                View Profile
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="<?= url('directory') ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-users me-2"></i>View All Profiles
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-university me-2"></i>
                        TSU Staff Portal
                    </h5>
                    <p class="text-muted">
                        Connecting the academic community at Taraba State University through 
                        professional profiles and collaboration.
                    </p>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= url() ?>" class="text-muted text-decoration-none">Home</a></li>
                        <li><a href="<?= url('directory') ?>" class="text-muted text-decoration-none">Directory</a></li>
                        <li><a href="<?= url('search') ?>" class="text-muted text-decoration-none">Search</a></li>
                        <li><a href="<?= url('about') ?>" class="text-muted text-decoration-none">About</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Account</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= url('register') ?>" class="text-muted text-decoration-none">Register</a></li>
                        <li><a href="<?= url('login') ?>" class="text-muted text-decoration-none">Login</a></li>
                        <li><a href="<?= url('forgot-password') ?>" class="text-muted text-decoration-none">Reset Password</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3">Contact Information</h6>
                    <p class="text-muted mb-1">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Taraba State University, Jalingo
                    </p>
                    <p class="text-muted mb-1">
                        <i class="fas fa-envelope me-2"></i>
                        info@tsuniversity.edu.ng
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-phone me-2"></i>
                        +234 (0) 79 xxx xxxx
                    </p>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        &copy; <?= date('Y') ?> Taraba State University. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-muted me-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-muted me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-muted me-3"><i class="fab fa-linkedin"></i></a>
                    <a href="#" class="text-muted"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
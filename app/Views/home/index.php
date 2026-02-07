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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        .navbar {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            color: white !important;
            font-weight: 600;
            font-size: 1.3rem;
        }
        .hero-section {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 60px 0;
            text-align: center;
        }
        .hero-title {
            color: #1976d2;
            font-size: 2.5rem;
            font-weight: 300;
            margin-bottom: 10px;
        }
        .hero-subtitle {
            color: #666;
            font-size: 1rem;
            margin-bottom: 40px;
        }
        .action-card {
            background: white;
            border-radius: 10px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .action-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        .action-icon.blue { color: #2196f3; }
        .action-icon.green { color: #4caf50; }
        .action-icon.cyan { color: #00bcd4; }
        .action-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        .action-desc {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        .btn-action {
            padding: 10px 30px;
            border-radius: 5px;
            font-weight: 500;
            text-transform: capitalize;
        }
        .btn-blue { background: #2196f3; color: white; }
        .btn-blue:hover { background: #1976d2; color: white; }
        .btn-green { background: #4caf50; color: white; }
        .btn-green:hover { background: #388e3c; color: white; }
        .btn-cyan { background: #00bcd4; color: white; }
        .btn-cyan:hover { background: #0097a7; color: white; }
        .features-section {
            padding: 60px 0;
            background: white;
        }
        .section-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 50px;
        }
        .feature-box {
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            height: 100%;
        }
        .feature-box.blue { background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); color: white; }
        .feature-box.green { background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white; }
        .feature-box.cyan { background: linear-gradient(135deg, #00bcd4 0%, #0097a7 100%); color: white; }
        .feature-box-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .feature-list li {
            padding: 8px 0;
            font-size: 0.95rem;
        }
        .feature-list li:before {
            content: "✓ ";
            margin-right: 8px;
        }
        .getting-started {
            background: #f5f5f5;
            padding: 60px 0;
            text-align: center;
        }
        .getting-started-box {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .getting-started-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .getting-started-text {
            color: #666;
            margin-bottom: 30px;
            font-size: 1rem;
        }
        .btn-outline-blue {
            border: 2px solid #2196f3;
            color: #2196f3;
            padding: 10px 30px;
            border-radius: 5px;
            font-weight: 500;
        }
        .btn-outline-blue:hover {
            background: #2196f3;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= url() ?>">
                <img src="<?= asset('assets/images/tsu-logo.png') ?>" alt="TSU Logo" height="35" class="me-2">
                TSU Staff Portal
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Welcome to TSU Staff Profile Portal</h1>
            <p class="hero-subtitle">Taraba State University, Staff Profile Management System</p>
            
            <div class="row g-4 mt-4">
                <!-- Create Profile Card -->
                <div class="col-md-4">
                    <div class="action-card">
                        <div class="action-icon blue">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h3 class="action-title">Create Profile</h3>
                        <p class="action-desc">Register and create your professional profile</p>
                        <a href="<?= url('register') ?>" class="btn btn-action btn-blue">
                            Register Now
                        </a>
                    </div>
                </div>

                <!-- Staff Login Card -->
                <div class="col-md-4">
                    <div class="action-card">
                        <div class="action-icon green">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <h3 class="action-title">Staff Login</h3>
                        <p class="action-desc">Access your existing staff profile</p>
                        <a href="<?= url('login') ?>" class="btn btn-action btn-green">
                            Login
                        </a>
                    </div>
                </div>

                <!-- Browse Directory Card -->
                <div class="col-md-4">
                    <div class="action-card">
                        <div class="action-icon cyan">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="action-title">Browse Directory</h3>
                        <p class="action-desc">Explore staff profiles and expertise</p>
                        <a href="<?= url('directory') ?>" class="btn btn-action btn-cyan">
                            Browse
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Key Features</h2>
            
            <div class="row g-4">
                <!-- Professional Profiles -->
                <div class="col-md-4">
                    <div class="feature-box blue">
                        <div class="feature-box-title">
                            <i class="fas fa-id-card"></i>
                            Professional Profiles
                        </div>
                        <ul class="feature-list">
                            <li>Comprehensive profile management</li>
                            <li>Education & experience tracking</li>
                            <li>Publications showcase</li>
                            <li>Skills & expertise display</li>
                        </ul>
                    </div>
                </div>

                <!-- Advanced Search -->
                <div class="col-md-4">
                    <div class="feature-box green">
                        <div class="feature-box-title">
                            <i class="fas fa-search"></i>
                            Advanced Search
                        </div>
                        <ul class="feature-list">
                            <li>Search by faculty & department</li>
                            <li>Filter by designation</li>
                            <li>Research interests matching</li>
                            <li>Expertise-based discovery</li>
                        </ul>
                    </div>
                </div>

                <!-- Secure & Private -->
                <div class="col-md-4">
                    <div class="feature-box cyan">
                        <div class="feature-box-title">
                            <i class="fas fa-shield-alt"></i>
                            Secure & Private
                        </div>
                        <ul class="feature-list">
                            <li>Email verification system</li>
                            <li>Privacy controls</li>
                            <li>Secure authentication</li>
                            <li>Profile visibility settings</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Getting Started Section -->
    <section class="getting-started">
        <div class="container">
            <div class="getting-started-box">
                <h2 class="getting-started-title">
                    <i class="fas fa-info-circle text-primary"></i>
                    Getting Started
                </h2>
                <p class="getting-started-text">
                    Join the TSU Staff Profile Portal to showcase your academic achievements, connect with colleagues, 
                    and enhance collaboration across the university.
                </p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="<?= url('register') ?>" class="btn btn-action btn-blue">
                        <i class="fas fa-user-plus me-2"></i>Create Your Profile
                    </a>
                    <a href="<?= url('directory') ?>" class="btn btn-outline-blue">
                        <i class="fas fa-search me-2"></i>Explore Directory
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
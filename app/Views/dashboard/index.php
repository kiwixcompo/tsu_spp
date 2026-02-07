<?php
// Load helpers if not already loaded
if (!function_exists('url')) {
    require_once __DIR__ . '/../../Helpers/UrlHelper.php';
}
if (!function_exists('escape_html')) {
    require_once __DIR__ . '/../../Helpers/TextHelper.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TSU Staff Profile Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #1e40af;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 0;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        .main-content {
            background: #f8fafc;
            min-height: 100vh;
        }
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .progress-circle {
            width: 80px;
            height: 80px;
        }
        .activity-item {
            border-left: 3px solid #e9ecef;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        .activity-item.recent {
            border-left-color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <div class="text-center mb-4">
                        <i class="fas fa-university fa-2x text-white mb-2"></i>
                        <h5 class="text-white mb-0">TSU Staff Profile Portal</h5>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="<?= url('dashboard') ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="<?= url('profile/edit') ?>">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </a>
                        <a class="nav-link" href="<?= url('profile/education') ?>">
                            <i class="fas fa-graduation-cap me-2"></i>Education
                        </a>
                        <a class="nav-link" href="<?= url('profile/experience') ?>">
                            <i class="fas fa-briefcase me-2"></i>Experience
                        </a>
                        <a class="nav-link" href="<?= url('profile/publications') ?>">
                            <i class="fas fa-book me-2"></i>Publications
                        </a>
                        <a class="nav-link" href="<?= url('profile/skills') ?>">
                            <i class="fas fa-cogs me-2"></i>Skills
                        </a>
                        <a class="nav-link" href="<?= url('settings') ?>">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                        <hr class="text-white-50">
                        <?php if (!empty($user['profile_slug'])): ?>
                        <a class="nav-link" href="<?= url('profile/' . htmlspecialchars($user['profile_slug'])) ?>" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>View Public Profile
                        </a>
                        <?php endif; ?>
                        <a class="nav-link" href="<?= url('directory') ?>">
                            <i class="fas fa-users me-2"></i>Staff Directory
                        </a>
                        <a class="nav-link" href="<?= url('logout') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header -->
                <div class="bg-white border-bottom p-3 mb-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="mb-0">Welcome back, <?= safe_output($profile['first_name'] ?? $user['first_name'] ?? 'User') ?>!</h4>
                            <p class="text-muted mb-0">Manage your professional profile and connect with colleagues</p>
                        </div>
                        <div class="col-auto">
                            <?php if (!empty($profile['profile_photo'])): ?>
                                <img src="<?= asset('uploads/' . $profile['profile_photo']) ?>" 
                                     alt="Profile Photo" 
                                     class="rounded-circle" 
                                     width="50" height="50"
                                     style="object-fit: cover; border: 2px solid #e9ecef;"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px; display: none; font-weight: bold;">
                                    <?php 
                                    $firstName = $profile['first_name'] ?? $user['first_name'] ?? 'U';
                                    $lastName = $profile['last_name'] ?? $user['last_name'] ?? 'U';
                                    echo strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
                                    ?>
                                </div>
                            <?php else: ?>
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px; font-weight: bold;">
                                    <?php 
                                    $firstName = $profile['first_name'] ?? $user['first_name'] ?? 'U';
                                    $lastName = $profile['last_name'] ?? $user['last_name'] ?? 'U';
                                    echo strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card h-100">
                                <div class="card-body text-center">
                                    <div class="d-flex justify-content-center mb-3">
                                        <div class="progress-circle d-flex align-items-center justify-content-center bg-primary text-white rounded-circle">
                                            <span class="fw-bold"><?= $profile_stats['completion'] ?>%</span>
                                        </div>
                                    </div>
                                    <h6 class="card-title">Profile Completion</h6>
                                    <p class="text-muted small mb-0">
                                        <?php if ($profile_stats['completion'] < 50): ?>
                                            Complete your profile to increase visibility
                                        <?php elseif ($profile_stats['completion'] < 80): ?>
                                            Good progress! Add more details
                                        <?php else: ?>
                                            Excellent! Your profile looks great
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-eye fa-3x text-info mb-3"></i>
                                    <h3 class="fw-bold text-info"><?= number_format($profile_views) ?></h3>
                                    <h6 class="card-title">Profile Views</h6>
                                    <p class="text-muted small mb-0">People who viewed your profile</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h3 class="fw-bold text-success"><?= array_sum($profile_stats['sections']) ?></h3>
                                    <h6 class="card-title">Completed Sections</h6>
                                    <p class="text-muted small mb-0">Out of <?= count($profile_stats['sections']) ?> sections</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar fa-3x text-warning mb-3"></i>
                                    <h3 class="fw-bold text-warning"><?= date('M d') ?></h3>
                                    <h6 class="card-title">Today</h6>
                                    <p class="text-muted small mb-0">Last login: <?= $user['last_login'] ? date('M d, Y', strtotime($user['last_login'])) : 'Never' ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Profile Completion Guide -->
                        <div class="col-lg-8 mb-4">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-tasks me-2"></i>Complete Your Profile
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($profile_stats['completion'] >= 100): ?>
                                        <div class="alert alert-success">
                                            <i class="fas fa-trophy me-2"></i>
                                            Congratulations! Your profile is complete and ready to showcase your expertise.
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted mb-3">Complete these sections to improve your profile visibility:</p>
                                        
                                        <div class="list-group list-group-flush">
                                            <?php if (!$profile_stats['sections']['basic_info']): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-user text-primary me-2"></i>
                                                    <strong>Basic Information</strong>
                                                    <small class="d-block text-muted">Add your name, title, and department</small>
                                                </div>
                                                <a href="<?= url('profile/edit') ?>" class="btn btn-sm btn-outline-primary">Complete</a>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!$profile_stats['sections']['photo']): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-camera text-primary me-2"></i>
                                                    <strong>Profile Photo</strong>
                                                    <small class="d-block text-muted">Upload a professional photo</small>
                                                </div>
                                                <a href="<?= url('profile/edit') ?>" class="btn btn-sm btn-outline-primary">Add Photo</a>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!$profile_stats['sections']['summary']): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-align-left text-primary me-2"></i>
                                                    <strong>Professional Summary</strong>
                                                    <small class="d-block text-muted">Describe your expertise and interests</small>
                                                </div>
                                                <a href="<?= url('profile/edit') ?>" class="btn btn-sm btn-outline-primary">Add Summary</a>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!$profile_stats['sections']['education']): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-graduation-cap text-primary me-2"></i>
                                                    <strong>Education</strong>
                                                    <small class="d-block text-muted">Add your academic qualifications</small>
                                                </div>
                                                <a href="<?= url('profile/education') ?>" class="btn btn-sm btn-outline-primary">Add Education</a>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!$profile_stats['sections']['experience']): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-briefcase text-primary me-2"></i>
                                                    <strong>Experience</strong>
                                                    <small class="d-block text-muted">Add your work experience</small>
                                                </div>
                                                <a href="<?= url('profile/experience') ?>" class="btn btn-sm btn-outline-primary">Add Experience</a>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!$profile_stats['sections']['skills']): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-cogs text-primary me-2"></i>
                                                    <strong>Skills</strong>
                                                    <small class="d-block text-muted">List your key skills and expertise</small>
                                                </div>
                                                <a href="<?= url('profile/skills') ?>" class="btn btn-sm btn-outline-primary">Add Skills</a>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bolt me-2"></i>Quick Actions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <?php if (!empty($user['profile_slug'])): ?>
                                        <a href="<?= url('profile/' . htmlspecialchars($user['profile_slug'])) ?>" 
                                           class="btn btn-outline-primary" target="_blank">
                                            <i class="fas fa-external-link-alt me-2"></i>View Public Profile
                                        </a>
                                        <?php endif; ?>
                                        
                                        <a href="<?= url('profile/edit') ?>" class="btn btn-outline-success">
                                            <i class="fas fa-edit me-2"></i>Edit Profile
                                        </a>
                                        
                                        <a href="<?= url('profile/publications') ?>" class="btn btn-outline-info">
                                            <i class="fas fa-plus me-2"></i>Add Publication
                                        </a>
                                        
                                        <a href="<?= url('directory') ?>" class="btn btn-outline-secondary">
                                            <i class="fas fa-search me-2"></i>Browse Directory
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Activity -->
                            <?php if (!empty($recent_activity)): ?>
                            <div class="card mt-3">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-history me-2"></i>Recent Activity
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <?php foreach (array_slice($recent_activity, 0, 5) as $activity): ?>
                                    <div class="activity-item <?= (strtotime($activity['created_at']) > strtotime('-1 day')) ? 'recent' : '' ?>">
                                        <small class="text-muted d-block"><?= date('M d, Y H:i', strtotime($activity['created_at'])) ?></small>
                                        <span class="fw-medium"><?= ucwords(str_replace('_', ' ', $activity['action'])) ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
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
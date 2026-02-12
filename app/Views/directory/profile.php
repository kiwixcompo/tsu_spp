<?php
// Load helpers if not already loaded
if (!function_exists('url')) {
    require_once __DIR__ . '/../../Helpers/UrlHelper.php';
}
if (!function_exists('safe_output')) {
    require_once __DIR__ . '/../../Helpers/TextHelper.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(trim(($profile['title'] ?? '') . ' ' . $profile['first_name'] . ' ' . ($profile['middle_name'] ? $profile['middle_name'] . ' ' : '') . $profile['last_name'])) ?> - TSU Staff Profile Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
        }
        .profile-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 4px solid white;
        }
        .section-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .skill-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            margin: 2px;
            display: inline-block;
        }
        .publication-type-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= url() ?>">
                <i class="fas fa-university me-2"></i>TSU Staff Profile Portal
            </a>
            <div class="navbar-nav ms-auto">
                <?php
                // Check if user is logged in and determine back URL
                $backUrl = url('directory');
                if (isset($_SESSION['user'])) {
                    $userRole = $_SESSION['user']['role'] ?? 'user';
                    if ($userRole === 'id_card_manager') {
                        $backUrl = url('id-card-manager/dashboard');
                    } elseif ($userRole === 'admin') {
                        $backUrl = url('admin/dashboard');
                    } elseif ($userRole === 'user') {
                        $backUrl = url('dashboard');
                    }
                }
                ?>
                <a class="nav-link" href="<?= $backUrl ?>">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
                <?php if (!isset($_SESSION['user'])): ?>
                <a class="nav-link" href="<?= url('login') ?>">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Profile Header -->
    <section class="profile-header py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3 text-center mb-4 mb-md-0">
                    <?php if (!empty($profile['profile_photo'])): ?>
                        <img src="<?= url($profile['profile_photo']) ?>" 
                             alt="<?= safe_output($profile['first_name'] . ' ' . $profile['last_name']) ?>" 
                             class="rounded-circle profile-photo">
                    <?php endif; ?>
                </div>
                <div class="col-md-9">
                    <h1 class="display-5 fw-bold mb-2">
                        <?= htmlspecialchars(trim(($profile['title'] ?? '') . ' ' . $profile['first_name'] . ' ' . ($profile['middle_name'] ? $profile['middle_name'] . ' ' : '') . $profile['last_name'])) ?>
                    </h1>
                    <h4 class="mb-3"><?= htmlspecialchars($profile['designation'] ?? 'Staff Member') ?></h4>
                    <p class="lead mb-3">
                        <i class="fas fa-building me-2"></i>
                        <?= htmlspecialchars($profile['department'] ?? 'N/A') ?>, 
                        <?= htmlspecialchars($profile['faculty'] ?? 'N/A') ?>
                    </p>
                    <?php if (!empty($profile['office_location'])): ?>
                        <p class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Office: <?= htmlspecialchars($profile['office_location']) ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($profile['office_phone'])): ?>
                        <p class="mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <?= htmlspecialchars($profile['office_phone']) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Profile Content -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Professional Summary -->
                    <?php if (!empty($profile['professional_summary'])): ?>
                        <div class="section-card">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-user-tie me-2"></i>Professional Summary
                                </h5>
                                <p class="card-text"><?= nl2br(htmlspecialchars($profile['professional_summary'])) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Research Interests -->
                    <?php if (!empty($profile['research_interests'])): ?>
                        <div class="section-card">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-microscope me-2"></i>Research Interests
                                </h5>
                                <p class="card-text"><?= nl2br(htmlspecialchars($profile['research_interests'])) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Publications -->
                    <?php if (!empty($publications)): ?>
                        <div class="section-card">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-book me-2"></i>Publications
                                </h5>
                                <?php foreach ($publications as $pub): ?>
                                    <div class="mb-4 pb-3 <?= $pub !== end($publications) ? 'border-bottom' : '' ?>">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-primary publication-type-badge">
                                                <?= ucfirst($pub['publication_type']) ?>
                                            </span>
                                            <?php if ($pub['year']): ?>
                                                <span class="badge bg-secondary publication-type-badge">
                                                    <?= $pub['year'] ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <h6 class="fw-bold mb-2"><?= htmlspecialchars($pub['title']) ?></h6>
                                        
                                        <?php if (!empty($pub['authors'])): ?>
                                            <p class="text-muted small mb-2">
                                                <i class="fas fa-users me-1"></i>
                                                <?= htmlspecialchars($pub['authors']) ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($pub['journal_conference_name'])): ?>
                                            <p class="text-primary small mb-2">
                                                <i class="fas fa-journal-whills me-1"></i>
                                                <?= htmlspecialchars($pub['journal_conference_name']) ?>
                                                <?php if ($pub['volume'] || $pub['issue'] || $pub['pages']): ?>
                                                    <span class="text-muted">
                                                        <?php if ($pub['volume']): ?>Vol. <?= htmlspecialchars($pub['volume']) ?><?php endif; ?>
                                                        <?php if ($pub['issue']): ?>(<?= htmlspecialchars($pub['issue']) ?>)<?php endif; ?>
                                                        <?php if ($pub['pages']): ?>, pp. <?= htmlspecialchars($pub['pages']) ?><?php endif; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($pub['publisher'])): ?>
                                            <p class="text-muted small mb-2">
                                                <i class="fas fa-building me-1"></i>
                                                <?= htmlspecialchars($pub['publisher']) ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($pub['abstract'])): ?>
                                            <p class="small text-muted mb-2">
                                                <?= htmlspecialchars(substr($pub['abstract'], 0, 200)) ?>
                                                <?php if (strlen($pub['abstract']) > 200): ?>...<?php endif; ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <?php if (!empty($pub['doi'])): ?>
                                                    <a href="https://doi.org/<?= htmlspecialchars($pub['doi']) ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary me-2">
                                                        <i class="fas fa-external-link-alt me-1"></i>DOI
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($pub['url'])): ?>
                                                    <a href="<?= htmlspecialchars($pub['url']) ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-link me-1"></i>Link
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <?php if ($pub['citation_count'] > 0): ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-quote-right me-1"></i>
                                                    <?= $pub['citation_count'] ?> citations
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Experience -->
                    <?php if (!empty($experience)): ?>
                        <div class="section-card">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-briefcase me-2"></i>Experience
                                </h5>
                                <?php foreach ($experience as $exp): ?>
                                    <div class="mb-3 pb-3 <?= $exp !== end($experience) ? 'border-bottom' : '' ?>">
                                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($exp['job_title']) ?></h6>
                                        <p class="text-primary mb-1"><?= htmlspecialchars($exp['organization']) ?></p>
                                        <p class="text-muted small mb-2">
                                            <?= date('M Y', strtotime($exp['start_date'])) ?> - 
                                            <?= $exp['end_date'] ? date('M Y', strtotime($exp['end_date'])) : 'Present' ?>
                                            <?php if (!empty($exp['location'])): ?>
                                                | <?= htmlspecialchars($exp['location']) ?>
                                            <?php endif; ?>
                                        </p>
                                        <?php if (!empty($exp['description'])): ?>
                                            <p class="mb-0"><?= nl2br(htmlspecialchars($exp['description'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Education -->
                    <?php if (!empty($education)): ?>
                        <div class="section-card">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-graduation-cap me-2"></i>Education
                                </h5>
                                <?php foreach ($education as $edu): ?>
                                    <div class="mb-3 pb-3 <?= $edu !== end($education) ? 'border-bottom' : '' ?>">
                                        <h6 class="fw-bold mb-1">
                                            <?= htmlspecialchars($edu['degree_type']) ?> in <?= htmlspecialchars($edu['field_of_study']) ?>
                                        </h6>
                                        <p class="text-primary mb-1"><?= htmlspecialchars($edu['institution']) ?></p>
                                        <?php if (!empty($edu['display_years']) && $edu['display_years'] == 1): ?>
                                            <p class="text-muted small mb-2">
                                                <?= $edu['start_year'] ?> - <?= $edu['end_year'] ?: 'Present' ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if (!empty($edu['description'])): ?>
                                            <p class="mb-0"><?= nl2br(htmlspecialchars($edu['description'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <!-- Skills -->
                    <?php if (!empty($skills)): ?>
                        <div class="section-card">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-cogs me-2"></i>Skills & Expertise
                                </h5>
                                <?php foreach ($skills as $skill): ?>
                                    <span class="skill-badge">
                                        <?= htmlspecialchars($skill['skill_name']) ?>
                                        <small>(<?= ucfirst($skill['proficiency_level']) ?>)</small>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Publication Summary -->
                    <?php if (!empty($publications)): ?>
                        <div class="section-card">
                            <div class="card-body p-4">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-chart-bar me-2"></i>Research Output
                                </h5>
                                <div class="text-center">
                                    <h3 class="text-primary mb-1"><?= count($publications) ?></h3>
                                    <p class="text-muted mb-2">Published Works</p>
                                    
                                    <?php 
                                    $totalCitations = array_sum(array_column($publications, 'citation_count'));
                                    if ($totalCitations > 0): 
                                    ?>
                                        <h4 class="text-success mb-1"><?= $totalCitations ?></h4>
                                        <p class="text-muted small">Total Citations</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Contact -->
                    <div class="section-card">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-envelope me-2"></i>Contact
                            </h5>
                            <p class="mb-2">
                                <i class="fas fa-envelope me-2"></i>
                                <a href="mailto:<?= htmlspecialchars($profile['email']) ?>">
                                    <?= htmlspecialchars($profile['email']) ?>
                                </a>
                            </p>
                            <?php if (!empty($profile['office_phone'])): ?>
                                <p class="mb-2">
                                    <i class="fas fa-phone me-2"></i>
                                    <?= htmlspecialchars($profile['office_phone']) ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($profile['office_location'])): ?>
                                <p class="mb-2">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <?= htmlspecialchars($profile['office_location']) ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($profile['cv_file'])): ?>
                                <div class="mt-3">
                                    <a href="<?= url('uploads/' . $profile['cv_file']) ?>" 
                                       target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-download me-2"></i>Download CV
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
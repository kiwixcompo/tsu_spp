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
    <title>Staff Directory - TSU Staff Profile Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/images/tsu-logo.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .profile-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .profile-photo {
            width: 80px;
            height: 80px;
            object-fit: cover;
            /* Performance fix */
            content-visibility: auto;
        }
        .profile-initials {
            width: 80px;
            height: 80px;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .search-section {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?= url() ?>">
                <img src="<?= url('assets/images/tsu-logo.png') ?>" alt="TSU Logo" height="40" class="me-2">
                TSU Staff Profile Portal
            </a>
            <div class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="<?= url('dashboard') ?>">
                        <i class="fas fa-tachometer-alt me-1"></i>My Dashboard
                    </a>
                    <a class="nav-link" href="<?= url('logout') ?>">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                <?php else: ?>
                    <a class="nav-link" href="<?= url('login') ?>">
                        <i class="fas fa-sign-in-alt me-1"></i>Login
                    </a>
                    <a class="nav-link" href="<?= url('register') ?>">
                        <i class="fas fa-user-plus me-1"></i>Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="search-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-5 fw-bold mb-3">Staff Directory</h1>
                    <p class="lead mb-4">Discover and connect with TSU faculty and staff members</p>
                    
                    <form method="GET" class="row g-3">
                        <div class="col-md-12 mb-3">
                            <input type="text" class="form-control form-control-lg" 
                                   name="search" placeholder="Search by name, expertise, research interests, or keywords..." 
                                   value="<?= htmlspecialchars($search ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="faculty" id="facultyFilter">
                                <option value="">All Faculties</option>
                                <?php foreach ($faculties as $fac): ?>
                                    <option value="<?= htmlspecialchars($fac['faculty']) ?>" 
                                            <?= ($faculty ?? '') === $fac['faculty'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($fac['faculty']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="department" id="departmentFilter">
                                <option value="">All Departments</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= htmlspecialchars($dept['department']) ?>" 
                                            <?= ($department ?? '') === $dept['department'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dept['department']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="designation">
                                <option value="">All Positions</option>
                                <?php foreach ($designations as $desig): ?>
                                    <option value="<?= htmlspecialchars($desig['designation']) ?>" 
                                            <?= ($designation ?? '') === $desig['designation'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($desig['designation']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="sort">
                                <option value="name" <?= ($sort ?? 'name') === 'name' ? 'selected' : '' ?>>Sort by Name</option>
                                <option value="faculty" <?= ($sort ?? '') === 'faculty' ? 'selected' : '' ?>>Sort by Faculty</option>
                                <option value="department" <?= ($sort ?? '') === 'department' ? 'selected' : '' ?>>Sort by Department</option>
                                <option value="designation" <?= ($sort ?? '') === 'designation' ? 'selected' : '' ?>>Sort by Position</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-light w-100">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </div>
                    </form>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <a href="<?= url('directory') ?>" class="btn btn-outline-light btn-sm">
                                    <i class="fas fa-users me-1"></i>All Staff
                                </a>
                                <a href="<?= url('directory?designation=Professor') ?>" class="btn btn-outline-light btn-sm">
                                    <i class="fas fa-graduation-cap me-1"></i>Professors
                                </a>
                                <a href="<?= url('directory?designation=Senior Lecturer') ?>" class="btn btn-outline-light btn-sm">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>Senior Lecturers
                                </a>
                                <a href="<?= url('directory?designation=Lecturer') ?>" class="btn btn-outline-light btn-sm">
                                    <i class="fas fa-user-tie me-1"></i>Lecturers
                                </a>
                                <a href="<?= url('directory?designation=Research Fellow') ?>" class="btn btn-outline-light btn-sm">
                                    <i class="fas fa-microscope me-1"></i>Researchers
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col">
                    <h3>
                        <?php if ($totalProfiles > 0): ?>
                            <?= $totalProfiles ?> Staff Member<?= $totalProfiles !== 1 ? 's' : '' ?> Found
                        <?php else: ?>
                            No Staff Members Found
                        <?php endif; ?>
                    </h3>
                    <?php if (!empty($search) || !empty($faculty) || !empty($department) || !empty($designation)): ?>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="text-muted">Active filters:</span>
                            <?php if (!empty($search)): ?>
                                <span class="badge bg-primary">Search: "<?= htmlspecialchars($search) ?>"</span>
                            <?php endif; ?>
                            <?php if (!empty($faculty)): ?>
                                <span class="badge bg-info">Faculty: <?= htmlspecialchars($faculty) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($department)): ?>
                                <span class="badge bg-success">Department: <?= htmlspecialchars($department) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($designation)): ?>
                                <span class="badge bg-warning">Position: <?= htmlspecialchars($designation) ?></span>
                            <?php endif; ?>
                            <a href="<?= url('directory') ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times me-1"></i>Clear All
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($profiles)): ?>
                <div class="row">
                    <?php foreach ($profiles as $profile): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card profile-card h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="me-3">
                                            <?php if (!empty($profile['profile_photo'])): ?>
                                                <?php
                                                    $photoPath = $profile['profile_photo'];
                                                    if (strpos($photoPath, 'uploads/') === 0 || strpos($photoPath, '/uploads/') === 0) {
                                                        $photoUrl = url($photoPath);
                                                    } else {
                                                        $photoUrl = asset('uploads/profiles/' . ltrim($photoPath, '/'));
                                                    }
                                                    $initials = strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1));
                                                ?>
                                                <img src="<?= $photoUrl ?>" 
                                                     alt="<?= safe_output($profile['first_name'] . ' ' . $profile['last_name']) ?>" 
                                                     class="rounded-circle profile-photo"
                                                     loading="lazy"
                                                     decoding="async"
                                                     width="80"
                                                     height="80"
                                                     onerror="this.replaceWith(createInitials('<?= $initials ?>'))">
                                            <?php else: ?>
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center profile-initials">
                                                    <?= strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="card-title mb-1">
                                                <?= htmlspecialchars(($profile['title'] ?? '') . ' ' . $profile['first_name'] . ' ' . $profile['last_name']) ?>
                                            </h5>
                                            <p class="text-muted mb-2"><?= htmlspecialchars($profile['designation'] ?? 'Staff Member') ?></p>
                                            <p class="text-muted small mb-0">
                                                <i class="fas fa-building me-1"></i>
                                                <?= htmlspecialchars($profile['department'] ?? 'N/A') ?>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($profile['professional_summary'])): ?>
                                        <p class="card-text text-muted small mb-3">
                                            <?= htmlspecialchars(substr($profile['professional_summary'], 0, 120)) ?>
                                            <?= strlen($profile['professional_summary']) > 120 ? '...' : '' ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($profile['research_interests'])): ?>
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-1">Research Interests:</small>
                                            <small class="text-primary">
                                                <?= htmlspecialchars(substr($profile['research_interests'], 0, 100)) ?>
                                                <?= strlen($profile['research_interests']) > 100 ? '...' : '' ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-end align-items-center">
                                        <a href="<?= url('profile/' . $profile['profile_slug']) ?>" 
                                           class="btn btn-primary btn-sm">
                                            View Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Directory pagination" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= url('directory?' . http_build_query(array_merge($_GET, ['page' => $currentPage - 1]))) ?>">
                                        Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= url('directory?' . http_build_query(array_merge($_GET, ['page' => $i]))) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= url('directory?' . http_build_query(array_merge($_GET, ['page' => $currentPage + 1]))) ?>">
                                        Next
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Staff Members Found</h4>
                    <p class="text-muted">Try adjusting your search criteria or browse all profiles.</p>
                    <a href="<?= url('directory') ?>" class="btn btn-primary">View All Profiles</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function createInitials(initials) {
            const div = document.createElement('div');
            div.className = 'bg-primary text-white rounded-circle d-flex align-items-center justify-content-center profile-initials';
            div.textContent = initials;
            return div;
        }
        // Faculty and Department data for dynamic filtering
        const facultiesData = <?= json_encode($faculties) ?>;
        
        // Populate departments based on selected faculty
        function populateDepartments(selectedFaculty, selectedDepartment = '') {
            const departmentSelect = document.getElementById('departmentFilter');
            const currentValue = departmentSelect.value;
            
            // Clear existing options except "All Departments"
            departmentSelect.innerHTML = '<option value="">All Departments</option>';
            
            if (selectedFaculty) {
                const faculty = facultiesData.find(f => f.faculty === selectedFaculty);
                if (faculty && faculty.departments) {
                    faculty.departments.forEach(dept => {
                        const option = document.createElement('option');
                        option.value = dept;
                        option.textContent = dept;
                        if (dept === selectedDepartment || dept === currentValue) {
                            option.selected = true;
                        }
                        departmentSelect.appendChild(option);
                    });
                }
            } else {
                // Show all departments if no faculty selected
                <?php foreach ($departments as $dept): ?>
                    const option<?= $dept['id'] ?? rand() ?> = document.createElement('option');
                    option<?= $dept['id'] ?? rand() ?>.value = '<?= htmlspecialchars($dept['department']) ?>';
                    option<?= $dept['id'] ?? rand() ?>.textContent = '<?= htmlspecialchars($dept['department']) ?>';
                    if ('<?= htmlspecialchars($dept['department']) ?>' === selectedDepartment || '<?= htmlspecialchars($dept['department']) ?>' === currentValue) {
                        option<?= $dept['id'] ?? rand() ?>.selected = true;
                    }
                    departmentSelect.appendChild(option<?= $dept['id'] ?? rand() ?>);
                <?php endforeach; ?>
            }
        }

        // Handle faculty change
        document.getElementById('facultyFilter').addEventListener('change', function() {
            populateDepartments(this.value);
        });

        // Initialize departments on page load
        document.addEventListener('DOMContentLoaded', function() {
            const selectedFaculty = document.getElementById('facultyFilter').value;
            const selectedDepartment = '<?= htmlspecialchars($department ?? '') ?>';
            
            if (selectedFaculty) {
                populateDepartments(selectedFaculty, selectedDepartment);
            }
        });
    </script>
</body>
</html>
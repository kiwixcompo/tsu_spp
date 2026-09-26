<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Profile;
use App\Models\User;

class DirectoryController extends Controller
{
    private $profileModel;
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        try {
            $this->profileModel = new Profile();
            $this->userModel = new User();
        } catch (\Exception $e) {
            $this->profileModel = null;
            $this->userModel = null;
        }
    }

    public function index(): void
    {
        $search = $this->input('search') ?? '';
        $faculty = $this->input('faculty') ?? '';
        $department = $this->input('department') ?? '';
        $designation = $this->input('designation') ?? '';
        $sort = $this->input('sort') ?? 'name';
        $page = max(1, (int)($this->input('page') ?? 1));
        $perPage = 12;

        $profiles = [];
        $totalProfiles = 0;
        $faculties = [];
        $departments = [];
        $designations = [];

        if ($this->db) {
            try {
                // Build search query
                $whereConditions = ["u.account_status = 'active'", "p.profile_visibility = 'public'", "u.role != 'admin'"];
                $params = [];

                if (!empty($search)) {
                    $whereConditions[] = "(p.first_name LIKE ? OR p.last_name LIKE ? OR p.professional_summary LIKE ? OR p.research_interests LIKE ? OR p.expertise_keywords LIKE ?)";
                    $searchTerm = "%{$search}%";
                    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
                }

                if (!empty($faculty)) {
                    $whereConditions[] = "p.faculty = ?";
                    $params[] = $faculty;
                }

                if (!empty($department)) {
                    $whereConditions[] = "p.department = ?";
                    $params[] = $department;
                }

                if (!empty($designation)) {
                    $whereConditions[] = "p.designation = ?";
                    $params[] = $designation;
                }

                // Build ORDER BY clause
                $orderBy = "p.first_name, p.last_name";
                switch ($sort) {
                    case 'faculty':
                        $orderBy = "p.faculty, p.first_name, p.last_name";
                        break;
                    case 'department':
                        $orderBy = "p.department, p.first_name, p.last_name";
                        break;
                    case 'designation':
                        $orderBy = "p.designation, p.first_name, p.last_name";
                        break;
                    default:
                        $orderBy = "p.first_name, p.last_name";
                }

                $whereClause = implode(' AND ', $whereConditions);
                $offset = ($page - 1) * $perPage;

                // Get profiles
                $profiles = $this->db->fetchAll(
                    "SELECT p.*, u.email 
                     FROM profiles p 
                     JOIN users u ON p.user_id = u.id 
                     WHERE {$whereClause} 
                     ORDER BY {$orderBy}
                     LIMIT {$perPage} OFFSET {$offset}",
                    $params
                );

                // Get total count
                $totalResult = $this->db->fetch(
                    "SELECT COUNT(*) as total 
                     FROM profiles p 
                     JOIN users u ON p.user_id = u.id 
                     WHERE {$whereClause}",
                    $params
                );
                $totalProfiles = $totalResult['total'] ?? 0;

                // Get filter options
                $faculties = $this->db->fetchAll(
                    "SELECT DISTINCT faculty 
                     FROM profiles p
                     JOIN users u ON p.user_id = u.id
                     WHERE u.account_status = 'active' AND u.role != 'admin' AND faculty IS NOT NULL AND faculty != '' 
                     ORDER BY faculty"
                );

                $departments = $this->db->fetchAll(
                    "SELECT DISTINCT department 
                     FROM profiles p
                     JOIN users u ON p.user_id = u.id
                     WHERE u.account_status = 'active' AND u.role != 'admin' AND department IS NOT NULL AND department != '' 
                     ORDER BY department"
                );

                $designations = $this->db->fetchAll(
                    "SELECT DISTINCT designation 
                     FROM profiles p
                     JOIN users u ON p.user_id = u.id
                     WHERE u.account_status = 'active' AND u.role != 'admin' AND designation IS NOT NULL AND designation != '' 
                     ORDER BY designation"
                );

                // Get faculties with departments for dynamic filtering
                $facultiesWithDepts = $this->db->fetchAll(
                    "SELECT DISTINCT faculty, department 
                     FROM profiles p
                     JOIN users u ON p.user_id = u.id
                     WHERE u.account_status = 'active' AND u.role != 'admin' AND faculty IS NOT NULL AND department IS NOT NULL
                     ORDER BY faculty, department"
                );

                // Group departments by faculty
                $groupedFaculties = [];
                foreach ($facultiesWithDepts as $row) {
                    $facultyName = $row['faculty'];
                    if (!isset($groupedFaculties[$facultyName])) {
                        $groupedFaculties[$facultyName] = [
                            'faculty' => $facultyName,
                            'departments' => []
                        ];
                    }
                    $groupedFaculties[$facultyName]['departments'][] = $row['department'];
                }
                $faculties = array_values($groupedFaculties);

            } catch (\Exception $e) {
                error_log("Directory error: " . $e->getMessage());
            }
        }

        $totalPages = ceil($totalProfiles / $perPage);

        $this->view('directory/index', [
            'profiles' => $profiles,
            'search' => $search,
            'faculty' => $faculty,
            'department' => $department,
            'designation' => $designation,
            'sort' => $sort,
            'faculties' => $faculties,
            'departments' => $departments,
            'designations' => $designations,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProfiles' => $totalProfiles,
        ]);
    }

    public function show(string $slug): void
    {
        if (!$this->profileModel || !$this->db) {
            $this->view('errors/404');
            return;
        }

        try {
            // First, check if profile exists (regardless of visibility)
            $profile = $this->db->fetch(
                "SELECT p.*, u.email, u.created_at as user_created_at 
                 FROM profiles p 
                 JOIN users u ON p.user_id = u.id 
                 WHERE p.profile_slug = ? AND u.role != 'admin'",
                [$slug]
            );

            if (!$profile) {
                $this->view('errors/404');
                return;
            }

            // Check if profile is private
            if ($profile['profile_visibility'] !== 'public') {
                // Check if viewing own profile
                $currentUser = $this->getCurrentUser();
                $isOwnProfile = $currentUser && $currentUser['id'] === $profile['user_id'];
                
                if (!$isOwnProfile) {
                    // Show private profile message
                    $this->view('directory/private-profile', [
                        'profile' => $profile
                    ]);
                    return;
                }
            }

            // Increment profile views (only if not viewing own profile)
            $currentUser = $this->getCurrentUser();
            $isOwnProfile = $currentUser && $currentUser['id'] === $profile['user_id'];
            
            if (!$isOwnProfile) {
                $this->db->query(
                    "UPDATE profiles SET profile_views = profile_views + 1 WHERE id = ?",
                    [$profile['id']]
                );
            }

            // Get additional profile data
            $education = $this->db->fetchAll(
                "SELECT * FROM education WHERE user_id = ? ORDER BY end_year DESC, start_year DESC",
                [$profile['user_id']]
            );

            $experience = $this->db->fetchAll(
                "SELECT * FROM experience WHERE user_id = ? ORDER BY end_date DESC, start_date DESC",
                [$profile['user_id']]
            );

            $skills = $this->db->fetchAll(
                "SELECT * FROM skills WHERE user_id = ? ORDER BY proficiency_level DESC, skill_name ASC",
                [$profile['user_id']]
            );

            $publications = $this->db->fetchAll(
                "SELECT * FROM publications WHERE user_id = ? ORDER BY publication_year DESC, title ASC LIMIT 10",
                [$profile['user_id']]
            );

            $this->view('directory/profile', [
                'profile' => $profile,
                'education' => $education,
                'experience' => $experience,
                'skills' => $skills,
                'publications' => $publications,
            ]);

        } catch (\Exception $e) {
            error_log("Profile view error: " . $e->getMessage());
            $this->view('errors/500');
        }
    }

    /**
     * Public JSON API for TSU Main Website Integration
     * Endpoint: /api/staff-directory or /public/api/staff-directory
     */
    public function apiDirectory(): void
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        try {
            $search = trim($_GET['search'] ?? '');
            $faculty = trim($_GET['faculty'] ?? '');
            $department = trim($_GET['department'] ?? '');
            $staffType = trim($_GET['staff_type'] ?? '');
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = min(200, max(1, (int)($_GET['limit'] ?? 100)));
            $offset = ($page - 1) * $limit;

            $where = [
                "u.account_status = 'active'",
                "p.profile_visibility = 'public'",
                "u.role != 'admin'"
            ];
            $params = [];

            if (!empty($search)) {
                $where[] = "(p.first_name LIKE ? OR p.last_name LIKE ? OR p.professional_summary LIKE ? OR p.research_interests LIKE ? OR p.expertise_keywords LIKE ?)";
                $term = "%{$search}%";
                $params = array_merge($params, [$term, $term, $term, $term, $term]);
            }

            if (!empty($faculty)) {
                $where[] = "p.faculty = ?";
                $params[] = $faculty;
            }

            if (!empty($department)) {
                $where[] = "p.department = ?";
                $params[] = $department;
            }

            if (!empty($staffType)) {
                if ($staffType === 'teaching' || $staffType === 'academic') {
                    $where[] = "(p.staff_type = 'teaching' OR p.staff_type = 'academic')";
                } else if ($staffType === 'non-teaching' || $staffType === 'non-academic') {
                    $where[] = "(p.staff_type = 'non-teaching' OR p.staff_type = 'non-academic')";
                }
            }

            $whereSql = implode(' AND ', $where);

            // Fetch profiles
            $sql = "SELECT p.*, u.email
                    FROM profiles p
                    JOIN users u ON p.user_id = u.id
                    WHERE {$whereSql}
                    ORDER BY p.first_name ASC, p.last_name ASC
                    LIMIT ? OFFSET ?";
            $queryParams = array_merge($params, [$limit, $offset]);

            $profiles = $this->db ? $this->db->fetchAll($sql, $queryParams) : [];

            // Get total count
            $countSql = "SELECT COUNT(*) as total FROM profiles p JOIN users u ON p.user_id = u.id WHERE {$whereSql}";
            $totalRow = $this->db ? $this->db->fetch($countSql, $params) : null;
            $total = (int)($totalRow['total'] ?? count($profiles));

            // Format profiles for TSU website consumption
            $baseUrl = 'https://staff.tsuniversity.ng/public';
            $formatted = [];
            foreach ($profiles as $p) {
                $fullName = trim(($p['title'] ? $p['title'] . ' ' : '') . $p['first_name'] . ' ' . $p['last_name']);
                
                $photoUrl = null;
                if (!empty($p['profile_photo'])) {
                    if (strpos($p['profile_photo'], 'http') === 0) {
                        $photoUrl = $p['profile_photo'];
                    } else {
                        $photoUrl = $baseUrl . '/' . ltrim($p['profile_photo'], '/');
                    }
                }

                $normType = (in_array(strtolower($p['staff_type'] ?? ''), ['teaching', 'academic'])) ? 'teaching' : 'non-teaching';

                $formatted[] = [
                    'id' => (string)$p['id'],
                    'user_id' => (string)$p['user_id'],
                    'slug' => $p['profile_slug'],
                    'name' => $fullName,
                    'first_name' => $p['first_name'],
                    'last_name' => $p['last_name'],
                    'title' => $p['designation'] ?: ($p['title'] ?: 'Staff Member'),
                    'staff_type' => $normType,
                    'faculty' => $p['faculty'] ?: '',
                    'department' => $p['department'] ?: ($p['directorate'] ?: ''),
                    'directorate' => $p['directorate'] ?: '',
                    'unit' => $p['unit'] ?: '',
                    'email' => $p['email'] ?: '',
                    'phone' => $p['phone'] ?? ($p['phone_number'] ?? ''),
                    'photo_url' => $photoUrl,
                    'summary' => $p['professional_summary'] ?: '',
                    'research_interests' => $p['research_interests'] ?: '',
                    'expertise_keywords' => $p['expertise_keywords'] ?: '',
                    'profile_url' => "https://staff.tsuniversity.ng/public/profile/{$p['profile_slug']}"
                ];
            }

            echo json_encode([
                'success' => true,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit),
                'data' => $formatted
            ]);
        } catch (\Throwable $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Public JSON API for Individual Staff Profile
     * Endpoint: /api/staff-directory/{slug}
     */
    public function apiProfile(string $slug): void
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        try {
            $profile = $this->db ? $this->db->fetch(
                "SELECT p.*, u.email 
                 FROM profiles p 
                 JOIN users u ON p.user_id = u.id 
                 WHERE p.profile_slug = ? AND u.account_status = 'active' AND p.profile_visibility = 'public'",
                [$slug]
            ) : null;

            if (!$profile) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Staff profile not found']);
                return;
            }

            $userId = $profile['user_id'];
            $education = $this->db->fetchAll("SELECT * FROM education WHERE user_id = ? ORDER BY end_year DESC", [$userId]);
            $experience = $this->db->fetchAll("SELECT * FROM experience WHERE user_id = ? ORDER BY end_date DESC", [$userId]);
            $skills = $this->db->fetchAll("SELECT * FROM skills WHERE user_id = ? ORDER BY proficiency_level DESC", [$userId]);
            $publications = $this->db->fetchAll("SELECT * FROM publications WHERE user_id = ? ORDER BY publication_year DESC LIMIT 20", [$userId]);

            $baseUrl = 'https://staff.tsuniversity.ng/public';
            $photoUrl = null;
            if (!empty($profile['profile_photo'])) {
                $photoUrl = strpos($profile['profile_photo'], 'http') === 0 
                    ? $profile['profile_photo'] 
                    : $baseUrl . '/' . ltrim($profile['profile_photo'], '/');
            }

            $fullName = trim(($profile['title'] ? $profile['title'] . ' ' : '') . $profile['first_name'] . ' ' . $profile['last_name']);
            $normType = (in_array(strtolower($profile['staff_type'] ?? ''), ['teaching', 'academic'])) ? 'teaching' : 'non-teaching';

            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => (string)$profile['id'],
                    'slug' => $profile['profile_slug'],
                    'name' => $fullName,
                    'designation' => $profile['designation'] ?: ($profile['title'] ?: 'Staff Member'),
                    'staff_type' => $normType,
                    'faculty' => $profile['faculty'] ?: '',
                    'department' => $profile['department'] ?: '',
                    'directorate' => $profile['directorate'] ?: '',
                    'unit' => $profile['unit'] ?: '',
                    'email' => $profile['email'] ?: '',
                    'phone' => $profile['phone'] ?? ($profile['phone_number'] ?? ''),
                    'photo_url' => $photoUrl,
                    'summary' => $profile['professional_summary'] ?: '',
                    'research_interests' => $profile['research_interests'] ?: '',
                    'expertise_keywords' => $profile['expertise_keywords'] ?: '',
                    'education' => $education,
                    'experience' => $experience,
                    'skills' => $skills,
                    'publications' => $publications,
                    'profile_url' => "https://staff.tsuniversity.ng/public/profile/{$profile['profile_slug']}"
                ]
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
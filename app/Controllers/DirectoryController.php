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
            // Get profile by slug
            $profile = $this->db->fetch(
                "SELECT p.*, u.email, u.created_at as user_created_at 
                 FROM profiles p 
                 JOIN users u ON p.user_id = u.id 
                 WHERE p.profile_slug = ? AND p.profile_visibility = 'public' AND u.role != 'admin'",
                [$slug]
            );

            if (!$profile) {
                $this->view('errors/404');
                return;
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
}
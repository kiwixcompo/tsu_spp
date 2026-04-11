<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Profile;

class AdminController extends Controller
{
    private $userModel;
    private $profileModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->profileModel = new Profile();
    }

    /**
     * Admin dashboard
     */
    public function dashboard(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        // Get system statistics
        $stats = $this->getSystemStats();
        
        // Get recent users
        $recentUsers = $this->getRecentUsers(10);
        
        // Get pending verifications
        $pendingUsers = $this->getPendingUsers();

        $this->view('admin/dashboard', [
            'stats' => $stats,
            'recent_users' => $recentUsers,
            'pending_users' => $pendingUsers,
        ]);
    }

    /**
     * User management
     */
    public function users(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $page = (int)($this->input('page') ?: 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get users with pagination
        $users = $this->getUsersWithProfiles($limit, $offset);
        $totalUsers = $this->getTotalUsersCount();
        $totalPages = ceil($totalUsers / $limit);

        $this->view('admin/users', [
            'users' => $users,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_users' => $totalUsers,
        ]);
    }

    /**
     * User details
     */
    public function userDetails(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $userId = $this->input('id');
        if (!$userId) {
            $this->redirect('admin/users');
            return;
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->redirect('admin/users');
            return;
        }

        $profile = $this->profileModel->getByUserId($userId);
        $activityLogs = $this->getActivityLogs($userId, 20);

        $this->view('admin/user-details', [
            'user' => $user,
            'profile' => $profile,
            'activity_logs' => $activityLogs,
        ]);
    }

    /**
     * Activate user account
     */
    public function activateUser(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $userId = $this->input('user_id');
        if (!$userId) {
            $this->json(['error' => 'User ID required'], 400);
            return;
        }

        try {
            $result = $this->db->update('users', 
                ['account_status' => 'active', 'email_verified' => 1], 
                'id = ?', 
                [$userId]
            );

            if ($result) {
                $this->logActivity('user_activated', ['target_user_id' => $userId]);
                $this->json(['success' => true, 'message' => 'User activated successfully']);
            } else {
                $this->json(['error' => 'Failed to activate user'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => 'Activation failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Suspend user account
     */
    public function suspendUser(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $userId = $this->input('user_id');
        $reason = $this->sanitizeInput($this->input('reason'));

        if (!$userId) {
            $this->json(['error' => 'User ID required'], 400);
            return;
        }

        try {
            $result = $this->db->update('users', 
                ['account_status' => 'suspended'], 
                'id = ?', 
                [$userId]
            );

            if ($result) {
                $this->logActivity('user_suspended', [
                    'target_user_id' => $userId,
                    'reason' => $reason
                ]);
                $this->json(['success' => true, 'message' => 'User suspended successfully']);
            } else {
                $this->json(['error' => 'Failed to suspend user'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => 'Suspension failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Reinstate suspended user account
     */
    public function reinstateUser(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $userId = $this->input('user_id');

        if (!$userId) {
            $this->json(['error' => 'User ID required'], 400);
            return;
        }

        try {
            $result = $this->db->update('users', 
                ['account_status' => 'active'], 
                'id = ?', 
                [$userId]
            );

            if ($result) {
                $this->logActivity('user_reinstated', ['target_user_id' => $userId]);
                $this->json(['success' => true, 'message' => 'User reinstated successfully']);
            } else {
                $this->json(['error' => 'Failed to reinstate user'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => 'Reinstatement failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * System statistics
     */
    private function getSystemStats(): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            $stats = [];
            
            // Total users
            $stats['total_users'] = $this->db->fetch("SELECT COUNT(*) as count FROM users")['count'];
            
            // Active users
            $stats['active_users'] = $this->db->fetch("SELECT COUNT(*) as count FROM users WHERE account_status = 'active'")['count'];
            
            // Pending users
            $stats['pending_users'] = $this->db->fetch("SELECT COUNT(*) as count FROM users WHERE account_status = 'pending'")['count'];
            
            // Total profiles
            $stats['total_profiles'] = $this->db->fetch("SELECT COUNT(*) as count FROM profiles")['count'];
            
            // Complete profiles (with photo and summary)
            $stats['complete_profiles'] = $this->db->fetch("
                SELECT COUNT(*) as count FROM profiles 
                WHERE profile_photo IS NOT NULL 
                AND professional_summary IS NOT NULL 
                AND professional_summary != ''
            ")['count'];
            
            // Recent registrations (last 7 days)
            $stats['recent_registrations'] = $this->db->fetch("
                SELECT COUNT(*) as count FROM users 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ")['count'];
            
            // Total publications
            $stats['total_publications'] = $this->db->fetch("SELECT COUNT(*) as count FROM publications")['count'];

            return $stats;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get recent users
     */
    private function getRecentUsers(int $limit = 10): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            return $this->db->fetchAll("
                SELECT u.id, u.email, u.account_status, u.created_at,
                       p.first_name, p.last_name, p.faculty, p.department
                FROM users u
                LEFT JOIN profiles p ON u.id = p.user_id
                ORDER BY u.created_at DESC
                LIMIT ?
            ", [$limit]);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get pending users
     */
    private function getPendingUsers(): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            return $this->db->fetchAll("
                SELECT u.id, u.email, u.created_at, u.verification_expires,
                       p.first_name, p.last_name
                FROM users u
                LEFT JOIN profiles p ON u.id = p.user_id
                WHERE u.account_status = 'pending'
                ORDER BY u.created_at DESC
            ");
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get users with profiles
     */
    private function getUsersWithProfiles(int $limit, int $offset): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            return $this->db->fetchAll("
                SELECT u.id, u.email, u.account_status, u.email_verified, u.created_at, u.last_login, u.role,
                       p.first_name, p.last_name, p.faculty, p.department, p.unit, p.directorate,
                       p.designation, p.staff_number, p.profile_slug, p.staff_type, p.gender, p.profile_photo,
                       COALESCE(p.id_card_generated, 0) as id_card_generated,
                       p.id_card_generated_at
                FROM users u
                LEFT JOIN profiles p ON u.id = p.user_id
                ORDER BY u.created_at DESC
                LIMIT ? OFFSET ?
            ", [$limit, $offset]);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get total users count
     */
    private function getTotalUsersCount(): int
    {
        if (!$this->db) {
            return 0;
        }

        try {
            return $this->db->fetch("SELECT COUNT(*) as count FROM users")['count'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get activity logs for a user
     */
    private function getActivityLogs(int $userId, int $limit = 20): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            return $this->db->fetchAll("
                SELECT action, details, ip_address, created_at
                FROM activity_logs
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT ?
            ", [$userId, $limit]);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Analytics dashboard
     */
    public function analytics(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $analytics = [
            'user_growth' => $this->getUserGrowthData(),
            'profile_completion' => $this->getProfileCompletionData(),
            'faculty_distribution' => $this->getFacultyDistribution(),
            'publication_stats' => $this->getPublicationStats(),
            'activity_summary' => $this->getActivitySummary(),
            'top_contributors' => $this->getTopContributors(),
        ];

        $this->view('admin/analytics', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Publications management
     */
    public function publications(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $page = (int)($this->input('page') ?: 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $publications = $this->getAllPublications($limit, $offset);
        $totalPublications = $this->getTotalPublicationsCount();
        $totalPages = ceil($totalPublications / $limit);

        $this->view('admin/publications', [
            'publications' => $publications,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_publications' => $totalPublications,
        ]);
    }

    /**
     * Activity logs
     */
    public function activityLogs(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $page = (int)($this->input('page') ?: 1);
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $logs = $this->getAllActivityLogs($limit, $offset);
        $totalLogs = $this->getTotalActivityLogsCount();
        $totalPages = ceil($totalLogs / $limit);

        $this->view('admin/activity-logs', [
            'logs' => $logs,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_logs' => $totalLogs,
        ]);
    }

    /**
     * Faculties and Departments Management
     */
    public function facultiesDepartments(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $faculties = $this->getAllFacultiesWithDepartments();

        $this->view('admin/faculties-departments', [
            'faculties' => $faculties,
            'csrf_token' => $this->generateCSRFToken(),
        ]);
    }

    /**
     * Add Faculty
     */
    public function addFaculty(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $facultyName = $this->sanitizeInput($this->input('faculty_name'));

        if (empty($facultyName)) {
            $this->json(['error' => 'Faculty name is required'], 400);
            return;
        }

        try {
            // Check if faculty already exists
            $exists = $this->db->fetch(
                "SELECT id FROM faculties_departments WHERE faculty = ? LIMIT 1",
                [$facultyName]
            );

            if ($exists) {
                $this->json(['error' => 'Faculty already exists'], 400);
                return;
            }

            // Add a placeholder department for the faculty
            $this->db->insert('faculties_departments', [
                'faculty' => $facultyName,
                'department' => 'General'
            ]);

            $this->logActivity('faculty_added', ['faculty' => $facultyName]);
            $this->json(['success' => true, 'message' => 'Faculty added successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to add faculty: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Add Department
     */
    public function addDepartment(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $faculty = $this->sanitizeInput($this->input('faculty'));
        $department = $this->sanitizeInput($this->input('department'));

        if (empty($faculty) || empty($department)) {
            $this->json(['error' => 'Faculty and department are required'], 400);
            return;
        }

        try {
            // Check if department already exists in this faculty
            $exists = $this->db->fetch(
                "SELECT id FROM faculties_departments WHERE faculty = ? AND department = ?",
                [$faculty, $department]
            );

            if ($exists) {
                $this->json(['error' => 'Department already exists in this faculty'], 400);
                return;
            }

            $this->db->insert('faculties_departments', [
                'faculty' => $faculty,
                'department' => $department
            ]);

            $this->logActivity('department_added', [
                'faculty' => $faculty,
                'department' => $department
            ]);

            $this->json(['success' => true, 'message' => 'Department added successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to add department: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete Department
     */
    public function deleteDepartment(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $id = $this->input('id');

        if (!$id) {
            $this->json(['error' => 'Department ID required'], 400);
            return;
        }

        try {
            $result = $this->db->delete('faculties_departments', 'id = ?', [$id]);

            if ($result) {
                $this->logActivity('department_deleted', ['department_id' => $id]);
                $this->json(['success' => true, 'message' => 'Department deleted successfully']);
            } else {
                $this->json(['error' => 'Failed to delete department'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to delete department: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get all faculties with their departments
     */
    private function getAllFacultiesWithDepartments(): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            $data = $this->db->fetchAll("
                SELECT id, faculty, department, created_at
                FROM faculties_departments
                ORDER BY faculty, department
            ");

            $faculties = [];
            foreach ($data as $row) {
                $facultyName = $row['faculty'];
                if (!isset($faculties[$facultyName])) {
                    $faculties[$facultyName] = [
                        'name' => $facultyName,
                        'departments' => []
                    ];
                }
                $faculties[$facultyName]['departments'][] = [
                    'id' => $row['id'],
                    'name' => $row['department'],
                    'created_at' => $row['created_at']
                ];
            }

            return array_values($faculties);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * System settings
     */
    public function settings(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $settings = $this->getSystemSettings();

        $this->view('admin/settings', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update system settings
     */
    public function updateSettings(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        try {
            $inputData = json_decode(file_get_contents('php://input'), true);
            
            $settingsToUpdate = [
                'site_name' => ['value' => $this->sanitizeInput($inputData['site_name'] ?? ''), 'type' => 'string'],
                'site_description' => ['value' => $this->sanitizeInput($inputData['site_description'] ?? ''), 'type' => 'string'],
                'admin_email' => ['value' => $this->sanitizeInput($inputData['admin_email'] ?? ''), 'type' => 'string'],
                'allow_registration' => ['value' => $inputData['allow_registration'] ?? 0, 'type' => 'boolean'],
                'require_email_verification' => ['value' => $inputData['require_email_verification'] ?? 0, 'type' => 'boolean'],
                'auto_approve_users' => ['value' => $inputData['auto_approve_users'] ?? 0, 'type' => 'boolean'],
                'default_profile_visibility' => ['value' => $this->sanitizeInput($inputData['default_profile_visibility'] ?? 'public'), 'type' => 'string'],
                'require_profile_photo' => ['value' => $inputData['require_profile_photo'] ?? 0, 'type' => 'boolean'],
                'max_photo_size' => ['value' => (int)($inputData['max_photo_size'] ?? 2), 'type' => 'integer'],
                'session_timeout' => ['value' => (int)($inputData['session_timeout'] ?? 120), 'type' => 'integer'],
                'password_min_length' => ['value' => (int)($inputData['password_min_length'] ?? 8), 'type' => 'integer'],
                'enable_2fa' => ['value' => $inputData['enable_2fa'] ?? 0, 'type' => 'boolean'],
            ];

            $stmt = $this->db->prepare("
                INSERT INTO system_settings (setting_key, setting_value, setting_type)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ");

            foreach ($settingsToUpdate as $key => $data) {
                if (isset($inputData[$key])) {
                    $stmt->execute([$key, $data['value'], $data['type']]);
                }
            }

            $this->logActivity('settings_updated', ['settings' => array_keys($settingsToUpdate)]);
            
            $this->json(['success' => true, 'message' => 'Settings updated successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to update settings: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete user
     */
    public function deleteUser(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $userId = $this->input('user_id');
        if (!$userId) {
            $this->json(['error' => 'User ID required'], 400);
            return;
        }

        // Prevent admin from deleting themselves
        if ($userId == $_SESSION['user_id']) {
            $this->json(['error' => 'You cannot delete your own account'], 400);
            return;
        }

        try {
            // Get user info before deletion for logging
            $user = $this->userModel->findById($userId);

            // Begin transaction and delete related data explicitly to ensure consistency
            $this->db->beginTransaction();

            // Remove profile files if any
            $profile = $this->db->fetch("SELECT profile_photo, cv_file FROM profiles WHERE user_id = ?", [$userId]);
            if ($profile) {
                if (!empty($profile['profile_photo'])) {
                    $path = __DIR__ . '/../../storage/uploads/' . $profile['profile_photo'];
                    if (file_exists($path)) {
                        @unlink($path);
                    }
                }
                if (!empty($profile['cv_file'])) {
                    $path = __DIR__ . '/../../storage/uploads/' . $profile['cv_file'];
                    if (file_exists($path)) {
                        @unlink($path);
                    }
                }
            }

            // Delete related records (only from tables that exist)
            $this->safeDeleteFromTable('profiles', 'user_id = ?', [$userId]);
            $this->safeDeleteFromTable('publications', 'user_id = ?', [$userId]);
            $this->safeDeleteFromTable('education', 'user_id = ?', [$userId]);
            $this->safeDeleteFromTable('experience', 'user_id = ?', [$userId]);
            $this->safeDeleteFromTable('skills', 'user_id = ?', [$userId]);
            $this->safeDeleteFromTable('certifications', 'user_id = ?', [$userId]);
            $this->safeDeleteFromTable('awards', 'user_id = ?', [$userId]);
            $this->safeDeleteFromTable('memberships', 'user_id = ?', [$userId]);
            $this->safeDeleteFromTable('activity_logs', 'user_id = ?', [$userId]);

            // Finally delete the user
            $result = $this->db->delete('users', 'id = ?', [$userId]);

            if ($result) {
                $this->db->commit();
                $this->logActivity('user_deleted', [
                    'target_user_id' => $userId,
                    'email' => $user['email'] ?? 'unknown'
                ]);
                $this->json(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                $this->db->rollback();
                $this->json(['error' => 'Failed to delete user'], 500);
            }
        } catch (\Exception $e) {
            // Rollback on failure
            try { $this->db->rollback(); } catch (\Exception $_) {}
            $this->json(['error' => 'Deletion failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Safely delete from a table (ignores if table doesn't exist)
     */
    private function safeDeleteFromTable(string $table, string $where, array $params): void
    {
        try {
            $this->db->delete($table, $where, $params);
        } catch (\Exception $e) {
            // If table doesn't exist, just log and continue
            if (strpos($e->getMessage(), 'Base table or view not found') !== false) {
                error_log("Table '$table' does not exist, skipping deletion");
            } else {
                // Re-throw other exceptions
                throw $e;
            }
        }
    }

    /**
     * Safely delete from a table in bulk (ignores if table doesn't exist)
     */
    private function safeDeleteBulkFromTable(string $table, string $column, array $ids): void
    {
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $this->db->query("DELETE FROM $table WHERE $column IN ($placeholders)", $ids);
        } catch (\Exception $e) {
            // If table doesn't exist, just log and continue
            if (strpos($e->getMessage(), 'Base table or view not found') !== false) {
                error_log("Table '$table' does not exist, skipping bulk deletion");
            } else {
                // Re-throw other exceptions
                throw $e;
            }
        }
    }

    /**
     * Bulk delete users
     */
    public function bulkDeleteUsers(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $userIds = $this->input('user_ids');
        if (!is_array($userIds) || empty($userIds)) {
            $this->json(['error' => 'User IDs required'], 400);
            return;
        }

        // Remove current admin from the list
        $userIds = array_filter($userIds, function($id) {
            return $id != $_SESSION['user_id'];
        });

        if (empty($userIds)) {
            $this->json(['error' => 'No valid users to delete'], 400);
            return;
        }

        try {
            $deletedCount = 0;
            $placeholders = implode(',', array_fill(0, count($userIds), '?'));

            // Get user emails for logging
            $users = $this->db->fetchAll(
                "SELECT id, email FROM users WHERE id IN ($placeholders)",
                $userIds
            );

            $this->db->beginTransaction();

            // Delete related records for all users (only from tables that exist)
            $this->safeDeleteBulkFromTable('profiles', 'user_id', $userIds);
            $this->safeDeleteBulkFromTable('publications', 'user_id', $userIds);
            $this->safeDeleteBulkFromTable('education', 'user_id', $userIds);
            $this->safeDeleteBulkFromTable('experience', 'user_id', $userIds);
            $this->safeDeleteBulkFromTable('skills', 'user_id', $userIds);
            $this->safeDeleteBulkFromTable('certifications', 'user_id', $userIds);
            $this->safeDeleteBulkFromTable('awards', 'user_id', $userIds);
            $this->safeDeleteBulkFromTable('memberships', 'user_id', $userIds);
            $this->safeDeleteBulkFromTable('activity_logs', 'user_id', $userIds);

            // Delete users
            $result = $this->db->query(
                "DELETE FROM users WHERE id IN ($placeholders)",
                $userIds
            );

            $deletedCount = $result->rowCount();

            if ($deletedCount > 0) {
                $this->db->commit();
                $this->logActivity('bulk_users_deleted', [
                    'count' => $deletedCount,
                    'user_ids' => $userIds,
                    'emails' => array_column($users, 'email')
                ]);
                $this->json([
                    'success' => true,
                    'message' => "$deletedCount user(s) deleted successfully",
                    'deleted_count' => $deletedCount
                ]);
            } else {
                $this->db->rollback();
                $this->json(['error' => 'No users were deleted'], 500);
            }
        } catch (\Exception $e) {
            try { $this->db->rollback(); } catch (\Exception $_) {}
            $this->json(['error' => 'Bulk deletion failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk suspend users
     */
    public function bulkSuspendUsers(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $userIds = $this->input('user_ids');
        $reason = $this->sanitizeInput($this->input('reason'));

        if (!is_array($userIds) || empty($userIds)) {
            $this->json(['error' => 'User IDs required'], 400);
            return;
        }

        // Remove current admin from the list
        $userIds = array_filter($userIds, function($id) {
            return $id != $_SESSION['user_id'];
        });

        if (empty($userIds)) {
            $this->json(['error' => 'No valid users to suspend'], 400);
            return;
        }

        try {
            $placeholders = implode(',', array_fill(0, count($userIds), '?'));
            
            $result = $this->db->query(
                "UPDATE users SET account_status = 'suspended' WHERE id IN ($placeholders)",
                $userIds
            );

            $suspendedCount = $result->rowCount();

            if ($suspendedCount > 0) {
                $this->logActivity('bulk_users_suspended', [
                    'count' => $suspendedCount,
                    'user_ids' => $userIds,
                    'reason' => $reason
                ]);
                $this->json([
                    'success' => true, 
                    'message' => "$suspendedCount user(s) suspended successfully",
                    'suspended_count' => $suspendedCount
                ]);
            } else {
                $this->json(['error' => 'No users were suspended'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => 'Bulk suspension failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk activate users
     */
    public function bulkActivateUsers(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $userIds = $this->input('user_ids');

        if (!is_array($userIds) || empty($userIds)) {
            $this->json(['error' => 'User IDs required'], 400);
            return;
        }

        try {
            $placeholders = implode(',', array_fill(0, count($userIds), '?'));
            
            $result = $this->db->query(
                "UPDATE users SET account_status = 'active', email_verified = 1 WHERE id IN ($placeholders)",
                $userIds
            );

            $activatedCount = $result->rowCount();

            if ($activatedCount > 0) {
                $this->logActivity('bulk_users_activated', [
                    'count' => $activatedCount,
                    'user_ids' => $userIds
                ]);
                $this->json([
                    'success' => true, 
                    'message' => "$activatedCount user(s) activated successfully",
                    'activated_count' => $activatedCount
                ]);
            } else {
                $this->json(['error' => 'No users were activated'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => 'Bulk activation failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Manually verify user email
     */
    public function verifyUser(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $userId = $this->input('user_id');
        if (!$userId) {
            $this->json(['error' => 'User ID required'], 400);
            return;
        }

        try {
            // Get user info for logging
            $user = $this->userModel->findById($userId);
            
            // Verify email and clear verification code
            $result = $this->db->update('users', 
                [
                    'email_verified' => 1,
                    'verification_code' => null,
                    'verification_expires' => null
                ], 
                'id = ?', 
                [$userId]
            );

            if ($result) {
                $this->logActivity('user_email_verified_manually', [
                    'target_user_id' => $userId,
                    'email' => $user['email'] ?? 'unknown',
                    'verified_by' => 'admin'
                ]);
                $this->json(['success' => true, 'message' => 'User email verified successfully']);
            } else {
                $this->json(['error' => 'Failed to verify user email'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => 'Verification failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk verify user emails
     */
    public function bulkVerifyUsers(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $userIds = $this->input('user_ids');

        if (!is_array($userIds) || empty($userIds)) {
            $this->json(['error' => 'User IDs required'], 400);
            return;
        }

        try {
            $placeholders = implode(',', array_fill(0, count($userIds), '?'));
            
            // Get user emails for logging
            $users = $this->db->fetchAll(
                "SELECT id, email FROM users WHERE id IN ($placeholders)",
                $userIds
            );
            
            // Verify emails
            $result = $this->db->query(
                "UPDATE users SET email_verified = 1, verification_code = NULL, verification_expires = NULL WHERE id IN ($placeholders)",
                $userIds
            );

            $verifiedCount = $result->rowCount();

            if ($verifiedCount > 0) {
                $this->logActivity('bulk_users_verified', [
                    'count' => $verifiedCount,
                    'user_ids' => $userIds,
                    'emails' => array_column($users, 'email'),
                    'verified_by' => 'admin'
                ]);
                $this->json([
                    'success' => true, 
                    'message' => "$verifiedCount user(s) verified successfully",
                    'verified_count' => $verifiedCount
                ]);
            } else {
                $this->json(['error' => 'No users were verified'], 500);
            }
        } catch (\Exception $e) {
            $this->json(['error' => 'Bulk verification failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get user growth data
     */
    private function getUserGrowthData(): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            return $this->db->fetchAll("
                SELECT DATE(created_at) as date, COUNT(*) as count
                FROM users
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get profile completion data
     */
    private function getProfileCompletionData(): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            return [
                'with_photo' => $this->db->fetch("SELECT COUNT(*) as count FROM profiles WHERE profile_photo IS NOT NULL")['count'],
                'with_summary' => $this->db->fetch("SELECT COUNT(*) as count FROM profiles WHERE professional_summary IS NOT NULL AND professional_summary != ''")['count'],
                'with_education' => $this->db->fetch("SELECT COUNT(DISTINCT user_id) as count FROM education")['count'],
                'with_experience' => $this->db->fetch("SELECT COUNT(DISTINCT user_id) as count FROM experience")['count'],
                'with_publications' => $this->db->fetch("SELECT COUNT(DISTINCT user_id) as count FROM publications")['count'],
                'with_skills' => $this->db->fetch("SELECT COUNT(DISTINCT user_id) as count FROM skills")['count'],
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get faculty distribution
     */
    private function getFacultyDistribution(): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            return $this->db->fetchAll("
                SELECT faculty, COUNT(*) as count
                FROM profiles
                WHERE faculty IS NOT NULL AND faculty != ''
                GROUP BY faculty
                ORDER BY count DESC
            ");
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get publication statistics
     */
    private function getPublicationStats(): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            return [
                'total' => $this->db->fetch("SELECT COUNT(*) as count FROM publications")['count'],
                'by_type' => $this->db->fetchAll("
                    SELECT publication_type, COUNT(*) as count
                    FROM publications
                    GROUP BY publication_type
                    ORDER BY count DESC
                "),
                'recent' => $this->db->fetch("
                    SELECT COUNT(*) as count FROM publications 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ")['count'],
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get activity summary
     */
    private function getActivitySummary(): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            return $this->db->fetchAll("
                SELECT action, COUNT(*) as count
                FROM activity_logs
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY action
                ORDER BY count DESC
                LIMIT 10
            ");
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get top contributors
     */
    private function getTopContributors(): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            return $this->db->fetchAll("
                SELECT u.id, p.first_name, p.last_name, p.faculty, p.department,
                       (SELECT COUNT(*) FROM publications WHERE user_id = u.id) as publication_count,
                       (SELECT COUNT(*) FROM education WHERE user_id = u.id) as education_count,
                       (SELECT COUNT(*) FROM experience WHERE user_id = u.id) as experience_count
                FROM users u
                JOIN profiles p ON u.id = p.user_id
                WHERE u.account_status = 'active'
                ORDER BY publication_count DESC
                LIMIT 10
            ");
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get all publications
     */
    private function getAllPublications(int $limit, int $offset): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            return $this->db->fetchAll("
                SELECT pub.*, p.first_name, p.last_name, p.faculty, p.department, u.email
                FROM publications pub
                JOIN users u ON pub.user_id = u.id
                LEFT JOIN profiles p ON u.id = p.user_id
                ORDER BY pub.created_at DESC
                LIMIT ? OFFSET ?
            ", [$limit, $offset]);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get total publications count
     */
    private function getTotalPublicationsCount(): int
    {
        if (!$this->db) {
            return 0;
        }

        try {
            return $this->db->fetch("SELECT COUNT(*) as count FROM publications")['count'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get all activity logs
     */
    private function getAllActivityLogs(int $limit, int $offset): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            return $this->db->fetchAll("
                SELECT al.*, u.email, p.first_name, p.last_name
                FROM activity_logs al
                JOIN users u ON al.user_id = u.id
                LEFT JOIN profiles p ON u.id = p.user_id
                ORDER BY al.created_at DESC
                LIMIT ? OFFSET ?
            ", [$limit, $offset]);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get total activity logs count
     */
    private function getTotalActivityLogsCount(): int
    {
        if (!$this->db) {
            return 0;
        }

        try {
            return $this->db->fetch("SELECT COUNT(*) as count FROM activity_logs")['count'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get system settings
     */
    private function getSystemSettings(): array
    {
        if (!$this->db) {
            return $this->getDefaultSettings();
        }

        try {
            $settings = $this->db->fetchAll("SELECT setting_key, setting_value, setting_type FROM system_settings");
            
            $result = [];
            foreach ($settings as $setting) {
                $value = $setting['setting_value'];
                
                // Convert based on type
                if ($setting['setting_type'] === 'boolean') {
                    $value = (bool)$value;
                } elseif ($setting['setting_type'] === 'integer') {
                    $value = (int)$value;
                } elseif ($setting['setting_type'] === 'json') {
                    $value = json_decode($value, true);
                }
                
                $result[$setting['setting_key']] = $value;
            }
            
            return $result;
        } catch (\Exception $e) {
            return $this->getDefaultSettings();
        }
    }

    /**
     * Get default settings fallback
     */
    private function getDefaultSettings(): array
    {
        return [
            'site_name' => 'TSU Staff Profile Portal',
            'site_description' => 'Taraba State University Staff Profile Management System',
            'admin_email' => 'admin@tsuniversity.edu.ng',
            'allow_registration' => true,
            'require_email_verification' => true,
            'auto_approve_users' => false,
            'default_profile_visibility' => 'public',
            'require_profile_photo' => false,
            'max_photo_size' => 2,
            'session_timeout' => 120,
            'password_min_length' => 8,
            'enable_2fa' => false,
        ];
    }

    /**
     * Check if current user is admin
     */
    private function requireAdmin(): void
    {
        $user = $this->getCurrentUser();
        if (!$user || !isset($user['role']) || $user['role'] !== 'admin') {
            $this->redirect('dashboard');
            exit;
        }
    }

    /**
     * Units Management Page
     */
    public function unitsManagement(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $units = $this->getAllUnits();

        $this->view('admin/units-management', [
            'units' => $units,
            'csrf_token' => $this->generateCSRFToken(),
        ]);
    }

    /**
     * Get all units
     */
    private function getAllUnits(): array
    {
        if (!$this->db) {
            return [];
        }

        try {
            return $this->db->fetchAll("
                SELECT id, name, type, created_at
                FROM units_offices
                ORDER BY name
            ");
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Add a new unit
     */
    public function addUnit(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $name = $this->sanitizeInput($this->input('name'));
        $type = $this->sanitizeInput($this->input('type')) ?: 'unit';

        if (empty($name)) {
            $this->json(['error' => 'Unit name is required'], 422);
            return;
        }

        try {
            $this->db->insert('units_offices', [
                'name' => $name,
                'type' => $type
            ]);

            $this->json(['success' => true, 'message' => 'Unit added successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to add unit'], 500);
        }
    }

    /**
     * Update a unit
     */
    public function updateUnit(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $id = (int)$this->input('id');
        $name = $this->sanitizeInput($this->input('name'));
        $type = $this->sanitizeInput($this->input('type')) ?: 'unit';

        if (empty($name)) {
            $this->json(['error' => 'Unit name is required'], 422);
            return;
        }

        try {
            $this->db->update('units_offices', [
                'name' => $name,
                'type' => $type
            ], 'id = ?', [$id]);

            $this->json(['success' => true, 'message' => 'Unit updated successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to update unit'], 500);
        }
    }

    /**
     * Delete a unit
     */
    public function deleteUnit(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $id = (int)$this->input('id');

        try {
            $this->db->delete('units_offices', 'id = ?', [$id]);
            $this->json(['success' => true, 'message' => 'Unit deleted successfully']);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to delete unit'], 500);
        }
    }

    /**
     * Search users with filters (AJAX endpoint)
     */
    public function searchUsers(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        $query = $this->sanitizeInput($this->input('query') ?? '');
        $staffType = $this->sanitizeInput($this->input('staff_type') ?? '');
        $gender = $this->sanitizeInput($this->input('gender') ?? '');
        $faculty = $this->sanitizeInput($this->input('faculty') ?? '');
        $unit = $this->sanitizeInput($this->input('unit') ?? '');
        $idCardFilter = $this->sanitizeInput($this->input('id_card_filter') ?? ''); // 'printed' | 'not_printed'
        $noPhoto = $this->sanitizeInput($this->input('no_photo') ?? '');            // '1' = no photo only
        $page = (int)($this->input('page') ?: 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        try {
            $conditions = [];
            $params = [];

            if (!empty($query)) {
                $conditions[] = "(p.first_name LIKE ? OR p.last_name LIKE ? OR u.email LIKE ? OR p.staff_number LIKE ? OR p.faculty LIKE ? OR p.unit LIKE ?)";
                $searchTerm = "%$query%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }
            if (!empty($staffType)) { $conditions[] = "p.staff_type = ?"; $params[] = $staffType; }
            if (!empty($gender))    { $conditions[] = "p.gender = ?";     $params[] = $gender; }
            if (!empty($faculty))   { $conditions[] = "p.faculty = ?";    $params[] = $faculty; }
            if (!empty($unit))      { $conditions[] = "p.unit = ?";       $params[] = $unit; }

            if ($idCardFilter === 'printed') {
                $conditions[] = "p.id_card_generated = 1";
            } elseif ($idCardFilter === 'not_printed') {
                $conditions[] = "(p.id_card_generated IS NULL OR p.id_card_generated = 0)";
            }

            if ($noPhoto === '1') {
                $conditions[] = "(p.profile_photo IS NULL OR p.profile_photo = '')";
            }

            $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

            $totalUsers = $this->db->fetch(
                "SELECT COUNT(*) as count FROM users u LEFT JOIN profiles p ON u.id = p.user_id $whereClause",
                $params
            )['count'];
            $totalPages = ceil($totalUsers / $limit);

            $countParams = $params;
            $params[] = $limit;
            $params[] = $offset;

            $users = $this->db->fetchAll("
                SELECT u.id, u.email, u.account_status, u.email_verified, u.created_at, u.last_login, u.role,
                       p.first_name, p.last_name, p.faculty, p.department, p.unit, p.directorate,
                       p.designation, p.staff_number, p.profile_slug, p.staff_type, p.gender,
                       p.profile_photo,
                       COALESCE(p.id_card_generated, 0) as id_card_generated,
                       p.id_card_generated_at
                FROM users u
                LEFT JOIN profiles p ON u.id = p.user_id
                $whereClause
                ORDER BY u.created_at DESC
                LIMIT ? OFFSET ?
            ", $params);

            $this->json([
                'success' => true,
                'users' => $users,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_users' => $totalUsers,
                    'per_page' => $limit
                ]
            ]);
        } catch (\Exception $e) {
            $this->json(['error' => 'Search failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Send photo reminder email to selected users
     */
    public function sendPhotoReminder(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $userIds = $this->input('user_ids');
        if (!is_array($userIds) || empty($userIds)) {
            $this->json(['error' => 'No users selected'], 400);
            return;
        }

        $userIds = array_values(array_filter(array_map('intval', $userIds), fn($id) => $id > 0));
        if (empty($userIds)) {
            $this->json(['error' => 'Invalid user IDs'], 400);
            return;
        }

        try {
            $placeholders = implode(',', array_fill(0, count($userIds), '?'));
            $users = $this->db->fetchAll(
                "SELECT u.id, u.email, p.first_name, p.last_name
                 FROM users u
                 LEFT JOIN profiles p ON u.id = p.user_id
                 WHERE u.id IN ($placeholders)",
                $userIds
            );

            if (empty($users)) {
                $this->json(['error' => 'No users found'], 404);
                return;
            }

            $emailHelper = new \App\Helpers\EmailHelper();
            $sent = 0;
            $failed = 0;

            foreach ($users as $user) {
                $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'Staff Member';
                $appUrl = $_ENV['APP_URL'] ?? 'https://staff.tsuniversity.edu.ng';
                $editUrl = $appUrl . '/profile/edit';

                $subject = 'Action Required: Update Your Profile Photo for ID Card';
                $body = $this->getPhotoReminderEmailBody($name, $editUrl, $appUrl);

                try {
                    $emailHelper->sendRawEmail($user['email'], $subject, $body);
                    $sent++;
                } catch (\Exception $e) {
                    error_log("Photo reminder email failed for {$user['email']}: " . $e->getMessage());
                    $failed++;
                }
            }

            $this->logActivity('photo_reminder_sent', ['user_ids' => $userIds, 'sent' => $sent, 'failed' => $failed]);

            $this->json([
                'success' => true,
                'message' => "Reminder sent to $sent user(s)" . ($failed > 0 ? ", $failed failed" : ''),
            ]);
        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to send reminders: ' . $e->getMessage()], 500);
        }
    }

    private function getPhotoReminderEmailBody(string $name, string $editUrl, string $appUrl): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head><meta charset='utf-8'><title>Update Your Profile Photo</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #1e40af; color: white; padding: 20px; text-align: center; }
            .content { padding: 30px 20px; background: #f9fafb; }
            .button { display: inline-block; padding: 12px 28px; background: #1e40af; color: white; text-decoration: none; border-radius: 6px; margin: 20px 0; font-weight: bold; }
            .photo-guide { background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0; }
            .photo-guide h3 { color: #1e40af; margin-top: 0; }
            .good { color: #16a34a; } .bad { color: #dc2626; }
            .sample-box { background: #f3f4f6; border: 2px dashed #9ca3af; border-radius: 8px; padding: 20px; text-align: center; margin: 15px 0; }
            .footer { padding: 20px; text-align: center; color: #666; font-size: 13px; }
        </style>
        </head>
        <body>
        <div class='container'>
            <div class='header'>
                <h1>TSU Staff Portal</h1>
                <p>Profile Photo Update Required</p>
            </div>
            <div class='content'>
                <p>Dear <strong>{$name}</strong>,</p>
                <p>Your ID card is pending production. To proceed, your profile photo must meet the requirements below. <strong>ID cards will not be printed until a suitable photo is uploaded.</strong></p>

                <div class='sample-box'>
                    <p style='font-size:60px; margin:0;'>🧑</p>
                    <p style='margin:5px 0; font-weight:bold; color:#1e40af;'>Sample: Passport-style photo</p>
                    <p style='margin:0; font-size:13px; color:#555;'>Face centred, plain background, shoulders visible</p>
                </div>

                <div class='photo-guide'>
                    <h3>📋 Photo Requirements</h3>
                    <p><span class='good'>✔</span> Clear, recent passport-style photo</p>
                    <p><span class='good'>✔</span> Face clearly visible, looking directly at the camera</p>
                    <p><span class='good'>✔</span> Plain white or light-coloured background</p>
                    <p><span class='good'>✔</span> Head and shoulders only (no full-body shots)</p>
                    <p><span class='good'>✔</span> Good lighting — no shadows across the face</p>
                    <p><span class='good'>✔</span> Neutral expression or slight smile</p>
                    <p><span class='good'>✔</span> File format: JPG or PNG, max 2MB</p>
                    <hr>
                    <p><span class='bad'>✘</span> No sunglasses, hats, or face coverings</p>
                    <p><span class='bad'>✘</span> No group photos or cropped images</p>
                    <p><span class='bad'>✘</span> No blurry, dark, or heavily filtered photos</p>
                    <p><span class='bad'>✘</span> No photos taken from a distance</p>
                </div>

                <p>Please update your photo as soon as possible to avoid delays in ID card production.</p>

                <div style='text-align:center;'>
                    <a href='{$editUrl}' class='button'>Update My Profile Photo</a>
                </div>
            </div>
            <div class='footer'>
                <p>TSU Staff Portal &mdash; Taraba State University, Jalingo</p>
                <p>If you have already updated your photo, please disregard this message.</p>
            </div>
        </div>
        </body>
        </html>";
    }

    /**
     * Export users to Excel with categorized sheets
     */
    public function exportUsers(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        try {
            // Check if PHPSpreadsheet is available
            if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                $this->json(['error' => 'PHPSpreadsheet library not installed. Run: composer require phpoffice/phpspreadsheet'], 500);
                return;
            }

            // Get filter parameters
            $staffType = $this->sanitizeInput($this->input('staff_type') ?? '');
            $gender = $this->sanitizeInput($this->input('gender') ?? '');
            $faculty = $this->sanitizeInput($this->input('faculty') ?? '');
            $unit = $this->sanitizeInput($this->input('unit') ?? '');

            // Build WHERE clause
            $conditions = [];
            $params = [];

            if (!empty($staffType)) {
                $conditions[] = "p.staff_type = ?";
                $params[] = $staffType;
            }

            if (!empty($gender)) {
                $conditions[] = "p.gender = ?";
                $params[] = $gender;
            }

            if (!empty($faculty)) {
                $conditions[] = "p.faculty = ?";
                $params[] = $faculty;
            }

            if (!empty($unit)) {
                $conditions[] = "p.unit = ?";
                $params[] = $unit;
            }

            $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

            // Get all users matching filters
            $query = "
                SELECT u.id, u.email, u.account_status, u.email_verified, u.created_at, u.role,
                       p.first_name, p.last_name, p.faculty, p.department, p.unit, p.designation, 
                       p.staff_number, p.staff_type, p.gender, p.phone
                FROM users u
                LEFT JOIN profiles p ON u.id = p.user_id
                $whereClause
                ORDER BY p.faculty, p.unit, p.last_name
            ";

            $users = $this->db->fetchAll($query, $params);

            if (empty($users)) {
                $this->json(['error' => 'No users found to export'], 404);
                return;
            }

            // Group users by faculty/unit
            $grouped = [];
            foreach ($users as $user) {
                $category = !empty($user['faculty']) ? $user['faculty'] : (!empty($user['unit']) ? $user['unit'] : 'Uncategorized');
                if (!isset($grouped[$category])) {
                    $grouped[$category] = [];
                }
                $grouped[$category][] = $user;
            }

            // Create spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $spreadsheet->removeSheetByIndex(0); // Remove default sheet

            $sheetIndex = 0;
            foreach ($grouped as $category => $categoryUsers) {
                // Create sheet for this category
                $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, substr($category, 0, 31)); // Excel sheet name limit
                $spreadsheet->addSheet($sheet, $sheetIndex++);

                // Set headers
                $headers = ['#', 'Staff Number', 'Full Name', 'Email', 'Phone', 'Faculty', 'Department', 'Unit', 'Designation', 'Staff Type', 'Gender', 'Status', 'Email Verified', 'Registered Date'];
                $sheet->fromArray($headers, null, 'A1');

                // Style headers
                $headerStyle = [
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
                ];
                $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);

                // Add data
                $row = 2;
                foreach ($categoryUsers as $index => $user) {
                    $sheet->fromArray([
                        $index + 1,
                        $user['staff_number'] ?? 'N/A',
                        trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
                        $user['email'] ?? '',
                        $user['phone'] ?? 'N/A',
                        $user['faculty'] ?? 'N/A',
                        $user['department'] ?? 'N/A',
                        $user['unit'] ?? 'N/A',
                        $user['designation'] ?? 'N/A',
                        $user['staff_type'] ?? 'N/A',
                        $user['gender'] ?? 'N/A',
                        ucfirst($user['account_status'] ?? 'unknown'),
                        $user['email_verified'] ? 'Yes' : 'No',
                        date('Y-m-d', strtotime($user['created_at'] ?? 'now'))
                    ], null, 'A' . $row);
                    $row++;
                }

                // Auto-size columns
                foreach (range('A', 'N') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }

            // Set active sheet to first
            $spreadsheet->setActiveSheetIndex(0);

            // Generate filename
            $filename = 'TSU_Staff_Export_' . date('Y-m-d_His') . '.xlsx';

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Write file to output
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');

            $this->logActivity('users_exported', [
                'total_users' => count($users),
                'categories' => count($grouped),
                'filters' => compact('staffType', 'gender', 'faculty', 'unit')
            ]);

            exit;
        } catch (\Exception $e) {
            $this->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }
}

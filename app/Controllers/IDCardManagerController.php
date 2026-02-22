<?php

namespace App\Controllers;

use App\Core\Controller;

class IDCardManagerController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ID Card Manager Dashboard
     */
    public function dashboard()
    {
        // Get statistics
        $stats = $this->getDashboardStats();
        
        // Get recent print logs
        $recentPrints = $this->getRecentPrintLogs(10);
        
        // Get pending profiles (profiles without ID cards printed)
        $pendingProfiles = $this->getPendingProfiles();
        
        $this->view('id-card-manager/dashboard', [
            'stats' => $stats,
            'recentPrints' => $recentPrints,
            'pendingProfiles' => $pendingProfiles
        ]);
    }

    /**
     * Search and Browse Profiles for ID Card Printing
     */
    public function browse()
    {
        $search = $_GET['search'] ?? '';
        $faculty = $_GET['faculty'] ?? '';
        $department = $_GET['department'] ?? '';
        $staffType = $_GET['staff_type'] ?? '';
        
        // Build query - exclude profiles with ID cards already generated
        $query = "SELECT p.*, u.email, u.account_status 
                  FROM profiles p 
                  INNER JOIN users u ON p.user_id = u.id 
                  WHERE (p.id_card_generated IS NULL OR p.id_card_generated = 0)";
        
        $params = [];
        
        if ($search) {
            $query .= " AND (p.first_name LIKE ? OR p.last_name LIKE ? OR p.staff_number LIKE ? OR u.email LIKE ?)";
            $searchTerm = "%{$search}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        if ($faculty) {
            $query .= " AND p.faculty = ?";
            $params[] = $faculty;
        }
        
        if ($department) {
            $query .= " AND p.department = ?";
            $params[] = $department;
        }
        
        if ($staffType) {
            $query .= " AND p.staff_type = ?";
            $params[] = $staffType;
        }
        
        $query .= " ORDER BY p.created_at DESC";
        
        $profiles = $this->db->fetchAll($query, $params);
        
        // Get faculties for filter
        $faculties = $this->getFaculties();
        
        $this->view('id-card-manager/browse', [
            'profiles' => $profiles,
            'faculties' => $faculties,
            'search' => $search,
            'selectedFaculty' => $faculty,
            'selectedDepartment' => $department,
            'selectedStaffType' => $staffType
        ]);
    }

    /**
     * Print History/Logs
     */
    public function printHistory()
    {
        $page = $_GET['page'] ?? 1;
        $perPage = 50;
        $offset = ($page - 1) * $perPage;
        
        // Get print logs with pagination
        $logs = $this->db->fetchAll("
            SELECT l.*, 
                   p.first_name, p.last_name, p.staff_number,
                   u.email as printer_email
            FROM id_card_print_logs l
            INNER JOIN profiles p ON l.profile_id = p.id
            INNER JOIN users u ON l.user_id = u.id
            ORDER BY l.created_at DESC
            LIMIT ? OFFSET ?
        ", [$perPage, $offset]);
        
        // Get total count
        $totalResult = $this->db->fetch("SELECT COUNT(*) as total FROM id_card_print_logs");
        $total = $totalResult['total'] ?? 0;
        $totalPages = ceil($total / $perPage);
        
        $this->view('id-card-manager/print-history', [
            'logs' => $logs,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }

    /**
     * Bulk Print Interface
     */
    public function bulkPrint()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $profileIds = $_POST['profile_ids'] ?? [];
            
            if (empty($profileIds)) {
                $_SESSION['error'] = 'Please select at least one profile';
                $this->redirect('id-card-manager/browse');
                return;
            }
            
            // Check max bulk print limit
            $maxBulk = 50; // Default max
            if (count($profileIds) > $maxBulk) {
                $_SESSION['error'] = "Maximum {$maxBulk} cards can be printed at once";
                $this->redirect('id-card-manager/browse');
                return;
            }
            
            // Store selected IDs in session and redirect to bulk preview
            $_SESSION['bulk_print_ids'] = $profileIds;
            $this->redirect('id-card-manager/bulk-preview');
            return;
        }
        
        $this->redirect('id-card-manager/browse');
    }

    /**
     * Settings Management
     */
    public function settings()
    {
        // Only admin can access settings
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied';
            $this->redirect('id-card-manager/dashboard');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateSettings($_POST);
            $_SESSION['success'] = 'Settings updated successfully';
            $this->redirect('id-card-manager/settings');
            return;
        }
        
        $settings = $this->getAllSettings();
        
        $this->view('id-card-manager/settings', [
            'settings' => $settings
        ]);
    }

    // Helper Methods
    
    private function getDashboardStats()
    {
        $stats = [];
        
        try {
            // Total profiles
            $result = $this->db->fetch("SELECT COUNT(*) as count FROM profiles");
            $stats['total_profiles'] = $result['count'];
            
            // Pending ID cards (not yet generated)
            $result = $this->db->fetch("SELECT COUNT(*) as count FROM profiles WHERE (id_card_generated IS NULL OR id_card_generated = 0)");
            $stats['pending_id_cards'] = $result['count'];
            
            // Generated ID cards
            $result = $this->db->fetch("SELECT COUNT(*) as count FROM profiles WHERE id_card_generated = 1");
            $stats['generated_id_cards'] = $result['count'];
        } catch (\Exception $e) {
            $stats['total_profiles'] = 0;
            $stats['pending_id_cards'] = 0;
            $stats['generated_id_cards'] = 0;
        }
        
        try {
            // Check if id_card_print_logs table exists
            $tableExists = $this->db->fetch("SHOW TABLES LIKE 'id_card_print_logs'");
            
            if ($tableExists) {
                // Total prints today
                $result = $this->db->fetch("SELECT COUNT(*) as count FROM id_card_print_logs WHERE DATE(created_at) = CURDATE()");
                $stats['prints_today'] = $result['count'];
                
                // Total prints this month
                $result = $this->db->fetch("SELECT COUNT(*) as count FROM id_card_print_logs WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
                $stats['prints_this_month'] = $result['count'];
                
                // Total prints all time
                $result = $this->db->fetch("SELECT COUNT(*) as count FROM id_card_print_logs");
                $stats['total_prints'] = $result['count'];
                
                // Recent activity (last 7 days)
                $stats['activity_chart'] = $this->db->fetchAll("
                    SELECT DATE(created_at) as date, COUNT(*) as count 
                    FROM id_card_print_logs 
                    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                    GROUP BY DATE(created_at)
                    ORDER BY date ASC
                ");
            } else {
                $stats['prints_today'] = 0;
                $stats['prints_this_month'] = 0;
                $stats['total_prints'] = 0;
                $stats['activity_chart'] = [];
            }
        } catch (\Exception $e) {
            $stats['prints_today'] = 0;
            $stats['prints_this_month'] = 0;
            $stats['total_prints'] = 0;
            $stats['activity_chart'] = [];
        }
        
        try {
            // Profiles by staff type
            $stats['by_staff_type'] = $this->db->fetchAll("SELECT staff_type, COUNT(*) as count FROM profiles WHERE staff_type IS NOT NULL GROUP BY staff_type");
        } catch (\Exception $e) {
            $stats['by_staff_type'] = [];
        }
        
        return $stats;
    }
    
    private function getRecentPrintLogs($limit = 10)
    {
        try {
            // Check if table exists
            $tableExists = $this->db->query("SHOW TABLES LIKE 'id_card_print_logs'")->fetch();
            
            if (!$tableExists) {
                return [];
            }
            
            return $this->db->fetchAll("
                SELECT l.*, 
                       p.first_name, p.last_name, p.staff_number, p.profile_photo,
                       u.email as printer_email
                FROM id_card_print_logs l
                INNER JOIN profiles p ON l.profile_id = p.id
                INNER JOIN users u ON l.user_id = u.id
                ORDER BY l.created_at DESC
                LIMIT ?
            ", [$limit]);
        } catch (\Exception $e) {
            error_log("Error fetching print logs: " . $e->getMessage());
            return [];
        }
    }
    
    private function getPendingProfiles($limit = 20)
    {
        try {
            // Return profiles where ID card has not been generated
            return $this->db->fetchAll("
                SELECT p.*, u.email
                FROM profiles p
                INNER JOIN users u ON p.user_id = u.id
                WHERE (p.id_card_generated IS NULL OR p.id_card_generated = 0)
                ORDER BY p.created_at DESC
                LIMIT ?
            ", [$limit]);
        } catch (\Exception $e) {
            error_log("Error fetching pending profiles: " . $e->getMessage());
            return [];
        }
    }
    
    private function getFaculties()
    {
        $stmt = $this->db->query("SELECT DISTINCT faculty FROM profiles WHERE faculty IS NOT NULL ORDER BY faculty");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    private function logBulkPrint($profileIds)
    {
        $stmt = $this->db->prepare("
            INSERT INTO id_card_print_logs (user_id, profile_id, print_type, print_format, ip_address, user_agent)
            VALUES (?, ?, 'bulk', 'pdf', ?, ?)
        ");
        
        foreach ($profileIds as $profileId) {
            $stmt->execute([
                $_SESSION['user_id'],
                $profileId,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        }
    }
    
    private function getSetting($key, $default = null)
    {
        $stmt = $this->db->prepare("SELECT setting_value FROM id_card_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        return $value !== false ? $value : $default;
    }
    
    private function getAllSettings()
    {
        $stmt = $this->db->query("SELECT * FROM id_card_settings ORDER BY setting_key");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    private function updateSettings($data)
    {
        foreach ($data as $key => $value) {
            $stmt = $this->db->prepare("UPDATE id_card_settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        }
    }

    /**
     * Preview ID card for a specific user
     */
    public function preview(int $userId): void
    {
        $this->requireAuth();

        if (!$this->db) {
            $this->redirect('id-card-manager/dashboard');
            return;
        }

        // Get user profile
        $profile = $this->db->fetch("
            SELECT u.id, u.email, 
                   p.id as profile_id, p.title, p.first_name, p.middle_name, p.last_name,
                   p.designation, p.faculty, p.department, p.unit, p.staff_type, p.profile_photo, 
                   p.profile_slug, p.qr_code_path, p.staff_number, p.blood_group
            FROM users u
            INNER JOIN profiles p ON u.id = p.user_id
            WHERE u.id = ?
        ", [$userId]);

        if (!$profile) {
            $this->redirect('id-card-manager/dashboard');
            return;
        }

        // Ensure QR code exists
        $qrCodeUrl = $this->ensureQRCodeExists($userId, $profile['profile_slug'], $profile['qr_code_path']);

        // Mark ID card as generated
        $this->db->update('profiles', [
            'id_card_generated' => 1,
            'id_card_generated_at' => date('Y-m-d H:i:s'),
            'id_card_generated_by' => $_SESSION['user_id'] ?? null
        ], 'user_id = ?', [$userId]);

        // Decode HTML entities
        $textFields = ['title', 'first_name', 'middle_name', 'last_name', 'faculty', 'department', 'unit', 'designation', 'staff_number', 'email', 'blood_group'];
        foreach ($textFields as $field) {
            if (isset($profile[$field])) {
                $profile[$field] = html_entity_decode($profile[$field], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        $this->view('admin/id-card-preview', [
            'profile' => $profile,
            'qr_code_url' => $qrCodeUrl,
        ]);
    }

    /**
     * Bulk preview ID cards
     */
    public function bulkPreview(): void
    {
        $this->requireAuth();

        if (!isset($_SESSION['bulk_print_ids']) || empty($_SESSION['bulk_print_ids'])) {
            $_SESSION['error'] = 'No profiles selected for bulk print';
            $this->redirect('id-card-manager/browse');
            return;
        }

        $userIds = $_SESSION['bulk_print_ids'];
        $placeholders = implode(',', array_fill(0, count($userIds), '?'));

        // Get profiles
        $profiles = $this->db->fetchAll("
            SELECT u.id, u.email, 
                   p.id as profile_id, p.title, p.first_name, p.middle_name, p.last_name,
                   p.designation, p.faculty, p.department, p.unit, p.staff_type, p.profile_photo, 
                   p.profile_slug, p.qr_code_path, p.staff_number, p.blood_group
            FROM users u
            INNER JOIN profiles p ON u.id = p.user_id
            WHERE u.id IN ($placeholders)
        ", $userIds);

        if (empty($profiles)) {
            $_SESSION['error'] = 'No profiles found';
            $this->redirect('id-card-manager/browse');
            return;
        }

        // Ensure QR codes exist and mark as generated
        foreach ($profiles as &$profile) {
            $profile['qr_code_url'] = $this->ensureQRCodeExists($profile['id'], $profile['profile_slug'], $profile['qr_code_path']);
            
            // Mark as generated
            $this->db->update('profiles', [
                'id_card_generated' => 1,
                'id_card_generated_at' => date('Y-m-d H:i:s'),
                'id_card_generated_by' => $_SESSION['user_id'] ?? null
            ], 'user_id = ?', [$profile['id']]);

            // Decode HTML entities
            $textFields = ['title', 'first_name', 'middle_name', 'last_name', 'faculty', 'department', 'unit', 'designation', 'staff_number', 'email', 'blood_group'];
            foreach ($textFields as $field) {
                if (isset($profile[$field])) {
                    $profile[$field] = html_entity_decode($profile[$field], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            }
        }

        $this->view('id-card-manager/bulk-preview', [
            'profiles' => $profiles,
        ]);
    }

    /**
     * Print single ID card
     */
    public function printSingle(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $userId = $this->input('user_id');
        if (!$userId) {
            $this->json(['error' => 'User ID required'], 400);
            return;
        }

        // Redirect to preview page
        $this->json(['success' => true, 'redirect' => url("id-card-manager/preview/{$userId}")]);
    }

    /**
     * Show generated ID cards
     */
    public function generatedCards(): void
    {
        $this->requireAuth();

        $page = (int)($this->input('page') ?: 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get profiles with generated ID cards
        $profiles = $this->db->fetchAll("
            SELECT p.*, u.email, p.id_card_generated_at,
                   gen_by.email as generated_by_email,
                   CONCAT(gen_profile.first_name, ' ', gen_profile.last_name) as generated_by_name
            FROM profiles p
            INNER JOIN users u ON p.user_id = u.id
            LEFT JOIN users gen_by ON p.id_card_generated_by = gen_by.id
            LEFT JOIN profiles gen_profile ON gen_by.id = gen_profile.user_id
            WHERE p.id_card_generated = 1
            ORDER BY p.id_card_generated_at DESC
            LIMIT ? OFFSET ?
        ", [$limit, $offset]);

        $totalGenerated = $this->db->fetch("SELECT COUNT(*) as count FROM profiles WHERE id_card_generated = 1")['count'];
        $totalPages = ceil($totalGenerated / $limit);

        $this->view('id-card-manager/generated-cards', [
            'profiles' => $profiles,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_generated' => $totalGenerated,
        ]);
    }

    /**
     * Ensure QR code exists
     */
    private function ensureQRCodeExists($userId, $slug, $currentPath)
    {
        require_once __DIR__ . '/../Helpers/QRCodeHelper.php';
        
        // Check if QR code already exists and is valid
        if (!empty($currentPath)) {
            $fullPath = __DIR__ . '/../../public/' . ltrim($currentPath, '/');
            if (file_exists($fullPath) && filesize($fullPath) > 0) {
                return url($currentPath);
            }
        }
        
        // Generate new QR code
        $filename = \App\Helpers\QRCodeHelper::generateProfileQRCode($userId, $slug);
        
        if ($filename) {
            // Update database with new QR code path
            $relativePath = 'storage/qrcodes/' . $filename;
            $this->db->update('profiles', ['qr_code_path' => $relativePath], 'user_id = ?', [$userId]);
            
            // Return URL through serving route
            return url('qrcode/' . $filename);
        }
        
        // Return empty if generation failed
        return '';
    }
}

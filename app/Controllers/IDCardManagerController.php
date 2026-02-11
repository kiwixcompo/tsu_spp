<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class IDCardManagerController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
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
        
        // Build query
        $query = "SELECT p.*, u.email, u.account_status 
                  FROM profiles p 
                  INNER JOIN users u ON p.user_id = u.id 
                  WHERE 1=1";
        
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
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $profiles = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
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
        $stmt = $this->db->prepare("
            SELECT l.*, 
                   p.first_name, p.last_name, p.staff_number,
                   u.email as printer_email
            FROM id_card_print_logs l
            INNER JOIN profiles p ON l.profile_id = p.id
            INNER JOIN users u ON l.user_id = u.id
            ORDER BY l.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Get total count
        $totalStmt = $this->db->query("SELECT COUNT(*) FROM id_card_print_logs");
        $total = $totalStmt->fetchColumn();
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
            $maxBulk = $this->getSetting('max_bulk_print_count', 50);
            if (count($profileIds) > $maxBulk) {
                $_SESSION['error'] = "Maximum {$maxBulk} cards can be printed at once";
                $this->redirect('id-card-manager/browse');
                return;
            }
            
            // Log bulk print
            $this->logBulkPrint($profileIds);
            
            // Redirect to ID card controller for actual printing
            $_SESSION['bulk_print_ids'] = $profileIds;
            $this->redirect('admin/id-card-generator?bulk=1');
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
        
        // Total profiles
        $stmt = $this->db->query("SELECT COUNT(*) FROM profiles");
        $stats['total_profiles'] = $stmt->fetchColumn();
        
        // Total prints today
        $stmt = $this->db->query("SELECT COUNT(*) FROM id_card_print_logs WHERE DATE(created_at) = CURDATE()");
        $stats['prints_today'] = $stmt->fetchColumn();
        
        // Total prints this month
        $stmt = $this->db->query("SELECT COUNT(*) FROM id_card_print_logs WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $stats['prints_this_month'] = $stmt->fetchColumn();
        
        // Total prints all time
        $stmt = $this->db->query("SELECT COUNT(*) FROM id_card_print_logs");
        $stats['total_prints'] = $stmt->fetchColumn();
        
        // Profiles by staff type
        $stmt = $this->db->query("SELECT staff_type, COUNT(*) as count FROM profiles GROUP BY staff_type");
        $stats['by_staff_type'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Recent activity (last 7 days)
        $stmt = $this->db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM id_card_print_logs 
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stats['activity_chart'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $stats;
    }
    
    private function getRecentPrintLogs($limit = 10)
    {
        $stmt = $this->db->prepare("
            SELECT l.*, 
                   p.first_name, p.last_name, p.staff_number, p.profile_photo,
                   u.email as printer_email
            FROM id_card_print_logs l
            INNER JOIN profiles p ON l.profile_id = p.id
            INNER JOIN users u ON l.user_id = u.id
            ORDER BY l.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    private function getPendingProfiles($limit = 20)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, u.email
            FROM profiles p
            INNER JOIN users u ON p.user_id = u.id
            LEFT JOIN id_card_print_logs l ON p.id = l.profile_id
            WHERE l.id IS NULL
            ORDER BY p.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
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
}

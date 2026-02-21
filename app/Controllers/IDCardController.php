<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Profile;
use App\Helpers\QRCodeHelper;

class IDCardController extends Controller
{
    private $profileModel;

    public function __construct()
    {
        parent::__construct();
        $this->profileModel = new Profile();
    }

    /**
     * Show ID card generator page (Admin only)
     */
    public function index(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        // Get all users with profiles
        $users = [];
        if ($this->db) {
            $users = $this->db->fetchAll("
                SELECT u.id, u.email, 
                       p.id as profile_id, p.title, p.first_name, p.middle_name, p.last_name,
                       p.designation, p.faculty, p.department, p.unit, p.staff_type, p.profile_photo, 
                       p.profile_slug, p.qr_code_path, p.staff_number, p.blood_group
                FROM users u
                INNER JOIN profiles p ON u.id = p.user_id
                WHERE u.account_status = 'active'
                ORDER BY p.last_name, p.first_name
            ");
        }

        $this->view('admin/id-card-generator', [
            'csrf_token' => $this->generateCSRFToken(),
            'users' => $users,
        ]);
    }

    /**
     * Generate ID card for a specific user
     */
    public function generate(int $userId): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->db) {
            $this->json(['error' => 'Database unavailable'], 500);
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
            $this->json(['error' => 'Profile not found'], 404);
            return;
        }

        // Generate QR code if not exists or file missing
        $qrCodeUrl = $this->ensureQRCodeExists($userId, $profile['profile_slug'], $profile['qr_code_path']);
        
        // Update profile array with new path if it changed
        if ($qrCodeUrl) {
             $profile['qr_code_url'] = $qrCodeUrl;
        }

        // Mark ID card as generated
        $this->db->update('profiles', [
            'id_card_generated' => 1,
            'id_card_generated_at' => date('Y-m-d H:i:s'),
            'id_card_generated_by' => $_SESSION['user_id'] ?? null
        ], 'user_id = ?', [$userId]);

        // Return profile data for ID card generation
        $this->json([
            'success' => true,
            'profile' => $profile,
            'qr_code_url' => $qrCodeUrl,
        ]);
    }

    /**
     * Show ID card preview/print page
     */
    public function preview(int $userId): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->db) {
            $this->redirect('admin/id-cards');
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
            $this->redirect('admin/id-cards');
            return;
        }

        // Ensure QR code exists and is valid
        $qrCodeUrl = $this->ensureQRCodeExists($userId, $profile['profile_slug'], $profile['qr_code_path']);

        // Mark ID card as generated when preview is accessed
        $this->db->update('profiles', [
            'id_card_generated' => 1,
            'id_card_generated_at' => date('Y-m-d H:i:s'),
            'id_card_generated_by' => $_SESSION['user_id'] ?? null
        ], 'user_id = ?', [$userId]);

        // Decode HTML entities for proper display on ID card
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
     * Helper to check/generate QR code
     */
    private function ensureQRCodeExists($userId, $slug, $currentPath) 
    {
        $qrCodeUrl = null;
        
        // 1. Check if we have a path
        if (!empty($currentPath)) {
            $qrCodeUrl = QRCodeHelper::getQRCodeUrl($currentPath);
        }

        // 2. If URL is empty (means file missing on disk) OR path was empty, regenerate
        if (empty($qrCodeUrl)) {
            $qrCodePath = QRCodeHelper::generateProfileQRCode($userId, $slug);
            if ($qrCodePath) {
                $this->db->update('profiles', 
                    ['qr_code_path' => $qrCodePath], 
                    'user_id = ?', 
                    [$userId]
                );
                $qrCodeUrl = QRCodeHelper::getQRCodeUrl($qrCodePath);
            }
        }

        return $qrCodeUrl;
    }

    /**
     * Regenerate QR code for a user
     */
    public function regenerateQR(int $userId): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        if (!$this->db) {
            $this->json(['error' => 'Database unavailable'], 500);
            return;
        }

        // Get profile
        $profile = $this->db->fetch("
            SELECT user_id, profile_slug, qr_code_path
            FROM profiles
            WHERE user_id = ?
        ", [$userId]);

        if (!$profile) {
            $this->json(['error' => 'Profile not found'], 404);
            return;
        }

        // Regenerate QR code
        $newQRCode = QRCodeHelper::regenerateQRCode(
            $userId, 
            $profile['profile_slug'], 
            $profile['qr_code_path']
        );

        if ($newQRCode) {
            // Update database
            $this->db->update('profiles', 
                ['qr_code_path' => $newQRCode], 
                'user_id = ?', 
                [$userId]
            );

            $this->json([
                'success' => true,
                'message' => 'QR code regenerated successfully',
                'qr_code_url' => QRCodeHelper::getQRCodeUrl($newQRCode),
            ]);
        } else {
            $this->json(['error' => 'Failed to regenerate QR code'], 500);
        }
    }

    /**
     * Bulk generate ID cards - returns profile data with QR codes
     */
    public function bulkGenerate(): void
    {
        $this->requireAuth();
        $this->requireAdmin();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        if (!$this->db) {
            $this->json(['error' => 'Database unavailable'], 500);
            return;
        }

        $userIds = $this->input('user_ids');
        if (!is_array($userIds) || empty($userIds)) {
            $this->json(['error' => 'No user IDs provided'], 400);
            return;
        }

        // Sanitize IDs to integers
        $userIds = array_values(array_filter(array_map('intval', $userIds), fn($id) => $id > 0));
        if (empty($userIds)) {
            $this->json(['error' => 'Invalid user IDs'], 400);
            return;
        }

        $placeholders = implode(',', array_fill(0, count($userIds), '?'));

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
            $this->json(['error' => 'No profiles found'], 404);
            return;
        }

        $results = [];

        foreach ($profiles as $profile) {
            $userId = (int)$profile['id'];
            
            // Ensure QR code exists (Generate if missing)
            $qrCodeUrl = $this->ensureQRCodeExists($userId, $profile['profile_slug'], $profile['qr_code_path']);

            // Decode HTML entities for safe display
            $textFields = ['title', 'first_name', 'middle_name', 'last_name', 'faculty', 'department', 'designation', 'staff_number', 'email', 'blood_group'];
            foreach ($textFields as $field) {
                if (isset($profile[$field])) {
                    $profile[$field] = html_entity_decode($profile[$field], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            }

            $profile['qr_code_url'] = $qrCodeUrl;
            $results[] = $profile;
        }

        $this->json([
            'success' => true,
            'profiles' => $results,
        ]);
    }

    /**
     * Serve QR code image
     */
    public function serveQRCode(string $filename): void
    {
        // Sanitize filename
        $filename = basename($filename);
        $filepath = __DIR__ . '/../../storage/qrcodes/' . $filename;
        
        if (!file_exists($filepath)) {
            header('HTTP/1.0 404 Not Found');
            echo '404 Not Found';
            exit;
        }
        
        // Serve the image
        header('Content-Type: image/png');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: public, max-age=31536000');
        readfile($filepath);
        exit;
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
}
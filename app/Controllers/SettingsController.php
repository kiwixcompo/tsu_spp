<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class SettingsController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        try {
            $this->userModel = new User();
        } catch (\Exception $e) {
            $this->userModel = null;
        }
    }

    public function index(): void
    {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        
        $this->view('settings/index', [
            'csrf_token' => $this->generateCSRFToken(),
            'user' => $user,
        ]);
    }

    public function updatePassword(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $errors = $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|password_strength',
            'confirm_password' => 'required|matches:new_password',
        ]);

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        $user = $this->getCurrentUser();
        
        // Verify current password
        if (!password_verify($this->input('current_password'), $user['password'])) {
            $this->json(['errors' => ['current_password' => 'Current password is incorrect']], 422);
            return;
        }

        try {
            $newPasswordHash = password_hash($this->input('new_password'), PASSWORD_DEFAULT);
            
            if ($this->db) {
                $success = $this->db->update('users', 
                    ['password' => $newPasswordHash, 'updated_at' => date('Y-m-d H:i:s')], 
                    'id = ?', 
                    [$user['id']]
                );

                if ($success) {
                    // Log activity
                    $this->logActivity('password_changed');

                    $this->json([
                        'success' => true,
                        'message' => 'Password updated successfully!',
                    ]);
                } else {
                    $this->json(['error' => 'Failed to update password'], 500);
                }
            } else {
                $this->json(['error' => 'Database unavailable'], 500);
            }

        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to update password: ' . $e->getMessage()], 500);
        }
    }

    public function updateProfile(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $errors = $this->validate([
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
        ]);

        if (!empty($errors)) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        $user = $this->getCurrentUser();

        try {
            $updateData = [
                'first_name' => $this->sanitizeInput($this->input('first_name')),
                'last_name' => $this->sanitizeInput($this->input('last_name')),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($this->db) {
                $success = $this->db->update('users', $updateData, 'id = ?', [$user['id']]);

                if ($success) {
                    // Update session data
                    $_SESSION['user_first_name'] = $updateData['first_name'];
                    $_SESSION['user_last_name'] = $updateData['last_name'];

                    // Log activity
                    $this->logActivity('profile_info_updated');

                    $this->json([
                        'success' => true,
                        'message' => 'Profile updated successfully!',
                    ]);
                } else {
                    $this->json(['error' => 'Failed to update profile'], 500);
                }
            } else {
                $this->json(['error' => 'Database unavailable'], 500);
            }

        } catch (\Exception $e) {
            $this->json(['error' => 'Failed to update profile: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete current user's account and related data
     */
    public function deleteAccount(): void
    {
        $this->requireAuth();

        if (!$this->verifyCSRFToken()) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            return;
        }

        $user = $this->getCurrentUser();
        if (!$user) {
            $this->json(['error' => 'User not found'], 404);
            return;
        }

        $userId = $user['id'];

        try {
            // Begin transaction and remove related data
            $this->db->beginTransaction();

            // Remove uploaded files from profile
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

            // Delete related records
            $this->db->delete('profiles', 'user_id = ?', [$userId]);
            $this->db->delete('publications', 'user_id = ?', [$userId]);
            $this->db->delete('education', 'user_id = ?', [$userId]);
            $this->db->delete('experience', 'user_id = ?', [$userId]);
            $this->db->delete('skills', 'user_id = ?', [$userId]);
            $this->db->delete('certifications', 'user_id = ?', [$userId]);
            $this->db->delete('awards', 'user_id = ?', [$userId]);
            $this->db->delete('memberships', 'user_id = ?', [$userId]);
            $this->db->delete('activity_logs', 'user_id = ?', [$userId]);

            // Delete user record
            $result = $this->db->delete('users', 'id = ?', [$userId]);

            if ($result) {
                $this->db->commit();
                // Clear session and logout
                session_unset();
                session_destroy();

                $this->json(['success' => true, 'message' => 'Your account has been deleted']);
            } else {
                $this->db->rollback();
                $this->json(['error' => 'Failed to delete account'], 500);
            }
        } catch (\Exception $e) {
            try { $this->db->rollback(); } catch (\Exception $_) {}
            $this->json(['error' => 'Account deletion failed: ' . $e->getMessage()], 500);
        }
    }
}
<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Profile;
use App\Models\User;

class DashboardController extends Controller
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

    /**
     * Check if a non-teaching staff member needs to update their profile
     * to use the new Directorate/Unit structure.
     * Returns true if: staff_type is non-teaching AND directorate column is empty.
     */
    private function needsDirectorateUpdate(?array $profile): bool
    {
        if (!$profile) return false;
        if (($profile['staff_type'] ?? '') !== 'non-teaching') return false;
        return empty($profile['directorate']);
    }

    public function index(): void
    {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        // Get role from user object (in case session wasn't set)
        $role = $user['role'] ?? $_SESSION['role'] ?? 'user';
        
        // Redirect Nominal Role to their dedicated dashboard
        if ($role === 'nominal_role') {
            $this->redirect('nominal-role/dashboard');
            return;
        }
        
        // Redirect ID Card Manager to their dedicated dashboard
        if ($role === 'id_card_manager') {
            $this->redirect('id-card-manager/dashboard');
            return;
        }

        // Redirect Admin to admin dashboard
        if ($role === 'admin') {
            $this->redirect('admin/dashboard');
            return;
        }

        // Check if user has a profile, if not redirect to profile setup
        if ($this->profileModel) {
            try {
                $profile = $this->profileModel->findByUserId($user['id']);
                if (!$profile) {
                    $this->redirect('profile/setup');
                    return;
                }
            } catch (\Exception $e) {
                // If there's an error checking profile, continue to dashboard
                error_log("Profile check error: " . $e->getMessage());
            }
        }

        // Get profile completion stats
        $profileStats = [
            'completion' => 0,
            'sections' => [
                'basic_info' => false,
                'photo' => false,
                'summary' => false,
                'education' => false,
                'experience' => false,
                'skills' => false,
            ]
        ];
        
        if ($this->profileModel) {
            try {
                $stats = $this->profileModel->getProfileStats($user['id']);
                if (isset($stats['completion']) && isset($stats['sections'])) {
                    $profileStats = $stats;
                    // Ensure all expected sections exist
                    $defaultSections = [
                        'basic_info' => false,
                        'photo' => false,
                        'summary' => false,
                        'education' => false,
                        'experience' => false,
                        'skills' => false,
                    ];
                    $profileStats['sections'] = array_merge($defaultSections, $profileStats['sections']);
                }
            } catch (\Exception $e) {
                error_log("Profile stats error: " . $e->getMessage());
            }
        }

        // Get recent activity (if database is available)
        $recentActivity = [];
        if ($this->db) {
            try {
                $recentActivity = $this->db->fetchAll(
                    "SELECT * FROM activity_logs 
                     WHERE user_id = ? 
                     ORDER BY created_at DESC 
                     LIMIT 10",
                    [$user['id']]
                );
            } catch (\Exception $e) {
                $recentActivity = [];
            }
        }

        // Get profile views (if profile exists)
        $profileViews = 0;
        if ($this->profileModel && !empty($user['profile_slug'])) {
            try {
                $profile = $this->profileModel->findBySlug($user['profile_slug']);
                $profileViews = $profile['profile_views'] ?? 0;
            } catch (\Exception $e) {
                $profileViews = 0;
            }
        }

        // Get profile data for display
        $profile = null;
        if ($this->profileModel) {
            try {
                $profile = $this->profileModel->findByUserId($user['id']);
            } catch (\Exception $e) {
                error_log("Profile fetch error: " . $e->getMessage());
            }
        }

        $this->view('dashboard/index', [
            'user' => $user,
            'profile' => $profile,
            'profile_stats' => $profileStats,
            'recent_activity' => $recentActivity,
            'profile_views' => $profileViews,
            'needs_directorate_update' => $this->needsDirectorateUpdate($profile),
        ]);
    }
}
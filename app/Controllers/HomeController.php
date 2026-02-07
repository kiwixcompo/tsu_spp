<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Profile;

class HomeController extends Controller
{
    private $profileModel;

    public function __construct()
    {
        parent::__construct();
        // Only initialize profile model if database is available
        try {
            $this->profileModel = new Profile();
        } catch (\Exception $e) {
            $this->profileModel = null;
        }
    }

    public function index(): void
    {
        // Get featured profiles for homepage (empty array if no database)
        $featuredProfiles = [];
        if ($this->profileModel) {
            try {
                $featuredProfiles = $this->profileModel->getPublicProfiles(8);
            } catch (\Exception $e) {
                $featuredProfiles = [];
            }
        }
        
        // Get basic stats
        $stats = $this->getBasicStats();

        $this->view('home/index', [
            'featured_profiles' => $featuredProfiles,
            'stats' => $stats,
        ]);
    }

    public function about(): void
    {
        $this->view('home/about');
    }

    private function getBasicStats(): array
    {
        // Return default stats if database is not available
        if (!$this->db) {
            return [
                'total_profiles' => 0,
                'total_faculties' => 11,
                'total_departments' => 65,
            ];
        }

        try {
            $totalProfiles = $this->db->fetch(
                "SELECT COUNT(*) as count FROM profiles p 
                 JOIN users u ON p.user_id = u.id 
                 WHERE u.account_status = 'active' AND p.profile_visibility = 'public'"
            )['count'];

            $totalFaculties = $this->db->fetch(
                "SELECT COUNT(DISTINCT faculty) as count FROM profiles 
                 WHERE faculty IS NOT NULL AND faculty != ''"
            )['count'];

            $totalDepartments = $this->db->fetch(
                "SELECT COUNT(DISTINCT department) as count FROM profiles 
                 WHERE department IS NOT NULL AND department != ''"
            )['count'];

            return [
                'total_profiles' => $totalProfiles,
                'total_faculties' => $totalFaculties,
                'total_departments' => $totalDepartments,
            ];
        } catch (\Exception $e) {
            return [
                'total_profiles' => 0,
                'total_faculties' => 11,
                'total_departments' => 65,
            ];
        }
    }
}
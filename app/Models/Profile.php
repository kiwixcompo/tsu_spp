<?php

namespace App\Models;

use App\Core\Database;

class Profile
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(array $data): int
    {
        return $this->db->insert('profiles', $data);
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM profiles WHERE user_id = ?",
            [$userId]
        );
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetch(
            "SELECT p.*, u.email, u.last_login 
             FROM profiles p 
             JOIN users u ON p.user_id = u.id 
             WHERE p.profile_slug = ? AND u.account_status = 'active'",
            [$slug]
        );
    }

    public function update(int $userId, array $data): bool
    {
        return $this->db->update(
            'profiles',
            $data,
            'user_id = ?',
            [$userId]
        ) > 0;
    }

    public function incrementViews(string $slug): void
    {
        $this->db->query(
            "UPDATE profiles SET profile_views = profile_views + 1 WHERE profile_slug = ?",
            [$slug]
        );
    }

    public function searchProfiles(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $where = ["u.account_status = 'active'", "p.profile_visibility = 'public'", "u.email != 'admin@tsuniversity.edu.ng'"];
        $params = [];

        // Build search query
        if (!empty($filters['search'])) {
            $searchTerm = "%{$filters['search']}%";
            $where[] = "(MATCH(p.first_name, p.last_name, p.professional_summary, p.research_interests, p.expertise_keywords) AGAINST(? IN NATURAL LANGUAGE MODE) 
                       OR p.first_name LIKE ? 
                       OR p.last_name LIKE ? 
                       OR p.professional_summary LIKE ?)";
            $params = array_merge($params, [$filters['search'], $searchTerm, $searchTerm, $searchTerm]);
        }

        if (!empty($filters['faculty'])) {
            $where[] = "p.faculty = ?";
            $params[] = $filters['faculty'];
        }

        if (!empty($filters['department'])) {
            $where[] = "p.department = ?";
            $params[] = $filters['department'];
        }

        $whereClause = implode(' AND ', $where);
        
        // Determine sort order
        $orderBy = "p.created_at DESC";
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'name':
                    $orderBy = "p.first_name ASC, p.last_name ASC";
                    break;
                case 'updated':
                    $orderBy = "p.updated_at DESC";
                    break;
                case 'views':
                    $orderBy = "p.profile_views DESC";
                    break;
            }
        }

        $profiles = $this->db->fetchAll(
            "SELECT p.*, u.email 
             FROM profiles p 
             JOIN users u ON p.user_id = u.id 
             WHERE {$whereClause} 
             ORDER BY {$orderBy} 
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        $total = $this->db->fetch(
            "SELECT COUNT(*) as count 
             FROM profiles p 
             JOIN users u ON p.user_id = u.id 
             WHERE {$whereClause}",
            $params
        )['count'];

        return [
            'profiles' => $profiles,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage),
            'filters' => $filters,
        ];
    }

    public function getPublicProfiles(int $limit = 12): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, u.email 
             FROM profiles p 
             JOIN users u ON p.user_id = u.id 
             WHERE u.account_status = 'active' 
               AND p.profile_visibility = 'public'
               AND u.email != 'admin@tsuniversity.edu.ng'
             ORDER BY p.profile_views DESC, p.updated_at DESC 
             LIMIT ?",
            [$limit]
        );
    }

    public function getFaculties(): array
    {
        return $this->db->fetchAll(
            "SELECT DISTINCT faculty 
             FROM profiles 
             WHERE faculty IS NOT NULL AND faculty != '' 
             ORDER BY faculty"
        );
    }

    public function getDepartmentsByFaculty(string $faculty): array
    {
        return $this->db->fetchAll(
            "SELECT DISTINCT department 
             FROM profiles 
             WHERE faculty = ? AND department IS NOT NULL AND department != '' 
             ORDER BY department",
            [$faculty]
        );
    }

    public function generateUniqueSlug(string $baseSlug, int $userId = null): string
    {
        $slug = $baseSlug;
        $counter = 1;
        
        while (true) {
            $existing = $this->db->fetch(
                "SELECT id FROM profiles WHERE profile_slug = ?" . ($userId ? " AND user_id != ?" : ""),
                $userId ? [$slug, $userId] : [$slug]
            );
            
            if (!$existing) {
                return $slug;
            }
            
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
    }

    public function getProfileStats(int $userId): array
    {
        $profile = $this->findByUserId($userId);
        if (!$profile) {
            return ['completion' => 0, 'sections' => []];
        }

        $sections = [
            'basic_info' => !empty($profile['first_name']) && !empty($profile['last_name']) && !empty($profile['faculty']),
            'photo' => !empty($profile['profile_photo']),
            'summary' => !empty($profile['professional_summary']),
            'education' => $this->hasEducation($userId),
            'experience' => $this->hasExperience($userId),
            'skills' => $this->hasSkills($userId),
        ];

        $completed = array_sum($sections);
        $total = count($sections);
        $completion = round(($completed / $total) * 100);

        return [
            'completion' => $completion,
            'sections' => $sections,
        ];
    }

    private function hasEducation(int $userId): bool
    {
        $count = $this->db->fetch(
            "SELECT COUNT(*) as count FROM education WHERE user_id = ?",
            [$userId]
        )['count'];
        return $count > 0;
    }

    private function hasExperience(int $userId): bool
    {
        $count = $this->db->fetch(
            "SELECT COUNT(*) as count FROM experience WHERE user_id = ?",
            [$userId]
        )['count'];
        return $count > 0;
    }

    private function hasSkills(int $userId): bool
    {
        $count = $this->db->fetch(
            "SELECT COUNT(*) as count FROM skills WHERE user_id = ?",
            [$userId]
        )['count'];
        return $count > 0;
    }
}
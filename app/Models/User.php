<?php

namespace App\Models;

use App\Core\Database;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(array $data): int
    {
        return $this->db->insert('users', [
            'email' => $data['email'],
            'email_prefix' => $data['email_prefix'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'verification_code' => $data['verification_code'],
            'verification_expires' => $data['verification_expires'],
            'account_status' => 'pending',
        ]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM users WHERE email = ?",
            [$email]
        );
    }

    public function findByVerificationCode(string $code): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM users WHERE verification_code = ? AND email_verified = 0",
            [$code]
        );
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM users WHERE id = ?",
            [$id]
        );
    }

    public function verifyEmail(int $userId): bool
    {
        // Only verify email, keep account as pending until profile setup is complete
        return $this->db->update(
            'users',
            [
                'email_verified' => true,
                // account_status remains 'pending' until profile is set up
                'verification_code' => null,
                'verification_expires' => null,
            ],
            'id = ?',
            [$userId]
        ) > 0;
    }

    public function updateVerificationCode(int $userId, string $code, string $expires): bool
    {
        return $this->db->update(
            'users',
            [
                'verification_code' => $code,
                'verification_expires' => $expires,
            ],
            'id = ?',
            [$userId]
        ) > 0;
    }

    public function updateLastLogin(int $userId): void
    {
        $this->db->update(
            'users',
            ['last_login' => date('Y-m-d H:i:s')],
            'id = ?',
            [$userId]
        );
    }

    public function setPasswordResetToken(int $userId, string $token, string $expires): bool
    {
        return $this->db->update(
            'users',
            [
                'reset_token' => $token,
                'reset_expires' => $expires,
            ],
            'id = ?',
            [$userId]
        ) > 0;
    }

    public function findByResetToken(string $token): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()",
            [$token]
        );
    }

    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        return $this->db->update(
            'users',
            ['password_hash' => $hashedPassword],
            'id = ?',
            [$userId]
        ) > 0;
    }

    public function clearPasswordResetToken(int $userId): bool
    {
        return $this->db->update(
            'users',
            [
                'reset_token' => null,
                'reset_expires' => null,
            ],
            'id = ?',
            [$userId]
        ) > 0;
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function updateProfileCompletion(int $userId, int $percentage): void
    {
        $this->db->update(
            'users',
            ['profile_completion' => $percentage],
            'id = ?',
            [$userId]
        );
    }

    public function suspendAccount(int $userId): bool
    {
        return $this->db->update(
            'users',
            ['account_status' => 'suspended'],
            'id = ?',
            [$userId]
        ) > 0;
    }

    public function activateAccount(int $userId): bool
    {
        return $this->db->update(
            'users',
            ['account_status' => 'active'],
            'id = ?',
            [$userId]
        ) > 0;
    }

    public function getAllUsers(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $users = $this->db->fetchAll(
            "SELECT u.*, p.first_name, p.last_name, p.faculty, p.department 
             FROM users u 
             LEFT JOIN profiles p ON u.id = p.user_id 
             ORDER BY u.created_at DESC 
             LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        $total = $this->db->fetch("SELECT COUNT(*) as count FROM users")['count'];

        return [
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage),
        ];
    }

    public function searchUsers(string $query, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $searchTerm = "%{$query}%";
        
        $users = $this->db->fetchAll(
            "SELECT u.*, p.first_name, p.last_name, p.faculty, p.department 
             FROM users u 
             LEFT JOIN profiles p ON u.id = p.user_id 
             WHERE u.email LIKE ? 
                OR p.first_name LIKE ? 
                OR p.last_name LIKE ? 
                OR p.faculty LIKE ? 
                OR p.department LIKE ?
             ORDER BY u.created_at DESC 
             LIMIT ? OFFSET ?",
            [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $perPage, $offset]
        );

        $total = $this->db->fetch(
            "SELECT COUNT(*) as count 
             FROM users u 
             LEFT JOIN profiles p ON u.id = p.user_id 
             WHERE u.email LIKE ? 
                OR p.first_name LIKE ? 
                OR p.last_name LIKE ? 
                OR p.faculty LIKE ? 
                OR p.department LIKE ?",
            [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]
        )['count'];

        return [
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage),
            'query' => $query,
        ];
    }
}
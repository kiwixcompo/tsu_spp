<?php

namespace App\Middleware;

use App\Core\Database;

class Admin
{
    public function handle(): bool
    {
        // Check if user is authenticated first
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            return false;
        }

        // Check if user has admin role
        $db = Database::getInstance();
        $user = $db->fetch(
            "SELECT * FROM users WHERE id = ? AND account_status = 'active'",
            [$_SESSION['user_id']]
        );

        if (!$user) {
            return false;
        }

        // Check if user has admin role
        $isAdmin = isset($user['role']) && $user['role'] === 'admin';

        if (!$isAdmin) {
            // For API requests, return JSON error
            if ($this->isApiRequest()) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Forbidden - Admin access required']);
                exit;
            }
            
            // For web requests, redirect to dashboard
            if (!function_exists('url')) {
                require_once __DIR__ . '/../Helpers/UrlHelper.php';
            }
            header('Location: ' . url('dashboard'));
            exit;
        }

        return true;
    }

    private function isApiRequest(): bool
    {
        $uri = $_SERVER['REQUEST_URI'];
        return strpos($uri, '/api/') === 0;
    }
}
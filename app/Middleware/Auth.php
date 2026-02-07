<?php

namespace App\Middleware;

class Auth
{
    public function handle(): bool
    {
        // Check if user is authenticated
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            // For API requests, return JSON error
            if ($this->isApiRequest()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }
            
            // For web requests, redirect to login
            if (!function_exists('url')) {
                require_once __DIR__ . '/../Helpers/UrlHelper.php';
            }
            header('Location: ' . url('login'));
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
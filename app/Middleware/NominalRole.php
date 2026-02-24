<?php

namespace App\Middleware;

class NominalRole
{
    public function handle()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        // Check if user has nominal_role or admin role
        $allowedRoles = ['nominal_role', 'admin'];
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
            header('Location: ' . url('dashboard'));
            exit;
        }

        return true;
    }
}

<?php

namespace App\Middleware;

class IDCardManager
{
    public function handle(): bool
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        // Check if user has id_card_manager or admin role
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['id_card_manager', 'admin'])) {
            header('Location: ' . url('dashboard'));
            exit;
        }

        return true;
    }
}

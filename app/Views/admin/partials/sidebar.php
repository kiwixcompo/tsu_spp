<?php
// Admin Sidebar Component
$currentPage = $currentPage ?? '';
?>
<div class="admin-sidebar" id="adminSidebar">
    <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
        <i class="fas fa-chevron-left" id="toggleIcon"></i>
    </button>
    <div class="text-center mb-4">
        <h4 class="text-white">
            <i class="fas fa-shield-alt me-2"></i><span class="sidebar-text">Admin Panel</span>
        </h4>
    </div>
    
    <nav class="nav flex-column">
        <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= url('/admin/dashboard') ?>">
            <i class="fas fa-tachometer-alt me-2"></i><span class="sidebar-text">Dashboard</span>
        </a>
        <a class="nav-link <?= $currentPage === 'users' ? 'active' : '' ?>" href="<?= url('/admin/users') ?>">
            <i class="fas fa-users me-2"></i><span class="sidebar-text">Users Management</span>
        </a>
        <a class="nav-link <?= $currentPage === 'publications' ? 'active' : '' ?>" href="<?= url('/admin/publications') ?>">
            <i class="fas fa-book me-2"></i><span class="sidebar-text">Publications</span>
        </a>
        <a class="nav-link <?= $currentPage === 'analytics' ? 'active' : '' ?>" href="<?= url('/admin/analytics') ?>">
            <i class="fas fa-chart-line me-2"></i><span class="sidebar-text">Analytics</span>
        </a>
        <a class="nav-link <?= $currentPage === 'activity-logs' ? 'active' : '' ?>" href="<?= url('/admin/activity-logs') ?>">
            <i class="fas fa-history me-2"></i><span class="sidebar-text">Activity Logs</span>
        </a>
        <a class="nav-link <?= $currentPage === 'faculties-departments' ? 'active' : '' ?>" href="<?= url('/admin/faculties-departments') ?>">
            <i class="fas fa-building me-2"></i><span class="sidebar-text">Faculties & Departments</span>
        </a>
        <a class="nav-link <?= $currentPage === 'units' ? 'active' : '' ?>" href="<?= url('/admin/units') ?>">
            <i class="fas fa-sitemap me-2"></i><span class="sidebar-text">Units & Offices</span>
        </a>
        <a class="nav-link <?= $currentPage === 'settings' ? 'active' : '' ?>" href="<?= url('/admin/settings') ?>">
            <i class="fas fa-cog me-2"></i><span class="sidebar-text">System Settings</span>
        </a>
        <hr class="text-white">
        <a class="nav-link" href="<?= url('/logout') ?>">
            <i class="fas fa-sign-out-alt me-2"></i><span class="sidebar-text">Logout</span>
        </a>
    </nav>
</div>

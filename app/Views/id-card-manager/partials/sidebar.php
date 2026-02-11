<style>
    .sidebar {
        min-height: 100vh;
        background: linear-gradient(180deg, #1e40af 0%, #1e3a8a 100%);
        color: white;
        position: fixed;
        width: 260px;
        padding: 0;
    }
    .sidebar-header {
        padding: 20px;
        background: rgba(0,0,0,0.2);
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .sidebar-menu { padding: 20px 0; }
    .sidebar-menu a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        transition: all 0.3s;
    }
    .sidebar-menu a:hover, .sidebar-menu a.active {
        background: rgba(255,255,255,0.1);
        color: white;
        border-left: 4px solid #f59e0b;
    }
    .sidebar-menu a i { width: 25px; margin-right: 10px; }
    .main-content { margin-left: 260px; padding: 30px; }
</style>

<div class="sidebar">
    <div class="sidebar-header">
        <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>ID Card Manager</h5>
        <small class="text-white-50"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></small>
    </div>
    <div class="sidebar-menu">
        <a href="<?= url('id-card-manager/dashboard') ?>" class="<?= strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="<?= url('id-card-manager/browse') ?>" class="<?= strpos($_SERVER['REQUEST_URI'], 'browse') !== false ? 'active' : '' ?>">
            <i class="fas fa-search"></i> Browse Profiles
        </a>
        <a href="<?= url('id-card-manager/print-history') ?>" class="<?= strpos($_SERVER['REQUEST_URI'], 'print-history') !== false ? 'active' : '' ?>">
            <i class="fas fa-history"></i> Print History
        </a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="<?= url('admin/dashboard') ?>">
            <i class="fas fa-shield-alt"></i> Admin Panel
        </a>
        <?php endif; ?>
        <a href="<?= url('dashboard') ?>">
            <i class="fas fa-user"></i> My Profile
        </a>
        <a href="<?= url('logout') ?>">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

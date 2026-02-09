<style>
    body {
        overflow-x: hidden;
    }
    .admin-sidebar {
        background: #2c3e50;
        min-height: 100vh;
        padding-top: 2rem;
        position: fixed;
        left: 0;
        top: 0;
        width: 250px;
        transition: all 0.3s ease;
        z-index: 1000;
        overflow-y: auto;
    }
    .admin-sidebar.collapsed {
        width: 70px;
    }
    .admin-sidebar.collapsed .sidebar-text {
        display: none;
    }
    .admin-sidebar.collapsed .text-center h4 {
        font-size: 0;
    }
    .admin-sidebar.collapsed .text-center h4 i {
        font-size: 1.5rem;
    }
    .admin-sidebar .nav-link {
        color: #ecf0f1;
        padding: 1rem 1.5rem;
        border-radius: 0;
        margin-bottom: 0.5rem;
        white-space: nowrap;
        transition: all 0.3s ease;
    }
    .admin-sidebar.collapsed .nav-link {
        padding: 1rem 0.5rem;
        text-align: center;
    }
    .admin-sidebar .nav-link:hover,
    .admin-sidebar .nav-link.active {
        background: #34495e;
        color: white;
    }
    .sidebar-toggle {
        position: absolute;
        top: 10px;
        right: -15px;
        background: #2c3e50;
        color: white;
        border: 2px solid #34495e;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 1001;
        transition: all 0.3s ease;
    }
    .sidebar-toggle:hover {
        background: #34495e;
    }
    .main-content {
        margin-left: 250px;
        transition: all 0.3s ease;
        width: calc(100% - 250px);
    }
    .main-content.expanded {
        margin-left: 70px;
        width: calc(100% - 70px);
    }
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    @media (max-width: 768px) {
        .admin-sidebar {
            width: 70px;
        }
        .admin-sidebar .sidebar-text {
            display: none;
        }
        .main-content {
            margin-left: 70px;
            width: calc(100% - 70px);
        }
    }
</style>

<script>
    // Sidebar Toggle Function
    function toggleSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleIcon = document.getElementById('toggleIcon');
        
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        
        if (sidebar.classList.contains('collapsed')) {
            toggleIcon.classList.remove('fa-chevron-left');
            toggleIcon.classList.add('fa-chevron-right');
            localStorage.setItem('sidebarCollapsed', 'true');
        } else {
            toggleIcon.classList.remove('fa-chevron-right');
            toggleIcon.classList.add('fa-chevron-left');
            localStorage.setItem('sidebarCollapsed', 'false');
        }
    }

    // Restore sidebar state on page load
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (sidebarCollapsed) {
            const sidebar = document.getElementById('adminSidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (sidebar && mainContent && toggleIcon) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                toggleIcon.classList.remove('fa-chevron-left');
                toggleIcon.classList.add('fa-chevron-right');
            }
        }
    });
</script>

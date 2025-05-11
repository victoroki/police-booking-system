<!-- Place this right before your closing </body> tag -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ======================
    // 1. DROPDOWN MENU SETUP
    // ======================
    const dropdownElements = document.querySelectorAll('.dropdown-toggle');
    dropdownElements.forEach(function(dropdownToggle) {
        // Initialize Bootstrap dropdown
        const dropdown = new bootstrap.Dropdown(dropdownToggle);
        
        // Fix click behavior on mobile
        dropdownToggle.addEventListener('click', function(e) {
            if (window.innerWidth < 992) { // Mobile breakpoint
                e.preventDefault();
                const menu = this.nextElementSibling;
                menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
            }
        });
    });

    // ======================
    // 2. SIDEBAR TOGGLE SETUP
    // ======================
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Toggle the collapsed class on body
            document.body.classList.toggle('sidebar-collapsed');
            
            // Save state in localStorage
            localStorage.setItem('sidebarCollapsed', 
                document.body.classList.contains('sidebar-collapsed'));
            
            // Dispatch event for other components
            window.dispatchEvent(new Event('sidebarToggled'));
        });
        
        // Initialize from localStorage
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }
    }

    // ======================
    // 3. CLOSE DROPDOWN WHEN CLICKING OUTSIDE
    // ======================
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            const openMenus = document.querySelectorAll('.dropdown-menu.show');
            openMenus.forEach(function(menu) {
                const dropdownInstance = bootstrap.Dropdown.getInstance(
                    menu.previousElementSibling
                );
                if (dropdownInstance) {
                    dropdownInstance.hide();
                }
            });
        }
    });
});
</script>
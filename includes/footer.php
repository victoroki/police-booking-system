<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar
    const menuToggle = document.getElementById('menu-toggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('sidebar-collapsed');
        });
    }

    // Auto-collapse on mobile
    function handleResize() {
        if (window.innerWidth < 768) {
            document.body.classList.add('sidebar-collapsed');
        } else {
            document.body.classList.remove('sidebar-collapsed');
        }
    }

    // Initialize
    handleResize();
    window.addEventListener('resize', handleResize);

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth < 768 && 
            !e.target.closest('.sidebar-wrapper') && 
            !e.target.closest('#menu-toggle')) {
            document.body.classList.add('sidebar-collapsed');
        }
    });
});
</script>
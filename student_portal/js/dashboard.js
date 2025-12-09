// Dropdown Toggle Function
function toggleDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('hidden');
}

// Sidebar Toggle Function (Hamburger Menu)
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const body = document.body;

    // Sidebar එක දැනට හැංගිලා තිබේ නම් (Mobile View එකේදී)
    if (sidebar.classList.contains('-translate-x-full')) {
        // Open Sidebar
        sidebar.classList.remove('-translate-x-full');
        
        // Show Overlay
        overlay.classList.remove('hidden');
        // Small delay to allow display:block to apply before opacity transition
        setTimeout(() => {
            overlay.classList.remove('opacity-0');
        }, 10);
        
        // Prevent scrolling on background when sidebar is open
        body.style.overflow = 'hidden';
    } else {
        // Close Sidebar
        sidebar.classList.add('-translate-x-full');
        
        // Hide Overlay
        overlay.classList.add('opacity-0');
        setTimeout(() => {
            overlay.classList.add('hidden');
        }, 300); // Wait for transition duration
        
        // Restore scrolling
        body.style.overflow = 'auto';
    }
}

// පිටත Click කළ විට Sidebar හෝ Dropdown වැසීමට (Event Listener)
window.addEventListener('click', function(e) {
    // 1. Dropdown Close Logic
    const dropdown = document.getElementById('userDropdown');
    const button = document.querySelector('button[onclick="toggleDropdown()"]');
    
    // Click කළේ Button එකේවත් Dropdown එකේවත් නොවේ නම්, Dropdown එක වහන්න
    if (button && dropdown && !button.contains(e.target) && !dropdown.contains(e.target)) {
        if (!dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
        }
    }
});

// Mobile View එකේදි Window Resize කළොත් Sidebar එක හරි තැනට එන්න (Optional Fix)
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (window.innerWidth >= 768) { // md breakpoint
        // Desktop වලදී Overlay එක හංගන්න
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.classList.add('opacity-0');
        }
        document.body.style.overflow = 'auto';
    }
});


tailwind.config = {
     theme: {
         extend: {
             colors: {
                 primary: '#4F46E5',
                secondary: '#1E293B',
                }
            }
     }
}        

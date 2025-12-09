function toggleDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('hidden');
    }

    // වෙන තැනක Click කළාම Dropdown එක වැහෙන්න හදන්න
    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('userDropdown');
        const button = document.querySelector('button[onclick="toggleDropdown()"]');
        
        // Click කළේ Button එකේවත්, Dropdown එකේවත් නෙවෙයි නම් විතරක් වහන්න
        if (!button.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
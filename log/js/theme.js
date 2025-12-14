/**
 * Theme Switcher Logic
 * Handles toggling between light and dark modes and persisting preference.
 */

// On page load or when changing themes, best to add inline in the head to avoid FOUC
if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
} else {
    document.documentElement.classList.remove('dark');
}

function toggleTheme() {
    if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    } else {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    }
    updateThemeIcon();
}

function updateThemeIcon() {
    const icon = document.getElementById('theme-toggle-icon');
    if (!icon) return;

    if (document.documentElement.classList.contains('dark')) {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
    } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
    }
}

// Run update on load
document.addEventListener('DOMContentLoaded', updateThemeIcon);

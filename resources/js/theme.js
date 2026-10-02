/**
 * Dark Mode Theme Manager
 * Handles dark mode toggle with localStorage persistence
 */

const THEME_KEY = 'app-theme';
const DARK_CLASS = 'dark';

/**
 * Initialize theme on page load
 */
function initializeTheme() {
    const theme = getStoredTheme() || getSystemTheme();
    applyTheme(theme);
}

/**
 * Get stored theme preference from localStorage
 */
function getStoredTheme() {
    return localStorage.getItem(THEME_KEY);
}

/**
 * Get system theme preference
 */
function getSystemTheme() {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        return 'dark';
    }
    return 'light';
}

/**
 * Apply theme to the document
 */
function applyTheme(theme) {
    const html = document.documentElement;
    
    if (theme === 'dark') {
        html.classList.add(DARK_CLASS);
    } else {
        html.classList.remove(DARK_CLASS);
    }
    
    localStorage.setItem(THEME_KEY, theme);
    window.currentTheme = theme;
}

/**
 * Toggle dark mode
 */
function toggleDarkMode() {
    const currentTheme = window.currentTheme || getStoredTheme() || getSystemTheme();
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    applyTheme(newTheme);
    
    // Dispatch custom event for reactive updates
    window.dispatchEvent(
        new CustomEvent('theme-changed', { detail: { theme: newTheme } })
    );
}

/**
 * Get current theme
 */
function getCurrentTheme() {
    return window.currentTheme || getStoredTheme() || getSystemTheme();
}

/**
 * Listen for system theme changes
 */
function listenForSystemThemeChanges() {
    if (!window.matchMedia) return;
    
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        const storedTheme = getStoredTheme();
        
        // Only apply system theme if user hasn't manually set a preference
        if (!storedTheme) {
            const newTheme = e.matches ? 'dark' : 'light';
            applyTheme(newTheme);
        }
    });
}

// Export functions for use in Alpine.js and other scripts
window.themeManager = {
    initialize: initializeTheme,
    toggle: toggleDarkMode,
    getCurrent: getCurrentTheme,
    apply: applyTheme,
};

// Initialize theme when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeTheme);
} else {
    initializeTheme();
}

// Listen for system theme changes
listenForSystemThemeChanges();

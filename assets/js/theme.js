(function() {
    const THEME_KEY = 'k86_theme_preference';
    
    // Function to apply theme correctly
    const applyTheme = (theme) => {
        if (theme === 'light') {
            document.documentElement.classList.add('light-mode');
        } else {
            document.documentElement.classList.remove('light-mode');
        }
        localStorage.setItem(THEME_KEY, theme);
        
        // Dispatch event for components that need to react
        window.dispatchEvent(new CustomEvent('k86-theme-changed', { detail: { theme } }));
    };

    // Initialize theme from storage or default to dark
    const storedTheme = localStorage.getItem(THEME_KEY) || 'dark';
    applyTheme(storedTheme);

    // Global toggle function
    window.toggleK86Theme = () => {
        const currentTheme = document.documentElement.classList.contains('light-mode') ? 'light' : 'dark';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        applyTheme(newTheme);
    };
})();

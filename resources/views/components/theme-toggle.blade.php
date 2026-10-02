@props(['class' => ''])

<button 
    @click="themeManager.toggle()"
    x-data="{ 
        isDark: function() { return document.documentElement.classList.contains('dark') },
        init() {
            window.addEventListener('theme-changed', () => { this.$el.classList.toggle('dark-toggle-active'); });
        }
    }"
    x-init="init()"
    class="relative inline-flex items-center justify-center w-10 h-10 rounded-lg transition-all duration-300 {{ $class }} group
        bg-slate-100 hover:bg-slate-200 text-slate-700
        dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300"
    title="Toggle dark mode"
    aria-label="Toggle dark mode">
    
    <!-- Sun icon (shown in light mode) -->
    <svg 
        xmlns="http://www.w3.org/2000/svg" 
        class="absolute w-5 h-5 transition-all duration-300 rotate-0 scale-100 dark:rotate-90 dark:scale-0" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1m-16 0H1m15.364 1.636l.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
    </svg>
    
    <!-- Moon icon (shown in dark mode) -->
    <svg 
        xmlns="http://www.w3.org/2000/svg" 
        class="absolute w-5 h-5 transition-all duration-300 -rotate-90 scale-0 dark:rotate-0 dark:scale-100" 
        fill="currentColor" 
        viewBox="0 0 20 20">
        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
    </svg>
</button>

/**
 * AURA Theme Switcher
 * Este script maneja el cambio de temas de colores y el modo claro/oscuro
 */

// Clase principal para la gestión de temas
class ThemeManager {
    constructor() {
        // Valores predeterminados
        this.defaultTheme = 'light';
        this.defaultColorTheme = 'default';
        
        // Inicializar tema desde localStorage o usar predeterminado
        this.currentTheme = localStorage.getItem('aura-theme') || this.defaultTheme;
        this.currentColorTheme = localStorage.getItem('aura-color-theme') || this.defaultColorTheme;
        
        // Inicializar al cargar la página
        this.initialize();
        
        // Vincular métodos al contexto actual
        this.toggleTheme = this.toggleTheme.bind(this);
        this.setColorTheme = this.setColorTheme.bind(this);
    }
    
    // Inicializar la gestión de temas
    initialize() {
        // Aplicar tema guardado
        document.documentElement.setAttribute('data-theme', this.currentTheme);
        
        // Aplicar tema de color guardado
        if (this.currentColorTheme !== 'default') {
            document.documentElement.setAttribute('data-color-theme', this.currentColorTheme);
        } else {
            document.documentElement.removeAttribute('data-color-theme');
        }
        
        // Actualizar estado de los controles de tema
        this.updateThemeControls();
        
        // Agregar listeners para botones y controles
        this.setupEventListeners();
    }
    
    // Alternar entre tema claro y oscuro
    toggleTheme() {
        // Cambiar tema
        this.currentTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        
        // Guardar preferencia
        localStorage.setItem('aura-theme', this.currentTheme);
        
        // Aplicar tema
        document.documentElement.setAttribute('data-theme', this.currentTheme);
        
        // Actualizar controles
        this.updateThemeControls();
    }
    
    // Establecer un tema de color específico
    setColorTheme(theme) {
        // Actualizar tema actual
        this.currentColorTheme = theme;
        
        // Guardar preferencia
        localStorage.setItem('aura-color-theme', theme);
        
        // Aplicar tema de color
        if (theme !== 'default') {
            document.documentElement.setAttribute('data-color-theme', theme);
        } else {
            document.documentElement.removeAttribute('data-color-theme');
        }
        
        // Actualizar controles
        this.updateThemeControls();
    }
    
    // Actualizar estado de controles de selección de tema
    updateThemeControls() {
        // Actualizar toggle de modo oscuro
        const darkModeToggle = document.getElementById('darkModeToggle');
        if (darkModeToggle) {
            darkModeToggle.checked = this.currentTheme === 'dark';
        }
        
        // Actualizar botones de selección de color
        const colorButtons = document.querySelectorAll('.color-theme-btn');
        colorButtons.forEach(button => {
            const themeValue = button.getAttribute('data-theme');
            if (themeValue === this.currentColorTheme) {
                button.classList.add('active');
            } else {
                button.classList.remove('active');
            }
        });
        
        // Actualizar iconos de tema
        const themeIcon = document.getElementById('themeIcon');
        if (themeIcon) {
            if (this.currentTheme === 'dark') {
                themeIcon.classList.remove('icon-sun');
                themeIcon.classList.add('icon-moon');
            } else {
                themeIcon.classList.remove('icon-moon');
                themeIcon.classList.add('icon-sun');
            }
        }
    }
    
    // Configurar listeners de eventos
    setupEventListeners() {
        // Listener para toggle de modo oscuro
        const darkModeToggle = document.getElementById('darkModeToggle');
        if (darkModeToggle) {
            darkModeToggle.addEventListener('change', this.toggleTheme);
        }
        
        // Listener para botón de tema (en header o sidebar)
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', this.toggleTheme);
        }
        
        // Listeners para botones de selección de color
        const colorButtons = document.querySelectorAll('.color-theme-btn');
        colorButtons.forEach(button => {
            button.addEventListener('click', () => {
                const theme = button.getAttribute('data-theme');
                this.setColorTheme(theme);
            });
        });
    }
}

// Inicializar gestor de temas cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.themeManager = new ThemeManager();
});

// Exponer funciones globalmente para uso desde botones HTML
window.toggleTheme = function() {
    if (window.themeManager) {
        window.themeManager.toggleTheme();
    }
};

window.setColorTheme = function(theme) {
    if (window.themeManager) {
        window.themeManager.setColorTheme(theme);
    }
};

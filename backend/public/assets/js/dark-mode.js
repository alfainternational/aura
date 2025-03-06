/**
 * AURA Dark Mode Manager
 * Sistema mejorado para gestionar el modo oscuro y claro
 */

class DarkModeManager {
    constructor() {
        // Elementos DOM
        this.toggleBtn = document.getElementById('themeToggleBtn');
        this.themeIcon = document.getElementById('themeIcon');
        
        // Valores predeterminados
        this.defaultTheme = 'light';
        this.storageKey = 'aura-theme';
        
        // Inicializar
        this.init();
    }
    
    init() {
        // Obtener tema de las cookies, localStorage o preferencia del sistema
        this.currentTheme = this.getThemePreference();
        
        // Aplicar tema inicial
        this.applyTheme(this.currentTheme);
        
        // Configurar eventos
        this.setupEventListeners();
        
        // Observador para cambios en preferencias del sistema
        this.setupSystemPreferenceObserver();
    }
    
    getThemePreference() {
        // Prioridad: 1. Cookie (configuración del servidor), 2. localStorage, 3. Preferencia del sistema
        const cookieTheme = this.getCookie('aura_theme');
        const localTheme = localStorage.getItem(this.storageKey);
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (cookieTheme && ['light', 'dark', 'system'].includes(cookieTheme)) {
            return cookieTheme === 'system' 
                ? (systemPrefersDark ? 'dark' : 'light')
                : cookieTheme;
        }
        
        if (localTheme) {
            return localTheme;
        }
        
        return systemPrefersDark ? 'dark' : 'light';
    }
    
    applyTheme(theme) {
        // Guardar tema actual
        this.currentTheme = theme;
        
        // Aplicar al documento
        document.documentElement.setAttribute('data-theme', theme);
        
        // Actualizar icono
        this.updateThemeIcon(theme);
        
        // Guardar en localStorage
        localStorage.setItem(this.storageKey, theme);
        
        // Disparar evento personalizado
        document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
    }
    
    toggleTheme() {
        const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        this.applyTheme(newTheme);
        
        // Si el usuario está autenticado, guardar en el servidor
        this.saveThemePreference(newTheme);
    }
    
    updateThemeIcon(theme) {
        if (!this.themeIcon) return;
        
        if (theme === 'dark') {
            this.themeIcon.classList.remove('fa-sun');
            this.themeIcon.classList.add('fa-moon');
        } else {
            this.themeIcon.classList.remove('fa-moon');
            this.themeIcon.classList.add('fa-sun');
        }
    }
    
    setupEventListeners() {
        // Botón de alternar tema
        if (this.toggleBtn) {
            this.toggleBtn.addEventListener('click', () => this.toggleTheme());
        }
        
        // Botones de radio para selección de tema
        const themeRadios = document.querySelectorAll('input[name="theme"]');
        themeRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.checked) {
                    const selectedTheme = e.target.value;
                    if (selectedTheme === 'system') {
                        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                        this.applyTheme(systemTheme);
                    } else {
                        this.applyTheme(selectedTheme);
                    }
                    
                    // Guardar preferencia
                    this.saveThemePreference(selectedTheme);
                }
            });
        });
    }
    
    setupSystemPreferenceObserver() {
        // Observar cambios en la preferencia del sistema
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        
        mediaQuery.addEventListener('change', e => {
            // Solo cambiar si el tema está configurado como 'system'
            const cookieTheme = this.getCookie('aura_theme');
            if (cookieTheme === 'system') {
                this.applyTheme(e.matches ? 'dark' : 'light');
            }
        });
    }
    
    saveThemePreference(theme) {
        // Guardar en el servidor si el usuario está autenticado y existe la ruta
        const updateThemeUrl = document.querySelector('meta[name="theme-update-url"]')?.getAttribute('content');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (updateThemeUrl && csrfToken) {
            fetch(updateThemeUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ theme })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Tema actualizado en el servidor:', data);
            })
            .catch(error => {
                console.error('Error al actualizar el tema:', error);
            });
        }
    }
    
    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.darkModeManager = new DarkModeManager();
});

// Este código se ejecuta cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tema desde localStorage o usar predeterminado
    const savedTheme = localStorage.getItem('aura-theme') || 'light';
    const savedColorTheme = localStorage.getItem('aura-color-theme') || 'default';
    
    // Aplicar tema actual al documento
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    // Aplicar tema de color si no es el predeterminado
    if (savedColorTheme !== 'default') {
        document.documentElement.setAttribute('data-color-theme', savedColorTheme);
    }
    
    // Actualizar el icono del botón de tema
    const themeIcon = document.getElementById('themeIcon');
    if (themeIcon) {
        themeIcon.className = savedTheme === 'dark' ? 'icon-moon' : 'icon-sun';
    }
    
    // Configurar botón de cambio de tema en el header
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            // Cambiar tema
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('aura-theme', newTheme);
            
            // Actualizar icono
            if (themeIcon) {
                themeIcon.className = newTheme === 'dark' ? 'icon-moon' : 'icon-sun';
            }
        });
    }
});

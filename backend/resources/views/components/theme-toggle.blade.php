<div class="theme-toggle">
    <button id="themeToggleBtn" class="btn btn-sm btn-link text-muted p-0" title="تبديل المظهر">
        <i id="themeIcon" class="fas fa-sun"></i>
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const themeIcon = document.getElementById('themeIcon');
    
    // Obtener tema actual
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    
    // Actualizar icono según el tema actual
    updateThemeIcon(currentTheme);
    
    // Agregar evento para cambiar el tema
    themeToggleBtn.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        // Cambiar tema en el documento
        document.documentElement.setAttribute('data-theme', newTheme);
        
        // Actualizar icono
        updateThemeIcon(newTheme);
        
        // Guardar preferencia en localStorage
        localStorage.setItem('aura-theme', newTheme);
        
        // Guardar preferencia en el servidor si el usuario está autenticado
        @auth
        fetch('{{ route("profile.ui-settings.update-theme") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ theme: newTheme })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Tema actualizado en el servidor');
            }
        })
        .catch(error => {
            console.error('Error al actualizar el tema:', error);
        });
        @endauth
    });
    
    // Función para actualizar el icono según el tema
    function updateThemeIcon(theme) {
        if (theme === 'dark') {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        } else {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }
    }
});
</script>

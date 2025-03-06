/**
 * Dashboard JavaScript
 * 
 * Este archivo contiene funciones para mejorar la experiencia de usuario
 * en el dashboard de la aplicación.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar todos los tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Inicializar todos los popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Manejar el clic en notificaciones para marcarlas como leídas
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (this.classList.contains('unread')) {
                // Aquí se podría hacer una llamada AJAX para marcar como leída
                this.classList.remove('unread');
                
                // Actualizar el contador de notificaciones
                const badge = document.querySelector('#notificationsDropdown .badge');
                if (badge) {
                    const count = parseInt(badge.textContent) - 1;
                    if (count > 0) {
                        badge.textContent = count;
                    } else {
                        badge.remove();
                    }
                }
            }
        });
    });

    // Función para mostrar mensajes de alerta
    window.showAlert = function(message, type = 'success') {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) return;

        const alertElement = document.createElement('div');
        alertElement.className = `alert alert-${type} alert-dismissible fade show`;
        alertElement.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        alertContainer.appendChild(alertElement);

        // Auto-cerrar después de 5 segundos
        setTimeout(() => {
            const alert = new bootstrap.Alert(alertElement);
            alert.close();
        }, 5000);
    };

    // Manejar envío de formularios con AJAX
    const ajaxForms = document.querySelectorAll('.ajax-form');
    ajaxForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Mostrar indicador de carga
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري المعالجة...';
            
            // Recopilar datos del formulario
            const formData = new FormData(this);
            
            // Enviar solicitud AJAX
            fetch(this.action, {
                method: this.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                
                // Manejar respuesta
                if (data.success) {
                    showAlert(data.message || 'تمت العملية بنجاح', 'success');
                    
                    // Redirigir si es necesario
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1000);
                    }
                    
                    // Resetear formulario si es necesario
                    if (data.reset) {
                        this.reset();
                    }
                } else {
                    showAlert(data.message || 'حدث خطأ أثناء المعالجة', 'danger');
                }
            })
            .catch(error => {
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                
                // Mostrar error
                showAlert('حدث خطأ أثناء الاتصال بالخادم', 'danger');
                console.error('Error:', error);
            });
        });
    });

    // Manejar confirmaciones
    const confirmButtons = document.querySelectorAll('[data-confirm]');
    confirmButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const message = this.getAttribute('data-confirm') || 'هل أنت متأكد من أنك تريد القيام بهذا الإجراء؟';
            
            if (confirm(message)) {
                // Si hay un formulario, enviarlo
                if (this.getAttribute('data-form')) {
                    document.getElementById(this.getAttribute('data-form')).submit();
                } else if (this.getAttribute('href')) {
                    // Si hay un enlace, seguirlo
                    window.location.href = this.getAttribute('href');
                }
            }
        });
    });

    // Manejar tabs con almacenamiento local
    const tabElements = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabElements.forEach(tabEl => {
        tabEl.addEventListener('shown.bs.tab', function (e) {
            const id = this.getAttribute('data-bs-target');
            const storageKey = 'activeTab_' + document.location.pathname;
            localStorage.setItem(storageKey, id);
        });
    });

    // Restaurar tab activo desde almacenamiento local
    const activeTab = localStorage.getItem('activeTab_' + document.location.pathname);
    if (activeTab) {
        const tab = new bootstrap.Tab(document.querySelector(`[data-bs-target="${activeTab}"]`));
        tab.show();
    }

    // Manejar cambio de tema
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Actualizar icono
            this.querySelector('i').className = newTheme === 'light' ? 'bi bi-moon' : 'bi bi-sun';
        });
    }

    // Inicializar tema desde almacenamiento local
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        document.documentElement.setAttribute('data-theme', savedTheme);
        if (themeToggle) {
            themeToggle.querySelector('i').className = savedTheme === 'light' ? 'bi bi-moon' : 'bi bi-sun';
        }
    }
});

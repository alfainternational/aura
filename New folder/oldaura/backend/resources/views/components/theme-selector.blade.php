<div class="theme-selector">
    <!-- Contenedor del selector de tema -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="themeCustomizer" aria-labelledby="themeCustomizerLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="themeCustomizerLabel">تخصيص المظهر</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <!-- Modo oscuro / claro -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3">وضع العرض</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <span>الوضع الليلي</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="darkModeToggle">
                        <label class="form-check-label" for="darkModeToggle"></label>
                    </div>
                </div>
            </div>
            
            <!-- Selector de tema de color -->
            <div class="mt-4">
                <h6 class="fw-bold mb-3">لون القالب</h6>
                <div class="theme-colors">
                    <div class="d-flex flex-wrap gap-2">
                        <!-- Color predeterminado -->
                        <button type="button" class="color-theme-btn default-theme" data-theme="default" title="الألوان الافتراضية" onclick="setColorTheme('default')">
                            <span class="color-circle" style="background: linear-gradient(45deg, #6c63ff, #f50057);"></span>
                        </button>
                        
                        <!-- Tema azul -->
                        <button type="button" class="color-theme-btn blue-theme" data-theme="blue" title="الأزرق" onclick="setColorTheme('blue')">
                            <span class="color-circle" style="background: linear-gradient(45deg, #1565c0, #0288d1);"></span>
                        </button>
                        
                        <!-- Tema verde -->
                        <button type="button" class="color-theme-btn green-theme" data-theme="green" title="الأخضر" onclick="setColorTheme('green')">
                            <span class="color-circle" style="background: linear-gradient(45deg, #2e7d32, #00897b);"></span>
                        </button>
                        
                        <!-- Tema morado -->
                        <button type="button" class="color-theme-btn purple-theme" data-theme="purple" title="الأرجواني" onclick="setColorTheme('purple')">
                            <span class="color-circle" style="background: linear-gradient(45deg, #6a1b9a, #8e24aa);"></span>
                        </button>
                        
                        <!-- Tema naranja -->
                        <button type="button" class="color-theme-btn orange-theme" data-theme="orange" title="البرتقالي" onclick="setColorTheme('orange')">
                            <span class="color-circle" style="background: linear-gradient(45deg, #e65100, #ef6c00);"></span>
                        </button>
                        
                        <!-- Tema rojo -->
                        <button type="button" class="color-theme-btn red-theme" data-theme="red" title="الأحمر" onclick="setColorTheme('red')">
                            <span class="color-circle" style="background: linear-gradient(45deg, #c62828, #d32f2f);"></span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Información adicional -->
            <div class="theme-info mt-5">
                <p class="text-muted small">سيتم حفظ تفضيلاتك تلقائيًا وتطبيقها في زياراتك المستقبلية.</p>
            </div>
            
            <!-- Restablecer configuración -->
            <div class="d-grid gap-2 mt-3">
                <button class="btn btn-outline-secondary" onclick="resetThemeSettings()">
                    إعادة تعيين إلى الإعدادات الافتراضية
                </button>
                <button class="btn btn-primary" data-bs-dismiss="offcanvas">
                    حفظ التفضيلات
                </button>
            </div>
        </div>
    </div>
    
    <!-- Botón flotante para abrir el selector de tema -->
    <button class="btn btn-primary rounded-circle shadow theme-selector-toggle" 
            type="button" 
            data-bs-toggle="offcanvas" 
            data-bs-target="#themeCustomizer" 
            aria-controls="themeCustomizer"
            title="تخصيص المظهر">
        <i class="fas fa-palette"></i>
    </button>
</div>

<style>
    /* Estilos para el selector de tema */
    .theme-selector-toggle {
        position: fixed;
        bottom: 20px;
        left: 20px;
        width: 50px;
        height: 50px;
        z-index: 1050;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .theme-selector-toggle:hover {
        transform: rotate(30deg);
    }
    
    .color-theme-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid transparent;
        background: transparent;
        padding: 3px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .color-theme-btn:hover {
        transform: scale(1.1);
    }
    
    .color-theme-btn.active {
        border-color: var(--primary-color);
    }
    
    .color-circle {
        display: block;
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }
    
    /* Estilizar el switch de modo oscuro */
    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
    }
    
    /* Customizador de tema */
    #themeCustomizer {
        width: 300px;
    }
</style>

<script>
    // Función para restablecer la configuración del tema
    function resetThemeSettings() {
        localStorage.removeItem('aura-theme');
        localStorage.removeItem('aura-color-theme');
        location.reload();
    }
</script>

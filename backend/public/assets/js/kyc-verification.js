/**
 * KYC Verification Helper Functions
 * 
 * Este archivo contiene funciones para mejorar la experiencia de usuario
 * durante el proceso de verificación KYC.
 */

// Objeto principal para las funciones de KYC
const KycVerification = {
    
    /**
     * Inicializa las funcionalidades de KYC
     */
    init: function() {
        this.setupFilePreview();
        this.setupFormValidation();
        this.setupStatusIndicators();
    },

    /**
     * Configura la previsualización de archivos subidos
     */
    setupFilePreview: function() {
        const fileInputs = document.querySelectorAll('.custom-file-input');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', function(e) {
                const fileName = this.files[0]?.name || 'Ningún archivo seleccionado';
                const previewElement = this.parentElement.querySelector('.file-preview');
                const fileNameElement = this.parentElement.querySelector('.file-name');
                
                if (fileNameElement) {
                    fileNameElement.textContent = fileName;
                }
                
                // Si es una imagen, mostrar vista previa
                if (this.files[0] && this.files[0].type.match('image.*')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        if (previewElement) {
                            previewElement.style.backgroundImage = `url(${e.target.result})`;
                            previewElement.classList.add('has-preview');
                        }
                    };
                    
                    reader.readAsDataURL(this.files[0]);
                } else {
                    if (previewElement) {
                        previewElement.style.backgroundImage = '';
                        previewElement.classList.remove('has-preview');
                    }
                }
            });
        });
    },

    /**
     * Configura la validación de formularios
     */
    setupFormValidation: function() {
        const kycForm = document.getElementById('kyc-form');
        
        if (kycForm) {
            kycForm.addEventListener('submit', function(e) {
                const requiredInputs = this.querySelectorAll('[required]');
                let isValid = true;
                
                requiredInputs.forEach(input => {
                    if (!input.value) {
                        isValid = false;
                        input.classList.add('is-invalid');
                        
                        // Añadir mensaje de error si no existe
                        const errorContainer = input.parentElement.querySelector('.invalid-feedback');
                        if (!errorContainer) {
                            const errorMessage = document.createElement('div');
                            errorMessage.className = 'invalid-feedback';
                            errorMessage.textContent = 'Este campo es obligatorio.';
                            input.parentElement.appendChild(errorMessage);
                        }
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    // Desplazarse al primer error
                    const firstError = document.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                } else {
                    // Mostrar indicador de carga
                    const submitBtn = this.querySelector('[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...';
                    }
                }
            });
            
            // Eliminar clase de error al cambiar el valor
            kycForm.querySelectorAll('input, select, textarea').forEach(element => {
                element.addEventListener('input', function() {
                    if (this.value) {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        }
    },

    /**
     * Configura los indicadores de estado de KYC
     */
    setupStatusIndicators: function() {
        const statusSteps = document.querySelectorAll('.kyc-status-step');
        
        if (statusSteps.length) {
            // Animar la aparición de los pasos
            statusSteps.forEach((step, index) => {
                setTimeout(() => {
                    step.classList.add('active');
                }, 100 * index);
            });
        }
    },

    /**
     * Muestra el modal de KYC requerido
     */
    showRequiredModal: function() {
        const kycModal = new bootstrap.Modal(document.getElementById('kycRequiredModal'));
        if (kycModal) {
            kycModal.show();
        }
    }
};

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    KycVerification.init();
});

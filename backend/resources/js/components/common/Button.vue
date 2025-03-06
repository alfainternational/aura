<template>
  <button
    :type="type"
    :class="buttonClasses"
    :disabled="disabled || loading"
    @click="$emit('click', $event)"
  >
    <!-- Icono de carga si está en estado loading -->
    <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
    
    <!-- Icono a la izquierda si se proporciona -->
    <i v-if="icon && !iconRight" :class="icon" class="me-2"></i>
    
    <!-- Contenido del botón -->
    <slot></slot>
    
    <!-- Icono a la derecha si se proporciona -->
    <i v-if="icon && iconRight" :class="icon" class="ms-2"></i>
  </button>
</template>

<script>
export default {
  name: 'AppButton',
  props: {
    /**
     * Tipo de botón
     */
    type: {
      type: String,
      default: 'button'
    },
    /**
     * Variante de estilo (primary, secondary, success, danger, warning, info, light, dark)
     */
    variant: {
      type: String,
      default: 'primary'
    },
    /**
     * Tamaño del botón (sm, md, lg)
     */
    size: {
      type: String,
      default: 'md'
    },
    /**
     * Si el botón es de tipo outline
     */
    outline: {
      type: Boolean,
      default: false
    },
    /**
     * Si el botón está deshabilitado
     */
    disabled: {
      type: Boolean,
      default: false
    },
    /**
     * Si el botón está en estado de carga
     */
    loading: {
      type: Boolean,
      default: false
    },
    /**
     * Si el botón debe ser de ancho completo
     */
    block: {
      type: Boolean,
      default: false
    },
    /**
     * Clase de icono (por ejemplo, 'fas fa-save')
     */
    icon: {
      type: String,
      default: ''
    },
    /**
     * Si el icono debe estar a la derecha
     */
    iconRight: {
      type: Boolean,
      default: false
    },
    /**
     * Clases adicionales para el botón
     */
    customClass: {
      type: String,
      default: ''
    },
    /**
     * Si el botón debe ser redondeado
     */
    rounded: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    /**
     * Construir las clases del botón
     */
    buttonClasses() {
      return [
        'btn',
        this.outline ? `btn-outline-${this.variant}` : `btn-${this.variant}`,
        this.size === 'sm' ? 'btn-sm' : '',
        this.size === 'lg' ? 'btn-lg' : '',
        this.block ? 'w-100' : '',
        this.rounded ? 'rounded-pill' : '',
        this.customClass
      ];
    }
  }
};
</script>

<style scoped>
.btn {
  position: relative;
  overflow: hidden;
  transition: all 0.2s;
}

.btn:active {
  transform: scale(0.97);
}

/* Efecto sutil de onda en click */
.btn::after {
  content: '';
  position: absolute;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.2);
  top: 0;
  left: 0;
  transform: scale(0);
  opacity: 0;
  pointer-events: none;
  border-radius: inherit;
}

.btn:active::after {
  transform: scale(1);
  opacity: 1;
  transition: transform 0.2s ease-out, opacity 0.2s ease-out;
}
</style>

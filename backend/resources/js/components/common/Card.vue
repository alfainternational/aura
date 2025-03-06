<template>
  <div :class="['card', customClass]" :style="cardStyle">
    <!-- Imagen de la tarjeta (si se proporciona) -->
    <img v-if="imgSrc" :src="imgSrc" class="card-img-top" :alt="imgAlt">
    
    <!-- Encabezado de la tarjeta (si se proporciona) -->
    <div v-if="$slots.header || title" :class="['card-header', headerClass]">
      <slot name="header">
        <h5 v-if="title" class="card-title mb-0">{{ title }}</h5>
      </slot>
    </div>
    
    <!-- Cuerpo de la tarjeta -->
    <div :class="['card-body', bodyClass]">
      <!-- Si se proporciona title y no hay header, mostrarlo aquí -->
      <h5 v-if="title && !$slots.header" class="card-title">{{ title }}</h5>
      
      <!-- Si se proporciona subtitle -->
      <h6 v-if="subtitle" class="card-subtitle mb-2 text-muted">{{ subtitle }}</h6>
      
      <!-- Contenido principal -->
      <slot></slot>
    </div>
    
    <!-- Pie de la tarjeta (si se proporciona) -->
    <div v-if="$slots.footer" :class="['card-footer', footerClass]">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AppCard',
  props: {
    /**
     * Título de la tarjeta
     */
    title: {
      type: String,
      default: ''
    },
    /**
     * Subtítulo de la tarjeta
     */
    subtitle: {
      type: String,
      default: ''
    },
    /**
     * URL de la imagen (si se quiere mostrar)
     */
    imgSrc: {
      type: String,
      default: ''
    },
    /**
     * Texto alternativo para la imagen
     */
    imgAlt: {
      type: String,
      default: 'Card image'
    },
    /**
     * Clase personalizada para la tarjeta
     */
    customClass: {
      type: String,
      default: ''
    },
    /**
     * Clase personalizada para el encabezado
     */
    headerClass: {
      type: String,
      default: ''
    },
    /**
     * Clase personalizada para el cuerpo
     */
    bodyClass: {
      type: String,
      default: ''
    },
    /**
     * Clase personalizada para el pie
     */
    footerClass: {
      type: String,
      default: ''
    },
    /**
     * Si la tarjeta debe tener sombra
     */
    shadow: {
      type: Boolean,
      default: false
    },
    /**
     * Si la tarjeta debe tener bordes
     */
    border: {
      type: Boolean,
      default: true
    },
    /**
     * Color de borde (primary, secondary, success, danger, warning, info)
     */
    borderColor: {
      type: String,
      default: ''
    },
    /**
     * Ancho personalizado
     */
    width: {
      type: String,
      default: ''
    },
    /**
     * Altura personalizada
     */
    height: {
      type: String,
      default: ''
    }
  },
  computed: {
    /**
     * Estilos computados para la tarjeta
     */
    cardStyle() {
      return {
        width: this.width || null,
        height: this.height || null,
        boxShadow: this.shadow ? '0 4px 12px rgba(0, 0, 0, 0.1)' : null,
        border: !this.border ? 'none' : null,
        borderColor: this.borderColor ? `var(--bs-${this.borderColor})` : null
      };
    }
  }
};
</script>

<style scoped>
.card {
  transition: all 0.2s ease-in-out;
  margin-bottom: 1rem;
}

.card-title {
  font-weight: 600;
}

.card-subtitle {
  font-weight: 500;
}

/* Efecto hover opcional para tarjetas con sombra */
.card[style*="box-shadow"]:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
}

/* Estilos para tarjetas con colores de borde */
.card[style*="--bs-primary"] {
  border-right: none;
  border-bottom: none;
  border-top: none;
  border-left: 3px solid;
}

.card[style*="--bs-secondary"] {
  border-right: none;
  border-bottom: none;
  border-top: none;
  border-left: 3px solid;
}

.card[style*="--bs-success"] {
  border-right: none;
  border-bottom: none;
  border-top: none;
  border-left: 3px solid;
}

.card[style*="--bs-danger"] {
  border-right: none;
  border-bottom: none;
  border-top: none;
  border-left: 3px solid;
}

.card[style*="--bs-warning"] {
  border-right: none;
  border-bottom: none;
  border-top: none;
  border-left: 3px solid;
}

.card[style*="--bs-info"] {
  border-right: none;
  border-bottom: none;
  border-top: none;
  border-left: 3px solid;
}
</style>

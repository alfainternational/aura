<template>
  <div class="form-group mb-3">
    <label v-if="label" :for="inputId" class="form-label">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>
    
    <div class="input-group" :class="{ 'has-validation': error }">
      <!-- Prepend Add-on si existe -->
      <span v-if="prependText || prependIcon" class="input-group-text">
        <i v-if="prependIcon" :class="prependIcon"></i>
        <span v-if="prependText">{{ prependText }}</span>
      </span>
      
      <!-- Input según el tipo -->
      <template v-if="type === 'textarea'">
        <textarea
          :id="inputId"
          v-model="inputValue"
          :class="inputClasses"
          :placeholder="placeholder"
          :rows="rows"
          :disabled="disabled"
          :readonly="readonly"
          @input="updateValue"
          @blur="onBlur"
          @focus="onFocus"
        ></textarea>
      </template>
      
      <template v-else-if="type === 'select'">
        <select
          :id="inputId"
          v-model="inputValue"
          :class="inputClasses"
          :disabled="disabled"
          :multiple="multiple"
          @change="updateValue"
          @blur="onBlur"
          @focus="onFocus"
        >
          <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
          <option v-for="option in options" :key="option.value" :value="option.value">
            {{ option.label }}
          </option>
        </select>
      </template>
      
      <template v-else>
        <input
          :id="inputId"
          :type="type"
          v-model="inputValue"
          :class="inputClasses"
          :placeholder="placeholder"
          :min="min"
          :max="max"
          :step="step"
          :maxlength="maxlength"
          :disabled="disabled"
          :readonly="readonly"
          :autocomplete="autocomplete"
          @input="updateValue"
          @blur="onBlur"
          @focus="onFocus"
          :pattern="pattern"
        />
      </template>
      
      <!-- Append Add-on si existe -->
      <span v-if="appendText || appendIcon" class="input-group-text">
        <i v-if="appendIcon" :class="appendIcon"></i>
        <span v-if="appendText">{{ appendText }}</span>
      </span>
      
      <!-- Botón de limpiar si clearable -->
      <button 
        v-if="clearable && inputValue && !disabled && !readonly" 
        class="btn btn-outline-secondary" 
        type="button" 
        @click="clearInput"
      >
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <!-- Mensaje de ayuda -->
    <div v-if="helpText" class="form-text text-muted small mt-1">
      {{ helpText }}
    </div>
    
    <!-- Mensaje de error -->
    <div v-if="error" class="invalid-feedback d-block">
      {{ error }}
    </div>
  </div>
</template>

<script>
import { ref, computed, watch } from 'vue';

export default {
  name: 'FormInput',
  props: {
    /**
     * Valor del input
     */
    modelValue: {
      type: [String, Number, Boolean, Array],
      default: ''
    },
    /**
     * Tipo de input (text, email, password, number, etc.)
     */
    type: {
      type: String,
      default: 'text'
    },
    /**
     * Etiqueta para el input
     */
    label: {
      type: String,
      default: ''
    },
    /**
     * Texto de placeholder
     */
    placeholder: {
      type: String,
      default: ''
    },
    /**
     * Si el campo es requerido
     */
    required: {
      type: Boolean,
      default: false
    },
    /**
     * ID del input
     */
    id: {
      type: String,
      default: ''
    },
    /**
     * Nombre del input
     */
    name: {
      type: String,
      default: ''
    },
    /**
     * Mensaje de error
     */
    error: {
      type: String,
      default: ''
    },
    /**
     * Mensaje de ayuda
     */
    helpText: {
      type: String,
      default: ''
    },
    /**
     * Si el input está deshabilitado
     */
    disabled: {
      type: Boolean,
      default: false
    },
    /**
     * Si el input es de solo lectura
     */
    readonly: {
      type: Boolean,
      default: false
    },
    /**
     * Valor mínimo (para inputs de tipo number)
     */
    min: {
      type: [Number, String],
      default: null
    },
    /**
     * Valor máximo (para inputs de tipo number)
     */
    max: {
      type: [Number, String],
      default: null
    },
    /**
     * Paso (para inputs de tipo number)
     */
    step: {
      type: [Number, String],
      default: null
    },
    /**
     * Número máximo de caracteres
     */
    maxlength: {
      type: [Number, String],
      default: null
    },
    /**
     * Patrón de validación
     */
    pattern: {
      type: String,
      default: null
    },
    /**
     * Comportamiento de autocompletar
     */
    autocomplete: {
      type: String,
      default: 'off'
    },
    /**
     * Número de filas (para textarea)
     */
    rows: {
      type: [Number, String],
      default: 3
    },
    /**
     * Opciones para select
     */
    options: {
      type: Array,
      default: () => []
    },
    /**
     * Si permite selecciones múltiples (para select)
     */
    multiple: {
      type: Boolean,
      default: false
    },
    /**
     * Prepend texto
     */
    prependText: {
      type: String,
      default: ''
    },
    /**
     * Prepend icono
     */
    prependIcon: {
      type: String,
      default: ''
    },
    /**
     * Append texto
     */
    appendText: {
      type: String,
      default: ''
    },
    /**
     * Append icono
     */
    appendIcon: {
      type: String,
      default: ''
    },
    /**
     * Si se puede limpiar el campo con un botón
     */
    clearable: {
      type: Boolean,
      default: false
    }
  },
  setup(props, { emit }) {
    const inputValue = ref(props.modelValue);
    
    // ID único para el input
    const inputId = computed(() => {
      return props.id || `input-${Math.random().toString(36).substring(2, 9)}`;
    });
    
    // Clases para el input
    const inputClasses = computed(() => {
      return [
        props.type === 'textarea' ? 'form-control' : 'form-control',
        props.error ? 'is-invalid' : '',
        props.disabled ? 'disabled' : '',
      ];
    });
    
    // Actualizar el valor cuando cambia el prop
    watch(() => props.modelValue, (newValue) => {
      inputValue.value = newValue;
    });
    
    // Emitir evento de actualización
    const updateValue = (event) => {
      emit('update:modelValue', inputValue.value);
      emit('input', event);
    };
    
    // Método para limpiar el input
    const clearInput = () => {
      inputValue.value = '';
      emit('update:modelValue', inputValue.value);
      emit('clear');
    };
    
    // Métodos para eventos
    const onBlur = (event) => {
      emit('blur', event);
    };
    
    const onFocus = (event) => {
      emit('focus', event);
    };
    
    return {
      inputValue,
      inputId,
      inputClasses,
      updateValue,
      clearInput,
      onBlur,
      onFocus
    };
  }
};
</script>

<style scoped>
.form-group {
  position: relative;
}

.form-label {
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.input-group-text {
  display: flex;
  align-items: center;
}

/* Estilo para inputs deshabilitados */
.form-control:disabled,
.form-control[readonly] {
  background-color: #f8f9fa;
  opacity: 0.8;
}

/* Animación para focus */
.form-control:focus {
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
  transition: box-shadow 0.2s ease-in-out;
}
</style>

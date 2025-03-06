<template>
  <div class="password-field">
    <div class="password-input-container">
      <input 
        :type="showPassword ? 'text' : 'password'" 
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        class="form-control"
        :placeholder="placeholder"
        :required="required"
        :minlength="minlength"
        :autocomplete="autocomplete"
      />
      <button 
        type="button" 
        class="password-toggle-btn" 
        @click="togglePasswordVisibility"
        tabindex="-1"
      >
        <i :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
      </button>
    </div>
    <div v-if="modelValue && showPasswordStrength" class="password-strength">
      <div class="strength-meter">
        <div :class="['strength-meter-fill', strengthClass]" :style="{ width: strength + '%' }"></div>
      </div>
      <div class="strength-text" :class="strengthClass">{{ strengthText }}</div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PasswordField',
  props: {
    modelValue: {
      type: String,
      required: true
    },
    placeholder: {
      type: String,
      default: 'Password'
    },
    required: {
      type: Boolean,
      default: true
    },
    minlength: {
      type: [String, Number],
      default: 8
    },
    autocomplete: {
      type: String,
      default: 'off'
    },
    showPasswordStrength: {
      type: Boolean,
      default: false
    }
  },
  emits: ['update:modelValue'],
  data() {
    return {
      showPassword: false,
      strength: 0,
      strengthClass: '',
      strengthText: ''
    };
  },
  watch: {
    modelValue: {
      handler(newValue) {
        if (this.showPasswordStrength && newValue) {
          this.calculatePasswordStrength(newValue);
        }
      },
      immediate: true
    }
  },
  methods: {
    togglePasswordVisibility() {
      this.showPassword = !this.showPassword;
    },
    calculatePasswordStrength(password) {
      // Asignar puntos según la complejidad
      let points = 0;
      
      // Longitud de la contraseña
      if (password.length >= 8) points += 20;
      if (password.length >= 10) points += 10;
      
      // Complejidad
      if (/[a-z]/.test(password)) points += 10; // Minúsculas
      if (/[A-Z]/.test(password)) points += 20; // Mayúsculas
      if (/[0-9]/.test(password)) points += 20; // Números
      if (/[^a-zA-Z0-9]/.test(password)) points += 20; // Caracteres especiales
      
      // Asignar clase y texto según los puntos
      this.strength = points;
      
      if (points < 40) {
        this.strengthClass = 'weak';
        this.strengthText = 'ضعيف';
      } else if (points < 70) {
        this.strengthClass = 'medium';
        this.strengthText = 'متوسط';
      } else {
        this.strengthClass = 'strong';
        this.strengthText = 'قوي';
      }
    }
  }
};
</script>

<style scoped>
.password-field {
  position: relative;
  width: 100%;
}

.password-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.password-toggle-btn {
  position: absolute;
  right: 10px;
  border: none;
  background: transparent;
  cursor: pointer;
  color: #6c757d;
}

.password-toggle-btn:hover {
  color: #495057;
}

.password-strength {
  margin-top: 5px;
}

.strength-meter {
  height: 4px;
  background-color: #e9ecef;
  border-radius: 2px;
  overflow: hidden;
}

.strength-meter-fill {
  height: 100%;
  transition: width 0.3s ease;
}

.strength-text {
  font-size: 12px;
  margin-top: 3px;
  text-align: right;
}

.weak {
  color: #dc3545;
  background-color: #dc3545;
}

.medium {
  color: #ffc107;
  background-color: #ffc107;
}

.strong {
  color: #28a745;
  background-color: #28a745;
}
</style>

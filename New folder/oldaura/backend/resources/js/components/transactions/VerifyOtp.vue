<template>
  <div class="otp-verification-container">
    <div v-if="loading" class="text-center my-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">{{ loadingMessage }}</p>
    </div>
    
    <div v-else>
      <div class="text-center mb-4">
        <div class="icon-container">
          <i class="fas fa-shield-alt fa-3x text-primary"></i>
        </div>
        <h4 class="mt-3">{{ $t('otp.verification_title') }}</h4>
        <p>{{ $t('otp.verification_message', { phone: maskedPhone }) }}</p>
      </div>
      
      <div class="otp-input-container">
        <input
          v-for="(digit, index) in otpDigits"
          :key="index"
          ref="otpInputs"
          type="text"
          maxlength="1"
          class="otp-input"
          :class="{'is-invalid': verificationError}"
          v-model="otpDigits[index]"
          @input="onInput(index)"
          @keydown="onKeyDown($event, index)"
          @paste="onPaste"
          @focus="$event.target.select()"
        />
      </div>
      
      <div v-if="verificationError" class="text-danger text-center mt-2">
        {{ verificationError }}
      </div>
      
      <div class="otp-actions mt-4">
        <div class="d-grid gap-2">
          <button 
            class="btn btn-primary" 
            @click="verifyOtp" 
            :disabled="!isComplete || verifying"
          >
            <span v-if="verifying" class="spinner-border spinner-border-sm me-2" role="status"></span>
            {{ $t('otp.verify_button') }}
          </button>
        </div>
        
        <div class="text-center mt-3">
          <p class="mb-1">{{ $t('otp.not_received') }}</p>
          <button 
            class="btn btn-link p-0" 
            @click="resendOtp" 
            :disabled="resendCountdown > 0 || resending"
          >
            <span v-if="resending" class="spinner-border spinner-border-sm me-1" role="status"></span>
            {{ resendCountdown > 0 ? $t('otp.resend_countdown', { time: resendCountdown }) : $t('otp.resend_button') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'VerifyOtp',
  props: {
    purpose: {
      type: String,
      required: true,
      validator: value => ['transaction', 'login', 'password_reset', 'account_verification'].includes(value)
    },
    transactionId: {
      type: String,
      default: null
    },
    phoneNumber: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      otpDigits: ['', '', '', '', '', ''],
      verifying: false,
      resending: false,
      loading: false,
      loadingMessage: '',
      verificationError: '',
      resendCountdown: 0,
      resendInterval: null,
      maskedPhone: this.maskPhoneNumber(this.phoneNumber)
    };
  },
  computed: {
    isComplete() {
      return this.otpDigits.every(digit => digit !== '');
    },
    otpCode() {
      return this.otpDigits.join('');
    }
  },
  mounted() {
    // Iniciar el contador para reenvío
    this.startResendCountdown();
    // Enfocar el primer input
    this.$nextTick(() => {
      if (this.$refs.otpInputs && this.$refs.otpInputs.length > 0) {
        this.$refs.otpInputs[0].focus();
      }
    });
  },
  beforeUnmount() {
    // Limpiar el intervalo al desmontar
    clearInterval(this.resendInterval);
  },
  methods: {
    maskPhoneNumber(phone) {
      if (!phone) return '';
      // Mantener los dos primeros dígitos y los dos últimos, y ocultar el resto con asteriscos
      return phone.substr(0, 2) + '*'.repeat(phone.length - 4) + phone.substr(phone.length - 2);
    },
    
    onInput(index) {
      const digit = this.otpDigits[index];
      
      // Validar que sea un número
      if (digit && !/^[0-9]$/.test(digit)) {
        this.otpDigits[index] = '';
        return;
      }
      
      // Si se ingresó un dígito y no es el último input, pasar al siguiente
      if (digit !== '' && index < this.otpDigits.length - 1) {
        this.$nextTick(() => {
          this.$refs.otpInputs[index + 1].focus();
        });
      }
      
      // Si se completaron todos los dígitos, verificar automáticamente
      if (this.isComplete) {
        this.verifyOtp();
      }
      
      // Limpiar el error si existe
      if (this.verificationError) {
        this.verificationError = '';
      }
    },
    
    onKeyDown(event, index) {
      // Si se presiona Backspace en un input vacío, regresar al anterior
      if (event.key === 'Backspace' && this.otpDigits[index] === '' && index > 0) {
        this.$refs.otpInputs[index - 1].focus();
      }
    },
    
    onPaste(event) {
      event.preventDefault();
      
      // Obtener el texto pegado y limpiarlo
      const pastedText = (event.clipboardData || window.clipboardData).getData('text').trim();
      
      // Verificar que sea un número de 6 dígitos
      if (/^\d{6}$/.test(pastedText)) {
        // Asignar cada dígito al array
        for (let i = 0; i < Math.min(6, pastedText.length); i++) {
          this.otpDigits[i] = pastedText[i];
        }
        
        // Enfocar el último input
        this.$nextTick(() => {
          this.$refs.otpInputs[5].focus();
        });
      }
    },
    
    verifyOtp() {
      if (!this.isComplete || this.verifying) return;
      
      this.verifying = true;
      this.verificationError = '';
      
      // Datos para la verificación
      const data = {
        purpose: this.purpose,
        code: this.otpCode
      };
      
      // Si hay un transaction_id, lo incluimos
      if (this.transactionId) {
        data.transaction_id = this.transactionId;
      }
      
      // Llamada a la API
      this.axios.post('/api/otp/verify', data)
        .then(response => {
          if (response.data && response.data.status === 'success') {
            // Emitir evento de verificación exitosa
            this.$emit('verified', response.data.data);
          } else {
            this.verificationError = this.$t('otp.generic_error');
          }
        })
        .catch(error => {
          if (error.response && error.response.data) {
            const data = error.response.data;
            
            if (data.message) {
              this.verificationError = data.message;
            }
            
            // Si hay información sobre intentos restantes
            if (data.data && data.data.attempts_left !== undefined) {
              if (data.data.attempts_left > 0) {
                this.verificationError += ' ' + this.$t('otp.attempts_left', { attempts: data.data.attempts_left });
              } else {
                this.verificationError = this.$t('otp.no_attempts_left');
                this.$emit('max-attempts-reached');
              }
            }
          } else {
            this.verificationError = this.$t('otp.generic_error');
          }
        })
        .finally(() => {
          this.verifying = false;
        });
    },
    
    resendOtp() {
      if (this.resendCountdown > 0 || this.resending) return;
      
      this.resending = true;
      this.verificationError = '';
      
      // Datos para reenvío
      const data = {
        purpose: this.purpose
      };
      
      // Si hay un transaction_id, lo incluimos
      if (this.transactionId) {
        data.transaction_id = this.transactionId;
      }
      
      // Llamada a la API
      this.axios.post('/api/otp/resend', data)
        .then(response => {
          if (response.data && response.data.status === 'success') {
            // Mostrar mensaje de éxito
            this.$toast.success(this.$t('otp.resent_success'));
            
            // Reiniciar los campos
            this.otpDigits = ['', '', '', '', '', ''];
            this.$nextTick(() => {
              this.$refs.otpInputs[0].focus();
            });
            
            // Reiniciar el contador
            this.startResendCountdown();
          } else {
            this.verificationError = this.$t('otp.resend_error');
          }
        })
        .catch(error => {
          this.verificationError = this.$t('otp.resend_error');
        })
        .finally(() => {
          this.resending = false;
        });
    },
    
    startResendCountdown() {
      // Iniciar con 60 segundos
      this.resendCountdown = 60;
      
      // Limpiar intervalo existente si hay uno
      if (this.resendInterval) {
        clearInterval(this.resendInterval);
      }
      
      // Crear nuevo intervalo
      this.resendInterval = setInterval(() => {
        if (this.resendCountdown > 0) {
          this.resendCountdown--;
        } else {
          clearInterval(this.resendInterval);
        }
      }, 1000);
    }
  }
};
</script>

<style scoped>
.otp-verification-container {
  max-width: 400px;
  margin: 0 auto;
  padding: 20px;
}

.icon-container {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background-color: rgba(13, 110, 253, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}

.otp-input-container {
  display: flex;
  justify-content: space-between;
  margin: 20px 0;
}

.otp-input {
  width: 45px;
  height: 50px;
  border: 1px solid #ced4da;
  border-radius: 8px;
  font-size: 20px;
  text-align: center;
  font-weight: bold;
  transition: border-color 0.15s ease-in-out;
}

.otp-input:focus {
  outline: none;
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.otp-input.is-invalid {
  border-color: #dc3545;
}

/* Para dispositivos móviles */
@media (max-width: 576px) {
  .otp-input {
    width: 40px;
    height: 45px;
    font-size: 18px;
  }
}
</style>

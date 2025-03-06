<template>
  <div class="deposit-funds">
    <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
          <i class="fas fa-money-bill-wave me-2"></i>
          {{ $t('banking.deposit_funds') }}
        </h5>
      </div>
      <div class="card-body">
        <!-- Paso 1: Seleccionar cuenta y monto -->
        <div v-if="currentStep === 1">
          <form @submit.prevent="goToConfirmation">
            <!-- Selección de cuenta -->
            <div class="mb-3">
              <label for="accountSelect" class="form-label">{{ $t('banking.select_account') }}</label>
              <div v-if="loading" class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
              </div>
              <div v-else-if="bankAccounts.length > 0">
                <select id="accountSelect" class="form-select" v-model="depositForm.bank_account_id" required>
                  <option value="">{{ $t('banking.select_account') }}</option>
                  <option v-for="account in bankAccounts" :key="account.id" :value="account.id">
                    {{ account.bank_name }} - {{ maskAccountNumber(account.account_number) }}
                  </option>
                </select>
                <div class="mt-2">
                  <button type="button" class="btn btn-sm btn-outline-primary" @click="$emit('add-account')">
                    <i class="fas fa-plus me-1"></i> {{ $t('banking.add_account') }}
                  </button>
                </div>
              </div>
              <div v-else class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ $t('banking.no_accounts') }}
                <div class="mt-2">
                  <button type="button" class="btn btn-sm btn-primary" @click="$emit('add-account')">
                    {{ $t('banking.add_account') }}
                  </button>
                </div>
              </div>
              <div v-if="errors.bank_account_id" class="text-danger mt-1">{{ errors.bank_account_id }}</div>
            </div>
            
            <!-- Monto -->
            <div class="mb-3">
              <label for="depositAmount" class="form-label">{{ $t('banking.deposit_amount') }}</label>
              <div class="input-group">
                <span class="input-group-text">SDG</span>
                <input 
                  type="number" 
                  class="form-control" 
                  id="depositAmount" 
                  v-model="depositForm.amount"
                  min="10"
                  step="0.01"
                  required
                  :class="{'is-invalid': errors.amount}">
              </div>
              <small class="text-muted">{{ $t('banking.min_deposit', {amount: '10 SDG'}) }}</small>
              <div v-if="errors.amount" class="text-danger mt-1">{{ errors.amount }}</div>
            </div>
            
            <!-- Descripción -->
            <div class="mb-4">
              <label for="depositDescription" class="form-label">{{ $t('banking.description') }} ({{ $t('common.optional') }})</label>
              <textarea 
                class="form-control" 
                id="depositDescription" 
                v-model="depositForm.description"
                rows="2"
                :class="{'is-invalid': errors.description}"></textarea>
              <div v-if="errors.description" class="text-danger mt-1">{{ errors.description }}</div>
            </div>
            
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary" :disabled="!canProceed">
                {{ $t('common.continue') }}
              </button>
              <button type="button" class="btn btn-outline-secondary" @click="$emit('cancel')">
                {{ $t('common.cancel') }}
              </button>
            </div>
          </form>
        </div>
        
        <!-- Paso 2: Confirmación -->
        <div v-else-if="currentStep === 2">
          <div class="text-center mb-4">
            <div class="mb-3">
              <i class="fas fa-check-circle fa-3x text-success"></i>
            </div>
            <h5>{{ $t('banking.confirm_deposit') }}</h5>
          </div>
          
          <div class="deposit-summary p-3 mb-4 bg-light rounded">
            <div class="d-flex justify-content-between mb-2">
              <span>{{ $t('banking.bank') }}:</span>
              <span class="fw-bold">{{ selectedAccount ? selectedAccount.bank_name : '' }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>{{ $t('banking.account_number') }}:</span>
              <span class="fw-bold">{{ selectedAccount ? maskAccountNumber(selectedAccount.account_number) : '' }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>{{ $t('banking.amount') }}:</span>
              <span class="fw-bold">{{ formatCurrency(depositForm.amount) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-0" v-if="depositForm.description">
              <span>{{ $t('banking.description') }}:</span>
              <span class="fw-bold">{{ depositForm.description }}</span>
            </div>
          </div>
          
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            {{ $t('banking.deposit_info') }}
          </div>
          
          <div class="d-grid gap-2">
            <button 
              type="button" 
              class="btn btn-primary" 
              @click="processDeposit"
              :disabled="processing"
            >
              <span v-if="processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
              {{ $t('banking.confirm') }}
            </button>
            <button type="button" class="btn btn-outline-secondary" @click="currentStep = 1">
              {{ $t('common.back') }}
            </button>
          </div>
        </div>
        
        <!-- Paso 3: Resultado -->
        <div v-else-if="currentStep === 3">
          <div class="text-center mb-4">
            <div class="mb-3">
              <i :class="['fa-3x', transactionStatus.icon]"></i>
            </div>
            <h5>{{ transactionStatus.title }}</h5>
            <p>{{ transactionStatus.message }}</p>
          </div>
          
          <div v-if="needsOtp" class="mt-4">
            <verify-otp 
              purpose="transaction"
              :transaction-id="transactionId"
              :phone-number="userPhone"
              @verified="handleOtpVerified"
            />
          </div>
          
          <div v-else class="d-grid gap-2 mt-4">
            <button type="button" class="btn btn-primary" @click="$emit('done')">
              {{ $t('common.done') }}
            </button>
            <button v-if="depositSuccess" type="button" class="btn btn-outline-primary" @click="resetForm">
              {{ $t('banking.new_deposit') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import VerifyOtp from '../transactions/VerifyOtp.vue';

export default {
  name: 'DepositFunds',
  components: {
    VerifyOtp
  },
  props: {
    userPhone: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      loading: true,
      processing: false,
      bankAccounts: [],
      currentStep: 1,
      depositSuccess: false,
      depositForm: {
        bank_account_id: '',
        amount: '',
        description: ''
      },
      errors: {
        bank_account_id: '',
        amount: '',
        description: ''
      },
      needsOtp: false,
      transactionId: null
    };
  },
  computed: {
    canProceed() {
      return this.depositForm.bank_account_id && 
             this.depositForm.amount && 
             parseFloat(this.depositForm.amount) >= 10;
    },
    selectedAccount() {
      if (!this.depositForm.bank_account_id) return null;
      return this.bankAccounts.find(account => account.id === this.depositForm.bank_account_id);
    },
    transactionStatus() {
      if (this.needsOtp) {
        return {
          icon: 'fas fa-shield-alt text-primary',
          title: this.$t('otp.transaction_verification'),
          message: this.$t('otp.transaction_verification_message')
        };
      } else if (this.depositSuccess) {
        return {
          icon: 'fas fa-check-circle text-success',
          title: this.$t('banking.deposit_success'),
          message: this.$t('banking.deposit_success_message')
        };
      } else {
        return {
          icon: 'fas fa-times-circle text-danger',
          title: this.$t('banking.deposit_failed'),
          message: this.$t('banking.deposit_failed_message')
        };
      }
    }
  },
  mounted() {
    this.loadBankAccounts();
  },
  methods: {
    loadBankAccounts() {
      this.loading = true;
      
      this.axios.get('/api/banking/accounts')
        .then(response => {
          this.bankAccounts = response.data.data;
        })
        .catch(error => {
          this.$toast.error(this.$t('common.error_loading'));
          console.error('Error loading bank accounts:', error);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    
    goToConfirmation() {
      // Validar forma
      this.clearErrors();
      
      if (!this.depositForm.bank_account_id) {
        this.errors.bank_account_id = this.$t('banking.select_account_error');
        return;
      }
      
      if (!this.depositForm.amount || parseFloat(this.depositForm.amount) < 10) {
        this.errors.amount = this.$t('banking.min_deposit', {amount: '10 SDG'});
        return;
      }
      
      // Avanzar a paso de confirmación
      this.currentStep = 2;
    },
    
    processDeposit() {
      this.processing = true;
      
      this.axios.post('/api/banking/deposit', this.depositForm)
        .then(response => {
          const data = response.data.data;
          
          // Verificar si necesita OTP
          if (data.requires_otp) {
            this.needsOtp = true;
            this.transactionId = data.transaction_id;
          } else {
            this.depositSuccess = true;
          }
          
          this.currentStep = 3;
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            this.errors = error.response.data.errors;
            this.currentStep = 1;
          } else {
            this.$toast.error(error.response?.data?.message || this.$t('banking.deposit_failed'));
            this.depositSuccess = false;
            this.currentStep = 3;
          }
        })
        .finally(() => {
          this.processing = false;
        });
    },
    
    handleOtpVerified(data) {
      this.needsOtp = false;
      this.depositSuccess = true;
    },
    
    maskAccountNumber(accountNumber) {
      if (!accountNumber) return '';
      const len = accountNumber.length;
      if (len <= 4) return accountNumber;
      return '•••• •••• •••• ' + accountNumber.slice(-4);
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('ar-SD', {
        style: 'currency',
        currency: 'SDG'
      }).format(amount);
    },
    
    resetForm() {
      this.depositForm = {
        bank_account_id: '',
        amount: '',
        description: ''
      };
      this.clearErrors();
      this.currentStep = 1;
      this.needsOtp = false;
      this.depositSuccess = false;
    },
    
    clearErrors() {
      this.errors = {
        bank_account_id: '',
        amount: '',
        description: ''
      };
    }
  }
};
</script>

<style scoped>
.deposit-funds {
  max-width: 650px;
  margin: 0 auto;
}

.card {
  border-radius: 10px;
  overflow: hidden;
}

.card-header {
  padding: 1rem;
}

.deposit-summary {
  border-left: 4px solid #0d6efd;
}

@media (max-width: 576px) {
  .deposit-funds {
    max-width: 100%;
  }
}
</style>

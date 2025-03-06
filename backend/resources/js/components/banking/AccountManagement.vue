<template>
  <div class="account-management">
    <h2 class="section-title">{{ $t('banking.account_management') }}</h2>
    
    <!-- Loading indicator -->
    <div v-if="loading" class="text-center p-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-3">{{ $t('common.loading') }}</p>
    </div>
    
    <!-- Lista de cuentas bancarias -->
    <div v-else-if="bankAccounts.length > 0" class="mb-4">
      <div class="card mb-3" v-for="account in bankAccounts" :key="account.id">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h5 class="mb-1">{{ account.bank_name }}</h5>
              <p class="mb-2 text-muted">{{ maskAccountNumber(account.account_number) }}</p>
              <p class="mb-0 text-muted">{{ account.account_name }}</p>
              <span 
                v-if="account.is_primary" 
                class="badge bg-primary mt-2">{{ $t('banking.is_primary') }}</span>
            </div>
            <div class="align-self-center">
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                  <li v-if="!account.is_primary">
                    <a class="dropdown-item" href="#" @click.prevent="setPrimaryAccount(account.id)">
                      <i class="fas fa-star me-2"></i> {{ $t('banking.set_as_primary') }}
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="#" @click.prevent="showEditModal(account)">
                      <i class="fas fa-edit me-2"></i> {{ $t('common.edit') }}
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item text-danger" href="#" @click.prevent="showDeleteConfirmation(account)">
                      <i class="fas fa-trash-alt me-2"></i> {{ $t('common.delete') }}
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Mensaje si no hay cuentas -->
    <div v-else class="text-center p-5 bg-light rounded mb-4">
      <i class="fas fa-university fa-3x text-muted mb-3"></i>
      <h5>{{ $t('banking.no_accounts') }}</h5>
      <p class="text-muted">{{ $t('banking.add_account_description') }}</p>
    </div>
    
    <!-- Botón para agregar cuenta -->
    <div class="d-grid gap-2">
      <button class="btn btn-primary" @click="showAddModal">
        <i class="fas fa-plus me-2"></i> {{ $t('banking.add_account') }}
      </button>
    </div>
    
    <!-- Modal para agregar o editar cuenta bancaria -->
    <div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true" ref="accountModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="accountModalLabel">
              {{ isEditing ? $t('banking.account_details') : $t('banking.add_account') }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveAccount">
              <!-- Selección de banco -->
              <div class="mb-3">
                <label for="bankSelect" class="form-label">{{ $t('banking.bank') }}</label>
                <select id="bankSelect" class="form-select" v-model="accountForm.bank_id" required>
                  <option value="">{{ $t('banking.select_bank') }}</option>
                  <option v-for="bank in banks" :key="bank.id" :value="bank.id">
                    {{ bank.name }}
                  </option>
                </select>
                <div v-if="errors.bank_id" class="text-danger mt-1">{{ errors.bank_id }}</div>
              </div>
              
              <!-- Número de cuenta -->
              <div class="mb-3">
                <label for="accountNumber" class="form-label">{{ $t('banking.account_number') }}</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="accountNumber" 
                  v-model="accountForm.account_number"
                  required
                  :class="{'is-invalid': errors.account_number}"
                  maxlength="20">
                <div v-if="errors.account_number" class="invalid-feedback">{{ errors.account_number }}</div>
              </div>
              
              <!-- Nombre de la cuenta -->
              <div class="mb-3">
                <label for="accountName" class="form-label">{{ $t('banking.account_name') }}</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="accountName" 
                  v-model="accountForm.account_name"
                  required
                  :class="{'is-invalid': errors.account_name}">
                <div v-if="errors.account_name" class="invalid-feedback">{{ errors.account_name }}</div>
              </div>
              
              <!-- Marcar como principal -->
              <div class="mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" v-model="accountForm.is_primary" id="isPrimary">
                  <label class="form-check-label" for="isPrimary">
                    {{ $t('banking.set_as_primary') }}
                  </label>
                </div>
              </div>
              
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary" :disabled="submitting">
                  <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
                  {{ $t('common.save') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal para confirmar eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" ref="deleteModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">{{ $t('common.confirm_delete') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>{{ $t('banking.confirm_delete_account') }}</p>
            <p class="mb-0 fw-bold">{{ selectedAccount ? selectedAccount.bank_name : '' }}</p>
            <p class="mb-0">{{ selectedAccount ? maskAccountNumber(selectedAccount.account_number) : '' }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              {{ $t('common.cancel') }}
            </button>
            <button type="button" class="btn btn-danger" @click="deleteAccount" :disabled="submitting">
              <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
              {{ $t('common.delete') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AccountManagement',
  data() {
    return {
      loading: true,
      submitting: false,
      isEditing: false,
      bankAccounts: [],
      banks: [],
      selectedAccount: null,
      accountForm: {
        bank_id: '',
        account_number: '',
        account_name: '',
        is_primary: false
      },
      errors: {
        bank_id: '',
        account_number: '',
        account_name: ''
      },
      modals: {
        account: null,
        delete: null
      }
    };
  },
  mounted() {
    this.loadData();
    
    // Inicializar los modales
    this.modals.account = new bootstrap.Modal(this.$refs.accountModal);
    this.modals.delete = new bootstrap.Modal(this.$refs.deleteModal);
  },
  methods: {
    // Cargar datos de cuentas y bancos
    loadData() {
      this.loading = true;
      
      // Cargar cuentas bancarias del usuario
      this.axios.get('/api/banking/accounts')
        .then(response => {
          this.bankAccounts = response.data.data;
        })
        .catch(error => {
          this.$toast.error(this.$t('common.error_loading'));
          console.error('Error loading bank accounts:', error);
        });
      
      // Cargar lista de bancos disponibles
      this.axios.get('/api/banking/banks')
        .then(response => {
          this.banks = response.data.data;
        })
        .catch(error => {
          this.$toast.error(this.$t('common.error_loading'));
          console.error('Error loading banks:', error);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    
    // Mostrar modal para agregar cuenta
    showAddModal() {
      this.isEditing = false;
      this.resetForm();
      this.modals.account.show();
    },
    
    // Mostrar modal para editar cuenta
    showEditModal(account) {
      this.isEditing = true;
      this.resetForm();
      
      this.accountForm = {
        id: account.id,
        bank_id: account.bank_id,
        account_number: account.account_number,
        account_name: account.account_name,
        is_primary: account.is_primary
      };
      
      this.modals.account.show();
    },
    
    // Mostrar confirmación de eliminación
    showDeleteConfirmation(account) {
      this.selectedAccount = account;
      this.modals.delete.show();
    },
    
    // Guardar cuenta (crear o actualizar)
    saveAccount() {
      this.submitting = true;
      this.clearErrors();
      
      const url = this.isEditing 
        ? `/api/banking/accounts/${this.accountForm.id}` 
        : '/api/banking/accounts';
      
      const method = this.isEditing ? 'put' : 'post';
      
      this.axios[method](url, this.accountForm)
        .then(response => {
          this.$toast.success(this.$t('banking.success_message'));
          this.modals.account.hide();
          this.loadData();
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            this.errors = error.response.data.errors;
          } else {
            this.$toast.error(this.$t('banking.error_message'));
          }
          console.error('Error saving bank account:', error);
        })
        .finally(() => {
          this.submitting = false;
        });
    },
    
    // Eliminar cuenta
    deleteAccount() {
      if (!this.selectedAccount) return;
      
      this.submitting = true;
      
      this.axios.delete(`/api/banking/accounts/${this.selectedAccount.id}`)
        .then(response => {
          this.$toast.success(this.$t('banking.success_message'));
          this.modals.delete.hide();
          this.loadData();
        })
        .catch(error => {
          this.$toast.error(error.response?.data?.message || this.$t('banking.error_message'));
          console.error('Error deleting bank account:', error);
        })
        .finally(() => {
          this.submitting = false;
        });
    },
    
    // Establecer cuenta como principal
    setPrimaryAccount(accountId) {
      this.axios.put(`/api/banking/accounts/${accountId}/primary`)
        .then(response => {
          this.$toast.success(this.$t('banking.success_message'));
          this.loadData();
        })
        .catch(error => {
          this.$toast.error(this.$t('banking.error_message'));
          console.error('Error setting primary account:', error);
        });
    },
    
    // Ocultar parte del número de cuenta por seguridad
    maskAccountNumber(accountNumber) {
      if (!accountNumber) return '';
      const len = accountNumber.length;
      if (len <= 4) return accountNumber;
      return '•••• •••• •••• ' + accountNumber.slice(-4);
    },
    
    // Resetear formulario
    resetForm() {
      this.accountForm = {
        bank_id: '',
        account_number: '',
        account_name: '',
        is_primary: false
      };
      this.clearErrors();
    },
    
    // Limpiar errores
    clearErrors() {
      this.errors = {
        bank_id: '',
        account_number: '',
        account_name: ''
      };
    }
  }
};
</script>

<style scoped>
.section-title {
  margin-bottom: 1.5rem;
  font-weight: 600;
}

.account-management {
  max-width: 850px;
  margin: 0 auto;
  padding: 1.5rem 0;
}

.card {
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: all 0.2s ease;
}

.card:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.badge {
  font-weight: 500;
  padding: 0.35em 0.65em;
}

@media (max-width: 576px) {
  .account-management {
    padding: 1rem 0;
  }
}
</style>

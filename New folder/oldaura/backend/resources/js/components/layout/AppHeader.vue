<template>
  <header :class="['app-header', {'header-fixed': fixed}]">
    <nav class="navbar navbar-expand-lg">
      <div class="container">
        <!-- Logo -->
        <router-link class="navbar-brand" to="/">
          <img v-if="logoUrl" :src="logoUrl" height="36" alt="Aura Logo" />
          <span v-else class="fw-bold text-primary">Aura</span>
        </router-link>
        
        <!-- Botón para móvil -->
        <button 
          class="navbar-toggler" 
          type="button" 
          data-bs-toggle="collapse" 
          data-bs-target="#navbarContent"
          aria-controls="navbarContent" 
          aria-expanded="false" 
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Menú de navegación -->
        <div class="collapse navbar-collapse" id="navbarContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <slot name="menu-items">
              <!-- Elementos de menú por defecto -->
              <li class="nav-item">
                <router-link class="nav-link" to="/" exact>{{ $t('nav.home') }}</router-link>
              </li>
              <li class="nav-item">
                <router-link class="nav-link" to="/services">{{ $t('nav.services') }}</router-link>
              </li>
              <li class="nav-item">
                <router-link class="nav-link" to="/about">{{ $t('nav.about') }}</router-link>
              </li>
              <li class="nav-item">
                <router-link class="nav-link" to="/contact">{{ $t('nav.contact') }}</router-link>
              </li>
            </slot>
          </ul>
          
          <!-- Elementos de la derecha (auth, idioma, etc) -->
          <div class="d-flex align-items-center">
            <!-- Selector de idioma -->
            <div class="dropdown me-3" v-if="languages.length > 0">
              <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-globe me-1"></i> {{ currentLanguageLabel }}
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                <li v-for="lang in languages" :key="lang.code">
                  <a class="dropdown-item" href="#" @click.prevent="changeLanguage(lang.code)">{{ lang.label }}</a>
                </li>
              </ul>
            </div>
            
            <!-- Opciones de usuario (no logueado) -->
            <template v-if="!isLoggedIn">
              <router-link class="btn btn-sm btn-outline-primary me-2" to="/login">{{ $t('auth.login') }}</router-link>
              <router-link class="btn btn-sm btn-primary" to="/register">{{ $t('auth.register') }}</router-link>
            </template>
            
            <!-- Opciones de usuario (logueado) -->
            <div class="dropdown" v-else>
              <button class="btn btn-link text-decoration-none dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="avatar me-2" v-if="user.avatar">
                  <img :src="user.avatar" alt="user avatar" class="avatar-img rounded-circle" />
                </div>
                <div class="avatar me-2" v-else>
                  <span class="avatar-text rounded-circle bg-primary text-white">{{ userInitials }}</span>
                </div>
                <span class="d-none d-md-inline">{{ user.name }}</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li>
                  <router-link class="dropdown-item" :to="dashboardLink">
                    <i class="fas fa-tachometer-alt me-2"></i> {{ $t('nav.dashboard') }}
                  </router-link>
                </li>
                <li>
                  <router-link class="dropdown-item" to="/profile">
                    <i class="fas fa-user me-2"></i> {{ $t('nav.profile') }}
                  </router-link>
                </li>
                <li>
                  <router-link class="dropdown-item" to="/settings">
                    <i class="fas fa-cog me-2"></i> {{ $t('nav.settings') }}
                  </router-link>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item text-danger" href="#" @click.prevent="logout">
                    <i class="fas fa-sign-out-alt me-2"></i> {{ $t('auth.logout') }}
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </nav>
  </header>
</template>

<script>
export default {
  name: 'AppHeader',
  props: {
    /**
     * Logo URL
     */
    logoUrl: {
      type: String,
      default: '/img/logo.png'
    },
    /**
     * Si el header debe ser fijo (sticky)
     */
    fixed: {
      type: Boolean,
      default: true
    },
    /**
     * Datos del usuario (si está logueado)
     */
    user: {
      type: Object,
      default: () => ({})
    },
    /**
     * Si el usuario está logueado
     */
    isLoggedIn: {
      type: Boolean,
      default: false
    },
    /**
     * Idiomas disponibles
     */
    languages: {
      type: Array,
      default: () => [
        { code: 'en', label: 'English' },
        { code: 'ar', label: 'العربية' }
      ]
    },
    /**
     * Código de idioma actual
     */
    currentLanguage: {
      type: String,
      default: 'ar'
    }
  },
  computed: {
    /**
     * Obtener iniciales del nombre del usuario
     */
    userInitials() {
      if (!this.user || !this.user.name) return '';
      
      const names = this.user.name.split(' ');
      if (names.length >= 2) {
        return `${names[0][0]}${names[1][0]}`.toUpperCase();
      }
      return names[0][0].toUpperCase();
    },
    
    /**
     * Obtener etiqueta del idioma actual
     */
    currentLanguageLabel() {
      const found = this.languages.find(lang => lang.code === this.currentLanguage);
      return found ? found.label : this.languages[0].label;
    },
    
    /**
     * Enlace al dashboard según tipo de usuario
     */
    dashboardLink() {
      if (!this.user || !this.user.user_type) return '/dashboard';
      
      const routes = {
        'admin': '/admin/dashboard',
        'merchant': '/merchant/dashboard',
        'agent': '/agent/dashboard',
        'messenger': '/messenger/dashboard',
        'customer': '/customer/dashboard'
      };
      
      return routes[this.user.user_type] || '/dashboard';
    }
  },
  methods: {
    /**
     * Cambiar idioma
     */
    changeLanguage(langCode) {
      this.$emit('change-language', langCode);
    },
    
    /**
     * Cerrar sesión
     */
    logout() {
      this.$emit('logout');
    }
  }
};
</script>

<style scoped>
.app-header {
  z-index: 1000;
  background: #fff;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.header-fixed {
  position: sticky;
  top: 0;
}

.navbar-brand {
  font-size: 1.5rem;
}

.avatar {
  width: 36px;
  height: 36px;
  overflow: hidden;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-text {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 500;
  font-size: 0.9rem;
}

.nav-link {
  font-weight: 500;
  padding: 0.5rem 1rem;
  position: relative;
  transition: color 0.2s;
}

.nav-link.router-link-active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0.75rem;
  right: 0.75rem;
  height: 2px;
  background-color: var(--bs-primary);
}

/* Estilos para responsive */
@media (max-width: 992px) {
  .navbar-collapse {
    padding: 1rem 0;
  }
  
  .nav-link {
    padding: 0.5rem 0;
  }
  
  .nav-link.router-link-active::after {
    left: 0;
    right: 0;
  }
}
</style>

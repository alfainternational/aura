<template>
  <div class="side-navigation" :class="{ 'side-navigation-collapsed': collapsed }">
    <!-- Header -->
    <div class="side-nav-header">
      <div class="logo-container">
        <router-link to="/" class="logo-link">
          <img v-if="!collapsed" :src="expandedLogo" alt="Aura" class="logo-full" />
          <img v-else :src="collapsedLogo" alt="Aura" class="logo-icon" />
        </router-link>
      </div>
      <button @click="toggleCollapse" class="collapse-button">
        <i :class="collapsed ? 'fas fa-angle-right' : 'fas fa-angle-left'"></i>
      </button>
    </div>
    
    <!-- User Info -->
    <div v-if="user" class="user-info">
      <div class="user-avatar">
        <img v-if="user.avatar" :src="user.avatar" alt="User Avatar" class="avatar-img" />
        <div v-else class="avatar-placeholder">
          {{ userInitials }}
        </div>
      </div>
      
      <div v-if="!collapsed" class="user-details">
        <h6 class="user-name">{{ user.name }}</h6>
        <p class="user-role">{{ userTypeLabel }}</p>
      </div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="side-nav-menu">
      <ul class="nav-list">
        <li v-for="(item, index) in filteredMenuItems" :key="index" class="nav-item">
          <!-- Si el ítem tiene submenú -->
          <template v-if="item.children && item.children.length > 0">
            <a 
              href="#" 
              class="nav-link" 
              :class="{ 'active': isActive(item), 'collapsed': !isExpanded(item) }" 
              @click.prevent="toggleSubmenu(item)"
            >
              <i v-if="item.icon" :class="['nav-icon', item.icon]"></i>
              <span v-if="!collapsed" class="nav-text">{{ item.title }}</span>
              <i v-if="!collapsed" class="arrow-icon fas" :class="isExpanded(item) ? 'fa-angle-down' : 'fa-angle-left'"></i>
            </a>
            
            <ul class="submenu" :class="{ 'show': isExpanded(item) && !collapsed }">
              <li v-for="(child, childIndex) in item.children" :key="childIndex" class="submenu-item">
                <router-link :to="child.url" class="submenu-link" :class="{ 'active': isRouteActive(child.url) }">
                  <i v-if="child.icon" :class="['submenu-icon', child.icon]"></i>
                  <span v-if="!collapsed" class="submenu-text">{{ child.title }}</span>
                </router-link>
              </li>
            </ul>
          </template>
          
          <!-- Si el ítem es un enlace directo -->
          <router-link 
            v-else 
            :to="item.url" 
            class="nav-link" 
            :class="{ 'active': isRouteActive(item.url) }"
          >
            <i v-if="item.icon" :class="['nav-icon', item.icon]"></i>
            <span v-if="!collapsed" class="nav-text">{{ item.title }}</span>
          </router-link>
        </li>
      </ul>
    </nav>
    
    <!-- Bottom Actions -->
    <div class="side-nav-footer">
      <a href="#" class="footer-link" @click.prevent="$emit('logout')">
        <i class="fas fa-sign-out-alt"></i>
        <span v-if="!collapsed">{{ $t('auth.logout') }}</span>
      </a>
      
      <a href="#" class="footer-link" @click.prevent="$emit('settings')">
        <i class="fas fa-cog"></i>
        <span v-if="!collapsed">{{ $t('nav.settings') }}</span>
      </a>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SideNavigation',
  props: {
    /**
     * Información del usuario
     */
    user: {
      type: Object,
      default: () => ({})
    },
    /**
     * Logo cuando la navegación está expandida
     */
    expandedLogo: {
      type: String,
      default: '/img/logo.png'
    },
    /**
     * Logo cuando la navegación está colapsada
     */
    collapsedLogo: {
      type: String,
      default: '/img/logo-icon.png'
    },
    /**
     * Elementos de menú
     */
    menuItems: {
      type: Array,
      default: () => []
    },
    /**
     * Si la navegación está inicialmente colapsada
     */
    initialCollapsed: {
      type: Boolean,
      default: false
    },
    /**
     * Ancho cuando está expandido (en px)
     */
    expandedWidth: {
      type: Number,
      default: 250
    }
  },
  data() {
    return {
      collapsed: this.initialCollapsed,
      expandedItems: new Set()
    };
  },
  computed: {
    /**
     * Obtener iniciales del usuario
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
     * Obtener etiqueta del tipo de usuario
     */
    userTypeLabel() {
      if (!this.user || !this.user.user_type) return '';
      
      const types = {
        'admin': this.$t('user_types.admin'),
        'merchant': this.$t('user_types.merchant'),
        'agent': this.$t('user_types.agent'),
        'messenger': this.$t('user_types.messenger'),
        'customer': this.$t('user_types.customer')
      };
      
      return types[this.user.user_type] || this.user.user_type;
    },
    
    /**
     * Filtrar elementos del menú según permisos del usuario
     */
    filteredMenuItems() {
      if (!this.user || !this.menuItems) return [];
      
      return this.menuItems.filter(item => {
        // Si no hay restricciones, mostrar el ítem
        if (!item.visibleTo) return true;
        
        // Comprobar si el tipo de usuario actual está en la lista de visibleTo
        return item.visibleTo.includes(this.user.user_type);
      });
    }
  },
  mounted() {
    // Recuperar el estado colapsado desde localStorage si está disponible
    const savedCollapsed = localStorage.getItem('sideNavCollapsed');
    if (savedCollapsed !== null) {
      this.collapsed = savedCollapsed === 'true';
    }
    
    // Inicialmente expandir el ítem activo
    this.expandActiveItems();
  },
  methods: {
    /**
     * Alternar colapso del menú lateral
     */
    toggleCollapse() {
      this.collapsed = !this.collapsed;
      this.$emit('collapse-change', this.collapsed);
      
      // Guardar estado en localStorage
      localStorage.setItem('sideNavCollapsed', this.collapsed.toString());
    },
    
    /**
     * Alternar visibilidad del submenú
     */
    toggleSubmenu(item) {
      if (this.isExpanded(item)) {
        this.expandedItems.delete(item.title);
      } else {
        this.expandedItems.add(item.title);
      }
    },
    
    /**
     * Comprobar si un ítem está activo (él o alguno de sus hijos)
     */
    isActive(item) {
      if (this.isRouteActive(item.url)) return true;
      
      if (item.children) {
        return item.children.some(child => this.isRouteActive(child.url));
      }
      
      return false;
    },
    
    /**
     * Comprobar si una ruta está activa
     */
    isRouteActive(url) {
      if (!url) return false;
      return this.$route && this.$route.path === url;
    },
    
    /**
     * Comprobar si un ítem está expandido
     */
    isExpanded(item) {
      return this.expandedItems.has(item.title);
    },
    
    /**
     * Expandir los ítems activos
     */
    expandActiveItems() {
      this.menuItems.forEach(item => {
        if (item.children && item.children.some(child => this.isRouteActive(child.url))) {
          this.expandedItems.add(item.title);
        }
      });
    }
  }
};
</script>

<style scoped>
.side-navigation {
  height: 100vh;
  width: v-bind('expandedWidth + "px"');
  position: fixed;
  top: 0;
  left: 0;
  background-color: #fff;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
  transition: width 0.3s ease;
  z-index: 1000;
}

.side-navigation-collapsed {
  width: 70px;
}

.side-nav-header {
  height: 70px;
  padding: 0 15px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #f0f0f0;
}

.logo-container {
  display: flex;
  align-items: center;
  height: 100%;
}

.logo-link {
  display: block;
  text-decoration: none;
}

.logo-full {
  height: 36px;
  max-width: 180px;
}

.logo-icon {
  height: 32px;
  width: auto;
}

.collapse-button {
  width: 28px;
  height: 28px;
  border: 1px solid #e9ecef;
  background: #f8f9fa;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.collapse-button:hover {
  background: #e9ecef;
}

.user-info {
  padding: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
  border-bottom: 1px solid #f0f0f0;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--bs-primary);
  color: white;
  font-weight: 600;
  font-size: 0.85rem;
}

.user-details {
  overflow: hidden;
}

.user-name {
  margin: 0;
  font-size: 0.95rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  margin: 0;
  font-size: 0.8rem;
  color: #6c757d;
}

.side-nav-menu {
  flex: 1;
  overflow-y: auto;
  padding: 15px 0;
}

.nav-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-item {
  margin-bottom: 5px;
}

.nav-link {
  display: flex;
  align-items: center;
  padding: 10px 15px;
  color: #495057;
  text-decoration: none;
  transition: all 0.2s;
  border-radius: 5px;
  margin: 0 8px;
  position: relative;
}

.nav-link:hover {
  background-color: rgba(var(--bs-primary-rgb), 0.05);
  color: var(--bs-primary);
}

.nav-link.active {
  background-color: rgba(var(--bs-primary-rgb), 0.1);
  color: var(--bs-primary);
  font-weight: 500;
}

.nav-icon {
  font-size: 1.1rem;
  width: 22px;
  text-align: center;
  margin-right: 10px;
}

.nav-text {
  flex: 1;
}

.arrow-icon {
  margin-left: 5px;
  font-size: 0.8rem;
  transition: transform 0.2s;
}

.submenu {
  list-style: none;
  padding: 0;
  margin: 0;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease;
}

.submenu.show {
  max-height: 500px;
}

.submenu-item {
  margin: 5px 0;
}

.submenu-link {
  display: flex;
  align-items: center;
  padding: 8px 15px 8px 47px;
  color: #6c757d;
  text-decoration: none;
  font-size: 0.9rem;
  border-radius: 5px;
  margin: 0 8px;
  transition: all 0.2s;
}

.submenu-link:hover {
  background-color: rgba(var(--bs-primary-rgb), 0.05);
  color: var(--bs-primary);
}

.submenu-link.active {
  background-color: rgba(var(--bs-primary-rgb), 0.1);
  color: var(--bs-primary);
  font-weight: 500;
}

.submenu-icon {
  font-size: 0.9rem;
  width: 16px;
  text-align: center;
  margin-right: 10px;
}

.side-nav-footer {
  padding: 15px;
  border-top: 1px solid #f0f0f0;
  display: flex;
  justify-content: space-around;
}

.footer-link {
  color: #495057;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px;
  border-radius: 5px;
  transition: all 0.2s;
}

.footer-link:hover {
  background-color: rgba(var(--bs-primary-rgb), 0.05);
  color: var(--bs-primary);
}

/* Ajustes para navegación colapsada */
.side-navigation-collapsed .nav-text,
.side-navigation-collapsed .arrow-icon,
.side-navigation-collapsed .submenu,
.side-navigation-collapsed .user-details {
  display: none;
}

.side-navigation-collapsed .nav-link,
.side-navigation-collapsed .submenu-link {
  justify-content: center;
  padding: 10px;
}

.side-navigation-collapsed .nav-icon,
.side-navigation-collapsed .submenu-icon {
  margin-right: 0;
}

.side-navigation-collapsed .footer-link {
  justify-content: center;
}

/* Estilo para RTL */
[dir="rtl"] .side-navigation {
  left: auto;
  right: 0;
}

[dir="rtl"] .nav-icon {
  margin-right: 0;
  margin-left: 10px;
}

[dir="rtl"] .arrow-icon {
  margin-left: 0;
  margin-right: 5px;
  transform: rotate(180deg);
}

[dir="rtl"] .submenu-link {
  padding: 8px 47px 8px 15px;
}

[dir="rtl"] .submenu-icon {
  margin-right: 0;
  margin-left: 10px;
}
</style>

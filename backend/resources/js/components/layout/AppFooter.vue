<template>
  <footer class="app-footer mt-auto py-4">
    <div class="container">
      <div class="row g-4">
        <!-- Columna de información de la empresa -->
        <div class="col-lg-4 col-md-6">
          <div class="footer-brand mb-3">
            <img v-if="logoUrl" :src="logoUrl" height="36" alt="Aura Logo" />
            <h4 v-else class="text-primary fw-bold">Aura</h4>
          </div>
          <p class="footer-description">
            {{ $t('footer.description') }}
          </p>
          <div class="social-links mt-3">
            <a v-for="(link, index) in socialLinks" :key="index" :href="link.url" target="_blank" rel="noopener noreferrer" class="social-link" :title="link.title">
              <i :class="link.icon"></i>
            </a>
          </div>
        </div>
        
        <!-- Columna de enlaces rápidos -->
        <div class="col-lg-2 col-md-6 col-6">
          <h5 class="footer-title">{{ $t('footer.quick_links') }}</h5>
          <ul class="footer-links list-unstyled">
            <li v-for="(link, index) in quickLinks" :key="index">
              <router-link :to="link.url">{{ link.text }}</router-link>
            </li>
          </ul>
        </div>
        
        <!-- Columna de enlaces legales -->
        <div class="col-lg-2 col-md-6 col-6">
          <h5 class="footer-title">{{ $t('footer.legal') }}</h5>
          <ul class="footer-links list-unstyled">
            <li v-for="(link, index) in legalLinks" :key="index">
              <router-link :to="link.url">{{ link.text }}</router-link>
            </li>
          </ul>
        </div>
        
        <!-- Columna de contacto -->
        <div class="col-lg-4 col-md-6">
          <h5 class="footer-title">{{ $t('footer.contact_us') }}</h5>
          <ul class="footer-contact list-unstyled">
            <li v-if="contactInfo.address">
              <i class="fas fa-map-marker-alt me-2"></i> {{ contactInfo.address }}
            </li>
            <li v-if="contactInfo.email">
              <i class="fas fa-envelope me-2"></i> {{ contactInfo.email }}
            </li>
            <li v-if="contactInfo.phone">
              <i class="fas fa-phone-alt me-2"></i> {{ contactInfo.phone }}
            </li>
          </ul>
          
          <!-- Formulario de suscripción al boletín -->
          <form v-if="showNewsletter" @submit.prevent="subscribeNewsletter" class="newsletter-form mt-3">
            <div class="input-group">
              <input type="email" class="form-control" v-model="newsletterEmail" :placeholder="$t('footer.newsletter_placeholder')" required>
              <button class="btn btn-primary" type="submit" :disabled="subscribeLoading">
                <span v-if="subscribeLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <i v-else class="fas fa-paper-plane"></i>
              </button>
            </div>
            <small class="form-text text-muted mt-2">{{ $t('footer.newsletter_hint') }}</small>
          </form>
        </div>
      </div>
      
      <!-- Separador -->
      <hr class="my-4">
      
      <!-- Copyright y enlaces del pie -->
      <div class="row align-items-center">
        <div class="col-md-6 text-center text-md-start">
          <p class="mb-0">
            &copy; {{ new Date().getFullYear() }} Aura. {{ $t('footer.all_rights_reserved') }}
          </p>
        </div>
        <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
          <div class="footer-bottom-links">
            <slot name="footerBottomLinks">
              <router-link v-for="(link, index) in bottomLinks" :key="index" :to="link.url" class="mx-2">
                {{ link.text }}
              </router-link>
            </slot>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'AppFooter',
  props: {
    /**
     * Logo URL
     */
    logoUrl: {
      type: String,
      default: '/img/logo.png'
    },
    /**
     * Enlaces de redes sociales
     */
    socialLinks: {
      type: Array,
      default: () => [
        { title: 'Facebook', url: '#', icon: 'fab fa-facebook-f' },
        { title: 'Twitter', url: '#', icon: 'fab fa-twitter' },
        { title: 'Instagram', url: '#', icon: 'fab fa-instagram' },
        { title: 'LinkedIn', url: '#', icon: 'fab fa-linkedin-in' }
      ]
    },
    /**
     * Enlaces rápidos
     */
    quickLinks: {
      type: Array,
      default: () => [
        { text: 'Home', url: '/' },
        { text: 'About', url: '/about' },
        { text: 'Services', url: '/services' },
        { text: 'Contact', url: '/contact' }
      ]
    },
    /**
     * Enlaces legales
     */
    legalLinks: {
      type: Array,
      default: () => [
        { text: 'Terms', url: '/terms' },
        { text: 'Privacy', url: '/privacy' },
        { text: 'Cookies', url: '/cookies' },
        { text: 'FAQ', url: '/faq' }
      ]
    },
    /**
     * Enlaces del pie
     */
    bottomLinks: {
      type: Array,
      default: () => [
        { text: 'Terms', url: '/terms' },
        { text: 'Privacy', url: '/privacy' },
        { text: 'Support', url: '/support' }
      ]
    },
    /**
     * Información de contacto
     */
    contactInfo: {
      type: Object,
      default: () => ({
        address: 'Khartoum, Sudan',
        email: 'info@aura.com',
        phone: '+249 123 456 789'
      })
    },
    /**
     * Mostrar formulario de newsletter
     */
    showNewsletter: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      newsletterEmail: '',
      subscribeLoading: false
    };
  },
  methods: {
    /**
     * Suscribirse al boletín
     */
    subscribeNewsletter() {
      if (!this.newsletterEmail) return;
      
      this.subscribeLoading = true;
      
      // Simulación de envío de solicitud
      setTimeout(() => {
        this.subscribeLoading = false;
        this.$emit('newsletter-subscribe', this.newsletterEmail);
        this.newsletterEmail = '';
        
        // Aquí normalmente se enviaría una solicitud al backend
      }, 1000);
    }
  }
};
</script>

<style scoped>
.app-footer {
  background-color: #f8f9fa;
  border-top: 1px solid #e9ecef;
  color: #6c757d;
}

.footer-brand {
  margin-bottom: 1rem;
}

.footer-description {
  max-width: 300px;
}

.footer-title {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 1.25rem;
  color: #212529;
}

.footer-links li {
  margin-bottom: 0.75rem;
}

.footer-links a {
  color: #6c757d;
  text-decoration: none;
  transition: color 0.2s;
}

.footer-links a:hover {
  color: var(--bs-primary);
}

.footer-contact li {
  margin-bottom: 0.75rem;
  display: flex;
  align-items: flex-start;
}

.footer-contact li i {
  margin-top: 0.25rem;
  color: var(--bs-primary);
}

.social-links {
  display: flex;
  gap: 0.75rem;
}

.social-link {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background-color: rgba(var(--bs-primary-rgb), 0.1);
  color: var(--bs-primary);
  transition: all 0.2s;
}

.social-link:hover {
  background-color: var(--bs-primary);
  color: white;
}

.newsletter-form .input-group {
  border-radius: 0.375rem;
  overflow: hidden;
}

.footer-bottom-links a {
  color: #6c757d;
  text-decoration: none;
  transition: color 0.2s;
}

.footer-bottom-links a:hover {
  color: var(--bs-primary);
}

/* Estilos RTL para árabe */
[dir="rtl"] .social-links {
  padding-right: 0;
}

[dir="rtl"] .footer-contact li i {
  margin-right: 0;
  margin-left: 0.5rem;
}
</style>

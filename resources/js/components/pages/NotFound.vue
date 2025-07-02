<template>
  <main class="main not-found-page">
    <section class="container text-center py-10">
      <div class="error-visual">
        <h1 class="error-code">404</h1>
        <div class="error-icon">
          <i class="fas fa-search"></i>
        </div>
      </div>
      <h2 class="error-title">{{ t('errors.pageNotFound') }}</h2>
      <p class="error-description">{{ t('errors.pageNotFoundDesc') }}</p>
      <div class="error-actions">
        <router-link to="/" class="btn btn-primary">
          <i class="fas fa-home"></i>
          {{ t('common.goHome') }}
        </router-link>
        <router-link to="/dashboard" v-if="isAuthenticated" class="btn btn-secondary">
          <i class="fas fa-tachometer-alt"></i>
          {{ t('dashboard') }}
        </router-link>
      </div>
    </section>
  </main>
</template>

<script>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

export default {
  name: 'NotFound',
  setup() {
    const { t } = useI18n()
    
    const isAuthenticated = computed(() => {
      return !!localStorage.getItem('auth_token')
    })
    
    return {
      t,
      isAuthenticated
    }
  }
}
</script>

<style scoped>
.not-found-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-base);
  color: var(--text-primary);
  padding: var(--space-lg);
}

.container {
  max-width: 600px;
  text-align: center;
}

.error-visual {
  position: relative;
  margin-bottom: var(--space-xl);
}

.error-code {
  font-size: 8rem;
  font-weight: 900;
  color: var(--text-muted);
  margin: 0;
  line-height: 1;
  opacity: 0.3;
}

.error-icon {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 3rem;
  color: var(--primary);
}

.error-title {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: var(--space-md);
}

.error-description {
  font-size: 1.125rem;
  color: var(--text-secondary);
  margin-bottom: var(--space-xl);
  line-height: 1.6;
}

.error-actions {
  display: flex;
  gap: var(--space-md);
  justify-content: center;
  flex-wrap: wrap;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: var(--space-sm);
  padding: var(--space-md) var(--space-xl);
  border-radius: var(--radius-lg);
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  border: none;
  cursor: pointer;
}

.btn-primary {
  background: var(--primary);
  color: white;
}

.btn-primary:hover {
  background: var(--primary-light);
  transform: translateY(-2px);
}

.btn-secondary {
  background: var(--bg-secondary);
  color: var(--text-primary);
  border: 1px solid var(--border);
}

.btn-secondary:hover {
  background: var(--bg-tertiary);
  transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 768px) {
  .error-code {
    font-size: 6rem;
  }
  
  .error-title {
    font-size: 1.5rem;
  }
  
  .error-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .btn {
    width: 100%;
    max-width: 200px;
  }
}

/* Dark Theme Support */
body.dark-theme .not-found-page {
  background: var(--bg-base);
  color: var(--text-primary);
}
</style> 
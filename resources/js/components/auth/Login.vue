<template>
  <main class="auth-main">
    <!-- Animated Background (same as homepage) -->
    <div class="animated-bg">
      <div class="gradient-sphere sphere-1"></div>
      <div class="gradient-sphere sphere-2"></div>
      <div class="gradient-sphere sphere-3"></div>
    </div>

    <div class="auth-container">
      <!-- Auth card with Contact.vue style -->
      <div class="form-container reveal-on-scroll reveal-scale">
        <div class="auth-header text-center mb-xl">
          <div class="auth-logo">
            <i class="fas fa-file-alt"></i>
          </div>
          <h1 class="section-title">{{ t('welcome_back') }}</h1>
          <p class="section-description">{{ t('loginDescription') }}</p>
        </div>

        <!-- Messages -->
        <div v-if="error || successMessage" class="message-area">
          <div v-if="error" class="message error" role="alert">
            <i class="fas fa-exclamation-triangle icon"></i>
            <p class="message-text">{{ error }}</p>
          </div>
          <div v-if="successMessage" class="message success" role="status">
            <i class="fas fa-check-circle icon"></i>
            <p class="message-text">{{ successMessage }}</p>
          </div>
        </div>

        <!-- Login form -->
        <form class="contact-form" @submit.prevent="handleLogin">
          <div class="form-group">
            <label for="email" class="form-label required">{{ t('email') }}</label>
            <div class="input-icon-wrapper">
              <i class="fas fa-envelope input-icon"></i>
              <input
                type="email"
                v-model="form.email"
                id="email"
                required
                :placeholder="t('your_email_address')"
                :disabled="loading"
                autocomplete="email"
                class="form-input"
              />
            </div>
          </div>

          <div class="form-group">
            <label for="password" class="form-label required">{{ t('password') }}</label>
            <div class="input-icon-wrapper">
              <i class="fas fa-lock input-icon"></i>
              <input
                type="password"
                v-model="form.password"
                id="password"
                required
                :placeholder="t('password_placeholder')"
                :disabled="loading"
                autocomplete="current-password"
                class="form-input"
              />
            </div>
          </div>

          <div class="form-actions">
            <button
              type="submit"
              class="btn btn-primary btn-with-icon"
              :disabled="loading"
            >
              <i v-if="loading" class="fas fa-spinner fa-spin icon"></i>
              <i v-else class="fas fa-sign-in-alt icon"></i>
              {{ t('sign_in') }}
            </button>
          </div>
        </form>

        <!-- Alternative action -->
        <div class="auth-footer">
          <p class="form-alternate-action">
            {{ t('no_account') }}
            <router-link to="/register" class="auth-link">{{ t('register_here') }}</router-link>
          </p>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
  import { ref, reactive, onMounted } from 'vue'
  import { useRouter, useRoute } from 'vue-router'
  import { useI18n } from 'vue-i18n'
  import authService from '../../services/authService'

  export default {
    name: 'Login',
    setup() {
      const { t } = useI18n()
      const router = useRouter()
      const route = useRoute()
      const loading = ref(false)
      const error = ref('')
      const successMessage = ref('')

      const form = reactive({
        email: '',
        password: '',
      })

      onMounted(() => {
        if (route.query.success) {
          successMessage.value = route.query.success
        }
        
        // If already authenticated, redirect to dashboard
        if (authService.isAuthenticated()) {
          router.push('/dashboard')
        }
      })

      const handleLogin = async () => {
        if (loading.value) return

        loading.value = true
        error.value = ''
        successMessage.value = ''

        try {
          const result = await authService.login({
            email: form.email,
            password: form.password,
          })

          if (result.success) {
            const redirectPath = route.query.redirect || '/dashboard'
            await router.push(redirectPath)
          } else {
            error.value = result.message
          }
        } catch (err) {
          console.error('Login error:', err)
          error.value = t('login_error')
        } finally {
          loading.value = false
        }
      }

      return {
        form,
        loading,
        error,
        successMessage,
        handleLogin,
        t
      }
    },
  }
</script>

<style scoped>
/* Auth Main Layout - Fixed background for white mode */
.auth-main {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-base);
  position: relative;
  overflow: hidden;
  padding: var(--space-lg);
}

.auth-container {
  position: relative;
  z-index: 2;
  width: 100%;
  max-width: 500px;
}

/* Animated Background (same as homepage) */
.animated-bg {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
  overflow: hidden;
  pointer-events: none;
  opacity: 0.3;
}

.gradient-sphere {
  position: absolute;
  border-radius: 50%;
  filter: blur(120px);
  opacity: 0.6;
  animation: float 20s infinite ease-in-out;
}

.sphere-1 {
  width: 600px;
  height: 600px;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
  top: -300px;
  left: -300px;
  animation-delay: 0s;
}

.sphere-2 {
  width: 400px;
  height: 400px;
  background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
  bottom: -200px;
  right: -200px;
  animation-delay: 5s;
}

.sphere-3 {
  width: 300px;
  height: 300px;
  background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  animation-delay: 10s;
}

@keyframes float {
  0%, 100% { transform: translate(0, 0) scale(1); }
  25% { transform: translate(100px, -100px) scale(1.1); }
  50% { transform: translate(-100px, 100px) scale(0.9); }
  75% { transform: translate(50px, 50px) scale(1.05); }
}

/* Form Container (similar to Contact.vue) */
.form-container {
  max-width: 600px;
  margin: var(--space-xxl) auto;
  padding: var(--space-xl);
  position: relative;
}

/* Auth Header */
.auth-header {
  margin-bottom: var(--space-xl);
}

.auth-logo {
  width: 80px;
  height: 80px;
  background: var(--gradient-primary);
  border-radius: var(--radius-xl);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto var(--space-lg);
  box-shadow: var(--shadow-primary);
}

.auth-logo i {
  font-size: 2rem;
  color: white;
}

/* Section styling (reusing Contact.vue classes) */
.section-title {
  font-family: var(--font-heading);
  font-size: var(--font-size-4xl);
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: var(--space-md);
  line-height: 1.2;
  letter-spacing: -0.02em;
}

.section-description {
  font-size: var(--font-size-lg);
  color: var(--text-secondary);
  line-height: var(--line-height-normal);
  margin: 0 auto var(--space-xl);
}

.text-center {
  text-align: center;
}

.mb-xl {
  margin-bottom: var(--space-xl);
}

/* Message Area */
.message-area {
  margin-bottom: var(--space-lg);
}

.message {
  padding: var(--space-md);
  border-radius: var(--radius);
  margin-bottom: var(--space-lg);
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  font-size: var(--font-size-base);
}

.message.success {
  background-color: rgba(16, 185, 129, 0.1);
  color: var(--success-dark);
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.message.error {
  background-color: rgba(239, 68, 68, 0.1);
  color: var(--danger-dark);
  border: 1px solid rgba(239, 68, 68, 0.2);
}

.message .icon {
  font-size: var(--font-size-lg);
}

.message-text {
  margin: 0;
}

/* Form Styling (reusing Contact.vue structure) */
.contact-form {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
}

.form-group {
  margin-bottom: var(--space-lg);
}

.form-label {
  display: block;
  font-weight: 500;
  color: var(--text-primary);
  margin-bottom: var(--space-sm);
  font-size: var(--font-size-sm);
  letter-spacing: var(--letter-spacing-wide);
}

.form-label.required::after {
  content: ' *';
  color: var(--danger);
  font-weight: 400;
}

.input-icon-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: var(--space-md);
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
  pointer-events: none;
  transition: var(--transition-colors);
}

.input-icon-wrapper:focus-within .input-icon {
  color: var(--primary);
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.75rem;
  border: 1.5px solid var(--border);
  border-radius: var(--radius);
  font-size: var(--font-size-base);
  color: var(--text-primary);
  background-color: var(--bg-elevated);
  transition: var(--transition-all);
  font-family: inherit;
  line-height: var(--line-height-normal);
}

.form-input:hover:not(:focus):not(:disabled) {
  border-color: var(--border-hover);
}

.form-input:focus {
  outline: none;
  border-color: var(--border-focus);
  box-shadow: var(--focus-ring-style);
}

.form-input:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background-color: var(--bg-secondary);
}

/* Form Actions */
.form-actions {
  display: flex;
  justify-content: center;
  margin-top: var(--space-lg);
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-sm);
  padding: 0.875rem 2rem;
  border: 2px solid transparent;
  border-radius: var(--radius);
  font-size: var(--font-size-base);
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: var(--transition-all);
  font-family: inherit;
  white-space: nowrap;
  min-width: 160px;
}

.btn-primary {
  background: var(--gradient-primary);
  color: white;
  box-shadow: var(--shadow-primary);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn .icon {
  font-size: 1em;
}

/* Auth Footer */
.auth-footer {
  text-align: center;
  padding-top: var(--space-lg);
  border-top: 1px solid var(--border-light);
  margin-top: var(--space-lg);
}

.form-alternate-action {
  color: var(--text-secondary);
  font-size: var(--font-size-sm);
  margin: 0;
}

.auth-link {
  color: var(--primary);
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s ease;
}

.auth-link:hover {
  color: var(--primary-dark);
  text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
  .auth-main {
    padding: var(--space-md);
  }
  
  .form-container {
    padding: var(--space-xl);
  }
  
  .section-title {
    font-size: var(--font-size-3xl);
  }
  
  .section-description {
    font-size: var(--font-size-base);
  }
  
  .auth-logo {
    width: 64px;
    height: 64px;
  }
  
  .auth-logo i {
    font-size: 1.5rem;
  }
  
  .gradient-sphere {
    filter: blur(80px);
  }
  
  .sphere-1 {
    width: 400px;
    height: 400px;
  }
  
  .sphere-2 {
    width: 300px;
    height: 300px;
  }
  
  .sphere-3 {
    width: 200px;
    height: 200px;
  }
}

@media (max-width: 480px) {
  .form-container {
    padding: var(--space-lg);
  }
  
  .btn {
    width: 100%;
  }
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
  .gradient-sphere {
    animation: none;
  }
  
  .btn-primary:hover:not(:disabled) {
    transform: none;
  }
}

/* Dark Theme Overrides */
body.dark-theme .auth-main {
  background: var(--bg-base);
  color: var(--text-primary);
}

body.dark-theme .animated-bg .gradient-sphere {
  opacity: 0.2;
}

body.dark-theme .form-container {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-xl);
}

body.dark-theme .section-title {
  color: var(--text-primary);
}

body.dark-theme .section-description {
  color: var(--text-secondary);
}

body.dark-theme .message.success {
  background-color: rgba(16, 185, 129, 0.1);
  color: var(--success-light);
  border-color: var(--success);
}

body.dark-theme .message.error {
  background-color: rgba(239, 68, 68, 0.1);
  color: var(--red-400);
  border-color: var(--red-500);
}

body.dark-theme .form-label {
  color: var(--text-primary);
}

body.dark-theme .form-input {
  background-color: var(--bg-elevated);
  border-color: var(--border);
  color: var(--text-primary);
}

body.dark-theme .form-input:hover:not(:focus):not(:disabled) {
  border-color: var(--border-hover);
}

body.dark-theme .form-input:focus {
  border-color: var(--border-focus);
  box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

body.dark-theme .form-input::placeholder {
  color: var(--text-muted);
}

body.dark-theme .input-icon {
  color: var(--text-muted);
}

body.dark-theme .input-icon-wrapper:focus-within .input-icon {
  color: var(--primary-light);
}

body.dark-theme .form-input:disabled {
  background-color: var(--bg-secondary);
  color: var(--text-muted);
}

body.dark-theme .auth-footer {
  border-color: var(--border);
}

body.dark-theme .form-alternate-action {
  color: var(--text-secondary);
}

body.dark-theme .auth-link {
  color: var(--primary-light);
}

body.dark-theme .auth-link:hover {
  color: var(--primary);
}
</style>

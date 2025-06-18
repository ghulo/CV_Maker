<template>
  <main class="main">
    <div class="login-form-container page-container animate-in">
      <h2 class="reveal-on-scroll">Mirë se Vini Përsëri!</h2>
      <p class="form-intro-text reveal-on-scroll" data-reveal-delay="100">
        Kyçu në llogarinë tënde për të vazhduar menaxhimin e CV-ve tua.
      </p>

      <div class="message-area">
        <div v-if="error" class="message error" role="alert">
          {{ error }}
        </div>
        <div v-if="successMessage" class="message success" role="status">
          {{ successMessage }}
        </div>
      </div>

      <form class="login-form" @submit.prevent="handleLogin">
        <div class="form-group reveal-on-scroll" data-reveal-delay="150">
          <label for="email">Email</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input
              type="email"
              v-model="form.email"
              id="email"
              required
              placeholder="Adresa juaj e emailit"
              :disabled="loading"
              autocomplete="email"
            />
          </div>
        </div>
        <div class="form-group reveal-on-scroll" data-reveal-delay="200">
          <label for="password">Fjalëkalimi</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input
              type="password"
              v-model="form.password"
              id="password"
              required
              placeholder="••••••••"
              :disabled="loading"
              autocomplete="current-password"
            />
          </div>
        </div>
        <button
          type="submit"
          class="btn-primary-form btn btn-primary reveal-on-scroll"
          data-reveal-delay="250"
          :disabled="loading"
        >
          <i v-if="loading" class="fas fa-spinner fa-spin"></i>
          <span v-else>Kyçu <i class="fas fa-sign-in-alt icon-right"></i></span>
        </button>
      </form>
      <p class="form-alternate-action reveal-on-scroll" data-reveal-delay="300">
        Nuk keni llogari? <router-link to="/register">Regjistrohu këtu</router-link>
      </p>
    </div>
  </main>
</template>

<script>
  import { ref, reactive, onMounted } from 'vue'
  import { useRouter, useRoute } from 'vue-router'
  import axios from 'axios'

  export default {
    name: 'Login',
    setup() {
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
      })

      const handleLogin = async () => {
        if (loading.value) return

        loading.value = true
        error.value = ''
        successMessage.value = ''

        try {
          await axios.get('/sanctum/csrf-cookie')
          const response = await axios.post('/api/login', {
            email: form.email,
            password: form.password,
          })

          if (response.data.token) {
            localStorage.setItem('auth_token', response.data.token)
            localStorage.setItem('user', JSON.stringify(response.data.user))
            axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`

            const redirectPath = route.query.redirect || '/dashboard'
            router.push(redirectPath)
          }
        } catch (err) {
          if (err.response?.status === 422) {
            error.value = err.response.data.message || 'Të dhënat e dhëna janë të pavlefshme.'
          } else if (err.response?.data?.message) {
            error.value = err.response.data.message
          } else {
            error.value = 'Ka ndodhur një gabim gjatë kyçjes. Provoni përsëri.'
          }
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
      }
    },
  }
</script>

<style scoped>
  @reference "tailwindcss/theme";
  /* Styles specific to Login.vue and other auth forms */
  .login-form-container {
    padding: var(--space-xl) var(--space-lg);
    max-width: 700px;
    margin-left: auto; margin-right: auto;
    margin-top: var(--space-xl);
    margin-bottom: var(--space-xl);
  }

  body.dark-theme .login-form-container {
    /* specific dark theme adjustments if needed */
  }

  .form-intro-text {
      font-size: 1em;
      color: var(--muted-text);
      text-align: center;
      margin-bottom: var(--space-lg);
      line-height: 1.5;
      max-width: 600px;
      margin-left: auto; margin-right: auto;
  }
  body.dark-theme .form-intro-text {
      color: var(--dark-muted-text);
  }

  .message-area {
      margin-bottom: var(--space-md);
      min-height: 2em; /* Ensure consistent height even without message */
  }
  .message {
      padding: var(--space-md) var(--space-lg);
      border-radius: var(--radius-sm);
      text-align: center;
      font-weight: 500;
      border: 1px solid transparent;
      transition: all 0.3s ease;
  }

  .message.success {
      background-color: var(--message-success-bg);
      color: var(--message-success-color);
      border-color: var(--message-success-border);
  }

  .message.error {
      background-color: var(--message-error-bg);
      color: var(--message-error-color);
      border-color: var(--message-error-border);
  }

  body.dark-theme .message.success {
      background-color: var(--dark-message-success-bg);
      color: var(--dark-message-success-color);
      border-color: var(--dark-message-success-border);
  }

  body.dark-theme .message.error {
      background-color: var(--dark-message-error-bg);
      color: var(--dark-message-error-color);
      border-color: var(--dark-message-error-border);
  }

  .form-group {
      margin-bottom: var(--space-md);
  }
  .form-group label {
      font-weight: 500;
      font-size: 0.9em;
      margin-bottom: var(--space-xs);
      display: block;
      color: var(--neutral-text);
  }
  body.dark-theme .form-group label { color: var(--dark-neutral-text); }

  .form-group input, .form-group textarea, .form-group select {
      padding: var(--space-sm) var(--space-md);
      font-size: 1em;
      width: 100%;
      border: 1px solid var(--neutral-border);
      border-radius: var(--radius-sm);
      background-color: var(--neutral-light);
      color: var(--neutral-text);
      transition: border-color 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease, color 0.3s ease;
  }
  .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 1px var(--form-focus-glow-light);
  }
  body.dark-theme .form-group input:focus, body.dark-theme .form-group textarea:focus, body.dark-theme .form-group select:focus {
      border-color: var(--dark-primary);
      box-shadow: 0 0 0 3px var(--form-focus-glow-dark);
  }

  .input-icon-wrapper {
      position: relative;
      display: flex;
      align-items: center;
  }
  .input-icon-wrapper input {
      padding-left: var(--space-xl);
  }
  .input-icon {
      position: absolute;
      left: var(--space-sm);
      color: var(--muted-text);
      font-size: 1em;
  }
  body.dark-theme .input-icon { color: var(--dark-muted-text); }

  .btn-primary-form {
      width: 100%;
      padding: var(--space-md);
      font-size: 1.05em;
      font-weight: 600;
      margin-top: var(--space-sm);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: var(--space-sm);
  }
  .icon-right {
      margin-left: var(--space-xs);
      transition: transform 0.2s ease;
  }
  .btn-primary-form:hover .icon-right {
      transform: translateX(3px);
  }

  .form-alternate-action {
      text-align: center;
      margin-top: var(--space-lg);
      font-size: 0.9em;
      color: var(--muted-text);
  }
  .form-alternate-action a {
      color: var(--primary);
      font-weight: 500;
      text-decoration: none;
      transition: color 0.2s ease, text-decoration 0.2s ease;
      position: relative; /* For animated underline */
  }
  .form-alternate-action a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 100%;
      height: var(--link-underline-thickness);
      background-color: var(--link-underline-color);
      transform: scaleX(0);
      transform-origin: bottom right;
      transition: transform 0.3s var(--animation-ease-out);
      border-radius: 1px;
  }
  .form-alternate-action a:hover::after {
      transform: scaleX(1);
      transform-origin: bottom left;
  }
  body.dark-theme .form-alternate-action a::after {
      background-color: var(--link-underline-color-dark);
  }

  body.dark-theme .form-alternate-action {
      color: var(--dark-muted-text);
  }
  body.dark-theme .form-alternate-action a {
      color: var(--dark-primary);
  }
</style>

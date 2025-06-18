<template>
  <main class="main">
    <div class="signup-form-container page-container animate-in">
      <h2 class="reveal-on-scroll">Krijo Llogarinë Tënde</h2>
      <p class="form-intro-text reveal-on-scroll" data-reveal-delay="100">
        Regjistrohuni për të krijuar, ruajtur dhe menaxhuar CV-të tuaja profesionale.
      </p>

      <div class="message-area">
        <div v-if="error" class="message error" role="alert">
          {{ error }}
        </div>
        <ul v-if="validationErrors" class="message error" role="alert">
          <li v-for="(value, key) in validationErrors" :key="key">{{ value[0] }}</li>
        </ul>
      </div>

      <form class="signup-form" @submit.prevent="handleRegister">
        <div class="form-group reveal-on-scroll" data-reveal-delay="150">
          <label for="name">Emri i plotë</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-user input-icon"></i>
            <input
              type="text"
              v-model="form.name"
              id="name"
              required
              placeholder="Emri dhe Mbiemri"
              :disabled="loading"
            />
          </div>
        </div>
        <div class="form-group reveal-on-scroll" data-reveal-delay="200">
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
            />
          </div>
        </div>
        <div class="form-group reveal-on-scroll" data-reveal-delay="250">
          <label for="password">Fjalëkalimi</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input
              type="password"
              v-model="form.password"
              id="password"
              required
              placeholder="Minimumi 8 karaktere"
              :disabled="loading"
            />
          </div>
        </div>
        <div class="form-group reveal-on-scroll" data-reveal-delay="300">
          <label for="password_confirmation">Konfirmo Fjalëkalimin</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input
              type="password"
              v-model="form.password_confirmation"
              id="password_confirmation"
              required
              placeholder="Shkruani përsëri fjalëkalimin"
              :disabled="loading"
            />
          </div>
        </div>
        <button
          type="submit"
          class="btn-primary-form btn btn-primary reveal-on-scroll"
          data-reveal-delay="350"
          :disabled="loading"
        >
          <i v-if="loading" class="fas fa-spinner fa-spin"></i>
          <span v-else>Regjistrohu <i class="fas fa-user-plus icon-right"></i></span>
        </button>
      </form>
      <p class="form-alternate-action reveal-on-scroll" data-reveal-delay="400">
        Keni tashmë një llogari? <router-link to="/login">Kyçu këtu</router-link>
      </p>
    </div>
  </main>
</template>

<script>
  import { ref, reactive } from 'vue'
  import { useRouter } from 'vue-router'
  import axios from 'axios'

  export default {
    name: 'Register',
    setup() {
      const router = useRouter()
      const loading = ref(false)
      const error = ref('')
      const validationErrors = ref(null)

      const form = reactive({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
      })

      const handleRegister = async () => {
        if (loading.value) return

        loading.value = true
        error.value = ''
        validationErrors.value = null

        try {
          await axios.get('/sanctum/csrf-cookie')
          const response = await axios.post('/api/register', form)

          if (response.data.success) {
            router.push({
              name: 'login',
              query: { success: 'Llogaria u krijua me sukses! Ju lutemi kyçuni.' },
            })
          }
        } catch (err) {
          if (err.response?.status === 422) {
            validationErrors.value = err.response.data.errors
            error.value = 'Ju lutemi korrigjoni gabimet e mëposhtme.'
          } else {
            error.value =
              err.response?.data?.message ||
              'Ka ndodhur një gabim gjatë regjistrimit. Provoni përsëri.'
          }
        } finally {
          loading.value = false
        }
      }

      return {
        form,
        loading,
        error,
        validationErrors,
        handleRegister,
      }
    },
  }
</script>

<style scoped>
  @reference "tailwindcss/theme";
  /* Styles specific to Login.vue and other auth forms */
  .signup-form-container {
    padding: var(--space-xl) var(--space-lg);
    max-width: 700px;
    margin-left: auto; margin-right: auto;
    margin-top: var(--space-xl);
    margin-bottom: var(--space-xl);
  }

  body.dark-theme .signup-form-container {
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

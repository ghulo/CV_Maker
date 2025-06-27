<template>
  <main class="main">
    <div class="auth-form-container page-container reveal-on-scroll reveal-scale">
      <h1 class="section-title text-center">{{ t('welcome_back') }}</h1>
      <p class="form-intro-text reveal-on-scroll reveal-delay-100">
        {{ t('login_intro') }}
      </p>

      <div class="message-area">
        <div v-if="error" class="message error" role="alert">
          <i class="fas fa-exclamation-triangle icon"></i>
          <p class="message-text">{{ error }}</p>
        </div>
        <div v-if="successMessage" class="message success" role="status">
          <i class="fas fa-check-circle icon"></i>
          <p class="message-text">{{ successMessage }}</p>
        </div>
      </div>

      <form class="login-form" @submit.prevent="handleLogin">
        <div class="form-group reveal-on-scroll reveal-delay-150">
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
        <div class="form-group reveal-on-scroll reveal-delay-200">
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
        <button
          type="submit"
          class="btn btn-primary btn-full-width btn-with-icon reveal-on-scroll reveal-delay-250"
          :disabled="loading"
        >
          <i v-if="loading" class="fas fa-spinner fa-spin icon"></i>
          <i v-else class="fas fa-sign-in-alt icon"></i>
          {{ t('sign_in') }}
        </button>
      </form>
      <p class="form-alternate-action reveal-on-scroll reveal-delay-300">
        {{ t('no_account') }} <router-link to="/register">{{ t('register_here') }}</router-link>
      </p>
    </div>
  </main>
</template>

<script>
  import { ref, reactive, onMounted } from 'vue'
  import { useRouter, useRoute } from 'vue-router'
  import { useI18n } from 'vue-i18n'
  import axios from 'axios'

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
            error.value = err.response.data.message || t('invalid_credentials')
          } else if (err.response?.data?.message) {
            error.value = err.response.data.message
          } else {
            error.value = t('login_error')
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
        t
      }
    },
  }
</script>

<style scoped>
  /* No scoped styles here, relying on _auth_forms.css and global styles */
</style>

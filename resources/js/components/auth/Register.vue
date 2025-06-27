<template>
  <main class="main">
    <div class="auth-form-container page-container reveal-on-scroll reveal-scale">
      <h1 class="section-title text-center">{{ t('create_your_account') }}</h1>
      <p class="form-intro-text reveal-on-scroll reveal-delay-100">
        {{ t('register_to_create_manage') }}
      </p>

      <div class="message-area">
        <div v-if="error" class="message error" role="alert">
          <i class="fas fa-exclamation-triangle icon"></i>
          <p class="message-text">{{ error }}</p>
        </div>
        <ul v-if="validationErrors" class="message error" role="alert">
          <li v-for="(value, key) in validationErrors" :key="key" class="message-text">{{ value[0] }}</li>
        </ul>
      </div>

      <form class="signup-form" @submit.prevent="handleRegister">
        <div class="form-group reveal-on-scroll reveal-delay-150">
          <label for="name" class="form-label required">{{ t('full_name') }}</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-user input-icon"></i>
            <input
              type="text"
              v-model="form.name"
              id="name"
              required
              :placeholder="t('full_name_placeholder')"
              :disabled="loading"
              class="form-input"
            />
          </div>
        </div>
        <div class="form-group reveal-on-scroll reveal-delay-200">
          <label for="email" class="form-label required">{{ t('email') }}</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input
              type="email"
              v-model="form.email"
              id="email"
              required
              :placeholder="t('email_placeholder')"
              :disabled="loading"
              class="form-input"
            />
          </div>
        </div>
        <div class="form-group reveal-on-scroll reveal-delay-250">
          <label for="password" class="form-label required">{{ t('password') }}</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input
              type="password"
              v-model="form.password"
              id="password"
              required
              :placeholder="t('password_min_8')"
              :disabled="loading"
              class="form-input"
            />
          </div>
        </div>
        <div class="form-group reveal-on-scroll reveal-delay-300">
          <label for="password_confirmation" class="form-label required">{{ t('confirm_password') }}</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input
              type="password"
              v-model="form.password_confirmation"
              id="password_confirmation"
              required
              :placeholder="t('confirm_password_placeholder')"
              :disabled="loading"
              class="form-input"
            />
          </div>
        </div>
        <button
          type="submit"
          class="btn btn-primary btn-full-width btn-with-icon reveal-on-scroll reveal-delay-350"
          :disabled="loading"
        >
          <i v-if="loading" class="fas fa-spinner fa-spin icon"></i>
          <i v-else class="fas fa-user-plus icon"></i>
          {{ t('register_button') }}
        </button>
      </form>
      <p class="form-alternate-action reveal-on-scroll reveal-delay-400">
        {{ t('already_have_account') }} <router-link to="/login">{{ t('login_here') }}</router-link>
      </p>
    </div>
  </main>
</template>

<script>
  import { ref, reactive } from 'vue'
  import { useRouter } from 'vue-router'
  import { useI18n } from 'vue-i18n'
  import axios from 'axios'

  export default {
    name: 'Register',
    setup() {
      const { t } = useI18n()
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
              query: { success: t('account_created_successfully') },
            })
          }
        } catch (err) {
          if (err.response?.status === 422) {
            validationErrors.value = err.response.data.errors
            error.value = t('please_correct_errors')
          } else {
            error.value = err.response?.data?.message || t('registration_error')
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
        t
      }
    },
  }
</script>

<style scoped>
  /* No scoped styles here, relying on _auth_forms.css and global styles */
</style>

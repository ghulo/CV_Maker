<template>
  <main class="main">
    <div class="login-form-container page-container animate-in">
      <h2 class="reveal-on-scroll">Mirë se Vini Përsëri!</h2>
      <p class="form-intro-text reveal-on-scroll" data-reveal-delay="100">Kyçu në llogarinë tënde për të vazhduar menaxhimin e CV-ve tua.</p>

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
            <input type="email" v-model="form.email" id="email" required placeholder="Adresa juaj e emailit" :disabled="loading">
          </div>
        </div>
        <div class="form-group reveal-on-scroll" data-reveal-delay="200">
        <label for="password">Fjalëkalimi</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" v-model="form.password" id="password" required placeholder="••••••••" :disabled="loading">
          </div>
      </div>
        <button type="submit" class="btn-primary-form btn btn-primary reveal-on-scroll" data-reveal-delay="250" :disabled="loading">
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
import { ref, reactive, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

export default {
  name: 'Login',
  setup() {
    const router = useRouter();
    const route = useRoute();
    const loading = ref(false);
    const error = ref('');
    const successMessage = ref('');

    const form = reactive({
      email: '',
      password: ''
    });

    onMounted(() => {
      if (route.query.success) {
        successMessage.value = route.query.success;
      }
    });

    const handleLogin = async () => {
      if (loading.value) return;
      
      loading.value = true;
      error.value = '';
      successMessage.value = '';

      try {
        await axios.get('/sanctum/csrf-cookie');
        const response = await axios.post('/api/login', {
          email: form.email,
          password: form.password
        });

        if (response.data.token) {
          localStorage.setItem('auth_token', response.data.token);
          localStorage.setItem('user', JSON.stringify(response.data.user));
          axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
          
          const redirectPath = route.query.redirect || '/dashboard';
          router.push(redirectPath);
        }
      } catch (err) {
        if (err.response?.status === 422) {
          error.value = err.response.data.message || 'Të dhënat e dhëna janë të pavlefshme.';
        } else if (err.response?.data?.message) {
          error.value = err.response.data.message;
        } else {
          error.value = 'Ka ndodhur një gabim gjatë kyçjes. Provoni përsëri.';
        }
      } finally {
        loading.value = false;
      }
    };

    return {
      form,
      loading,
      error,
      successMessage,
      handleLogin
    };
  }
}
</script>

<style scoped>
@reference "tailwindcss/theme";
.btn-primary-form {
  width: 100%;
}
</style> 
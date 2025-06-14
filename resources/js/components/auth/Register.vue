<template>
  <main class="main">
    <div class="signup-form-container page-container animate-in">
      <h2 class="reveal-on-scroll">Krijo Llogarinë Tënde</h2>
      <p class="form-intro-text reveal-on-scroll" data-reveal-delay="100">Regjistrohuni për të krijuar, ruajtur dhe menaxhuar CV-të tuaja profesionale.</p>

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
            <input type="text" v-model="form.name" id="name" required placeholder="Emri dhe Mbiemri" :disabled="loading">
          </div>
        </div>
        <div class="form-group reveal-on-scroll" data-reveal-delay="200">
          <label for="email">Email</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" v-model="form.email" id="email" required placeholder="Adresa juaj e emailit" :disabled="loading">
          </div>
        </div>
        <div class="form-group reveal-on-scroll" data-reveal-delay="250">
          <label for="password">Fjalëkalimi</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" v-model="form.password" id="password" required placeholder="Minimumi 8 karaktere" :disabled="loading">
          </div>
        </div>
        <div class="form-group reveal-on-scroll" data-reveal-delay="300">
          <label for="password_confirmation">Konfirmo Fjalëkalimin</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" v-model="form.password_confirmation" id="password_confirmation" required placeholder="Shkruani përsëri fjalëkalimin" :disabled="loading">
          </div>
        </div>
        <button type="submit" class="btn-primary-form btn btn-primary reveal-on-scroll" data-reveal-delay="350" :disabled="loading">
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
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
  name: 'Register',
  setup() {
    const router = useRouter();
    const loading = ref(false);
    const error = ref('');
    const validationErrors = ref(null);

    const form = reactive({
      name: '',
      email: '',
      password: '',
      password_confirmation: ''
    });

    const handleRegister = async () => {
      if (loading.value) return;

      loading.value = true;
      error.value = '';
      validationErrors.value = null;

      try {
        await axios.get('/sanctum/csrf-cookie');
        const response = await axios.post('/api/register', form);

        if (response.data.success) {
          router.push({ 
            name: 'login', 
            query: { success: 'Llogaria u krijua me sukses! Ju lutemi kyçuni.' } 
          });
        }
      } catch (err) {
        if (err.response?.status === 422) {
          validationErrors.value = err.response.data.errors;
          error.value = 'Ju lutemi korrigjoni gabimet e mëposhtme.';
        } else {
          error.value = err.response?.data?.message || 'Ka ndodhur një gabim gjatë regjistrimit. Provoni përsëri.';
        }
      } finally {
        loading.value = false;
      }
    };

    return {
      form,
      loading,
      error,
      validationErrors,
      handleRegister
    };
  }
}
</script>

<style scoped>
@reference "tailwindcss/theme";
.btn-primary-form {
  width: 100%;
}
.signup-form-container {
  max-width: 800px;
}
</style>

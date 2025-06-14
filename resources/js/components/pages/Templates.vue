<template>
  <main class="main">
    <div class="template-selection-page-container page-container animate-in">
      <h2 class="reveal-on-scroll">Zgjidhni Modelin për CV-në Tuaj</h2>
      <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">
        Çdo model ofron një pamje unike. Klikoni mbi një model për të filluar plotësimin e të dhënave tuaja.
      </p>

      <div class="message-area">
        <p v-if="errorMessage" class="message error" role="alert">{{ errorMessage }}</p>
        <p v-if="infoMessage" class="message info" role="status">{{ infoMessage }}</p>
      </div>

      <div class="template-options-grid">
        <div
          v-for="(template, index) in templates"
          :key="template.id"
          class="template-option-card reveal-on-scroll glassmorphic-card"
          :data-reveal-delay="150 + index * 50"
          @click="selectTemplate(template.id)"
          role="button" 
          tabindex="0"
        >
          <div class="template-thumbnail">
            <!-- Classic Template Thumbnail -->
            <div v-if="template.id === 'classic'">
              <div class="thumb-header">EMRI MBIEMRI</div>
              <div class="thumb-line bg-muted"></div>
              <div class="thumb-line bg-primary w-80"></div>
              <div class="thumb-line bg-muted w-90"></div>
              <div class="thumb-line bg-primary w-70"></div>
              <div class="thumb-line bg-muted w-full"></div>
            </div>

            <!-- Modern Template Thumbnail -->
            <div v-if="template.id === 'modern'">
               <div class="thumb-modern-layout">
                  <div class="thumb-sidebar">
                      <div class="thumb-avatar"></div>
                      <div class="thumb-line bg-primary w-70 mt-sm"></div>
                      <div class="thumb-line bg-muted w-90"></div>
                  </div>
                  <div class="thumb-main-content">
                      <div class="thumb-header-sm">EMRI MBIEMRI</div>
                      <div class="thumb-line bg-muted w-60"></div>
                      <div class="thumb-line bg-primary w-full mt-sm"></div>
                      <div class="thumb-line bg-muted w-80"></div>
                  </div>
              </div>
            </div>

            <!-- Professional Template Thumbnail -->
            <div v-if="template.id === 'professional'">
              <div class="thumb-header-alt">EMRI MBIEMRI</div>
              <div class="thumb-line bg-primary w-50 mx-auto mb-sm"></div>
              <div class="thumb-modern-layout">
                  <div class="thumb-sidebar-alt">
                      <div class="thumb-line bg-muted w-full"></div>
                      <div class="thumb-line bg-muted w-80"></div>
                  </div>
                  <div class="thumb-main-content-alt">
                      <div class="thumb-line bg-primary w-full"></div>
                      <div class="thumb-line bg-muted w-90"></div>
                      <div class="thumb-line bg-muted w-full"></div>
                  </div>
              </div>
            </div>

            <!-- Creative Template Thumbnail (Placeholder) -->
            <div v-if="template.id === 'creative'">
              <div class="thumb-creative-layout bg-gradient-to-br from-purple-200 to-pink-200 p-4 rounded-md">
                <div class="thumb-shape-1 bg-purple-400 w-12 h-12 rounded-full mb-2"></div>
                <div class="thumb-line bg-purple-300 w-full mb-1"></div>
                <div class="thumb-line bg-purple-300 w-3/4"></div>
                <div class="thumb-shape-2 bg-pink-400 w-8 h-8 rounded-full ml-auto mt-4"></div>
              </div>
            </div>

          </div>
          <span class="template-name">{{ template.name }}</span>
          <p class="template-description">{{ template.description }}</p>
        </div>
      </div>

      <div class="page-actions" style="margin-top: var(--space-xl);">
        <router-link v-if="isAuthenticated" to="/my-cvs" class="btn btn-secondary reveal-on-scroll" data-reveal-delay="300">
          <i class="fas fa-arrow-left"></i> Kthehu te CV-të e Mia
        </router-link>
        <p v-else class="form-alternate-action reveal-on-scroll" data-reveal-delay="300">
          Keni një llogari? <router-link to="/login">Kyçu</router-link> për të ruajtur dhe menaxhuar CV-të.
        </p>
      </div>
    </div>
  </main>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';

export default {
  name: 'Templates',
  setup() {
    const router = useRouter();
    const errorMessage = ref('');
    const infoMessage = ref('');

    const isAuthenticated = computed(() => !!localStorage.getItem('auth_token'));

    const templates = ref([
      { id: 'classic', name: 'Modeli Klasik', description: 'Një dizajn tradicional dhe i qartë, ideal për shumicën e profesioneve.' },
      { id: 'modern', name: 'Modeli Modern', description: 'Një pamje bashkëkohore me fokus në dizajn dhe lexueshmëri.' },
      { id: 'professional', name: 'Modeli Profesional', description: 'Elegant dhe i strukturuar, perfekt për role ekzekutive dhe korporative.' },
      { id: 'creative', name: 'Modeli Kreativ', description: 'Një dizajn modern dhe dinamik, ideal për fusha kreative dhe inovative.' }
    ]);

    const selectTemplate = (templateId) => {
      if (!isAuthenticated.value) {
        // In a real SPA, we'd store the intent and redirect after login.
        // For now, we'll mimic the old logic's redirect.
        infoMessage.value = "Ju lutemi kyçuni ose regjistrohuni për të vazhduar me modelin e zgjedhur.";
        router.push({ name: 'login', query: { redirect: `/cv/create?template=${templateId}` }});
      } else {
        router.push({ path: '/cv/create', query: { template: templateId } });
      }
    };

    return { templates, selectTemplate, isAuthenticated, errorMessage, infoMessage };
  }
};
</script>

<style scoped>
@reference "tailwindcss/theme";

.template-option-card {
  cursor: pointer;
}
</style>

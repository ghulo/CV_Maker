<template>
  <main class="main">
    <div class="template-selection-page-container page-container animate-in">
      <h2 class="reveal-on-scroll">Zgjidhni Modelin për CV-në Tuaj</h2>
      <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">
        Çdo model ofron një pamje unike. Klikoni mbi një model për të filluar plotësimin e të
        dhënave tuaja.
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
              <div
                class="thumb-creative-layout bg-gradient-to-br from-purple-200 to-pink-200 p-4 rounded-md"
              >
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

      <div class="page-actions" style="margin-top: var(--space-xl)">
        <router-link
          v-if="isAuthenticated"
          to="/my-cvs"
          class="btn btn-secondary reveal-on-scroll"
          data-reveal-delay="300"
        >
          <i class="fas fa-arrow-left"></i> Kthehu te CV-të e Mia
        </router-link>
        <p v-else class="form-alternate-action reveal-on-scroll" data-reveal-delay="300">
          Keni një llogari? <router-link to="/login">Kyçu</router-link> për të ruajtur dhe menaxhuar
          CV-të.
        </p>
      </div>
    </div>
  </main>
</template>

<script>
  import { ref, computed, onMounted } from 'vue'
  import { useRouter } from 'vue-router'

  export default {
    name: 'Templates',
    setup() {
      const router = useRouter()
      const errorMessage = ref('')
      const infoMessage = ref('')

      const isAuthenticated = computed(() => !!localStorage.getItem('auth_token'))

      const templates = ref([
        {
          id: 'classic',
          name: 'Modeli Klasik',
          description: 'Një dizajn tradicional dhe i qartë, ideal për shumicën e profesioneve.',
        },
        {
          id: 'modern',
          name: 'Modeli Modern',
          description: 'Një pamje bashkëkohore me fokus në dizajn dhe lexueshmëri.',
        },
        {
          id: 'professional',
          name: 'Modeli Profesional',
          description: 'Elegant dhe i strukturuar, perfekt për role ekzekutive dhe korporative.',
        },
        {
          id: 'creative',
          name: 'Modeli Kreativ',
          description: 'Një dizajn modern dhe dinamik, ideal për fusha kreative dhe inovative.',
        },
      ])

      const selectTemplate = (templateId) => {
        if (!isAuthenticated.value) {
          // In a real SPA, we'd store the intent and redirect after login.
          // For now, we'll mimic the old logic's redirect.
          infoMessage.value =
            'Ju lutemi kyçuni ose regjistrohuni për të vazhduar me modelin e zgjedhur.'
          router.push({ name: 'login', query: { redirect: `/cv/create?template=${templateId}` } })
        } else {
          router.push({ path: '/cv/create', query: { template: templateId } })
        }
      }

      return { templates, selectTemplate, isAuthenticated, errorMessage, infoMessage }
    },
  }
</script>

<style scoped>
  @reference "tailwindcss/theme";

  /* Styles specific to Templates.vue */

  .template-options-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: var(--space-lg);
      margin-top: var(--space-lg);
  }
  .template-option-card {
      text-decoration: none;
      color: var(--neutral-text);
      background-color: var(--neutral-light);
      border: 1px solid var(--neutral-border);
      border-radius: var(--radius);
      padding: var(--space-md);
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      box-shadow: var(--shadow-light);
      transition: all 0.4s var(--animation-ease-out);
      position: relative;
      overflow: hidden;
      /* For tilt effect */
      transform-style: preserve-3d;
      transform: perspective(1000px) rotateX(var(--rotate-x, 0deg)) rotateY(var(--rotate-y, 0deg)) scale(var(--scale, 1));
      cursor: pointer; /* Keep existing cursor style */
  }
  .template-option-card:hover {
      transform: translateY(-10px) scale(1.04);
      box-shadow: var(--shadow-hover);
      border-color: var(--primary);
      /* Add glow on hover */
      box-shadow: var(--shadow-hover), 0 0 var(--card-hover-glow-spread) rgba(var(--primary-rgb), 0.3);
  }
  body.dark-theme .template-option-card {
      color: var(--dark-neutral-text);
      background-color: var(--dark-neutral-light);
      border-color: var(--dark-neutral-border);
  }
  body.dark-theme .template-option-card:hover {
      border-color: var(--dark-primary);
      background-color: #16161A;
      box-shadow: 0 4px 10px rgba(0,0,0,0.25), 0 2px 5px rgba(0,0,0,0.15), 0 0 var(--card-hover-glow-spread) rgba(var(--dark-primary-rgb), 0.4);
  }

  .template-thumbnail {
      width: 100%;
      height: 220px;
      background-color: var(--neutral-bg);
      border: 1px solid var(--divider-color);
      border-radius: var(--radius-sm);
      margin-bottom: var(--space-md);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: var(--space-sm);
      overflow: hidden;
      font-size: 0.8em;
      color: var(--muted-text);
  }
  body.dark-theme .template-thumbnail {
      background-color: var(--dark-neutral-container);
      border-color: var(--dark-divider-color);
      color: var(--dark-muted-text);
  }

  .thumb-header, .thumb-header-sm, .thumb-header-alt { font-weight: bold; margin-bottom: var(--space-xs); font-size: 1.1em; color: var(--neutral-text); }
  .thumb-line { height: 6px; border-radius: 3px; margin-bottom: 4px; }
  .thumb-line.bg-muted { background-color: var(--subtle-text); }
  .thumb-line.bg-primary { background-color: var(--dark-primary); opacity: 0.7; }
  .thumb-line.w-50 { width: 50%; } .thumb-line.w-60 { width: 60%; } .thumb-line.w-70 { width: 70%; }
  .thumb-line.w-80 { width: 80%; } .thumb-line.w-90 { width: 90%; } .thumb-line.w-full { width: 100%; }
  .thumb-line.mx-auto { margin-left: auto; margin-right: auto; }
  .thumb-line.mt-sm { margin-top: var(--space-xs); } .thumb-line.mb-sm { margin-bottom: var(--space-xs); }

  body.dark-theme .thumb-header, body.dark-theme .thumb-header-sm, body.dark-theme .thumb-header-alt { color: var(--dark-neutral-text); }
  body.dark-theme .thumb-line.bg-muted { background-color: var(--dark-subtle-text); }
  body.dark-theme .thumb-line.bg-primary { background-color: var(--dark-primary); opacity: 0.6; }


  .thumb-modern-layout { display: flex; width: 100%; height: 100%; gap: var(--space-xs); }
  .thumb-sidebar { flex: 1; background-color: rgba(0,0,0,0.03); padding: var(--space-xs); border-radius: var(--radius-sm); }
  .thumb-main-content { flex: 2; padding: var(--space-xs); }
  .thumb-avatar { width: 30px; height: 30px; border-radius: 50%; background-color: var(--subtle-text); margin: 0 auto var(--space-xs); }
  .thumb-sidebar-alt { flex: 1; padding: var(--space-xs); }
  .thumb-main-content-alt { flex: 2; padding: var(--space-xs); }

  body.dark-theme .thumb-sidebar, body.dark-theme .thumb-sidebar-alt { background-color: rgba(var(--dark-neutral-light), 0.5); }
  body.dark-theme .thumb-avatar { background-color: var(--dark-subtle-text); }

  .template-name {
      font-size: 1.3em;
      font-weight: 600;
      margin-top: var(--space-md);
      color: var(--neutral-text);
  }

  .template-description {
      font-size: 0.95em;
      color: var(--muted-text);
      margin-top: var(--space-xs);
      line-height: 1.5;
  }
</style>

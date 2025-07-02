<template>
  <div id="vue-app" :class="{ 'theme-dark': isDarkTheme, 'theme-light': !isDarkTheme }">
    <!-- Navigation Component -->
    <Navigation @theme-changed="handleThemeChange" />

    <!-- Main Content - Router View -->
    <main class="app-main-content">
      <router-view />
    </main>

    <!-- Footer Component -->
    <AppFooter />

    <!-- Animated Background -->
    <div class="animated-bg">
      <div class="gradient-sphere sphere-1"></div>
      <div class="gradient-sphere sphere-2"></div>
      <div class="gradient-sphere sphere-3"></div>
    </div>

    <!-- Global Confirmation Modal -->
    <ConfirmationModal
      :is-visible="modalState.isVisible"
      :title="modalState.title"
      :message="modalState.message"
      :confirm-button-text="modalState.confirmButtonText"
      :cancel-button-text="modalState.cancelButtonText"
      :confirm-button-class="modalState.confirmButtonClass"
      @confirm="confirm"
      @cancel="cancel"
    />

    <!-- Back-to-Top Button -->
    <button
      @click="scrollToTop"
      :class="['back-to-top', { 'visible': showBackToTop }]"
      aria-label="Scroll to top"
    >
      <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Floating AI Assistant - Always visible -->
    <GeminiChatAssistant :cvData="currentCvData" />
  </div>
</template>

<script>
  import { onMounted, watch, ref, onUnmounted, nextTick, computed } from 'vue'
  import { useRoute } from 'vue-router'
  import { useI18n } from 'vue-i18n'
  import Navigation from './layout/Navigation.vue'
  import AppFooter from './layout/AppFooter.vue'
  import ConfirmationModal from './common/ConfirmationModal.vue'
  import { initializeEffects, reinitializeScrollReveal } from '../script.js'
  import { useConfirmationModal } from '../composables/useConfirmationModal.js'
  import { useLiquidAnimations } from '../liquidAnimations.js'
  import GeminiChatAssistant from './common/GeminiChatAssistant.vue'

  export default {
    name: 'App',
    components: {
      Navigation,
      AppFooter,
      ConfirmationModal,
      GeminiChatAssistant
    },
    setup() {
      const route = useRoute()
      const { t, locale } = useI18n()
      const { modalState, confirm, cancel } = useConfirmationModal()
      const liquidAnimations = useLiquidAnimations()
              const showBackToTop = ref(false)
        const isDarkTheme = ref(false)
        const userCvData = ref({})
        const currentCvData = computed(() => {
          // If we're on a specific CV route, get that CV's data
          if (route.params.id) {
            // This would be the specific CV being viewed/edited
            return userCvData.value
          }
          // Otherwise return the most recent CV for general analysis
          return userCvData.value
        })

      // Initialize i18n with saved language preference
      const initializeLanguage = () => {
        const savedLanguage = localStorage.getItem('preferred-language')
        if (savedLanguage) {
          locale.value = savedLanguage
        }
      }

      const handleScroll = () => {
        showBackToTop.value = window.scrollY > 300
      }

      const scrollToTop = () => {
        window.scrollTo({ top: 0, behavior: 'smooth' })
      }

      const handleThemeChange = (isDark) => {
        isDarkTheme.value = isDark
      }

      const initializeTheme = () => {
        // Check for saved theme preference or default to light
        const savedTheme = localStorage.getItem('theme')
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
        
        if (savedTheme) {
          isDarkTheme.value = savedTheme === 'dark'
        } else {
          isDarkTheme.value = prefersDark
        }
        
        // Apply theme to body
        if (isDarkTheme.value) {
          document.body.classList.add('theme-dark')
          document.body.classList.remove('theme-light')
        } else {
          document.body.classList.add('theme-light')
          document.body.classList.remove('theme-dark')
        }
      }

      const loadUserCvData = async () => {
        try {
          // Get user's most recent CV for AI analysis
          const token = localStorage.getItem('auth_token')
          if (!token) return
          
          const response = await fetch('/api/my-cvs', {
            headers: { Authorization: `Bearer ${token}` }
          })
          
          if (response.ok) {
            const data = await response.json()
            if (data.success && data.cvs && data.cvs.length > 0) {
              // Use the most recent CV for AI analysis
              userCvData.value = data.cvs[0]
            }
          }
        } catch (error) {
          console.log('No CV data available yet')
        }
      }

      onMounted(() => {
        initializeLanguage()
        initializeTheme()
        initializeEffects()
        window.addEventListener('scroll', handleScroll)
        
        // DISABLE AOS FOR DEBUGGING - this was hiding content!
        // if (typeof AOS !== 'undefined') {
        //   AOS.init({
        //     duration: 800,
        //     easing: 'ease-out',
        //     once: true,
        //     offset: 100
        //   })
        // }

        // Load user CV data if available
        loadUserCvData()
      })

      watch(
        () => route.fullPath,
        async () => {
          await nextTick()
          reinitializeScrollReveal()
          liquidAnimations.refresh()
          window.scrollTo({ top: 0, behavior: 'smooth' })

          // DISABLE AOS REFRESH FOR DEBUGGING
          // if (typeof AOS !== 'undefined') {
          //   AOS.refresh()
          // }
        }
      )

      onUnmounted(() => {
        window.removeEventListener('scroll', handleScroll)
      })

      return {
        modalState,
        confirm,
        cancel,
        showBackToTop,
        scrollToTop,
        isDarkTheme,
        handleThemeChange,
        t,
        userCvData,
        currentCvData,
        loadUserCvData
      }
    },
  }
</script>

<style>
/* App-specific styles */
#vue-app {
  min-height: 100vh;
  position: relative;
  overflow-x: hidden;
  background: var(--bg-primary);
  color: var(--text-primary);
  transition: background-color 0.3s ease, color 0.3s ease;
}

.app-main-content {
  min-height: calc(100vh - var(--header-height-compact) - 300px); /* Adjust based on footer height */
  padding-top: var(--header-height-compact);
  position: relative;
  z-index: 1;
}

/* Animated Background */
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

/* Back to Top Button */
.back-to-top {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 50px;
  height: 50px;
  background: var(--gradient-primary);
  color: white;
  border: none;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  opacity: 0;
  visibility: hidden;
  transform: translateY(20px);
  transition: all 0.3s ease;
  box-shadow: 0 4px 14px rgba(91, 33, 182, 0.4);
  z-index: var(--z-sticky);
}

.back-to-top.visible {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.back-to-top:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(91, 33, 182, 0.5);
}

.back-to-top i {
  font-size: 20px;
}

/* Page Transitions */
.page-enter-active,
.page-leave-active {
  transition: all 0.3s ease;
}

.page-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.page-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}

/* Ensure all components respect theme */
.theme-dark {
  --header-bg: rgba(15, 23, 42, 0.95);
  --footer-bg: var(--bg-muted);
}

.theme-light {
  --header-bg: rgba(255, 255, 255, 0.95);
  --footer-bg: var(--bg-muted);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .app-main-content {
    padding-top: calc(var(--header-height-compact) - 10px);
  }
  
  .back-to-top {
    bottom: 20px;
    right: 20px;
    width: 45px;
    height: 45px;
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
</style>

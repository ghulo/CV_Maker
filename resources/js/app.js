import './bootstrap'
import { createApp } from 'vue'
import App from './components/App.vue'
import router from './router'
import VueApexCharts from 'vue3-apexcharts'

/* import font awesome icon component */
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { fas } from '@fortawesome/free-solid-svg-icons'

/* import vue-i18n */
import { createI18n } from 'vue-i18n'

/* import translation files */
import en from './lang/en.json'
import sq from './lang/sq.json'

import { initializeEffects } from './script' // Import initializeEffects
import { initLiquidAnimations } from './liquidAnimations' // Import liquid animations

// All styles are now consolidated in master.css

library.add(fas)

// Get saved language or default to 'en'
const savedLanguage = localStorage.getItem('preferred-language') || 'en'

// Create i18n instance with type definition
const i18n = createI18n({
  legacy: false, // You must set `false`, to use Composition API
  globalInjection: true, // Make translation functions available globally
  locale: savedLanguage,
  fallbackLocale: 'en',
  allowComposition: true, // Enable Composition API in legacy mode
  messages: {
    en,
    sq
  },
  missing: (locale, key) => {
    console.warn(`Missing translation: ${key} for locale: ${locale}`)
    return key
  },
  silentTranslationWarn: true,
  silentFallbackWarn: true,
  warnHtmlInMessage: 'off'
})

// Create and configure Vue app
const app = createApp(App)

// Use plugins
app.use(router)
app.use(VueApexCharts)
app.use(i18n)

// Register global components
app.component('FontAwesomeIcon', FontAwesomeIcon)

// Function to hide loading indicator
const hideLoadingIndicator = () => {
  const loadingIndicator = document.getElementById('loading-indicator')
  if (loadingIndicator) {
    loadingIndicator.style.display = 'none'
  }
}

// Wait until the router is ready before mounting the app
router.isReady().then(() => {
  // Mount the app
  app.mount('#vue-app')
  
  // Initialize effects
  initializeEffects()
  
  // Initialize liquid animations
  initLiquidAnimations({
    threshold: 0.1,
    rootMargin: '50px 0px -50px 0px',
    triggerOnce: true
  })
  
  // Hide loading indicator
  setTimeout(hideLoadingIndicator, 500)
}).catch(error => {
  console.error('Failed to initialize app:', error)
  hideLoadingIndicator()
})

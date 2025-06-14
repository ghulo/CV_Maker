<template>
  <div id="vue-app">
    <!-- Navigation Component -->
    <Navigation />
    
    <!-- Main Content - Router View -->
    <main>
      <router-view />
    </main>

    <!-- Footer Component -->
    <Footer />

    <!-- Background CV Elements -->
    <div class="background-cvs-container" aria-hidden="true">
      <div class="background-cv">
        <div class="header-placeholder"></div>
        <div class="line-placeholder"></div>
        <div class="line-placeholder w-80"></div>
        <div class="block-placeholder"></div>
        <div class="line-placeholder w-90"></div>
        <div class="line-placeholder w-70"></div>
      </div>
      <div class="background-cv">
        <div class="header-placeholder"></div>
        <div class="line-placeholder"></div>
        <div class="line-placeholder w-80"></div>
        <div class="block-placeholder"></div>
        <div class="line-placeholder w-90"></div>
        <div class="line-placeholder w-70"></div>
      </div>
      <div class="background-cv">
        <div class="header-placeholder"></div>
        <div class="line-placeholder"></div>
        <div class="line-placeholder w-80"></div>
        <div class="block-placeholder"></div>
        <div class="line-placeholder w-90"></div>
        <div class="line-placeholder w-70"></div>
      </div>
       <div class="background-cv">
        <div class="header-placeholder"></div>
        <div class="line-placeholder"></div>
        <div class="line-placeholder w-80"></div>
        <div class="block-placeholder"></div>
        <div class="line-placeholder w-90"></div>
        <div class="line-placeholder w-70"></div>
      </div>
    </div>
  </div>
</template>

<script>
import { onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import Navigation from './layout/Navigation.vue'
import Footer from './layout/Footer.vue'
import { initializeEffects, reinitializeScrollReveal } from '../script.js'

export default {
  name: 'App',
  components: {
    Navigation,
    Footer
  },
  setup() {
    const route = useRoute()

    onMounted(() => {
      // Initialize all effects from the original script.js
      initializeEffects()
    })

    watch(() => route.path, () => {
      reinitializeScrollReveal()
    })
  }
}
</script>

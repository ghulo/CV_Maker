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
    
    <!-- Global Confirmation Modal -->
    <ConfirmationModal
      :isVisible="modalState.isVisible"
      :title="modalState.title"
      :message="modalState.message"
      :confirmButtonText="modalState.confirmButtonText"
      :cancelButtonText="modalState.cancelButtonText"
      :confirmButtonClass="modalState.confirmButtonClass"
      @confirm="confirm"
      @cancel="cancel"
    />
  </div>
</template>

<script>
import { onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import Navigation from './layout/Navigation.vue'
import Footer from './layout/Footer.vue'
import ConfirmationModal from './common/ConfirmationModal.vue'
import { initializeEffects, reinitializeScrollReveal } from '../script.js'
import { useConfirmationModal } from '../composables/useConfirmationModal.js'

export default {
  name: 'App',
  components: {
    Navigation,
    Footer,
    ConfirmationModal
  },
  setup() {
    const route = useRoute()
    const { modalState, confirm, cancel } = useConfirmationModal()

    onMounted(() => {
      // Initialize all effects from the original script.js
      initializeEffects()
    })

    watch(() => route.path, () => {
      reinitializeScrollReveal()
    })

    return {
      modalState,
      confirm,
      cancel,
    }
  }
}
</script>

<style>
/* Removed global modal styles from here */
</style>

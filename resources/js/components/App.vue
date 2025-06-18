<template>
  <div id="vue-app">
    <!-- Navigation Component -->
    <Navigation />

    <!-- Main Content - Router View -->
    <main>
      <router-view />
    </main>

    <!-- Footer Component -->
    <AppFooter />

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
      :class="['back-to-top-btn', { 'is-visible': showBackToTop }]"
      aria-label="Scroll to top"
    >
      <i class="fas fa-arrow-up"></i>
    </button>
  </div>
</template>

<script>
  import { onMounted, watch, ref, onUnmounted } from 'vue'
  import { useRoute } from 'vue-router'
  import Navigation from './layout/Navigation.vue'
  import AppFooter from './layout/AppFooter.vue'
  import ConfirmationModal from './common/ConfirmationModal.vue'
  import { initializeEffects, reinitializeScrollReveal } from '../script.js'
  import { useConfirmationModal } from '../composables/useConfirmationModal.js'

  export default {
    name: 'App',
    components: {
      Navigation,
      AppFooter,
      ConfirmationModal,
    },
    setup() {
      const route = useRoute()
      const { modalState, confirm, cancel } = useConfirmationModal()
      const showBackToTop = ref(false)

      const handleScroll = () => {
        showBackToTop.value = window.scrollY > 200
      }

      const scrollToTop = () => {
        window.scrollTo({ top: 0, behavior: 'smooth' })
      }

      onMounted(() => {
        initializeEffects()
        window.addEventListener('scroll', handleScroll)
      })

      watch(
        () => route.path,
        () => {
          reinitializeScrollReveal()
          window.scrollTo({ top: 0, behavior: 'smooth' })
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
      }
    },
  }
</script>

<style>
  /* Removed global modal styles from here */
  /* Back-to-Top Button Styles (added here for simplicity, consider moving to global.css if desired) */
  .back-to-top-btn {
    position: fixed;
    bottom: var(--space-xl);
    right: var(--space-xl);
    background-color: var(--primary);
    color: white;
    padding: var(--space-md);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5em;
    box-shadow: var(--shadow-interactive);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s var(--animation-ease-out);
    z-index: 900;
    cursor: pointer;
  }

  .back-to-top-btn.is-visible {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
  }

  .back-to-top-btn:hover {
    transform: translateY(-5px); /* Subtle lift on hover */
    box-shadow: var(--shadow-hover);
  }

  /* Dark theme specific styles for back-to-top button */
  body.dark-theme .back-to-top-btn {
    background-color: var(--dark-primary);
    box-shadow: var(--shadow-interactive-dark);
  }

  body.dark-theme .back-to-top-btn:hover {
    background-color: var(--dark-primary-hover);
  }
</style>

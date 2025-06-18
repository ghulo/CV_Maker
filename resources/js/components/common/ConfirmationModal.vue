<template>
  <div v-show="isVisible" :class="['modal-overlay', { 'is-visible': isVisible }]">
    <div class="modal-content">
      <h3 class="modal-title">{{ title }}</h3>
      <p class="modal-message">{{ message }}</p>
      <div class="modal-actions">
        <button @click="cancel" class="btn btn-secondary">{{ cancelButtonText }}</button>
        <button @click="confirm" :class="['btn', confirmButtonClass]">
          {{ confirmButtonText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { defineProps, defineEmits } from 'vue'

  const props = defineProps({
    isVisible: {
      type: Boolean,
      default: false,
    },
    title: {
      type: String,
      default: 'Konfirmo Veprimin',
    },
    message: {
      type: String,
      default: 'Jeni të sigurt që dëshironi të vazhdoni me këtë veprim?',
    },
    confirmButtonText: {
      type: String,
      default: 'Konfirmo',
    },
    cancelButtonText: {
      type: String,
      default: 'Anulo',
    },
    confirmButtonClass: {
      type: String,
      default: 'btn-primary', // Tailwind classes like 'btn-danger' for delete
    },
  })

  const emit = defineEmits(['confirm', 'cancel'])

  const confirm = () => {
    emit('confirm')
  }

  const cancel = () => {
    emit('cancel')
  }
</script>

<style scoped>
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }
  .modal-content {
    background: var(--neutral-light);
    padding: var(--space-lg);
    border-radius: var(--radius);
    box-shadow: var(--shadow-hover);
    width: 90%;
    max-width: 400px;
    transform: translateY(-20px); /* Initial position */
    transition: all 0.3s ease-out;
    opacity: 0; /* Start hidden */
  }
  .modal-overlay.is-visible .modal-content {
    transform: translateY(0px); /* Final position */
    opacity: 1; /* Show content */
  }
  body.dark-theme .modal-content {
    background: var(--dark-neutral-container);
  }

  .modal-title {
    font-size: 1.5em; /* text-2xl */
    font-weight: 600; /* font-semibold */
    margin-bottom: 1rem; /* mb-4 */
    color: var(--neutral-text);
  }
  body.dark-theme .modal-title {
    color: var(--dark-neutral-text);
  }

  .modal-message {
    font-size: 1em; /* text-base */
    color: var(--muted-text);
    margin-bottom: 1.5rem; /* mb-6 */
  }
  body.dark-theme .modal-message {
    color: var(--dark-muted-text);
  }

  .modal-actions {
    margin-top: 1.5rem; /* mt-6 */
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem; /* gap-3 */
  }

  /* Ensure buttons adopt existing styles */
  .btn {
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius);
    border: 1px solid transparent;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  .btn-primary {
    background-color: var(--primary);
    color: white;
  }
  .btn-primary:hover {
    background-color: var(--primary-darker);
  }
  .btn-secondary {
    background-color: var(--neutral-container);
    color: var(--neutral-text);
    border-color: var(--neutral-border);
  }
  .btn-secondary:hover {
    border-color: var(--primary);
    color: var(--primary);
  }
  .btn-danger {
    background-color: var(--danger);
    color: white;
  }
  .btn-danger:hover {
    background-color: var(--danger-darker);
  }
</style>

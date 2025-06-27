<template>
  <div v-show="isVisible" class="modal-overlay">
    <div class="modal-content card reveal-on-scroll reveal-scale">
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
      default: 'btn-primary', // btn-danger for delete
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
    background-color: rgba(0, 0, 0, 0.6); /* Slightly lighter overlay */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: var(--z-modal);
    opacity: 0;
    visibility: hidden;
    transition: var(--transition-opacity);
  }

  .modal-overlay[style*="display: block"] {
    opacity: 1;
    visibility: visible;
  }

  .modal-content {
    padding: var(--space-xl);
    max-width: 500px;
    width: 90%;
    text-align: center;
  }

  .modal-title {
    font-family: var(--font-heading);
    font-size: var(--font-size-2xl);
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: var(--space-md);
  }

  .modal-message {
    font-size: var(--font-size-base);
    color: var(--text-secondary);
    line-height: var(--line-height-normal);
    margin-bottom: var(--space-lg);
  }

  .modal-actions {
    display: flex;
    justify-content: center;
    gap: var(--space-md);
    margin-top: var(--space-xl);
  }

  /* Responsive Adjustments */
  @media (max-width: 768px) {
    .modal-content {
      padding: var(--space-lg);
    }

    .modal-title {
      font-size: var(--font-size-xl);
    }

    .modal-message {
      font-size: var(--font-size-sm);
    }

    .modal-actions {
      flex-direction: column;
      gap: var(--space-sm);
    }

    .modal-actions .btn {
      width: 100%;
    }
  }

  /* Dark Mode Overrides */
  body.dark-theme .modal-title {
    color: var(--text-primary);
  }

  body.dark-theme .modal-message {
    color: var(--text-secondary);
  }
</style>

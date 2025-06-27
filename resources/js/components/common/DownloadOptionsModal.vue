<template>
  <div v-if="isVisible" class="modal-overlay">
    <div class="modal-content card reveal-on-scroll reveal-scale">
      <h3 class="modal-title">Shkarko CV</h3>
      <p class="modal-message">Zgjidhni opsionet e shkarkimit për CV-në tuaj.</p>

      <div class="space-y-lg">
        <div class="form-group">
          <label for="pdf-style" class="form-label">Stili i PDF-së</label>
          <select id="pdf-style" v-model="selectedStyle" class="form-select">
            <option value="default">Parazgjedhur</option>
            <option value="professional">Profesional</option>
            <option value="creative">Kreativ</option>
            <option value="modern">Modern</option>
          </select>
        </div>
        <div class="form-group">
          <label for="pdf-quality" class="form-label">Cilësia</label>
          <select id="pdf-quality" v-model="selectedQuality" class="form-select">
            <option value="high">Lartë</option>
            <option value="medium">Mesatare</option>
            <option value="low">Ulët</option>
          </select>
        </div>
      </div>

      <div class="modal-actions">
        <button @click="emitCancel" class="btn btn-secondary">Anulo</button>
        <button @click="emitConfirm" class="btn btn-primary">Shkarko</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits } from 'vue';

const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false,
  },
  cvId: {
    type: String,
    required: true,
  },
  cvTitle: {
    type: String,
    default: 'Untitled CV',
  },
});

const emits = defineEmits(['confirm', 'cancel']);

const selectedStyle = ref('default');
const selectedQuality = ref('high');

// Reset selections when modal becomes visible
watch(() => props.isVisible, (newVal) => {
  if (newVal) {
    selectedStyle.value = 'default';
    selectedQuality.value = 'high';
  }
});

const emitConfirm = () => {
  emits('confirm', {
    id: props.cvId,
    title: props.cvTitle,
    style: selectedStyle.value,
    quality: selectedQuality.value,
  });
};

const emitCancel = () => {
  emits('cancel');
};
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

.space-y-lg > *:not(:last-child) {
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
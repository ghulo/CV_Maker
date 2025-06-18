<template>
  <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1002] transition-opacity duration-300">
    <div class="bg-neutral-light dark:bg-dark-neutral-container p-6 rounded-lg shadow-xl w-full max-w-md animate-fade-in-up">
      <h3 class="text-xl font-semibold text-neutral-text dark:text-dark-neutral-text mb-4">Shkarko CV</h3>
      <p class="text-muted-text dark:text-dark-muted-text mb-6">Zgjidhni opsionet e shkarkimit për CV-në tuaj.</p>

      <div class="space-y-4">
        <div>
          <label for="pdf-style" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text mb-1">Stili i PDF-së</label>
          <select id="pdf-style" v-model="selectedStyle"
            class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-sm">
            <option value="default">Parazgjedhur</option>
            <option value="professional">Profesional</option>
          </select>
        </div>
        <div>
          <label for="pdf-quality" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text mb-1">Cilësia</label>
          <select id="pdf-quality" v-model="selectedQuality"
            class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-sm">
            <option value="high">Lartë</option>
            <option value="medium">Mesatare</option>
            <option value="low">Ulët</option>
          </select>
        </div>
      </div>

      <div class="mt-6 flex justify-end gap-3">
        <button @click="emitCancel" class="btn-secondary px-4 py-2">Anulo</button>
        <button @click="emitConfirm" class="btn-primary px-4 py-2">Shkarko</button>
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
/* Scoped styles for the modal can be added here if needed,
   but most styling should come from Tailwind and global.css */
</style> 
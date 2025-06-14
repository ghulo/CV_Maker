<template>
  <div class="cv-preview-card border border-neutral-border dark:border-dark-neutral-border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200 cursor-pointer relative group">
    <div class="relative w-full h-64 bg-neutral-light dark:bg-dark-neutral-container flex items-center justify-center">
      <div v-if="!cv" class="text-muted-text dark:text-dark-muted-text">
        No CV Data
      </div>
      <component :is="currentTemplateComponent" :cv="cv" v-else></component>
    </div>
    <div class="p-3 bg-neutral-bg dark:bg-dark-neutral-bg border-t border-neutral-border dark:border-dark-neutral-border">
      <h3 class="text-base font-semibold text-neutral-text dark:text-dark-neutral-text truncate">{{ cv.title || 'Untitled CV' }}</h3>
      <p class="text-sm text-muted-text dark:text-dark-muted-text">Template: {{ templateName }}</p>
    </div>
    <div class="cv-actions-overlay absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
      <button @click.stop="$emit('preview', cv.id)" class="btn btn-primary btn-sm mx-1"><i class="fas fa-eye"></i> Preview</button>
      <button @click.stop="$emit('edit', cv.id)" class="btn btn-secondary btn-sm mx-1"><i class="fas fa-edit"></i> Edit</button>
      <button @click.stop="$emit('download', cv.id, cv.title)" class="btn btn-secondary btn-sm mx-1"><i class="fas fa-download"></i> Download</button>
      <button @click.stop="$emit('delete', cv.id)" class="btn btn-danger btn-sm mx-1"><i class="fas fa-trash"></i> Delete</button>
    </div>
  </div>
</template>

<script setup>
import { defineProps, computed, defineEmits } from 'vue';
import ClassicTemplateMini from '@/components/cv_templates/ClassicTemplateMini.vue';
import ModernTemplateMini from '@/components/cv_templates/ModernTemplateMini.vue';
import ProfessionalTemplateMini from '@/components/cv_templates/ProfessionalTemplateMini.vue';
import CreativeTemplateMini from '@/components/cv_templates/CreativeTemplateMini.vue';
// Import other mini templates here as they are created

const props = defineProps({
  cv: {
    type: Object,
    required: true
  }
});

const emits = defineEmits(['preview', 'edit', 'download', 'delete']);

const currentTemplateComponent = computed(() => {
  if (!props.cv) return null;
  switch (props.cv.selected_template) {
    case 'classic':
      return ClassicTemplateMini;
    case 'modern':
      return ModernTemplateMini;
    case 'professional':
      return ProfessionalTemplateMini;
    case 'creative':
      return CreativeTemplateMini;
    // Add cases for other mini templates here
    default:
      return ClassicTemplateMini; // Fallback
  }
});

const templateName = computed(() => {
  switch (props.cv.selected_template) {
    case 'classic':
      return 'Classic';
    case 'modern':
      return 'Modern';
    case 'professional':
      return 'Professional';
    case 'creative':
      return 'Creative';
    default:
      return 'Unknown';
  }
});
</script>

<style scoped>
.cv-preview-card {
  position: relative;
  overflow: hidden;
}

.cv-actions-overlay {
  background-color: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.cv-preview-card:hover .cv-actions-overlay {
  opacity: 1;
}

.btn-sm {
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
}
</style> 
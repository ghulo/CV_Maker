<template>
  <div class="card cv-preview-card card-hover">
    <div class="cv-render-area">
      <div v-if="!cv || !cv.id" class="empty-cv-data">
        <i class="fas fa-file-alt empty-cv-icon"></i>
        <p>Nuk ka të dhëna për CV</p>
      </div>
      <component :is="currentTemplateComponent" :cv="cv" v-else></component>
    </div>
    <div class="card-body cv-card-footer">
      <h3 class="card-title">{{ cv.title || 'Untitled CV' }}</h3>
      <p class="card-subtitle">Modeli: {{ templateName }}</p>
    </div>
    <div class="cv-actions-overlay" role="group" aria-label="CV Actions">
      <button class="btn btn-icon-with-text" @click.stop="$emit('preview', cv.id)" aria-label="Preview CV">
        <i class="fas fa-eye"></i> Parapamje
      </button>
      <button class="btn btn-icon-with-text" @click.stop="$emit('edit', cv.id)" aria-label="Edit CV">
        <i class="fas fa-edit"></i> Redakto
      </button>
      <button class="btn btn-icon-with-text" @click.stop="$emit('download', cv.id, cv.title)" aria-label="Download CV">
        <i class="fas fa-download"></i> Shkarko
      </button>
      <button class="btn btn-icon-with-text" @click.stop="$emit('delete', cv.id)" aria-label="Delete CV">
        <i class="fas fa-trash"></i> Fshij
      </button>
    </div>
  </div>
</template>

<script setup>
  import { defineProps, computed, defineEmits } from 'vue'
  import ClassicTemplateMini from '@/components/cv_templates/ClassicTemplateMini.vue'
  import ModernTemplateMini from '@/components/cv_templates/ModernTemplateMini.vue'
  import ProfessionalTemplateMini from '@/components/cv_templates/ProfessionalTemplateMini.vue'
  import CreativeTemplateMini from '@/components/cv_templates/CreativeTemplateMini.vue'
  // Import other mini templates here as they are created

  const props = defineProps({
    cv: {
      type: Object,
      required: true,
    },
  })

  const emits = defineEmits(['preview', 'edit', 'download', 'delete'])

  const currentTemplateComponent = computed(() => {
    if (!props.cv || !props.cv.selectedTemplate) return ClassicTemplateMini
    switch (props.cv.selectedTemplate) {
      case 'classic':
        return ClassicTemplateMini
      case 'modern':
        return ModernTemplateMini
      case 'professional':
        return ProfessionalTemplateMini
      case 'creative':
        return CreativeTemplateMini
      // Add cases for other mini templates here
      default:
        return ClassicTemplateMini // Fallback
    }
  })

  const templateName = computed(() => {
    if (!props.cv || !props.cv.selectedTemplate) return 'Klasik'
    switch (props.cv.selectedTemplate) {
      case 'classic':
        return 'Klasik'
      case 'modern':
        return 'Modern'
      case 'professional':
        return 'Profesional'
      case 'creative':
        return 'Kreativ'
      default:
        return 'E Panjohur'
    }
  })
</script>

<style scoped>
  .cv-preview-card {
    position: relative;
    overflow: hidden;
    border: 1.5px solid #2D9CDB;
    border-radius: var(--radius-xl);
    box-shadow: 0 8px 32px rgba(45, 156, 219, 0.10), 0 1.5px 8px #F2C94C22;
    background: rgba(24, 28, 37, 0.85);
    transition: var(--transition-all);
    backdrop-filter: blur(8px);
  }
  .cv-preview-card:hover {
    box-shadow: 0 16px 48px rgba(45, 156, 219, 0.18), 0 2px 16px #F2C94C33;
    border-color: #F2C94C;
    background: rgba(24, 28, 37, 0.95);
  }
  .cv-render-area {
    width: 100%;
    aspect-ratio: 1 / 1.414;
    overflow: hidden;
    background-color: white;
    border-bottom: 1.5px solid #2D9CDB;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    z-index: 1;
    box-shadow: 0 2px 8px rgba(45, 156, 219, 0.10);
  }
  .empty-cv-data {
    text-align: center;
    color: #B0B8C1;
    font-size: var(--font-size-base);
  }
  .empty-cv-icon {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--space-sm);
    color: #B0B8C1;
  }
  .cv-card-footer {
    padding: var(--space-md);
    text-align: left;
    background: rgba(24, 28, 37, 0.85);
    border-top: none;
  }
  .cv-card-footer .card-title {
    font-size: var(--font-size-lg);
    font-weight: 700;
    color: #F2C94C;
    margin-bottom: var(--space-xs);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-family: var(--font-heading);
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }
  .cv-card-footer .card-subtitle {
    font-size: var(--font-size-sm);
    color: #B0B8C1;
  }
  .cv-actions-overlay {
    position: absolute;
    inset: 0;
    background: rgba(16, 19, 26, 0.85);
    backdrop-filter: blur(8px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: var(--space-sm);
    opacity: 0;
    visibility: hidden;
    transition: var(--transition-opacity);
    z-index: 2;
    box-shadow: 0 4px 24px #2D9CDB33;
  }
  .cv-preview-card:hover .cv-actions-overlay,
  .cv-preview-card:focus-within .cv-actions-overlay {
    opacity: 1;
    visibility: visible;
  }
  .btn,
  .btn:hover,
  .btn:active,
  .btn:focus {
    text-decoration: none !important;
    border-bottom: none !important;
  }
  .btn-icon-with-text {
    font-weight: 700;
    font-family: var(--font-heading);
    border-radius: var(--radius);
    box-shadow: 0 2px 8px rgba(45, 156, 219, 0.10);
    transition: var(--transition-all);
  }
  .btn-icon-with-text:hover {
    box-shadow: 0 8px 32px rgba(242, 201, 76, 0.18);
    color: #2D9CDB;
  }
  @media (max-width: 768px) {
    .cv-actions-overlay {
      flex-direction: row;
      flex-wrap: wrap;
      padding: var(--space-md);
    }
    .btn-icon-with-text {
      font-size: var(--font-size-sm);
      padding: var(--space-xs) var(--space-sm);
    }
  }
</style>
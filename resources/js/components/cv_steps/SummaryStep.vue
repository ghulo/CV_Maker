<template>
  <div class="form-step summary-step">
    <div class="step-intro">
      <h2>{{ t('cv.summary') }}</h2>
      <p>{{ t('professional_summary') }}</p>
    </div>

    <div class="form-section">
      <div class="form-group">
        <label>{{ t('cv.summary') }}</label>
        <textarea 
          v-model="cv.summary" 
          :placeholder="t('summary_placeholder')"
          rows="6"
          @input="$emit('input-change')"
          class="large-textarea"
        ></textarea>
        <div class="character-count">
          {{ cv.summary?.length || 0 }}/300 characters
          <span class="status" :class="getSummaryStatus()">
            {{ getSummaryStatusText() }}
          </span>
        </div>
      </div>

      <!-- AI Summary Templates -->
      <div class="ai-templates">
        <h4>🤖 AI-Generated Templates</h4>
        <p>Click any template to use it as a starting point:</p>
        <div class="template-options">
          <div 
            v-for="template in aiSummaryTemplates" 
            :key="template.id"
            @click="useSummaryTemplate(template)"
            class="summary-template"
          >
            <div class="template-content">{{ template.content }}</div>
          </div>
        </div>
      </div>

      <!-- AI Generate Button -->
      <div class="ai-actions">
        <button 
          @click="$emit('generate-ai-summary')" 
          class="btn btn-secondary ai-generate-btn"
          :disabled="generating"
        >
          <i class="fas" :class="generating ? 'fa-spinner fa-spin' : 'fa-brain'"></i>
          <span>{{ generating ? t('ai_generating') : t('ai_generate') }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// Props
const props = defineProps({
  cv: {
    type: Object,
    required: true
  },
  generating: {
    type: Boolean,
    default: false
  }
})

// Emits
defineEmits(['input-change', 'generate-ai-summary'])

// AI Summary Templates
const aiSummaryTemplates = [
  {
    id: 1,
    content: "Results-driven professional with [X years] of experience in [industry/field]. Proven track record of delivering exceptional results through strategic thinking and collaborative leadership."
  },
  {
    id: 2,
    content: "Dedicated [profession] with expertise in [key skills]. Passionate about driving innovation and achieving measurable business outcomes through data-driven solutions."
  },
  {
    id: 3,
    content: "Experienced [job title] with strong background in [field]. Skilled in [relevant technologies/methods] with a commitment to continuous learning and professional growth."
  }
]

// Summary status helpers
const getSummaryStatus = () => {
  const length = props.cv.summary?.length || 0
  if (length === 0) return 'empty'
  if (length < 50) return 'short'
  if (length > 300) return 'long'
  return 'good'
}

const getSummaryStatusText = () => {
  const status = getSummaryStatus()
  switch (status) {
    case 'empty': return 'Add your professional summary'
    case 'short': return 'Add more details'
    case 'long': return 'Too long, consider shortening'
    case 'good': return 'Perfect length'
    default: return ''
  }
}

const useSummaryTemplate = (template) => {
  props.cv.summary = template.content
  // Emit input change to trigger reactivity
  const event = new Event('input')
  document.dispatchEvent(event)
}
</script>

<style scoped>
.ai-templates {
  margin-top: 2rem;
  padding: 1.5rem;
  background: var(--bg-secondary);
  border-radius: var(--radius-lg);
  border: 1px solid var(--border-light);
}

.ai-templates h4 {
  margin: 0 0 0.5rem 0;
  color: var(--text-primary);
  font-size: var(--font-size-lg);
  font-weight: 600;
}

.ai-templates p {
  margin: 0 0 1rem 0;
  color: var(--text-secondary);
  font-size: var(--font-size-sm);
}

.template-options {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.summary-template {
  padding: 1rem;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  cursor: pointer;
  transition: var(--transition-all);
}

.summary-template:hover {
  border-color: var(--primary);
  background: var(--bg-elevated);
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
}

.template-content {
  font-size: var(--font-size-sm);
  line-height: var(--line-height-relaxed);
  color: var(--text-secondary);
}

.character-count {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 0.5rem;
  font-size: var(--font-size-xs);
  color: var(--text-muted);
}

.character-count .status {
  font-weight: 500;
}

.character-count .status.empty {
  color: var(--text-muted);
}

.character-count .status.short {
  color: var(--warning);
}

.character-count .status.long {
  color: var(--danger);
}

.character-count .status.good {
  color: var(--success);
}

.ai-actions {
  margin-top: 1.5rem;
  display: flex;
  justify-content: center;
}

.ai-generate-btn {
  min-width: 180px;
}

.large-textarea {
  min-height: 150px;
  resize: vertical;
}
</style> 
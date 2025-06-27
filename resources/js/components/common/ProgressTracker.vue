<template>
  <div class="progress-tracker" :class="{ 'vertical': orientation === 'vertical' }">
    <div class="progress-container">
      <!-- Progress Bar Background -->
      <div class="progress-background">
        <div 
          class="progress-fill"
          :style="{ width: orientation === 'horizontal' ? `${overallProgress}%` : '100%', height: orientation === 'vertical' ? `${overallProgress}%` : '100%' }"
        ></div>
      </div>
      
      <!-- Progress Steps -->
      <div class="steps-container">
        <div
          v-for="(step, index) in steps"
          :key="index"
          class="step"
          :class="getStepClasses(index)"
          @click="$emit('stepClick', index)"
        >
          <!-- Step Circle -->
          <div class="step-circle">
            <div class="step-inner">
              <!-- Completed Icon -->
              <svg v-if="index < currentStep" class="step-icon completed" viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
              </svg>
              
              <!-- Active/Loading Icon -->
              <div v-else-if="index === currentStep" class="step-active">
                <div v-if="loading" class="step-spinner"></div>
                <span v-else class="step-number">{{ index + 1 }}</span>
              </div>
              
              <!-- Pending Number -->
              <span v-else class="step-number">{{ index + 1 }}</span>
            </div>
            
            <!-- Completion Ring -->
            <svg class="completion-ring" viewBox="0 0 42 42">
              <circle
                cx="21" cy="21" r="15.5"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                :stroke-dasharray="getCircumference()"
                :stroke-dashoffset="getStrokeOffset(index)"
                class="progress-circle"
              />
            </svg>
          </div>
          
          <!-- Step Content -->
          <div class="step-content">
            <div class="step-title">{{ step.title }}</div>
            <div class="step-description">{{ step.description }}</div>
            
            <!-- Progress Details for Current Step -->
            <div v-if="index === currentStep && showDetails" class="step-details">
              <div class="detail-item">
                <span class="detail-label">{{ $t('progress.completion') }}:</span>
                <span class="detail-value">{{ getStepProgress(index) }}%</span>
              </div>
              
              <div v-if="step.timeEstimate" class="detail-item">
                <span class="detail-label">{{ $t('progress.timeLeft') }}:</span>
                <span class="detail-value">{{ step.timeEstimate }}min</span>
              </div>
              
              <div v-if="step.fields && step.fields.length" class="detail-item">
                <span class="detail-label">{{ $t('progress.fields') }}:</span>
                <div class="field-indicators">
                  <div
                    v-for="field in step.fields"
                    :key="field.key"
                    class="field-indicator"
                    :class="{ 'completed': isFieldCompleted(field) }"
                    :title="field.label"
                  ></div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Step Status Badge -->
          <div v-if="showStatus" class="step-status">
            <div v-if="index < currentStep" class="status-badge completed">
              <svg viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
              </svg>
            </div>
            <div v-else-if="index === currentStep" class="status-badge active">
              <div class="pulse-dot"></div>
            </div>
            <div v-else class="status-badge pending">{{ index + 1 }}</div>
          </div>
        </div>
      </div>
      
      <!-- Overall Progress Info -->
      <div v-if="showOverallProgress" class="progress-info">
        <div class="progress-stats">
          <div class="stat">
            <span class="stat-value">{{ currentStep + 1 }}</span>
            <span class="stat-label">/{{ steps.length }}</span>
          </div>
          <div class="stat">
            <span class="stat-value">{{ Math.round(overallProgress) }}%</span>
            <span class="stat-label">{{ $t('progress.complete') }}</span>
          </div>
          <div v-if="estimatedTimeLeft" class="stat">
            <span class="stat-value">{{ estimatedTimeLeft }}</span>
            <span class="stat-label">{{ $t('progress.remaining') }}</span>
          </div>
        </div>
        
        <!-- Motivational Messages -->
        <div v-if="motivationalMessage" class="motivation">
          <div class="motivation-icon">🎯</div>
          <div class="motivation-text">{{ motivationalMessage }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  steps: {
    type: Array,
    required: true,
    default: () => []
  },
  currentStep: {
    type: Number,
    default: 0
  },
  stepProgress: {
    type: Array,
    default: () => []
  },
  orientation: {
    type: String,
    default: 'horizontal',
    validator: (value) => ['horizontal', 'vertical'].includes(value)
  },
  showDetails: {
    type: Boolean,
    default: false
  },
  showStatus: {
    type: Boolean,
    default: true
  },
  showOverallProgress: {
    type: Boolean,
    default: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  theme: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'success', 'warning', 'info'].includes(value)
  },
  animated: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['stepClick', 'progressUpdate'])

// Computed Properties
const overallProgress = computed(() => {
  if (props.steps.length === 0) return 0
  
  let totalProgress = 0
  props.steps.forEach((step, index) => {
    if (index < props.currentStep) {
      totalProgress += 100
    } else if (index === props.currentStep) {
      totalProgress += getStepProgress(index)
    }
  })
  
  return totalProgress / props.steps.length
})

const estimatedTimeLeft = computed(() => {
  let totalTime = 0
  
  for (let i = props.currentStep; i < props.steps.length; i++) {
    const step = props.steps[i]
    if (step.timeEstimate) {
      if (i === props.currentStep) {
        // Current step: calculate remaining time based on progress
        const progress = getStepProgress(i) / 100
        totalTime += step.timeEstimate * (1 - progress)
      } else {
        totalTime += step.timeEstimate
      }
    }
  }
  
  if (totalTime < 1) return null
  if (totalTime < 60) return `${Math.round(totalTime)}min`
  
  const hours = Math.floor(totalTime / 60)
  const minutes = Math.round(totalTime % 60)
  return `${hours}h ${minutes}min`
})

const motivationalMessage = computed(() => {
  const progress = overallProgress.value
  
  if (progress >= 90) return t('progress.messages.almostDone')
  if (progress >= 75) return t('progress.messages.excellentProgress')
  if (progress >= 50) return t('progress.messages.halfway')
  if (progress >= 25) return t('progress.messages.goodStart')
  if (progress >= 10) return t('progress.messages.keepGoing')
  
  return t('progress.messages.getStarted')
})

// Methods
const getStepClasses = (index) => {
  return {
    'completed': index < props.currentStep,
    'active': index === props.currentStep,
    'pending': index > props.currentStep,
    'clickable': canClickStep(index),
    'loading': index === props.currentStep && props.loading,
    [`theme-${props.theme}`]: true
  }
}

const canClickStep = (index) => {
  // Allow clicking on completed steps and current step
  return index <= props.currentStep
}

const getStepProgress = (index) => {
  if (props.stepProgress && props.stepProgress[index] !== undefined) {
    return props.stepProgress[index]
  }
  
  // Calculate progress based on completed fields if available
  const step = props.steps[index]
  if (step && step.fields) {
    const completedFields = step.fields.filter(field => isFieldCompleted(field))
    return Math.round((completedFields.length / step.fields.length) * 100)
  }
  
  return index < props.currentStep ? 100 : 0
}

const isFieldCompleted = (field) => {
  // This would be passed down from parent component
  return field.completed || false
}

const getCircumference = () => {
  const radius = 15.5
  return 2 * Math.PI * radius
}

const getStrokeOffset = (index) => {
  const circumference = getCircumference()
  const progress = getStepProgress(index)
  return circumference - (progress / 100) * circumference
}

// Watchers
watch(() => overallProgress.value, (newProgress) => {
  emit('progressUpdate', newProgress)
})
</script>

<style scoped>
.progress-tracker {
  --primary-color: #3b82f6;
  --success-color: #10b981;
  --warning-color: #f59e0b;
  --info-color: #06b6d4;
  --completed-color: var(--primary-color);
  --active-color: var(--primary-color);
  --pending-color: #9ca3af;
  --background-color: #f1f5f9;
  --text-primary: #1e293b;
  --text-secondary: #64748b;
  --border-radius: 8px;
}

.progress-container {
  position: relative;
  padding: 1rem;
}

/* Horizontal Layout (Default) */
.progress-background {
  position: absolute;
  top: 2rem;
  left: 1rem;
  right: 1rem;
  height: 4px;
  background: var(--background-color);
  border-radius: 2px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--primary-color) 0%, var(--success-color) 100%);
  border-radius: 2px;
  transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
}

.progress-fill::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
  animation: shimmer 2s infinite;
}

@keyframes shimmer {
  0% { left: -100%; }
  100% { left: 100%; }
}

.steps-container {
  display: flex;
  justify-content: space-between;
  position: relative;
  z-index: 2;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  max-width: 200px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.step.clickable:hover {
  transform: translateY(-2px);
}

.step.clickable:hover .step-circle {
  transform: scale(1.1);
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.25);
}

.step-circle {
  position: relative;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: white;
  border: 3px solid var(--pending-color);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.75rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.step.completed .step-circle {
  border-color: var(--completed-color);
  background: var(--completed-color);
  color: white;
}

.step.active .step-circle {
  border-color: var(--active-color);
  background: white;
  color: var(--active-color);
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.step.pending .step-circle {
  border-color: var(--pending-color);
  background: white;
  color: var(--pending-color);
}

.step-inner {
  position: relative;
  z-index: 2;
}

.step-icon {
  width: 20px;
  height: 20px;
  fill: currentColor;
}

.step-active {
  display: flex;
  align-items: center;
  justify-content: center;
}

.step-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid currentColor;
  border-top: 2px solid transparent;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.step-number {
  font-weight: 600;
  font-size: 0.9rem;
}

.completion-ring {
  position: absolute;
  top: -3px;
  left: -3px;
  width: 54px;
  height: 54px;
  transform: rotate(-90deg);
  opacity: 0;
  transition: all 0.5s ease;
}

.step.active .completion-ring,
.step.completed .completion-ring {
  opacity: 1;
}

.progress-circle {
  transition: stroke-dashoffset 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.step.completed .progress-circle {
  stroke: var(--completed-color);
}

.step.active .progress-circle {
  stroke: var(--active-color);
}

.step-content {
  text-align: center;
  min-height: 4rem;
}

.step-title {
  font-weight: 600;
  font-size: 0.9rem;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.step.completed .step-title {
  color: var(--completed-color);
}

.step.active .step-title {
  color: var(--active-color);
}

.step-description {
  font-size: 0.75rem;
  color: var(--text-secondary);
  line-height: 1.3;
  margin-bottom: 0.5rem;
}

.step-details {
  background: rgba(59, 130, 246, 0.05);
  border-radius: var(--border-radius);
  padding: 0.75rem;
  margin-top: 0.5rem;
  text-align: left;
  border: 1px solid rgba(59, 130, 246, 0.1);
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.detail-item:last-child {
  margin-bottom: 0;
}

.detail-label {
  font-size: 0.75rem;
  color: var(--text-secondary);
}

.detail-value {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--text-primary);
}

.field-indicators {
  display: flex;
  gap: 2px;
}

.field-indicator {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--pending-color);
  transition: background-color 0.3s ease;
}

.field-indicator.completed {
  background: var(--success-color);
}

.step-status {
  position: absolute;
  top: -8px;
  right: -8px;
}

.status-badge {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  font-weight: 600;
}

.status-badge.completed {
  background: var(--success-color);
  color: white;
}

.status-badge.completed svg {
  width: 12px;
  height: 12px;
  fill: currentColor;
}

.status-badge.active {
  background: var(--active-color);
  color: white;
}

.pulse-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: white;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.status-badge.pending {
  background: var(--background-color);
  color: var(--pending-color);
  border: 1px solid var(--pending-color);
}

.progress-info {
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e2e8f0;
}

.progress-stats {
  display: flex;
  justify-content: center;
  gap: 2rem;
  margin-bottom: 1rem;
}

.stat {
  text-align: center;
}

.stat-value {
  display: block;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--active-color);
  line-height: 1;
}

.stat-label {
  font-size: 0.75rem;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.motivation {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%);
  border-radius: var(--border-radius);
  border: 1px solid rgba(59, 130, 246, 0.2);
}

.motivation-icon {
  font-size: 1.2rem;
}

.motivation-text {
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--text-primary);
}

/* Vertical Layout */
.progress-tracker.vertical {
  max-width: 300px;
}

.vertical .progress-background {
  top: 1rem;
  bottom: 1rem;
  left: 2rem;
  right: auto;
  width: 4px;
  height: auto;
}

.vertical .steps-container {
  flex-direction: column;
  gap: 2rem;
}

.vertical .step {
  flex-direction: row;
  text-align: left;
  max-width: none;
  align-items: flex-start;
}

.vertical .step-circle {
  margin-bottom: 0;
  margin-right: 1rem;
  flex-shrink: 0;
}

.vertical .step-content {
  text-align: left;
  min-height: auto;
}

/* Theme Variations */
.step.theme-success.completed .step-circle,
.step.theme-success.active .step-circle {
  border-color: var(--success-color);
}

.step.theme-success.completed .step-circle {
  background: var(--success-color);
}

.step.theme-warning.completed .step-circle,
.step.theme-warning.active .step-circle {
  border-color: var(--warning-color);
}

.step.theme-warning.completed .step-circle {
  background: var(--warning-color);
}

.step.theme-info.completed .step-circle,
.step.theme-info.active .step-circle {
  border-color: var(--info-color);
}

.step.theme-info.completed .step-circle {
  background: var(--info-color);
}

/* Responsive Design */
@media (max-width: 768px) {
  .steps-container {
    flex-wrap: wrap;
    gap: 1rem;
  }
  
  .step {
    max-width: 120px;
  }
  
  .step-title {
    font-size: 0.8rem;
  }
  
  .step-description {
    font-size: 0.7rem;
  }
  
  .progress-stats {
    gap: 1rem;
  }
  
  .stat-value {
    font-size: 1.25rem;
  }
}

@media (max-width: 480px) {
  .progress-tracker:not(.vertical) {
    padding: 0.5rem;
  }
  
  .step {
    max-width: 100px;
  }
  
  .step-circle {
    width: 40px;
    height: 40px;
  }
  
  .step-title {
    font-size: 0.75rem;
  }
  
  .step-description {
    display: none;
  }
  
  .progress-info {
    margin-top: 1rem;
    padding-top: 1rem;
  }
  
  .progress-stats {
    flex-direction: column;
    gap: 0.5rem;
  }
}
</style> 
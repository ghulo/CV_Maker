<template>
  <div class="smart-field" :class="fieldClasses">
    <!-- Field Label with Status -->
    <div class="field-header">
      <label 
        :for="fieldId" 
        class="field-label"
        :class="{ 'required': required }"
      >
        {{ label }}
        <span v-if="required" class="required-indicator">*</span>
      </label>
      
      <!-- Field Status Indicators -->
      <div class="field-status">
        <div v-if="isValidating" class="status-indicator validating">
          <div class="spinner-sm"></div>
        </div>
        <div v-else-if="isValid && modelValue" class="status-indicator valid">
          <svg viewBox="0 0 24 24">
            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
          </svg>
        </div>
        <div v-else-if="hasError" class="status-indicator error">
          <svg viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
          </svg>
        </div>
      </div>
    </div>
    
    <!-- Field Description -->
    <div v-if="description" class="field-description">
      {{ description }}
    </div>
    
    <!-- Input Field Container -->
    <div class="input-container">
      <!-- Text Input -->
      <input
        v-if="type === 'text' || type === 'email' || type === 'tel' || type === 'url'"
        :id="fieldId"
        ref="inputRef"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :maxlength="maxLength"
        :class="inputClasses"
        @input="handleInput"
        @blur="handleBlur"
        @focus="handleFocus"
        @keydown="handleKeydown"
      />
      
      <!-- Textarea -->
      <textarea
        v-else-if="type === 'textarea'"
        :id="fieldId"
        ref="inputRef"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :maxlength="maxLength"
        :rows="rows"
        :class="inputClasses"
        @input="handleInput"
        @blur="handleBlur"
        @focus="handleFocus"
        @keydown="handleKeydown"
      ></textarea>
      
      <!-- Select Dropdown -->
      <select
        v-else-if="type === 'select'"
        :id="fieldId"
        ref="inputRef"
        :value="modelValue"
        :disabled="disabled"
        :class="inputClasses"
        @change="handleInput"
        @blur="handleBlur"
        @focus="handleFocus"
      >
        <option value="" disabled>{{ placeholder || 'Select an option' }}</option>
        <option
          v-for="option in options"
          :key="option.value"
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>
      
      <!-- Date Input -->
      <input
        v-else-if="type === 'date'"
        :id="fieldId"
        ref="inputRef"
        type="date"
        :value="modelValue"
        :disabled="disabled"
        :min="minDate"
        :max="maxDate"
        :class="inputClasses"
        @input="handleInput"
        @blur="handleBlur"
        @focus="handleFocus"
      />
      
      <!-- Character Count -->
      <div v-if="showCharCount && maxLength" class="char-count">
        {{ characterCount }}/{{ maxLength }}
      </div>
      
      <!-- AI Enhancement Button -->
      <button
        v-if="aiEnhanceable && !disabled"
        type="button"
        class="ai-enhance-btn"
        :class="{ 'processing': aiProcessing }"
        :disabled="aiProcessing || !modelValue"
        @click="requestAIEnhancement"
        :title="$t('ai.enhance')"
      >
        <svg v-if="!aiProcessing" viewBox="0 0 24 24">
          <path d="M12 2l2.09 6.26L20 10l-5.91 1.74L12 18l-2.09-6.26L4 10l5.91-1.74L12 2z"/>
        </svg>
        <div v-else class="spinner-sm"></div>
      </button>
    </div>
    
    <!-- AI Suggestions Panel -->
    <div v-if="showSuggestions && suggestions.length" class="suggestions-panel">
      <div class="suggestions-header">
        <span class="suggestions-title">{{ $t('ai.suggestions') }}</span>
        <button
          type="button"
          class="close-suggestions"
          @click="showSuggestions = false"
        >
          <svg viewBox="0 0 24 24">
            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
          </svg>
        </button>
      </div>
      
      <div class="suggestions-list">
        <button
          v-for="(suggestion, index) in suggestions"
          :key="index"
          type="button"
          class="suggestion-item"
          @click="applySuggestion(suggestion)"
        >
          <div class="suggestion-content">
            <div class="suggestion-text">{{ suggestion.text || suggestion }}</div>
            <div v-if="suggestion.reason" class="suggestion-reason">
              {{ suggestion.reason }}
            </div>
          </div>
          <svg class="suggestion-apply" viewBox="0 0 24 24">
            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
          </svg>
        </button>
      </div>
    </div>
    
    <!-- Validation Messages -->
    <div v-if="hasError || hasWarning || hasInfo" class="field-messages">
      <div v-if="hasError" class="message error">
        <svg viewBox="0 0 24 24">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
        </svg>
        <span>{{ errorMessage }}</span>
      </div>
      
      <div v-if="hasWarning" class="message warning">
        <svg viewBox="0 0 24 24">
          <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
        </svg>
        <span>{{ warningMessage }}</span>
      </div>
      
      <div v-if="hasInfo" class="message info">
        <svg viewBox="0 0 24 24">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
        </svg>
        <span>{{ infoMessage }}</span>
      </div>
    </div>
    
    <!-- Smart Hints -->
    <div v-if="smartHints.length && showHints" class="smart-hints">
      <div class="hints-header">
        <svg viewBox="0 0 24 24">
          <path d="M12 2l3.09 6.26L22 9l-5.91 1.74L13 17l-2.09-6.26L5 9l5.91-1.74L12 2z"/>
        </svg>
        <span>{{ $t('form.smartHints') }}</span>
      </div>
      <ul class="hints-list">
        <li v-for="hint in smartHints" :key="hint">{{ hint }}</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text',
    validator: (value) => ['text', 'email', 'tel', 'url', 'textarea', 'select', 'date'].includes(value)
  },
  label: {
    type: String,
    required: true
  },
  placeholder: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  maxLength: {
    type: Number,
    default: null
  },
  rows: {
    type: Number,
    default: 3
  },
  options: {
    type: Array,
    default: () => []
  },
  minDate: {
    type: String,
    default: null
  },
  maxDate: {
    type: String,
    default: null
  },
  validationRules: {
    type: Array,
    default: () => []
  },
  aiEnhanceable: {
    type: Boolean,
    default: false
  },
  aiSuggestions: {
    type: Array,
    default: () => []
  },
  showCharCount: {
    type: Boolean,
    default: false
  },
  showHints: {
    type: Boolean,
    default: true
  },
  debounceDelay: {
    type: Number,
    default: 300
  },
  fieldContext: {
    type: String,
    default: ''
  }
})

const emit = defineEmits([
  'update:modelValue',
  'blur',
  'focus',
  'validate',
  'ai-enhance',
  'suggestion-applied'
])

// Refs
const inputRef = ref(null)
const isValidating = ref(false)
const isFocused = ref(false)
const aiProcessing = ref(false)
const showSuggestions = ref(false)
const suggestions = ref([])
const debounceTimer = ref(null)

// Validation state
const errorMessage = ref('')
const warningMessage = ref('')
const infoMessage = ref('')

// Computed Properties
const fieldId = computed(() => `field-${Math.random().toString(36).substr(2, 9)}`)

const fieldClasses = computed(() => ({
  'field-focused': isFocused.value,
  'field-valid': isValid.value && props.modelValue,
  'field-error': hasError.value,
  'field-warning': hasWarning.value,
  'field-disabled': props.disabled,
  'field-required': props.required,
  [`field-${props.type}`]: true
}))

const inputClasses = computed(() => ({
  'form-input': true,
  'input-error': hasError.value,
  'input-warning': hasWarning.value,
  'input-valid': isValid.value && props.modelValue,
  'input-enhanced': props.aiEnhanceable
}))

const isValid = computed(() => {
  return !hasError.value && props.modelValue
})

const hasError = computed(() => !!errorMessage.value)
const hasWarning = computed(() => !!warningMessage.value)
const hasInfo = computed(() => !!infoMessage.value)

const characterCount = computed(() => {
  return props.modelValue ? props.modelValue.toString().length : 0
})

const smartHints = computed(() => {
  const hints = []
  
  if (props.type === 'email') {
    hints.push(t('hints.emailFormat'))
  }
  
  if (props.type === 'tel') {
    hints.push(t('hints.phoneFormat'))
  }
  
  if (props.maxLength && characterCount.value > props.maxLength * 0.8) {
    hints.push(t('hints.characterLimit', { remaining: props.maxLength - characterCount.value }))
  }
  
  if (props.fieldContext === 'summary' && characterCount.value < 50) {
    hints.push(t('hints.summaryLength'))
  }
  
  if (props.fieldContext === 'experience' && characterCount.value < 30) {
    hints.push(t('hints.experienceDetail'))
  }
  
  return hints
})

// Methods
const handleInput = (event) => {
  const value = event.target.value
  emit('update:modelValue', value)
  
  // Debounced validation
  if (debounceTimer.value) {
    clearTimeout(debounceTimer.value)
  }
  
  debounceTimer.value = setTimeout(() => {
    validateField(value)
  }, props.debounceDelay)
}

const handleBlur = () => {
  isFocused.value = false
  validateField(props.modelValue)
  emit('blur')
}

const handleFocus = () => {
  isFocused.value = true
  
  // Load AI suggestions if available
  if (props.aiSuggestions.length > 0) {
    suggestions.value = props.aiSuggestions
    showSuggestions.value = true
  }
  
  emit('focus')
}

const handleKeydown = (event) => {
  // Handle Enter key for AI enhancement
  if (event.key === 'Enter' && event.ctrlKey && props.aiEnhanceable) {
    event.preventDefault()
    requestAIEnhancement()
  }
  
  // Handle Escape to close suggestions
  if (event.key === 'Escape' && showSuggestions.value) {
    showSuggestions.value = false
    inputRef.value?.focus()
  }
}

const validateField = async (value) => {
  if (!props.validationRules.length) return
  
  isValidating.value = true
  errorMessage.value = ''
  warningMessage.value = ''
  infoMessage.value = ''
  
  try {
    for (const rule of props.validationRules) {
      const result = await validateRule(rule, value)
      
      if (!result.valid) {
        if (result.severity === 'error') {
          errorMessage.value = result.message
          break
        } else if (result.severity === 'warning') {
          warningMessage.value = result.message
        } else if (result.severity === 'info') {
          infoMessage.value = result.message
        }
      }
    }
    
    emit('validate', {
      valid: !hasError.value,
      value,
      errors: errorMessage.value ? [errorMessage.value] : []
    })
    
  } finally {
    isValidating.value = false
  }
}

const validateRule = async (rule, value) => {
  switch (rule.type) {
    case 'required':
      return {
        valid: !!value,
        message: rule.message || t('validation.required'),
        severity: 'error'
      }
      
    case 'email':
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      return {
        valid: !value || emailRegex.test(value),
        message: rule.message || t('validation.emailInvalid'),
        severity: 'error'
      }
      
    case 'minLength':
      return {
        valid: !value || value.length >= rule.value,
        message: rule.message || t('validation.minLength', { min: rule.value }),
        severity: rule.severity || 'error'
      }
      
    case 'maxLength':
      return {
        valid: !value || value.length <= rule.value,
        message: rule.message || t('validation.maxLength', { max: rule.value }),
        severity: 'error'
      }
      
    case 'pattern':
      const regex = new RegExp(rule.pattern)
      return {
        valid: !value || regex.test(value),
        message: rule.message || t('validation.patternMismatch'),
        severity: 'error'
      }
      
    case 'custom':
      if (typeof rule.validator === 'function') {
        return await rule.validator(value)
      }
      return { valid: true }
      
    default:
      return { valid: true }
  }
}

const requestAIEnhancement = async () => {
  if (!props.modelValue || aiProcessing.value) return
  
  aiProcessing.value = true
  
  try {
    emit('ai-enhance', {
      value: props.modelValue,
      context: props.fieldContext,
      callback: (enhancedValue) => {
        if (enhancedValue && enhancedValue !== props.modelValue) {
          emit('update:modelValue', enhancedValue)
          
          // Show success feedback
          infoMessage.value = t('ai.enhancementApplied')
          setTimeout(() => {
            infoMessage.value = ''
          }, 3000)
        }
      }
    })
  } finally {
    aiProcessing.value = false
  }
}

const applySuggestion = (suggestion) => {
  const suggestionText = typeof suggestion === 'string' ? suggestion : suggestion.text
  emit('update:modelValue', suggestionText)
  showSuggestions.value = false
  
  emit('suggestion-applied', suggestion)
  
  // Focus back to input
  nextTick(() => {
    inputRef.value?.focus()
  })
}

const focus = () => {
  inputRef.value?.focus()
}

const blur = () => {
  inputRef.value?.blur()
}

// Watchers
watch(() => props.aiSuggestions, (newSuggestions) => {
  suggestions.value = newSuggestions
  if (newSuggestions.length > 0 && isFocused.value) {
    showSuggestions.value = true
  }
})

watch(() => props.modelValue, (newValue) => {
  if (newValue && props.validationRules.length) {
    validateField(newValue)
  }
})

// Lifecycle
onMounted(() => {
  // Initial validation if there's a value
  if (props.modelValue && props.validationRules.length) {
    validateField(props.modelValue)
  }
})

// Expose methods
defineExpose({
  focus,
  blur,
  validate: () => validateField(props.modelValue)
})
</script>

<style scoped>
.smart-field {
  margin-bottom: 1.5rem;
}

.field-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.field-label {
  font-weight: 600;
  font-size: 0.875rem;
  color: #374151;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.field-label.required {
  color: #1f2937;
}

.required-indicator {
  color: #ef4444;
  font-weight: 700;
}

.field-status {
  display: flex;
  align-items: center;
}

.status-indicator {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  border-radius: 50%;
}

.status-indicator.validating {
  background: #f3f4f6;
}

.status-indicator.valid {
  background: #10b981;
  color: white;
}

.status-indicator.error {
  background: #ef4444;
  color: white;
}

.status-indicator svg {
  width: 12px;
  height: 12px;
  fill: currentColor;
}

.field-description {
  font-size: 0.75rem;
  color: #6b7280;
  margin-bottom: 0.5rem;
  line-height: 1.4;
}

.input-container {
  position: relative;
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  background: white;
  color: #1f2937;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input:disabled {
  background: #f9fafb;
  color: #9ca3af;
  cursor: not-allowed;
}

.input-error {
  border-color: #ef4444;
}

.input-error:focus {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.input-warning {
  border-color: #f59e0b;
}

.input-warning:focus {
  border-color: #f59e0b;
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

.input-valid {
  border-color: #10b981;
}

.input-enhanced {
  padding-right: 3rem;
}

.char-count {
  position: absolute;
  bottom: -1.5rem;
  right: 0;
  font-size: 0.75rem;
  color: #6b7280;
}

.ai-enhance-btn {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  width: 32px;
  height: 32px;
  border: none;
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  color: white;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.ai-enhance-btn:hover:not(:disabled) {
  transform: translateY(-50%) scale(1.1);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.ai-enhance-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.ai-enhance-btn svg {
  width: 16px;
  height: 16px;
  fill: currentColor;
}

.suggestions-panel {
  margin-top: 0.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: white;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  max-height: 200px;
  overflow-y: auto;
}

.suggestions-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #e5e7eb;
  background: #f9fafb;
}

.suggestions-title {
  font-size: 0.75rem;
  font-weight: 600;
  color: #374151;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.close-suggestions {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 4px;
  transition: background-color 0.2s ease;
}

.close-suggestions:hover {
  background: #e5e7eb;
}

.close-suggestions svg {
  width: 16px;
  height: 16px;
  fill: currentColor;
}

.suggestions-list {
  max-height: 140px;
  overflow-y: auto;
}

.suggestion-item {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem 1rem;
  border: none;
  background: white;
  cursor: pointer;
  transition: background-color 0.2s ease;
  text-align: left;
}

.suggestion-item:hover {
  background: #f3f4f6;
}

.suggestion-item:not(:last-child) {
  border-bottom: 1px solid #f3f4f6;
}

.suggestion-content {
  flex: 1;
}

.suggestion-text {
  font-size: 0.875rem;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.suggestion-reason {
  font-size: 0.75rem;
  color: #6b7280;
}

.suggestion-apply {
  width: 16px;
  height: 16px;
  fill: #10b981;
  opacity: 0;
  transition: opacity 0.2s ease;
}

.suggestion-item:hover .suggestion-apply {
  opacity: 1;
}

.field-messages {
  margin-top: 0.5rem;
}

.message {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem;
  border-radius: 6px;
  font-size: 0.75rem;
  line-height: 1.4;
}

.message svg {
  width: 16px;
  height: 16px;
  fill: currentColor;
  flex-shrink: 0;
}

.message.error {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

.message.warning {
  background: #fffbeb;
  color: #d97706;
  border: 1px solid #fed7aa;
}

.message.info {
  background: #eff6ff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
}

.smart-hints {
  margin-top: 0.75rem;
  padding: 0.75rem;
  background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
  border: 1px solid #bae6fd;
  border-radius: 8px;
}

.hints-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #0284c7;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.hints-header svg {
  width: 14px;
  height: 14px;
  fill: currentColor;
}

.hints-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.hints-list li {
  font-size: 0.75rem;
  color: #0369a1;
  margin-bottom: 0.25rem;
  padding-left: 1rem;
  position: relative;
}

.hints-list li:before {
  content: '💡';
  position: absolute;
  left: 0;
  top: 0;
}

.hints-list li:last-child {
  margin-bottom: 0;
}

/* Spinner Animation */
.spinner-sm {
  width: 14px;
  height: 14px;
  border: 2px solid currentColor;
  border-top: 2px solid transparent;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Processing Animation */
.processing {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

/* Responsive Design */
@media (max-width: 768px) {
  .form-input {
    padding: 0.625rem 0.875rem;
  }
  
  .ai-enhance-btn {
    width: 28px;
    height: 28px;
    right: 0.625rem;
  }
  
  .ai-enhance-btn svg {
    width: 14px;
    height: 14px;
  }
  
  .suggestions-panel {
    max-height: 160px;
  }
  
  .suggestion-item {
    padding: 0.625rem 0.875rem;
  }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
  .field-label {
    color: #f9fafb;
  }
  
  .field-description {
    color: #9ca3af;
  }
  
  .form-input {
    background: #1f2937;
    border-color: #374151;
    color: #f9fafb;
  }
  
  .form-input:focus {
    border-color: #3b82f6;
  }
  
  .suggestions-panel {
    background: #1f2937;
    border-color: #374151;
  }
  
  .suggestions-header {
    background: #111827;
    border-color: #374151;
  }
  
  .suggestion-item {
    background: #1f2937;
  }
  
  .suggestion-item:hover {
    background: #374151;
  }
  
  .suggestion-text {
    color: #f9fafb;
  }
}
</style> 
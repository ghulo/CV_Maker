<template>
  <div class="error-boundary" v-if="hasError">
    <div class="error-container">
      <div class="error-icon">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      
      <div class="error-content">
        <h3 class="error-title">{{ $t('errors.somethingWentWrong') }}</h3>
        <p class="error-message">{{ errorMessage }}</p>
        
        <div class="error-details" v-if="showDetails">
          <details>
            <summary>{{ $t('errors.technicalDetails') }}</summary>
            <pre class="error-stack">{{ errorStack }}</pre>
          </details>
        </div>
        
        <div class="error-actions">
          <button 
            @click="retry" 
            class="btn-primary"
            :disabled="retrying"
          >
            <i :class="retrying ? 'fas fa-spinner fa-spin' : 'fas fa-redo'"></i>
            {{ retrying ? $t('common.retrying') : $t('common.retry') }}
          </button>
          
          <button 
            @click="goHome" 
            class="btn-secondary"
          >
            <i class="fas fa-home"></i>
            {{ $t('common.goHome') }}
          </button>
          
          <button 
            @click="reportError" 
            class="btn-outline"
            v-if="!errorReported"
          >
            <i class="fas fa-bug"></i>
            {{ $t('errors.reportError') }}
          </button>
        </div>
        
        <div class="error-suggestions" v-if="suggestions.length > 0">
          <h4>{{ $t('errors.suggestions') }}</h4>
          <ul>
            <li v-for="suggestion in suggestions" :key="suggestion">
              {{ suggestion }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  
  <slot v-else></slot>
</template>

<script setup>
import { ref, computed, onErrorCaptured, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'

const router = useRouter()
const { t } = useI18n()

const hasError = ref(false)
const error = ref(null)
const retrying = ref(false)
const errorReported = ref(false)
const retryCount = ref(0)
const maxRetries = 3

const props = defineProps({
  fallback: {
    type: String,
    default: null
  },
  showDetails: {
    type: Boolean,
    default: false
  },
  autoRetry: {
    type: Boolean,
    default: false
  },
  maxAutoRetries: {
    type: Number,
    default: 2
  }
})

const emit = defineEmits(['error', 'retry', 'recover'])

const errorMessage = computed(() => {
  if (!error.value) return ''
  
  // Network errors
  if (error.value.message?.includes('NetworkError') || 
      error.value.message?.includes('fetch')) {
    return t('errors.networkError')
  }
  
  // Authentication errors
  if (error.value.status === 401 || error.value.status === 403) {
    return t('errors.authenticationError')
  }
  
  // Server errors
  if (error.value.status >= 500) {
    return t('errors.serverError')
  }
  
  // Client errors
  if (error.value.status >= 400) {
    return t('errors.clientError')
  }
  
  // JavaScript errors
  if (error.value instanceof TypeError) {
    return t('errors.typeError')
  }
  
  // Generic error with message
  if (error.value.message) {
    return error.value.message
  }
  
  return t('errors.unknownError')
})

const errorStack = computed(() => {
  if (!error.value) return ''
  return error.value.stack || error.value.toString()
})

const suggestions = computed(() => {
  const errorMsg = error.value?.message?.toLowerCase() || ''
  const suggestions = []
  
  if (errorMsg.includes('network') || errorMsg.includes('fetch')) {
    suggestions.push(t('errors.checkConnection'))
    suggestions.push(t('errors.tryAgainLater'))
  }
  
  if (errorMsg.includes('auth') || error.value?.status === 401) {
    suggestions.push(t('errors.loginAgain'))
    suggestions.push(t('errors.checkCredentials'))
  }
  
  if (errorMsg.includes('timeout')) {
    suggestions.push(t('errors.checkConnectionSpeed'))
    suggestions.push(t('errors.tryAgainShorter'))
  }
  
  if (errorMsg.includes('validation')) {
    suggestions.push(t('errors.checkFormFields'))
    suggestions.push(t('errors.ensureRequired'))
  }
  
  return suggestions
})

// Error capture
onErrorCaptured((err, instance, info) => {
  console.error('Error captured by boundary:', err, info)
  handleError(err, info)
  return false // Prevent error from propagating
})

// Enhanced error handling with performance monitoring
const handleError = (err, info = null) => {
  // Performance impact measurement
  const errorTime = performance.now()
  
  hasError.value = true
  error.value = err
  
  // Add context information
  if (info) {
    error.value.componentInfo = info
  }
  
  // Add performance context
  error.value.performanceContext = {
    timestamp: errorTime,
    memoryUsage: performance.memory ? {
      used: Math.round(performance.memory.usedJSHeapSize / 1024 / 1024),
      total: Math.round(performance.memory.totalJSHeapSize / 1024 / 1024),
      limit: Math.round(performance.memory.jsHeapSizeLimit / 1024 / 1024)
    } : null,
    navigationTiming: performance.timing ? {
      domLoading: performance.timing.domLoading,
      domInteractive: performance.timing.domInteractive,
      domComplete: performance.timing.domComplete
    } : null
  }
  
  emit('error', err)
  
  // Auto-retry for certain error types
  if (props.autoRetry && retryCount.value < props.maxAutoRetries) {
    setTimeout(() => {
      retry()
    }, 2000 * (retryCount.value + 1)) // Exponential backoff
  }
  
  // Enhanced error logging
  logError(err)
}

const handleWindowError = (event) => {
  handleError(new Error(event.message), `${event.filename}:${event.lineno}`)
}

const handleUnhandledRejection = (event) => {
  handleError(event.reason, 'Unhandled Promise Rejection')
}

const retry = async () => {
  if (retrying.value) return
  
  retrying.value = true
  retryCount.value++
  
  try {
    // Wait a moment before retrying
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Reset error state
    hasError.value = false
    error.value = null
    
    emit('retry')
    
    // If we have a fallback, navigate to it
    if (props.fallback) {
      router.push(props.fallback)
    }
    
    emit('recover')
    
  } catch (retryError) {
    console.error('Retry failed:', retryError)
    // Keep the error state
  } finally {
    retrying.value = false
  }
}

const goHome = () => {
  router.push('/')
}

const reportError = async () => {
  try {
    errorReported.value = true
    
    // Send error report to backend
    const errorReport = {
      message: error.value?.message || 'Unknown error',
      stack: error.value?.stack || '',
      userAgent: navigator.userAgent,
      url: window.location.href,
      timestamp: new Date().toISOString(),
      userId: localStorage.getItem('user_id') || 'anonymous'
    }
    
    await fetch('/api/error-reports', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      },
      body: JSON.stringify(errorReport)
    })
    
    // Show success message
    console.log('Error reported successfully')
    
  } catch (reportError) {
    console.error('Failed to report error:', reportError)
    errorReported.value = false
  }
}

// Enhanced error logging with better debugging
const logError = (err) => {
  // Log to console in development
  if (import.meta.env.DEV) {
    console.group('🚨 Error Boundary Caught Error')
    console.error('Error:', err)
    console.error('Component Info:', err.componentInfo)
    console.error('Performance Context:', err.performanceContext)
    console.trace()
    console.groupEnd()
  }
  
  // Send to error tracking service in production
  if (import.meta.env.PROD) {
    // Example: Sentry, LogRocket, etc.
    try {
      // window.Sentry?.captureException(err, {
      //   tags: {
      //     component: 'ErrorBoundary',
      //     retryCount: retryCount.value
      //   },
      //   extra: {
      //     performanceContext: err.performanceContext,
      //     componentInfo: err.componentInfo
      //   }
      // })
    } catch (trackingError) {
      console.error('Failed to track error:', trackingError)
    }
  }
}

// Expose methods for parent components
defineExpose({
  handleError,
  retry,
  reset: () => {
    hasError.value = false
    error.value = null
    retryCount.value = 0
    errorReported.value = false
  }
})
</script>

<style scoped>
.error-boundary {
  min-height: 400px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.error-container {
  max-width: 600px;
  width: 100%;
  background: white;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.error-icon {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  padding: 2rem;
  text-align: center;
}

.error-icon i {
  font-size: 3rem;
  opacity: 0.9;
}

.error-content {
  padding: 2rem;
}

.error-title {
  margin: 0 0 1rem 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.error-message {
  margin: 0 0 1.5rem 0;
  color: #64748b;
  font-size: 1rem;
  line-height: 1.6;
}

.error-details {
  margin: 1.5rem 0;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.error-details summary {
  cursor: pointer;
  font-weight: 600;
  color: #475569;
  margin-bottom: 0.5rem;
}

.error-stack {
  margin: 0;
  padding: 1rem;
  background: #1e293b;
  color: #f1f5f9;
  border-radius: 6px;
  font-size: 0.75rem;
  line-height: 1.4;
  overflow-x: auto;
  white-space: pre-wrap;
  word-break: break-all;
}

.error-actions {
  display: flex;
  gap: 1rem;
  margin: 1.5rem 0;
  flex-wrap: wrap;
}

.btn-primary, .btn-secondary, .btn-outline {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
  text-decoration: none;
}

.btn-primary {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
  background: #f8fafc;
  color: #475569;
  border: 1px solid #e2e8f0;
}

.btn-secondary:hover {
  background: #e2e8f0;
}

.btn-outline {
  background: transparent;
  color: #ef4444;
  border: 1px solid #ef4444;
}

.btn-outline:hover {
  background: #ef4444;
  color: white;
}

.btn-primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.error-suggestions {
  margin-top: 1.5rem;
  padding: 1.5rem;
  background: #f0f9ff;
  border-radius: 8px;
  border: 1px solid #bae6fd;
}

.error-suggestions h4 {
  margin: 0 0 1rem 0;
  font-size: 1rem;
  font-weight: 600;
  color: #0c4a6e;
}

.error-suggestions ul {
  margin: 0;
  padding-left: 1.25rem;
  color: #075985;
}

.error-suggestions li {
  margin-bottom: 0.5rem;
  line-height: 1.5;
}

@media (max-width: 768px) {
  .error-boundary {
    padding: 1rem;
  }
  
  .error-container {
    margin: 0;
  }
  
  .error-content {
    padding: 1.5rem;
  }
  
  .error-actions {
    flex-direction: column;
  }
  
  .btn-primary, .btn-secondary, .btn-outline {
    justify-content: center;
  }
}
</style> 
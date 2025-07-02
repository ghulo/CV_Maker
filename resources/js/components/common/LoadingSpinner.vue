<template>
  <div 
    class="loading-spinner" 
    :class="[`size-${size}`, `variant-${variant}`, { 'with-overlay': overlay }]"
  >
    <!-- Background Overlay -->
    <div v-if="overlay" class="spinner-overlay" @click="$emit('cancel')"></div>
    
    <!-- Spinner Container -->
    <div class="spinner-container">
      <!-- Default Spinner -->
      <div v-if="variant === 'default'" class="spinner-default">
        <div class="spinner-ring"></div>
        <div class="spinner-inner"></div>
      </div>
      
      <!-- Pulse Spinner -->
      <div v-else-if="variant === 'pulse'" class="spinner-pulse">
        <div class="pulse-ring"></div>
        <div class="pulse-core"></div>
      </div>
      
      <!-- Dots Spinner -->
      <div v-else-if="variant === 'dots'" class="spinner-dots">
        <div class="dot" v-for="i in 3" :key="i"></div>
      </div>
      
      <!-- Ring Spinner -->
      <div v-else-if="variant === 'ring'" class="spinner-ring-loader">
        <div class="ring" v-for="i in 2" :key="i"></div>
      </div>
      
      <!-- Bars Spinner -->
      <div v-else-if="variant === 'bars'" class="spinner-bars">
        <div class="bar" v-for="i in 5" :key="i"></div>
      </div>
      
      <!-- Logo Spinner -->
      <div v-else-if="variant === 'logo'" class="spinner-logo">
        <div class="logo-container">
          <svg viewBox="0 0 100 100" class="logo-svg">
            <circle 
              cx="50" 
              cy="50" 
              r="45" 
              fill="none" 
              stroke="currentColor" 
              stroke-width="2"
              stroke-dasharray="280"
              stroke-dashoffset="280"
              class="logo-circle"
            />
            <text 
              x="50" 
              y="55" 
              text-anchor="middle" 
              class="logo-text"
              font-family="Arial, sans-serif"
              font-weight="bold"
              font-size="16"
            >
              A
            </text>
          </svg>
        </div>
      </div>
      
      <!-- Loading Text -->
      <div v-if="showText" class="spinner-text">
        <p class="loading-message">{{ message }}</p>
        <div v-if="showProgress" class="progress-dots">
          <span class="dot" :class="{ active: progressDots >= 1 }">.</span>
          <span class="dot" :class="{ active: progressDots >= 2 }">.</span>
          <span class="dot" :class="{ active: progressDots >= 3 }">.</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'pulse', 'dots', 'ring', 'bars', 'logo'].includes(value)
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large', 'xlarge'].includes(value)
  },
  overlay: {
    type: Boolean,
    default: false
  },
  message: {
    type: String,
    default: ''
  },
  showText: {
    type: Boolean,
    default: true
  },
  showProgress: {
    type: Boolean,
    default: false
  },
  duration: {
    type: Number,
    default: 0 // 0 means indefinite
  }
})

const emit = defineEmits(['cancel', 'timeout'])

const progressDots = ref(1)
let progressInterval = null
let timeoutId = null

const displayMessage = computed(() => {
  if (props.message) return props.message
  return t('common.loading')
})

onMounted(() => {
  // Animate progress dots
  if (props.showProgress) {
    progressInterval = setInterval(() => {
      progressDots.value = (progressDots.value % 3) + 1
    }, 500)
  }
  
  // Set timeout if duration is specified
  if (props.duration > 0) {
    timeoutId = setTimeout(() => {
      emit('timeout')
    }, props.duration)
  }
})

onUnmounted(() => {
  if (progressInterval) {
    clearInterval(progressInterval)
  }
  if (timeoutId) {
    clearTimeout(timeoutId)
  }
})
</script>

<style scoped>
.loading-spinner {
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.loading-spinner.with-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 9999;
}

.spinner-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
}

.spinner-container {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 2rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Size variations */
.size-small .spinner-container { padding: 1rem; }
.size-medium .spinner-container { padding: 1.5rem; }
.size-large .spinner-container { padding: 2rem; }
.size-xlarge .spinner-container { padding: 3rem; }

/* Default Spinner */
.spinner-default {
  display: flex;
  gap: 0.25rem;
}

.spinner-ring {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f4f6;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.spinner-inner {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  border-radius: 50%;
  background: transparent;
}

.size-small .spinner-ring { width: 30px; height: 30px; border-width: 3px; }
.size-large .spinner-ring { width: 60px; height: 60px; border-width: 6px; }
.size-xlarge .spinner-ring { width: 80px; height: 80px; border-width: 8px; }

/* Pulse Spinner */
.spinner-pulse {
  position: relative;
  width: 40px;
  height: 40px;
}

.pulse-ring {
  position: absolute;
  border: 2px solid #3b82f6;
  border-radius: 50%;
  animation: pulse 1.25s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
}

.pulse-core {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  border-radius: 50%;
  background: transparent;
}

.size-small .spinner-pulse { width: 30px; height: 30px; }
.size-large .spinner-pulse { width: 60px; height: 60px; }
.size-xlarge .spinner-pulse { width: 80px; height: 80px; }

/* Dots Spinner */
.spinner-dots {
  display: flex;
  gap: 0.5rem;
}

.spinner-dots .dot {
  width: 12px;
  height: 12px;
  background: #3b82f6;
  border-radius: 50%;
  animation: scale 1.0s ease-in-out infinite;
}

.spinner-dots .dot:nth-child(1) { animation-delay: 0.0s; }
.spinner-dots .dot:nth-child(2) { animation-delay: 0.2s; }
.spinner-dots .dot:nth-child(3) { animation-delay: 0.4s; }
.spinner-dots .dot:nth-child(4) { animation-delay: 0.6s; }

.size-small .spinner-dots .dot { width: 8px; height: 8px; }
.size-large .spinner-dots .dot { width: 16px; height: 16px; }
.size-xlarge .spinner-dots .dot { width: 20px; height: 20px; }

/* Ring Spinner */
.spinner-ring-loader {
  display: flex;
  gap: 0.25rem;
}

.spinner-ring-loader .ring {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f4f6;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.size-small .spinner-ring-loader .ring { width: 30px; height: 30px; border-width: 3px; }
.size-large .spinner-ring-loader .ring { width: 60px; height: 60px; border-width: 6px; }
.size-xlarge .spinner-ring-loader .ring { width: 80px; height: 80px; border-width: 8px; }

/* Bars Spinner */
.spinner-bars {
  display: flex;
  gap: 0.25rem;
  align-items: flex-end;
}

.spinner-bars .bar {
  width: 4px;
  height: 20px;
  background: #3b82f6;
  border-radius: 2px;
  animation: bars 1.2s ease-in-out infinite;
}

.spinner-bars .bar:nth-child(1) { animation-delay: -1.2s; }
.spinner-bars .bar:nth-child(2) { animation-delay: -1.1s; }
.spinner-bars .bar:nth-child(3) { animation-delay: -1.0s; }
.spinner-bars .bar:nth-child(4) { animation-delay: -0.9s; }
.spinner-bars .bar:nth-child(5) { animation-delay: -0.8s; }

.size-small .spinner-bars .bar { width: 3px; height: 15px; }
.size-large .spinner-bars .bar { width: 6px; height: 30px; }
.size-xlarge .spinner-bars .bar { width: 8px; height: 40px; }

/* Logo Spinner */
.spinner-logo {
  position: relative;
}

.logo-container {
  width: 60px;
  height: 60px;
  color: #3b82f6;
}

.logo-svg {
  width: 100%;
  height: 100%;
  animation: logoSpin 2s linear infinite;
}

.logo-circle {
  animation: drawCircle 2s ease-in-out infinite;
}

.logo-text {
  fill: currentColor;
  animation: fadeInOut 2s ease-in-out infinite;
}

.size-small .logo-container { width: 40px; height: 40px; }
.size-large .logo-container { width: 80px; height: 80px; }
.size-xlarge .logo-container { width: 100px; height: 100px; }

/* Text styles */
.spinner-text {
  text-align: center;
}

.loading-message {
  margin: 0;
  color: #64748b;
  font-size: 0.875rem;
  font-weight: 500;
}

.progress-dots {
  margin-top: 0.5rem;
  letter-spacing: 0.2rem;
}

.progress-dots .dot {
  color: #cbd5e1;
  font-size: 1.5rem;
  transition: color 0.3s ease;
}

.progress-dots .dot.active {
  color: #3b82f6;
}

/* Animations */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes pulse {
  0% {
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    opacity: 1;
    transform: translate(-50%, -50%);
  }
  100% {
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transform: translate(0, 0);
  }
}

@keyframes scale {
  0%, 80%, 100% { 
    transform: scale(0);
  } 
  40% { 
    transform: scale(1);
  }
}

@keyframes bars {
  0%, 40%, 100% { 
    transform: scaleY(0.4);
  }  
  20% { 
    transform: scaleY(1.0);
  }
}

@keyframes logoSpin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes drawCircle {
  0% { 
    stroke-dashoffset: 280;
    opacity: 0.3;
  }
  50% { 
    stroke-dashoffset: 140;
    opacity: 1;
  }
  100% { 
    stroke-dashoffset: 0;
    opacity: 0.3;
  }
}

@keyframes fadeInOut {
  0%, 100% { opacity: 0.3; }
  50% { opacity: 1; }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .spinner-container {
    background: #1e293b;
    color: #f1f5f9;
  }
  
  .loading-message {
    color: #94a3b8;
  }
}
</style> 
<template>
  <transition name="liquid-slide">
    <div v-if="visible" class="liquid-notification" :class="[type, position]">
      <div class="notification-glass">
        <div class="notification-content">
          <div class="notification-icon">
            <i :class="iconClass"></i>
          </div>
          <div class="notification-text">
            <h4>{{ title }}</h4>
            <p>{{ message }}</p>
          </div>
          <button @click="close" class="notification-close">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import { ref, computed, onMounted } from 'vue'

export default {
  name: 'LiquidNotification',
  props: {
    title: {
      type: String,
      required: true
    },
    message: {
      type: String,
      required: true
    },
    type: {
      type: String,
      default: 'info',
      validator: (value) => ['info', 'success', 'warning', 'error'].includes(value)
    },
    position: {
      type: String,
      default: 'top-right',
      validator: (value) => ['top-right', 'top-left', 'bottom-right', 'bottom-left'].includes(value)
    },
    duration: {
      type: Number,
      default: 5000
    },
    autoClose: {
      type: Boolean,
      default: true
    }
  },
  emits: ['close'],
  setup(props, { emit }) {
    const visible = ref(false)
    
    const iconClass = computed(() => {
      const icons = {
        info: 'fas fa-info-circle',
        success: 'fas fa-check-circle',
        warning: 'fas fa-exclamation-triangle',
        error: 'fas fa-times-circle'
      }
      return icons[props.type]
    })
    
    const close = () => {
      visible.value = false
      setTimeout(() => {
        emit('close')
      }, 300)
    }
    
    onMounted(() => {
      visible.value = true
      if (props.autoClose && props.duration > 0) {
        setTimeout(close, props.duration)
      }
    })
    
    return {
      visible,
      iconClass,
      close
    }
  }
}
</script>

<style scoped>
.liquid-notification {
  position: fixed;
  z-index: 9999;
  margin: 20px;
  max-width: 400px;
}

/* Positioning */
.liquid-notification.top-right {
  top: 20px;
  right: 20px;
}

.liquid-notification.top-left {
  top: 20px;
  left: 20px;
}

.liquid-notification.bottom-right {
  bottom: 20px;
  right: 20px;
}

.liquid-notification.bottom-left {
  bottom: 20px;
  left: 20px;
}

/* Notification Card (was Glass Container) */
.notification-glass {
  position: relative;
  background: var(--bg-card); /* Use theme background */
  border: 1px solid var(--border); /* Use theme border */
  border-radius: var(--radius-lg); /* Use global radius */
  padding: var(--space-lg); /* Use global spacing */
  box-shadow: var(--shadow-md); /* Use global shadow */
  overflow: hidden;
  transition: all 0.3s ease; /* Smooth transition */
}

/* Content Layout */
.notification-content {
  position: relative;
  display: flex;
  align-items: flex-start;
  gap: var(--space-md);
}

/* Icon Styling */
.notification-icon {
  flex-shrink: 0;
  width: 44px; /* Slightly larger */
  height: 44px; /* Slightly larger */
  background: var(--primary); /* Solid primary color */
  border-radius: var(--radius); /* Consistent radius */
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px; /* Slightly larger icon */
  color: white; /* White icon for contrast */
  box-shadow: var(--shadow-sm); /* Subtle shadow */
}

/* Type-specific icon colors */
.liquid-notification.success .notification-icon {
  background: var(--success); /* Use success color */
}

.liquid-notification.warning .notification-icon {
  background: var(--warning); /* Use warning color */
}

.liquid-notification.error .notification-icon {
  background: var(--danger); /* Use danger color */
}

/* Text Content */
.notification-text {
  flex: 1;
  min-width: 0;
}

.notification-text h4 {
  font-size: var(--font-size-base);
  font-weight: 600;
  margin: 0 0 var(--space-xs) 0;
  color: var(--text-primary);
}

.notification-text p {
  font-size: var(--font-size-sm);
  margin: 0;
  color: var(--text-secondary);
  line-height: var(--line-height-normal);
}

/* Close Button */
.notification-close {
  flex-shrink: 0;
  width: 32px; /* Slightly larger */
  height: 32px; /* Slightly larger */
  background: var(--bg-tertiary); /* Use tertiary background */
  border: 1px solid var(--border-light); /* Lighter border */
  border-radius: var(--radius-sm); /* Consistent radius */
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease-in-out; /* Smoother transition */
  color: var(--text-secondary);
  font-size: 14px;
}

.notification-close:hover {
  background: var(--bg-secondary); /* Hover background */
  transform: none; /* Removed rotation */
  color: var(--text-primary);
}

/* Slide Animation */
.liquid-slide-enter-active,
.liquid-slide-leave-active {
  transition: all 0.3s ease-out; /* Smoother, less bouncy transition */
}

.liquid-slide-enter-from {
  transform: translateX(100%) scale(0.9);
  opacity: 0;
}

.liquid-slide-leave-to {
  transform: translateX(100%) scale(0.9);
  opacity: 0;
}

.liquid-notification.top-left .liquid-slide-enter-from,
.liquid-notification.bottom-left .liquid-slide-enter-from {
  transform: translateX(-100%) scale(0.9);
}

.liquid-notification.top-left .liquid-slide-leave-to,
.liquid-notification.bottom-left .liquid-slide-leave-to {
  transform: translateX(-100%) scale(0.9);
}

/* Progress Bar (optional) */
.notification-progress {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: rgba(124, 58, 237, 0.2);
  border-radius: 0 0 var(--radius-lg) var(--radius-lg); /* Consistent radius */
  overflow: hidden;
}

.notification-progress-bar {
  height: 100%;
  background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
  animation: progress linear forwards;
  transform-origin: left;
}

/* Mobile Responsiveness */
@media (max-width: 480px) {
  .liquid-notification {
    margin: var(--space-md);
    max-width: calc(100vw - var(--space-xl));
  }
  
  .notification-glass {
    padding: var(--space-md);
  }
  
  .notification-icon {
    width: 36px;
    height: 36px;
    font-size: 18px;
  }
  
  .notification-text h4 {
    font-size: var(--font-size-sm);
  }
  
  .notification-text p {
    font-size: var(--font-size-xs);
  }
}
</style> 
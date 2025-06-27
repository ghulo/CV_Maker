<template>
  <div class="liquid-control-card" :class="{ 'active': isActive }" @click="toggleActive">
    <div class="control-glass">
      <div class="control-shimmer"></div>
      <div class="control-content">
        <div class="control-icon-wrapper">
          <div class="control-icon" :class="{ 'active': isActive }">
            <i :class="icon"></i>
          </div>
        </div>
        <h3>{{ title }}</h3>
        <p>{{ description }}</p>
        <div v-if="hasToggle" class="control-toggle">
          <div class="ios-toggle" :class="{ 'active': isActive }">
            <div class="ios-toggle-thumb"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'

export default {
  name: 'LiquidControlCard',
  props: {
    title: {
      type: String,
      required: true
    },
    description: {
      type: String,
      required: true
    },
    icon: {
      type: String,
      required: true
    },
    hasToggle: {
      type: Boolean,
      default: false
    },
    initialActive: {
      type: Boolean,
      default: false
    }
  },
  emits: ['toggle'],
  setup(props, { emit }) {
    const isActive = ref(props.initialActive)
    
    const toggleActive = () => {
      if (props.hasToggle) {
        isActive.value = !isActive.value
        emit('toggle', isActive.value)
      }
    }
    
    return {
      isActive,
      toggleActive
    }
  }
}
</script>

<style scoped>
.liquid-control-card {
  position: relative;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  transform-style: preserve-3d;
  perspective: 1000px;
}

.control-glass {
  position: relative;
  background: linear-gradient(
    145deg,
    rgba(255, 255, 255, 0.92) 0%,
    rgba(255, 255, 255, 0.85) 100%
  );
  backdrop-filter: blur(40px) saturate(180%);
  -webkit-backdrop-filter: blur(40px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.5);
  border-radius: 24px;
  padding: 24px;
  overflow: hidden;
  box-shadow: 
    0 12px 32px -8px rgba(0, 0, 0, 0.08),
    inset 0 2px 8px rgba(255, 255, 255, 0.95),
    inset 0 -2px 8px rgba(0, 0, 0, 0.02);
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

body.dark-theme .control-glass {
  background: linear-gradient(
    145deg,
    rgba(40, 40, 40, 0.92) 0%,
    rgba(30, 30, 30, 0.85) 100%
  );
  border: 1px solid rgba(255, 255, 255, 0.15);
}

/* Hover Effects */
.liquid-control-card:hover {
  transform: translateY(-4px) scale(1.02);
}

.liquid-control-card:hover .control-glass {
  box-shadow: 
    0 20px 40px -10px rgba(0, 0, 0, 0.12),
    0 0 40px rgba(124, 58, 237, 0.1),
    inset 0 2px 12px rgba(255, 255, 255, 1),
    inset 0 -2px 8px rgba(0, 0, 0, 0.02);
}

/* Active State */
.liquid-control-card.active .control-glass {
  background: linear-gradient(
    145deg,
    rgba(124, 58, 237, 0.15) 0%,
    rgba(124, 58, 237, 0.08) 100%
  );
  border: 1px solid rgba(124, 58, 237, 0.3);
  box-shadow: 
    0 12px 32px -8px rgba(124, 58, 237, 0.2),
    0 0 60px rgba(124, 58, 237, 0.15),
    inset 0 2px 8px rgba(255, 255, 255, 0.6),
    inset 0 -2px 8px rgba(124, 58, 237, 0.05);
}

/* Shimmer Effect */
.control-shimmer {
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent 0%,
    rgba(255, 255, 255, 0.3) 50%,
    transparent 100%
  );
  transform: skewX(-20deg);
  transition: left 0.5s ease;
}

.liquid-control-card:hover .control-shimmer {
  left: 100%;
}

/* Content Styling */
.control-content {
  position: relative;
  z-index: 1;
  text-align: center;
}

.control-icon-wrapper {
  display: inline-block;
  margin-bottom: 16px;
}

.control-icon {
  width: 64px;
  height: 64px;
  background: linear-gradient(
    135deg,
    rgba(240, 240, 240, 0.9) 0%,
    rgba(230, 230, 230, 0.8) 100%
  );
  border: 1px solid rgba(0, 0, 0, 0.05);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  color: var(--text-secondary);
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  box-shadow: 
    0 4px 12px rgba(0, 0, 0, 0.05),
    inset 0 2px 4px rgba(255, 255, 255, 0.9);
}

body.dark-theme .control-icon {
  background: linear-gradient(
    135deg,
    rgba(60, 60, 60, 0.9) 0%,
    rgba(50, 50, 50, 0.8) 100%
  );
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.control-icon.active {
  background: linear-gradient(
    135deg,
    var(--primary) 0%,
    var(--primary-light) 100%
  );
  color: white;
  transform: scale(1.1) rotate(-5deg);
  box-shadow: 
    0 8px 24px rgba(124, 58, 237, 0.3),
    inset 0 2px 4px rgba(255, 255, 255, 0.3);
}

.liquid-control-card:hover .control-icon {
  transform: scale(1.05) rotate(5deg);
}

.liquid-control-card:hover .control-icon.active {
  transform: scale(1.15) rotate(-10deg);
}

.control-content h3 {
  font-size: 18px;
  font-weight: 600;
  margin: 0 0 8px 0;
  color: var(--text-primary);
  transition: color 0.3s ease;
}

.liquid-control-card.active .control-content h3 {
  color: var(--primary);
}

.control-content p {
  font-size: 14px;
  color: var(--text-secondary);
  margin: 0 0 16px 0;
  line-height: 1.5;
  transition: color 0.3s ease;
}

/* iOS Toggle */
.control-toggle {
  display: flex;
  justify-content: center;
  margin-top: 16px;
}

.ios-toggle {
  width: 51px;
  height: 31px;
  background: rgba(120, 120, 128, 0.16);
  border-radius: 31px;
  padding: 2px;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  box-shadow: 
    inset 0 1px 2px rgba(0, 0, 0, 0.1),
    0 1px 2px rgba(0, 0, 0, 0.05);
}

.ios-toggle.active {
  background: linear-gradient(
    135deg,
    var(--primary) 0%,
    var(--primary-light) 100%
  );
  box-shadow: 
    inset 0 1px 2px rgba(0, 0, 0, 0.2),
    0 2px 8px rgba(124, 58, 237, 0.3);
}

.ios-toggle-thumb {
  width: 27px;
  height: 27px;
  background: white;
  border-radius: 50%;
  box-shadow: 
    0 3px 8px rgba(0, 0, 0, 0.15),
    0 3px 1px rgba(0, 0, 0, 0.06);
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  transform: translateX(0);
}

.ios-toggle.active .ios-toggle-thumb {
  transform: translateX(20px);
  box-shadow: 
    0 3px 12px rgba(0, 0, 0, 0.2),
    0 3px 1px rgba(0, 0, 0, 0.1);
}

/* 3D Press Effect */
.liquid-control-card:active .control-glass {
  transform: translateZ(-10px);
  box-shadow: 
    0 4px 12px -4px rgba(0, 0, 0, 0.08),
    inset 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Responsive */
@media (max-width: 768px) {
  .control-icon {
    width: 56px;
    height: 56px;
    font-size: 24px;
  }
  
  .control-content h3 {
    font-size: 16px;
  }
  
  .control-content p {
    font-size: 13px;
  }
}
</style> 
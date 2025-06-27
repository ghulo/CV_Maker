/**
 * LIQUID ANIMATIONS CONTROLLER
 * Modern, performant animation system using Intersection Observer API
 * Perfect for glassmorphism and liquid design aesthetics
 */

class LiquidAnimations {
  constructor(options = {}) {
    this.options = {
      threshold: 0.1,
      rootMargin: '50px 0px -50px 0px',
      triggerOnce: true,
      ...options
    }
    
    this.observer = null
    this.animatedElements = new Set()
    
    this.init()
  }

  init() {
    // Check for browser support
    if (!('IntersectionObserver' in window)) {
      console.warn('Intersection Observer not supported, falling back to immediate animation')
      this.fallbackAnimation()
      return
    }

    this.createObserver()
    this.observeElements()
  }

  createObserver() {
    this.observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !this.animatedElements.has(entry.target)) {
          this.animateElement(entry.target)
          
          if (this.options.triggerOnce) {
            this.observer.unobserve(entry.target)
            this.animatedElements.add(entry.target)
          }
        }
      })
    }, this.options)
  }

  observeElements() {
    const elements = document.querySelectorAll('.liquid-reveal, .liquid-premium')
    elements.forEach(element => {
      this.observer.observe(element)
    })
  }

  animateElement(element) {
    // Add a small delay for more natural feel
    const delay = this.getElementDelay(element)
    
    setTimeout(() => {
      element.classList.add('animate')
      
      // Trigger any custom callbacks
      const event = new CustomEvent('liquidAnimated', { detail: { element } })
      element.dispatchEvent(event)
    }, delay)
  }

  getElementDelay(element) {
    // Check for custom delay attribute
    const customDelay = element.getAttribute('data-delay')
    if (customDelay) {
      return parseInt(customDelay)
    }

    // Auto-calculate delay for staggered containers
    const parent = element.closest('.stagger-container, .stagger-premium')
    if (parent) {
      const siblings = Array.from(parent.querySelectorAll('.liquid-reveal, .liquid-premium'))
      const index = siblings.indexOf(element)
      const delayMultiplier = parent.classList.contains('stagger-premium') ? 150 : 100
      return index * delayMultiplier // Premium has longer delays for smoother effect
    }

    return 0
  }

  // Fallback for browsers without Intersection Observer
  fallbackAnimation() {
    const elements = document.querySelectorAll('.liquid-reveal, .liquid-premium')
    elements.forEach((element, index) => {
      setTimeout(() => {
        element.classList.add('animate')
      }, index * 100)
    })
  }

  // Method to manually trigger animation
  animateNow(selector) {
    const elements = document.querySelectorAll(selector)
    elements.forEach(element => {
      if (element.classList.contains('liquid-reveal') || element.classList.contains('liquid-premium')) {
        this.animateElement(element)
      }
    })
  }

  // Method to reset animations (useful for page transitions)
  reset() {
    const elements = document.querySelectorAll('.liquid-reveal.animate, .liquid-premium.animate')
    elements.forEach(element => {
      element.classList.remove('animate')
    })
    this.animatedElements.clear()
    
    // Re-observe elements
    if (this.observer) {
      this.observeElements()
    }
  }

  // Refresh method to handle dynamically added content
  refresh() {
    if (this.observer) {
      this.observer.disconnect()
      this.createObserver()
      this.observeElements()
    }
  }

  // Clean up
  destroy() {
    if (this.observer) {
      this.observer.disconnect()
    }
    this.animatedElements.clear()
  }
}

// Create global instance
let liquidAnimator = null

// Auto-initialize when DOM is ready
function initLiquidAnimations(options = {}) {
  if (liquidAnimator) {
    liquidAnimator.destroy()
  }
  
  liquidAnimator = new LiquidAnimations(options)
  return liquidAnimator
}

// Utility functions for Vue components
export function useLiquidAnimations() {
  return {
    init: (options) => initLiquidAnimations(options),
    animateNow: (selector) => liquidAnimator?.animateNow(selector),
    reset: () => liquidAnimator?.reset(),
    refresh: () => liquidAnimator?.refresh(),
    destroy: () => liquidAnimator?.destroy()
  }
}

// Auto-initialize on DOM ready
if (typeof document !== 'undefined') {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initLiquidAnimations())
  } else {
    initLiquidAnimations()
  }
}

// Export for manual use
export { LiquidAnimations, initLiquidAnimations }
export default LiquidAnimations 
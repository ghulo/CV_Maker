<template>
  <Teleport to="body">
    <div
      v-if="isVisible"
      class="modal-overlay visible"
      @click.self="cancel"
      role="dialog"
      aria-modal="true"
      aria-labelledby="modal-title"
      aria-describedby="modal-message"
      tabindex="-1"
    >
      <div class="modal modal-sm" @click.stop>
        <div class="modal-header">
          <h3 id="modal-title" class="modal-title">{{ title }}</h3>
        </div>
        <div class="modal-body">
          <p id="modal-message">{{ message }}</p>
        </div>
        <div class="modal-footer">
          <button
            @click="cancel"
            class="btn btn-secondary"
            type="button"
            autofocus
          >
            {{ cancelButtonText }}
          </button>
          <button
            @click="confirm"
            :class="['btn', confirmButtonClass]"
            type="button"
          >
            {{ confirmButtonText }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { defineProps, defineEmits, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Confirm Action',
  },
  message: {
    type: String,
    default: 'Are you sure you want to continue?',
  },
  confirmButtonText: {
    type: String,
    default: 'Confirm',
  },
  cancelButtonText: {
    type: String,
    default: 'Cancel',
  },
  confirmButtonClass: {
    type: String,
    default: 'btn-primary', // btn-danger for delete
  },
})

const emit = defineEmits(['confirm', 'cancel'])

const confirm = () => {
  emit('confirm')
}
const cancel = () => {
  emit('cancel')
}

// Accessibility: Trap focus within modal when open
let lastFocusedElement = null
const trapFocus = (e) => {
  const modal = document.querySelector('.modal-overlay.visible .modal')
  if (!modal) return
  const focusable = modal.querySelectorAll('button, [tabindex]:not([tabindex="-1"])')
  if (!focusable.length) return
  const first = focusable[0]
  const last = focusable[focusable.length - 1]
  if (e.key === 'Tab') {
    if (e.shiftKey) {
      if (document.activeElement === first) {
        e.preventDefault()
        last.focus()
      }
    } else {
      if (document.activeElement === last) {
        e.preventDefault()
        first.focus()
      }
    }
  } else if (e.key === 'Escape') {
    cancel()
  }
}

onMounted(() => {
  if (props.isVisible) {
    lastFocusedElement = document.activeElement
    setTimeout(() => {
      const modal = document.querySelector('.modal-overlay.visible .modal')
      if (modal) {
        const btn = modal.querySelector('button')
        if (btn) btn.focus()
      }
    }, 0)
    document.addEventListener('keydown', trapFocus)
  }
})

onBeforeUnmount(() => {
  document.removeEventListener('keydown', trapFocus)
  if (lastFocusedElement) lastFocusedElement.focus()
})
</script>

<style scoped>
/* No custom styles needed; uses global modal/button/card styles for consistency */
</style>

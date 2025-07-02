import { ref, reactive } from 'vue'

const modalState = reactive({
  isVisible: false,
  title: '',
  message: '',
  confirmButtonText: '',
  cancelButtonText: '',
  confirmButtonClass: '',
  resolvePromise: null,
})

export function useConfirmationModal() {
  const showModal = (options) => {
    modalState.isVisible = true
    modalState.title = options.title || 'Konfirmo Veprimin'
    modalState.message =
      options.message || 'Jeni të sigurt që dëshironi të vazhdoni me këtë veprim?'
    modalState.confirmButtonText = options.confirmButtonText || 'Konfirmo'
    modalState.cancelButtonText = options.cancelButtonText || 'Anulo'
    modalState.confirmButtonClass = options.confirmButtonClass || 'btn-primary'

    return new Promise((resolve) => {
      modalState.resolvePromise = resolve
    })
  }

  const confirm = () => {
    modalState.isVisible = false
    if (modalState.resolvePromise) {
      modalState.resolvePromise(true)
      modalState.resolvePromise = null
    }
  }

  const cancel = () => {
    modalState.isVisible = false
    if (modalState.resolvePromise) {
      modalState.resolvePromise(false)
      modalState.resolvePromise = null
    }
  }

  return {
    modalState,
    showModal,
    confirm,
    cancel,
  }
}

import { ref, createApp, h } from 'vue'
import LiquidNotification from '@/components/common/LiquidNotification.vue'

const notifications = ref([])

export function useLiquidNotification() {
  const show = (options) => {
    const id = Date.now()
    const notification = {
      id,
      ...options
    }
    
    // Create a container for the notification
    const container = document.createElement('div')
    document.body.appendChild(container)
    
    // Create the notification component
    const app = createApp({
      render() {
        return h(LiquidNotification, {
          ...notification,
          onClose: () => {
            app.unmount()
            document.body.removeChild(container)
          }
        })
      }
    })
    
    app.mount(container)
    
    notifications.value.push(notification)
    
    return id
  }
  
  const success = (title, message, options = {}) => {
    return show({
      title,
      message,
      type: 'success',
      ...options
    })
  }
  
  const error = (title, message, options = {}) => {
    return show({
      title,
      message,
      type: 'error',
      ...options
    })
  }
  
  const warning = (title, message, options = {}) => {
    return show({
      title,
      message,
      type: 'warning',
      ...options
    })
  }
  
  const info = (title, message, options = {}) => {
    return show({
      title,
      message,
      type: 'info',
      ...options
    })
  }
  
  return {
    show,
    success,
    error,
    warning,
    info,
    notifications
  }
} 
import { ref, reactive } from 'vue'

// Global notification state
const notifications = ref([])
let notificationId = 0

export function useNotifications() {
  const addNotification = (notification) => {
    const id = ++notificationId
    const newNotification = {
      id,
      type: 'info', // default type
      title: '',
      message: '',
      duration: 5000, // default duration
      persistent: false,
      actions: [],
      ...notification,
      timestamp: Date.now()
    }

    notifications.value.push(newNotification)

    // Auto-remove non-persistent notifications
    if (!newNotification.persistent && newNotification.duration > 0) {
      setTimeout(() => {
        removeNotification(id)
      }, newNotification.duration)
    }

    return id
  }

  const removeNotification = (id) => {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      notifications.value.splice(index, 1)
    }
  }

  const clearAll = () => {
    notifications.value = []
  }

  // Convenience methods for different notification types
  const success = (message, options = {}) => {
    return addNotification({
      type: 'success',
      title: options.title || 'Success',
      message,
      ...options
    })
  }

  const error = (message, options = {}) => {
    return addNotification({
      type: 'error',
      title: options.title || 'Error',
      message,
      duration: options.duration || 7000, // Errors stay longer
      ...options
    })
  }

  const warning = (message, options = {}) => {
    return addNotification({
      type: 'warning',
      title: options.title || 'Warning',
      message,
      ...options
    })
  }

  const info = (message, options = {}) => {
    return addNotification({
      type: 'info',
      title: options.title || 'Information',
      message,
      ...options
    })
  }

  const loading = (message, options = {}) => {
    return addNotification({
      type: 'loading',
      title: options.title || 'Loading',
      message,
      persistent: true, // Loading notifications don't auto-dismiss
      ...options
    })
  }

  // Method to update existing notification (useful for loading states)
  const updateNotification = (id, updates) => {
    const notification = notifications.value.find(n => n.id === id)
    if (notification) {
      Object.assign(notification, updates)
    }
  }

  return {
    notifications,
    addNotification,
    removeNotification,
    clearAll,
    success,
    error,
    warning,
    info,
    loading,
    updateNotification
  }
} 
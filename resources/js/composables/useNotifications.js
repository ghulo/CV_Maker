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

  // Enhanced notification removal with cleanup
  const removeNotification = (id) => {
    const startTime = performance.now()
    const index = notifications.value.findIndex(n => n.id === id)
    
    if (index > -1) {
      const notification = notifications.value[index]
      const duration = Date.now() - notification.timestamp
      
      performanceMetrics.removed++
      performanceMetrics.totalDuration += duration
      
      notifications.value.splice(index, 1)
      
      // Performance logging in development
      if (import.meta.env.DEV) {
        const endTime = performance.now()
        console.log(`📢 Notification removed in ${(endTime - startTime).toFixed(2)}ms`, {
          duration: `${duration}ms`,
          averageDuration: `${(performanceMetrics.totalDuration / performanceMetrics.removed).toFixed(2)}ms`
        })
      }
    }
  }

  // Enhanced bulk operations
  const clearAll = () => {
    const startTime = performance.now()
    const count = notifications.value.length
    
    notifications.value.splice(0)
    performanceMetrics.removed += count
    
    if (import.meta.env.DEV) {
      const endTime = performance.now()
      console.log(`📢 Cleared ${count} notifications in ${(endTime - startTime).toFixed(2)}ms`)
    }
  }

  // Enhanced notification helpers with better defaults
  const success = (message, options = {}) => {
    return addNotification({
      type: 'success',
      title: options.title || 'Success',
      message,
      duration: options.duration || 4000,
      ...options
    })
  }

  const error = (message, options = {}) => {
    return addNotification({
      type: 'error',
      title: options.title || 'Error',
      message,
      duration: options.duration || 6000,
      persistent: options.persistent || false,
      ...options
    })
  }

  const warning = (message, options = {}) => {
    return addNotification({
      type: 'warning',
      title: options.title || 'Warning',
      message,
      duration: options.duration || 5000,
      ...options
    })
  }

  const info = (message, options = {}) => {
    return addNotification({
      type: 'info',
      title: options.title || 'Info',
      message,
      duration: options.duration || 4000,
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
      
      if (import.meta.env.DEV) {
        console.log(`📢 Notification ${id} updated`, updates)
      }
    }
  }

  // Performance monitoring methods
  const getPerformanceMetrics = () => {
    return {
      ...performanceMetrics,
      current: notifications.value.length,
      averageDuration: performanceMetrics.removed > 0 
        ? performanceMetrics.totalDuration / performanceMetrics.removed 
        : 0
    }
  }

  // Computed properties for better reactivity
  const notificationCount = computed(() => notifications.value.length)
  const hasNotifications = computed(() => notifications.value.length > 0)
  const latestNotification = computed(() => 
    notifications.value.length > 0 ? notifications.value[notifications.value.length - 1] : null
  )

  return {
    notifications,
    notificationCount,
    hasNotifications,
    latestNotification,
    addNotification,
    removeNotification,
    clearAll,
    success,
    error,
    warning,
    info,
    loading,
    updateNotification,
    getPerformanceMetrics
  }
} 
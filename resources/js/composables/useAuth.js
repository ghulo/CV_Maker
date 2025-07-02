import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import authService from '../services/authService'

// Global auth state
const isAuthenticated = ref(false)
const user = ref(null)
const loading = ref(false)

// Initialize auth state from localStorage
const initializeAuth = () => {
  const token = authService.getToken()
  const userData = authService.getUser()
  
  isAuthenticated.value = !!token
  user.value = userData
  
  // Listen for storage changes (logout from another tab)
  if (typeof window !== 'undefined') {
    window.addEventListener('storage', (e) => {
      if (e.key === 'auth_token') {
        isAuthenticated.value = !!e.newValue
        if (!e.newValue) {
          user.value = null
        }
      }
    })
  }
}

// Watch for storage changes (multi-tab support)
if (typeof window !== 'undefined') {
  window.addEventListener('storage', (e) => {
    if (e.key === 'auth_token' || e.key === 'user') {
      initializeAuth()
    }
  })
}

export function useAuth() {
  const router = useRouter()

  // Initialize on first use
  if (!isAuthenticated.value && !user.value) {
    initializeAuth()
  }

  const login = async (credentials) => {
    loading.value = true
    try {
      const result = await authService.login(credentials)
      
      if (result.success) {
        isAuthenticated.value = true
        user.value = result.user
        return result
      } else {
        return result
      }
    } catch (error) {
      console.error('Login error:', error)
      return {
        success: false,
        message: 'Login failed. Please try again.'
      }
    } finally {
      loading.value = false
    }
  }

  const register = async (userData) => {
    loading.value = true
    try {
      const result = await authService.register(userData)
      
      if (result.success) {
        isAuthenticated.value = true
        user.value = result.user
        return result
      } else {
        return result
      }
    } catch (error) {
      console.error('Registration error:', error)
      return {
        success: false,
        message: 'Registration failed. Please try again.'
      }
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    loading.value = true
    try {
      await authService.logout()
      isAuthenticated.value = false
      user.value = null
      router.push('/login')
    } catch (error) {
      console.error('Logout error:', error)
      // Still clear local state even if API call fails
      isAuthenticated.value = false
      user.value = null
      router.push('/login')
    } finally {
      loading.value = false
    }
  }

  const updateUser = (userData) => {
    user.value = { ...user.value, ...userData }
    authService.setUser(user.value)
  }

  const checkAuth = () => {
    const token = authService.getToken()
    const userData = authService.getUser()
    
    isAuthenticated.value = !!token
    user.value = userData
    
    return isAuthenticated.value
  }

  const requireAuth = () => {
    if (!isAuthenticated.value) {
      router.push('/login')
      return false
    }
    return true
  }

  const redirectIfAuthenticated = () => {
    if (isAuthenticated.value) {
      router.push('/dashboard')
      return true
    }
    return false
  }

  return {
    // State
    isAuthenticated: computed(() => isAuthenticated.value),
    user: computed(() => user.value),
    loading: computed(() => loading.value),
    
    // Computed
    userName: computed(() => user.value?.name || ''),
    userEmail: computed(() => user.value?.email || ''),
    isGuest: computed(() => !isAuthenticated.value),
    
    // Actions
    login,
    register,
    logout,
    updateUser,
    checkAuth,
    requireAuth,
    redirectIfAuthenticated,
    initializeAuth
  }
} 
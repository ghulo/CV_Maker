import axios from 'axios'

class AuthService {
  constructor() {
    this.baseURL = '/api'
    this.tokenKey = 'auth_token'
    this.userKey = 'auth_user'
  }

  /**
   * User Registration with enhanced validation
   */
  async register(userData) {
    try {
      const response = await axios.post(`${this.baseURL}/register`, {
        name: userData.name,
        email: userData.email,
        password: userData.password,
        password_confirmation: userData.password_confirmation,
        terms_accepted: userData.terms_accepted || false
      })

      if (response.data.token) {
        this.setToken(response.data.token)
        this.setUser(response.data.user)
      }

      return {
        success: true,
        user: response.data.user,
        message: response.data.message || 'Registration successful'
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Registration failed',
        errors: error.response?.data?.errors || {}
      }
    }
  }

  /**
   * User Login with security tracking
   */
  async login(credentials) {
    try {
      const response = await axios.post(`${this.baseURL}/login`, {
        email: credentials.email,
        password: credentials.password,
        remember: credentials.remember || false,
        device_name: this.getDeviceInfo()
      })

      if (response.data.token) {
        this.setToken(response.data.token)
        this.setUser(response.data.user)
        
        // Track login activity
        this.trackLoginActivity(response.data.user)
      }

      return {
        success: true,
        user: response.data.user,
        message: response.data.message || 'Login successful'
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Login failed',
        errors: error.response?.data?.errors || {}
      }
    }
  }

  /**
   * User Logout with token cleanup
   */
  async logout() {
    try {
      const token = this.getToken()
      if (token) {
        await axios.post(`${this.baseURL}/logout`)
      }
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      this.clearToken()
      this.clearUser()
    }
  }

  /**
   * Get current user profile
   */
  async getCurrentUser() {
    try {
      const response = await axios.get(`${this.baseURL}/user`)
      this.setUser(response.data.user)
      return response.data.user
    } catch (error) {
      this.clearUser()
      throw error
    }
  }

  /**
   * Update user profile
   */
  async updateProfile(userData) {
    try {
      const response = await axios.put(`${this.baseURL}/profile`, userData)
      this.setUser(response.data.user)
      
      return {
        success: true,
        user: response.data.user,
        message: response.data.message || 'Profile updated successfully'
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Profile update failed',
        errors: error.response?.data?.errors || {}
      }
    }
  }

  /**
   * Change user password
   */
  async changePassword(passwordData) {
    try {
      const response = await axios.put(`${this.baseURL}/change-password`, {
        current_password: passwordData.current_password,
        new_password: passwordData.new_password,
        new_password_confirmation: passwordData.new_password_confirmation
      })

      return {
        success: true,
        message: response.data.message || 'Password changed successfully'
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Password change failed',
        errors: error.response?.data?.errors || {}
      }
    }
  }

  /**
   * Request password reset
   */
  async requestPasswordReset(email) {
    try {
      const response = await axios.post(`${this.baseURL}/forgot-password`, { email })
      
      return {
        success: true,
        message: response.data.message || 'Password reset email sent'
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Password reset request failed'
      }
    }
  }

  /**
   * Reset password with token
   */
  async resetPassword(resetData) {
    try {
      const response = await axios.post(`${this.baseURL}/reset-password`, {
        token: resetData.token,
        email: resetData.email,
        password: resetData.password,
        password_confirmation: resetData.password_confirmation
      })

      return {
        success: true,
        message: response.data.message || 'Password reset successful'
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Password reset failed',
        errors: error.response?.data?.errors || {}
      }
    }
  }

  /**
   * Token Management Methods
   */
  setToken(accessToken) {
    localStorage.setItem(this.tokenKey, accessToken)
  }

  getToken() {
    return localStorage.getItem(this.tokenKey)
  }

  clearToken() {
    localStorage.removeItem(this.tokenKey)
  }

  /**
   * User Management Methods
   */
  setUser(user) {
    localStorage.setItem(this.userKey, JSON.stringify(user))
  }

  getUser() {
    const user = localStorage.getItem(this.userKey)
    return user ? JSON.parse(user) : null
  }

  clearUser() {
    localStorage.removeItem(this.userKey)
  }

  /**
   * Authentication State Checks
   */
  isAuthenticated() {
    return !!this.getToken()
  }

  isGuest() {
    return !this.isAuthenticated()
  }

  /**
   * Role & Permission Checks
   */
  hasRole(role) {
    const user = this.getUser()
    return user?.roles?.includes(role) || false
  }

  hasPermission(permission) {
    const user = this.getUser()
    return user?.permissions?.includes(permission) || false
  }

  isAdmin() {
    return this.hasRole('admin') || this.hasRole('super_admin')
  }

  /**
   * Security Methods
   */
  getDeviceInfo() {
    return {
      user_agent: navigator.userAgent,
      platform: navigator.platform,
      screen_resolution: `${screen.width}x${screen.height}`,
      timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
      timestamp: new Date().toISOString()
    }
  }

  trackLoginActivity(user) {
    const activity = {
      user_id: user.id,
      action: 'login',
      ip_address: '', // Will be captured on backend
      device_info: this.getDeviceInfo(),
      timestamp: new Date().toISOString()
    }

    // Send to backend for logging
    axios.post('/api/activity-log', activity).catch(error => {
      console.warn('Failed to log activity:', error)
    })
  }

  /**
   * Session Management
   */
  extendSession() {
    return Promise.resolve()
  }

  /**
   * Two-Factor Authentication Methods
   */
  async enable2FA() {
    try {
      const response = await axios.post(`${this.baseURL}/2fa/enable`)
      return {
        success: true,
        qr_code: response.data.qr_code,
        backup_codes: response.data.backup_codes
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || '2FA setup failed'
      }
    }
  }

  async verify2FA(code) {
    try {
      const response = await axios.post(`${this.baseURL}/2fa/verify`, { code })
      return {
        success: true,
        message: response.data.message || '2FA verified successfully'
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || '2FA verification failed'
      }
    }
  }

  async disable2FA(password) {
    try {
      const response = await axios.post(`${this.baseURL}/2fa/disable`, { password })
      return {
        success: true,
        message: response.data.message || '2FA disabled successfully'
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || '2FA disable failed'
      }
    }
  }

  /**
   * Security Monitoring
   */
  async getSecurityEvents() {
    try {
      const response = await axios.get(`${this.baseURL}/security/events`)
      return response.data.events
    } catch (error) {
      console.error('Failed to fetch security events:', error)
      return []
    }
  }

  async getActiveSessions() {
    try {
      const response = await axios.get(`${this.baseURL}/security/sessions`)
      return response.data.sessions
    } catch (error) {
      console.error('Failed to fetch active sessions:', error)
      return []
    }
  }

  async revokeSession(sessionId) {
    try {
      await axios.delete(`${this.baseURL}/security/sessions/${sessionId}`)
      return { success: true }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to revoke session'
      }
    }
  }
}

// Export singleton instance
export default new AuthService()

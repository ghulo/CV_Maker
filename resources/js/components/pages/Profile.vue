<template>
  <div class="profile-wrapper">
    <!-- Animated Background (same as homepage) -->
    <div class="animated-bg">
      <div class="gradient-sphere sphere-1"></div>
      <div class="gradient-sphere sphere-2"></div>
      <div class="gradient-sphere sphere-3"></div>
    </div>

    <div class="profile-container">
      <div v-if="user" class="profile-content">
        <!-- Enhanced Profile Header Section -->
        <section class="profile-header-section">
          <div class="profile-hero-card">
            <div class="profile-hero-content">
              <div class="profile-avatar-section">
                <div class="profile-avatar-wrapper">
                  <div class="profile-avatar" :class="{ 'uploading': avatarUploading }">
                    <img 
                v-if="user.avatar_url || user.avatar" 
                :src="user.avatar_url || (user.avatar ? `/storage/${user.avatar}` : '')" 
                :alt="user.name"
                class="profile-avatar-img"
                loading="lazy"
              />
                    <i v-else class="fas fa-user"></i>
                    <div class="avatar-overlay">
                      <input 
                        type="file" 
                        id="avatar-upload"
                        name="avatar"
                        ref="avatarInput" 
                        @change="handleAvatarUpload" 
                        accept="image/*" 
                        class="avatar-input"
                        aria-label="Upload profile picture"
                      />
                      <button @click="triggerAvatarUpload" class="avatar-upload-btn" :disabled="avatarUploading">
                        <i class="fas" :class="avatarUploading ? 'fa-spinner fa-spin' : 'fa-camera'"></i>
                      </button>
                    </div>
                  </div>
                  <div class="profile-status-indicator online"></div>
                </div>
              </div>
              
              <div class="profile-info-section flex-row-align">
                <div class="profile-info">
                  <h1 class="profile-name">{{ user.name }}</h1>
                  <p class="profile-email">{{ user.email }}</p>
                  <div class="profile-badges">
                                      <span class="badge premium" v-if="user.is_premium">
                    <i class="fas fa-crown"></i>
                    {{ t('profile.premiumMember') }}
                  </span>
                  <span class="badge verified">
                    <i class="fas fa-check-circle"></i>
                    {{ t('profile.verified') }}
                  </span>
                  <span class="badge member-since">
                    <i class="fas fa-calendar"></i>
                    {{ t('profile.memberSince', { date: formatDate(user.created_at, 'short') }) }}
                  </span>
                  </div>
                </div>
                <div class="profile-actions">
                  <button @click="toggleEditMode" class="btn btn-primary" :disabled="profileLoading">
                    <i class="fas" :class="editMode ? 'fa-times' : 'fa-edit'"></i>
                    {{ editMode ? t('common.cancel') : t('profile.editProfile') }}
                  </button>
                  <button @click="downloadProfileData" class="btn btn-secondary" :disabled="loading">
                    <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-download'"></i>
                    {{ t('profile.exportData') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Success/Error Messages -->
        <div v-if="globalMessage" :class="globalMessageClass" class="global-message">
          <i :class="globalMessageType === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle'"></i>
          <span>{{ globalMessage }}</span>
          <button @click="clearGlobalMessage" class="message-close">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Enhanced Profile Statistics -->
        <section class="profile-stats-section">
          <div class="stats-grid">
            <div class="stat-card" v-for="(stat, index) in enhancedStats" :key="stat.id" :style="{ '--delay': `${index * 0.1}s` }">
              <div class="stat-icon" :class="stat.iconClass">
                <i :class="stat.icon"></i>
              </div>
              <div class="stat-content">
                <div class="stat-number">{{ stat.value }}</div>
                <div class="stat-label">{{ stat.label }}</div>
                <div class="stat-trend" :class="stat.trend">
                  <i :class="stat.trendIcon"></i>
                  <span>{{ stat.trendText }}</span>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Enhanced Main Content Grid -->
        <div class="profile-main-grid">
          <!-- Left Column - Enhanced -->
          <div class="profile-left-column">
            
            <!-- Enhanced Profile Information Card -->
            <div class="profile-card modern-card">
              <div class="card-header">
                <div class="card-title-group">
                  <div class="card-icon">
                    <i class="fas fa-user-circle"></i>
                  </div>
                  <h3 class="card-title">{{ t('profile.profileInformation') }}</h3>
                </div>
                <button @click="toggleEditMode" class="btn btn-sm btn-primary" :disabled="profileLoading">
                  <i class="fas" :class="editMode ? 'fa-times' : 'fa-edit'"></i>
                  {{ editMode ? t('common.cancel') : t('common.edit') }}
                </button>
              </div>
              <div class="card-body">
                <form v-if="editMode" @submit.prevent="updateProfile" class="profile-edit-form">
                  <div class="form-grid enhanced-grid">
                    <div class="form-group">
                      <label for="profile-name" class="form-label">{{ t('personal.fullName') }}</label>
                      <div class="input-icon-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input 
                          type="text" 
                          id="profile-name"
                          name="name"
                          v-model="editForm.name" 
                          class="form-input enhanced-input"
                          placeholder="Enter your full name"
                          required
                        />
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="profile-email" class="form-label">Email</label>
                      <div class="input-icon-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input 
                          type="email" 
                          id="profile-email"
                          name="email"
                          v-model="editForm.email" 
                          class="form-input enhanced-input"
                          placeholder="Enter your email"
                          required
                        />
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="profile-phone" class="form-label">Phone</label>
                      <div class="input-icon-wrapper">
                        <i class="fas fa-phone input-icon"></i>
                        <input 
                          type="tel" 
                          id="profile-phone"
                          name="phone"
                          v-model="editForm.phone" 
                          class="form-input enhanced-input"
                          placeholder="Enter your phone number"
                        />
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="profile-location" class="form-label">Location</label>
                      <div class="input-icon-wrapper">
                        <i class="fas fa-map-marker-alt input-icon"></i>
                        <input 
                          type="text" 
                          id="profile-location"
                          name="location"
                          v-model="editForm.location" 
                          class="form-input enhanced-input"
                          placeholder="Enter your location"
                        />
                      </div>
                    </div>
                    <div class="form-group full-width">
                      <label for="profile-bio" class="form-label">Bio</label>
                      <div class="textarea-wrapper">
                        <textarea 
                          id="profile-bio"
                          name="bio"
                          v-model="editForm.bio" 
                          class="form-textarea enhanced-textarea"
                          placeholder="Tell us about yourself..."
                          rows="4"
                        ></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-enhanced" :disabled="profileLoading">
                      <i v-if="profileLoading" class="fas fa-spinner fa-spin"></i>
                      <i v-else class="fas fa-save"></i>
                      {{ t('common.saveChanges') }}
                    </button>
                  </div>
                </form>
                <div v-else class="profile-info-display enhanced-display">
                  <div class="info-grid enhanced-info-grid">
                    <div class="info-item">
                      <div class="info-icon">
                        <i class="fas fa-user"></i>
                      </div>
                      <div class="info-content">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ user.name }}</div>
                      </div>
                    </div>
                    <div class="info-item">
                      <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                      </div>
                      <div class="info-content">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ user.email }}</div>
                      </div>
                    </div>
                    <div class="info-item">
                      <div class="info-icon">
                        <i class="fas fa-phone"></i>
                      </div>
                      <div class="info-content">
                        <div class="info-label">Phone</div>
                        <div class="info-value">{{ user.phone || 'Not provided' }}</div>
                      </div>
                    </div>
                    <div class="info-item">
                      <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                      </div>
                      <div class="info-content">
                        <div class="info-label">Location</div>
                        <div class="info-value">{{ user.location || user.address || 'Not provided' }}</div>
                      </div>
                    </div>
                    <div class="info-item full-width">
                      <div class="info-icon">
                        <i class="fas fa-user-edit"></i>
                      </div>
                      <div class="info-content">
                        <div class="info-label">Bio</div>
                        <div class="info-value bio-text">{{ user.bio || 'No bio provided yet.' }}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Enhanced Security Settings -->
            <div class="profile-card modern-card">
              <div class="card-header">
                <div class="card-title-group">
                  <div class="card-icon security-icon">
                    <i class="fas fa-shield-alt"></i>
                  </div>
                  <h3 class="card-title">Security Settings</h3>
                </div>
              </div>
              <div class="card-body">
                <form @submit.prevent="updatePassword" class="security-form">
                  <div v-if="passwordMessage" :class="passwordMessageClass" class="message enhanced-message">
                    <i :class="passwordMessageType === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle'"></i>
                    <span>{{ passwordMessage }}</span>
                  </div>
                  
                  <div class="form-group">
                    <label for="current-password" class="form-label">Current Password</label>
                    <div class="input-icon-wrapper">
                      <i class="fas fa-lock input-icon"></i>
                      <input 
                        type="password" 
                        id="current-password"
                        name="current_password"
                        v-model="passwordForm.current_password" 
                        class="form-input enhanced-input"
                        placeholder="Enter current password"
                        required
                      />
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="new-password" class="form-label">New Password</label>
                    <div class="input-icon-wrapper">
                      <i class="fas fa-key input-icon"></i>
                      <input 
                        type="password" 
                        id="new-password"
                        name="password"
                        v-model="passwordForm.password" 
                        class="form-input enhanced-input"
                        placeholder="Enter new password"
                        required
                        minlength="8"
                      />
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="confirm-password" class="form-label">Confirm New Password</label>
                    <div class="input-icon-wrapper">
                      <i class="fas fa-key input-icon"></i>
                      <input 
                        type="password" 
                        id="confirm-password"
                        name="password_confirmation"
                        v-model="passwordForm.password_confirmation" 
                        class="form-input enhanced-input"
                        placeholder="Confirm new password"
                        required
                        minlength="8"
                      />
                    </div>
                  </div>
                  
                  <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-enhanced" :disabled="passwordLoading">
                      <i v-if="passwordLoading" class="fas fa-spinner fa-spin"></i>
                      <i v-else class="fas fa-shield-alt"></i>
                      Update Password
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Enhanced Activity Timeline -->
            <div class="profile-card modern-card">
              <div class="card-header">
                <div class="card-title-group">
                  <div class="card-icon activity-icon">
                    <i class="fas fa-history"></i>
                  </div>
                  <h3 class="card-title">Recent Activity</h3>
                </div>
              </div>
              <div class="card-body">
                <div v-if="recentActivity.length > 0" class="activity-timeline enhanced-timeline">
                  <div v-for="activity in recentActivity" :key="activity.id" class="activity-item enhanced-activity" :class="`activity-${activity.type}`">
                    <div class="activity-icon" :class="getActivityIconClass(activity.type)">
                      <i :class="activity.icon"></i>
                    </div>
                    <div class="activity-content">
                      <div class="activity-title">{{ activity.title }}</div>
                      <div class="activity-description">{{ activity.description }}</div>
                      <div v-if="activity.metadata" class="activity-metadata">
                        <div v-if="activity.metadata.cv_title" class="metadata-item">
                          <i class="fas fa-file-alt"></i>
                          <span>{{ activity.metadata.cv_title }}</span>
                        </div>
                        <div v-if="activity.metadata.template" class="metadata-item">
                          <i class="fas fa-palette"></i>
                          <span>{{ formatTemplate(activity.metadata.template) }}</span>
                        </div>
                        <div v-if="activity.metadata.cvs_count" class="metadata-item">
                          <i class="fas fa-archive"></i>
                          <span>{{ activity.metadata.cvs_count }} CVs</span>
                        </div>
                        <div v-if="activity.metadata.file_size" class="metadata-item">
                          <i class="fas fa-hdd"></i>
                          <span>{{ formatFileSize(activity.metadata.file_size) }}</span>
                        </div>
                      </div>
                      <div class="activity-time">
                        <i class="fas fa-clock"></i>
                        {{ formatDate(activity.created_at, 'relative') }}
                      </div>
                    </div>
                  </div>
                </div>
                <div v-else class="empty-state enhanced-empty">
                  <div class="empty-icon">
                    <i class="fas fa-history"></i>
                  </div>
                  <h4>No Recent Activity</h4>
                  <p>Your recent activities will appear here.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column - Enhanced -->
          <div class="profile-right-column">
            
            <!-- Enhanced Achievements Card -->
            <div class="profile-card modern-card">
              <div class="card-header">
                <div class="card-title-group">
                  <div class="card-icon achievements-icon">
                    <i class="fas fa-trophy"></i>
                  </div>
                  <h3 class="card-title">Achievements</h3>
                </div>
                <div class="achievement-count">{{ achievements.filter(a => a.unlocked).length }}/{{ achievements.length }}</div>
              </div>
              <div class="card-body">
                <div class="achievements-grid enhanced-achievements">
                  <div v-for="achievement in achievements" :key="achievement.id" 
                       class="achievement-item enhanced-achievement" 
                       :class="{ 'unlocked': achievement.unlocked }">
                    <div class="achievement-icon">
                      <i :class="achievement.icon"></i>
                    </div>
                    <div class="achievement-info">
                      <h4 class="achievement-title">{{ achievement.title }}</h4>
                      <p class="achievement-description">{{ achievement.description }}</p>
                      <div v-if="achievement.progress !== undefined" class="achievement-progress">
                        <div class="progress-bar">
                          <div class="progress-fill" :style="{ '--bar-width': achievement.progress + '%' }"></div>
                        </div>
                        <span class="progress-text">{{ achievement.progress }}%</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Enhanced Quick Actions -->
            <div class="profile-card modern-card">
              <div class="card-header">
                <div class="card-title-group">
                  <div class="card-icon actions-icon">
                    <i class="fas fa-bolt"></i>
                  </div>
                  <h3 class="card-title">Quick Actions</h3>
                </div>
              </div>
              <div class="card-body">
                <div class="quick-actions-grid enhanced-actions">
                  <router-link to="/create-cv" class="quick-action-btn enhanced-action">
                    <div class="action-icon">
                      <i class="fas fa-plus"></i>
                    </div>
                    <span>Create CV</span>
                  </router-link>
                  <router-link to="/templates" class="quick-action-btn enhanced-action">
                    <div class="action-icon">
                      <i class="fas fa-palette"></i>
                    </div>
                    <span>Browse Templates</span>
                  </router-link>
                  <button @click="downloadAllCvs" class="quick-action-btn enhanced-action" :disabled="loading">
                    <div class="action-icon">
                      <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-download'"></i>
                    </div>
                    <span>Download All</span>
                  </button>
                  <router-link to="/contact" class="quick-action-btn enhanced-action">
                    <div class="action-icon">
                                                  <i class="fas fa-life-ring"></i>
                    </div>
                    <span>Get Support</span>
                  </router-link>
                </div>
              </div>
            </div>

            <!-- Enhanced Danger Zone -->
            <div class="profile-card modern-card danger-zone">
              <div class="card-header">
                <div class="card-title-group">
                  <div class="card-icon danger-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                  </div>
                  <h3 class="card-title">Danger Zone</h3>
                </div>
              </div>
              <div class="card-body">
                                  <div class="danger-actions">
                    <div class="danger-action enhanced-danger">
                      <div class="danger-info">
                        <h4>Delete Account</h4>
                        <p>Permanently delete your account and all associated data. This action cannot be undone.</p>
                      </div>
                      <button @click="confirmDeleteAccount" class="btn btn-danger btn-enhanced" :disabled="loading">
                        <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                        <i v-else class="fas fa-trash"></i>
                        Delete Account
                      </button>
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Loading State -->
      <div v-else class="loading-wrapper enhanced-loading">
        <div class="loading-spinner">
          <div class="simple-spinner"></div>
        </div>
        <p class="loading-text">{{ t('common.loading') }}...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import axios from 'axios'
import { useConfirmationModal } from '../../composables/useConfirmationModal.js'

// Router, i18n and modal
const router = useRouter()
const { t } = useI18n()
const { showModal } = useConfirmationModal()

// Refs
const avatarInput = ref(null)
const user = ref(null)
const userStats = ref({})
const recentActivity = ref([])

// Loading states
const loading = ref(false)
const profileLoading = ref(false)
const passwordLoading = ref(false)
const avatarUploading = ref(false)

// Edit mode
const editMode = ref(false)

// Messages
const passwordMessage = ref('')
const passwordMessageType = ref('')
const globalMessage = ref('')
const globalMessageType = ref('')

// Forms
const editForm = reactive({
  name: '',
  email: '',
  phone: '',
  location: '',
  bio: ''
})

const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: ''
})

// Computed
const passwordMessageClass = computed(() => {
  if (!passwordMessage.value) return ''
  return passwordMessageType.value === 'success' ? 'message success' : 'message error'
})

const globalMessageClass = computed(() => {
  if (!globalMessage.value) return ''
  return globalMessageType.value === 'success' ? 'global-message success' : 'global-message error'
})

const enhancedStats = computed(() => [
  {
    id: 'cvs',
    icon: 'fas fa-file-alt',
    iconClass: 'cvs-icon',
    value: userStats.value.cvs_created || 0,
    label: 'CVs Created',
    trend: 'positive',
    trendIcon: 'fas fa-arrow-up',
    trendText: '+2 this month'
  },
  {
    id: 'downloads',
    icon: 'fas fa-download',
    iconClass: 'downloads-icon',
    value: userStats.value.total_downloads || 0,
    label: 'Downloads',
    trend: 'positive',
    trendIcon: 'fas fa-arrow-up',
    trendText: '+5 this week'
  },
  {
    id: 'views',
    icon: 'fas fa-eye',
    iconClass: 'views-icon',
    value: userStats.value.total_views || 0,
    label: 'Profile Views',
    trend: 'neutral',
    trendIcon: 'fas fa-minus',
    trendText: 'No change'
  },
  {
    id: 'achievements',
    icon: 'fas fa-trophy',
    iconClass: 'achievements-icon',
    value: achievements.value.filter(a => a.unlocked).length,
    label: 'Achievements',
    trend: 'positive',
    trendIcon: 'fas fa-arrow-up',
    trendText: 'Recent unlock'
  }
])

// Mock achievements data with dynamic progress
const achievements = computed(() => [
  {
    id: 1,
    title: 'First Steps',
    description: 'Created your first CV',
    icon: 'fas fa-baby',
    unlocked: userStats.value.cvs_created >= 1,
    progress: userStats.value.cvs_created >= 1 ? 100 : 0
  },
  {
    id: 2,
    title: 'Profile Complete',
    description: 'Complete your profile information',
    icon: 'fas fa-user-check',
    unlocked: user.value?.bio && user.value?.phone && user.value?.location,
    progress: calculateProfileProgress()
  },
  {
    id: 3,
    title: 'Template Explorer',
    description: 'Try 3 different CV templates',
    icon: 'fas fa-palette',
    unlocked: false,
    progress: 33
  },
  {
    id: 4,
    title: 'Download Master',
    description: 'Download 10 CVs',
    icon: 'fas fa-download',
    unlocked: userStats.value.total_downloads >= 10,
    progress: Math.min((userStats.value.total_downloads || 0) * 10, 100)
  },
  {
    id: 5,
    title: 'Premium Member',
    description: 'Upgrade to premium',
    icon: 'fas fa-crown',
    unlocked: user.value?.is_premium,
    progress: user.value?.is_premium ? 100 : 0
  },
  {
    id: 6,
    title: 'CV Expert',
    description: 'Create 5 different CVs',
    icon: 'fas fa-graduation-cap',
    unlocked: userStats.value.cvs_created >= 5,
    progress: Math.min((userStats.value.cvs_created || 0) * 20, 100)
  }
])

// Methods
const fetchUser = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    if (!token) {
      router.push('/login')
      return
    }

    // Fetch user data
    const userResponse = await axios.get('/api/user/profile', {
      headers: { Authorization: `Bearer ${token}` }
    })
    user.value = userResponse.data.user

    // Populate edit form
    editForm.name = user.value.name || ''
    editForm.email = user.value.email || ''
    editForm.phone = user.value.phone || ''
    editForm.location = user.value.location || user.value.address || ''
    editForm.bio = user.value.bio || ''

    // Fetch user's stats and activity in parallel
    await Promise.all([
      fetchUserStats(),
      fetchRecentActivity()
    ])

  } catch (error) {
    console.error('Error fetching user:', error)
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      router.push('/login')
    } else {
      showGlobalMessage('Error loading profile data', 'error')
    }
  }
}

const fetchUserStats = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.get('/api/user/stats', {
      headers: { Authorization: `Bearer ${token}` }
    })
    userStats.value = response.data.stats || {}
  } catch (error) {
    console.error('Error fetching stats:', error)
    // Fallback to default stats
    userStats.value = {
      cvs_created: 0,
      total_downloads: 0,
      total_views: 0,
      achievements: 0
    }
  }
}

const fetchRecentActivity = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.get('/api/user/activity', {
      headers: { Authorization: `Bearer ${token}` }
    })
    recentActivity.value = response.data.activity || []
  } catch (error) {
    console.error('Error fetching activity:', error)
    recentActivity.value = []
  }
}

const toggleEditMode = () => {
  editMode.value = !editMode.value
  if (!editMode.value) {
    // Reset form when canceling
    editForm.name = user.value.name || ''
    editForm.email = user.value.email || ''
    editForm.phone = user.value.phone || ''
    editForm.location = user.value.location || user.value.address || ''
    editForm.bio = user.value.bio || ''
  }
}

const updateProfile = async () => {
  profileLoading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.put('/api/user/profile', editForm, {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    // Update user data with response
    user.value = { ...user.value, ...editForm }
    editMode.value = false
    
    showGlobalMessage('Profile updated successfully!', 'success')
    
  } catch (error) {
    console.error('Error updating profile:', error)
    const message = error.response?.data?.message || 'Error updating profile'
    showGlobalMessage(message, 'error')
  } finally {
    profileLoading.value = false
  }
}

const triggerAvatarUpload = () => {
  if (!avatarUploading.value) {
    avatarInput.value?.click()
  }
}

const handleAvatarUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Validate file size (2MB max)
  if (file.size > 2 * 1024 * 1024) {
    showGlobalMessage('Avatar file size must be less than 2MB', 'error')
    return
  }

  avatarUploading.value = true
  try {
    const formData = new FormData()
    formData.append('avatar', file)

    const token = localStorage.getItem('auth_token')
    const response = await axios.post('/api/user/avatar', formData, {
      headers: { 
        Authorization: `Bearer ${token}`,
        'Content-Type': 'multipart/form-data'
      }
    })

    user.value.avatar_url = response.data.avatar_url
    showGlobalMessage('Avatar updated successfully!', 'success')
  } catch (error) {
    console.error('Error uploading avatar:', error)
    const message = error.response?.data?.message || 'Error uploading avatar'
    showGlobalMessage(message, 'error')
  } finally {
    avatarUploading.value = false
    // Clear the input
    event.target.value = ''
  }
}

const updatePassword = async () => {
  // Validate password confirmation
  if (passwordForm.password !== passwordForm.password_confirmation) {
    passwordMessage.value = 'Passwords do not match'
    passwordMessageType.value = 'error'
    return
  }

  passwordLoading.value = true
  passwordMessage.value = ''
  
  try {
    const token = localStorage.getItem('auth_token')
    await axios.put('/api/user/password', passwordForm, {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    passwordMessage.value = 'Password updated successfully!'
    passwordMessageType.value = 'success'
    
    // Reset form
    passwordForm.current_password = ''
    passwordForm.password = ''
    passwordForm.password_confirmation = ''
    
  } catch (error) {
    passwordMessage.value = error.response?.data?.message || 'Error updating password'
    passwordMessageType.value = 'error'
  } finally {
    passwordLoading.value = false
    setTimeout(() => {
      passwordMessage.value = ''
    }, 5000)
  }
}

const downloadProfileData = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.get('/api/user/export', {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    const dataStr = JSON.stringify(response.data, null, 2)
    const dataBlob = new Blob([dataStr], { type: 'application/json' })
    const url = window.URL.createObjectURL(dataBlob)
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `profile-data-${new Date().toISOString().split('T')[0]}.json`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
    
    showGlobalMessage('Profile data exported successfully!', 'success')
  } catch (error) {
    console.error('Error downloading profile data:', error)
    showGlobalMessage('Error exporting profile data', 'error')
  } finally {
    loading.value = false
  }
}

const downloadAllCvs = async () => {
  if ((userStats.value.cvs_created || 0) === 0) {
    showGlobalMessage('No CVs available to download', 'error')
    return
  }

  loading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.get('/api/user/cvs/download-all', {
      headers: { Authorization: `Bearer ${token}` },
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    const fileName = `all-cvs-${new Date().toISOString().split('T')[0]}.zip`
    link.setAttribute('download', fileName)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
    
    showGlobalMessage('All CVs downloaded successfully!', 'success')
  } catch (error) {
    console.error('Error downloading CVs:', error)
    const message = error.response?.data?.message || 'Error downloading CVs'
    showGlobalMessage(message, 'error')
  } finally {
    loading.value = false
  }
}

const confirmDeleteAccount = async () => {
  try {
    const confirmed = await showModal({
      title: 'Delete Account',
      message: 'Are you absolutely sure you want to delete your account? This action cannot be undone and will permanently delete all your data, including CVs.',
      confirmButtonText: 'Delete Account',
      cancelButtonText: 'Cancel',
      confirmButtonClass: 'btn-danger'
    })

    if (confirmed) {
      await deleteAccount()
    }
  } catch (error) {
    console.error('Error in confirmDeleteAccount:', error)
    showGlobalMessage('Error showing confirmation dialog', 'error')
  }
}

// Modal and delete functionality

const deleteAccount = async () => {
  loading.value = true
  
  try {
    const token = localStorage.getItem('auth_token')
    
    if (!token) {
      throw new Error('No authentication token found')
    }

    const response = await axios.delete('/api/user', {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    // Clear authentication data
    localStorage.removeItem('auth_token')
    
    // Show success message and redirect
    showGlobalMessage('Account deleted successfully! Redirecting...', 'success')
    
    setTimeout(() => {
      router.push({ name: 'home', query: { message: 'Account deleted successfully', type: 'success' } })
    }, 2000)
    
  } catch (error) {
    console.error('Error deleting account:', error)
    
    let errorMessage = 'Failed to delete account'
    
    if (error.response?.status === 401) {
      errorMessage = 'Authentication failed. Please log in again.'
      localStorage.removeItem('auth_token')
      router.push('/login')
    } else if (error.response?.status === 403) {
      errorMessage = 'You do not have permission to delete this account'
    } else if (error.response?.status === 404) {
      errorMessage = 'Account not found'
    } else if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    } else if (error.message) {
      errorMessage = error.message
    }
    
    showGlobalMessage(errorMessage, 'error')
  } finally {
    loading.value = false
  }
}

const showGlobalMessage = (message, type) => {
  globalMessage.value = message
  globalMessageType.value = type
  setTimeout(() => {
    globalMessage.value = ''
  }, 5000)
}

const clearGlobalMessage = () => {
  globalMessage.value = ''
}

const calculateProfileProgress = () => {
  if (!user.value) return 0
  let progress = 0
  const fields = ['name', 'email', 'phone', 'location', 'bio']
  const filledFields = fields.filter(field => user.value[field] && user.value[field].trim())
  progress = (filledFields.length / fields.length) * 100
  return Math.round(progress)
}

const formatDate = (dateString, format = 'long') => {
  if (!dateString) return 'N/A'
  
  const date = new Date(dateString)
  
  if (format === 'short') {
    return date.getFullYear().toString()
  } else if (format === 'relative') {
    const now = new Date()
    const diff = now - date
    const minutes = Math.floor(diff / (1000 * 60))
    const hours = Math.floor(diff / (1000 * 60 * 60))
    const days = Math.floor(diff / (1000 * 60 * 60 * 24))
    
    if (minutes < 1) return 'Just now'
    if (minutes < 60) return `${minutes}m ago`
    if (hours < 24) return `${hours}h ago`
    if (days === 0) return 'Today'
    if (days === 1) return 'Yesterday'
    if (days < 7) return `${days}d ago`
    if (days < 30) return `${Math.floor(days / 7)}w ago`
    if (days < 365) return `${Math.floor(days / 30)}mo ago`
    return `${Math.floor(days / 365)}y ago`
  } else {
    const options = { year: 'numeric', month: 'long', day: 'numeric' }
    return date.toLocaleDateString(undefined, options)
  }
}

const formatTemplate = (template) => {
  const templates = {
    'modern': 'Modern',
    'classic': 'Classic',
    'professional': 'Professional',
    'creative': 'Creative'
  }
  return templates[template] || template
}

const formatFileSize = (bytes) => {
  if (!bytes) return 'N/A'
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  if (bytes === 0) return '0 Bytes'
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
}

const getActivityIconClass = (type) => {
  const iconClasses = {
    'cv_created': 'activity-icon-success',
    'cv_updated': 'activity-icon-info',
    'cv_deleted': 'activity-icon-danger',
    'cv_downloaded': 'activity-icon-primary',
    'cv_previewed': 'activity-icon-secondary',
    'profile_updated': 'activity-icon-info',
    'password_updated': 'activity-icon-warning',
    'avatar_updated': 'activity-icon-success',
    'account_created': 'activity-icon-success',
    'export_data': 'activity-icon-primary',
    'bulk_download': 'activity-icon-primary'
  }
  return iconClasses[type] || 'activity-icon-default'
}

// Utility methods

// Lifecycle
onMounted(fetchUser)
</script>

<style scoped>
/* Profile Wrapper with Animated Background */
.profile-wrapper {
  min-height: 100vh;
  background: var(--bg-base);
  position: relative;
  overflow: hidden;
  padding: var(--space-xl) 0;
}

/* Animated Background (same as homepage) */
.animated-bg {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
  overflow: hidden;
  pointer-events: none;
  opacity: 0.3;
}

.gradient-sphere {
  position: absolute;
  border-radius: 50%;
  filter: blur(120px);
  opacity: 0.6;
  animation: float 20s infinite ease-in-out;
}

.sphere-1 {
  width: 600px;
  height: 600px;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
  top: -300px;
  left: -300px;
  animation-delay: 0s;
}

.sphere-2 {
  width: 400px;
  height: 400px;
  background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
  bottom: -200px;
  right: -200px;
  animation-delay: 5s;
}

.sphere-3 {
  width: 300px;
  height: 300px;
  background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  animation-delay: 10s;
}

@keyframes float {
  0%, 100% { transform: translate(0, 0) scale(1); }
  25% { transform: translate(100px, -100px) scale(1.1); }
  50% { transform: translate(-100px, 100px) scale(0.9); }
  75% { transform: translate(50px, 50px) scale(1.05); }
}

/* Main Container */
.profile-container {
  position: relative;
  z-index: 2;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 var(--space-lg);
}

/* Enhanced Modern Cards */
.modern-card {
  background: rgba(255, 255, 255, 0.98);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: var(--radius-2xl);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  overflow: hidden;
}

.modern-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
}

.modern-card .card-header {
  background: linear-gradient(135deg, rgba(91, 33, 182, 0.05), rgba(124, 58, 237, 0.05));
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  padding: var(--space-xl);
}

.card-icon {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--gradient-primary);
  color: white;
  font-size: 1.2rem;
  margin-right: var(--space-md);
}

.security-icon {
  background: linear-gradient(135deg, #10B981, #059669);
}

.achievements-icon {
  background: linear-gradient(135deg, #F59E0B, #D97706);
}

.activity-icon {
  background: linear-gradient(135deg, #3B82F6, #2563EB);
}

.actions-icon {
  background: linear-gradient(135deg, #8B5CF6, #7C3AED);
}

.danger-icon {
  background: linear-gradient(135deg, #EF4444, #DC2626);
}

/* Enhanced Form Styles */
.enhanced-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: var(--space-lg);
}

.enhanced-input,
.enhanced-textarea {
  background: rgba(255, 255, 255, 0.8);
  border: 2px solid rgba(0, 0, 0, 0.1);
  border-radius: var(--radius-xl);
  padding: var(--space-md) var(--space-lg);
  font-size: var(--font-size-base);
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.enhanced-input:focus,
.enhanced-textarea:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
  background: rgba(255, 255, 255, 0.95);
}

.input-icon-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: var(--space-lg);
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
  font-size: 1.1rem;
  z-index: 1;
}

.input-icon-wrapper .enhanced-input {
  padding-left: calc(var(--space-lg) * 2.5);
}

.textarea-wrapper {
  position: relative;
}

.btn-enhanced {
  background: var(--gradient-primary);
  border: none;
  border-radius: var(--radius-xl);
  padding: var(--space-md) var(--space-xl);
  font-weight: 600;
  font-size: var(--font-size-base);
  transition: all 0.3s ease;
  box-shadow: 0 8px 25px rgba(124, 58, 237, 0.3);
}

.btn-enhanced:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(124, 58, 237, 0.4);
}

/* Enhanced Info Display */
.enhanced-display {
  padding: var(--space-lg);
}

.enhanced-info-grid {
  display: grid;
  gap: var(--space-lg);
}

.enhanced-info-grid .info-item {
  display: flex;
  align-items: flex-start;
  gap: var(--space-md);
  padding: var(--space-lg);
  background: rgba(255, 255, 255, 0.5);
  border-radius: var(--radius-lg);
  border: 1px solid rgba(0, 0, 0, 0.05);
  transition: all 0.2s ease;
}

.enhanced-info-grid .info-item:hover {
  background: rgba(255, 255, 255, 0.8);
  transform: translateX(5px);
}

.enhanced-info-grid .info-icon {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-md);
  background: var(--gradient-primary);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  flex-shrink: 0;
}

.enhanced-info-grid .info-content {
  flex: 1;
}

.enhanced-info-grid .info-label {
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: var(--text-secondary);
  margin-bottom: var(--space-xs);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.enhanced-info-grid .info-value {
  font-size: var(--font-size-base);
  color: var(--text-primary);
  font-weight: 500;
}

.bio-text {
  line-height: 1.6;
  color: var(--text-secondary);
}

/* Enhanced Stats */
.stat-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: var(--radius-xl);
  padding: var(--space-xl);
  text-align: center;
  transition: all 0.3s ease;
  animation: fadeInUp 0.6s ease-out both;
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--gradient-primary);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.stat-card:hover::before {
  opacity: 1;
}

.stat-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: var(--radius-lg);
  margin: 0 auto var(--space-md);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
  transition: all 0.3s ease;
}

.stat-icon.cvs-icon {
  background: linear-gradient(135deg, #3B82F6, #2563EB);
}

.stat-icon.downloads-icon {
  background: linear-gradient(135deg, #10B981, #059669);
}

.stat-icon.views-icon {
  background: linear-gradient(135deg, #F59E0B, #D97706);
}

.stat-icon.achievements-icon {
  background: linear-gradient(135deg, #8B5CF6, #7C3AED);
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 800;
  color: var(--text-primary);
  margin-bottom: var(--space-xs);
  background: var(--gradient-primary);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.stat-label {
  font-size: var(--font-size-base);
  font-weight: 600;
  color: var(--text-secondary);
  margin-bottom: var(--space-sm);
}

.stat-trend {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-xs);
  font-size: var(--font-size-sm);
  font-weight: 500;
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--radius-md);
  background: rgba(0, 0, 0, 0.05);
}

.stat-trend.positive {
  color: var(--success);
  background: rgba(16, 185, 129, 0.1);
}

.stat-trend.neutral {
  color: var(--text-muted);
  background: rgba(0, 0, 0, 0.05);
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Enhanced Achievements */
.enhanced-achievements {
  display: grid;
  gap: var(--space-md);
}

.enhanced-achievement {
  padding: var(--space-lg);
  background: rgba(255, 255, 255, 0.7);
  border: 2px solid rgba(0, 0, 0, 0.1);
  border-radius: var(--radius-lg);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.enhanced-achievement.unlocked {
  background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), rgba(91, 33, 182, 0.05));
  border-color: var(--primary);
  box-shadow: 0 8px 25px rgba(124, 58, 237, 0.15);
}

.enhanced-achievement:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.achievement-count {
  background: var(--gradient-primary);
  color: white;
  padding: var(--space-sm) var(--space-md);
  border-radius: var(--radius-lg);
  font-size: var(--font-size-sm);
  font-weight: 600;
}

/* Enhanced Activity Timeline */
.enhanced-timeline {
  position: relative;
  padding-left: var(--space-xl);
}

.enhanced-timeline::before {
  content: '';
  position: absolute;
  left: 20px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: linear-gradient(to bottom, var(--primary), transparent);
}

.enhanced-activity {
  position: relative;
  margin-bottom: var(--space-lg);
  background: rgba(255, 255, 255, 0.7);
  border-radius: var(--radius-lg);
  padding: var(--space-lg);
  margin-left: var(--space-lg);
  transition: all 0.3s ease;
  border-left: 4px solid transparent;
}

.enhanced-activity:hover {
  background: rgba(255, 255, 255, 0.9);
  transform: translateX(5px);
}

/* Activity type specific styling */
.activity-cv_created {
  border-left-color: var(--success);
}

.activity-cv_updated {
  border-left-color: var(--primary);
}

.activity-cv_deleted {
  border-left-color: var(--danger);
}

.activity-cv_downloaded,
.activity-bulk_download,
.activity-export_data {
  border-left-color: var(--info);
}

.activity-profile_updated,
.activity-avatar_updated {
  border-left-color: var(--primary);
}

.activity-password_updated {
  border-left-color: var(--warning);
}

.enhanced-activity .activity-icon {
  position: absolute;
  left: -45px;
  top: 20px;
  width: 40px;
  height: 40px;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  border: 3px solid white;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

/* Activity icon color classes */
.activity-icon-success {
  background: linear-gradient(135deg, var(--success), #059669);
}

.activity-icon-info {
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
}

.activity-icon-danger {
  background: linear-gradient(135deg, var(--danger), #DC2626);
}

.activity-icon-primary {
  background: linear-gradient(135deg, #3B82F6, #2563EB);
}

.activity-icon-secondary {
  background: linear-gradient(135deg, #6B7280, #4B5563);
}

.activity-icon-warning {
  background: linear-gradient(135deg, var(--warning), #D97706);
}

.activity-icon-default {
  background: linear-gradient(135deg, var(--gray-500), var(--gray-600));
}

.activity-content {
  flex: 1;
}

.activity-title {
  font-size: var(--font-size-base);
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: var(--space-xs);
}

.activity-description {
  font-size: var(--font-size-sm);
  color: var(--text-secondary);
  margin-bottom: var(--space-sm);
  line-height: 1.5;
}

.activity-metadata {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-sm);
  margin-bottom: var(--space-sm);
}

.metadata-item {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  background: rgba(0, 0, 0, 0.05);
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--radius-md);
  font-size: var(--font-size-xs);
  color: var(--text-muted);
}

.metadata-item i {
  font-size: 0.8rem;
  opacity: 0.7;
}

.activity-time {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  font-size: var(--font-size-xs);
  color: var(--text-muted);
  font-weight: 500;
}

.activity-time i {
  font-size: 0.7rem;
  opacity: 0.6;
}

/* Enhanced Quick Actions */
.enhanced-actions {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: var(--space-md);
}

.enhanced-action {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-sm);
  padding: var(--space-xl);
  background: rgba(255, 255, 255, 0.8);
  border: 2px solid rgba(0, 0, 0, 0.1);
  border-radius: var(--radius-xl);
  transition: all 0.3s ease;
  text-decoration: none;
  color: var(--text-primary);
  font-weight: 500;
}

.enhanced-action:hover {
  background: rgba(255, 255, 255, 0.95);
  border-color: var(--primary);
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  text-decoration: none;
  color: var(--primary);
}

.action-icon {
  width: 50px;
  height: 50px;
  border-radius: var(--radius-lg);
  background: var(--gradient-primary);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  transition: all 0.3s ease;
}

.enhanced-action:hover .action-icon {
  transform: scale(1.1);
  box-shadow: 0 8px 25px rgba(124, 58, 237, 0.3);
}

/* Enhanced Empty States */
.enhanced-empty {
  text-align: center;
  padding: var(--space-xxl);
  color: var(--text-muted);
}

.empty-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(0, 0, 0, 0.05), rgba(0, 0, 0, 0.1));
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto var(--space-lg);
  font-size: 2rem;
  color: var(--text-muted);
}

/* Enhanced Danger Zone */
.enhanced-danger {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-lg);
  padding: var(--space-xl);
  background: rgba(239, 68, 68, 0.05);
  border: 2px solid rgba(239, 68, 68, 0.1);
  border-radius: var(--radius-lg);
  transition: all 0.3s ease;
}

.enhanced-danger:hover {
  background: rgba(239, 68, 68, 0.08);
  border-color: rgba(239, 68, 68, 0.2);
}

/* Enhanced Loading */
.enhanced-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 500px;
  text-align: center;
}

.loading-spinner {
  margin-bottom: var(--space-lg);
}

.simple-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid rgba(124, 58, 237, 0.2);
  border-left: 4px solid var(--primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.loading-text {
  font-size: var(--font-size-lg);
  color: var(--text-secondary);
  margin: 0;
  font-weight: 500;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Enhanced Messages */
.enhanced-message {
  padding: var(--space-lg);
  border-radius: var(--radius-xl);
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: var(--space-md);
  margin-bottom: var(--space-lg);
  backdrop-filter: blur(10px);
  border: 2px solid transparent;
  transition: all 0.3s ease;
}

.enhanced-message.success {
  background: rgba(16, 185, 129, 0.1);
  border-color: rgba(16, 185, 129, 0.2);
  color: var(--success-dark);
}

.enhanced-message.error {
  background: rgba(239, 68, 68, 0.1);
  border-color: rgba(239, 68, 68, 0.2);
  color: var(--danger-dark);
}

/* Profile Header Section */
.profile-header-section {
  margin-bottom: var(--space-xxl);
}

.profile-hero-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: var(--radius-2xl);
  padding: var(--space-xxl);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: var(--shadow-xl);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-xl);
  animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.profile-hero-content {
  display: flex;
  align-items: center;
  gap: var(--space-xl);
}

.profile-avatar-section {
  display: flex;
  align-items: center;
  gap: var(--space-xl);
}

.profile-avatar-wrapper {
  position: relative;
}

.profile-avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: var(--gradient-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  transition: var(--transition-all);
  border: 4px solid white;
  box-shadow: var(--shadow-lg);
}

.profile-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

.profile-avatar i {
  font-size: 3rem;
  color: white;
}

.profile-avatar.uploading {
  opacity: 0.7;
}

.avatar-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: var(--transition-all);
}

.profile-avatar:hover .avatar-overlay {
  opacity: 1;
}

.avatar-input {
  display: none;
}

.avatar-upload-btn {
  background: none;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
  padding: var(--space-sm);
  border-radius: 50%;
  transition: var(--transition-all);
}

.avatar-upload-btn:hover {
  background: rgba(255, 255, 255, 0.2);
}

.profile-status-indicator {
  position: absolute;
  bottom: 8px;
  right: 8px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 3px solid white;
}

.profile-status-indicator.online {
  background: var(--success);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

.profile-info-section {
  flex: 1;
}

.profile-info {
  flex: 1;
}

.profile-name {
  font-size: var(--font-size-4xl);
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 var(--space-sm);
  font-family: var(--font-heading);
}

.profile-email {
  font-size: var(--font-size-lg);
  color: var(--text-secondary);
  margin: 0 0 var(--space-md);
}

.profile-badges {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-sm);
}

.badge {
  display: inline-flex;
  align-items: center;
  gap: var(--space-xs);
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--radius-full);
  font-size: var(--font-size-sm);
  font-weight: 600;
  border: 1px solid;
}

.badge.premium {
  background: linear-gradient(135deg, var(--accent), var(--accent-light));
  color: white;
  border-color: var(--accent);
}

.badge.verified {
  background: rgba(16, 185, 129, 0.1);
  color: var(--success-dark);
  border-color: var(--success);
}

.badge.member-since {
  background: rgba(59, 130, 246, 0.1);
  color: var(--info-dark);
  border-color: var(--info);
}

.profile-actions {
  display: flex;
  gap: var(--space-md);
  align-items: center;
}

/* Profile Statistics */
.profile-stats-section {
  margin-bottom: var(--space-xxl);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: var(--space-lg);
}

.stat-card {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border-radius: var(--radius-xl);
  padding: var(--space-xl);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: var(--shadow-md);
  display: flex;
  align-items: center;
  gap: var(--space-lg);
  transition: var(--transition-all);
  animation: fadeInUp 0.6s ease-out;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-xl);
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: var(--radius-lg);
  background: var(--gradient-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}

.stat-content {
  flex: 1;
}

.stat-number {
  font-size: var(--font-size-3xl);
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1;
  margin-bottom: var(--space-xs);
}

.stat-label {
  font-size: var(--font-size-sm);
  color: var(--text-secondary);
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

/* Main Grid Layout */
.profile-main-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: var(--space-xl);
}

/* Profile Cards */
.profile-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: var(--radius-xl);
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: var(--shadow-lg);
  margin-bottom: var(--space-xl);
  overflow: hidden;
  transition: var(--transition-all);
  animation: fadeInUp 0.6s ease-out;
  position: relative;
}

.profile-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: var(--gradient-primary);
  opacity: 0;
  transition: var(--transition-all);
}

.profile-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-xl);
  border-color: rgba(91, 33, 182, 0.2);
}

.profile-card:hover::before {
  opacity: 1;
}

.card-header {
  padding: var(--space-xl);
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.card-title-group {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
}

.card-title {
  font-size: var(--font-size-xl);
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
}

.card-title i {
  color: var(--primary);
}

.card-body {
  padding: var(--space-xl);
}

/* Form Styles */
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: var(--space-lg);
}

.form-grid .full-width {
  grid-column: 1 / -1;
}

.form-group {
  margin-bottom: var(--space-lg);
}

.form-label {
  display: block;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: var(--space-sm);
  font-size: var(--font-size-sm);
}

.input-icon-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: var(--space-md);
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
  pointer-events: none;
  transition: var(--transition-colors);
}

.input-icon-wrapper:focus-within .input-icon {
  color: var(--primary);
}

.form-input,
.form-textarea {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 2.75rem;
  border: 2px solid var(--border);
  border-radius: var(--radius-lg);
  font-size: var(--font-size-base);
  color: var(--text-primary);
  background: rgba(255, 255, 255, 0.8);
  transition: var(--transition-all);
  font-family: inherit;
}

.form-textarea {
  padding-left: 1rem;
  resize: vertical;
  min-height: 120px;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(91, 33, 182, 0.15), 0 4px 12px rgba(91, 33, 182, 0.1);
  background: white;
  transform: translateY(-1px);
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: var(--space-lg);
  gap: var(--space-md);
}

/* Profile Info Display */
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: var(--space-lg);
}

.info-grid .full-width {
  grid-column: 1 / -1;
}

.info-item {
  padding: var(--space-lg);
  background: rgba(0, 0, 0, 0.02);
  border-radius: var(--radius-lg);
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.info-icon {
  margin-bottom: var(--space-xs);
}

.info-content {
  flex: 1;
}

.info-label {
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: var(--space-xs);
}

.info-value {
  font-size: var(--font-size-base);
  color: var(--text-primary);
  font-weight: 500;
}

/* CV List */
.cv-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-md);
}

.cv-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--space-lg);
  background: rgba(0, 0, 0, 0.02);
  border-radius: var(--radius-lg);
  border: 1px solid rgba(0, 0, 0, 0.05);
  transition: var(--transition-all);
}

.cv-item:hover {
  background: rgba(91, 33, 182, 0.05);
  border-color: var(--primary);
}

.cv-info {
  flex: 1;
}

.cv-title {
  font-size: var(--font-size-lg);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 var(--space-xs);
}

.cv-meta {
  display: flex;
  gap: var(--space-md);
  font-size: var(--font-size-sm);
  color: var(--text-muted);
}

.cv-date,
.cv-stats {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
}

.cv-actions {
  display: flex;
  gap: var(--space-sm);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: var(--space-xxl);
  color: var(--text-muted);
}

.empty-state i {
  font-size: 4rem;
  margin-bottom: var(--space-lg);
  opacity: 0.5;
}

.empty-state h4 {
  font-size: var(--font-size-xl);
  margin: 0 0 var(--space-sm);
  color: var(--text-primary);
}

.empty-state p {
  margin: 0 0 var(--space-lg);
}

/* Achievements */
.achievements-grid {
  display: grid;
  gap: var(--space-lg);
}

.achievement-item {
  display: flex;
  align-items: center;
  gap: var(--space-lg);
  padding: var(--space-lg);
  background: rgba(0, 0, 0, 0.02);
  border-radius: var(--radius-lg);
  border: 1px solid rgba(0, 0, 0, 0.05);
  transition: var(--transition-all);
}

.achievement-item.unlocked {
  background: rgba(16, 185, 129, 0.1);
  border-color: var(--success);
}

.achievement-icon {
  width: 50px;
  height: 50px;
  border-radius: var(--radius-lg);
  background: var(--gradient-secondary);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
}

.achievement-item.unlocked .achievement-icon {
  background: var(--gradient-primary);
}

.achievement-info {
  flex: 1;
}

.achievement-title {
  font-size: var(--font-size-base);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 var(--space-xs);
}

.achievement-description {
  font-size: var(--font-size-sm);
  color: var(--text-secondary);
  margin: 0 0 var(--space-sm);
}

.achievement-progress {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
}

.progress-bar {
  flex: 1;
  height: 6px;
  background: rgba(0, 0, 0, 0.1);
  border-radius: var(--radius-full);
  overflow: hidden;
}

.progress-fill {
  width: var(--bar-width, 100%);
  background: var(--gradient-primary);
  border-radius: var(--radius-full);
  transition: width 0.3s ease;
}

.progress-text {
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: var(--text-primary);
  min-width: 40px;
}

/* Activity Timeline */
.activity-timeline {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
}

.activity-item {
  display: flex;
  gap: var(--space-md);
  padding: var(--space-lg);
  background: rgba(0, 0, 0, 0.02);
  border-radius: var(--radius-lg);
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.activity-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--gradient-accent);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1rem;
  flex-shrink: 0;
}

.activity-content {
  flex: 1;
}

.activity-title {
  font-size: var(--font-size-base);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 var(--space-xs);
}

.activity-description {
  font-size: var(--font-size-sm);
  color: var(--text-secondary);
  margin: 0 0 var(--space-xs);
}

.activity-time {
  font-size: var(--font-size-xs);
  color: var(--text-muted);
  font-weight: 500;
}

/* Quick Actions */
.quick-actions-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: var(--space-md);
}

.quick-action-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-sm);
  padding: var(--space-lg);
  background: rgba(0, 0, 0, 0.02);
  border-radius: var(--radius-lg);
  border: 1px solid rgba(0, 0, 0, 0.05);
  text-decoration: none;
  color: var(--text-primary);
  transition: var(--transition-all);
  cursor: pointer;
}

.quick-action-btn:hover {
  background: rgba(91, 33, 182, 0.1);
  border-color: var(--primary);
  transform: translateY(-2px);
}

.quick-action-btn i {
  font-size: 1.5rem;
  color: var(--primary);
}

.quick-action-btn span {
  font-size: var(--font-size-sm);
  font-weight: 600;
}

/* Danger Zone */
.danger-zone {
  border-color: rgba(239, 68, 68, 0.2) !important;
}

.danger-zone .card-title {
  color: var(--danger);
}

.danger-zone .card-title i {
  color: var(--danger);
}

.danger-actions {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
}

.danger-action {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-lg);
}

.danger-info h4 {
  font-size: var(--font-size-lg);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 var(--space-xs);
}

.danger-info p {
  font-size: var(--font-size-sm);
  color: var(--text-secondary);
  margin: 0;
}

/* Button Styles */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-sm);
  padding: 0.75rem 1.5rem;
  border: 2px solid transparent;
  border-radius: var(--radius-lg);
  font-size: var(--font-size-sm);
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: var(--transition-all);
  font-family: inherit;
  white-space: nowrap;
}

.btn-sm {
  padding: 0.5rem 1rem;
  font-size: var(--font-size-xs);
}

.btn-primary {
  background: var(--gradient-primary);
  color: white;
  box-shadow: var(--shadow-primary);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(91, 33, 182, 0.4);
  filter: brightness(1.1);
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.8);
  color: var(--text-primary);
  border-color: var(--border);
}

.btn-secondary:hover:not(:disabled) {
  background: white;
  border-color: var(--primary);
  color: var(--primary);
}

.btn-danger {
  background: var(--danger);
  color: white;
}

.btn-danger:hover:not(:disabled) {
  background: var(--danger-dark);
  transform: translateY(-2px);
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* Messages */
.message {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  padding: var(--space-md);
  border-radius: var(--radius-lg);
  margin-bottom: var(--space-lg);
  font-size: var(--font-size-sm);
  font-weight: 500;
}

.message.success {
  background: rgba(16, 185, 129, 0.1);
  color: var(--success-dark);
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.message.error {
  background: rgba(239, 68, 68, 0.1);
  color: var(--danger-dark);
  border: 1px solid rgba(239, 68, 68, 0.2);
}

/* Global Messages */
.global-message {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-md);
  padding: var(--space-lg);
  border-radius: var(--radius-xl);
  margin-bottom: var(--space-xl);
  font-size: var(--font-size-base);
  font-weight: 500;
  backdrop-filter: blur(10px);
  box-shadow: var(--shadow-lg);
  animation: slideDown 0.3s ease-out;
}

.global-message.success {
  background: rgba(16, 185, 129, 0.15);
  color: var(--success-dark);
  border: 1px solid rgba(16, 185, 129, 0.3);
}

.global-message.error {
  background: rgba(239, 68, 68, 0.15);
  color: var(--danger-dark);
  border: 1px solid rgba(239, 68, 68, 0.3);
}

.global-message .message-close {
  background: none;
  border: none;
  color: inherit;
  cursor: pointer;
  padding: var(--space-xs);
  border-radius: var(--radius-md);
  transition: var(--transition-all);
  opacity: 0.7;
}

.global-message .message-close:hover {
  background: rgba(0, 0, 0, 0.1);
  opacity: 1;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Loading State */
.loading-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 400px;
  text-align: center;
}

.loading-spinner {
  font-size: 3rem;
  color: var(--primary);
  margin-bottom: var(--space-lg);
}

.loading-text {
  font-size: var(--font-size-lg);
  color: var(--text-secondary);
  margin: 0;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .profile-main-grid {
    grid-template-columns: 1fr;
  }
  
  .profile-hero-card {
    flex-direction: column;
    text-align: center;
    gap: var(--space-lg);
  }
  
  .profile-avatar-section {
    flex-direction: column;
    text-align: center;
  }
  
  .profile-actions {
    justify-content: flex-end;
  }
}

@media (max-width: 768px) {
  .profile-container {
    padding: 0 var(--space-md);
  }
  
  .profile-hero-card {
    padding: var(--space-xl);
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
  }
  
  .quick-actions-grid {
    grid-template-columns: 1fr;
  }
  
  .danger-action {
    flex-direction: column;
    text-align: center;
  }
  
  .cv-item {
    flex-direction: column;
    gap: var(--space-md);
    text-align: center;
  }
  
  .gradient-sphere {
    filter: blur(80px);
  }
  
  .sphere-1 {
    width: 400px;
    height: 400px;
  }
  
  .sphere-2 {
    width: 300px;
    height: 300px;
  }
  
  .sphere-3 {
    width: 200px;
    height: 200px;
  }
}

@media (max-width: 480px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .profile-name {
    font-size: var(--font-size-2xl);
  }
  
  .profile-badges {
    justify-content: center;
  }
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
  .gradient-sphere,
  .profile-card,
  .stat-card {
    animation: none;
  }
  
  .btn:hover:not(:disabled),
  .profile-card:hover,
  .stat-card:hover {
    transform: none;
  }
}

/* Dark Theme Overrides */
body.dark-theme .profile-wrapper {
  background: var(--gray-900);
  color: var(--gray-100);
}

body.dark-theme .animated-bg .gradient-sphere {
  opacity: 0.3;
}

body.dark-theme .profile-hero-card {
  background: var(--gray-800);
  border: 1px solid var(--gray-700);
}

body.dark-theme .profile-name {
  color: var(--gray-100);
}

body.dark-theme .profile-email {
  color: var(--gray-400);
}

body.dark-theme .badge {
  background: var(--gray-700);
  color: var(--gray-300);
  border: 1px solid var(--gray-600);
}

body.dark-theme .badge.premium {
  background: var(--primary);
  color: var(--gray-100);
}

body.dark-theme .badge.verified {
  background: var(--success);
  color: var(--gray-100);
}

body.dark-theme .global-message {
  background: var(--gray-800);
  border: 1px solid var(--gray-700);
  color: var(--gray-200);
}

body.dark-theme .global-message.success {
  background: rgba(16, 185, 129, 0.1);
  border-color: var(--success);
  color: var(--success-light);
}

body.dark-theme .global-message.error {
  background: rgba(239, 68, 68, 0.1);
  border-color: var(--red-500);
  color: var(--red-400);
}

body.dark-theme .message-close {
  color: var(--gray-400);
}

body.dark-theme .message-close:hover {
  color: var(--gray-200);
}

body.dark-theme .stat-card {
  background: var(--gray-800);
  border: 1px solid var(--gray-700);
}

body.dark-theme .stat-number {
  color: var(--gray-100);
}

body.dark-theme .stat-label {
  color: var(--gray-400);
}

body.dark-theme .stat-icon {
  background: var(--primary);
  color: var(--gray-100);
}

body.dark-theme .profile-card {
  background: var(--gray-800);
  border: 1px solid var(--gray-700);
}

body.dark-theme .card-title {
  color: var(--gray-100);
}

body.dark-theme .form-label {
  color: var(--gray-300);
}

body.dark-theme .form-input,
body.dark-theme .form-textarea {
  background: var(--gray-700);
  border: 1px solid var(--gray-600);
  color: var(--gray-200);
}

body.dark-theme .form-input:focus,
body.dark-theme .form-textarea:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

body.dark-theme .form-input::placeholder,
body.dark-theme .form-textarea::placeholder {
  color: var(--gray-500);
}

body.dark-theme .input-icon {
  color: var(--gray-500);
}

body.dark-theme .btn {
  background: var(--primary);
  color: var(--gray-100);
  border-color: var(--primary);
}

body.dark-theme .btn:hover {
  background: var(--primary-light);
}

body.dark-theme .btn.btn-secondary {
  background: var(--gray-700);
  color: var(--gray-200);
  border-color: var(--gray-600);
}

body.dark-theme .btn.btn-secondary:hover {
  background: var(--gray-600);
}

body.dark-theme .btn.btn-danger {
  background: var(--red-600);
  color: var(--gray-100);
}

body.dark-theme .btn.btn-danger:hover {
  background: var(--red-500);
}

body.dark-theme .info-label {
  color: var(--gray-400);
}

body.dark-theme .info-value {
  color: var(--gray-200);
}

body.dark-theme .message {
  background: var(--gray-700);
  border: 1px solid var(--gray-600);
  color: var(--gray-200);
}

body.dark-theme .message.success {
  background: rgba(16, 185, 129, 0.1);
  border-color: var(--success);
  color: var(--success-light);
}

body.dark-theme .message.error {
  background: rgba(239, 68, 68, 0.1);
  border-color: var(--red-500);
  color: var(--red-400);
}

body.dark-theme .achievement-item {
  background: var(--gray-700);
  border: 1px solid var(--gray-600);
}

body.dark-theme .achievement-item.unlocked {
  background: var(--gray-600);
  border-color: var(--primary);
}

body.dark-theme .achievement-title {
  color: var(--gray-200);
}

body.dark-theme .achievement-description {
  color: var(--gray-400);
}

body.dark-theme .progress-bar {
  background: var(--gray-700);
}

body.dark-theme .progress-fill {
  background: var(--primary);
}

body.dark-theme .progress-text {
  color: var(--gray-300);
}

body.dark-theme .activity-item {
  border-color: var(--gray-700);
}

body.dark-theme .activity-title {
  color: var(--gray-200);
}

body.dark-theme .activity-description {
  color: var(--gray-400);
}

body.dark-theme .activity-time {
  color: var(--gray-500);
}

body.dark-theme .activity-icon {
  background: var(--gray-700);
  color: var(--gray-400);
}

body.dark-theme .quick-action-btn {
  background: var(--gray-700);
  color: var(--gray-300);
  border: 1px solid var(--gray-600);
  text-decoration: none;
}

body.dark-theme .quick-action-btn:hover {
  background: var(--gray-600);
  color: var(--gray-200);
  text-decoration: none;
}

body.dark-theme .empty-state {
  color: var(--gray-400);
}

body.dark-theme .empty-state h4 {
  color: var(--gray-300);
}

body.dark-theme .danger-zone {
  background: var(--gray-800);
  border: 1px solid var(--red-800);
}

body.dark-theme .danger-info h4 {
  color: var(--red-400);
}

body.dark-theme .danger-info p {
  color: var(--gray-400);
}

body.dark-theme .loading-wrapper {
  background: var(--gray-900);
  color: var(--gray-300);
}

body.dark-theme .loading-text {
  color: var(--gray-400);
}

body.dark-theme .profile-avatar {
  border: 3px solid var(--gray-600);
}

body.dark-theme .avatar-upload-btn {
  background: var(--gray-700);
  color: var(--gray-300);
}

body.dark-theme .avatar-upload-btn:hover {
  background: var(--gray-600);
}

body.dark-theme .profile-status-indicator {
  border: 2px solid var(--gray-800);
}

body.dark-theme .profile-status-indicator.online {
  background: var(--success);
}

/* Responsive adjustments for dark theme */
@media (max-width: 768px) {
  body.dark-theme .profile-name {
    color: var(--gray-100);
  }
  
  body.dark-theme .profile-email {
    color: var(--gray-400);
  }
}

.profile-avatar-img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 50%;
  display: block;
}

.animated-item {
  animation-delay: var(--delay, 0s);
}

.flex-row-align {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-lg);
  flex-wrap: wrap;
}
.profile-info {
  min-width: 0;
}
.profile-actions {
  display: flex;
  gap: var(--space-md);
  align-items: center;
}
@media (max-width: 768px) {
  .flex-row-align {
    flex-direction: column;
    align-items: flex-start;
    gap: var(--space-md);
  }
  .profile-actions {
    width: 100%;
    justify-content: flex-start;
  }
}
</style>

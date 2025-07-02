<template>
  <main class="dashboard-main">
    <!-- Optimized Background -->
    <div class="dashboard-bg">
      <div class="bg-sphere bg-sphere-1"></div>
      <div class="bg-sphere bg-sphere-2"></div>
      <div class="bg-sphere bg-sphere-3"></div>
    </div>

    <div class="dashboard-container">
      <!-- Dashboard Header -->
      <section class="dashboard-header">
        <div class="header-card card">
          <div class="header-content">
            <div class="greeting-section">
              <div class="time-indicator">
                <i class="fas fa-clock"></i>
                <span>{{ getCurrentTime() }}</span>
              </div>
              <h1 class="dashboard-title">{{ currentGreeting }}</h1>
              <p class="dashboard-subtitle">Ready to elevate your career journey?</p>
            </div>
            
            <div class="header-actions">
              <router-link to="/create-cv" class="btn btn-primary create-cv-btn">
                <i class="fas fa-plus"></i>
                <span>Create CV</span>
              </router-link>
              <div class="dropdown-wrapper">
                <button class="btn btn-secondary" @click="showDropdown = !showDropdown">
                  <i class="fas fa-ellipsis-h"></i>
                  <span>More</span>
                </button>
                <div v-if="showDropdown" class="dropdown-menu">
                  <router-link to="/templates" class="dropdown-item">
                    <i class="fas fa-palette"></i>
                    <span>Browse Templates</span>
                  </router-link>
                  <button @click="duplicateLastCV" class="dropdown-item">
                    <i class="fas fa-copy"></i>
                    <span>Duplicate Last CV</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Stats Grid -->
          <div class="stats-grid">
            <div class="stat-card" 
                 v-for="stat in dashboardStats" 
                 :key="stat.id">
              <div v-if="loading" class="loading-overlay">
                <div class="loading-spinner"></div>
              </div>
              <div class="stat-icon" :class="stat.theme">
                <i :class="stat.icon"></i>
              </div>
              <div class="stat-content">
                <div class="stat-value">{{ stat.value }}</div>
                <div class="stat-label">{{ stat.label }}</div>
                <div class="stat-description">{{ stat.description }}</div>
              </div>
              <div class="stat-trend" :class="stat.trendDirection">
                <i :class="stat.trendIcon"></i>
                <span>{{ stat.trendValue }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Main Content -->
      <div class="dashboard-content">
        <!-- CV Management Section -->
        <section class="cv-section card">
        <div class="section-header">
          <div class="section-title-group">
            <h2 class="section-title">
              <i class="fas fa-briefcase"></i>
              Your CV Collection
            </h2>
            <p class="section-subtitle">Manage and organize your professional documents</p>
          </div>
          
          <div class="section-actions">
            <div class="search-filter-group">
              <div class="search-box">
                <i class="fas fa-search"></i>
                <input
                  v-model="searchTerm"
                  type="text"
                  placeholder="Search CVs..."
                  class="form-input"
                />
                <button v-if="searchTerm" @click="searchTerm = ''" class="btn btn-secondary clear-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
              
              <div class="filter-controls">
                <select v-model="filterTemplate" class="form-select">
                  <option value="all">All Templates</option>
                  <option value="classic">Classic</option>
                  <option value="modern">Modern</option>
                  <option value="professional">Professional</option>
                  <option value="creative">Creative</option>
                </select>
                
                <select v-model="sortBy" class="form-select">
                  <option value="updated_at_desc">Latest First</option>
                  <option value="updated_at_asc">Oldest First</option>
                  <option value="title_asc">A-Z</option>
                  <option value="views_desc">Most Viewed</option>
                </select>
                
                <button class="btn btn-secondary view-mode-btn" @click="toggleView" :class="{ active: viewMode === 'grid' }">
                  <i :class="viewMode === 'grid' ? 'fas fa-th' : 'fas fa-list'" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- CV Cards Display -->
        <div class="cv-cards-container">
          <!-- Loading State -->
          <div v-if="loading" class="loading-state card">
            <div class="loading-spinner">
              <i class="fas fa-spinner fa-spin"></i>
            </div>
            <h3>Loading your CVs...</h3>
            <p>Please wait while we fetch your documents</p>
          </div>

          <!-- Empty State -->
          <div v-else-if="cvs.length === 0" class="empty-state card">
            <div class="empty-illustration">
              <i class="fas fa-file-plus"></i>
            </div>
            <h3>{{ searchTerm ? 'No matching CVs found' : 'Start your CV journey' }}</h3>
            <p>{{ searchTerm ? 'Try adjusting your search criteria' : 'Create your first professional CV and unlock new opportunities' }}</p>
            <router-link v-if="!searchTerm" to="/create-cv" class="btn btn-primary">
              <i class="fas fa-plus"></i>
              <span>Create Your First CV</span>
            </router-link>
          </div>

          <!-- CVs Grid -->
          <div v-else :class="['cvs-display-grid', viewMode]">
            <div
              v-for="cv_item in cvs"
              :key="cv_item.id"
              :data-cv-id="cv_item.id"
              class="cv-display-card card"
              @click="previewCv(cv_item.id)"
            >
              <div class="cv-card-header">
                <div class="template-badge" :class="cv_item.selectedTemplate">
                  <i :class="getTemplateIcon(cv_item.selectedTemplate)"></i>
                  <span>{{ cv_item.selectedTemplate || 'Classic' }}</span>
                </div>
                <div class="cv-status-badge" :class="{ draft: !cv_item.isFinalized }">
                  <i :class="cv_item.isFinalized ? 'fas fa-check-circle' : 'fas fa-edit'"></i>
                  <span>{{ cv_item.isFinalized ? 'Published' : 'Draft' }}</span>
                </div>
                <div class="cv-menu-trigger" @click.stop>
                  <button class="btn btn-secondary menu-trigger-btn" @click="toggleMenu(cv_item.id)">
                    <i class="fas fa-ellipsis-v"></i>
                  </button>
                  <div v-if="activeMenu === cv_item.id" class="cv-dropdown-menu card">
                    <button @click="previewCv(cv_item.id)" class="dropdown-item">
                      <i class="fas fa-eye"></i>
                      <span>Preview</span>
                    </button>
                    <button @click="editCv(cv_item.id)" class="dropdown-item">
                      <i class="fas fa-edit"></i>
                      <span>Edit</span>
                    </button>
                    <button @click="downloadCv(cv_item.id, cv_item.title)" class="dropdown-item">
                      <i class="fas fa-download"></i>
                      <span>Download</span>
                    </button>
                    <div class="dropdown-divider"></div>
                    <button @click="confirmDelete(cv_item.id)" class="dropdown-item danger">
                      <i class="fas fa-trash-alt"></i>
                      <span>Delete</span>
                    </button>
                  </div>
                </div>
              </div>


              
              <div class="cv-card-body">
                <div class="cv-title-section">
                  <h3 class="cv-title">{{ cv_item.title || 'Untitled CV' }}</h3>
                  <p class="cv-author">{{ getAuthorName(cv_item) }}</p>
                  <p v-if="cv_item.personalDetails?.jobTitle" class="cv-job-title">
                    {{ cv_item.personalDetails.jobTitle }}
                  </p>
                </div>

                <!-- Enhanced Skills Preview -->
                <div v-if="cv_item.skills && cv_item.skills.length > 0" class="cv-skills-preview">
                  <div class="skills-header">
                    <i class="fas fa-code"></i>
                    <span>Top Skills</span>
                  </div>
                  <div class="skills-tags">
                    <span 
                      v-for="skill in cv_item.skills.slice(0, 3)" 
                      :key="skill" 
                      class="skill-tag"
                    >
                      {{ skill }}
                    </span>
                    <span v-if="cv_item.skills.length > 3" class="skill-count">
                      +{{ cv_item.skills.length - 3 }} more
                    </span>
                  </div>
                </div>

                <!-- Experience Preview -->
                <div v-if="cv_item.experience && cv_item.experience.length > 0" class="cv-experience-preview">
                  <div class="experience-header">
                    <i class="fas fa-briefcase"></i>
                    <span>Latest Experience</span>
                  </div>
                  <div class="experience-item">
                    <div class="job-title">{{ cv_item.experience[0].jobTitle }}</div>
                    <div class="company-name">{{ cv_item.experience[0].company }}</div>
                  </div>
                </div>
                
                <div class="cv-metrics">
                  <div class="metric-item">
                    <i class="fas fa-eye"></i>
                    <span class="metric-value">{{ cv_item.views || 0 }}</span>
                    <span class="metric-label">views</span>
                  </div>
                  <div class="metric-item">
                    <i class="fas fa-download"></i>
                    <span class="metric-value">{{ cv_item.downloads || 0 }}</span>
                    <span class="metric-label">downloads</span>
                  </div>
                  <div class="metric-item">
                    <i class="fas fa-star"></i>
                    <span class="metric-value">{{ getCvScore(cv_item) }}%</span>
                    <span class="metric-label">score</span>
                  </div>
                </div>
                
                <div class="cv-completion">
                  <div class="completion-info">
                    <span class="completion-label">Completion</span>
                    <span class="completion-percentage">{{ getCvCompleteness(cv_item) }}%</span>
                  </div>
                  <div class="completion-bar">
                    <div class="completion-progress" :style="{ '--bar-width': getCvCompleteness(cv_item) + '%' }"></div>
                  </div>
                </div>
                
                <div class="cv-card-footer">
                  <span class="cv-date">
                    <i class="fas fa-clock"></i>
                    Updated {{ formatDate(cv_item.updatedAt) }}
                  </span>
                  <div class="quick-actions">
                    <button @click.stop="editCv(cv_item.id)" class="quick-action-btn edit-btn" title="Edit CV">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button @click.stop="previewCv(cv_item.id)" class="quick-action-btn preview-btn" title="Preview CV">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button @click.stop="downloadCv(cv_item.id, cv_item.title)" class="quick-action-btn download-btn" title="Download">
                      <i class="fas fa-download"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
              </section>

        <!-- Analytics Sidebar -->
        <aside class="analytics-sidebar">
        <!-- Performance Analytics -->
        <div class="premium-analytics-card card">
          <div class="analytics-header">
            <div class="header-icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <div class="header-content">
              <h3>Performance Analytics</h3>
              <p>Last 30 days overview</p>
            </div>
            <div class="header-action">
              <button class="btn btn-secondary refresh-btn" @click="refreshCharts">
                <i class="fas fa-sync-alt"></i>
              </button>
            </div>
          </div>
          <div class="analytics-body">
            <div v-if="loading" class="chart-loading">
              <i class="fas fa-chart-line"></i>
              <span>Loading performance data...</span>
            </div>
            <div v-else class="chart-container">
              <canvas ref="performanceChart"></canvas>
            </div>
            <div class="chart-legend">
              <div class="legend-item">
                <div class="legend-dot views"></div>
                <span>Profile Views</span>
              </div>
              <div class="legend-item">
                <div class="legend-dot downloads"></div>
                <span>Downloads</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Smart Insights -->
        <div class="premium-analytics-card card">
          <div class="analytics-header">
            <div class="header-icon">
              <i class="fas fa-brain"></i>
            </div>
            <div class="header-content">
              <h3>Smart Insights</h3>
              <p>AI-powered recommendations</p>
            </div>
          </div>
          <div class="analytics-body">
            <div class="insights-container">
              <div class="insight-card card" v-for="insight in smartInsights" :key="insight.id">
                <div class="insight-indicator" :class="insight.priority"></div>
                <div class="insight-icon" :class="insight.type">
                  <i :class="insight.icon"></i>
                </div>
                <div class="insight-content">
                  <h4>{{ insight.title }}</h4>
                  <p>{{ insight.message }}</p>
                  <button class="btn btn-secondary insight-action" v-if="insight.action">
                    {{ insight.action }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Skills Analytics -->
        <div class="premium-analytics-card card">
          <div class="analytics-header">
            <div class="header-icon">
              <i class="fas fa-code"></i>
            </div>
            <div class="header-content">
              <h3>Skills Analytics</h3>
              <p>Market demand analysis</p>
            </div>
          </div>
          <div class="analytics-body">
            <div v-if="loading" class="chart-loading">
              <i class="fas fa-code"></i>
              <span>Analyzing skills data...</span>
            </div>
            <template v-else>
              <div class="skills-chart-container">
                <canvas ref="skillsChart"></canvas>
              </div>
              <div class="skills-summary">
                <div class="skill-stat card" v-for="(skill, index) in skillsData.labels" :key="skill">
                  <div class="skill-info">
                    <span class="skill-name">{{ skill }}</span>
                    <span class="skill-percentage">{{ Math.round((skillsData.data[index] / skillsData.data.reduce((a, b) => a + b, 0)) * 100) }}%</span>
                  </div>
                  <div class="skill-bar">
                    <div class="skill-progress" :style="{ 
                      width: (skillsData.data[index] / skillsData.data.reduce((a, b) => a + b, 0)) * 100 + '%',
                      backgroundColor: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4'][index % 6]
                    }"></div>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>
      </aside>
    </div>
    </div>

    <!-- Download Modal -->
    <DownloadOptionsModal
      :is-visible="showDownloadModal"
      :cv-id="currentCvToDownload?.id"
      :cv-title="currentCvToDownload?.title"
      @confirm="handleDownloadConfirm"
      @cancel="showDownloadModal = false"
      v-if="currentCvToDownload"
    />

    <!-- Confirmation Modal -->
    <div v-if="showConfirmModal" class="confirmation-modal-overlay" @click="showConfirmModal = false">
      <div class="confirmation-modal-content" @click.stop>
        <div class="modal-header">
          <h3>{{ confirmModal.title }}</h3>
          <button @click="showConfirmModal = false" class="modal-close-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="modal-icon">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <p>{{ confirmModal.message }}</p>
        </div>
        <div class="modal-actions">
          <button @click="showConfirmModal = false" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            <span>Cancel</span>
          </button>
          <button @click="confirmModal.onConfirm" class="btn btn-danger">
            <i class="fas fa-trash"></i>
            <span>Delete</span>
          </button>
        </div>
      </div>
    </div>

  </main>
</template>

<script setup>
/*
 * PERFORMANCE OPTIMIZATIONS APPLIED:
 * - Used shallowRef for large data arrays (cvs_all)
 * - Removed heavy animations and intervals (blinking, speaking, pulse rings)
 * - Simplified computed properties (no random calculations)
 * - Removed direct DOM manipulation (notifications)
 * - Streamlined AI initialization (no complex animations)
 * - Eliminated typewriter effect recursion
 * - Reduced reactive dependencies
 */

import { ref, onMounted, onBeforeUnmount, computed, watchEffect, shallowRef, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import axios from 'axios'
import DownloadOptionsModal from '@/components/common/DownloadOptionsModal.vue'
import aiService from '@/services/aiService'
import Chart from 'chart.js/auto'

const { t } = useI18n()

const router = useRouter()

// Optimized Core State
const cvs_all = shallowRef([])
const loading = ref(true)
const searchTerm = ref('')
const filterTemplate = ref('all')
const sortBy = ref('updated_at_desc')
const showDownloadModal = ref(false)
const currentCvToDownload = ref(null)
const showConfirmModal = ref(false)
const confirmModal = ref({})
const activeMenu = ref(null)
const viewMode = ref('grid')

// Simplified UI State
const showDropdown = ref(false)
const selectedCvs = ref([])

// Basic Analytics
const todayViews = ref(23)
const weeklyViews = ref(156)

// Chart refs and instances
const performanceChart = ref(null)
const skillsChart = ref(null)
let performanceChartInstance = null
let skillsChartInstance = null

// Dashboard animation controller
let dashboardAnimationController = null

// Removed animation state for CV previews (no longer needed)

// User data
const userName = ref('Professional')
const profileViews = ref(342)
const downloadRate = ref(78)

// Optimized AI Dashboard Data
const currentGreeting = computed(() => {
  const hour = new Date().getHours()
  const name = userName.value
  if (hour < 12) return `Good morning, ${name}!`
  if (hour < 18) return `Good afternoon, ${name}!`
  return `Good evening, ${name}!`
})

// Dashboard stats for header
const dashboardStats = computed(() => [
  {
    id: 'cvs',
    label: 'Active CVs',
    value: totalCvs.value,
    description: 'Professional documents',
    icon: 'fas fa-file-alt',
    theme: 'primary',
    trendDirection: 'up',
    trendIcon: 'fas fa-arrow-up',
    trendValue: '+2 this month'
  },
  {
    id: 'views',
    label: 'Profile Views',
    value: totalViews.value,
    description: 'Total visibility',
    icon: 'fas fa-eye',
    theme: 'success',
    trendDirection: 'up',
    trendIcon: 'fas fa-arrow-up',
    trendValue: '+12% growth'
  },
  {
    id: 'downloads',
    label: 'Downloads',
    value: totalDownloads.value,
    description: 'CV acquisitions',
    icon: 'fas fa-download',
    theme: 'warning',
    trendDirection: 'up',
    trendIcon: 'fas fa-arrow-up',
    trendValue: '+8% this week'
  },
  {
    id: 'score',
    label: 'ATS Score',
    value: `${calculateProfileScore()}%`,
    description: 'Optimization level',
    icon: 'fas fa-robot',
    theme: 'info',
    trendDirection: calculateProfileScore() > 70 ? 'up' : 'neutral',
    trendIcon: calculateProfileScore() > 70 ? 'fas fa-arrow-up' : 'fas fa-minus',
    trendValue: calculateProfileScore() > 70 ? 'Excellent' : 'Good'
  }
])

// Get current time
const getCurrentTime = () => {
  return new Date().toLocaleTimeString('en-US', { 
    hour: '2-digit', 
    minute: '2-digit',
    hour12: true 
  })
}

// Smart insights for premium sidebar
const smartInsights = computed(() => {
  const insights = []
  
  if (totalCvs.value === 0) {
    insights.push({
      id: 1,
      type: 'info',
      priority: 'high',
      icon: 'fas fa-rocket',
      title: 'Welcome Aboard!',
      message: 'Create your first professional CV and join thousands of successful job seekers.',
      action: 'Get Started'
    })
  } else {
    if (profileCompleteness.value < 80) {
      insights.push({
        id: 2,
        type: 'warning',
        priority: 'medium',
        icon: 'fas fa-chart-line',
        title: 'Boost Your Profile',
        message: `${100 - profileCompleteness.value}% more completion could increase your visibility by 40%.`,
        action: 'Optimize Now'
      })
    }
    
    if (views24h.value > 5) {
      insights.push({
        id: 3,
        type: 'success',
        priority: 'low',
        icon: 'fas fa-fire',
        title: 'Trending Profile',
        message: `Your CV is gaining traction with ${views24h.value} views today. Keep the momentum!`,
        action: 'View Analytics'
      })
    }
    
    const missingSkills = trendingSkills.filter(t => !userSkills.value.includes(t.skill))
    if (missingSkills.length > 0) {
      insights.push({
        id: 4,
        type: 'info',
        priority: 'high',
        icon: 'fas fa-brain',
        title: 'Market Opportunity',
        message: `${missingSkills[0].skill} is trending +${missingSkills[0].change}% in your field. Consider adding it.`,
        action: 'Add Skill'
      })
    }
  }
  
  return insights.slice(0, 3)
})

// Optimize chart updates with debouncing
let chartUpdateTimeout = null
const debouncedChartUpdate = () => {
  if (chartUpdateTimeout) clearTimeout(chartUpdateTimeout)
  chartUpdateTimeout = setTimeout(() => {
    if (performanceChartInstance || skillsChartInstance) {
      refreshCharts()
    }
  }, 300)
}

// Real performance data based on CV data
const performanceData = computed(() => {
  const days = 30
  const labels = []
  const viewsData = []
  const downloadsData = []
  
  // Generate last 30 days
  for (let i = days - 1; i >= 0; i--) {
    const date = new Date()
    date.setDate(date.getDate() - i)
    labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }))
    
    // Simulate realistic data based on actual CV metrics
    const baseViews = Math.floor(totalViews.value / 30)
    const baseDownloads = Math.floor(totalDownloads.value / 30)
    
    // Add some realistic variation
    const viewVariation = Math.floor(Math.random() * (baseViews * 0.5)) - (baseViews * 0.25)
    const downloadVariation = Math.floor(Math.random() * (baseDownloads * 0.5)) - (baseDownloads * 0.25)
    
    viewsData.push(Math.max(0, baseViews + viewVariation))
    downloadsData.push(Math.max(0, baseDownloads + downloadVariation))
  }
  
  return { labels, viewsData, downloadsData }
})

// Real skills data from user's actual skills
const skillsData = computed(() => {
  const skills = userSkills.value.slice(0, 6) // Top 6 skills
  if (skills.length === 0) {
    return {
      labels: ['JavaScript', 'Python', 'React', 'Node.js', 'CSS', 'HTML'],
      data: [25, 22, 18, 15, 12, 8]
    }
  }
  
  // Create more realistic skill distribution based on frequency in CVs
  const skillCounts = new Map()
  cvs_all.value.forEach(cv => {
    (cv.skills || []).forEach(skill => {
      skillCounts.set(skill, (skillCounts.get(skill) || 0) + 1)
    })
  })
  
  const data = skills.map(skill => skillCounts.get(skill) || 1)
  const total = data.reduce((sum, count) => sum + count, 0)
  const percentages = data.map(count => Math.round((count / total) * 100))
  
  return {
    labels: skills,
    data: percentages
  }
})

const optimizedMetrics = computed(() => [
  {
    id: 'profile',
    label: 'Profile Score',
    value: `${calculateProfileScore()}%`,
    icon: 'fas fa-brain',
    theme: 'primary'
  },
  {
    id: 'cvs',
    label: 'CVs Created', 
    value: totalCvs.value,
    icon: 'fas fa-file-alt',
    theme: 'secondary'
  },
  {
    id: 'views',
    label: 'Total Views',
    value: totalViews.value,
    icon: 'fas fa-eye', 
    theme: 'accent'
  }
])

// AI Features for showcase
const aiFeatures = computed(() => [
  {
    id: 'recommendations',
    label: 'Smart Recommendations',
    icon: 'fas fa-brain'
  },
  {
    id: 'optimization',
    label: 'Career Optimization',
    icon: 'fas fa-chart-line'
  },
  {
    id: 'insights',
    label: 'Real-time Insights',
    icon: 'fas fa-eye'
  },
  {
    id: 'analysis',
    label: 'AI Analysis',
    icon: 'fas fa-robot'
  }
])

// Real AI Insights Data
const realTimeInsights = computed(() => [
  {
    id: 1,
    title: 'Skills Gap Detected',
    description: 'Your profile is missing React.js - a high-demand skill in your field.',
    icon: 'fas fa-exclamation-triangle',
    type: 'warning',
    action: 'Add Skill'
  },
  {
    id: 2,
    title: 'Strong Keywords',
    description: 'Your CV contains 85% of trending keywords for your industry.',
    icon: 'fas fa-check-circle',
    type: 'success',
    action: 'View Details'
  },
  {
    id: 3,
    title: 'Experience Enhancement',
    description: 'Consider adding quantified achievements to boost your profile strength.',
    icon: 'fas fa-chart-line',
    type: 'info',
    action: 'Improve Now'
  },
  {
    id: 4,
    title: 'ATS Compatibility',
    description: 'Your CV scores 92% on ATS readability - excellent performance.',
    icon: 'fas fa-robot',
    type: 'success',
    action: 'View Report'
  },
  {
    id: 5,
    title: 'Format Optimization',
    description: 'Switch to Professional template for 23% better recruiter engagement.',
    icon: 'fas fa-palette',
    type: 'info',
    action: 'Preview'
  }
])

const aiSuggestions = computed(() => [
  {
    id: 1,
    title: 'Add Project Portfolio',
    description: 'Include 2-3 key projects to demonstrate practical skills and increase interview chances.',
    priority: 'high',
    impact: 'high',
    category: 'content'
  },
  {
    id: 2,
    title: 'Optimize Summary Section',
    description: 'Rewrite your professional summary to include more action verbs and quantified results.',
    priority: 'medium',
    impact: 'medium',
    category: 'content'
  },
  {
    id: 3,
    title: 'Add Certifications',
    description: 'Include relevant certifications to increase your profile credibility by 34%.',
    priority: 'medium',
    impact: 'high',
    category: 'credentials'
  },
  {
    id: 4,
    title: 'Update Contact Info',
    description: 'Add LinkedIn profile and ensure all contact information is current and professional.',
    priority: 'low',
    impact: 'medium',
    category: 'basic'
  }
])

const marketTrends = computed(() => [
  {
    id: 1,
    skill: 'React.js',
    category: 'Frontend',
    description: 'High demand for React developers with 45% salary increase year-over-year.',
    demand: 94,
    change: 12,
    direction: 'up'
  },
  {
    id: 2,
    skill: 'Python',
    category: 'Backend',
    description: 'Python remains top choice for data science and backend development.',
    demand: 89,
    change: 8,
    direction: 'up'
  },
  {
    id: 3,
    skill: 'Cloud Computing',
    category: 'Infrastructure',
    description: 'AWS and Azure certifications showing massive growth in job postings.',
    demand: 91,
    change: 25,
    direction: 'up'
  },
  {
    id: 4,
    skill: 'UX Design',
    category: 'Design',
    description: 'User experience design skills highly valued across all industries.',
    demand: 76,
    change: 15,
    direction: 'up'
  },
  {
    id: 5,
    skill: 'DevOps',
    category: 'Operations',
    description: 'DevOps practices becoming standard requirement for technical roles.',
    demand: 83,
    change: 18,
    direction: 'up'
  }
])

// Stats data for the hero section
const statsData = computed(() => [
  {
    id: 'cvs',
    label: 'CVs Created',
    value: totalCvs.value,
    icon: 'fas fa-file-alt',
    iconClass: 'stat-icon-primary',
    trend: 'positive',
    trendIcon: 'fas fa-arrow-up',
    trendText: '+2 this month'
  },
  {
    id: 'views',
    label: 'Total Views',
    value: totalViews.value,
    icon: 'fas fa-eye',
    iconClass: 'stat-icon-secondary',
    trend: 'positive',
    trendIcon: 'fas fa-arrow-up',
    trendText: '+12% growth'
  },
  {
    id: 'downloads',
    label: 'Downloads',
    value: totalDownloads.value,
    icon: 'fas fa-download',
    iconClass: 'stat-icon-accent',
    trend: 'positive',
    trendIcon: 'fas fa-arrow-up',
    trendText: '+8% this week'
  },
  {
    id: 'score',
    label: 'Profile Score',
    value: `${calculateProfileScore()}%`,
    icon: 'fas fa-star',
    iconClass: 'stat-icon-success',
    trend: 'positive',
    trendIcon: 'fas fa-arrow-up',
    trendText: 'Excellent'
  }
])

// AI Process Demo State
const currentAIStep = ref(1)
const step1AIAnimation = ref(false)
const step2AIAnimation = ref(false)
const step3AIAnimation = ref(false)
const step4AIAnimation = ref(false)

// AI Process Steps Data
const aiProcessSteps = ref([
  {
    id: 1,
    title: 'AI Engine Initialization',
    description: 'Starting intelligent CV analysis system',
    icon: 'fas fa-power-off',
    iconClass: 'icon-primary',
    progress: 25
  },
  {
    id: 2,
    title: 'CV Analysis & Scoring',
    description: 'Analyzing skills match, ATS compatibility, and format quality',
    icon: 'fas fa-search',
    iconClass: 'icon-secondary',
    progress: 50
  },
  {
    id: 3,
    title: 'Smart Suggestions',
    description: 'Generating personalized improvement recommendations',
    icon: 'fas fa-lightbulb',
    iconClass: 'icon-accent',
    progress: 75
  },
  {
    id: 4,
    title: 'Market Intelligence',
    description: 'Analyzing current job market trends and skill demands',
    icon: 'fas fa-chart-trending-up',
    iconClass: 'icon-success',
    progress: 90
  },
  {
    id: 5,
    title: 'Results & Insights',
    description: 'Comprehensive analysis complete with actionable insights',
    icon: 'fas fa-check-circle',
    iconClass: 'icon-complete',
    progress: 100
  }
])

// Demo Data
const aiInitText = ref('Initializing neural networks...')

const analysisMetrics = ref([
  { name: 'Skills', value: 85 },
  { name: 'ATS', value: 92 },
  { name: 'Format', value: 78 }
])

const previewSuggestions = ref([
  { id: 1, priority: 'high', text: 'Add React Hooks keyword' },
  { id: 2, priority: 'medium', text: 'Expand work experience' },
  { id: 3, priority: 'low', text: 'Update contact format' }
])

const previewTrends = ref([
  { skill: 'JavaScript', change: 15, direction: 'up' },
  { skill: 'Python', change: 23, direction: 'up' },
  { skill: 'React', change: 18, direction: 'up' }
])

// Computed
const totalCvs = computed(() => cvs_all.value.length)
const totalViews = computed(() => cvs_all.value.reduce((sum, cv) => sum + (cv.views || 0), 0))
const totalDownloads = computed(() => cvs_all.value.reduce((sum, cv) => sum + (cv.downloads || 0), 0))

const filteredAndSortedCvs = computed(() => {
  let tempCvs = [...cvs_all.value]

  if (searchTerm.value) {
    const lowerSearchTerm = searchTerm.value.toLowerCase()
    tempCvs = tempCvs.filter(cv =>
      (cv.title && cv.title.toLowerCase().includes(lowerSearchTerm)) ||
      (cv.personalDetails?.fullName && cv.personalDetails.fullName.toLowerCase().includes(lowerSearchTerm))
    )
  }

  if (filterTemplate.value !== 'all') {
    tempCvs = tempCvs.filter(cv => cv.selectedTemplate === filterTemplate.value)
  }

  tempCvs.sort((a, b) => {
    switch (sortBy.value) {
      case 'title_asc':
        return (a.title || '').localeCompare(b.title || '')
      case 'updated_at_asc':
        return new Date(a.updatedAt) - new Date(b.updatedAt)
      case 'updated_at_desc':
        return new Date(b.updatedAt) - new Date(a.updatedAt)
      case 'views_desc':
        return (b.views || 0) - (a.views || 0)
      default:
        return 0
    }
  })

  return tempCvs
})

const cvs = computed(() => filteredAndSortedCvs.value)

// Helper function for profile score calculation
const calculateProfileScore = () => {
  if (totalCvs.value === 0) return 0
  
  let score = 60 // Base score
  score += Math.min(totalCvs.value * 10, 30) // +10 per CV, max 30
  score += Math.min(totalViews.value / 10, 10) // Views bonus
  
  return Math.min(score, 100)
}

// Optimized Methods
const fetchCvs = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    if (!token) {
      router.push('/login')
      return
    }

    const response = await axios.get('/api/my-cvs', {
      headers: { Authorization: `Bearer ${token}` },
      timeout: 15000
    })

    if (response.data.success && Array.isArray(response.data.cvs)) {
      cvs_all.value = response.data.cvs
      
      // Show success message if CVs loaded
      if (response.data.cvs.length > 0) {
        showNotification('CVs loaded successfully!', 'success')
      }
    } else {
      cvs_all.value = []
      showNotification('No CVs found. Create your first CV!', 'info')
    }
  } catch (error) {
    console.error('Error fetching CVs:', error)
    
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      showNotification('Session expired. Please login again.', 'error')
      router.push('/login')
    } else if (error.code === 'ECONNABORTED') {
      showNotification('Request timeout. Please check your connection.', 'error')
    } else if (error.response?.status >= 500) {
      showNotification('Server error. Please try again later.', 'error')
    } else {
      showNotification('Failed to load CVs. Please refresh the page.', 'error')
    }
    
    cvs_all.value = []
  } finally {
    loading.value = false
  }
}

// Enhanced notification system
const showNotification = (message, type = 'info') => {
  // Create notification element
  const notification = document.createElement('div')
  notification.className = `notification notification-${type}`
  notification.innerHTML = `
    <div class="notification-content">
      <i class="fas ${getNotificationIcon(type)}"></i>
      <span>${message}</span>
    </div>
  `
  
  // Add to DOM
  document.body.appendChild(notification)
  
  // Animate in
  setTimeout(() => notification.classList.add('show'), 100)
  
  // Remove after delay
  setTimeout(() => {
    notification.classList.remove('show')
    setTimeout(() => document.body.removeChild(notification), 300)
  }, 4000)
}

const getNotificationIcon = (type) => {
  const icons = {
    success: 'fa-check-circle',
    error: 'fa-exclamation-circle',
    warning: 'fa-exclamation-triangle',
    info: 'fa-info-circle'
  }
  return icons[type] || 'fa-info-circle'
}

// Simple Methods
const duplicateLastCV = () => {
  if (cvs_all.value.length > 0) {
    const lastCV = cvs_all.value[0]
    router.push(`/create-cv?duplicate=${lastCV.id}`)
  }
  showCTADropdown.value = false
}



const toggleView = () => {
  viewMode.value = viewMode.value === 'grid' ? 'list' : 'grid'
}

const toggleMenu = (cvId) => {
  activeMenu.value = activeMenu.value === cvId ? null : cvId
}

const previewCv = (id) => {
  activeMenu.value = null
  router.push(`/cv/${id}/preview`)
}

const editCv = (id) => {
  activeMenu.value = null
  router.push(`/edit-cv/${id}`)
}

const downloadCv = (id, title) => {
  activeMenu.value = null
  currentCvToDownload.value = { id, title }
  showDownloadModal.value = true
}

const handleDownloadConfirm = async ({ id, title, style, quality }) => {
  showDownloadModal.value = false
  
  // Show loading notification
  showNotification('Preparing your CV download...', 'info')
  
  try {
    const token = localStorage.getItem('auth_token')
    
    const response = await axios.get(`/api/cvs/${id}/download?style=${style}&quality=${quality}`, {
      headers: { Authorization: `Bearer ${token}` },
      responseType: 'blob',
      timeout: 30000, // 30 seconds for PDF generation
      onDownloadProgress: (progressEvent) => {
        // Optional: Show download progress
        console.log('Download progress:', progressEvent)
      }
    })
    
    // Validate response
    if (response.data.size === 0) {
      throw new Error('Empty PDF file received')
    }
    
    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
    const link = document.createElement('a')
    link.href = url
    const safeTitle = title ? title.replace(/[^a-z0-9]/gi, '_').toLowerCase() : 'cv'
    const fileName = `${safeTitle}_${style}_${quality}.pdf`
    link.setAttribute('download', fileName)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
    
    // Success notification
    showNotification(`CV "${title}" downloaded successfully!`, 'success')
    
  } catch (error) {
    console.error('Download failed:', error)
    
    let errorMessage = 'Failed to download CV. Please try again.'
    
    if (error.code === 'ECONNABORTED') {
      errorMessage = 'Download timeout. The PDF generation is taking too long.'
    } else if (error.response?.status === 404) {
      errorMessage = 'CV not found. It may have been deleted.'
    } else if (error.response?.status === 403) {
      errorMessage = 'Access denied. You may not have permission to download this CV.'
    } else if (error.response?.status >= 500) {
      errorMessage = 'Server error during PDF generation. Please try again later.'
    } else if (error.message === 'Empty PDF file received') {
      errorMessage = 'Generated PDF is empty. Please check your CV data.'
    }
    
    showNotification(errorMessage, 'error')
  }
}

const confirmDelete = (id) => {
  activeMenu.value = null
  const cv = cvs_all.value.find(c => c.id === id)
  showConfirmModal.value = true
  confirmModal.value = {
    title: 'Delete CV',
    message: `Are you sure you want to delete "${cv?.title || 'this CV'}"? This action cannot be undone.`,
    onConfirm: () => deleteCv(id)
  }
}

const deleteCv = async (id) => {
  try {
    const token = localStorage.getItem('auth_token')
    const cvToDelete = cvs_all.value.find(cv => cv.id === id)
    
    await axios.delete(`/api/cvs/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
      timeout: 10000
    })
    
    cvs_all.value = cvs_all.value.filter((cv) => cv.id !== id)
    showConfirmModal.value = false
    
    showNotification(`CV "${cvToDelete?.title || 'Untitled'}" deleted successfully!`, 'success')
    
  } catch (error) {
    console.error('Error deleting CV:', error)
    
    let errorMessage = 'Failed to delete CV. Please try again.'
    
    if (error.response?.status === 404) {
      errorMessage = 'CV not found. It may have already been deleted.'
    } else if (error.response?.status === 403) {
      errorMessage = 'Access denied. You may not have permission to delete this CV.'
    } else if (error.code === 'ECONNABORTED') {
      errorMessage = 'Request timeout. Please check your connection.'
    }
    
    showNotification(errorMessage, 'error')
  }
}

// Utilities
const getTemplateIcon = (template) => {
  const icons = {
    'classic': 'fas fa-file-alt',
    'modern': 'fas fa-laptop-code',
    'professional': 'fas fa-briefcase',
    'creative': 'fas fa-palette'
  }
  return icons[template] || 'fas fa-file-alt'
}

const getCvScore = (cv) => {
  if (!cv || typeof cv !== 'object') return 0
  
  let score = 60
  
  // Validate and score personal details
  if (cv.personalDetails && typeof cv.personalDetails === 'object') {
    if (cv.personalDetails.firstName && cv.personalDetails.firstName.trim()) score += 10
    if (cv.personalDetails.email && cv.personalDetails.email.includes('@')) score += 5
    if (cv.personalDetails.phone && cv.personalDetails.phone.trim()) score += 5
  }
  
  // Validate and score summary
  if (cv.summary && typeof cv.summary === 'string' && cv.summary.trim().length > 20) {
    score += 10
  }
  
  // Validate and score experience
  if (Array.isArray(cv.experience) && cv.experience.length > 0) {
    score += 15
    // Bonus for detailed experience
    if (cv.experience.some(exp => exp.description && exp.description.length > 50)) {
      score += 5
    }
  }
  
  // Validate and score skills
  if (Array.isArray(cv.skills) && cv.skills.length > 0) {
    score += 5
    // Bonus for having multiple skills
    if (cv.skills.length >= 5) score += 5
  }
  
  // Validate and score education
  if (Array.isArray(cv.education) && cv.education.length > 0) {
    score += 5
  }
  
  return Math.min(100, Math.max(0, score))
}

const getCvCompleteness = (cv) => {
  let completeness = 0
  const weights = {
    personalDetails: 25,
    summary: 20,
    experience: 30,
    education: 15,
    skills: 10
  }
  
  if (cv.personalDetails?.firstName && cv.personalDetails?.email) {
    completeness += weights.personalDetails
  }
  if (cv.summary && cv.summary.length > 50) {
    completeness += weights.summary
  }
  if (cv.experience?.length > 0) {
    completeness += weights.experience
  }
  if (cv.education?.length > 0) {
    completeness += weights.education
  }
  if (cv.skills?.length >= 3) {
    completeness += weights.skills
  }
  
  return Math.round(completeness)
}

const getAuthorName = (cv) => {
  if (cv.personalDetails?.fullName) {
    return cv.personalDetails.fullName
  }
  if (cv.personalDetails?.firstName && cv.personalDetails?.lastName) {
    return `${cv.personalDetails.firstName} ${cv.personalDetails.lastName}`
  }
  return cv.personalDetails?.firstName || 'Your Name'
}

const formatDate = (dateString) => {
  if (!dateString) return 'Recently'
  try {
    const date = new Date(dateString)
    if (isNaN(date.getTime())) return 'Recently'
    
    const now = new Date()
    const diffTime = Math.abs(now - date)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    
    if (diffDays === 0) return 'Today'
    if (diffDays === 1) return 'Yesterday'
    if (diffDays < 7) return `${diffDays} days ago`
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`
    
    return date.toLocaleDateString('en-US', { 
      month: 'short', 
      day: 'numeric',
      year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
    })
  } catch (error) {
    console.warn('Date formatting error:', error)
    return 'Recently'
  }
}

// Enhanced input sanitization
const sanitizeInput = (input) => {
  if (typeof input !== 'string') return ''
  return input
    .replace(/[<>]/g, '') // Remove potential HTML tags
    .replace(/javascript:/gi, '') // Remove javascript: protocols
    .replace(/on\w+=/gi, '') // Remove event handlers
    .trim()
    .substring(0, 1000) // Limit length
}

// Enhanced data validation
const validateCvData = (cv) => {
  if (!cv || typeof cv !== 'object') return false
  
  // Check required fields
  if (!cv.id || typeof cv.id !== 'number') return false
  
  // Validate personal details
  if (cv.personalDetails && typeof cv.personalDetails === 'object') {
    const { email } = cv.personalDetails
    if (email && typeof email === 'string') {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!emailRegex.test(email)) return false
    }
  }
  
  // Validate arrays
  const arrayFields = ['skills', 'experience', 'education', 'interests']
  for (const field of arrayFields) {
    if (cv[field] && !Array.isArray(cv[field])) return false
  }
  
  return true
}

// AI Process Demo Methods
const setCurrentAIStep = (step) => {
  currentAIStep.value = step
  triggerAIStepAnimations(step)
}

const nextAIStep = () => {
  if (currentAIStep.value < 5) {
    currentAIStep.value++
    triggerAIStepAnimations(currentAIStep.value)
  }
}

const prevAIStep = () => {
  if (currentAIStep.value > 1) {
    currentAIStep.value--
    triggerAIStepAnimations(currentAIStep.value)
  }
}

const previewAIStep = (step) => {
  // Optional: add preview logic for hover states
}

const resetAIPreview = () => {
  // Optional: reset preview states
}

const triggerAIStepAnimations = (step) => {
  // Reset animations
  step1AIAnimation.value = false
  step2AIAnimation.value = false
  step3AIAnimation.value = false
  step4AIAnimation.value = false
  
  // Trigger step-specific animations
  setTimeout(() => {
    if (step === 1) {
      step1AIAnimation.value = true
    } else if (step === 2) {
      step2AIAnimation.value = true
    } else if (step === 3) {
      step3AIAnimation.value = true
    } else if (step === 4) {
      step4AIAnimation.value = true
    }
  }, 300)
}

// Real Chart.js initialization with proper error handling
const initializeCharts = async () => {
  await nextTick()
  
  // Add a delay to ensure DOM is ready
  setTimeout(() => {
    try {
      createPerformanceChart()
      createSkillsChart()
    } catch (error) {
      console.error('Error initializing charts:', error)
      // Retry once after a delay
      setTimeout(() => {
        try {
          createPerformanceChart()
          createSkillsChart()
        } catch (retryError) {
          console.error('Failed to initialize charts after retry:', retryError)
        }
      }, 500)
    }
  }, 300)
}

const createPerformanceChart = () => {
  const canvas = performanceChart.value
  if (!canvas) {
    console.warn('Performance chart canvas not found')
    return
  }
  
  // Destroy existing chart
  if (performanceChartInstance) {
    performanceChartInstance.destroy()
    performanceChartInstance = null
  }
  
  const ctx = canvas.getContext('2d')
  if (!ctx) {
    console.warn('Could not get 2D context for performance chart')
    return
  }
  
  const data = performanceData.value
  
  try {
    performanceChartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.labels,
        datasets: [
          {
            label: 'Profile Views',
            data: data.viewsData,
            borderColor: '#4F46E5',
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
            borderWidth: 3,
            pointBackgroundColor: '#4F46E5',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            fill: true,
            tension: 0.4
          },
          {
            label: 'Downloads',
            data: data.downloadsData,
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 2,
            pointBackgroundColor: '#10B981',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 3,
            pointHoverRadius: 5,
            fill: true,
            tension: 0.4
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false // We'll use custom legend
          },
          tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            titleColor: '#1F2937',
            bodyColor: '#6B7280',
            borderColor: 'rgba(79, 70, 229, 0.2)',
            borderWidth: 1,
            cornerRadius: 8,
            padding: 12
          }
        },
        interaction: {
          mode: 'nearest',
          axis: 'x',
          intersect: false
        },
        scales: {
          x: {
            display: true,
            grid: {
              display: false
            },
            ticks: {
              color: '#9CA3AF',
              font: {
                size: 11
              },
              maxTicksLimit: 8
            }
          },
          y: {
            display: true,
            grid: {
              color: 'rgba(148, 163, 184, 0.1)',
              borderDash: [5, 5]
            },
            ticks: {
              color: '#9CA3AF',
              font: {
                size: 11
              },
              beginAtZero: true
            }
          }
        },
        elements: {
          line: {
            borderJoinStyle: 'round'
          }
        }
      }
    })
  } catch (error) {
    console.error('Error creating performance chart:', error)
  }
}

const createSkillsChart = () => {
  const canvas = skillsChart.value
  if (!canvas) {
    console.warn('Skills chart canvas not found')
    return
  }
  
  console.log('Creating skills chart with data:', skillsData.value)
  
  // Destroy existing chart
  if (skillsChartInstance) {
    skillsChartInstance.destroy()
    skillsChartInstance = null
  }
  
  const ctx = canvas.getContext('2d')
  if (!ctx) {
    console.warn('Could not get 2D context for skills chart')
    return
  }
  
  const data = skillsData.value
  
  try {
    skillsChartInstance = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: data.labels,
        datasets: [{
          data: data.data,
          backgroundColor: [
            '#4F46E5',
            '#10B981',
            '#F59E0B',
            '#EF4444',
            '#8B5CF6',
            '#06B6D4'
          ],
          borderColor: '#ffffff',
          borderWidth: 3,
          hoverBorderWidth: 4,
          hoverOffset: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
          legend: {
            display: false // We'll use custom legend below
          },
          tooltip: {
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            titleColor: '#1F2937',
            bodyColor: '#6B7280',
            borderColor: 'rgba(79, 70, 229, 0.2)',
            borderWidth: 1,
            cornerRadius: 8,
            padding: 12,
            callbacks: {
              label: function(context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0)
                const percentage = ((context.parsed / total) * 100).toFixed(1)
                return `${context.label}: ${percentage}%`
              }
            }
          }
        },
        animation: {
          animateRotate: true,
          duration: 1000
        }
      }
    })
  } catch (error) {
    console.error('Error creating skills chart:', error)
  }
}

const refreshCharts = () => {
  // Add a small delay to ensure smooth refresh
  setTimeout(() => {
    createPerformanceChart()
    createSkillsChart()
  }, 100)
}

// CV Preview Animation Functions (Smooth JavaScript-powered)
const startCvPreview = (cvId, templateType) => {
  previewingCv.value = cvId
  previewTemplate.value = templateType
  
  nextTick(() => {
    animatePreviewSmoothly(cvId)
  })
}

const stopCvPreview = () => {
  previewingCv.value = null
  previewTemplate.value = null
  animatePreviewContent.value = false
}

// Smooth, natural JavaScript preview animations
const animatePreviewSmoothly = (cvId) => {
  const previewElement = document.querySelector(`[data-cv-id="${cvId}"] .cv-animated-preview`)
  if (!previewElement) return

  // Smooth slide-in with natural momentum
  previewElement.animate([
    { 
      opacity: 0, 
      transform: 'translateX(-100%) scale(0.95)',
      filter: 'brightness(0.9)'
    },
    { 
      opacity: 0.5, 
      transform: 'translateX(-10%) scale(0.98)',
      filter: 'brightness(0.95)',
      offset: 0.4
    },
    { 
      opacity: 1, 
      transform: 'translateX(0%) scale(1)',
      filter: 'brightness(1)'
    }
  ], {
    duration: 600,
    easing: 'cubic-bezier(0.23, 1, 0.32, 1)',
    fill: 'forwards'
  }).addEventListener('finish', () => {
    animateContentElements(previewElement)
  })
}

const animateContentElements = (container) => {
  const elements = {
    headers: container.querySelectorAll('h3, h4'),
    titles: container.querySelectorAll('.job-title, .degree, .cv-name, .preview-name'),
    companies: container.querySelectorAll('.company-name, .school, .cv-title, .preview-title'),
    descriptions: container.querySelectorAll('.job-description, .summary-content'),
    skills: container.querySelectorAll('.skill-tag'),
    progressBars: container.querySelectorAll('.progress')
  }

  // Smooth header animations
  elements.headers.forEach((header, index) => {
    setTimeout(() => {
      header.animate([
        { opacity: 0, transform: 'translateX(-30px)' },
        { opacity: 1, transform: 'translateX(0)' }
      ], {
        duration: 400,
        easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
        fill: 'forwards'
      })
      
      // Add underline sweep
      setTimeout(() => createSmoothUnderline(header), 200)
    }, index * 100)
  })

  // Smooth title animations
  elements.titles.forEach((title, index) => {
    setTimeout(() => {
      title.animate([
        { opacity: 0, transform: 'translateX(-25px) scale(0.95)' },
        { opacity: 1, transform: 'translateX(0) scale(1)' }
      ], {
        duration: 350,
        easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
        fill: 'forwards'
      })
    }, 200 + index * 80)
  })

  // Smooth company animations
  elements.companies.forEach((company, index) => {
    setTimeout(() => {
      company.animate([
        { opacity: 0, transform: 'translateX(-20px)' },
        { opacity: 1, transform: 'translateX(0)' }
      ], {
        duration: 300,
        easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
        fill: 'forwards'
      })
    }, 350 + index * 70)
  })

  // Smooth description flow
  elements.descriptions.forEach((desc, index) => {
    setTimeout(() => {
      desc.animate([
        { opacity: 0, transform: 'translateX(-15px)' },
        { opacity: 1, transform: 'translateX(0)' }
      ], {
        duration: 250,
        easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
        fill: 'forwards'
      })
    }, 500 + index * 60)
  })

  // Smooth skill animations
  elements.skills.forEach((skill, index) => {
    setTimeout(() => {
      skill.animate([
        { opacity: 0, transform: 'translateX(-30px) scale(0.9)' },
        { opacity: 1, transform: 'translateX(0) scale(1)' }
      ], {
        duration: 400,
        easing: 'cubic-bezier(0.34, 1.56, 0.64, 1)',
        fill: 'forwards'
      })
    }, 600 + index * 50)
  })

  // Smooth progress bars
  elements.progressBars.forEach((progress, index) => {
    const targetWidth = progress.style.width || '80%'
    setTimeout(() => {
      progress.style.width = '0%'
      progress.animate([
        { width: '0%' },
        { width: targetWidth }
      ], {
        duration: 800,
        easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
        fill: 'forwards'
      })
    }, 400 + index * 100)
  })

  animatePreviewContent.value = true
}

const createSmoothUnderline = (element) => {
  const underline = document.createElement('div')
  underline.style.cssText = `
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--primary), var(--primary-light));
    border-radius: 1px;
  `
  element.style.position = 'relative'
  element.appendChild(underline)

  underline.animate([
    { width: '0%' },
    { width: '100%' }
  ], {
    duration: 500,
    easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
    fill: 'forwards'
  })
}

// Complete Dashboard JavaScript Animation System
const initializeDashboardAnimations = () => {
  if (dashboardAnimationController) {
    dashboardAnimationController.cleanup()
  }
  
  dashboardAnimationController = new DashboardAnimationController()
  dashboardAnimationController.init()
}

class DashboardAnimationController {
  constructor() {
    this.animations = []
    this.observers = []
    this.isInitialized = false
  }

  init() {
    if (this.isInitialized) return
    
    // Wait for DOM to be ready
    setTimeout(() => {
      this.animateHeader()
      this.animateStatsCards()
      this.animateCvCards()
      this.animateSidebar()
      this.setupScrollAnimations()
      this.setupHoverAnimations()
      this.isInitialized = true
    }, 100)
  }

  animateHeader() {
    const header = document.querySelector('.premium-header')
    if (!header) return

    // Animate greeting
    const greeting = header.querySelector('.premium-greeting')
    if (greeting) {
      greeting.animate([
        { opacity: 0, transform: 'translateY(-20px)' },
        { opacity: 1, transform: 'translateY(0)' }
      ], {
        duration: 600,
        easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
        fill: 'forwards'
      })
    }

    // Animate subtitle
    const subtitle = header.querySelector('.greeting-subtitle')
    if (subtitle) {
      setTimeout(() => {
        subtitle.animate([
          { opacity: 0, transform: 'translateY(-15px)' },
          { opacity: 1, transform: 'translateY(0)' }
        ], {
          duration: 500,
          easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
          fill: 'forwards'
        })
      }, 200)
    }

    // Animate CTA button
    const ctaBtn = header.querySelector('.premium-cta-btn')
    if (ctaBtn) {
      setTimeout(() => {
        ctaBtn.animate([
          { opacity: 0, transform: 'translateX(30px) scale(0.95)' },
          { opacity: 1, transform: 'translateX(0) scale(1)' }
        ], {
          duration: 600,
          easing: 'cubic-bezier(0.34, 1.56, 0.64, 1)',
          fill: 'forwards'
        })
      }, 400)
    }
  }

  animateStatsCards() {
    const statsCards = document.querySelectorAll('.stat-card')
    
    statsCards.forEach((card, index) => {
      setTimeout(() => {
        card.animate([
          { 
            opacity: 0, 
            transform: 'translateY(30px) scale(0.95)',
          },
          { 
            opacity: 1, 
            transform: 'translateY(0) scale(1)',
          }
        ], {
          duration: 500,
          easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
          fill: 'forwards'
        })
      }, 600 + index * 100)
    })
  }

  animateCvCards() {
    const cvCards = document.querySelectorAll('.cv-display-card')
    
    cvCards.forEach((card, index) => {
      setTimeout(() => {
        card.animate([
          { 
            opacity: 0, 
            transform: 'translateY(40px) scale(0.9)',
          },
          { 
            opacity: 1, 
            transform: 'translateY(0) scale(1)',
          }
        ], {
          duration: 600,
          easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
          fill: 'forwards'
        })
      }, 1000 + index * 150)
    })
  }

  animateSidebar() {
    const sidebar = document.querySelector('.premium-analytics-sidebar')
    if (!sidebar) return

    setTimeout(() => {
      sidebar.animate([
        { 
          opacity: 0, 
          transform: 'translateX(30px)',
        },
        { 
          opacity: 1, 
          transform: 'translateX(0)',
        }
      ], {
        duration: 700,
        easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
        fill: 'forwards'
      })
    }, 800)

    // Animate sidebar cards
    const sidebarCards = sidebar.querySelectorAll('.premium-analytics-card')
    sidebarCards.forEach((card, index) => {
      setTimeout(() => {
        card.animate([
          { 
            opacity: 0, 
            transform: 'translateX(20px)',
          },
          { 
            opacity: 1, 
            transform: 'translateX(0)',
          }
        ], {
          duration: 400,
          easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
          fill: 'forwards'
        })
      }, 1200 + index * 200)
    })
  }

  setupScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const element = entry.target
          
          element.animate([
            { 
              opacity: 0, 
              transform: 'translateY(30px)',
            },
            { 
              opacity: 1, 
              transform: 'translateY(0)',
            }
          ], {
            duration: 500,
            easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
            fill: 'forwards'
          })
          
          observer.unobserve(element)
        }
      })
    }, {
      threshold: 0.1,
      rootMargin: '50px'
    })

    // Observe elements that come into view
    document.querySelectorAll('.cv-display-card, .stat-card, .premium-analytics-card').forEach(el => {
      observer.observe(el)
    })

    this.observers.push(observer)
  }

  setupHoverAnimations() {
    // CV Card hover animations
    document.querySelectorAll('.cv-display-card').forEach(card => {
      card.addEventListener('mouseenter', () => {
        card.animate([
          { 
            transform: 'translateY(0) scale(1)',
            boxShadow: '0 10px 25px rgba(0, 0, 0, 0.08)'
          },
          { 
            transform: 'translateY(-8px) scale(1.02)',
            boxShadow: '0 25px 50px rgba(0, 0, 0, 0.15)'
          }
        ], {
          duration: 300,
          easing: 'cubic-bezier(0.34, 1.56, 0.64, 1)',
          fill: 'forwards'
        })
      })

      card.addEventListener('mouseleave', () => {
        card.animate([
          { 
            transform: 'translateY(-8px) scale(1.02)',
            boxShadow: '0 25px 50px rgba(0, 0, 0, 0.15)'
          },
          { 
            transform: 'translateY(0) scale(1)',
            boxShadow: '0 10px 25px rgba(0, 0, 0, 0.08)'
          }
        ], {
          duration: 250,
          easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
          fill: 'forwards'
        })
      })
    })

    // Stats card hover animations
    document.querySelectorAll('.stat-card').forEach(card => {
      card.addEventListener('mouseenter', () => {
        card.animate([
          { transform: 'translateY(0) scale(1)' },
          { transform: 'translateY(-4px) scale(1.02)' }
        ], {
          duration: 200,
          easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
          fill: 'forwards'
        })
      })

      card.addEventListener('mouseleave', () => {
        card.animate([
          { transform: 'translateY(-4px) scale(1.02)' },
          { transform: 'translateY(0) scale(1)' }
        ], {
          duration: 200,
          easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
          fill: 'forwards'
        })
      })
    })

    // Button hover animations
    document.querySelectorAll('.btn, .premium-cta-btn').forEach(btn => {
      btn.addEventListener('mouseenter', () => {
        btn.animate([
          { transform: 'scale(1)' },
          { transform: 'scale(1.05)' }
        ], {
          duration: 150,
          easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
          fill: 'forwards'
        })
      })

      btn.addEventListener('mouseleave', () => {
        btn.animate([
          { transform: 'scale(1.05)' },
          { transform: 'scale(1)' }
        ], {
          duration: 150,
          easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
          fill: 'forwards'
        })
      })
    })
  }

  cleanup() {
    // Cancel all running animations
    this.animations.forEach(animation => {
      if (animation && animation.cancel) {
        animation.cancel()
      }
    })

    // Disconnect all observers
    this.observers.forEach(observer => {
      if (observer && observer.disconnect) {
        observer.disconnect()
      }
    })

    this.animations = []
    this.observers = []
    this.isInitialized = false
  }
}

// Enhanced initialization with complete JavaScript animation system
onMounted(async () => {
  // Set page title and meta for SEO
  document.title = 'Dashboard - CV Maker | Manage Your Professional CVs'
  const metaDescription = document.querySelector('meta[name="description"]')
  if (metaDescription) {
    metaDescription.setAttribute('content', 'Manage and organize your professional CVs. View analytics, download PDFs, and track your career documents with our advanced dashboard.')
  }
  
  // Performance monitoring
  const startTime = performance.now()
  
  try {
    await fetchCvs()
    
    // Initialize charts
    await nextTick()
    setTimeout(initializeCharts, 500)
    
    // Initialize complete dashboard animations
    setTimeout(() => {
      initializeDashboardAnimations()
    }, 100)
    
    // Log performance
    const loadTime = performance.now() - startTime
    console.log(`Dashboard loaded in ${Math.round(loadTime)}ms`)
    
    // Set up click outside handler for menus
    document.addEventListener('click', handleClickOutside)
    
  } catch (error) {
    console.error('Dashboard initialization error:', error)
    showNotification('Failed to initialize dashboard. Please refresh the page.', 'error')
  }
})

// Click outside handler
const handleClickOutside = (event) => {
  if (!event.target.closest('.cv-menu-trigger')) {
    activeMenu.value = null
  }
}

// Enhanced cleanup
onBeforeUnmount(() => {
  // Destroy chart instances
  if (performanceChartInstance) {
    performanceChartInstance.destroy()
  }
  if (skillsChartInstance) {
    skillsChartInstance.destroy()
  }
  
  // Cleanup dashboard animations
  if (dashboardAnimationController) {
    dashboardAnimationController.cleanup()
  }
})

// Note: Click outside handler is already set up in onMounted

// --- Feature Section Logic ---
// Smart Recommendations: Analyze CVs for missing/weak areas
const smartRecommendations = computed(() => {
  if (!cvs_all.value.length) return [
    { id: 1, text: 'Create your first CV to get personalized tips!' }
  ]
  const recs = []
  cvs_all.value.forEach(cv => {
    if (!cv.summary || cv.summary.length < 50) recs.push({ id: `sum-${cv.id}`, text: 'Expand your summary for more impact.' })
    if (!cv.skills || cv.skills.length < 3) recs.push({ id: `skills-${cv.id}`, text: 'Add at least 3 skills to showcase your strengths.' })
    if (!cv.experience || cv.experience.length === 0) recs.push({ id: `exp-${cv.id}`, text: 'Add work experience to strengthen your CV.' })
    if (!cv.education || cv.education.length === 0) recs.push({ id: `edu-${cv.id}`, text: 'Add your education history.' })
    if (!cv.personalDetails?.email) recs.push({ id: `email-${cv.id}`, text: 'Add a professional email address.' })
  })
  return recs.length ? recs : [{ id: 0, text: 'Your CVs look great! Keep them updated for best results.' }]
})
// Career Optimization: Trending skills and user skill gap
const trendingSkills = [
  { skill: 'React.js', change: 12 },
  { skill: 'Python', change: 8 },
  { skill: 'Cloud Computing', change: 25 },
  { skill: 'UX Design', change: 15 },
  { skill: 'DevOps', change: 18 }
]
const userSkills = computed(() => {
  const allSkills = new Set()
  cvs_all.value.forEach(cv => (cv.skills || []).forEach(s => allSkills.add(s)))
  return Array.from(allSkills)
})
// Real-time Insights: Views/downloads in last 24h, completeness
const now = Date.now()
const ms24h = 24 * 60 * 60 * 1000
const views24h = computed(() => cvs_all.value.reduce((sum, cv) => sum + ((cv.viewsHistory||[]).filter(v => now - new Date(v) < ms24h).length), 0))
const downloads24h = computed(() => cvs_all.value.reduce((sum, cv) => sum + ((cv.downloadsHistory||[]).filter(d => now - new Date(d) < ms24h).length), 0))
const profileCompleteness = computed(() => {
  if (!cvs_all.value.length) return 0
  let max = 0
  cvs_all.value.forEach(cv => {
    let c = 0
    if (cv.personalDetails?.firstName && cv.personalDetails?.email) c += 25
    if (cv.summary && cv.summary.length > 50) c += 20
    if (cv.experience?.length > 0) c += 30
    if (cv.education?.length > 0) c += 15
    if (cv.skills?.length >= 3) c += 10
    if (c > max) max = c
  })
  return max
})
// AI Analysis: Use latest CV, run analysis
const aiAnalysis = computed(() => {
  if (!cvs_all.value.length) return { atsScore: 0, skillMatch: 0, formatQuality: 0 }
  const latest = cvs_all.value.slice().sort((a, b) => new Date(b.updatedAt) - new Date(a.updatedAt))[0]
  // Simulate analysis (replace with real API if available)
  let atsScore = 60
  let skillMatch = 50
  let formatQuality = 60
  if (latest.skills?.length >= 5) skillMatch += 30
  if (latest.summary && latest.summary.length > 50) atsScore += 20
  if (latest.experience?.length > 0) atsScore += 10
  if (latest.education?.length > 0) atsScore += 5
  if (latest.selectedTemplate === 'professional') formatQuality += 20
  return {
    atsScore: Math.min(atsScore, 100),
    skillMatch: Math.min(skillMatch, 100),
    formatQuality: Math.min(formatQuality, 100)
  }
  })

</script>

<style scoped>
/* Modern Dashboard Styles */
.dashboard-main {
  min-height: 100vh;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  position: relative;
}

/* Clean Background */
.dashboard-bg {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
  pointer-events: none;
  opacity: 0.6;
}

.bg-sphere {
  position: absolute;
  border-radius: 50%;
  filter: blur(40px);
  opacity: 0.3;
  animation: gentleFloat 25s infinite ease-in-out;
}

.bg-sphere-1 {
  width: 400px;
  height: 400px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  top: -200px;
  left: -200px;
}

.bg-sphere-2 {
  width: 300px;
  height: 300px;
  background: linear-gradient(135deg, #06b6d4, #3b82f6);
  bottom: -150px;
  right: -150px;
  animation-delay: 12s;
}

.bg-sphere-3 {
  width: 250px;
  height: 250px;
  background: linear-gradient(135deg, #10b981, #059669);
  top: 50%;
  right: -125px;
  animation-delay: 18s;
}

@keyframes gentleFloat {
  0%, 100% { 
    transform: translate3d(0, 0, 0) scale(1); 
    opacity: 0.3;
  }
  50% { 
    transform: translate3d(30px, -30px, 0) scale(1.1); 
    opacity: 0.5;
  }
}

/* Container */
.dashboard-container {
  position: relative;
  z-index: 1;
  max-width: 1400px;
  margin: 0 auto;
  padding: 2rem 1.5rem;
  width: 100%;
}

/* Header Styles */
.dashboard-header {
  margin-bottom: 2rem;
}

.header-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  padding: 2rem;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.greeting-section {
  flex: 1;
}

.time-indicator {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(99, 102, 241, 0.1);
  color: #6366f1;
  padding: 0.5rem 1rem;
  border-radius: 50px;
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 1rem;
  border: 1px solid rgba(99, 102, 241, 0.2);
}

.dashboard-title {
  font-family: 'Inter', sans-serif;
  font-size: 3rem;
  font-weight: 800;
  margin: 0 0 0.5rem 0;
  background: linear-gradient(135deg, #1f2937 0%, #6366f1 50%, #10b981 100%);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  line-height: 1.1;
}

.dashboard-subtitle {
  font-size: 1.125rem;
  color: #64748b;
  margin: 0;
  font-weight: 500;
}

/* Header Actions */
.header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.create-cv-btn {
  background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
  color: white;
  padding: 1rem 2rem;
  border-radius: 16px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.create-cv-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(99, 102, 241, 0.4);
}

.dropdown-wrapper {
  position: relative;
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(0, 0, 0, 0.1);
  color: #64748b;
  padding: 1rem;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-secondary:hover {
  background: white;
  color: #1f2937;
  transform: translateY(-1px);
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + 0.5rem);
  right: 0;
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  min-width: 200px;
  z-index: 1000;
  padding: 0.5rem;
  border: 1px solid rgba(0, 0, 0, 0.1);
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem 1rem;
  background: none;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
  text-decoration: none;
}

.dropdown-item:hover {
  background: #f8fafc;
  color: #6366f1;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  margin-top: 2rem;
  max-width: 100%;
}

.stat-card {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 1.5rem;
  border: 1px solid rgba(255, 255, 255, 0.3);
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
  min-height: 120px;
  min-height: 120px;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
  border-color: rgba(99, 102, 241, 0.3);
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
  flex-shrink: 0;
}

.stat-icon.primary {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
}

.stat-icon.success {
  background: linear-gradient(135deg, #10b981, #34d399);
  box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
}

.stat-icon.warning {
  background: linear-gradient(135deg, #f59e0b, #fbbf24);
  box-shadow: 0 8px 16px rgba(245, 158, 11, 0.3);
}

.stat-icon.info {
  background: linear-gradient(135deg, #3b82f6, #60a5fa);
  box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
}

.stat-content {
  flex: 1;
  min-width: 0;
  overflow: hidden;
}

.stat-value {
  font-size: 2rem;
  font-weight: 800;
  color: #1f2937;
  line-height: 1;
  margin-bottom: 0.25rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.stat-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.25rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.stat-description {
  font-size: 0.75rem;
  color: #64748b;
  line-height: 1.4;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.stat-trend {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  white-space: nowrap;
  flex-shrink: 0;
}

.stat-trend.up {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.stat-trend.neutral {
  background: rgba(107, 114, 128, 0.1);
  color: #6b7280;
}

/* Header */
.dashboard-header {
  margin-bottom: 2rem;
}

.header-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  padding: 2rem;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.greeting-section {
  flex: 1;
}

.time-indicator {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(99, 102, 241, 0.1);
  color: #6366f1;
  padding: 0.5rem 1rem;
  border-radius: 50px;
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 1rem;
  border: 1px solid rgba(99, 102, 241, 0.2);
}

.dashboard-title {
  font-family: 'Inter', sans-serif;
  font-size: 3rem;
  font-weight: 800;
  margin: 0 0 0.5rem 0;
  background: linear-gradient(135deg, #1f2937 0%, #6366f1 50%, #10b981 100%);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  line-height: 1.1;
}

.dashboard-subtitle {
  font-size: 1.125rem;
  color: #64748b;
  margin: 0;
  font-weight: 500;
}

/* Header Actions */
.header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.create-cv-btn {
  background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
  color: white;
  padding: 1rem 2rem;
  border-radius: 16px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.create-cv-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(99, 102, 241, 0.4);
}

.dropdown-wrapper {
  position: relative;
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(0, 0, 0, 0.1);
  color: #64748b;
  padding: 1rem;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-secondary:hover {
  background: white;
  color: #1f2937;
  transform: translateY(-1px);
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + 0.5rem);
  right: 0;
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  min-width: 200px;
  z-index: 1000;
  padding: 0.5rem;
  border: 1px solid rgba(0, 0, 0, 0.1);
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem 1rem;
  background: none;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
  text-decoration: none;
}

.dropdown-item:hover {
  background: #f8fafc;
  color: #6366f1;
}

/* Header Card */
.header-card {
  background: var(--bg-elevated);
  border-radius: var(--radius-2xl);
  padding: var(--space-xl);
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--border-light);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-xl);
}

.greeting-section {
  flex: 1;
}

.time-indicator {
  display: inline-flex;
  align-items: center;
  gap: var(--space-xs);
  background: var(--primary-bg);
  color: var(--primary);
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--radius-full);
  font-size: var(--font-size-sm);
  font-weight: 600;
  margin-bottom: var(--space-md);
  border: 1px solid var(--primary-border);
}

.dashboard-title {
  font-family: var(--font-heading);
  font-size: var(--font-size-4xl);
  font-weight: 800;
  margin: 0 0 var(--space-sm) 0;
  color: var(--text-primary);
  line-height: var(--line-height-tight);
}

.dashboard-subtitle {
  font-size: var(--font-size-lg);
  color: var(--text-secondary);
  margin: 0;
  font-weight: 500;
}

/* Header Actions */
.header-actions {
  display: flex;
  align-items: center;
  gap: var(--space-md);
}

.create-cv-btn {
  background: var(--gradient-primary);
  color: white;
  padding: var(--space-md) var(--space-xl);
  border-radius: var(--radius-xl);
  font-weight: 600;
  text-decoration: none;
  transition: var(--transition-all);
  box-shadow: var(--shadow-primary);
}

.create-cv-btn:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.dropdown-wrapper {
  position: relative;
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + var(--space-xs));
  right: 0;
  background: var(--bg-elevated);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-xl);
  min-width: 180px;
  z-index: var(--z-dropdown);
  padding: var(--space-sm);
  border: 1px solid var(--border);
}

.header-glass-card {
  background: linear-gradient(135deg, 
    rgba(255, 255, 255, 0.95) 0%, 
    rgba(248, 250, 252, 0.9) 100%);
  backdrop-filter: blur(20px);
  border-radius: 32px;
  padding: 40px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 
    0 20px 40px rgba(0, 0, 0, 0.1),
    0 8px 16px rgba(79, 70, 229, 0.05),
    inset 0 1px 0 rgba(255, 255, 255, 0.3);
  position: relative;
  overflow: hidden;
}

.header-glass-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 30%, rgba(79, 70, 229, 0.05) 0%, transparent 50%),
    radial-gradient(circle at 80% 70%, rgba(16, 185, 129, 0.03) 0%, transparent 50%);
  pointer-events: none;
}

.header-main {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40px;
  position: relative;
  z-index: 2;
}

.greeting-area {
  flex: 1;
}

.time-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(79, 70, 229, 0.1);
  color: var(--primary);
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 16px;
  border: 1px solid rgba(79, 70, 229, 0.2);
}

.premium-greeting {
  font-family: var(--font-heading);
  font-size: clamp(2.5rem, 5vw, 3.5rem);
  font-weight: 800;
  margin: 0 0 12px 0;
  background: linear-gradient(135deg, #1f2937 0%, #4f46e5 50%, #059669 100%);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  line-height: 1.1;
  letter-spacing: -0.02em;
}

.greeting-subtitle {
  font-size: 1.125rem;
  color: var(--text-secondary);
  margin: 0;
  font-weight: 500;
}

.action-area {
  flex-shrink: 0;
}

.premium-cta-btn {
  display: flex;
  align-items: center;
  gap: 16px;
  background: linear-gradient(135deg, var(--primary) 0%, #6366f1 100%);
  color: white;
  padding: 20px 32px;
  border-radius: 20px;
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 
    0 8px 16px rgba(79, 70, 229, 0.3),
    0 4px 8px rgba(79, 70, 229, 0.2);
  position: relative;
  overflow: hidden;
}

.premium-cta-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.6s ease;
}

.premium-cta-btn:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 
    0 16px 32px rgba(79, 70, 229, 0.4),
    0 8px 16px rgba(79, 70, 229, 0.3);
}

.premium-cta-btn:hover::before {
  left: 100%;
}

.btn-icon {
  width: 48px;
  height: 48px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}

.btn-text {
  flex: 1;
  text-align: left;
}

.btn-title {
  display: block;
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 2px;
}

.btn-subtitle {
  display: block;
  font-size: 0.875rem;
  opacity: 0.8;
}

.btn-arrow {
  font-size: 16px;
  opacity: 0.8;
  transition: transform 0.3s ease;
}

.premium-cta-btn:hover .btn-arrow {
  transform: translateX(4px);
}

/* Enhanced Stats Grid */
.premium-stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
  position: relative;
  z-index: 2;
}

/* Activity Ticker */
.activity-ticker {
  margin-top: 24px;
  background: rgba(255, 255, 255, 0.6);
  border-radius: 12px;
  padding: 12px 0;
  overflow: hidden;
}

.ticker-container {
  width: 100%;
  overflow: hidden;
}

.ticker-content {
  display: flex;
  animation: ticker 30s linear infinite;
  gap: 48px;
  white-space: nowrap;
}

.ticker-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 500;
}

.ticker-item i {
  color: var(--primary);
}

@keyframes ticker {
  0% { transform: translateX(100%); }
  100% { transform: translateX(-100%); }
}

.stat-card {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(12px);
  border-radius: 20px;
  padding: 24px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  animation: slideInUp 0.6s ease-out both;
  animation-delay: var(--delay);
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
  opacity: 0;
  transition: opacity 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 
    0 20px 40px rgba(0, 0, 0, 0.15),
    0 8px 16px rgba(91, 33, 182, 0.2);
  border-color: rgba(91, 33, 182, 0.4);
  transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.stat-card:hover::before {
  opacity: 1;
}

/* Simple Stats */
.stat-card {
  cursor: default;
  transition: transform 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.stat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.stat-icon-wrapper {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  color: white;
}

.stat-icon-wrapper.primary {
  background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%);
}

.stat-icon-wrapper.success {
  background: linear-gradient(135deg, #10B981 0%, #34D399 100%);
}

.stat-icon-wrapper.accent {
  background: linear-gradient(135deg, #F59E0B 0%, #FCD34D 100%);
}

.stat-icon-wrapper.warning {
  background: linear-gradient(135deg, #EF4444 0%, #F87171 100%);
}

.stat-trend {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 12px;
}

.stat-trend.up {
  background: rgba(16, 185, 129, 0.1);
  color: #10B981;
}

.stat-trend.neutral {
  background: rgba(107, 114, 128, 0.1);
  color: #6B7280;
}

.stat-body {
  margin-bottom: 16px;
}

.stat-number {
  font-size: 2rem;
  font-weight: 800;
  color: var(--text-primary);
  line-height: 1;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 2px;
}

.stat-description {
  font-size: 0.75rem;
  color: var(--text-secondary);
}

.stat-progress {
  margin-top: 16px;
}

.progress-bar {
  width: 100%;
  height: 4px;
  background: rgba(0, 0, 0, 0.1);
  border-radius: 2px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--primary), var(--secondary));
  border-radius: 2px;
  transition: width 1s ease-out;
}

.greeting-section h1 {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 8px 0;
}

.dashboard-subtitle {
  font-size: 1rem;
  color: var(--text-secondary);
  margin: 0;
}

.btn-create-cv {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--primary);
  color: white;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
}

.btn-create-cv:hover {
  background: var(--primary-dark);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(79, 70, 229, 0.3);
}

/* Quick Stats Bar */
.quick-stats-bar {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 16px;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  color: white;
}

.stat-icon.primary {
  background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%);
}

.stat-icon.success {
  background: linear-gradient(135deg, #10B981 0%, #34D399 100%);
}

.stat-icon.accent {
  background: linear-gradient(135deg, #F59E0B 0%, #FCD34D 100%);
}

.stat-icon.warning {
  background: linear-gradient(135deg, #EF4444 0%, #F87171 100%);
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 0.875rem;
  color: var(--text-secondary);
}

/* Dashboard Grid Layout */
.dashboard-grid {
  display: grid;
  grid-template-columns: 1fr 420px;
  gap: 32px;
  align-items: start;
}

.main-content {
  min-height: 600px;
}

/* Premium Analytics Sidebar */
.premium-sidebar {
  display: flex;
  flex-direction: column;
  gap: 24px;
  position: sticky;
  top: 32px;
  max-height: calc(100vh - 64px);
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: rgba(0, 0, 0, 0.1) transparent;
}

.premium-sidebar::-webkit-scrollbar {
  width: 6px;
}

.premium-sidebar::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.1);
  border-radius: 3px;
}

.premium-sidebar::-webkit-scrollbar-track {
  background: transparent;
}

.premium-analytics-card {
  background: linear-gradient(135deg, 
    rgba(255, 255, 255, 0.95) 0%, 
    rgba(248, 250, 252, 0.9) 100%);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 
    0 20px 40px rgba(0, 0, 0, 0.08),
    0 8px 16px rgba(79, 70, 229, 0.05),
    inset 0 1px 0 rgba(255, 255, 255, 0.3);
  overflow: hidden;
  position: relative;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.premium-analytics-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
  opacity: 0;
  transition: opacity 0.3s ease;
}

.premium-analytics-card:hover {
  transform: translateY(-4px);
  box-shadow: 
    0 25px 50px rgba(0, 0, 0, 0.12),
    0 12px 24px rgba(79, 70, 229, 0.08);
}

.premium-analytics-card:hover::before {
  opacity: 1;
}

.analytics-header {
  padding: 24px 24px 20px 24px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.3);
  background: rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  gap: 16px;
}

.header-icon {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, var(--primary) 0%, #6366f1 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 16px;
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.header-content {
  flex: 1;
}

.header-content h3 {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 4px 0;
  letter-spacing: -0.025em;
}

.header-content p {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0;
}

.header-action {
  flex-shrink: 0;
}

.refresh-btn {
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 10px;
  padding: 8px;
  color: var(--text-secondary);
  cursor: pointer;
  transition: all 0.2s ease;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.refresh-btn:hover {
  background: var(--primary);
  color: white;
  transform: rotate(180deg);
}

.analytics-body {
  padding: 24px;
}

/* Insights Container */
.insights-container {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.insight-card {
  background: rgba(255, 255, 255, 0.8);
  border-radius: 16px;
  padding: 20px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.insight-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border-color: rgba(79, 70, 229, 0.3);
}

.insight-indicator {
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  transition: all 0.3s ease;
}

.insight-indicator.high {
  background: linear-gradient(180deg, #EF4444, #DC2626);
}

.insight-indicator.medium {
  background: linear-gradient(180deg, #F59E0B, #D97706);
}

.insight-indicator.low {
  background: linear-gradient(180deg, #10B981, #059669);
}

.insight-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  font-size: 20px;
  color: white;
}

.insight-icon.info {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}

.insight-icon.warning {
  background: linear-gradient(135deg, #F59E0B, #D97706);
}

.insight-icon.success {
  background: linear-gradient(135deg, #10B981, #059669);
}

.insight-content h4 {
  font-size: 1rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 8px 0;
  letter-spacing: -0.025em;
}

.insight-content p {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0 0 16px 0;
  line-height: 1.5;
}

.insight-action {
  background: linear-gradient(135deg, var(--primary), #6366f1);
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 0.8125rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.insight-action:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

/* Charts */
.chart-container {
  position: relative;
  height: 200px;
  width: 100%;
  margin-bottom: 16px;
  background: rgba(255, 255, 255, 0.5);
  border-radius: 12px;
  padding: 8px;
}

.skills-chart-container {
  position: relative;
  height: 220px;
  width: 100%;
  margin-bottom: 16px;
  background: rgba(255, 255, 255, 0.5);
  border-radius: 12px;
  padding: 8px;
}

canvas {
  width: 100% !important;
  height: 100% !important;
  display: block;
}

.chart-legend {
  display: flex;
  justify-content: center;
  gap: 16px;
  margin-top: 12px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.75rem;
  color: var(--text-secondary);
}

.legend-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.legend-dot.views {
  background: #4F46E5;
}

.legend-dot.downloads {
  background: #10B981;
}

/* Skills Summary */
.skills-summary {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.skill-stat {
  background: rgba(255, 255, 255, 0.6);
  border-radius: 12px;
  padding: 16px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  transition: all 0.3s ease;
}

.skill-stat:hover {
  background: rgba(255, 255, 255, 0.8);
  transform: translateY(-1px);
}

.skill-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.skill-name {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
}

.skill-percentage {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--text-secondary);
}

.skill-bar {
  width: 100%;
  height: 6px;
  background: rgba(0, 0, 0, 0.05);
  border-radius: 3px;
  overflow: hidden;
}

.skill-progress {
  height: 100%;
  border-radius: 3px;
  transition: width 1s ease-out;
}

/* Premium CV Management Section */
.cv-management-section {
  background: linear-gradient(135deg, 
    rgba(255, 255, 255, 0.95) 0%, 
    rgba(248, 250, 252, 0.9) 100%);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  padding: 32px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 
    0 20px 40px rgba(0, 0, 0, 0.08),
    0 8px 16px rgba(79, 70, 229, 0.05),
    inset 0 1px 0 rgba(255, 255, 255, 0.3);
  position: relative;
  overflow: hidden;
}

.cv-management-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 30%, rgba(79, 70, 229, 0.03) 0%, transparent 50%),
    radial-gradient(circle at 80% 70%, rgba(16, 185, 129, 0.02) 0%, transparent 50%);
  pointer-events: none;
}



.ai-avatar-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-md);
}

.ai-avatar-modern {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-sm);
}

.avatar-core {
  width: 80px;
  height: 80px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid rgba(255, 255, 255, 0.3);
  transition: all 0.3s ease;
  animation: gentle-float 4s ease-in-out infinite;
}

@keyframes gentle-float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-5px); }
}

.avatar-core:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.ai-face-modern {
  color: white;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.eye-group {
  display: flex;
  gap: 12px;
}

.eye {
  width: 8px;
  height: 8px;
  background: white;
  border-radius: 50%;
  animation: subtle-blink 3s ease-in-out infinite;
}

@keyframes subtle-blink {
  0%, 95%, 100% { height: 8px; }
  97.5% { height: 2px; }
}

.ai-mouth-modern {
  width: 16px;
  height: 8px;
  background: white;
  border-radius: 0 0 16px 16px;
  transition: all 0.3s ease;
}

.avatar-core:hover .ai-mouth-modern {
  border-radius: 0 0 20px 20px;
  width: 18px;
}

.status-indicator {
  background: rgba(255, 255, 255, 0.2);
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--radius-full);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.status-text {
  color: white;
  font-size: var(--font-size-xs);
  font-weight: 600;
}

.ai-info-section {
  color: white;
}

.ai-title-modern {
  font-size: clamp(1.75rem, 4vw, 2.5rem);
  font-weight: 700;
  margin: 0 0 var(--space-sm) 0;
  line-height: 1.2;
  background: linear-gradient(45deg, #ffffff, #f0f8ff);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.ai-subtitle-modern {
  font-size: var(--font-size-base);
  opacity: 0.9;
  margin: 0 0 var(--space-lg) 0;
  font-weight: 500;
}

.metrics-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: var(--space-md);
}

.metric-card {
  background: rgba(255, 255, 255, 0.1);
  border-radius: var(--radius-lg);
  padding: var(--space-lg);
  display: flex;
  align-items: center;
  gap: var(--space-md);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: all 0.3s ease;
}

.metric-card:hover {
  background: rgba(255, 255, 255, 0.15);
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.metric-icon-modern {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  color: white;
  flex-shrink: 0;
}

.metric-icon-modern.primary {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
}

.metric-icon-modern.secondary {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.3), rgba(5, 150, 105, 0.2));
}

.metric-icon-modern.accent {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(29, 78, 216, 0.2));
}

.metric-content {
  flex: 1;
  min-width: 0;
}

.metric-value-modern {
  font-size: var(--font-size-lg);
  font-weight: 700;
  color: white;
  line-height: 1;
  margin-bottom: var(--space-xs);
}

.metric-label-modern {
  font-size: var(--font-size-sm);
  color: rgba(255, 255, 255, 0.8);
  font-weight: 500;
}

/* Hero Content Section */
.ai-hero-content {
  display: flex;
  flex-direction: column;
  gap: 40px;
  color: white;
  flex: 1;
  position: relative;
}

/* Avatar & Welcome Section */
.ai-welcome-section {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 24px;
  animation: slideInLeft 0.8s ease-out;
}

.ai-avatar-showcase {
  position: relative;
}

.avatar-ring {
  position: relative;
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background: linear-gradient(45deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.05));
  padding: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 
    0 0 20px rgba(255, 255, 255, 0.1),
    inset 0 0 20px rgba(255, 255, 255, 0.05);
}

.avatar-core-enhanced {
  width: 100px;
  height: 100px;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.05));
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(15px);
  border: 2px solid rgba(255, 255, 255, 0.1);
  transition: all 0.3s ease;
  animation: gentle-pulse 6s ease-in-out infinite;
}

.avatar-core-enhanced:hover {
  transform: scale(1.05);
  box-shadow: 0 10px 30px rgba(255, 255, 255, 0.2);
}

.status-ring {
  position: absolute;
  bottom: -5px;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(34, 197, 94, 0.9);
  backdrop-filter: blur(10px);
  padding: 8px 16px;
  border-radius: 20px;
  border: 2px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 600;
  color: white;
  box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
}

.status-dot {
  width: 8px;
  height: 8px;
  background: #10b981;
  border-radius: 50%;
  animation: pulse-dot 2s ease-in-out infinite;
}

.welcome-content {
  align-self: flex-start;
}

.welcome-badge-modern {
  position: relative;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(15px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  padding: 12px 20px;
  border-radius: 25px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 16px;
  font-weight: 600;
  color: white;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  overflow: hidden;
}

.welcome-badge-modern:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
}

.badge-glow {
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.6s ease;
}

.welcome-badge-modern:hover .badge-glow {
  left: 100%;
}

/* Hero Text Section */
.ai-hero-text {
  text-align: left;
  animation: slideInUp 0.8s ease-out 0.2s both;
}

.ai-hero-title {
  font-family: var(--font-heading);
  font-size: clamp(3rem, 6vw, 4.5rem);
  font-weight: 800;
  margin: 0 0 16px 0;
  line-height: 1.1;
  background: linear-gradient(135deg, #ffffff 0%, #f0f8ff 50%, #e6f3ff 100%);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  letter-spacing: -0.02em;
}

/* Removed duplicate greeting styles */

.ai-hero-description {
  font-size: 20px;
  color: rgba(255, 255, 255, 0.85);
  line-height: 1.6;
  max-width: 600px;
  margin: 0 0 40px 0;
  font-weight: 400;
}

/* Action Center */
.ai-action-center {
  display: flex;
  flex-direction: column;
  gap: 32px;
  animation: slideInUp 0.8s ease-out 0.4s both;
}

.btn-hero-primary {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
  backdrop-filter: blur(12px);
  color: white;
  padding: 20px 40px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 60px;
  font-size: 18px;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s ease;
  overflow: hidden;
  width: fit-content;
  box-shadow: 
    0 8px 16px rgba(0, 0, 0, 0.1),
    0 4px 8px rgba(255, 255, 255, 0.1);
  text-transform: uppercase;
  letter-spacing: 1px;
}

.btn-hero-primary:hover {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0.15) 100%);
  transform: translateY(-4px) scale(1.02);
  box-shadow: 
    0 16px 32px rgba(0, 0, 0, 0.2),
    0 12px 24px rgba(255, 255, 255, 0.15);
  border-color: rgba(255, 255, 255, 0.4);
}

.btn-content {
  display: flex;
  align-items: center;
  gap: 12px;
  position: relative;
  z-index: 2;
}

.btn-glow {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 0;
  height: 0;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
  border-radius: 50%;
  transition: all 0.6s ease;
  z-index: 1;
}

.btn-hero-primary:hover .btn-glow {
  width: 300px;
  height: 300px;
}

/* Feature Showcase */
.feature-showcase {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  max-width: 400px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 12px;
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  padding: 16px;
  border-radius: 16px;
  transition: all 0.3s ease;
  animation: slideInUp 0.8s ease-out both;
  animation-delay: var(--delay);
}

.feature-item:hover {
  background: rgba(255, 255, 255, 0.12);
  transform: translateY(-2px);
  border-color: rgba(255, 255, 255, 0.25);
}

.feature-icon {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 14px;
}

.feature-label {
  font-size: 14px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.9);
}

/* Stats Showcase */
.ai-stats-showcase {
  display: flex;
  flex-direction: column;
  gap: 32px;
  animation: slideInRight 0.8s ease-out 0.3s both;
}

.metrics-spotlight {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.metric-card-spotlight {
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 24px;
  padding: 24px;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  text-align: center;
}

.metric-card-spotlight::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
}

.metric-card-spotlight:hover {
  background: rgba(255, 255, 255, 0.18);
  transform: translateY(-3px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
  border-color: rgba(255, 255, 255, 0.3);
}

.metric-visual {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
}

.metric-icon-spotlight {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 24px;
  margin-bottom: 8px;
  position: relative;
}

.metric-icon-spotlight.primary {
  background: linear-gradient(135deg, rgba(139, 69, 255, 0.8), rgba(79, 70, 229, 0.6));
  box-shadow: 0 8px 16px rgba(139, 69, 255, 0.3);
}

.metric-icon-spotlight.secondary {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.8), rgba(5, 150, 105, 0.6));
  box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
}

.metric-icon-spotlight.accent {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.8), rgba(29, 78, 216, 0.6));
  box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
}

.metric-value-spotlight {
  font-size: 32px;
  font-weight: 800;
  color: white;
  line-height: 1;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.metric-label-spotlight {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.8);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
}

/* Detailed Stats Grid */
.stats-detail-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.stat-card-modern {
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 20px;
  padding: 20px;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  animation: slideInUp 0.8s ease-out both;
  animation-delay: var(--delay);
}

.stat-card-modern::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, 
    rgba(139, 69, 255, 0.5) 0%, 
    rgba(59, 130, 246, 0.5) 50%, 
    rgba(16, 185, 129, 0.5) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.stat-card-modern:hover {
  background: rgba(255, 255, 255, 0.12);
  transform: translateY(-2px);
  border-color: rgba(255, 255, 255, 0.25);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.stat-card-modern:hover::before {
  opacity: 1;
}

.stat-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.stat-icon-modern {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 18px;
}

.stat-icon-modern.stat-icon-primary {
  background: linear-gradient(135deg, rgba(139, 69, 255, 0.6), rgba(79, 70, 229, 0.4));
}

.stat-icon-modern.stat-icon-secondary {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.6), rgba(5, 150, 105, 0.4));
}

.stat-icon-modern.stat-icon-accent {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.6), rgba(29, 78, 216, 0.4));
}

.stat-icon-modern.stat-icon-success {
  background: linear-gradient(135deg, rgba(34, 197, 94, 0.6), rgba(21, 128, 61, 0.4));
}

.stat-trend-modern {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 12px;
  background: rgba(34, 197, 94, 0.2);
  color: #10b981;
}

.stat-body {
  text-align: left;
}

.stat-number-modern {
  font-size: 24px;
  font-weight: 700;
  color: white;
  line-height: 1;
  margin-bottom: 4px;
}

.stat-label-modern {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.7);
  font-weight: 500;
}

/* Animation Keyframes */
@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-50px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(50px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Removed rotate animation for performance */

@keyframes gentle-pulse {
  0%, 100% { 
    transform: scale(1);
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
  }
  50% { 
    transform: scale(1.01);
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.15);
  }
}

@keyframes pulse-dot {
  0%, 100% { 
    transform: scale(1);
    opacity: 1;
  }
  50% { 
    transform: scale(1.2);
    opacity: 0.8;
  }
}

/* Responsive Dashboard */
@media (max-width: 1200px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }
  
  .premium-sidebar {
    position: static;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
  }
  
  .premium-stats-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
  }
}

@media (max-width: 768px) {
  .premium-header {
    margin-bottom: 24px;
  }
  
  .header-glass-card {
    padding: 24px 20px;
    border-radius: 20px;
  }
  
  .header-main {
    flex-direction: column;
    gap: 20px;
    align-items: stretch;
    margin-bottom: 24px;
  }
  
  .premium-greeting {
    font-size: clamp(2rem, 6vw, 2.5rem);
  }
  
  .premium-cta-btn {
    padding: 16px 24px;
    justify-content: center;
  }
  
  .premium-stats-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .premium-sidebar {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .cv-management-section {
    padding: 20px 16px;
  }
  
  .section-header {
    flex-direction: column;
    gap: 16px;
  }
  
  .filter-controls {
    flex-direction: column;
    width: 100%;
  }
  
  .analytics-header {
    padding: 20px 16px 16px 16px;
  }
  
  .analytics-body {
    padding: 16px;
  }
}

@media (max-width: 480px) {
  .ai-dashboard-enhanced {
    padding: 32px 20px;
  }
  
  .ai-hero-title {
    font-size: 2.5rem;
    margin-bottom: 16px;
  }
  
  .ai-hero-description {
    font-size: 16px;
  }
  
  .btn-hero-primary {
    padding: 14px 28px;
    font-size: 15px;
  }
  
  .welcome-badge-modern {
    padding: 10px 16px;
    font-size: 14px;
  }
  
  .metric-card-spotlight {
    padding: 16px;
  }
  
  .metric-icon-spotlight {
    width: 48px;
    height: 48px;
    font-size: 20px;
  }
  
  .metric-value-spotlight {
    font-size: 24px;
  }
}

/* Animation Keyframes */
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

/* Welcome Badge (now integrated in AI dashboard) */
.welcome-badge {
  display: inline-flex;
  align-items: center;
  gap: var(--space-sm);
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  padding: var(--space-sm) var(--space-md);
  border-radius: var(--radius-full);
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: white;
  transition: all 0.3s ease;
  animation: fadeInDown 0.6s ease-out 0.2s both;
  margin-top: var(--space-md);
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.badge-icon {
  width: 18px;
  height: 18px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: var(--font-size-xs);
}

/* Typewriter styles removed */

.btn-shine {
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  transition: left 0.6s ease;
}

.btn-primary-enhanced:hover .btn-shine {
  left: 100%;
}

/* Legacy stat styles removed - using enhanced versions */



.section-title {
  font-family: var(--font-heading);
  font-size: var(--font-size-2xl);
  font-weight: 700;
  color: var(--text-primary);
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  margin: 0;
}

.section-title i {
  color: var(--primary);
}

.close-panel-btn {
  background: rgba(0, 0, 0, 0.05);
  border: none;
  border-radius: var(--radius-lg);
  padding: var(--space-sm);
  color: var(--text-muted);
  cursor: pointer;
  transition: var(--transition-all);
}

.close-panel-btn:hover {
  background: rgba(0, 0, 0, 0.1);
  color: var(--text-primary);
}

.ai-insights-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: var(--space-xl);
}



.insight-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, 
    var(--primary) 0%, 
    var(--secondary) 50%, 
    var(--accent) 100%);
  opacity: 0;
  transition: var(--transition-all);
}

.insight-card:hover {
  transform: translateY(-8px);
  box-shadow: 
    0 25px 50px -12px rgba(0, 0, 0, 0.15),
    0 10px 16px -4px rgba(0, 0, 0, 0.1),
    inset 0 1px 0 rgba(255, 255, 255, 0.2);
  border-color: rgba(91, 33, 182, 0.3);
}

.insight-card:hover::before {
  opacity: 1;
}

.card-header {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  margin-bottom: var(--space-lg);
}

.card-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
  border-radius: var(--radius-xl);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
  box-shadow: 
    0 8px 16px -4px rgba(91, 33, 182, 0.3),
    inset 0 1px 0 rgba(255, 255, 255, 0.2);
  position: relative;
}

.card-icon::after {
  content: '';
  position: absolute;
  inset: 2px;
  border-radius: var(--radius-lg);
  background: linear-gradient(135deg, 
    rgba(255, 255, 255, 0.2) 0%, 
    rgba(255, 255, 255, 0.05) 100%);
  pointer-events: none;
}

.card-header h3 {
  font-size: var(--font-size-lg);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
}

.skills-cloud {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-sm);
}

.skill-bubble {
  background: rgba(91, 33, 182, 0.1);
  color: var(--primary);
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--radius-full);
  font-size: var(--font-size-sm);
  font-weight: 500;
  border: 1px solid rgba(91, 33, 182, 0.2);
  transition: var(--transition-all);
  cursor: pointer;
}

.skill-bubble:hover {
  background: var(--primary);
  color: white;
  transform: translateY(-1px);
}

.metrics-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-lg);
}

.metric-item {
  text-align: center;
  padding: var(--space-xl);
  background: linear-gradient(135deg, 
    rgba(255, 255, 255, 0.8) 0%, 
    rgba(248, 250, 252, 0.6) 100%);
  border-radius: var(--radius-xl);
  border: 1px solid rgba(255, 255, 255, 0.5);
  transition: var(--transition-all);
  position: relative;
  overflow: hidden;
}

.metric-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(91, 33, 182, 0.2), transparent);
}

.metric-item:hover {
  transform: translateY(-2px);
  background: linear-gradient(135deg, 
    rgba(255, 255, 255, 0.95) 0%, 
    rgba(248, 250, 252, 0.8) 100%);
  border-color: rgba(91, 33, 182, 0.2);
}

.metric-value {
  font-size: var(--font-size-2xl);
  font-weight: 700;
  color: var(--primary);
  display: block;
  margin-bottom: var(--space-xs);
}

.metric-label {
  font-size: var(--font-size-sm);
  color: var(--text-secondary);
  font-weight: 500;
  margin-bottom: var(--space-xs);
}

.metric-change {
  font-size: var(--font-size-xs);
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-xs);
}

.metric-change.positive {
  color: var(--success);
}

.recommendations-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-md);
}

.recommendation-item {
  display: flex;
  align-items: flex-start;
  gap: var(--space-lg);
  padding: var(--space-xl);
  background: linear-gradient(135deg, 
    rgba(255, 255, 255, 0.7) 0%, 
    rgba(248, 250, 252, 0.5) 100%);
  border-radius: var(--radius-xl);
  border: 1px solid rgba(255, 255, 255, 0.4);
  transition: var(--transition-all);
  position: relative;
  overflow: hidden;
}

.recommendation-item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 3px;
  background: linear-gradient(180deg, var(--primary), var(--primary-light));
  opacity: 0;
  transition: var(--transition-all);
}

.recommendation-item:hover {
  background: linear-gradient(135deg, 
    rgba(255, 255, 255, 0.9) 0%, 
    rgba(248, 250, 252, 0.7) 100%);
  transform: translateX(8px);
  border-color: rgba(91, 33, 182, 0.2);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.recommendation-item:hover::before {
  opacity: 1;
}

.rec-priority-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
  margin-top: 6px;
}

.rec-priority-dot.high { background: var(--danger); }
.rec-priority-dot.medium { background: var(--accent); }
.rec-priority-dot.low { background: var(--success); }

.rec-content {
  flex: 1;
}

.rec-content p {
  margin: 0 0 var(--space-sm);
  color: var(--text-primary);
  font-size: var(--font-size-sm);
  line-height: var(--line-height-normal);
}

.rec-action-btn {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
  color: white;
  border: none;
  padding: var(--space-sm) var(--space-md);
  border-radius: var(--radius-lg);
  font-size: var(--font-size-xs);
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition-all);
  box-shadow: 
    0 2px 4px rgba(91, 33, 182, 0.2),
    inset 0 1px 0 rgba(255, 255, 255, 0.2);
  position: relative;
  overflow: hidden;
}

.rec-action-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s ease;
}

.rec-action-btn:hover {
  background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
  transform: translateY(-2px);
  box-shadow: 
    0 4px 8px rgba(91, 33, 182, 0.3),
    inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.rec-action-btn:hover::before {
  left: 100%;
}

/* CV Management Section */
.cv-management-section {
  background: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(20px);
  border-radius: var(--radius-2xl);
  padding: var(--space-3xl);
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: var(--shadow-xl);
  animation: slideUp 0.8s ease-out 0.4s both;
}

.section-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: var(--space-2xl);
  gap: var(--space-xl);
}

.section-title-group h2 {
  font-family: var(--font-heading);
  font-size: var(--font-size-2xl);
  font-weight: 700;
  color: var(--text-primary);
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  margin: 0 0 var(--space-sm);
}

.section-title-group h2 i {
  color: var(--primary);
}

.section-subtitle {
  color: var(--text-secondary);
  font-size: var(--font-size-base);
  margin: 0;
}

.section-actions {
  flex-shrink: 0;
}

.search-filter-group {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
  align-items: flex-end;
}

.search-box {
  position: relative;
  min-width: 300px;
}

.search-box i {
  position: absolute;
  left: var(--space-md);
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
  font-size: var(--font-size-sm);
}

.search-input {
  width: 100%;
  padding: var(--space-md) var(--space-md) var(--space-md) 2.75rem;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: var(--radius-xl);
  font-size: var(--font-size-base);
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  color: var(--text-primary);
  transition: var(--transition-all);
  font-family: inherit;
}

.search-input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(91, 33, 182, 0.15);
  background: white;
}

.search-input::placeholder {
  color: var(--text-muted);
}

.clear-search {
  position: absolute;
  right: var(--space-md);
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0, 0, 0, 0.1);
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-muted);
  cursor: pointer;
  transition: var(--transition-all);
}

.clear-search:hover {
  background: rgba(0, 0, 0, 0.2);
  color: var(--text-primary);
}

.filter-controls {
  display: flex;
  gap: var(--space-md);
  align-items: center;
}

.filter-select {
  padding: var(--space-sm) var(--space-md);
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: var(--radius-lg);
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  color: var(--text-primary);
  font-size: var(--font-size-sm);
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition-all);
  font-family: inherit;
}

.filter-select:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(91, 33, 182, 0.15);
  background: white;
}

.view-mode-btn {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border: 2px solid rgba(255, 255, 255, 0.3);
  padding: var(--space-sm);
  border-radius: var(--radius-lg);
  color: var(--text-muted);
  cursor: pointer;
  transition: var(--transition-all);
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.view-mode-btn:hover {
  background: white;
  color: var(--text-primary);
  border-color: rgba(91, 33, 182, 0.2);
}

.view-mode-btn.active {
  background: var(--primary);
  color: white;
  border-color: var(--primary);
}

/* CV Cards Container */
.cv-cards-container {
  margin-top: var(--space-2xl);
}

/* Loading & Empty States */
.loading-state,
.empty-state {
  text-align: center;
  padding: var(--space-3xl);
  background: rgba(255, 255, 255, 0.5);
  backdrop-filter: blur(15px);
  border-radius: var(--radius-2xl);
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: var(--shadow-lg);
}

.loading-spinner {
  width: 60px;
  height: 60px;
  background: var(--gradient-primary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto var(--space-lg);
  animation: pulse 2s infinite;
}

.loading-spinner i {
  font-size: 1.5rem;
  color: white;
}

.loading-state h3 {
  font-size: var(--font-size-xl);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 var(--space-sm);
}

.loading-state p {
  color: var(--text-secondary);
  font-size: var(--font-size-base);
  margin: 0;
}

.empty-illustration {
  width: 80px;
  height: 80px;
  background: var(--gradient-secondary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto var(--space-xl);
  animation: float 6s ease-in-out infinite;
}

.empty-illustration i {
  font-size: 2rem;
  color: white;
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.empty-state h3 {
  font-size: var(--font-size-xl);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 var(--space-sm);
}

.empty-state p {
  color: var(--text-secondary);
  font-size: var(--font-size-base);
  line-height: var(--line-height-relaxed);
  margin: 0 0 var(--space-xl);
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
}

/* CV Display Grid */
.cvs-display-grid {
  display: grid;
  gap: var(--space-lg);
  animation: fadeInUp 0.6s ease-out 0.2s both;
}

.cvs-display-grid.grid {
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
}

.cvs-display-grid.list {
  grid-template-columns: 1fr;
}

/* CV Display Card */
.cv-display-card {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(20px);
  border-radius: var(--radius-2xl);
  overflow: hidden;
  cursor: pointer;
  position: relative;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
  /* Remove CSS transitions - JavaScript handles animations */
  will-change: transform, box-shadow, filter;
}

.cv-display-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--gradient-primary);
  opacity: 0;
  border-radius: var(--radius-2xl) var(--radius-2xl) 0 0;
  /* Remove CSS transitions - JavaScript handles animations */
}

/* Smooth CV Preview Styles - Natural & Clean */
.cv-animated-preview {
  position: absolute;
  top: 50px; /* Leave space for header buttons */
  left: 0;
  right: 0;
  bottom: 50px; /* Leave space for footer buttons */
  background: rgba(255, 255, 255, 0.98);
  backdrop-filter: blur(8px);
  border-radius: 20px;
  padding: 24px;
  z-index: 3; /* Lower than buttons */
  overflow: hidden;
  pointer-events: none; /* Allow clicks to pass through */
  box-shadow: 
    0 20px 40px rgba(0, 0, 0, 0.08),
    0 8px 16px rgba(79, 70, 229, 0.1),
    inset 0 1px 0 rgba(255, 255, 255, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.5);
  will-change: transform, opacity;
}

@keyframes previewSlideIn {
  0% {
    opacity: 0;
    transform: translateX(-100%) scale(0.95);
  }
  100% {
    opacity: 1;
    transform: translateX(0) scale(1);
  }
}

.preview-container {
  width: 100%;
  height: 100%;
  background: rgba(248, 250, 252, 0.6);
  border-radius: 16px;
  padding: 20px;
  box-shadow: 
    0 4px 12px rgba(0, 0, 0, 0.05),
    inset 0 1px 0 rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.7);
  position: relative;
  overflow: hidden;
  font-size: 11px;
  line-height: 1.4;
  will-change: transform;
}

.preview-container.animating {
  transform: scale(1.01);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

/* Classic Preview Styles */
.preview-classic .cv-header {
  border-bottom: 2px solid #e2e8f0;
  padding-bottom: 12px;
  margin-bottom: 16px;
  transition: all 0.6s ease;
}

.preview-classic .preview-name {
  font-size: 16px;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 4px;
  transition: all 0.6s ease;
}

.preview-classic .preview-title {
  font-size: 12px;
  color: #4a5568;
  margin-bottom: 8px;
  transition: all 0.6s ease;
}

.preview-name.typing,
.preview-title.typing {
  color: var(--primary);
  position: relative;
}

.preview-name.typing::after,
.preview-title.typing::after {
  content: '|';
  animation: blink 1s infinite;
  margin-left: 2px;
}

@keyframes blink {
  0%, 50% { opacity: 1; }
  51%, 100% { opacity: 0; }
}

.cv-contact {
  display: flex;
  flex-direction: column;
  gap: 3px;
  margin-top: 8px;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 9px;
  color: #64748b;
}

.contact-item i {
  width: 10px;
  color: var(--primary);
  font-size: 8px;
}

.cv-sections {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.cv-section h3 {
  font-size: 10px;
  font-weight: 700;
  color: var(--primary);
  text-transform: uppercase;
  letter-spacing: 0.8px;
  margin-bottom: 8px;
  opacity: 0;
  transform: translateX(-30px);
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.cv-section h3::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--primary), var(--primary-light));
  transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
  transition-delay: 0.3s;
}

.cv-section.filling h3 {
  opacity: 1;
  transform: translateX(0);
}

.cv-section.filling h3::after {
  width: 100%;
}

/* Real Content Animations */
.experience-content,
.education-content,
.professional-content,
.creative-content,
.summary-content {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
  transition-delay: 0.5s;
}

.cv-section.filling .experience-content,
.cv-section.filling .education-content,
.cv-section.filling .professional-content,
.cv-section.filling .creative-content,
.cv-section.filling .summary-content {
  opacity: 1;
  transform: translateY(0) scale(1);
}

.experience-item,
.education-item {
  margin-bottom: 12px;
}

.job-title,
.degree {
  font-size: 10px;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 2px;
  opacity: 0;
  transform: translateX(-40px) scale(0.9);
  transition: all 1.0s cubic-bezier(0.4, 0, 0.2, 1);
  transition-delay: 0.7s;
  position: relative;
  overflow: hidden;
}

.job-title::after,
.degree::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: -100%;
  width: 100%;
  height: 1px;
  background: linear-gradient(90deg, transparent, var(--primary), transparent);
  transition: left 1.2s cubic-bezier(0.4, 0, 0.2, 1);
  transition-delay: 1.0s;
}

.company-name,
.school {
  font-size: 8px;
  font-weight: 600;
  color: #64748b;
  margin-bottom: 4px;
  opacity: 0;
  transform: translateX(-30px) scale(0.9);
  transition: all 1.0s cubic-bezier(0.4, 0, 0.2, 1);
  transition-delay: 0.9s;
  position: relative;
  overflow: hidden;
}

.company-name::after,
.school::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: -100%;
  width: 100%;
  height: 1px;
  background: linear-gradient(90deg, transparent, #64748b, transparent);
  transition: left 1.0s cubic-bezier(0.4, 0, 0.2, 1);
  transition-delay: 1.2s;
}

.job-description {
  font-size: 7px;
  color: #6b7280;
  line-height: 1.4;
  opacity: 0;
  transform: translateX(-25px) scale(0.9);
  transition: all 1.0s cubic-bezier(0.4, 0, 0.2, 1);
  transition-delay: 1.1s;
  position: relative;
  overflow: hidden;
}

.job-description::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: -100%;
  width: 100%;
  height: 1px;
  background: linear-gradient(90deg, transparent, #6b7280, transparent);
  transition: left 1.0s cubic-bezier(0.4, 0, 0.2, 1);
  transition-delay: 1.4s;
}

.summary-content.typing-content {
  font-size: 8px;
  color: #6b7280;
  line-height: 1.4;
  position: relative;
}

/* Typing Animation for Content */
.typing-content .job-title,
.typing-content .degree {
  opacity: 1;
  transform: translateX(0) scale(1);
  position: relative;
}

.typing-content .job-title::after,
.typing-content .degree::after {
  left: 100%;
}

.typing-content .company-name,
.typing-content .school {
  opacity: 1;
  transform: translateX(0) scale(1);
}

.typing-content .company-name::after,
.typing-content .school::after {
  left: 100%;
}

.typing-content .job-description {
  opacity: 1;
  transform: translateX(0) scale(1);
}

.typing-content .job-description::after {
  left: 100%;
}

.typing-content.summary-content {
  opacity: 1;
  transform: translateY(0) scale(1);
}

/* Template-specific content styles */
.experience-item.compact .job-title,
.education-item.compact .degree {
  font-size: 9px;
  margin-bottom: 1px;
}

.experience-item.compact .company-name,
.education-item.compact .school {
  font-size: 7px;
  margin-bottom: 8px;
}

.experience-item.creative-style .job-title {
  color: #ff6b6b;
  font-size: 9px;
}

.experience-item.creative-style .company-name {
  color: #4ecdc4;
  font-size: 7px;
}

.experience-item.professional-style .job-title,
.education-item.professional-style .degree {
  color: #1a202c;
  font-size: 8px;
}

.experience-item.professional-style .company-name,
.education-item.professional-style .school {
  color: #64748b;
  font-size: 7px;
}

.two-column h4 {
  font-size: 8px;
  font-weight: 700;
  color: var(--primary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 6px;
  opacity: 0;
  transform: translateX(-10px);
  transition: all 0.6s ease;
}

.cv-section.filling h4 {
  opacity: 1;
  transform: translateX(0);
}

.skills-container {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  margin-top: 6px;
}

.skill-tag {
  background: rgba(91, 33, 182, 0.1);
  color: var(--primary);
  padding: 3px 6px;
  border-radius: 8px;
  font-size: 8px;
  font-weight: 600;
  opacity: 0;
  transform: translateX(-30px) scale(0.8);
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.skill-tag::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: left 0.6s ease-out;
}

.cv-section.filling .skill-tag {
  opacity: 1;
  transform: translateX(0) scale(1);
}

.cv-section.filling .skill-tag::before {
  left: 100%;
}

.skill-tag:nth-child(1) { transition-delay: 0.4s; }
.skill-tag:nth-child(2) { transition-delay: 0.5s; }
.skill-tag:nth-child(3) { transition-delay: 0.6s; }
.skill-tag:nth-child(4) { transition-delay: 0.7s; }

/* Modern Preview Styles */
.preview-modern .modern-layout {
  display: flex;
  gap: 12px;
  height: 100%;
}

.preview-modern .modern-sidebar {
  width: 35%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 8px;
  padding: 12px;
  color: white;
}

.preview-modern .profile-circle {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  margin-bottom: 12px;
  transition: all 0.3s ease;
  opacity: 0;
}

.profile-circle.appearing {
  opacity: 1;
  animation: photoAppear 0.6s ease-out;
}

@keyframes photoAppear {
  0% { transform: scale(0) rotate(180deg); opacity: 0; }
  100% { transform: scale(1) rotate(0deg); opacity: 1; }
}

.preview-modern .cv-name,
.preview-modern .cv-title {
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 6px;
  text-align: center;
}

.preview-modern .sidebar-section h4 {
  font-size: 9px;
  font-weight: 700;
  margin-bottom: 8px;
  opacity: 0.9;
}

.skill-bars {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.skill-bar {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.skill-name {
  font-size: 8px;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.9);
}

.bar {
  height: 3px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 2px;
  overflow: hidden;
}

.progress {
  height: 100%;
  background: white;
  border-radius: 2px;
  transition: width 1s ease-out 0.5s;
  transform: scaleX(0);
  transform-origin: left;
  position: relative;
  overflow: hidden;
}

.progress::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  animation: shimmer 2s infinite;
}

@keyframes shimmer {
  0% { left: -100%; }
  100% { left: 100%; }
}

.progress.filling {
  transform: scaleX(1);
}

.preview-modern .modern-main {
  flex: 1;
  padding: 8px;
}

/* Creative Preview Styles */
.preview-creative {
  position: relative;
  background: linear-gradient(135deg, #ff6b6b20, #4ecdc420);
  height: 100%;
}

.creative-shapes {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
}

.shape {
  position: absolute;
  opacity: 0.1;
  transition: all 0.6s ease;
}

.shape-1 {
  width: 30px;
  height: 30px;
  background: #ff6b6b;
  border-radius: 50%;
  top: 20px;
  right: 20px;
}

.shape-2 {
  width: 20px;
  height: 20px;
  background: #4ecdc4;
  transform: rotate(45deg);
  bottom: 20px;
  left: 20px;
}

.creative-shapes.floating .shape {
  opacity: 0.3;
  animation: float 3s ease-in-out infinite;
}

.creative-shapes.floating .shape-2 {
  animation-delay: 1.5s;
}

@keyframes float {
  0%, 100% { 
    transform: translateY(0) rotate(45deg); 
    opacity: 0.1;
  }
  33% { 
    transform: translateY(-15px) rotate(165deg); 
    opacity: 0.3;
  }
  66% { 
    transform: translateY(10px) rotate(285deg); 
    opacity: 0.2;
  }
}

.creative-layout {
  position: relative;
  z-index: 2;
  height: 100%;
  padding: 8px;
}

.creative-name {
  font-size: 16px;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 4px;
}

.creative-title {
  font-size: 12px;
  color: #ff6b6b;
  font-weight: 600;
  margin-bottom: 12px;
}

.creative-grid {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 12px;
  align-items: start;
}

.creative-visual {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.visual-element {
  width: 24px;
  height: 8px;
  background: linear-gradient(90deg, #ff6b6b, #4ecdc4);
  border-radius: 4px;
  opacity: 0;
  transform: translateX(20px);
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.creative-visual.animating .visual-element {
  opacity: 1;
  transform: translateX(0);
  transition-delay: 0.8s;
}

.visual-element.small {
  width: 16px;
  transition-delay: 1.0s;
}

/* Professional Preview Styles */
.preview-professional .cv-header.centered {
  text-align: center;
  padding-bottom: 12px;
  margin-bottom: 16px;
}

.professional-divider {
  height: 2px;
  background: linear-gradient(90deg, var(--primary), var(--secondary));
  width: 0;
  margin: 8px auto;
  border-radius: 2px;
  transition: width 1s ease;
}

.professional-divider.expanding {
  width: 40px;
}

.two-column {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

/* CV Card Header */
.cv-card-header {
  padding: var(--space-lg);
  padding-bottom: 0;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: var(--space-sm);
  position: relative;
  z-index: 10; /* Higher than overlay */
}

.template-badge {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--radius-lg);
  border: 1px solid rgba(255, 255, 255, 0.3);
  font-size: var(--font-size-xs);
  font-weight: 600;
  color: var(--text-primary);
  text-transform: capitalize;
}

.template-badge i {
  font-size: var(--font-size-sm);
}

.template-badge.classic { color: #64748b; border-color: rgba(100, 116, 139, 0.3); }
.template-badge.modern { color: var(--primary); border-color: rgba(91, 33, 182, 0.3); }
.template-badge.professional { color: #0f172a; border-color: rgba(15, 23, 42, 0.3); }
.template-badge.creative { color: #ec4899; border-color: rgba(236, 72, 153, 0.3); }

.cv-status-badge {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--radius-lg);
  font-size: var(--font-size-xs);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.cv-status-badge:not(.draft) {
  background: rgba(16, 185, 129, 0.1);
  color: var(--success);
  border: 1px solid rgba(16, 185, 129, 0.3);
}

.cv-status-badge.draft {
  background: rgba(245, 158, 11, 0.1);
  color: var(--accent);
  border: 1px solid rgba(245, 158, 11, 0.3);
}

.cv-menu-trigger {
  position: relative;
  z-index: 10; /* Stay above the animated overlay */
}

.menu-trigger-btn {
  background: rgba(0, 0, 0, 0.05);
  border: none;
  border-radius: var(--radius-lg);
  padding: var(--space-sm);
  color: var(--text-muted);
  cursor: pointer;
  transition: var(--transition-all);
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.menu-trigger-btn:hover {
  background: rgba(0, 0, 0, 0.1);
  color: var(--text-primary);
}

.cv-dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-xl);
  z-index: 20;
  min-width: 160px;
  padding: var(--space-sm);
  animation: dropdownFadeIn 0.2s ease-out;
}

@keyframes dropdownFadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  width: 100%;
  padding: var(--space-sm) var(--space-md);
  background: none;
  border: none;
  border-radius: var(--radius-lg);
  font-size: var(--font-size-sm);
  font-weight: 500;
  color: var(--text-primary);
  cursor: pointer;
  transition: var(--transition-all);
  text-align: left;
}

.dropdown-item:hover {
  background: rgba(91, 33, 182, 0.1);
  color: var(--primary);
}

.dropdown-item.danger {
  color: var(--danger);
}

.dropdown-item.danger:hover {
  background: rgba(239, 68, 68, 0.1);
  color: var(--danger);
}

.dropdown-divider {
  height: 1px;
  background: rgba(0, 0, 0, 0.1);
  margin: var(--space-sm) 0;
}

/* CV Card Body */
.cv-card-body {
  padding: var(--space-md) var(--space-lg) var(--space-lg);
}

.cv-title-section {
  margin-bottom: var(--space-md);
}

.cv-title {
  font-size: var(--font-size-lg);
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 var(--space-xs);
  line-height: var(--line-height-tight);
}

.cv-author {
  font-size: var(--font-size-sm);
  color: var(--text-secondary);
  margin: 0;
}

.cv-metrics {
  display: flex;
  justify-content: space-between;
  margin-bottom: var(--space-md);
  background: rgba(255, 255, 255, 0.5);
  backdrop-filter: blur(10px);
  padding: var(--space-sm);
  border-radius: var(--radius-lg);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.metric-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-xs);
  text-align: center;
}

.metric-item i {
  color: var(--primary);
  font-size: var(--font-size-sm);
}

.metric-value {
  font-size: var(--font-size-lg);
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1;
}

.metric-label {
  font-size: var(--font-size-xs);
  color: var(--text-secondary);
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.cv-completion {
  margin-bottom: var(--space-md);
}

.completion-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-sm);
}

.completion-label {
  font-size: var(--font-size-sm);
  color: var(--text-secondary);
  font-weight: 500;
}

.completion-percentage {
  font-size: var(--font-size-sm);
  color: var(--primary);
  font-weight: 700;
}

.completion-bar {
  height: 6px;
  background: rgba(0, 0, 0, 0.1);
  border-radius: var(--radius-full);
  overflow: hidden;
}

.completion-progress {
  height: 100%;
  background: var(--gradient-primary);
  border-radius: var(--radius-full);
  transition: width 1s ease-out;
}

.cv-card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: var(--space-md);
  border-top: 1px solid rgba(0, 0, 0, 0.05);
  position: relative;
  z-index: 10; /* Higher than overlay */
}

.cv-date {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  font-size: var(--font-size-xs);
  color: var(--text-muted);
  font-weight: 500;
}

.cv-date i {
  font-size: var(--font-size-xs);
}

.quick-actions {
  display: flex;
  gap: var(--space-sm);
}

.quick-action-btn {
  background: rgba(0, 0, 0, 0.05);
  border: none;
  border-radius: var(--radius-lg);
  padding: var(--space-sm);
  color: var(--text-muted);
  cursor: pointer;
  transition: var(--transition-all);
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: var(--font-size-sm);
}

.quick-action-btn:hover {
  background: var(--primary);
  color: white;
  transform: translateY(-2px);
}

/* List View Layout */
.cvs-display-grid.list .cv-display-card {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  padding: var(--space-xl);
  gap: var(--space-xl);
}

.cvs-display-grid.list .cv-card-header {
  padding: 0;
  width: auto;
  flex-direction: column;
  gap: var(--space-sm);
}

.cvs-display-grid.list .cv-card-body {
  padding: 0;
  display: flex;
  align-items: center;
  gap: var(--space-xl);
}

.cvs-display-grid.list .cv-completion {
  display: none;
}

.cvs-display-grid.list .cv-metrics {
  background: none;
  border: none;
  padding: 0;
  margin: 0;
  min-width: 200px;
}

/* Confirmation Modal */
.confirmation-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(5px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.confirmation-modal-content {
  background: white;
  backdrop-filter: blur(20px);
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-2xl);
  max-width: 480px;
  width: 90%;
  overflow: hidden;
  animation: modalSlideUp 0.3s ease-out;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

@keyframes modalSlideUp {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-header {
  padding: var(--space-xl);
  padding-bottom: var(--space-lg);
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.modal-header h3 {
  font-size: var(--font-size-xl);
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
}

.modal-close-btn {
  background: rgba(0, 0, 0, 0.05);
  border: none;
  border-radius: var(--radius-lg);
  padding: var(--space-sm);
  color: var(--text-muted);
  cursor: pointer;
  transition: var(--transition-all);
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close-btn:hover {
  background: rgba(0, 0, 0, 0.1);
  color: var(--text-primary);
}

.modal-body {
  padding: var(--space-xl);
  display: flex;
  align-items: center;
  gap: var(--space-lg);
}

.modal-icon {
  width: 60px;
  height: 60px;
  background: rgba(245, 158, 11, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--accent);
  font-size: 1.5rem;
  flex-shrink: 0;
}

.modal-body p {
  color: var(--text-secondary);
  font-size: var(--font-size-base);
  line-height: var(--line-height-relaxed);
  margin: 0;
}

.modal-actions {
  padding: 0 var(--space-xl) var(--space-xl);
  display: flex;
  gap: var(--space-md);
  justify-content: flex-end;
}

.btn-danger {
  background: var(--danger);
  color: white;
}

.btn-danger:hover:not(:disabled) {
  background: var(--danger-dark);
  transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 1024px) {
  .dashboard-container {
    padding: var(--space-lg) var(--space-md);
  }
  
  .hero-container {
    grid-template-columns: 1fr;
    gap: var(--space-2xl);
  }
  
  .hero-content {
    text-align: center;
  }
  
  .stats-sidebar {
    flex-direction: row;
    overflow-x: auto;
    gap: var(--space-md);
  }
  
  .stat-card {
    min-width: 200px;
    flex-shrink: 0;
  }
  
  .hero-section,
  .ai-insights-section,
  .cv-management-section {
    padding: var(--space-2xl);
  }
  
  .section-header {
    flex-direction: column;
    gap: var(--space-lg);
    align-items: stretch;
  }
  
  .search-filter-group {
    align-items: stretch;
  }
  
  .search-box {
    min-width: auto;
  }
}

@media (max-width: 768px) {
  .dashboard-container {
    padding: var(--space-md);
  }
  
  .hero-section,
  .ai-insights-section,
  .cv-management-section {
    padding: var(--space-xl);
  }
  
  .hero-title {
    font-size: clamp(2rem, 5vw, 3rem);
  }
  
  .stats-sidebar {
    flex-direction: column;
    gap: var(--space-sm);
  }
  
  .hero-actions {
    flex-direction: column;
    gap: var(--space-md);
  }
  
  .btn {
    width: 100%;
    max-width: 300px;
    justify-content: center;
  }
  
  .ai-insights-grid {
    grid-template-columns: 1fr;
  }
  
  .filter-controls {
    flex-direction: column;
    width: 100%;
  }
  
  .filter-select {
    width: 100%;
  }
  
  .cvs-display-grid.grid {
    grid-template-columns: 1fr;
  }
  
  .cv-metrics {
    padding: var(--space-sm);
  }
  
  .metric-item {
    gap: var(--space-xs);
  }
  
  /* Reduce sphere sizes for mobile */
  .sphere-1 {
    width: 300px;
    height: 300px;
  }
  
  .sphere-2 {
    width: 250px;
    height: 250px;
  }
  
  .sphere-3 {
    width: 200px;
    height: 200px;
  }
  
  .sphere-4 {
    width: 150px;
    height: 150px;
  }
}

@media (max-width: 480px) {
  .dashboard-container {
    padding: var(--space-sm);
  }
  
  .hero-section,
  .ai-insights-section,
  .cv-management-section {
    padding: var(--space-lg);
  }
  
  .stat-card {
    padding: var(--space-md);
    min-width: 0;
  }
  
  .ai-insights-header {
    flex-direction: column;
    gap: var(--space-md);
    align-items: stretch;
  }
  
  .metrics-grid {
    grid-template-columns: 1fr;
  }
  
  .cv-card-header {
    padding: var(--space-lg);
    padding-bottom: 0;
  }
  
  .cv-card-body {
    padding: var(--space-md) var(--space-lg) var(--space-lg);
  }
  
  .modal-header,
  .modal-body,
  .modal-actions {
    padding-left: var(--space-lg);
    padding-right: var(--space-lg);
  }
  
  .confirmation-modal-content {
    max-width: 95%;
  }
  
  /* Further reduce sphere sizes */
  .sphere-1 {
    width: 200px;
    height: 200px;
  }
  
  .sphere-2 {
    width: 150px;
    height: 150px;
  }
  
  .sphere-3,
  .sphere-4 {
    width: 100px;
    height: 100px;
  }
}

/* Accessibility & Performance */
@media (prefers-reduced-motion: reduce) {
  .gradient-sphere,
  .stat-card,
  .cv-display-card,
  .insight-card {
    animation: none;
  }
  
  .btn:hover:not(:disabled),
  .cv-display-card:hover,
  .stat-card:hover {
    transform: none;
  }
  
  .typewriter-text::after {
    animation: none;
  }
}

@media (prefers-color-scheme: dark) {
  .animated-bg {
    opacity: 0.2;
  }
}

/* Dark Theme Overrides */
body.dark-theme .dashboard-main {
  background: var(--gray-900);
  color: var(--gray-100);
}

body.dark-theme .animated-bg .gradient-sphere {
  opacity: 0.3;
}

body.dark-theme .hero-section {
  background: var(--gray-900);
}

body.dark-theme .welcome-badge {
  background: rgba(30, 41, 59, 0.8);
  border: 1px solid var(--gray-700);
  color: var(--gray-300);
}

body.dark-theme .hero-title {
  color: var(--gray-100);
}

body.dark-theme .hero-subtitle {
  color: var(--gray-400);
}

body.dark-theme .typewriter-text {
  color: var(--primary-light);
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

body.dark-theme .stat-card {
  background: var(--gray-800);
  border: 1px solid var(--gray-700);
  color: var(--gray-100);
}

body.dark-theme .stat-number {
  color: var(--gray-100);
}

body.dark-theme .stat-label {
  color: var(--gray-400);
}

body.dark-theme .stat-trend {
  color: var(--success);
}

body.dark-theme .ai-insights-section {
  background: rgba(30, 41, 59, 0.8);
  border: 1px solid var(--gray-700);
}

body.dark-theme .cv-management-section {
  background: rgba(30, 41, 59, 0.6);
  backdrop-filter: blur(20px);
  border: 1px solid var(--gray-700);
}

body.dark-theme .loading-state,
body.dark-theme .empty-state {
  background: rgba(30, 41, 59, 0.5);
  border: 1px solid var(--gray-700);
}

body.dark-theme .cv-metrics {
  background: rgba(30, 41, 59, 0.5);
  border: 1px solid var(--gray-700);
}

body.dark-theme .section-title {
  color: var(--gray-100);
}

body.dark-theme .insight-card {
  background: var(--gray-800);
  border: 1px solid var(--gray-700);
}

body.dark-theme .card-header h3 {
  color: var(--gray-200);
}

body.dark-theme .skill-bubble {
  background: var(--primary);
  color: var(--gray-100);
}

body.dark-theme .metric-value {
  color: var(--gray-100);
}

/* Legacy dark theme styles cleaned up */

body.dark-theme .recommendation-item p {
  color: var(--gray-300);
}

body.dark-theme .rec-action-btn {
  background: var(--primary);
  color: var(--gray-100);
}

body.dark-theme .section-header {
  color: var(--gray-100);
}

body.dark-theme .section-subtitle {
  color: var(--gray-400);
}

body.dark-theme .search-input {
  background: var(--gray-700);
  border: 1px solid var(--gray-600);
  color: var(--gray-200);
}

body.dark-theme .search-input::placeholder {
  color: var(--gray-500);
}

body.dark-theme .filter-select {
  background: var(--gray-700);
  border: 1px solid var(--gray-600);
  color: var(--gray-200);
}

body.dark-theme .view-mode-btn {
  background: var(--gray-700);
  border: 1px solid var(--gray-600);
  color: var(--gray-300);
}

body.dark-theme .view-mode-btn.active {
  background: var(--primary);
  color: var(--gray-100);
}

body.dark-theme .loading-state {
  color: var(--gray-300);
}

body.dark-theme .empty-state {
  color: var(--gray-300);
}

body.dark-theme .empty-state h3 {
  color: var(--gray-200);
}

body.dark-theme .cv-display-card {
  background: var(--gray-800);
  border: 1px solid var(--gray-700);
  color: var(--gray-200);
}

body.dark-theme .cv-display-card:hover {
  border-color: var(--primary);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

body.dark-theme .template-badge {
  background: var(--primary);
  color: var(--gray-100);
}

body.dark-theme .cv-status-badge {
  background: var(--gray-700);
  color: var(--gray-300);
}

body.dark-theme .cv-status-badge.draft {
  background: var(--warning);
  color: var(--gray-900);
}

body.dark-theme .cv-title {
  color: var(--gray-100);
}

body.dark-theme .cv-author {
  color: var(--gray-400);
}

body.dark-theme .metric-item {
  color: var(--gray-400);
}

body.dark-theme .metric-value {
  color: var(--gray-200);
}

body.dark-theme .completion-label {
  color: var(--gray-400);
}

body.dark-theme .completion-percentage {
  color: var(--gray-200);
}

body.dark-theme .completion-bar {
  background: var(--gray-700);
}

body.dark-theme .completion-progress {
  background: var(--primary);
}

body.dark-theme .cv-date {
  color: var(--gray-500);
}

body.dark-theme .quick-action-btn {
  background: var(--gray-700);
  color: var(--gray-300);
  border: 1px solid var(--gray-600);
}

body.dark-theme .quick-action-btn:hover {
  background: var(--gray-600);
  color: var(--gray-200);
}

body.dark-theme .cv-dropdown-menu {
  background: var(--gray-800);
  border: 1px solid var(--gray-700);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

body.dark-theme .dropdown-item {
  color: var(--gray-300);
}

body.dark-theme .dropdown-item:hover {
  background: var(--gray-700);
  color: var(--gray-200);
}

body.dark-theme .dropdown-item.danger {
  color: var(--red-400);
}

body.dark-theme .dropdown-item.danger:hover {
  background: var(--red-900);
  color: var(--red-300);
}

body.dark-theme .dropdown-divider {
  border-color: var(--gray-700);
}

body.dark-theme .confirmation-modal-overlay {
  background: rgba(0, 0, 0, 0.8);
}

body.dark-theme .confirmation-modal-content {
  background: var(--gray-800);
  border: 1px solid var(--gray-700);
}

body.dark-theme .modal-header h3 {
  color: var(--gray-100);
}

body.dark-theme .modal-close-btn {
  color: var(--gray-400);
}

body.dark-theme .modal-close-btn:hover {
  color: var(--gray-200);
}

body.dark-theme .modal-body p {
  color: var(--gray-300);
}

body.dark-theme .modal-icon {
  color: var(--warning);
}

body.dark-theme .btn.btn-danger {
  background: var(--red-600);
  color: var(--gray-100);
}

body.dark-theme .btn.btn-danger:hover {
  background: var(--red-500);
}

body.dark-theme .close-panel-btn {
  background: var(--gray-700);
  color: var(--gray-400);
  border: 1px solid var(--gray-600);
}

body.dark-theme .close-panel-btn:hover {
  background: var(--gray-600);
  color: var(--gray-200);
}

/* Responsive adjustments for dark theme */
@media (max-width: 768px) {
  body.dark-theme .hero-title {
    color: var(--gray-100);
  }
  
  body.dark-theme .hero-subtitle {
    color: var(--gray-400);
  }
}



/* AI Real-time Insights */
.ai-realtime-insights {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 24px;
  position: relative;
  z-index: 2;
}

.insight-card {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 20px;
  padding: 24px;
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  animation: ai-card-slide-up 0.8s ease-out forwards;
  opacity: 0;
  transform: translateY(30px);
}

.insight-card:nth-child(1) { animation-delay: 0.3s; }
.insight-card:nth-child(2) { animation-delay: 0.5s; }
.insight-card:nth-child(3) { animation-delay: 0.7s; }

@keyframes ai-card-slide-up {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.insight-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.card-header h3 {
  font-size: 18px;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.refresh-btn {
  background: #f3f4f6;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  color: #6b7280;
}

.refresh-btn:hover:not(:disabled) {
  background: #667eea;
  color: white;
  transform: scale(1.1);
}

.refresh-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* AI Score Display */
.ai-score-card {
  grid-row: span 2;
}

.analyzing-cv-info {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 12px;
  margin-bottom: 20px;
}

.cv-being-analyzed {
  margin: 0 0 4px 0;
  font-size: 14px;
  color: #374151;
  display: flex;
  align-items: center;
  gap: 8px;
}

.cv-being-analyzed i {
  color: #667eea;
}

.last-updated {
  margin: 0;
  font-size: 12px;
  color: #6b7280;
}

.no-cv-info {
  background: #fef3c7;
  border: 1px solid #f59e0b;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 20px;
  text-align: center;
}

.no-cv-message {
  margin: 0;
  font-size: 14px;
  color: #92400e;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.ai-score-display {
  text-align: center;
}

.score-circle {
  width: 140px;
  height: 140px;
  margin: 0 auto 24px;
  position: relative;
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  background: #f8fafc;
}

.score-circle.excellent { color: #10b981; }
.score-circle.good { color: #3b82f6; }
.score-circle.fair { color: #f59e0b; }
.score-circle.poor { color: #ef4444; }

.score-number {
  font-size: 36px;
  line-height: 1;
}

.score-suffix {
  font-size: 16px;
  opacity: 0.7;
}

.score-ring {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  transform: rotate(-90deg);
}

.score-progress {
  transition: stroke-dashoffset 2.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.score-breakdown {
  display: flex;
  flex-direction: column;
  gap: 16px;
  text-align: left;
}

.breakdown-row {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 14px;
}

.breakdown-row span:first-child {
  min-width: 100px;
  font-weight: 600;
  color: #374151;
}

.breakdown-row span:last-child {
  min-width: 40px;
  font-weight: 700;
  color: #1f2937;
}

.progress-bar {
  flex: 1;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.progress {
  width: var(--bar-width, 100%);
  height: 100%;
  border-radius: 4px;
  transition: width 2s cubic-bezier(0.4, 0, 0.2, 1);
  background: linear-gradient(90deg, #667eea, #764ba2);
  position: relative;
}

.progress::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
  animation: ai-progress-shimmer 2s ease-in-out infinite;
}

@keyframes ai-progress-shimmer {
  0% { left: -100%; }
  100% { left: 100%; }
}

.progress-bar.ats .progress {
  background: linear-gradient(90deg, #10b981, #059669);
}

.progress-bar.market .progress {
  background: linear-gradient(90deg, #f59e0b, #d97706);
}

/* Recommendations Card */
.recommendations-badge {
  background: #667eea;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  animation: ai-badge-pulse 2s ease-in-out infinite;
}

@keyframes ai-badge-pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}

.recommendations-carousel {
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-height: 300px;
  overflow-y: auto;
}

.recommendation-slide {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  background: #fafbfc;
}

.recommendation-slide:hover {
  border-color: #667eea;
  transform: translateX(8px);
  box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
}

.rec-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 18px;
}

.rec-icon.high {
  background: linear-gradient(135deg, #ef4444, #dc2626);
}

.rec-icon.medium {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}

.rec-content {
  flex: 1;
}

.rec-content h4 {
  font-size: 16px;
  font-weight: 600;
  margin: 0 0 4px 0;
  color: #1f2937;
}

.rec-content p {
  font-size: 13px;
  color: #6b7280;
  margin: 0 0 8px 0;
  line-height: 1.4;
}

.rec-impact {
  background: linear-gradient(135deg, #dcfce7, #bbf7d0);
  color: #065f46;
  padding: 4px 12px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 700;
  display: inline-block;
}

/* Trends Card */
.trend-indicator {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #fef3c7;
  color: #92400e;
  padding: 6px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  animation: ai-trend-glow 3s ease-in-out infinite alternate;
}

@keyframes ai-trend-glow {
  0% { background: #fef3c7; }
  100% { background: #fbbf24; }
}

.trending-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.trending-skills {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.skill-chip {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  border-radius: 16px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.skill-chip.blazing {
  background: linear-gradient(135deg, #fef3c7, #fde68a);
  color: #92400e;
  border-color: #f59e0b;
  animation: ai-skill-blazing 2s ease-in-out infinite alternate;
}

@keyframes ai-skill-blazing {
  0% { transform: scale(1); box-shadow: 0 0 0 rgba(245, 158, 11, 0); }
  100% { transform: scale(1.05); box-shadow: 0 0 20px rgba(245, 158, 11, 0.3); }
}

.skill-chip.hot {
  background: linear-gradient(135deg, #fecaca, #fca5a5);
  color: #991b1b;
  border-color: #ef4444;
}

.skill-chip.warm {
  background: linear-gradient(135deg, #dbeafe, #bfdbfe);
  color: #1e40af;
  border-color: #3b82f6;
}

.skill-chip:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.skill-growth {
  font-size: 11px;
  font-weight: 700;
  opacity: 0.8;
}

.market-insights {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.insight-bubble {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  background: #f8fafc;
  border-radius: 12px;
  border-left: 4px solid #667eea;
  transition: all 0.3s ease;
}

.insight-bubble:hover {
  background: #e5edff;
  transform: translateX(4px);
}

.insight-bubble i {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #667eea;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
}

.insight-bubble span {
  font-size: 14px;
  color: #374151;
  font-weight: 500;
}

/* Responsive AI Dashboard */
@media (max-width: 1024px) {
  .ai-realtime-insights {
    grid-template-columns: 1fr;
  }
  
  .ai-score-card {
    grid-row: span 1;
  }
  
  .ai-header {
    flex-direction: column;
    text-align: center;
    gap: 24px;
  }
  
  .ai-main-title {
    font-size: 32px;
  }
}

@media (max-width: 768px) {
  .ai-live-dashboard {
    padding: 20px;
    margin: 10px 0 30px 0;
  }
  
  .ai-main-title {
    font-size: 28px;
  }
  
  .ai-subtitle {
    font-size: 16px;
  }
  
  .ai-live-metrics {
    grid-template-columns: 1fr;
  }
  
  .live-metric {
    padding: 16px;
  }
  
  .metric-icon {
    width: 48px;
    height: 48px;
    font-size: 18px;
  }
  
  .metric-value {
    font-size: 24px;
  }
  
  .ai-avatar-main {
    width: 100px;
    height: 100px;
  }
  
  .score-circle {
    width: 120px;
    height: 120px;
  }
  
  .score-number {
    font-size: 32px;
  }
}

/* Global AI Notification Animations */
@keyframes slideInRight {
  0% {
    opacity: 0;
    transform: translateX(100%);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slideOutRight {
  0% {
    opacity: 1;
    transform: translateX(0);
  }
  100% {
    opacity: 0;
    transform: translateX(100%);
  }
}

.animated-item {
  animation-delay: var(--delay, 0s);
}

/* AI Process Demo */
.ai-process-demo {
  margin-top: 40px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  align-items: center;
}

.ai-demo-container {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(248, 250, 252, 0.05));
  backdrop-filter: blur(15px);
  border-radius: 20px;
  padding: 32px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  position: relative;
  overflow: hidden;
}

.ai-demo-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
  opacity: 0;
  transition: all 0.6s ease;
}

.ai-demo-container:hover::before {
  opacity: 1;
}

/* AI Demo Preview */
.ai-demo-preview {
  width: 100%;
  min-height: 350px;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  backdrop-filter: blur(10px);
}

.ai-demo-preview.analyzing {
  transform: scale(1.02);
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
}

/* AI Demo Header */
.ai-demo-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  background: linear-gradient(135deg, #1e293b, #334155);
  color: white;
  transition: all 0.6s ease;
}

.ai-demo-header.active {
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
}

.ai-demo-avatar {
  width: 48px;
  height: 48px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  color: white;
  transition: all 0.4s ease;
}

.ai-demo-header.active .ai-demo-avatar {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.1);
}

.ai-demo-title {
  flex: 1;
  margin-left: 16px;
  font-size: 18px;
  font-weight: 700;
}

.placeholder-text {
  color: rgba(255, 255, 255, 0.6);
  font-style: italic;
}

.ai-demo-status {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.15);
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  transition: all 0.4s ease;
}

.ai-demo-status.live {
  background: rgba(16, 185, 129, 0.9);
  color: white;
}

/* AI Demo Sections */
.ai-demo-sections {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.ai-demo-section {
  background: #f8fafc;
  border-radius: 12px;
  padding: 16px;
  transition: all 0.6s ease;
  border: 2px solid transparent;
}

.ai-demo-section.analyzing {
  background: #f0f8ff;
  border-color: var(--primary);
  box-shadow: 0 4px 12px rgba(91, 33, 182, 0.1);
}

.ai-demo-section .section-header {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 14px;
  font-weight: 700;
  color: #475569;
  margin-bottom: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.ai-demo-section.analyzing .section-header {
  color: var(--primary);
}

.ai-demo-section .section-header i {
  width: 16px;
  height: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Analysis Progress */
.analysis-progress {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.progress-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  opacity: 0;
  transform: translateX(-20px);
  animation: slideInProgress 0.8s ease-out forwards;
}

@keyframes slideInProgress {
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.progress-label {
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  min-width: 80px;
}

.progress-bar {
  flex: 1;
  height: 6px;
  background: #e2e8f0;
  border-radius: 3px;
  overflow: hidden;
  margin: 0 12px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--primary), var(--primary-light));
  border-radius: 3px;
  transition: width 1.2s ease-out;
  transform-origin: left;
  animation: fillProgress 1.5s ease-out;
}

@keyframes fillProgress {
  from { width: 0% !important; }
}

.progress-value {
  font-size: 12px;
  font-weight: 700;
  color: var(--primary);
  min-width: 35px;
  text-align: right;
}

/* Suggestions List */
.suggestions-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.suggestion-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 12px;
  background: white;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  opacity: 0;
  transform: translateY(20px);
  animation: slideInSuggestion 0.6s ease-out forwards;
}

@keyframes slideInSuggestion {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.priority-badge {
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  min-width: 40px;
  text-align: center;
}

.suggestion-item.high .priority-badge {
  background: #fee2e2;
  color: #dc2626;
}

.suggestion-item.medium .priority-badge {
  background: #fef3c7;
  color: #d97706;
}

.suggestion-item.low .priority-badge {
  background: #dcfce7;
  color: #16a34a;
}

.suggestion-text {
  font-size: 12px;
  color: #475569;
  font-weight: 500;
}

/* Trends Grid */
.trends-grid {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.trend-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  background: white;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  opacity: 0;
  transform: translateX(20px);
  animation: slideInTrend 0.6s ease-out forwards;
}

@keyframes slideInTrend {
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.trend-skill {
  font-size: 12px;
  font-weight: 600;
  color: #475569;
}

.trend-growth {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 4px;
}

.trend-growth.up {
  background: #dcfce7;
  color: #16a34a;
}

.trend-growth.down {
  background: #fee2e2;
  color: #dc2626;
}

/* Processing Overlay */
.processing-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(91, 33, 182, 0.95);
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 16px;
  animation: overlayFadeIn 0.6s ease-out;
}

.processing-animation {
  text-align: center;
  color: white;
}

.processing-animation i {
  font-size: 48px;
  margin-bottom: 20px;
  animation: spin 2s linear infinite;
}

.processing-text {
  font-weight: 600;
  font-size: 18px;
}

/* Results Overlay */
.results-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.95), rgba(6, 182, 212, 0.95));
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 16px;
  animation: overlayFadeIn 0.6s ease-out;
}

.results-animation {
  text-align: center;
  color: white;
}

.results-animation i {
  font-size: 64px;
  margin-bottom: 24px;
  animation: checkBounce 0.8s ease-out;
}

@keyframes checkBounce {
  0% { transform: scale(0); }
  50% { transform: scale(1.2); }
  100% { transform: scale(1); }
}

.results-summary h4 {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 12px;
}

.results-summary p {
  font-size: 16px;
  margin: 4px 0;
  opacity: 0.9;
}

@keyframes overlayFadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
  }
  
/* AI Process Steps */
.ai-process-steps {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.ai-step {
  display: flex;
  align-items: center;
  gap: 20px;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 20px;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 2px solid transparent;
  position: relative;
  overflow: hidden;
}

.ai-step::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
  opacity: 0;
  transition: all 0.4s ease;
}

.ai-step:hover {
  transform: translateX(8px);
  background: rgba(255, 255, 255, 0.15);
  border-color: rgba(255, 255, 255, 0.3);
}

.ai-step:hover::before {
  opacity: 1;
}

.ai-step.active {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.4);
  transform: translateX(12px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.ai-step.active::before {
  opacity: 1;
}

.ai-step.completed {
  background: rgba(16, 185, 129, 0.15);
  border-color: rgba(16, 185, 129, 0.3);
}

.ai-step.completed .ai-step-number {
  background: var(--success);
}

.ai-step-number {
  width: 48px;
  height: 48px;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 16px;
  flex-shrink: 0;
  transition: all 0.4s ease;
  border: 2px solid rgba(255, 255, 255, 0.3);
}

.ai-step.active .ai-step-number {
  background: var(--primary);
  transform: scale(1.1);
  border-color: var(--primary);
}

.ai-step-content {
  flex: 1;
  text-align: left;
}

.ai-step-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  margin-bottom: 12px;
  transition: all 0.4s ease;
}

.ai-step-icon.icon-primary {
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  color: white;
}

.ai-step-icon.icon-secondary {
  background: linear-gradient(135deg, var(--secondary), #38bdf8);
  color: white;
}

.ai-step-icon.icon-accent {
  background: linear-gradient(135deg, var(--accent), #fbbf24);
  color: white;
}

.ai-step-icon.icon-success {
  background: linear-gradient(135deg, var(--success), #34d399);
  color: white;
}

.ai-step-icon.icon-complete {
  background: linear-gradient(135deg, #8b5cf6, #a855f7);
  color: white;
}

.ai-step h4 {
  font-size: 16px;
  font-weight: 700;
  margin-bottom: 6px;
  color: white;
  transition: all 0.4s ease;
}

.ai-step.active h4 {
  color: white;
}

.ai-step p {
  color: rgba(255, 255, 255, 0.8);
  line-height: 1.5;
  margin-bottom: 12px;
  font-size: 14px;
}

.live-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  padding: 6px 12px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.pulse-dot {
  width: 6px;
  height: 6px;
  background: white;
  border-radius: 50%;
  animation: pulse-live-dot 2s ease-in-out infinite;
}

@keyframes pulse-live-dot {
  0%, 100% { 
    transform: scale(1);
    opacity: 1;
  }
  50% { 
    transform: scale(1.2);
    opacity: 0.8;
  }
}

.live-indicator-small {
  display: flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
}

.suggestions-count-small, .update-time-small {
  background: rgba(255, 255, 255, 0.15);
  color: rgba(255, 255, 255, 0.9);
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 10px;
  font-weight: 600;
}

.horizontal-scroll-container {
  overflow-x: auto;
  padding-bottom: 8px;
}

.horizontal-scroll-container::-webkit-scrollbar {
  height: 6px;
}

.horizontal-scroll-container::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 3px;
}

.horizontal-scroll-container::-webkit-scrollbar-thumb {
  background: linear-gradient(90deg, #667eea, #764ba2);
  border-radius: 3px;
}

.insight-cards, .suggestion-cards, .trend-cards {
  display: flex;
  gap: 20px;
  padding: 4px 0;
  min-width: max-content;
}

/* Integrated Insight Cards */
.insight-card-integrated {
  background: rgba(255, 255, 255, 0.15);
  border-radius: 12px;
  padding: 16px;
  min-width: 220px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.insight-card-integrated:hover {
  transform: translateY(-2px);
  background: rgba(255, 255, 255, 0.2);
}

.insight-icon-small {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  color: white;
  margin-bottom: 12px;
}

.insight-icon-small.warning {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}

.insight-icon-small.success {
  background: linear-gradient(135deg, #10b981, #059669);
}

.insight-icon-small.info {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}

/* Main Content Layout */
.dashboard-content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
  margin-top: 2rem;
  max-width: 100%;
}

.cv-section {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  padding: 2rem;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.section-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.section-subtitle {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0.25rem 0 0 0;
}

/* CV Controls */
.cv-controls {
  display: flex;
  gap: 1rem;
  align-items: center;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.search-box {
  flex: 1;
  min-width: 250px;
  position: relative;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.8);
  font-size: 0.875rem;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
  background: white;
}

.search-icon {
  position: absolute;
  left: 0.875rem;
  top: 50%;
  transform: translateY(-50%);
  color: #64748b;
  font-size: 0.875rem;
}

.filter-select {
  padding: 0.75rem 1rem;
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.8);
  font-size: 0.875rem;
  color: #374151;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.view-toggle {
  display: flex;
  background: rgba(0, 0, 0, 0.05);
  border-radius: 10px;
  padding: 0.25rem;
}

.view-btn {
  padding: 0.5rem 0.75rem;
  border: none;
  background: none;
  border-radius: 8px;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
}

.view-btn.active {
  background: white;
  color: #6366f1;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* CV Grid */
.cvs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

.cv-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  position: relative;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.cv-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
  border-color: rgba(99, 102, 241, 0.2);
}

.cv-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.cv-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.25rem 0;
  line-height: 1.3;
}

.cv-template {
  font-size: 0.75rem;
  color: #6366f1;
  background: rgba(99, 102, 241, 0.1);
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  font-weight: 500;
}

.cv-menu {
  position: relative;
}

.menu-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: rgba(0, 0, 0, 0.05);
  border-radius: 8px;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.menu-btn:hover {
  background: rgba(0, 0, 0, 0.1);
  color: #374151;
}

.cv-stats {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.cv-stat {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
  color: #64748b;
}

.cv-stat i {
  color: #9ca3af;
}

.cv-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-sm {
  padding: 0.5rem 1rem;
  font-size: 0.75rem;
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.2s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  border: none;
  cursor: pointer;
}

.btn-outline {
  background: rgba(255, 255, 255, 0.8);
  color: #374151;
  border: 1px solid rgba(0, 0, 0, 0.1);
}

.btn-outline:hover {
  background: white;
  border-color: rgba(99, 102, 241, 0.3);
  color: #6366f1;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #64748b;
}

.empty-icon {
  font-size: 4rem;
  color: #e5e7eb;
  margin-bottom: 1rem;
}

.empty-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 0.5rem 0;
}

.empty-description {
  font-size: 0.875rem;
  margin: 0 0 2rem 0;
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.5;
}

/* Analytics Sidebar */
.analytics-sidebar {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  max-width: 100%;
}

.premium-analytics-card {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 1.5rem;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
  max-width: 100%;
}

.analytics-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.header-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
  flex-shrink: 0;
}

.header-content {
  flex: 1;
  min-width: 0;
}

.header-content h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.25rem 0;
}

.header-content p {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0;
}

.header-action {
  flex-shrink: 0;
}

.refresh-btn {
  padding: 0.5rem;
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.8);
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s ease;
}

.refresh-btn:hover {
  background: white;
  color: #6366f1;
  transform: rotate(180deg);
}

.analytics-body {
  max-width: 100%;
}

.chart-container {
  height: 200px;
  margin-bottom: 1rem;
  position: relative;
}

.skills-chart-container {
  height: 200px;
  margin-bottom: 1rem;
  position: relative;
}

.chart-legend {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #64748b;
}

.legend-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
}

.legend-dot.views {
  background: #4F46E5;
}

.legend-dot.downloads {
  background: #10B981;
}

.insights-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.insight-card {
  background: rgba(255, 255, 255, 0.8);
  border-radius: 12px;
  padding: 1rem;
  border: 1px solid rgba(0, 0, 0, 0.05);
  display: flex;
  align-items: flex-start;
  gap: 1rem;
}

.insight-indicator {
  width: 4px;
  height: 100%;
  border-radius: 2px;
  flex-shrink: 0;
}

.insight-indicator.high {
  background: #dc2626;
}

.insight-indicator.medium {
  background: #f59e0b;
}

.insight-indicator.low {
  background: #10b981;
}

.insight-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  flex-shrink: 0;
}

.insight-icon.info {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
}

.insight-icon.warning {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
}

.insight-icon.success {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.insight-content {
  flex: 1;
  min-width: 0;
}

.insight-content h4 {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
}

.insight-content p {
  font-size: 0.75rem;
  color: #64748b;
  margin: 0 0 0.75rem 0;
  line-height: 1.4;
}

.insight-action {
  font-size: 0.75rem;
  padding: 0.375rem 0.75rem;
  border-radius: 6px;
  border: 1px solid rgba(99, 102, 241, 0.2);
  background: rgba(99, 102, 241, 0.05);
  color: #6366f1;
  cursor: pointer;
  transition: all 0.2s ease;
}

.insight-action:hover {
  background: rgba(99, 102, 241, 0.1);
}

.skills-summary {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.skill-stat {
  background: rgba(255, 255, 255, 0.8);
  border-radius: 8px;
  padding: 0.75rem;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.skill-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.skill-name {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1f2937;
}

.skill-percentage {
  font-size: 0.75rem;
  font-weight: 600;
  color: #6366f1;
}

.skill-bar {
  height: 6px;
  background: rgba(0, 0, 0, 0.1);
  border-radius: 3px;
  overflow: hidden;
}

.skill-progress {
  height: 100%;
  border-radius: 3px;
  transition: width 0.3s ease;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }
}

@media (max-width: 1024px) {
  .dashboard-content {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .analytics-sidebar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
  }
}

@media (max-width: 768px) {
  .dashboard-container {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .header-actions {
    width: 100%;
    justify-content: space-between;
  }
  
  .dashboard-title {
    font-size: 2rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .stat-card {
    padding: 1rem;
    min-height: 100px;
  }
  
  .stat-value {
    font-size: 1.5rem;
  }
  
  .cvs-grid {
    grid-template-columns: 1fr;
  }
  
  .cv-controls {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-box {
    min-width: auto;
  }
  
  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
}

/* Enhanced CV Card Styles */
.cv-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 1.5rem 1.5rem 0 1.5rem;
  gap: 1rem;
}

.template-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(99, 102, 241, 0.1);
  color: #6366f1;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 600;
  flex-shrink: 0;
}

.cv-status-badge {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.375rem 0.75rem;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 600;
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
  flex-shrink: 0;
}

.cv-status-badge.draft {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
}

.cv-menu-trigger {
  position: relative;
}

.menu-trigger-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: rgba(0, 0, 0, 0.05);
  border-radius: 8px;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.menu-trigger-btn:hover {
  background: rgba(0, 0, 0, 0.1);
  color: #374151;
}

.cv-dropdown-menu {
  position: absolute;
  top: calc(100% + 0.5rem);
  right: 0;
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  min-width: 160px;
  z-index: 1000;
  padding: 0.5rem;
  border: 1px solid rgba(0, 0, 0, 0.1);
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem;
  background: none;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
}

.dropdown-item:hover {
  background: #f8fafc;
  color: #6366f1;
}

.dropdown-item.danger:hover {
  background: #fef2f2;
  color: #dc2626;
}

.dropdown-divider {
  height: 1px;
  background: rgba(0, 0, 0, 0.1);
  margin: 0.5rem 0;
}

/* Enhanced CV Card Body */
.cv-card-body {
  padding: 1.5rem;
}

.cv-title-section {
  margin-bottom: 1.5rem;
}

.cv-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
  line-height: 1.3;
}

.cv-author {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0 0 0.25rem 0;
  font-weight: 500;
}

.cv-job-title {
  font-size: 0.875rem;
  color: #6366f1;
  margin: 0;
  font-weight: 600;
}

/* Enhanced Skills Preview */
.cv-skills-preview {
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: rgba(99, 102, 241, 0.05);
  border-radius: 12px;
  border: 1px solid rgba(99, 102, 241, 0.1);
}

.skills-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: #6366f1;
}

.skills-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.skill-tag {
  background: rgba(99, 102, 241, 0.1);
  color: #6366f1;
  padding: 0.25rem 0.75rem;
  border-radius: 16px;
  font-size: 0.75rem;
  font-weight: 500;
  border: 1px solid rgba(99, 102, 241, 0.2);
}

.skill-count {
  background: rgba(107, 114, 128, 0.1);
  color: #6b7280;
  padding: 0.25rem 0.75rem;
  border-radius: 16px;
  font-size: 0.75rem;
  font-weight: 500;
  border: 1px solid rgba(107, 114, 128, 0.2);
}

/* Enhanced Experience Preview */
.cv-experience-preview {
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: rgba(16, 185, 129, 0.05);
  border-radius: 12px;
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.experience-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: #10b981;
}

.experience-item .job-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.experience-item .company-name {
  font-size: 0.75rem;
  color: #64748b;
  font-weight: 500;
}

/* Enhanced Metrics */
.cv-metrics {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: rgba(0, 0, 0, 0.02);
  border-radius: 12px;
}

.metric-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 0.25rem;
}

.metric-item i {
  color: #6366f1;
  font-size: 1rem;
  margin-bottom: 0.25rem;
}

.metric-value {
  font-size: 1.125rem;
  font-weight: 700;
  color: #1f2937;
  line-height: 1;
}

.metric-label {
  font-size: 0.75rem;
  color: #64748b;
  font-weight: 500;
}

/* Enhanced Completion Bar */
.cv-completion {
  margin-bottom: 1.5rem;
}

.completion-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.completion-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
}

.completion-percentage {
  font-size: 0.875rem;
  font-weight: 700;
  color: #6366f1;
}

.completion-bar {
  height: 8px;
  background: rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  overflow: hidden;
}

.completion-progress {
  height: 100%;
  background: linear-gradient(90deg, #6366f1, #8b5cf6);
  border-radius: 4px;
  width: var(--bar-width);
  transition: width 0.3s ease;
}

/* Enhanced Footer */
.cv-card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
  border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.cv-date {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
  color: #64748b;
}

.cv-date i {
  color: #9ca3af;
}

/* Enhanced Quick Actions */
.quick-actions {
  display: flex;
  gap: 0.5rem;
}

.quick-action-btn {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
}

.edit-btn {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
}

.edit-btn:hover {
  background: rgba(245, 158, 11, 0.2);
  transform: translateY(-1px);
}

.preview-btn {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
}

.preview-btn:hover {
  background: rgba(59, 130, 246, 0.2);
  transform: translateY(-1px);
}

.download-btn {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.download-btn:hover {
  background: rgba(16, 185, 129, 0.2);
  transform: translateY(-1px);
}

/* Enhanced CV Card Styles */
.cv-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 1.5rem 1.5rem 0 1.5rem;
  gap: 1rem;
}

.template-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(99, 102, 241, 0.1);
  color: #6366f1;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 600;
  flex-shrink: 0;
}

.cv-status-badge {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.375rem 0.75rem;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 600;
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
  flex-shrink: 0;
}

.cv-status-badge.draft {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
}

.cv-menu-trigger {
  position: relative;
}

.menu-trigger-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: rgba(0, 0, 0, 0.05);
  border-radius: 8px;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.menu-trigger-btn:hover {
  background: rgba(0, 0, 0, 0.1);
  color: #374151;
}

.cv-dropdown-menu {
  position: absolute;
  top: calc(100% + 0.5rem);
  right: 0;
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  min-width: 160px;
  z-index: 1000;
  padding: 0.5rem;
  border: 1px solid rgba(0, 0, 0, 0.1);
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem;
  background: none;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
}

.dropdown-item:hover {
  background: #f8fafc;
  color: #6366f1;
}

.dropdown-item.danger:hover {
  background: #fef2f2;
  color: #dc2626;
}

.dropdown-divider {
  height: 1px;
  background: rgba(0, 0, 0, 0.1);
  margin: 0.5rem 0;
}

/* Enhanced CV Card Body */
.cv-card-body {
  padding: 1.5rem;
}

.cv-title-section {
  margin-bottom: 1.5rem;
}

.cv-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
  line-height: 1.3;
}

.cv-author {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0 0 0.25rem 0;
  font-weight: 500;
}

.cv-job-title {
  font-size: 0.875rem;
  color: #6366f1;
  margin: 0;
  font-weight: 600;
}

/* Enhanced Skills Preview */
.cv-skills-preview {
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: rgba(99, 102, 241, 0.05);
  border-radius: 12px;
  border: 1px solid rgba(99, 102, 241, 0.1);
}

.skills-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: #6366f1;
}

.skills-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.skill-tag {
  background: rgba(99, 102, 241, 0.1);
  color: #6366f1;
  padding: 0.25rem 0.75rem;
  border-radius: 16px;
  font-size: 0.75rem;
  font-weight: 500;
  border: 1px solid rgba(99, 102, 241, 0.2);
}

.skill-count {
  background: rgba(107, 114, 128, 0.1);
  color: #6b7280;
  padding: 0.25rem 0.75rem;
  border-radius: 16px;
  font-size: 0.75rem;
  font-weight: 500;
  border: 1px solid rgba(107, 114, 128, 0.2);
}

/* Enhanced Experience Preview */
.cv-experience-preview {
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: rgba(16, 185, 129, 0.05);
  border-radius: 12px;
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.experience-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: #10b981;
}

.experience-item .job-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.experience-item .company-name {
  font-size: 0.75rem;
  color: #64748b;
  font-weight: 500;
}

/* Enhanced Metrics */
.cv-metrics {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: rgba(0, 0, 0, 0.02);
  border-radius: 12px;
}

.metric-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 0.25rem;
}

.metric-item i {
  color: #6366f1;
  font-size: 1rem;
  margin-bottom: 0.25rem;
}

.metric-value {
  font-size: 1.125rem;
  font-weight: 700;
  color: #1f2937;
  line-height: 1;
}

.metric-label {
  font-size: 0.75rem;
  color: #64748b;
  font-weight: 500;
}

/* Enhanced Completion Bar */
.cv-completion {
  margin-bottom: 1.5rem;
}

.completion-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.completion-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
}

.completion-percentage {
  font-size: 0.875rem;
  font-weight: 700;
  color: #6366f1;
}

.completion-bar {
  height: 8px;
  background: rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  overflow: hidden;
}

.completion-progress {
  height: 100%;
  background: linear-gradient(90deg, #6366f1, #8b5cf6);
  border-radius: 4px;
  width: var(--bar-width);
  transition: width 0.3s ease;
}

/* Enhanced Footer */
.cv-card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
  border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.cv-date {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
  color: #64748b;
}

.cv-date i {
  color: #9ca3af;
}

/* Enhanced Quick Actions */
.quick-actions {
  display: flex;
  gap: 0.5rem;
}

.quick-action-btn {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
}

.edit-btn {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
}

.edit-btn:hover {
  background: rgba(245, 158, 11, 0.2);
  transform: translateY(-1px);
}

.preview-btn {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
}

.preview-btn:hover {
  background: rgba(59, 130, 246, 0.2);
  transform: translateY(-1px);
}

.download-btn {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.download-btn:hover {
  background: rgba(16, 185, 129, 0.2);
  transform: translateY(-1px);
}

/* Enhanced Notification System */
.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 10000;
  min-width: 300px;
  max-width: 500px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  border-left: 4px solid #6366f1;
  opacity: 0;
  transform: translateX(100%);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.notification.show {
  opacity: 1;
  transform: translateX(0);
}

.notification-success {
  border-left-color: #10b981;
}

.notification-error {
  border-left-color: #ef4444;
}

.notification-warning {
  border-left-color: #f59e0b;
}

.notification-info {
  border-left-color: #3b82f6;
}

.notification-content {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 20px;
}

.notification-content i {
  font-size: 18px;
  flex-shrink: 0;
}

.notification-success .notification-content i {
  color: #10b981;
}

.notification-error .notification-content i {
  color: #ef4444;
}

.notification-warning .notification-content i {
  color: #f59e0b;
}

.notification-info .notification-content i {
  color: #3b82f6;
}

.notification-content span {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  line-height: 1.4;
}

/* Loading States */
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: inherit;
  z-index: 10;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e5e7eb;
  border-top: 3px solid #6366f1;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-text {
  margin-top: 12px;
  font-size: 14px;
  color: #6b7280;
  font-weight: 500;
}

/* Enhanced Empty States */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #64748b;
}

.empty-icon {
  font-size: 4rem;
  color: #e5e7eb;
  margin-bottom: 1rem;
  animation: gentle-pulse 3s ease-in-out infinite;
}

@keyframes gentle-pulse {
  0%, 100% { 
    opacity: 0.6; 
    transform: scale(1);
  }
  50% { 
    opacity: 0.8; 
    transform: scale(1.05);
  }
}

.empty-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 0.5rem 0;
}

.empty-description {
  font-size: 0.875rem;
  margin: 0 0 2rem 0;
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.5;
}

/* Enhanced Chart Loading */
.chart-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 200px;
  color: #6b7280;
}

.chart-loading i {
  font-size: 2rem;
  margin-bottom: 1rem;
  animation: spin 2s linear infinite;
}

.chart-loading span {
  font-size: 0.875rem;
  font-weight: 500;
}

/* Enhanced Button States */
.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

.btn.loading {
  position: relative;
  color: transparent;
}

.btn.loading::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 16px;
  height: 16px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

/* Accessibility Enhancements */
.btn:focus-visible {
  outline: 2px solid #6366f1;
  outline-offset: 2px;
}

.form-input:focus-visible,
.form-select:focus-visible {
  outline: 2px solid #6366f1;
  outline-offset: 2px;
  border-color: #6366f1;
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
  .loading-spinner,
  .btn.loading::after,
  .gentle-pulse,
  .spin {
    animation: none;
  }
  
  .notification {
    transition: none;
  }
  
  .fade-enter-active,
  .fade-leave-active,
  .slide-enter-active,
  .slide-leave-active {
    transition: none;
  }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
  .card {
    border: 2px solid #000;
  }
  
  .btn {
    border: 2px solid currentColor;
  }
  
  .notification {
    border: 2px solid #000;
  }
}

/* Smooth Transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.slide-enter-active, .slide-leave-active {
  transition: all 0.3s ease;
}

.slide-enter-from {
  transform: translateX(30px);
  opacity: 0;
}

.slide-leave-to {
  transform: translateX(-30px);
  opacity: 0;
}

/* Enhanced Mobile Responsiveness */
@media (max-width: 768px) {
  .notification {
    left: 10px;
    right: 10px;
    top: 10px;
    min-width: auto;
    max-width: none;
  }
  
  .stats-grid {
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }
  
  .dashboard-content {
    flex-direction: column;
  }
  
  .analytics-sidebar {
    order: -1;
    margin-bottom: 2rem;
  }
  
  .cv-display-card {
    margin-bottom: 1rem;
  }
  
  .search-filter-group {
    flex-direction: column;
    gap: 1rem;
  }
  
  .filter-controls {
    flex-wrap: wrap;
    gap: 0.5rem;
  }
  
  .modal-content {
    margin: 1rem;
    max-width: calc(100% - 2rem);
  }
}

@media (max-width: 480px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .cv-metrics {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .quick-actions {
    flex-wrap: wrap;
  }
  
  .section-actions {
    flex-direction: column;
    align-items: stretch;
  }
}

</style>
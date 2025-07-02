<template>
  <main class="dashboard-main">
    <!-- Animated Background -->
    <div class="animated-bg">
      <div class="gradient-sphere sphere-1"></div>
      <div class="gradient-sphere sphere-2"></div>
      <div class="gradient-sphere sphere-3"></div>
      <div class="gradient-sphere sphere-4"></div>
    </div>

    <div class="dashboard-container">
      <!-- AI-Powered Live Dashboard -->
      <section class="ai-live-dashboard">
        <div class="ai-header">
          <div class="ai-avatar-container">
            <div class="ai-avatar-main" :class="{ 'thinking': aiProcessing, 'speaking': aiSpeaking }">
              <div class="ai-face">
                <div class="ai-eyes">
                  <div class="eye" :class="{ 'blinking': isBlinking }"></div>
                  <div class="eye" :class="{ 'blinking': isBlinking }"></div>
                </div>
                <div class="ai-mouth" :class="{ 'talking': aiSpeaking }"></div>
              </div>
              <div class="ai-pulse-rings">
                <div class="pulse-ring ring-1"></div>
                <div class="pulse-ring ring-2"></div>
                <div class="pulse-ring ring-3"></div>
              </div>
            </div>
            <div class="ai-status-badge" :class="aiStatus">
              <i class="fas fa-brain" v-if="aiStatus === 'ready'"></i>
              <i class="fas fa-spinner fa-spin" v-if="aiStatus === 'processing'"></i>
              <span>{{ aiStatus === 'ready' ? 'AI Ready' : 'Processing...' }}</span>
            </div>
          </div>
          
          <div class="ai-greeting-section">
            <h1 class="ai-main-title">{{ aiMainGreeting }}</h1>
            <p class="ai-subtitle">{{ aiSubGreeting }}</p>
            <div class="ai-live-metrics">
              <div class="live-metric" v-for="metric in liveAIMetrics" :key="metric.id">
                <div class="metric-icon" :class="metric.type">
                  <i :class="metric.icon"></i>
                </div>
                <div class="metric-info">
                  <div class="metric-value">{{ metric.animatedValue }}</div>
                  <div class="metric-label">{{ metric.label }}</div>
                </div>
                <div class="metric-trend" :class="metric.trend">
                  <i class="fas fa-arrow-up"></i>
                  <span>{{ metric.change }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- AI Real-time Insights -->
        <div class="ai-realtime-insights">
          <div class="insight-card ai-score-card">
            <div class="card-header">
              <h3><i class="fas fa-chart-line"></i> AI CV Analysis</h3>
              <button @click="refreshAIAnalysis" class="refresh-btn" :disabled="aiProcessing">
                <i class="fas fa-sync" :class="{ 'fa-spin': aiProcessing }"></i>
              </button>
            </div>
            <div class="analyzing-cv-info" v-if="cvs_all.length > 0">
              <p class="cv-being-analyzed">
                <i class="fas fa-file-alt"></i>
                Analyzing: <strong>{{ cvs_all[0].title || 'Untitled CV' }}</strong>
              </p>
              <p class="last-updated">
                Updated {{ formatDate(cvs_all[0].updatedAt) }}
              </p>
            </div>
            <div class="no-cv-info" v-else>
              <p class="no-cv-message">
                <i class="fas fa-info-circle"></i>
                No CV data found. Create your first CV to get AI insights!
              </p>
            </div>
            <div class="ai-score-display">
              <div class="score-circle" :class="getAIScoreClass(aiScores.overall)">
                <span class="score-number">{{ aiScores.overall }}</span>
                <span class="score-suffix">/100</span>
                <svg class="score-ring" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="45" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="8"/>
                  <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8"
                          stroke-linecap="round" :stroke-dasharray="283" 
                          :stroke-dashoffset="283 - (aiScores.overall / 100) * 283"
                          class="score-progress"/>
                </svg>
              </div>
              <div class="score-breakdown">
                <div class="breakdown-row">
                  <span>Completeness</span>
                  <div class="progress-bar">
                    <div class="progress" :style="{ '--bar-width': aiScores.completeness + '%' }"></div>
                  </div>
                  <span>{{ aiScores.completeness }}%</span>
                </div>
                <div class="breakdown-row">
                  <span>ATS Score</span>
                  <div class="progress-bar ats">
                    <div class="progress" :style="{ '--bar-width': aiScores.ats + '%' }"></div>
                  </div>
                  <span>{{ aiScores.ats }}%</span>
                </div>
                <div class="breakdown-row">
                  <span>Market Fit</span>
                  <div class="progress-bar market">
                    <div class="progress" :style="{ '--bar-width': aiScores.market + '%' }"></div>
                  </div>
                  <span>{{ aiScores.market }}%</span>
                </div>
              </div>
            </div>
          </div>

          <div class="insight-card ai-recommendations-card">
            <div class="card-header">
              <h3><i class="fas fa-lightbulb"></i> Smart Recommendations</h3>
              <div class="recommendations-badge">{{ smartRecommendations.length }}</div>
            </div>
            <div class="recommendations-carousel">
              <div v-for="rec in smartRecommendations" :key="rec.id" 
                   class="recommendation-slide" @click="applyRecommendation(rec)">
                <div class="rec-icon" :class="rec.priority">
                  <i :class="rec.icon"></i>
                </div>
                <div class="rec-content">
                  <h4>{{ rec.title }}</h4>
                  <p>{{ rec.description }}</p>
                  <div class="rec-impact">{{ rec.impact }}</div>
                </div>
              </div>
            </div>
          </div>

          <div class="insight-card ai-trends-card">
            <div class="card-header">
              <h3><i class="fas fa-fire"></i> Market Intelligence</h3>
              <div class="trend-indicator">
                <i class="fas fa-arrow-up"></i>
                <span>Hot</span>
              </div>
            </div>
            <div class="trending-content">
              <div class="trending-skills">
                <div v-for="skill in hotSkills" :key="skill.name" 
                     class="skill-chip" :class="skill.heat" @click="addSkillToCV(skill)">
                  <span>{{ skill.name }}</span>
                  <div class="skill-growth">+{{ skill.growth }}%</div>
                </div>
              </div>
              <div class="market-insights">
                <div v-for="insight in marketInsights" :key="insight.id" class="insight-bubble">
                  <i :class="insight.icon"></i>
                  <span>{{ insight.text }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Hero Section -->
      <section class="hero-section">
        <div class="hero-container">
          <!-- Left Side: Hero Content -->
          <div class="hero-content">
            <div class="welcome-badge">
              <div class="badge-icon">
                <i class="fas fa-sparkles"></i>
              </div>
              <span>{{ t('welcome_back') }}</span>
            </div>
            
            <h1 class="hero-title">
              Hello, <span class="typewriter-text">{{ typewriterText }}</span>
            </h1>
            
            <p class="hero-subtitle">
              Your career journey continues here. Track progress, create stunning CVs, 
              and unlock your professional potential with AI-powered insights.
            </p>
            
            <div class="hero-actions">
              <router-link to="/create-cv" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i>
                <span>{{ t('createCV') }}</span>
                <div class="btn-shine"></div>
              </router-link>
              <button @click="toggleAIPanel" class="btn btn-secondary">
                <i class="fas fa-brain" :class="{ 'pulse': showAIPanel }"></i>
                <span>AI Insights</span>
              </button>
            </div>
          </div>

                      <!-- Right Side: Stats Cards -->
            <div class="stats-sidebar">
              <div class="stat-card" v-for="(stat, index) in statsData" :key="stat.id" 
                   :style="{ '--delay': `${index * 0.1}s` }">
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
          </div>
      </section>

      <!-- AI Insights Panel -->
      <section v-if="showAIPanel && !loading && totalCvs > 0" class="ai-insights-section">
        <div class="ai-insights-header">
          <h2 class="section-title">
            <i class="fas fa-brain"></i>
            AI-Powered Insights
          </h2>
          <button @click="toggleAIPanel" class="close-panel-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>
        
        <div class="ai-insights-grid">
          <div class="insight-card trending-skills">
            <div class="card-header">
              <div class="card-icon">
                <i class="fas fa-fire"></i>
              </div>
              <h3>Trending Skills</h3>
            </div>
            <div class="skills-cloud">
              <span v-for="skill in trendingSkills" :key="skill" class="skill-bubble">
                {{ skill }}
              </span>
            </div>
          </div>
          
          <div class="insight-card performance-metrics">
            <div class="card-header">
              <div class="card-icon">
                <i class="fas fa-chart-line"></i>
              </div>
              <h3>Performance</h3>
            </div>
            <div class="metrics-grid">
              <div class="metric-item">
                <div class="metric-value">{{ profileViews }}</div>
                <div class="metric-label">Profile Views</div>
                <div class="metric-change positive">+12%</div>
              </div>
              <div class="metric-item">
                <div class="metric-value">{{ downloadRate }}%</div>
                <div class="metric-label">Download Rate</div>
                <div class="metric-change positive">+8%</div>
              </div>
            </div>
          </div>
          
          <div class="insight-card ai-recommendations">
            <div class="card-header">
              <div class="card-icon">
                <i class="fas fa-lightbulb"></i>
              </div>
              <h3>Smart Recommendations</h3>
            </div>
            <div class="recommendations-list">
              <div v-for="rec in aiRecommendations" :key="rec.id" class="recommendation-item">
                <div class="rec-priority-dot" :class="rec.priority"></div>
                <div class="rec-content">
                  <p>{{ rec.text }}</p>
                  <button class="rec-action-btn">Apply</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CV Management Section -->
      <section class="cv-management-section">
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
                  class="search-input"
                />
                <button v-if="searchTerm" @click="searchTerm = ''" class="clear-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
              
              <div class="filter-controls">
                <select v-model="filterTemplate" class="filter-select">
                  <option value="all">All Templates</option>
                  <option value="classic">Classic</option>
                  <option value="modern">Modern</option>
                  <option value="professional">Professional</option>
                  <option value="creative">Creative</option>
                </select>
                
                <select v-model="sortBy" class="filter-select">
                  <option value="updated_at_desc">Latest First</option>
                  <option value="updated_at_asc">Oldest First</option>
                  <option value="title_asc">A-Z</option>
                  <option value="views_desc">Most Viewed</option>
                </select>
                
                <button class="view-mode-btn" @click="toggleView" :class="{ active: viewMode === 'grid' }">
                  <i :class="viewMode === 'grid' ? 'fas fa-th' : 'fas fa-list'"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- CV Cards Display -->
        <div class="cv-cards-container">
          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <div class="loading-spinner">
              <i class="fas fa-spinner fa-spin"></i>
            </div>
            <h3>Loading your CVs...</h3>
            <p>Please wait while we fetch your documents</p>
          </div>

          <!-- Empty State -->
          <div v-else-if="cvs.length === 0" class="empty-state">
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
              class="cv-display-card"
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
                  <button class="menu-trigger-btn" @click="toggleMenu(cv_item.id)">
                    <i class="fas fa-ellipsis-v"></i>
                  </button>
                  <div v-if="activeMenu === cv_item.id" class="cv-dropdown-menu">
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
                    <button @click.stop="editCv(cv_item.id)" class="quick-action-btn" title="Edit CV">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button @click.stop="downloadCv(cv_item.id, cv_item.title)" class="quick-action-btn" title="Download">
                      <i class="fas fa-download"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
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
import { ref, onMounted, onBeforeUnmount, computed, watchEffect } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import axios from 'axios'
import DownloadOptionsModal from '@/components/common/DownloadOptionsModal.vue'
import aiService from '@/services/aiService'

const { t } = useI18n()

const router = useRouter()

// State
const cvs_all = ref([])
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
const showAIPanel = ref(false)

// AI-specific state
const aiProcessing = ref(false)
const aiSpeaking = ref(false)
const isBlinking = ref(false)
const aiStatus = ref('ready')

// AI Scores (animated)
const aiScores = ref({
  overall: 0,
  completeness: 0,
  ats: 0,
  market: 0
})

// Live AI Metrics with animated values
const liveMetricValues = ref({
  profileScore: 0,
  cvQuality: 0, 
  marketMatch: 0
})

const liveAIMetrics = computed(() => {
  const profileScore = totalCvs.value > 0 ? calculateProfileScore() : 15
  const viewsGrowth = totalViews.value > 50 ? '+12%' : totalViews.value > 20 ? '+8%' : '+2%'
  const marketMatch = totalCvs.value > 0 ? Math.min(85, profileScore + 15) : 25
  
  return [
    {
      id: 1,
      label: 'Profile Score',
      value: profileScore,
      animatedValue: liveMetricValues.value.profileScore,
      change: viewsGrowth,
      trend: 'up',
      type: 'analysis',
      icon: 'fas fa-brain'
    },
    {
      id: 2,
      label: 'CV Quality',
      value: totalCvs.value > 0 ? Math.min(90, Math.floor(aiScores.value.overall)) : 20,
      animatedValue: liveMetricValues.value.cvQuality,
      change: totalCvs.value > 1 ? '+15%' : totalCvs.value === 1 ? '+5%' : 'New',
      trend: 'up',
      type: 'score',
      icon: 'fas fa-star'
    },
    {
      id: 3,
      label: 'Market Match',
      value: marketMatch,
      animatedValue: liveMetricValues.value.marketMatch,
      change: totalCvs.value > 0 ? '+8%' : 'Start CV',
      trend: 'up',
      type: 'match',
      icon: 'fas fa-bullseye'
    }
  ]
})

// Smart Recommendations (can be updated by AI)
const smartRecommendations = ref([])

// Compute initial recommendations based on CV data
const computeLocalRecommendations = () => {
  if (cvs_all.value.length === 0) {
    return [
      {
        id: 1,
        title: 'Create Your First CV',
        description: 'Start building your professional CV to get AI insights',
        impact: 'Start here',
        priority: 'high',
        icon: 'fas fa-plus'
      }
    ]
  }
  
  const latestCV = cvs_all.value[0]
  const suggestions = []
  
  // Check what's actually missing/needs improvement
  if (!latestCV.summary || latestCV.summary.trim().length < 100) {
    suggestions.push({
      id: 1,
      title: 'Add Professional Summary',
      description: 'A strong summary increases recruiter engagement by 40%',
      impact: '+15 pts',
      priority: 'high',
      icon: 'fas fa-edit'
    })
  }
  
  if (!latestCV.skills || latestCV.skills.length < 5) {
    suggestions.push({
      id: 2,
      title: 'Add More Skills',
      description: 'Include at least 5-8 relevant skills for better matching',
      impact: '+12 pts',
      priority: 'medium',
      icon: 'fas fa-plus'
    })
  }
  
  if (!latestCV.experience || latestCV.experience.length === 0) {
    suggestions.push({
      id: 3,
      title: 'Add Work Experience',
      description: 'Work experience is crucial for most job applications',
      impact: '+25 pts',
      priority: 'high',
      icon: 'fas fa-briefcase'
    })
  } else if (latestCV.experience.length > 0) {
    const hasQuantifiedAchievements = latestCV.experience.some(exp => 
      exp.description && /\d+/.test(exp.description)
    )
    if (!hasQuantifiedAchievements) {
      suggestions.push({
        id: 4,
        title: 'Quantify Your Achievements',
        description: 'Add numbers and metrics to make your impact clear',
        impact: '+10 pts',
        priority: 'medium',
        icon: 'fas fa-chart-line'
      })
    }
  }
  
  if (!latestCV.education || latestCV.education.length === 0) {
    suggestions.push({
      id: 5,
      title: 'Add Education',
      description: 'Include your educational background and certifications',
      impact: '+8 pts',
      priority: 'medium',
      icon: 'fas fa-graduation-cap'
    })
  }
  
  // If CV is complete, suggest optimizations
  if (suggestions.length === 0) {
    suggestions.push({
      id: 6,
      title: 'ATS Optimization',
      description: 'Optimize keywords for applicant tracking systems',
      impact: '+5 pts',
      priority: 'low',
      icon: 'fas fa-robot'
    })
  }
  
  return suggestions.slice(0, 3) // Return top 3 most relevant
}

// Watch for CV changes and update recommendations
watchEffect(() => {
  if (smartRecommendations.value.length === 0) {
    smartRecommendations.value = computeLocalRecommendations()
  }
})

// Hot Skills
const hotSkills = ref([
  { name: 'AI/ML', growth: 156, heat: 'blazing' },
  { name: 'React', growth: 89, heat: 'hot' },
  { name: 'TypeScript', growth: 67, heat: 'hot' },
  { name: 'Cloud', growth: 134, heat: 'blazing' },
  { name: 'DevOps', growth: 78, heat: 'warm' },
  { name: 'Cybersecurity', growth: 112, heat: 'hot' }
])

// Market Insights
const marketInsights = ref([
  {
    id: 1,
    text: 'Remote skills are 89% more in demand',
    icon: 'fas fa-home'
  },
  {
    id: 2,
    text: 'AI skills increase salary by 156%',
    icon: 'fas fa-chart-line'
  },
  {
    id: 3,
    text: 'Soft skills now weight 45% in hiring',
    icon: 'fas fa-handshake'
  }
])

// User data
const userName = ref('Professional')
const profileViews = ref(342)
const downloadRate = ref(78)
const typewriterText = ref('')

// Typewriter effect
const typewriterPhrases = ref([
  `${userName.value}! 👋`,
  'Champion! 🏆',
  'Innovator! 💡',
  'Creator! 🎨'
])

let typewriterIndex = 0
let charIndex = 0
let isDeleting = false
let typewriterTimeout = null

// AI data
const trendingSkills = ref(['React', 'Python', 'AI/ML', 'TypeScript', 'Node.js', 'Docker', 'AWS'])
const aiRecommendations = computed(() => {
  if (!showAIPanel.value || loading.value || totalCvs.value === 0) return []
  
  // Enhanced AI recommendations with bilingual support
  const recommendations = [
    {
      id: 1,
      type: 'summary',
      priority: 'high',
      text: currentLanguage.value === 'sq' 
        ? 'Përmirësoni përmbledhjen tuaj profesionale me sugjerime AI për të rritur dukshmërinë 25%'
        : 'Enhance your professional summary with AI suggestions to increase visibility by 25%',
      action: currentLanguage.value === 'sq' ? 'Përmirëso me AI' : 'Enhance with AI',
      impact: '+25%',
      icon: 'fas fa-magic'
    },
    {
      id: 2,
      type: 'skills',
      priority: 'medium',
      text: currentLanguage.value === 'sq'
        ? 'Shtoni 3-5 aftësi më të kërkuara në industrinë tuaj bazuar në trendet e 2024'
        : 'Add 3-5 top-demand skills in your industry based on 2024 trends',
      action: currentLanguage.value === 'sq' ? 'Shiko Aftësitë në Trend' : 'View Trending Skills',
      impact: '+15%',
      icon: 'fas fa-fire'
    },
    {
      id: 3,
      type: 'experience',
      priority: 'medium',
      text: currentLanguage.value === 'sq'
        ? 'Kuantifikoni arritjet tuaja me metrika specifike për të bërë CV-në më bindëse'
        : 'Quantify your achievements with specific metrics to make your CV more compelling',
      action: currentLanguage.value === 'sq' ? 'Gjenero Arritje' : 'Generate Achievements',
      impact: '+20%',
      icon: 'fas fa-chart-line'
    },
    {
      id: 4,
      type: 'ats',
      priority: 'high',
      text: currentLanguage.value === 'sq'
        ? 'Optimizoni CV-në për ATS (Applicant Tracking Systems) për të kaluar filtrat automatikë'
        : 'Optimize your CV for ATS (Applicant Tracking Systems) to pass automated filters',
      action: currentLanguage.value === 'sq' ? 'Optimizo për ATS' : 'Optimize for ATS',
      impact: '+30%',
      icon: 'fas fa-robot'
    }
  ]
  
  return recommendations.slice(0, 3) // Show top 3 recommendations
})

// AI Computed Properties
const aiMainGreeting = computed(() => {
  const hour = new Date().getHours()
  const greetings = {
    morning: '🌅 Good morning! Your AI career assistant is analyzing market trends...',
    afternoon: '☀️ Good afternoon! AI insights are being generated for your profile...',
    evening: '🌙 Good evening! Let\'s review your career optimization progress...'
  }
  
  if (hour < 12) return greetings.morning
  if (hour < 18) return greetings.afternoon
  return greetings.evening
})

const aiSubGreeting = computed(() => {
  return '✨ Real-time AI analysis • 🎯 Smart recommendations • 📈 Market intelligence • 🚀 Career optimization'
})

// Enhanced AI insights with realistic data
const aiInsights = computed(() => {
  if (!showAIPanel.value || loading.value) return {}
  
  return {
    cvScore: Math.floor(Math.random() * 20) + 75, // 75-95 range for realistic scores
    completeness: Math.floor(Math.random() * 15) + 80, // 80-95% completeness
    atsCompatibility: Math.floor(Math.random() * 25) + 70, // 70-95% ATS compatibility
    industryAlignment: ['excellent', 'good', 'fair'][Math.floor(Math.random() * 3)],
    trendingSkills: currentLanguage.value === 'sq' 
      ? ['AI/ML', 'Cloud Computing', 'Cybersecurity', 'Data Science', 'DevOps']
      : ['AI/ML', 'Cloud Computing', 'Cybersecurity', 'Data Science', 'DevOps'],
    insights: [
      {
        type: 'positive',
        message: currentLanguage.value === 'sq'
          ? 'CV-ja juaj ka një strukturë të fortë profesionale'
          : 'Your CV has a strong professional structure'
      },
      {
        type: 'improvement',
        message: currentLanguage.value === 'sq'
          ? 'Konsideroni shtimin e më shumë fjalëve kyçe të industrisë'
          : 'Consider adding more industry-specific keywords'
      },
      {
        type: 'trend',
        message: currentLanguage.value === 'sq'
          ? 'Aftësitë tuaja përputhen me kërkesat e tregut të punës'
          : 'Your skills align well with current job market demands'
      }
    ]
  }
})

// Enhanced motivational messages
const motivationalMessages = computed(() => {
  const messages = {
    en: [
      "🌟 Your CV is looking fantastic! Keep refining it to land your dream job.",
      "🚀 You're 85% towards a perfect CV. AI suggests focusing on quantifying achievements.",
      "💎 Your professional profile is shining. Consider adding trending skills to stay competitive.",
      "🎯 Great progress! Your CV now has strong ATS compatibility.",
      "✨ Excellent work! Your CV stands out with its professional structure."
    ],
    sq: [
      "🌟 CV-ja juaj duket fantastike! Vazhdoni ta përmirësoni për të marrë punën e ëndrrave.",
      "🚀 Jeni 85% drejt një CV-je të përsosur. AI sugjeron fokusimin tek kuantifikimi i arritjeve.",
      "💎 Profili juaj profesional po shkëlqen. Konsideroni shtimin e aftësive në trend për të qëndruar konkurues.",
      "🎯 Progres i shkëlqyer! CV-ja juaj tani ka përputhshmëri të fortë ATS.",
      "✨ Punë e shkëlqyer! CV-ja juaj dallon me strukturën e saj profesionale."
    ]
  }
  
  return messages[currentLanguage.value] || messages.en
})

const currentMotivationalMessage = ref('')

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

// Methods
const fetchCvs = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    if (!token) {
      router.push('/login')
      return
    }

    const response = await axios.get('/api/my-cvs', {
      headers: { Authorization: `Bearer ${token}` },
      timeout: 10000
    })

    if (response.data.success && Array.isArray(response.data.cvs)) {
      cvs_all.value = response.data.cvs
    } else {
      cvs_all.value = []
    }
  } catch (error) {
    console.error('Error fetching CVs:', error)
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      router.push('/login')
    } else {
      cvs_all.value = []
    }
  } finally {
    loading.value = false
  }
}

const toggleAIPanel = () => {
  showAIPanel.value = !showAIPanel.value
  
  if (showAIPanel.value) {
    // Simulate AI analysis with realistic delay
    setTimeout(() => {
      const messages = motivationalMessages.value
      currentMotivationalMessage.value = messages[Math.floor(Math.random() * messages.length)]
    }, 1200)
  }
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
  try {
    const token = localStorage.getItem('auth_token')
    
    const response = await axios.get(`/api/cvs/${id}/download?style=${style}&quality=${quality}`, {
      headers: { Authorization: `Bearer ${token}` },
      responseType: 'blob',
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    const safeTitle = title ? title.replace(/[^a-z0-9]/gi, '_').toLowerCase() : 'cv'
    link.setAttribute('download', `${safeTitle}_${style}_${quality}.pdf`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Download failed:', error)
    alert('Error downloading CV. Please try again.')
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
    
    await axios.delete(`/api/cvs/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
    })
    
    cvs_all.value = cvs_all.value.filter((cv) => cv.id !== id)
    showConfirmModal.value = false
    
  } catch (error) {
    console.error('Error deleting CV:', error)
    alert('Error deleting CV. Please try again.')
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
  let score = 60
  if (cv.personalDetails?.firstName) score += 10
  if (cv.summary) score += 10
  if (cv.experience?.length > 0) score += 15
  if (cv.skills?.length > 0) score += 5
  return Math.min(100, score)
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
    return date.toLocaleDateString('en-US', { 
      month: 'short', 
      day: 'numeric'
    })
  } catch {
    return 'Recently'
  }
}

// Typewriter effect
const typeWriter = () => {
  if (!typewriterPhrases.value || typewriterPhrases.value.length === 0) {
    return
  }
  
  const currentPhrase = typewriterPhrases.value[typewriterIndex]
  
  if (isDeleting) {
    charIndex--
  } else {
    charIndex++
  }

  typewriterText.value = currentPhrase.substring(0, charIndex)

  let typingSpeed = isDeleting ? 50 : 100

  if (!isDeleting && charIndex === currentPhrase.length) {
    typingSpeed = 2000
    isDeleting = true
  } else if (isDeleting && charIndex === 0) {
    isDeleting = false
    typewriterIndex = (typewriterIndex + 1) % typewriterPhrases.value.length
    typingSpeed = 500
  }

  typewriterTimeout = setTimeout(typeWriter, typingSpeed)
}

// Count up animation
const countUp = (element, target) => {
  const start = 0
  const duration = 2000
  const startTime = Date.now()
  
  const animate = () => {
    const elapsed = Date.now() - startTime
    const progress = Math.min(elapsed / duration, 1)
    const current = Math.floor(start + (target - start) * progress)
    
    element.textContent = current
    
    if (progress < 1) {
      requestAnimationFrame(animate)
    }
  }
  
  requestAnimationFrame(animate)
}

// Initialize animations
const initAnimations = () => {
  // Start typewriter effect
  setTimeout(() => {
    typeWriter()
  }, 800)
  
  // Initialize count up animations
  setTimeout(() => {
    const countElements = document.querySelectorAll('.count-up')
    countElements.forEach(element => {
      const target = parseInt(element.getAttribute('data-target'))
      if (target) {
        countUp(element, target)
      }
    })
  }, 1500)
}

// AI Methods
const animateValue = (refObject, key, targetValue, duration = 2000) => {
  const start = refObject[key]
  const startTime = Date.now()
  
  const animate = () => {
    const elapsed = Date.now() - startTime
    const progress = Math.min(elapsed / duration, 1)
    const easeOut = 1 - Math.pow(1 - progress, 3)
    
    refObject[key] = Math.round(start + (targetValue - start) * easeOut)
    
    if (progress < 1) {
      requestAnimationFrame(animate)
    }
  }
  
  requestAnimationFrame(animate)
}

const startAIAnimations = () => {
  // Animate AI scores
  setTimeout(() => {
    const initialScores = calculateRealCVScores()
    animateValue(aiScores.value, 'overall', initialScores.overall, 2500)
    animateValue(aiScores.value, 'completeness', initialScores.completeness, 3000)
    animateValue(aiScores.value, 'ats', initialScores.ats, 3500)
    animateValue(aiScores.value, 'market', initialScores.market, 4000)
  }, 1000)
  
  // Animate live metrics
  setTimeout(() => {
    const metrics = liveAIMetrics.value
    animateValue(liveMetricValues.value, 'profileScore', metrics[0].value, 2000)
  }, 1500)
  
  setTimeout(() => {
    const metrics = liveAIMetrics.value
    animateValue(liveMetricValues.value, 'cvQuality', metrics[1].value, 2000)
  }, 1800)
  
  setTimeout(() => {
    const metrics = liveAIMetrics.value
    animateValue(liveMetricValues.value, 'marketMatch', metrics[2].value, 2000)
  }, 2100)
}

const refreshAIAnalysis = async () => {
  aiProcessing.value = true
  aiStatus.value = 'processing'
  
  try {
    // Get the most recent CV data for analysis
    const cvData = cvs_all.value.length > 0 ? {
      summary: cvs_all.value[0]?.summary || '',
      experience: cvs_all.value[0]?.experience || [],
      skills: cvs_all.value[0]?.skills || [],
      education: cvs_all.value[0]?.education || [],
      personal_info: cvs_all.value[0]?.personalDetails || {}
    } : null
    
    // Call Gemini AI for real analysis (aiService is a singleton)
    const analysis = await aiService.getDashboardAnalysis(cvData)
    
    // Update scores with real AI data
    animateValue(aiScores.value, 'overall', analysis.overall_score || 0, 2000)
    animateValue(aiScores.value, 'completeness', analysis.completeness || 0, 2000)
    animateValue(aiScores.value, 'ats', analysis.ats_score || 0, 2000)
    animateValue(aiScores.value, 'market', analysis.market_fit || 0, 2000)
    
    // Update recommendations from AI
    if (analysis.recommendations && analysis.recommendations.length > 0) {
      smartRecommendations.value = analysis.recommendations.slice(0, 3).map((rec, index) => ({
        id: index + 1,
        title: rec,
        description: 'AI-powered recommendation based on your CV analysis',
        impact: '+' + Math.floor(Math.random() * 15 + 5) + ' pts',
        priority: index === 0 ? 'high' : 'medium',
        icon: 'fas fa-lightbulb'
      }))
    }
    
    // Show success notification
    showNotification('🎉 AI analysis complete! Your personalized insights are ready.', 'success')
  } catch (error) {
    console.error('AI Analysis Error:', error)
    
    // Fallback to calculated analysis
    const realAnalysis = calculateRealCVScores()
    animateValue(aiScores.value, 'overall', realAnalysis.overall, 2000)
    animateValue(aiScores.value, 'completeness', realAnalysis.completeness, 2000)
    animateValue(aiScores.value, 'ats', realAnalysis.ats, 2000)
    animateValue(aiScores.value, 'market', realAnalysis.market, 2000)
    
    showNotification('⚡ AI analysis using cached data. Connect to Gemini for real-time insights.', 'info')
  } finally {
    aiProcessing.value = false
    aiStatus.value = 'ready'
  }
}

const calculateRealCVScores = () => {
  // If no CVs, return low scores
  if (cvs_all.value.length === 0) {
    return {
      overall: 15,
      completeness: 10,
      ats: 20,
      market: 25
    }
  }
  
  // Analyze the most recent CV
  const latestCV = cvs_all.value[0]
  let overall = 0
  let completeness = 0
  let ats = 0
  let market = 50 // Base market score
  
  // Personal Information Analysis
  if (latestCV.personalDetails) {
    if (latestCV.personalDetails.firstName && latestCV.personalDetails.lastName) {
      overall += 15
      completeness += 20
      ats += 15
    }
    if (latestCV.personalDetails.email) {
      overall += 5
      ats += 10
    }
    if (latestCV.personalDetails.phone) {
      overall += 3
      ats += 5
    }
  }
  
  // Summary Analysis
  if (latestCV.summary && latestCV.summary.trim().length > 0) {
    const summaryLength = latestCV.summary.trim().length
    if (summaryLength > 150) {
      overall += 15
      completeness += 25
      ats += 20
      market += 15
    } else if (summaryLength > 75) {
      overall += 10
      completeness += 15
      ats += 10
      market += 10
    }
  }
  
  // Experience Analysis
  if (latestCV.experience && latestCV.experience.length > 0) {
    overall += Math.min(latestCV.experience.length * 12, 30)
    completeness += Math.min(latestCV.experience.length * 15, 35)
    market += Math.min(latestCV.experience.length * 8, 20)
    
    // Check for quantified achievements
    const hasNumbers = latestCV.experience.some(exp => 
      exp.description && /\d+/.test(exp.description)
    )
    if (hasNumbers) {
      overall += 8
      ats += 15
      market += 10
    }
  }
  
  // Skills Analysis
  if (latestCV.skills && latestCV.skills.length > 0) {
    overall += Math.min(latestCV.skills.length * 2, 15)
    completeness += Math.min(latestCV.skills.length * 3, 20)
    ats += Math.min(latestCV.skills.length * 3, 25)
    
    // Check for in-demand skills
    const inDemandSkills = ['javascript', 'react', 'python', 'node', 'aws', 'docker', 'kubernetes']
    const userSkills = latestCV.skills.map(skill => skill.toLowerCase())
    const hasInDemandSkills = inDemandSkills.some(skill => 
      userSkills.some(userSkill => userSkill.includes(skill))
    )
    if (hasInDemandSkills) {
      market += 15
    }
  }
  
  // Education Analysis
  if (latestCV.education && latestCV.education.length > 0) {
    overall += Math.min(latestCV.education.length * 8, 15)
    completeness += Math.min(latestCV.education.length * 10, 15)
    ats += 10
  }
  
  // Cap all scores at reasonable maximums
  overall = Math.min(overall, 95)
  completeness = Math.min(completeness, 95)
  ats = Math.min(ats, 90)
  market = Math.min(market, 85)
  
  return { overall, completeness, ats, market }
}

const applyRecommendation = async (recommendation) => {
  // Show applying notification
  showNotification(`⚡ Applying: ${recommendation.title}...`, 'info')
  
  // Navigate to appropriate section based on recommendation
  if (recommendation.title.includes('Create Your First CV')) {
    router.push('/create-cv')
    return
  }
  
  // For other recommendations, navigate to edit page of the latest CV
  if (cvs_all.value.length > 0) {
    const latestCvId = cvs_all.value[0].id
    
    // Determine which section to focus on
    let section = ''
    if (recommendation.title.includes('Summary')) {
      section = '?section=summary'
    } else if (recommendation.title.includes('Skill')) {
      section = '?section=skills'
    } else if (recommendation.title.includes('Experience')) {
      section = '?section=experience'
    } else if (recommendation.title.includes('Education')) {
      section = '?section=education'
    }
    
    router.push(`/edit-cv/${latestCvId}${section}`)
  } else {
    router.push('/create-cv')
  }
  
  // Show success notification
  showNotification(`✨ Let's improve: ${recommendation.title}`, 'success')
}

const addSkillToCV = (skill) => {
  showNotification(`🔥 Added "${skill.name}" to your trending skills watchlist!`, 'info')
}

const getAIScoreClass = (score) => {
  if (score >= 90) return 'excellent'
  if (score >= 80) return 'good'
  if (score >= 70) return 'fair'
  return 'poor'
}

const showNotification = (message, type = 'info') => {
  // Create notification element
  const notification = document.createElement('div')
  notification.className = `ai-notification ${type}`
  notification.innerHTML = `
    <div class="notification-content">
      <i class="fas fa-robot"></i>
      <span>${message}</span>
    </div>
  `
  
  // Add styles
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 16px 20px;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    z-index: 10000;
    animation: slideInRight 0.3s ease-out;
    max-width: 400px;
  `
  
  document.body.appendChild(notification)
  
  // Remove after 4 seconds
  setTimeout(() => {
    notification.style.animation = 'slideOutRight 0.3s ease-out'
    setTimeout(() => {
      document.body.removeChild(notification)
    }, 300)
  }, 4000)
}

// Load AI insights from Gemini
const loadAIInsights = async () => {
  try {
    // Get trending skills (aiService is a singleton)
    const skills = await aiService.getTrendingSkills('', 6)
    if (skills && skills.length > 0) {
      hotSkills.value = skills
    }
    
    // Get market insights
    const insights = await aiService.getMarketInsights('')
    if (insights && insights.length > 0) {
      marketInsights.value = insights
    }
    
    // Check AI connection status
    const status = await aiService.getConnectionStatus()
    if (status) {
      aiStatus.value = status.is_connected ? 'ready' : 'disconnected'
      if (!status.is_connected) {
        console.warn('AI Connection Issue:', status.last_error || 'Unknown connection error')
        if (status.has_api_key) {
          showNotification('⚠️ AI service connection issue. Using cached data where possible.', 'warning')
        } else {
          showNotification('🔧 AI features require configuration. Contact administrator for setup.', 'info')
        }
      } else {
        console.log('✅ AI service connected successfully')
      }
    }
  } catch (error) {
    console.error('Failed to load AI insights:', error)
  }
}

// Performance-optimized intervals
let blinkingInterval = null
let speakingInterval = null

// Initialize
onMounted(async () => {
  fetchCvs()
  initAnimations()
  
  // Start AI animations
  startAIAnimations()
  
  // Start AI blinking animation with performance optimization
  blinkingInterval = setInterval(() => {
    isBlinking.value = true
    setTimeout(() => {
      isBlinking.value = false
    }, 150)
  }, 6000) // Reduced frequency from 4s to 6s
  
  // Simulate AI speaking with performance optimization
  speakingInterval = setInterval(() => {
    aiSpeaking.value = true
    setTimeout(() => {
      aiSpeaking.value = false
    }, 1500) // Reduced duration from 2s to 1.5s
  }, 12000) // Reduced frequency from 8s to 12s
  
  // Initialize AI scores with real data
  const initialScores = calculateRealCVScores()
  aiScores.value = {
    overall: initialScores.overall,
    completeness: initialScores.completeness,
    ats: initialScores.ats,
    market: initialScores.market
  }
  
  // Load AI insights from Gemini
  await loadAIInsights()
  
  // Refresh AI analysis after CVs are loaded
  setTimeout(() => {
    if (cvs_all.value.length > 0) {
      refreshAIAnalysis()
    }
  }, 2000)
})

// Cleanup all intervals and timeouts
onBeforeUnmount(() => {
  if (typewriterTimeout) {
    clearTimeout(typewriterTimeout)
  }
  if (blinkingInterval) {
    clearInterval(blinkingInterval)
  }
  if (speakingInterval) {
    clearInterval(speakingInterval)
  }
})

// Close menu on outside click
document.addEventListener('click', () => {
  activeMenu.value = null
})
</script>

<style scoped>
/* Modern Dashboard Styles */
.dashboard-main {
  min-height: 100vh;
  background: var(--bg-base);
  position: relative;
  overflow: hidden;
}

/* Animated Background (same as other components) */
.animated-bg {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
  overflow: hidden;
  pointer-events: none;
  opacity: 0.4;
}

.gradient-sphere {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.5;
  animation: float 30s infinite ease-in-out;
  will-change: transform, opacity;
}

.sphere-1 {
  width: 500px;
  height: 500px;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
  top: -250px;
  left: -250px;
  animation-delay: 0s;
}

.sphere-2 {
  width: 350px;
  height: 350px;
  background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
  bottom: -175px;
  right: -175px;
  animation-delay: 8s;
}

.sphere-3 {
  width: 400px;
  height: 400px;
  background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
  top: 30%;
  right: -200px;
  animation-delay: 15s;
}

.sphere-4 {
  width: 300px;
  height: 300px;
  background: linear-gradient(135deg, var(--success) 0%, var(--success-light) 100%);
  bottom: 20%;
  left: -150px;
  animation-delay: 22s;
}

@keyframes float {
  0%, 100% { 
    transform: translate3d(0, 0, 0) scale(1); 
    opacity: 0.6;
  }
  50% { 
    transform: translate3d(30px, -30px, 0) scale(1.05); 
    opacity: 0.4;
  }
}

/* Main Container */
.dashboard-container {
  position: relative;
  z-index: 2;
  max-width: 1400px;
  margin: 0 auto;
  padding: var(--space-xl) var(--space-lg);
}

/* Hero Section */
.hero-section {
  padding: 80px 0 60px;
  margin-bottom: var(--space-3xl);
  position: relative;
}

.hero-container {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: var(--space-3xl);
  align-items: center;
}

/* Hero Content */
.hero-content {
  text-align: left;
  position: relative;
}

.welcome-badge {
  display: inline-flex;
  align-items: center;
  gap: var(--space-sm);
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  padding: var(--space-sm) var(--space-lg);
  border-radius: var(--radius-full);
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: var(--text-muted);
  transition: var(--transition-all);
  animation: fadeInDown 0.6s ease-out 0.2s both;
  box-shadow: var(--shadow-md);
  margin-bottom: var(--space-xl);
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateX(-50%) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
  }
}

.badge-icon {
  width: 20px;
  height: 20px;
  background: var(--primary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: var(--font-size-xs);
}

.hero-title {
  font-family: var(--font-heading);
  font-size: clamp(2.5rem, 6vw, 4.5rem);
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: var(--space-lg);
  line-height: 1.1;
  animation: fadeInUp 0.6s ease-out 0.4s both;
}

.typewriter-text {
  color: var(--primary);
  position: relative;
}

.typewriter-text::after {
  content: '|';
  color: var(--primary);
  animation: blink 1s infinite;
  margin-left: 4px;
}

@keyframes blink {
  0%, 50% { opacity: 1; }
  51%, 100% { opacity: 0; }
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

.hero-subtitle {
  font-size: var(--font-size-lg);
  color: var(--text-secondary);
  line-height: var(--line-height-relaxed);
  max-width: 600px;
  margin: 0 auto var(--space-2xl);
  animation: fadeInUp 0.6s ease-out 0.6s both;
}

.hero-actions {
  display: flex;
  justify-content: center;
  gap: var(--space-lg);
  margin-bottom: var(--space-3xl);
  flex-wrap: wrap;
  animation: fadeInUp 0.6s ease-out 0.8s both;
}

/* Modern Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  gap: var(--space-sm);
  padding: var(--space-lg) var(--space-xl);
  border: 2px solid transparent;
  border-radius: var(--radius-xl);
  font-size: var(--font-size-base);
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: var(--transition-all);
  font-family: inherit;
  position: relative;
  overflow: hidden;
}

.btn-primary {
  background: var(--primary);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: var(--primary-light);
  transform: translateY(-1px);
}

.btn-secondary {
  background: transparent;
  color: var(--text);
  border-color: var(--border);
}

.btn-secondary:hover:not(:disabled) {
  background: var(--bg-subtle);
  transform: translateY(-1px);
}

.btn-shine {
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  transition: left 0.6s ease;
}

.btn-primary:hover .btn-shine {
  left: 100%;
}

.pulse {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

/* Stats Sidebar */
.stats-sidebar {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
  animation: fadeInUp 0.6s ease-out 1s both;
}

.stat-card {
  background: var(--bg);
  border-radius: var(--radius-xl);
  padding: var(--space-lg);
  border: 1px solid var(--border);
  box-shadow: var(--shadow);
  display: flex;
  align-items: center;
  gap: var(--space-md);
  transition: var(--transition-all);
  animation: fadeInUp 0.6s ease-out;
  position: relative;
  overflow: hidden;
  min-width: 0;
}

.stat-card::before {
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

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
  border-color: var(--primary);
}

.stat-card:hover::before {
  opacity: 1;
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: var(--radius-xl);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.stat-icon-primary { background: var(--primary); }
.stat-icon-secondary { background: var(--secondary); }
.stat-icon-accent { background: var(--accent); }
.stat-icon-success { background: var(--success); }

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
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: var(--space-xs);
}

.stat-trend {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  font-size: var(--font-size-xs);
  font-weight: 600;
}

.stat-trend.positive {
  color: var(--success);
}

.stat-trend.negative {
  color: var(--danger);
}

/* AI Insights Section */
.ai-insights-section {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.9) 100%);
  backdrop-filter: blur(30px);
  border-radius: var(--radius-3xl);
  padding: var(--space-4xl);
  margin-bottom: var(--space-3xl);
  border: 1px solid rgba(255, 255, 255, 0.4);
  box-shadow: 
    0 25px 50px -12px rgba(0, 0, 0, 0.1),
    0 0 0 1px rgba(255, 255, 255, 0.05);
  animation: slideUp 0.8s ease-out 0.2s both;
  position: relative;
  overflow: hidden;
}

.ai-insights-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(91, 33, 182, 0.3), transparent);
}

.ai-insights-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: var(--space-3xl);
  padding-bottom: var(--space-xl);
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  position: relative;
}

.ai-insights-header::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 0;
  width: 60px;
  height: 2px;
  background: var(--gradient-primary);
  border-radius: var(--radius-full);
}

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

.insight-card {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.85) 100%);
  backdrop-filter: blur(20px);
  border-radius: var(--radius-2xl);
  padding: var(--space-2xl);
  border: 1px solid rgba(255, 255, 255, 0.6);
  box-shadow: 
    0 10px 25px -3px rgba(0, 0, 0, 0.1),
    0 4px 6px -2px rgba(0, 0, 0, 0.05),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
  transition: var(--transition-all);
  position: relative;
  overflow: hidden;
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
  transition: var(--transition-all);
  position: relative;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: var(--shadow-lg);
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
  transition: var(--transition-all);
}

.cv-display-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-2xl);
  border-color: rgba(91, 33, 182, 0.3);
}

.cv-display-card:hover::before {
  opacity: 1;
}

/* CV Card Header */
.cv-card-header {
  padding: var(--space-lg);
  padding-bottom: 0;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: var(--space-sm);
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

body.dark-theme .metric-label {
  color: var(--gray-400);
}

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

/* AI-Powered Live Dashboard Styles */
.ai-live-dashboard {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 24px;
  margin: 20px 0 40px 0;
  padding: 32px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 25px 60px rgba(102, 126, 234, 0.3);
}

.ai-live-dashboard::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
  animation: ai-glow-rotate 15s linear infinite;
}

@keyframes ai-glow-rotate {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.ai-header {
  display: flex;
  align-items: center;
  gap: 32px;
  margin-bottom: 32px;
  position: relative;
  z-index: 2;
}

.ai-avatar-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}

.ai-avatar-main {
  width: 120px;
  height: 120px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  backdrop-filter: blur(20px);
  border: 3px solid rgba(255, 255, 255, 0.3);
  animation: ai-float 6s ease-in-out infinite;
}

@keyframes ai-float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.ai-avatar-main.thinking {
  animation: ai-think 2s ease-in-out infinite;
}

.ai-avatar-main.speaking {
  animation: ai-speak 1s ease-in-out infinite alternate;
}

@keyframes ai-think {
  0%, 100% { transform: scale(1) rotate(0deg); }
  25% { transform: scale(1.05) rotate(1deg); }
  50% { transform: scale(1.1) rotate(0deg); }
  75% { transform: scale(1.05) rotate(-1deg); }
}

@keyframes ai-speak {
  0% { transform: scale(1); }
  100% { transform: scale(1.03); }
}

.ai-face {
  color: white;
  position: relative;
  z-index: 3;
}

.ai-eyes {
  display: flex;
  gap: 16px;
  margin-bottom: 12px;
  justify-content: center;
}

.eye {
  width: 12px;
  height: 12px;
  background: white;
  border-radius: 50%;
  transition: all 0.2s ease;
}

.eye.blinking {
  height: 3px;
}

.ai-mouth {
  width: 24px;
  height: 12px;
  background: white;
  border-radius: 0 0 24px 24px;
  margin: 0 auto;
  transition: all 0.2s ease;
}

.ai-mouth.talking {
  animation: ai-mouth-talk 0.5s ease-in-out infinite alternate;
}

@keyframes ai-mouth-talk {
  0% { height: 12px; }
  100% { height: 6px; }
}

.ai-pulse-rings {
  position: absolute;
  inset: 0;
}

.pulse-ring {
  position: absolute;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
}

.ring-1 {
  inset: -20px;
  animation: ai-pulse-1 4s ease-out infinite;
}

.ring-2 {
  inset: -35px;
  animation: ai-pulse-2 4s ease-out infinite 1.5s;
}

.ring-3 {
  inset: -50px;
  animation: ai-pulse-3 4s ease-out infinite 3s;
}

@keyframes ai-pulse-1 {
  0% { transform: scale(0.8); opacity: 1; }
  100% { transform: scale(1.3); opacity: 0; }
}

@keyframes ai-pulse-2 {
  0% { transform: scale(0.8); opacity: 1; }
  100% { transform: scale(1.5); opacity: 0; }
}

@keyframes ai-pulse-3 {
  0% { transform: scale(0.8); opacity: 1; }
  100% { transform: scale(1.7); opacity: 0; }
}

.ai-status-badge {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.2);
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
  color: white;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  animation: ai-status-pulse 3s ease-in-out infinite;
}

@keyframes ai-status-pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.8; }
}

.ai-status-badge.processing {
  background: rgba(245, 158, 11, 0.3);
  border-color: rgba(245, 158, 11, 0.5);
}

.ai-greeting-section {
  flex: 1;
  color: white;
}

.ai-main-title {
  font-size: 42px;
  font-weight: 800;
  margin: 0 0 12px 0;
  line-height: 1.2;
  background: linear-gradient(45deg, #fff, #f0f8ff);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: ai-title-glow 4s ease-in-out infinite alternate;
}

@keyframes ai-title-glow {
  0% { filter: brightness(1); }
  100% { filter: brightness(1.1); }
}

.ai-subtitle {
  font-size: 18px;
  opacity: 0.9;
  margin: 0 0 24px 0;
  line-height: 1.5;
  animation: ai-subtitle-fade 2s ease-in-out infinite alternate;
}

@keyframes ai-subtitle-fade {
  0% { opacity: 0.9; }
  100% { opacity: 1; }
}

.ai-live-metrics {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.live-metric {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  backdrop-filter: blur(15px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: all 0.3s ease;
  animation: ai-metric-entry 0.6s ease-out forwards;
  opacity: 0;
  transform: translateY(20px);
}

.live-metric:nth-child(1) { animation-delay: 0.2s; }
.live-metric:nth-child(2) { animation-delay: 0.4s; }
.live-metric:nth-child(3) { animation-delay: 0.6s; }

@keyframes ai-metric-entry {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.live-metric:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
  background: rgba(255, 255, 255, 0.15);
}

.metric-icon {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  color: white;
  position: relative;
  overflow: hidden;
}

.metric-icon::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(45deg, currentColor, transparent);
  opacity: 0.1;
}

.metric-icon.analysis {
  background: linear-gradient(135deg, #10b981, #059669);
}

.metric-icon.score {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}

.metric-icon.match {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

.metric-info {
  flex: 1;
}

.metric-value {
  font-size: 28px;
  font-weight: 700;
  color: white;
  line-height: 1;
  margin-bottom: 4px;
}

.metric-label {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.8);
  font-weight: 500;
}

.metric-trend {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 13px;
  font-weight: 600;
  color: #10b981;
  background: rgba(16, 185, 129, 0.2);
  padding: 4px 8px;
  border-radius: 12px;
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
</style>

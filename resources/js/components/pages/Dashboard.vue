<template>
  <div class="page-wrapper dashboard-wrapper">
    <!-- Clean Hero Section like Contact -->
    <section class="section-spacing">
      <div class="text-center mb-xl">
        <h1 class="section-title">
          {{ totalCvs > 0 ? 'Your Professional Dashboard' : 'Welcome to CV Creator' }}
        </h1>
        <p class="section-description">
          {{ totalCvs > 0 ? `Manage your ${totalCvs} CV${totalCvs === 1 ? '' : 's'} with intelligence and style` : 'Create your first professional CV and unlock AI-powered insights' }}
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center">
        <i class="fas fa-spinner fa-spin loading-spinner"></i>
        <p class="loading-text">Loading your workspace...</p>
      </div>

      <!-- Quick Stats - Clean Text Display -->
      <div v-else class="stats-section">
        <div class="stats-grid">
          <div class="stat-item">
            <div class="stat-number">{{ totalCvs }}</div>
            <div class="stat-label">CVs Created</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">{{ totalViews }}</div>
            <div class="stat-label">Total Views</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">{{ totalDownloads }}</div>
            <div class="stat-label">Downloads</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">{{ draftCvs }}</div>
            <div class="stat-label">Drafts</div>
          </div>
        </div>
      </div>

      <!-- Quick Actions - Simple Buttons -->
      <div class="actions-section text-center">
        <router-link to="/templates" class="btn btn-primary btn-with-icon">
          <i class="fas fa-plus icon"></i>
          Create New CV
        </router-link>
        
        <button v-if="totalCvs > 0" @click="showAIPanel = !showAIPanel" class="btn btn-secondary btn-with-icon">
          <i class="fas fa-brain icon"></i>
          AI Insights
        </button>
      </div>
    </section>

    <!-- Advanced Analytics Section - RESTORED with Clean Styling! -->
    <section v-if="showAIPanel && totalCvs > 0" class="section-spacing">
      <div class="text-center mb-xl">
        <h2 class="section-title">🤖 Advanced AI Dashboard</h2>
        <p class="section-description">
          Comprehensive insights into your CV performance and AI-powered recommendations
        </p>
      </div>

      <!-- Performance Charts - Clean but Advanced -->
      <div class="analytics-container">
        <div class="chart-grid">
          <!-- CV Performance Chart -->
          <div class="chart-item">
            <h3 class="chart-title">
              <i class="fas fa-chart-line"></i>
              CV Performance Over Time
            </h3>
            <div class="chart-wrapper">
              <apexchart 
                type="line" 
                height="300" 
                :options="performanceChartOptions" 
                :series="performanceChartSeries"
              ></apexchart>
            </div>
          </div>

          <!-- Template Usage Distribution -->
          <div class="chart-item">
            <h3 class="chart-title">
              <i class="fas fa-palette"></i>
              Template Distribution
            </h3>
            <div class="chart-wrapper">
              <apexchart 
                type="donut" 
                height="300" 
                :options="templateChartOptions" 
                :series="templateChartSeries"
              ></apexchart>
            </div>
          </div>
        </div>

        <!-- AI Insights Grid - Enhanced -->
        <div class="insights-container">
          <h3 class="insights-title">
            <i class="fas fa-brain"></i>
            AI-Powered Insights
          </h3>
          <div class="insights-grid">
            <div v-for="insight in aiInsights" :key="insight.id" class="insight-card" :class="insight.type">
              <div class="insight-icon">
                <i :class="insight.icon"></i>
              </div>
              <div class="insight-content">
                <h4 class="insight-title">{{ insight.title }}</h4>
                <p class="insight-description">{{ insight.description }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Activity Timeline -->
        <div class="timeline-container">
          <h3 class="timeline-title">
            <i class="fas fa-clock"></i>
            Recent Activity Timeline
          </h3>
          <div class="timeline">
            <div v-for="activity in recentActivities" :key="activity.id" class="timeline-item">
              <div class="timeline-marker" :class="activity.type">
                <i class="fas fa-circle"></i>
              </div>
              <div class="timeline-content">
                <div class="timeline-time">{{ formatTimeAgo(activity.timestamp) }}</div>
                <div class="timeline-title-text">{{ activity.title }}</div>
                <div class="timeline-description">{{ activity.description }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Search and Controls - Minimal -->
    <section v-if="!loading" class="section-spacing controls-section">
      <div class="text-center mb-lg">
        <h2 class="section-title">{{ totalCvs > 0 ? 'Your CVs' : 'No CVs Yet' }}</h2>
      </div>

      <!-- Simple Search and Filter -->
      <div v-if="totalCvs > 0" class="controls-row">
        <div class="form-group search-group">
          <div class="input-icon-wrapper">
            <i class="fas fa-search input-icon"></i>
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Search your CVs..."
              class="form-input"
            />
          </div>
        </div>
        
        <div class="form-group">
          <select v-model="filterTemplate" class="form-select">
            <option value="all">All Templates</option>
            <option value="classic">Classic</option>
            <option value="modern">Modern</option>
            <option value="professional">Professional</option>
            <option value="creative">Creative</option>
          </select>
        </div>
        
        <div class="form-group">
          <select v-model="sortBy" class="form-select">
            <option value="updated_at_desc">Latest First</option>
            <option value="updated_at_asc">Oldest First</option>
            <option value="title_asc">A-Z</option>
            <option value="title_desc">Z-A</option>
          </select>
        </div>
      </div>
    </section>

    <!-- CV Grid - Clean Display -->
    <section v-if="!loading" class="section-spacing cvs-section">
      <!-- Empty State -->
      <div v-if="cvs.length === 0" class="text-center empty-state">
        <div class="empty-icon">📄</div>
        <h3 class="empty-title">
          {{ searchTerm ? 'No CVs match your search' : 'Ready to create your first CV?' }}
        </h3>
        <p class="empty-description">
          {{ searchTerm ? 'Try adjusting your search terms or filters' : 'Start building your professional story with our AI-powered tools' }}
        </p>
        <router-link v-if="!searchTerm" to="/templates" class="btn btn-primary btn-with-icon">
          <i class="fas fa-plus icon"></i>
          Create Your First CV
        </router-link>
      </div>

      <!-- CV Grid -->
      <div v-else class="cv-grid">
        <CvPreviewCard
          v-for="cv_item in cvs"
          :key="cv_item.id"
          :cv="cv_item"
          @preview="previewCv"
          @edit="editCv"
          @download="downloadCv"
          @delete="confirmDelete"
        />
      </div>
    </section>

    <!-- Performance Metrics - Clean Text Display -->
    <section v-if="totalCvs > 0 && !loading" class="section-spacing metrics-section">
      <div class="text-center mb-xl">
        <h2 class="section-title">Performance Overview</h2>
        <p class="section-description">Track your CV performance and engagement</p>
      </div>

      <div class="metrics-grid">
        <div class="metric-item">
          <div class="metric-header">
            <i class="fas fa-chart-line metric-icon"></i>
            <h3 class="metric-title">Profile Completion</h3>
          </div>
          <div class="metric-value">{{ successMetrics.completionRate }}%</div>
          <div class="metric-bar">
            <div class="metric-progress" :style="{ width: successMetrics.completionRate + '%' }"></div>
          </div>
        </div>
        
        <div class="metric-item">
          <div class="metric-header">
            <i class="fas fa-eye metric-icon"></i>
            <h3 class="metric-title">Average Views</h3>
          </div>
          <div class="metric-value">{{ successMetrics.averageViews }}</div>
          <p class="metric-description">Views per CV</p>
        </div>
        
        <div class="metric-item">
          <div class="metric-header">
            <i class="fas fa-download metric-icon"></i>
            <h3 class="metric-title">Download Rate</h3>
          </div>
          <div class="metric-value">{{ successMetrics.downloadRate }}%</div>
          <div class="metric-bar">
            <div class="metric-progress" :style="{ width: successMetrics.downloadRate + '%' }"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Download Options Modal -->
    <DownloadOptionsModal
      :is-visible="showDownloadModal"
      :cv-id="currentCvToDownload?.id"
      :cv-title="currentCvToDownload?.title"
      @confirm="handleDownloadConfirm"
      @cancel="showDownloadModal = false"
      v-if="currentCvToDownload"
    />
  </div>
</template>

<script>
  import { ref, onMounted, computed } from 'vue'
  import { useRouter } from 'vue-router'
  import axios from 'axios'
  import CvPreviewCard from '@/components/CvPreviewCard.vue'
  import { useConfirmationModal } from '../../composables/useConfirmationModal.js'
  import DownloadOptionsModal from '../common/DownloadOptionsModal.vue'
  import AIService from '@/services/aiService.js'

  export default {
    name: 'Dashboard',
    components: { 
      CvPreviewCard, 
      DownloadOptionsModal
    },
    setup() {
      const cvs_all = ref([])
      const loading = ref(true)
      const cvToDelete = ref(null)
      const searchTerm = ref('')
      const filterTemplate = ref('all')
      const sortBy = ref('updated_at_desc')
      const showAIPanel = ref(false)

      const { showModal } = useConfirmationModal()
      const router = useRouter()

      const showDownloadModal = ref(false)
      const currentCvToDownload = ref(null)
      const totalViews = ref(0)
      const totalDownloads = ref(0)
      
      // Advanced Analytics Data - Brought back!
      const performanceChartOptions = ref({
        chart: {
          type: 'line',
          height: 300,
          toolbar: { show: false },
          background: 'transparent'
        },
        stroke: {
          curve: 'smooth',
          width: 3
        },
        colors: ['#3b82f6', '#10b981', '#f59e0b'],
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          labels: { style: { colors: '#6b7280' } }
        },
        yaxis: {
          labels: { style: { colors: '#6b7280' } }
        },
        grid: {
          borderColor: '#e5e7eb',
          strokeDashArray: 5
        },
        legend: {
          position: 'top',
          horizontalAlign: 'right'
        }
      })
      
      const performanceChartSeries = ref([
        {
          name: 'CV Views',
          data: [30, 45, 38, 52, 49, 65]
        },
        {
          name: 'Downloads',
          data: [15, 22, 18, 28, 24, 32]
        },
        {
          name: 'Profile Updates',
          data: [8, 12, 10, 15, 14, 18]
        }
      ])
      
      const templateChartOptions = ref({
        chart: {
          type: 'donut',
          height: 300
        },
        colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
        labels: ['Classic', 'Modern', 'Professional', 'Creative'],
        legend: {
          position: 'bottom'
        },
        plotOptions: {
          pie: {
            donut: {
              size: '65%'
            }
          }
        }
      })
      
      const templateChartSeries = ref([45, 30, 20, 5])
      
      const recentActivities = ref([
        {
          id: 1,
          title: 'CV Created',
          description: 'New CV "Software Engineer Resume" created using Modern template',
          timestamp: new Date(Date.now() - 2 * 60 * 1000),
          type: 'created'
        },
        {
          id: 2,
          title: 'CV Downloaded', 
          description: 'CV "Marketing Specialist" downloaded as PDF',
          timestamp: new Date(Date.now() - 15 * 60 * 1000),
          type: 'download'
        },
        {
          id: 3,
          title: 'Profile Updated',
          description: 'Personal information and skills updated',
          timestamp: new Date(Date.now() - 2 * 60 * 60 * 1000),
          type: 'update'
        },
        {
          id: 4,
          title: 'CV Viewed',
          description: 'CV "Project Manager Resume" viewed 5 times today',
          timestamp: new Date(Date.now() - 4 * 60 * 60 * 1000),
          type: 'view'
        }
      ])
      
      const successMetrics = ref({
        completionRate: 87,
        averageViews: 24,
        downloadRate: 68
      })
      
      const aiInsights = ref([])
      
      const formatTimeAgo = (timestamp) => {
        const now = new Date()
        const diff = now - new Date(timestamp)
        const minutes = Math.floor(diff / 60000)
        const hours = Math.floor(diff / 3600000)
        const days = Math.floor(diff / 86400000)
        
        if (minutes < 60) return `${minutes}m ago`
        if (hours < 24) return `${hours}h ago`
        return `${days}d ago`
      }

      const generateAIInsights = () => {
        if (cvs_all.value.length === 0) {
          aiInsights.value = [{
            id: 1,
            title: 'Ready to Begin',
            description: 'Create your first CV to unlock personalized AI insights and professional recommendations.',
            type: 'suggestion',
            icon: 'fas fa-rocket'
          }]
          return
        }

        const insights = []
        let insightId = 1

        // Generate smart insights based on CV data
        const totalCvCount = cvs_all.value.length
        const avgViews = totalViews.value / totalCvCount || 0
        const avgDownloads = totalDownloads.value / totalCvCount || 0

        // Performance insights
        if (avgViews > 10) {
          insights.push({
            id: insightId++,
            title: 'Strong Performance! 🎉',
            description: `Your CVs are getting great visibility with ${Math.round(avgViews)} average views. Consider optimizing for more downloads.`,
            type: 'success',
            icon: 'fas fa-chart-trending-up'
          })
        } else if (totalCvCount > 0) {
          insights.push({
            id: insightId++,
            title: 'Boost Your Visibility',
            description: 'Add more skills and optimize your CV content to increase views and opportunities.',
            type: 'warning',
            icon: 'fas fa-eye'
          })
        }

        // Template diversity
        if (totalCvCount === 1) {
          insights.push({
            id: insightId++,
            title: 'Diversify Your Approach',
            description: 'Create multiple CV versions with different templates to target various roles effectively.',
            type: 'tip',
            icon: 'fas fa-copy'
          })
        } else if (totalCvCount >= 3) {
          insights.push({
            id: insightId++,
            title: 'Great Portfolio! 💼',
            description: `You have ${totalCvCount} CVs ready. Focus on optimizing content and tracking performance.`,
            type: 'success',
            icon: 'fas fa-briefcase'
          })
        }

        // Download insights
        if (avgDownloads > 2) {
          insights.push({
            id: insightId++,
            title: 'High Demand! 🔥',
            description: `Excellent! Your CVs are being downloaded frequently. Keep updating your content regularly.`,
            type: 'success',
            icon: 'fas fa-download'
          })
        } else if (totalCvCount > 0 && avgDownloads < 1) {
          insights.push({
            id: insightId++,
            title: 'Improve Download Rate',
            description: 'Add more compelling content and professional achievements to encourage downloads.',
            type: 'suggestion',
            icon: 'fas fa-arrow-up'
          })
        }

        // General tips
        insights.push({
          id: insightId++,
          title: 'Pro Tip 💡',
          description: 'Update your CV regularly with new skills and achievements to stay competitive.',
          type: 'tip',
          icon: 'fas fa-lightbulb'
        })

        aiInsights.value = insights.slice(0, 4)
      }

      const fetchCvs = async () => {
        try {
          const token = localStorage.getItem('auth_token')
          if (!token) {
            router.push('/login')
            return
          }
          
          const response = await axios.get('/api/my-cvs', {
            headers: { Authorization: `Bearer ${token}` },
          })
          
          if (response.data.success && response.data.cvs) {
            cvs_all.value = response.data.cvs
            totalViews.value = cvs_all.value.reduce((sum, cv) => sum + (cv.views || 0), 0)
            totalDownloads.value = cvs_all.value.reduce((sum, cv) => sum + (cv.downloads || 0), 0)
            generateAIInsights()
          } else {
            cvs_all.value = []
          }
        } catch (error) {
          console.error('Error fetching CVs:', error)
          if (error.response?.status === 401) {
            localStorage.removeItem('auth_token')
            router.push('/login')
            return
          }
          cvs_all.value = []
        } finally {
          loading.value = false
        }
      }

      const totalCvs = computed(() => cvs_all.value.length)
      const draftCvs = computed(() => cvs_all.value.filter(cv => !cv.isFinalized).length)

      const filteredAndSortedCvs = computed(() => {
        let tempCvs = [...cvs_all.value]

        if (searchTerm.value) {
          const lowerSearchTerm = searchTerm.value.toLowerCase()
          tempCvs = tempCvs.filter(
            (cv) =>
              (cv.title && cv.title.toLowerCase().includes(lowerSearchTerm)) ||
              (cv.personalDetails &&
                cv.personalDetails.fullName &&
                cv.personalDetails.fullName.toLowerCase().includes(lowerSearchTerm)) ||
              (cv.summary && cv.summary.toLowerCase().includes(lowerSearchTerm))
          )
        }

        if (filterTemplate.value !== 'all') {
          tempCvs = tempCvs.filter((cv) => cv.selectedTemplate === filterTemplate.value)
        }

        tempCvs.sort((a, b) => {
          switch (sortBy.value) {
            case 'title_asc':
              return (a.title || '').localeCompare(b.title || '')
            case 'title_desc':
              return (b.title || '').localeCompare(a.title || '')
            case 'updated_at_asc':
              return new Date(a.updatedAt) - new Date(b.updatedAt)
            case 'updated_at_desc':
              return new Date(b.updatedAt) - new Date(a.updatedAt)
            default:
              return 0
          }
        })

        return tempCvs
      })

      const confirmDelete = async (id) => {
        const confirmed = await showModal({
          title: 'Delete CV',
          message: 'Are you sure you want to delete this CV? This action cannot be undone.',
          confirmButtonText: 'Delete',
          cancelButtonText: 'Cancel',
          confirmButtonClass: 'btn-danger',
        })

        if (confirmed) {
          cvToDelete.value = id
          await deleteCv()
        } else {
          cvToDelete.value = null
        }
      }

      const deleteCv = async () => {
        if (!cvToDelete.value) return
        try {
          const token = localStorage.getItem('auth_token')
          await axios.delete(`/api/cvs/${cvToDelete.value}`, {
            headers: { Authorization: `Bearer ${token}` },
          })
          cvs_all.value = cvs_all.value.filter((cv) => cv.id !== cvToDelete.value)
          await showModal({
            title: 'Success!',
            message: 'CV deleted successfully.',
            confirmButtonText: 'OK',
            confirmButtonClass: 'btn-primary',
            cancelButtonText: '',
          })
        } catch (error) {
          console.error('Error deleting CV:', error)
          await showModal({
            title: 'Error!',
            message: 'Error deleting CV. Please try again.',
            confirmButtonText: 'OK',
            confirmButtonClass: 'btn-danger',
            cancelButtonText: '',
          })
        } finally {
          cvToDelete.value = null
        }
      }

      const downloadCv = (id, title) => {
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
        } catch (error) {
          console.error('Error downloading CV:', error)
          await showModal({
            title: 'Download Error!',
            message: 'Error downloading CV. Please try again.',
            confirmButtonText: 'OK',
            confirmButtonClass: 'btn-danger',
            cancelButtonText: '',
          })
        }
      }

      const previewCv = (id) => {
        router.push({ name: 'cv.preview', params: { id: id } })
      }

      const editCv = (id) => {
        router.push({ name: 'edit-cv', params: { id: id } })
      }

      onMounted(async () => {
        const token = localStorage.getItem('auth_token')
        if (!token) {
          router.push('/login')
          return
        }
        await fetchCvs()
      })

      return {
        cvs: filteredAndSortedCvs,
        loading,
        totalCvs,
        draftCvs,
        totalViews,
        totalDownloads,
        confirmDelete,
        deleteCv,
        downloadCv,
        previewCv,
        editCv,
        searchTerm,
        filterTemplate,
        sortBy,
        showDownloadModal,
        currentCvToDownload,
        handleDownloadConfirm,
        showAIPanel,
        recentActivities,
        successMetrics,
        aiInsights,
        formatTimeAgo,
        // Analytics data - Restored!
        performanceChartOptions,
        performanceChartSeries,
        templateChartOptions,
        templateChartSeries,
      }
    },
  }
</script>

<style scoped>
/* Page Wrapper - Same as Contact */
.dashboard-wrapper {
  padding-top: var(--space-xxl);
  padding-bottom: var(--space-xxl);
}

.section-spacing {
  padding: var(--space-section-vertical) var(--space-lg);
}

.text-center {
  text-align: center;
}

.mb-xl {
  margin-bottom: var(--space-xl);
}

.mb-lg {
  margin-bottom: var(--space-lg);
}

/* Section Title and Description - Same as Contact */
.section-title {
  font-family: var(--font-heading);
  font-size: var(--font-size-4xl);
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: var(--space-md);
  line-height: 1.2;
  letter-spacing: -0.02em;
}

.section-description {
  font-size: var(--font-size-lg);
  color: var(--text-secondary);
  line-height: var(--line-height-normal);
  max-width: 800px;
  margin: 0 auto var(--space-xl);
}

/* Loading State */
.loading-spinner {
  font-size: var(--font-size-4xl);
  color: var(--primary);
  margin-bottom: var(--space-md);
}

.loading-text {
  color: var(--text-secondary);
  font-size: var(--font-size-lg);
}

/* Stats Section - Clean Text Display */
.stats-section {
  margin: var(--space-xxl) 0;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: var(--space-xl);
  max-width: 600px;
  margin: 0 auto;
  text-align: center;
}

.stat-item {
  padding: var(--space-lg) 0;
}

.stat-number {
  font-size: var(--font-size-5xl);
  font-weight: 200;
  color: var(--primary);
  line-height: 1;
  margin-bottom: var(--space-sm);
}

.stat-label {
  font-size: var(--font-size-sm);
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 500;
}

/* Actions Section */
.actions-section {
  margin: var(--space-xxl) 0;
}

.actions-section .btn {
  margin: 0 var(--space-sm) var(--space-md);
}

/* Advanced Analytics - Clean but Feature-Rich */
.analytics-container {
  max-width: 1200px;
  margin: 0 auto;
}

.chart-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
  gap: var(--space-xl);
  margin-bottom: var(--space-xxl);
}

.chart-item {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: var(--space-lg);
  box-shadow: var(--shadow-sm);
  transition: all 0.3s ease;
}

.chart-item:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.chart-title {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  font-size: var(--font-size-lg);
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: var(--space-lg);
}

.chart-title i {
  color: var(--primary);
}

.chart-wrapper {
  margin: -var(--space-sm);
}

/* Enhanced AI Insights */
.insights-container {
  margin-bottom: var(--space-xxl);
}

.insights-title {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-sm);
  font-size: var(--font-size-xl);
  font-weight: 600;
  color: var(--text-primary);
  text-align: center;
  margin-bottom: var(--space-lg);
}

.insights-title i {
  color: var(--primary);
}

.insights-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: var(--space-lg);
}

.insight-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: var(--space-lg);
  box-shadow: var(--shadow-sm);
  transition: all 0.3s ease;
  border-left: 4px solid var(--primary);
}

.insight-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.insight-card.warning {
  border-left-color: #f59e0b;
}

.insight-card.suggestion {
  border-left-color: #10b981;
}

.insight-card.success {
  border-left-color: #22c55e;
}

.insight-card.tip {
  border-left-color: #3b82f6;
}

.insight-card .insight-icon {
  font-size: var(--font-size-2xl);
  color: var(--primary);
  margin-bottom: var(--space-md);
  display: block;
}

.insight-card .insight-title {
  font-size: var(--font-size-lg);
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: var(--space-sm);
}

.insight-card .insight-description {
  color: var(--text-secondary);
  line-height: var(--line-height-normal);
  margin: 0;
}

/* Enhanced Timeline */
.timeline-container {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: var(--space-xl);
  box-shadow: var(--shadow-sm);
}

.timeline-title {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-sm);
  font-size: var(--font-size-xl);
  font-weight: 600;
  color: var(--text-primary);
  text-align: center;
  margin-bottom: var(--space-xl);
}

.timeline-title i {
  color: var(--primary);
}

.timeline {
  position: relative;
  max-width: 700px;
  margin: 0 auto;
}

.timeline::before {
  content: '';
  position: absolute;
  left: 20px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: var(--border);
}

.timeline-item {
  position: relative;
  display: flex;
  align-items: flex-start;
  gap: var(--space-lg);
  margin-bottom: var(--space-xl);
  padding-left: var(--space-sm);
}

.timeline-item:last-child {
  margin-bottom: 0;
}

.timeline-marker {
  position: relative;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  background: var(--bg-card);
  border: 3px solid var(--primary);
  color: var(--primary);
  z-index: 1;
}

.timeline-marker.created {
  border-color: var(--success);
  color: var(--success);
}

.timeline-marker.download {
  border-color: var(--info);
  color: var(--info);
}

.timeline-marker.update {
  border-color: var(--warning);
  color: var(--warning);
}

.timeline-marker.view {
  border-color: var(--primary);
  color: var(--primary);
}

.timeline-content {
  flex: 1;
  background: var(--bg-secondary);
  border-radius: var(--radius);
  padding: var(--space-md);
  border: 1px solid var(--border);
}

.timeline-time {
  font-size: var(--font-size-xs);
  color: var(--text-muted);
  margin-bottom: var(--space-xs);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.timeline-title-text {
  font-size: var(--font-size-base);
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: var(--space-xs);
}

.timeline-description {
  font-size: var(--font-size-sm);
  color: var(--text-secondary);
  line-height: var(--line-height-normal);
  margin: 0;
}

/* Controls Section */
.controls-section {
  background-color: var(--bg-secondary);
  border-radius: var(--radius-lg);
}

.controls-row {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: var(--space-lg);
  flex-wrap: wrap;
  max-width: 800px;
  margin: 0 auto;
}

.search-group {
  flex: 1;
  min-width: 300px;
}

/* CV Grid */
.cv-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: var(--space-xl);
  margin-top: var(--space-xl);
}

/* Empty State */
.empty-state {
  padding: var(--space-xxl) var(--space-lg);
  max-width: 600px;
  margin: 0 auto;
}

.empty-icon {
  font-size: var(--font-size-6xl);
  margin-bottom: var(--space-lg);
  opacity: 0.5;
}

.empty-title {
  font-size: var(--font-size-2xl);
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: var(--space-md);
}

.empty-description {
  color: var(--text-secondary);
  line-height: var(--line-height-normal);
  margin-bottom: var(--space-xl);
}

/* Metrics Section */
.metrics-section {
  background-color: var(--bg-muted);
  border-radius: var(--radius-lg);
}

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: var(--space-xl);
  max-width: 800px;
  margin: 0 auto;
}

.metric-item {
  text-align: center;
  padding: var(--space-lg);
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
}

.metric-header {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-sm);
  margin-bottom: var(--space-md);
}

.metric-icon {
  font-size: var(--font-size-xl);
  color: var(--primary);
}

.metric-title {
  font-size: var(--font-size-base);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
}

.metric-value {
  font-size: var(--font-size-4xl);
  font-weight: 200;
  color: var(--primary);
  line-height: 1;
  margin-bottom: var(--space-md);
}

.metric-bar {
  height: 8px;
  background-color: var(--bg-tertiary);
  border-radius: var(--radius-full);
  overflow: hidden;
  margin-bottom: var(--space-sm);
}

.metric-progress {
  height: 100%;
  background: linear-gradient(90deg, var(--primary), var(--primary-dark));
  border-radius: var(--radius-full);
  transition: width 1s ease;
}

.metric-description {
  color: var(--text-secondary);
  font-size: var(--font-size-sm);
  margin: 0;
}

/* Responsive Design - Same patterns as Contact */
@media (max-width: 768px) {
  .section-spacing {
    padding: var(--space-xl) var(--space-md);
  }

  .section-title {
    font-size: var(--font-size-3xl);
  }

  .section-description {
    font-size: var(--font-size-base);
    margin-bottom: var(--space-lg);
  }

  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-lg);
  }

  .controls-row {
    flex-direction: column;
    align-items: stretch;
  }

  .search-group {
    min-width: unset;
  }

  .cv-grid {
    grid-template-columns: 1fr;
    gap: var(--space-lg);
  }

  .metrics-grid {
    grid-template-columns: 1fr;
    gap: var(--space-lg);
  }

  .actions-section .btn {
    width: 100%;
    max-width: 300px;
    margin: var(--space-sm) 0;
  }

     .chart-grid {
     grid-template-columns: 1fr;
   }

   .insights-grid {
     grid-template-columns: 1fr;
   }

   .timeline::before {
     left: 15px;
   }

   .timeline-item {
     gap: var(--space-md);
   }

   .timeline-marker {
     width: 30px;
     height: 30px;
   }
}

/* Dark Theme Overrides */
body.dark-theme .controls-section,
body.dark-theme .metrics-section {
  background-color: var(--bg-muted);
}

body.dark-theme .chart-item,
body.dark-theme .insight-card,
body.dark-theme .timeline-container,
body.dark-theme .timeline-content,
body.dark-theme .metric-item {
  background-color: var(--bg-card);
  border-color: var(--border);
}

body.dark-theme .timeline::before {
  background: var(--border);
}

body.dark-theme .timeline-marker {
  background: var(--bg-card);
}
</style>

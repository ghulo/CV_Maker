<template>
  <div class="preview-page">
    <!-- Minimalist Hero Section -->
    <section class="preview-hero">
      <div class="hero-content">
        <div v-if="loading" class="loading-state">
          <div class="loading-animation">
            <div class="pulse-dot"></div>
            <div class="pulse-dot"></div>
            <div class="pulse-dot"></div>
          </div>
          <h1 class="loading-title">Loading your CV...</h1>
          <p class="loading-subtitle">Preparing the perfect presentation</p>
        </div>
        
        <div v-else-if="error" class="error-state">
          <div class="error-icon">⚠️</div>
          <h1 class="error-title">{{ error }}</h1>
          <p class="error-subtitle">Let's get you back on track</p>
          <button @click="goBack" class="elegant-btn primary">
            <i class="fas fa-arrow-left"></i>
            Go Back
          </button>
        </div>
        
        <div v-else-if="cv" class="preview-ready">
          <div class="preview-header">
            <div class="cv-meta">
              <span class="cv-badge">{{ templateName }}</span>
              <span class="cv-status">Ready to Download</span>
            </div>
            <h1 class="cv-title">{{ cv.title || 'Your Professional CV' }}</h1>
            <p class="cv-owner">{{ cv.personalDetails?.fullName || 'Your Name' }}</p>
          </div>
          
          <div class="preview-actions">
            <button @click="downloadCv" class="elegant-btn primary">
              <i class="fas fa-download"></i>
              Download PDF
            </button>
            <button @click="printCv" class="elegant-btn secondary">
              <i class="fas fa-print"></i>
              Print
            </button>
            <button @click="shareCv" class="elegant-btn outline">
              <i class="fas fa-share-alt"></i>
              Share
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- CV Display Section -->
    <section v-if="cv" class="cv-display">
      <div class="cv-container">
        <div class="cv-paper">
          <div class="cv-shadow"></div>
          <div class="cv-content">
            <component :is="currentTemplateComponent" :cv="cv"></component>
          </div>
        </div>
        
        <!-- Floating Info Panel -->
        <div class="info-panel">
          <div class="info-item">
            <div class="info-icon">📊</div>
            <div class="info-text">
              <span class="info-label">Completion</span>
              <span class="info-value">{{ completionScore }}%</span>
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon">👁️</div>
            <div class="info-text">
              <span class="info-label">Views</span>
              <span class="info-value">{{ cv.views || 0 }}</span>
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon">⬇️</div>
            <div class="info-text">
              <span class="info-label">Downloads</span>
              <span class="info-value">{{ cv.downloads || 0 }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Elegant Actions Bar -->
    <section v-if="cv" class="actions-bar">
      <div class="actions-content">
        <button @click="goBack" class="elegant-btn text">
          <i class="fas fa-arrow-left"></i>
          Back to Dashboard
        </button>
        
        <div class="action-group">
          <button @click="editCv" class="elegant-btn outline">
            <i class="fas fa-edit"></i>
            Edit CV
          </button>
          <button @click="duplicateCv" class="elegant-btn outline">
            <i class="fas fa-copy"></i>
            Duplicate
          </button>
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

<script setup>
  import { ref, onMounted, computed } from 'vue'
  import { useRoute, useRouter } from 'vue-router'
  import axios from 'axios'
  import ClassicTemplate from '@/components/cv_templates/ClassicTemplate.vue'
  import ModernTemplate from '@/components/cv_templates/ModernTemplate.vue'
  import ProfessionalTemplate from '@/components/cv_templates/ProfessionalTemplate.vue'
  import CreativeTemplate from '@/components/cv_templates/CreativeTemplate.vue'
  import DownloadOptionsModal from '@/components/common/DownloadOptionsModal.vue'

  const route = useRoute()
  const router = useRouter()
  const cvId = route.params.id

  const cv = ref(null)
  const loading = ref(true)
  const error = ref(null)
  const showDownloadModal = ref(false)
  const currentCvToDownload = ref(null)

  const fetchCv = async () => {
    try {
      const token = localStorage.getItem('auth_token')
      const response = await axios.get(`/api/cvs/${cvId}`, {
        headers: { Authorization: `Bearer ${token}` },
      })
      if (response.data.success) {
        cv.value = response.data.cv
      } else {
        error.value = response.data.message || 'CV not found'
      }
    } catch (err) {
      if (err.response?.status === 403) {
        error.value = 'You don\'t have permission to view this CV'
      } else if (err.response?.status === 404) {
        error.value = 'CV not found'
      } else {
        error.value = 'Error loading CV. Please try again.'
      }
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  const currentTemplateComponent = computed(() => {
    if (!cv.value) return null
    switch (cv.value.selectedTemplate) {
      case 'classic': return ClassicTemplate
      case 'modern': return ModernTemplate
      case 'professional': return ProfessionalTemplate
      case 'creative': return CreativeTemplate
      default: return ClassicTemplate
    }
  })

  const templateName = computed(() => {
    if (!cv.value) return ''
    switch (cv.value.selectedTemplate) {
      case 'classic': return 'Classic'
      case 'modern': return 'Modern'
      case 'professional': return 'Professional'
      case 'creative': return 'Creative'
      default: return 'Classic'
    }
  })

  const completionScore = computed(() => {
    if (!cv.value) return 0
    
    let score = 0
    const weights = {
      personalDetails: 25,
      summary: 20,
      experience: 30,
      education: 15,
      skills: 10
    }
    
    // Personal details
    const personal = cv.value.personalDetails || {}
    if (personal.firstName && personal.email) score += weights.personalDetails
    
    // Summary
    if (cv.value.summary && cv.value.summary.length >= 50) score += weights.summary
    
    // Experience
    if (cv.value.experience && cv.value.experience.length > 0) score += weights.experience
    
    // Education
    if (cv.value.education && cv.value.education.length > 0) score += weights.education
    
    // Skills
    if (cv.value.skills && cv.value.skills.length >= 3) score += weights.skills
    
    return score
  })

  const goBack = () => {
    router.push('/dashboard')
  }

  const editCv = () => {
    router.push(`/edit-cv/${cvId}`)
  }

  const downloadCv = () => {
    currentCvToDownload.value = { id: cvId, title: cv.value.title }
    showDownloadModal.value = true
  }

  const printCv = () => {
    window.print()
  }

  const shareCv = () => {
    if (navigator.share) {
      navigator.share({
        title: cv.value.title,
        text: `Check out my professional CV: ${cv.value.title}`,
        url: window.location.href
      })
    } else {
      navigator.clipboard.writeText(window.location.href).then(() => {
        alert('Link copied to clipboard!')
      })
    }
  }

  const duplicateCv = async () => {
    try {
      const token = localStorage.getItem('auth_token')
      const response = await axios.post(`/api/cvs/${cvId}/duplicate`, {}, {
        headers: { Authorization: `Bearer ${token}` }
      })
      if (response.data.success) {
        router.push('/dashboard')
      }
    } catch (error) {
      console.error('Error duplicating CV:', error)
      alert('Error duplicating CV. Please try again.')
    }
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
      console.error('Error downloading CV:', error)
      alert('Error downloading CV. Please try again.')
    }
  }

  onMounted(() => {
    fetchCv()
  })
</script>

<style scoped>
.preview-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  position: relative;
  overflow-x: hidden;
}

/* Hero Section - Clean like contact page */
.preview-hero {
  padding: 6rem 2rem 4rem;
  text-align: center;
  position: relative;
}

.hero-content {
  max-width: 800px;
  margin: 0 auto;
}

/* Loading State */
.loading-animation {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-bottom: 2rem;
}

.pulse-dot {
  width: 12px;
  height: 12px;
  background: white;
  border-radius: 50%;
  animation: pulse 1.5s infinite ease-in-out;
}

.pulse-dot:nth-child(2) { animation-delay: -1.1s; }
.pulse-dot:nth-child(3) { animation-delay: -0.9s; }

@keyframes pulse {
  0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; }
  40% { transform: scale(1); opacity: 1; }
}

.loading-title, .error-title {
  font-size: 3rem;
  font-weight: 300;
  color: white;
  margin: 0 0 1rem 0;
  letter-spacing: -0.02em;
}

.loading-subtitle, .error-subtitle {
  font-size: 1.25rem;
  color: rgba(255, 255, 255, 0.8);
  margin: 0;
  font-weight: 300;
}

/* Error State */
.error-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

/* Preview Ready State */
.cv-meta {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
}

.cv-badge {
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 25px;
  font-size: 0.875rem;
  font-weight: 500;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.cv-status {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.875rem;
}

.cv-title {
  font-size: 3.5rem;
  font-weight: 200;
  color: white;
  margin: 0 0 0.5rem 0;
  letter-spacing: -0.03em;
  line-height: 1.1;
}

.cv-owner {
  font-size: 1.5rem;
  color: rgba(255, 255, 255, 0.9);
  margin: 0 0 3rem 0;
  font-weight: 300;
}

.preview-actions {
  display: flex;
  justify-content: center;
  gap: 1rem;
  flex-wrap: wrap;
}

/* Elegant Buttons */
.elegant-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  border: none;
  border-radius: 50px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  backdrop-filter: blur(10px);
  position: relative;
  overflow: hidden;
}

.elegant-btn.primary {
  background: rgba(255, 255, 255, 0.15);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.elegant-btn.primary:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.elegant-btn.secondary {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.elegant-btn.outline {
  background: transparent;
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.5);
}

.elegant-btn.text {
  background: transparent;
  color: rgba(255, 255, 255, 0.8);
  border: none;
  padding: 0.75rem 1.5rem;
}

/* CV Display Section */
.cv-display {
  padding: 4rem 2rem;
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(20px);
  position: relative;
}

.cv-container {
  max-width: 900px;
  margin: 0 auto;
  position: relative;
}

.cv-paper {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  position: relative;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
  transform: perspective(1000px) rotateX(2deg);
  transition: transform 0.3s ease;
}

.cv-paper:hover {
  transform: perspective(1000px) rotateX(0deg);
}

.cv-shadow {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(145deg, transparent, rgba(0, 0, 0, 0.1));
  pointer-events: none;
  z-index: 1;
}

.cv-content {
  position: relative;
  z-index: 2;
  aspect-ratio: 1 / 1.414;
  overflow: hidden;
}

/* Floating Info Panel */
.info-panel {
  position: absolute;
  top: 2rem;
  right: 2rem;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 1.5rem;
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.info-item:last-child {
  margin-bottom: 0;
}

.info-icon {
  font-size: 1.5rem;
}

.info-text {
  display: flex;
  flex-direction: column;
}

.info-label {
  font-size: 0.75rem;
  opacity: 0.8;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.info-value {
  font-size: 1.25rem;
  font-weight: 600;
}

/* Actions Bar */
.actions-bar {
  padding: 3rem 2rem;
  background: rgba(255, 255, 255, 0.03);
  backdrop-filter: blur(10px);
}

.actions-content {
  max-width: 900px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.action-group {
  display: flex;
  gap: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .preview-hero {
    padding: 4rem 1rem 3rem;
  }
  
  .cv-title {
    font-size: 2.5rem;
  }
  
  .preview-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .elegant-btn {
    width: 100%;
    max-width: 300px;
    justify-content: center;
  }
  
  .cv-display {
    padding: 3rem 1rem;
  }
  
  .info-panel {
    position: relative;
    top: auto;
    right: auto;
    margin-top: 2rem;
  }
  
  .actions-content {
    flex-direction: column;
    text-align: center;
  }
  
  .action-group {
    width: 100%;
    justify-content: center;
  }
}

/* Print Styles */
@media print {
  .preview-hero,
  .actions-bar,
  .info-panel {
    display: none;
  }
  
  .cv-display {
    background: white;
    padding: 0;
  }
  
  .cv-paper {
    box-shadow: none;
    transform: none;
    border-radius: 0;
  }
  
  .cv-shadow {
    display: none;
  }
}
</style>

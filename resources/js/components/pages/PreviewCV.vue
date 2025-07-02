<template>
  <div class="preview-page">
    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Loading your CV...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <div class="error-message">
        <h2>{{ error }}</h2>
        <button @click="goBack" class="back-btn">← Back to Dashboard</button>
      </div>
    </div>

    <!-- CV Preview -->
    <div v-else-if="cv" class="cv-preview-page">
      <!-- Simple Header -->
      <div class="preview-header" :style="headerStyle">
        <div class="header-content">
          <button @click="goBack" class="back-button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m12 19-7-7 7-7"/>
              <path d="M19 12H5"/>
            </svg>
            Back
          </button>
          <div class="cv-info">
            <h1>{{ cv.title || 'CV Preview' }}</h1>
            <span class="template-name">{{ templateName }} Template</span>
          </div>
          <div class="header-actions">
            <button @click="editCv" class="action-btn secondary">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
              </svg>
              Edit
            </button>
            <button @click="downloadCv" class="action-btn primary">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7,10 12,15 17,10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
              </svg>
              Download PDF
            </button>
          </div>
        </div>
      </div>

      <!-- CV Display -->
      <div class="cv-display">
        <div class="cv-container">
          <div class="cv-paper">
            
            <!-- Classic Template -->
            <div v-if="cv.selectedTemplate === 'classic'" class="cv-content classic-template">
              <div class="cv-header">
                <h2 class="cv-name">{{ fullName }}</h2>
                <p class="cv-title">{{ jobTitle }}</p>
                <div class="cv-contact">
                  <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>{{ cv.personalDetails?.email || 'email@example.com' }}</span>
                  </div>
                  <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <span>{{ cv.personalDetails?.phone || '+1 (555) 123-4567' }}</span>
                  </div>
                  <div v-if="cv.personalDetails?.address" class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ cv.personalDetails.address }}</span>
                  </div>
                </div>
              </div>

              <div class="cv-sections">
                <div v-if="cv.summary" class="cv-section">
                  <h3>Professional Summary</h3>
                  <p>{{ cv.summary }}</p>
                </div>

                <div v-if="cv.experience && cv.experience.length" class="cv-section">
                  <h3>Experience</h3>
                  <div v-for="exp in cv.experience" :key="exp.id || exp.position" class="experience-item">
                    <div class="exp-header">
                      <h4>{{ exp.position }}</h4>
                      <span class="exp-date">{{ formatDateRange(exp.start_date, exp.end_date, exp.current) }}</span>
                    </div>
                    <p class="exp-company">{{ exp.company }}</p>
                    <p v-if="exp.description" class="exp-description">{{ exp.description }}</p>
                  </div>
                </div>

                <div v-if="cv.education && cv.education.length" class="cv-section">
                  <h3>Education</h3>
                  <div v-for="edu in cv.education" :key="edu.id || edu.degree" class="education-item">
                    <div class="edu-header">
                      <h4>{{ edu.degree }}</h4>
                      <span class="edu-date">{{ formatDateRange(edu.start_date, edu.end_date) }}</span>
                    </div>
                    <p class="edu-institution">{{ edu.institution }}</p>
                    <p v-if="edu.gpa" class="edu-gpa">GPA: {{ edu.gpa }}</p>
                  </div>
                </div>

                <div v-if="cv.skills && cv.skills.length" class="cv-section">
                  <h3>Skills</h3>
                  <div class="skills-grid">
                    <span v-for="skill in cv.skills" :key="skill.id || skill.name" class="skill-tag">
                      {{ skill.name }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modern Template -->
            <div v-else-if="cv.selectedTemplate === 'modern'" class="cv-content modern-template">
              <div class="modern-layout">
                <div class="modern-sidebar">
                  <div class="profile-section">
                    <div class="profile-circle"></div>
                    <h2 class="cv-name">{{ fullName }}</h2>
                    <p class="cv-title">{{ jobTitle }}</p>
                  </div>

                  <div class="sidebar-section">
                    <h4>Contact</h4>
                    <div class="contact-list">
                      <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>{{ cv.personalDetails?.email || 'email@example.com' }}</span>
                      </div>
                      <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>{{ cv.personalDetails?.phone || '+1 (555) 123-4567' }}</span>
                      </div>
                      <div v-if="cv.personalDetails?.address" class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ cv.personalDetails.address }}</span>
                      </div>
                    </div>
                  </div>

                  <div v-if="cv.skills && cv.skills.length" class="sidebar-section">
                    <h4>Skills</h4>
                    <div class="skill-bars">
                      <div v-for="skill in cv.skills.slice(0, 6)" :key="skill.id || skill.name" class="skill-bar">
                        <span class="skill-name">{{ skill.name }}</span>
                        <div class="bar">
                          <div class="progress" :style="{ '--bar-width': `${skill.level * 20}%` }"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="modern-main">
                  <div v-if="cv.summary" class="cv-section">
                    <h3>Professional Summary</h3>
                    <p>{{ cv.summary }}</p>
                  </div>

                  <div v-if="cv.experience && cv.experience.length" class="cv-section">
                    <h3>Experience</h3>
                    <div v-for="exp in cv.experience" :key="exp.id || exp.position" class="experience-item">
                      <div class="exp-header">
                        <h4>{{ exp.position }}</h4>
                        <span class="exp-date">{{ formatDateRange(exp.start_date, exp.end_date, exp.current) }}</span>
                      </div>
                      <p class="exp-company">{{ exp.company }}</p>
                      <p v-if="exp.description" class="exp-description">{{ exp.description }}</p>
                    </div>
                  </div>

                  <div v-if="cv.education && cv.education.length" class="cv-section">
                    <h3>Education</h3>
                    <div v-for="edu in cv.education" :key="edu.id || edu.degree" class="education-item">
                      <div class="edu-header">
                        <h4>{{ edu.degree }}</h4>
                        <span class="edu-date">{{ formatDateRange(edu.start_date, edu.end_date) }}</span>
                      </div>
                      <p class="edu-institution">{{ edu.institution }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Professional Template -->
            <div v-else-if="cv.selectedTemplate === 'professional'" class="cv-content professional-template">
              <div class="professional-header">
                <h2 class="cv-name">{{ fullName }}</h2>
                <p class="cv-title">{{ jobTitle }}</p>
                <div class="professional-divider"></div>
                <div class="contact-row">
                  <span>{{ cv.personalDetails?.email || 'email@example.com' }}</span>
                  <span>{{ cv.personalDetails?.phone || '+1 (555) 123-4567' }}</span>
                  <span v-if="cv.personalDetails?.address">{{ cv.personalDetails.address }}</span>
                </div>
              </div>

              <div class="professional-content">
                <div class="content-left">
                  <div v-if="cv.summary" class="cv-section">
                    <h3>Professional Summary</h3>
                    <p>{{ cv.summary }}</p>
                  </div>

                  <div v-if="cv.experience && cv.experience.length" class="cv-section">
                    <h3>Experience</h3>
                    <div v-for="exp in cv.experience" :key="exp.id || exp.position" class="experience-item">
                      <div class="exp-header">
                        <h4>{{ exp.position }}</h4>
                        <span class="exp-date">{{ formatDateRange(exp.start_date, exp.end_date, exp.current) }}</span>
                      </div>
                      <p class="exp-company">{{ exp.company }}</p>
                      <p v-if="exp.description" class="exp-description">{{ exp.description }}</p>
                    </div>
                  </div>
                </div>

                <div class="content-right">
                  <div v-if="cv.education && cv.education.length" class="cv-section">
                    <h3>Education</h3>
                    <div v-for="edu in cv.education" :key="edu.id || edu.degree" class="education-item">
                      <h4>{{ edu.degree }}</h4>
                      <p class="edu-institution">{{ edu.institution }}</p>
                      <p class="edu-date">{{ formatDateRange(edu.start_date, edu.end_date) }}</p>
                    </div>
                  </div>

                  <div v-if="cv.skills && cv.skills.length" class="cv-section">
                    <h3>Skills</h3>
                    <div class="skills-list">
                      <div v-for="skill in cv.skills" :key="skill.id || skill.name" class="skill-item">
                        {{ skill.name }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Creative Template -->
            <div v-else-if="cv.selectedTemplate === 'creative'" class="cv-content creative-template">
              <div class="creative-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
              </div>
              
              <div class="creative-header">
                <h2 class="cv-name">{{ fullName }}</h2>
                <p class="cv-title">{{ jobTitle }}</p>
              </div>

              <div class="creative-layout">
                <div class="creative-main">
                  <div v-if="cv.summary" class="cv-section">
                    <h3>About Me</h3>
                    <p>{{ cv.summary }}</p>
                  </div>

                  <div v-if="cv.experience && cv.experience.length" class="cv-section">
                    <h3>Experience</h3>
                    <div v-for="exp in cv.experience" :key="exp.id || exp.position" class="experience-item">
                      <div class="exp-header">
                        <h4>{{ exp.position }}</h4>
                        <span class="exp-date">{{ formatDateRange(exp.start_date, exp.end_date, exp.current) }}</span>
                      </div>
                      <p class="exp-company">{{ exp.company }}</p>
                      <p v-if="exp.description" class="exp-description">{{ exp.description }}</p>
                    </div>
                  </div>
                </div>

                <div class="creative-sidebar">
                  <div class="contact-section">
                    <h4>Contact</h4>
                    <div class="contact-list">
                      <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>{{ cv.personalDetails?.email || 'email@example.com' }}</span>
                      </div>
                      <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>{{ cv.personalDetails?.phone || '+1 (555) 123-4567' }}</span>
                      </div>
                    </div>
                  </div>

                  <div v-if="cv.education && cv.education.length" class="cv-section">
                    <h4>Education</h4>
                    <div v-for="edu in cv.education" :key="edu.id || edu.degree" class="education-item">
                      <h5>{{ edu.degree }}</h5>
                      <p>{{ edu.institution }}</p>
                    </div>
                  </div>

                  <div v-if="cv.skills && cv.skills.length" class="cv-section">
                    <h4>Skills</h4>
                    <div class="creative-skills">
                      <span v-for="skill in cv.skills" :key="skill.id || skill.name" class="creative-skill-tag">
                        {{ skill.name }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

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
  // Template components no longer needed - using inline mini templates
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
      if (!token) {
        error.value = 'Please login to view your CV'
        loading.value = false
        setTimeout(() => router.push('/login'), 2000)
        return
      }
      
      console.log('🔍 Enhanced CV fetching with ID:', cvId)
      console.log('🔍 Available CV sources:', {
        routeParam: cvId,
        savedCvId: localStorage.getItem('current_cv_id'),
        lastSavedId: localStorage.getItem('last_saved_cv_id'),
        completedRecently: localStorage.getItem('last_cv_completed')
      })
      
      // 🚀 ENHANCED CV ID VALIDATION
      let finalCvId = cvId
      
      // If no CV ID in route, try localStorage sources
      if (!finalCvId || finalCvId === 'undefined' || finalCvId === 'null') {
        console.log('⚠️ Invalid or missing CV ID in route, checking localStorage...')
        
        finalCvId = localStorage.getItem('current_cv_id') || 
                   localStorage.getItem('last_saved_cv_id')
        
        if (finalCvId) {
          console.log('✅ Using localStorage CV ID:', finalCvId)
          // Update the route to reflect the correct ID
          router.replace(`/cv/${finalCvId}/preview`)
        } else {
          console.error('❌ No valid CV ID found anywhere')
          error.value = 'CV not found. Please return to your dashboard.'
          loading.value = false
          setTimeout(() => router.push('/dashboard'), 3000)
          return
        }
      }
      
      // Validate that CV ID is a valid number/string
      const cvIdNumber = parseInt(finalCvId)
      if (isNaN(cvIdNumber) || cvIdNumber <= 0) {
        console.error('❌ Invalid CV ID format:', finalCvId)
        error.value = 'Invalid CV identifier. Please check the link.'
        loading.value = false
        return
      }
      
      console.log('🚀 Fetching CV with validated ID:', finalCvId)
      
      // 🔥 ENHANCED FETCH WITH RETRY LOGIC
      let fetchAttempts = 0
      const maxAttempts = 3
      let response = null
      
      while (fetchAttempts < maxAttempts && !response) {
        try {
          fetchAttempts++
          console.log(`📡 Fetch attempt ${fetchAttempts}/${maxAttempts} for CV ID: ${finalCvId}`)
          
          response = await axios.get(`/api/cvs/${finalCvId}`, {
            headers: { Authorization: `Bearer ${token}` },
            timeout: 20000 // Increased timeout
          })
          
          console.log(`✅ Fetch attempt ${fetchAttempts} successful`)
          break
          
        } catch (fetchError) {
          console.warn(`⚠️ Fetch attempt ${fetchAttempts} failed:`, fetchError.message)
          
          if (fetchAttempts === maxAttempts) {
            throw fetchError // Re-throw on final attempt
          }
          
          // Wait before retry (exponential backoff)
          await new Promise(resolve => setTimeout(resolve, 1000 * fetchAttempts))
        }
      }
      
      console.log('📋 Enhanced CV Fetch Response:', {
        success: response.data.success,
        hasCv: !!response.data.cv,
        cvKeys: response.data.cv ? Object.keys(response.data.cv) : null,
        dataStructure: Object.keys(response.data)
      })
      
      if (response.data.success && response.data.cv) {
        // Handle different possible data structures from API
        const rawCv = response.data.cv
        
        // 🔥 ENHANCED DATA NORMALIZATION
        cv.value = {
          ...rawCv,
          // Ensure consistent field names
          title: rawCv.title || rawCv.cv_title || 'Professional CV',
          selectedTemplate: rawCv.selectedTemplate || rawCv.template || 'classic',
          
          // Map personal details to expected format with enhanced handling
          personalDetails: rawCv.personalDetails || rawCv.personal_details || {
            fullName: `${rawCv.personal_details?.firstName || rawCv.personalDetails?.firstName || ''} ${rawCv.personal_details?.lastName || rawCv.personalDetails?.lastName || ''}`.trim(),
            firstName: rawCv.personal_details?.firstName || rawCv.personalDetails?.firstName || '',
            lastName: rawCv.personal_details?.lastName || rawCv.personalDetails?.lastName || '',
            email: rawCv.personal_details?.email || rawCv.personalDetails?.email || '',
            phone: rawCv.personal_details?.phone || rawCv.personalDetails?.phone || '',
            address: rawCv.personal_details?.address || rawCv.personalDetails?.address || '',
            dateOfBirth: rawCv.personal_details?.dateOfBirth || rawCv.personalDetails?.dateOfBirth || '',
            nationality: rawCv.personal_details?.nationality || rawCv.personalDetails?.nationality || ''
          },
          
          // Ensure arrays exist with enhanced mapping
          experience: (rawCv.experience || rawCv.work_experiences || []).map(exp => ({
            position: exp.position || exp.job_title || exp.title || '',
            company: exp.company || exp.employer || '',
            start_date: exp.start_date || exp.startDate || '',
            end_date: exp.end_date || exp.endDate || '',
            description: exp.description || '',
            current: exp.current || exp.isCurrent || false
          })),
          
          education: (rawCv.education || rawCv.educations || []).map(edu => ({
            degree: edu.degree || edu.qualification || '',
            institution: edu.institution || edu.university || edu.school || '',
            start_date: edu.start_date || edu.startDate || '',
            end_date: edu.end_date || edu.endDate || '',
            gpa: edu.gpa || ''
          })),
          
          skills: (rawCv.skills || []).map(skill => ({
            name: skill.name || skill,
            level: skill.level || 3
          })),
          
          interests: (rawCv.interests || []).map(interest => ({
            name: interest.name || interest
          })),
          
          // Other fields
          summary: rawCv.summary || rawCv.professional_summary || '',
          views: rawCv.views || 0,
          downloads: rawCv.downloads || 0
        }
        
        console.log('✅ CV loaded and enhanced successfully:', {
          title: cv.value.title,
          template: cv.value.selectedTemplate,
          personalName: cv.value.personalDetails?.fullName,
          experienceCount: cv.value.experience?.length,
          skillsCount: cv.value.skills?.length,
          hasViews: cv.value.views > 0
        })
        
        // 🎯 STORE SUCCESSFUL CV ID for future reference
        localStorage.setItem('last_viewed_cv_id', finalCvId)
        localStorage.setItem(`cv_${finalCvId}_last_viewed`, new Date().toISOString())
        
        // Smooth fade in after load
        requestAnimationFrame(() => {
          document.querySelector('.cv-paper')?.classList.add('loaded')
        })
      } else {
        error.value = response.data.message || 'CV not found'
        console.error('❌ CV fetch failed:', response.data)
      }
    } catch (err) {
      console.error('❌ Error fetching CV:', err)
      
      // 🔥 ENHANCED ERROR HANDLING
      if (err.response?.status === 403) {
        error.value = 'You don\'t have permission to view this CV'
      } else if (err.response?.status === 404) {
        error.value = 'CV not found. It may have been deleted or the link is incorrect.'
        
        // Try to redirect to a valid CV if we have one
        const fallbackId = localStorage.getItem('last_saved_cv_id')
        if (fallbackId && fallbackId !== cvId) {
          console.log('🔄 Attempting redirect to fallback CV:', fallbackId)
          setTimeout(() => {
            router.replace(`/cv/${fallbackId}/preview`)
          }, 2000)
        }
      } else if (err.code === 'ECONNABORTED') {
        error.value = 'Connection timeout. Please check your internet connection and try again.'
      } else if (err.response?.status >= 500) {
        error.value = 'Server error. Please try again in a few moments.'
      } else {
        error.value = 'Error loading CV. Please try again or return to your dashboard.'
      }
    } finally {
      loading.value = false
    }
  }

  // Computed properties for CV data
  const fullName = computed(() => {
    if (!cv.value) return 'Your Name'
    const pd = cv.value.personalDetails || {}
    return pd.fullName || `${pd.firstName || ''} ${pd.lastName || ''}`.trim() || 'Your Name'
  })

  const jobTitle = computed(() => {
    if (!cv.value) return 'Your Title'
    return cv.value.experience?.[0]?.position || 
           cv.value.experience?.[0]?.title || 
           'Your Title'
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

  // Dynamic header styling based on template
  const headerStyle = computed(() => {
    if (!cv.value) return { background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' }
    
    switch (cv.value.selectedTemplate) {
      case 'classic':
        return { background: 'linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%)' }
      case 'modern':
        return { background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' }
      case 'professional':
        return { background: 'linear-gradient(135deg, #374151 0%, #111827 100%)' }
      case 'creative':
        return { background: 'linear-gradient(135deg, #F59E0B 0%, #DC2626 100%)' }
      default:
        return { background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' }
    }
  })

  // Helper function to format date ranges
  const formatDateRange = (startDate, endDate, isCurrent = false) => {
    if (!startDate) return ''
    
    const formatDate = (dateStr) => {
      if (!dateStr) return ''
      const date = new Date(dateStr)
      return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' })
    }
    
    const start = formatDate(startDate)
    const end = isCurrent ? 'Present' : formatDate(endDate)
    
    return `${start} - ${end}`
  }

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

  const downloadCv = async () => {
    try {
      const token = localStorage.getItem('auth_token')
      if (!token) {
        alert('Please login to download your CV')
        return
      }

      // Direct download without modal for now
      console.log('📁 Starting CV download for ID:', cvId)
      
      const response = await axios.get(`/api/cvs/${cvId}/download`, {
        headers: { 
          Authorization: `Bearer ${token}`,
          'Accept': 'application/pdf'
        },
        responseType: 'blob',
        timeout: 30000
      })
      
      console.log('✅ Download response received')
      
      // Create download link
      const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
      const link = document.createElement('a')
      link.href = url
      
      // Generate filename
      const safeTitle = (cv.value?.title || 'CV').replace(/[^a-z0-9]/gi, '_').toLowerCase()
      const fileName = `${safeTitle}_${templateName.value.toLowerCase()}.pdf`
      
      link.setAttribute('download', fileName)
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      window.URL.revokeObjectURL(url)
      
      console.log('✅ CV downloaded successfully:', fileName)
      
    } catch (error) {
      console.error('❌ Download error:', error)
      
      if (error.response?.status === 404) {
        alert('CV not found. Please try refreshing the page.')
      } else if (error.response?.status === 403) {
        alert('You don\'t have permission to download this CV.')
      } else if (error.code === 'ECONNABORTED') {
        alert('Download timed out. Please check your connection and try again.')
      } else {
        alert('Failed to download CV. Please try again or contact support.')
      }
    }
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
/* Clean CV Preview Styles */
:root {
  --primary: #3B82F6;
  --primary-dark: #1D4ED8;
  --gray-50: #F9FAFB;
  --gray-100: #F3F4F6;
  --gray-200: #E5E7EB;
  --gray-300: #D1D5DB;
  --gray-600: #4B5563;
  --gray-700: #374151;
  --gray-800: #1F2937;
  --gray-900: #111827;
  --green-500: #10B981;
  --red-500: #EF4444;
}

.preview-page {
  min-height: 100vh;
  background: var(--gray-50);
  color: var(--gray-900);
}

/* Loading States */
.loading-container, .error-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  flex-direction: column;
  gap: 1rem;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--gray-200);
  border-top: 3px solid var(--primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.back-btn {
  background: var(--primary);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  margin-top: 1rem;
}

/* Preview Header */
.preview-header {
  padding: 1.5rem 0;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.back-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
  font-weight: 500;
  cursor: pointer;
  padding: 0.75rem 1rem;
  border-radius: 10px;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.back-button:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.back-button svg {
  width: 18px;
  height: 18px;
}

.cv-info {
  flex: 1;
  text-align: center;
  margin: 0 2rem;
}

.cv-info h1 {
  margin: 0 0 0.25rem 0;
  font-size: 1.75rem;
  font-weight: 700;
  color: white;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.template-name {
  color: rgba(255, 255, 255, 0.9);
  font-size: 0.875rem;
  font-weight: 500;
  background: rgba(255, 255, 255, 0.1);
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  display: inline-block;
  backdrop-filter: blur(10px);
}

.header-actions {
  display: flex;
  gap: 0.75rem;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.25rem;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.1);
  color: white;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  font-size: 0.875rem;
}

.action-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.action-btn.primary {
  background: linear-gradient(135deg, #10B981 0%, #059669 100%);
  border-color: #10B981;
  color: white;
}

.action-btn.primary:hover {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  border-color: #059669;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
}

.action-btn svg {
  width: 16px;
  height: 16px;
}

/* Responsive Header */
@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 1rem;
    padding: 0 1rem;
  }
  
  .cv-info {
    margin: 0;
    text-align: center;
  }
  
  .cv-info h1 {
    font-size: 1.5rem;
  }
  
  .header-actions {
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.5rem;
  }
  
  .action-btn {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
  }
}

/* CV Display */
.cv-display {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.cv-container {
  display: flex;
  justify-content: center;
}

.cv-paper {
  width: 100%;
  max-width: 900px;
  min-height: 1000px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.cv-content {
  padding: 3rem;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  line-height: 1.6;
  color: var(--gray-800);
}

/* Common CV Elements */
.cv-section {
  margin-bottom: 2rem;
}

.cv-section h3 {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 1rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 2px solid var(--primary);
  padding-bottom: 0.5rem;
}

.experience-item, .education-item {
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--gray-200);
}

.experience-item:last-child, .education-item:last-child {
  border-bottom: none;
}

.exp-header, .edu-header {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 0.5rem;
}

.exp-header h4, .edu-header h4 {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--gray-900);
  margin: 0;
}

.exp-date, .edu-date {
  font-size: 0.875rem;
  color: var(--gray-600);
  font-weight: 500;
}

.exp-company, .edu-institution {
  font-size: 1rem;
  color: var(--gray-700);
  font-weight: 500;
  margin: 0.25rem 0;
}

.exp-description {
  color: var(--gray-700);
  margin: 0.5rem 0 0 0;
}

/* Classic Template */
.classic-template .cv-header {
  text-align: center;
  margin-bottom: 2.5rem;
  padding-bottom: 2rem;
  border-bottom: 3px solid var(--primary);
}

.classic-template .cv-name {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: 0.5rem;
}

.classic-template .cv-title {
  font-size: 1.25rem;
  color: var(--gray-600);
  margin-bottom: 1.5rem;
  font-weight: 500;
}

.classic-template .cv-contact {
  display: flex;
  justify-content: center;
  gap: 2rem;
  flex-wrap: wrap;
}

.classic-template .contact-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--gray-700);
  font-size: 0.9rem;
}

.classic-template .contact-item i {
  color: var(--primary);
  width: 16px;
}

.skills-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.skill-tag {
  background: var(--primary);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
}

/* Modern Template */
.modern-template .modern-layout {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 0;
  min-height: 800px;
}

.modern-template .modern-sidebar {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 2rem;
}

.modern-template .profile-section {
  text-align: center;
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.modern-template .profile-circle {
  width: 80px;
  height: 80px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  margin: 0 auto 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: bold;
}

.modern-template .cv-name {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.modern-template .cv-title {
  font-size: 1rem;
  opacity: 0.9;
  margin-bottom: 1rem;
}

.modern-template .sidebar-section {
  margin-bottom: 2rem;
}

.modern-template .sidebar-section h4 {
  font-size: 1rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 1rem;
  color: rgba(255, 255, 255, 0.9);
}

.modern-template .contact-list {
  space-y: 0.75rem;
}

.modern-template .contact-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
  font-size: 0.875rem;
}

.modern-template .contact-item i {
  width: 16px;
  color: rgba(255, 255, 255, 0.8);
}

.modern-template .skill-bars {
  space-y: 1rem;
}

.modern-template .skill-bar {
  margin-bottom: 1rem;
}

.modern-template .skill-name {
  display: block;
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
  color: rgba(255, 255, 255, 0.9);
}

.modern-template .bar {
  height: 6px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 3px;
  overflow: hidden;
}

.modern-template .progress {
  width: var(--bar-width, 100%);
  background: white;
  border-radius: 3px;
  transition: width 0.3s ease;
}

.modern-template .modern-main {
  padding: 2rem;
  background: white;
}

/* Professional Template */
.professional-template .professional-header {
  text-align: center;
  margin-bottom: 3rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid var(--gray-300);
}

.professional-template .cv-name {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: 0.5rem;
}

.professional-template .cv-title {
  font-size: 1.25rem;
  color: var(--gray-600);
  margin-bottom: 1rem;
  font-weight: 500;
}

.professional-template .professional-divider {
  width: 60px;
  height: 3px;
  background: var(--primary);
  margin: 1rem auto;
}

.professional-template .contact-row {
  display: flex;
  justify-content: center;
  gap: 2rem;
  flex-wrap: wrap;
  font-size: 0.9rem;
  color: var(--gray-700);
}

.professional-template .professional-content {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 3rem;
}

.professional-template .skills-list {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.5rem;
}

.professional-template .skill-item {
  padding: 0.5rem;
  background: var(--gray-100);
  border-radius: 6px;
  font-size: 0.875rem;
  color: var(--gray-700);
  text-align: center;
}

/* Creative Template */
.creative-template {
  position: relative;
  background: linear-gradient(135deg, #f8f9ff 0%, #fff5f8 100%);
}

.creative-template .creative-shapes {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
  overflow: hidden;
}

.creative-template .shape {
  position: absolute;
  opacity: 0.1;
}

.creative-template .shape-1 {
  width: 120px;
  height: 120px;
  background: #667eea;
  border-radius: 50%;
  top: 10%;
  right: 10%;
}

.creative-template .shape-2 {
  width: 80px;
  height: 80px;
  background: #f093fb;
  transform: rotate(45deg);
  bottom: 15%;
  left: 8%;
}

.creative-template .creative-header {
  text-align: center;
  margin-bottom: 2.5rem;
  position: relative;
  z-index: 1;
}

.creative-template .cv-name {
  font-size: 2.5rem;
  font-weight: 700;
  background: linear-gradient(135deg, #667eea, #764ba2);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 0.5rem;
}

.creative-template .cv-title {
  font-size: 1.25rem;
  color: #f093fb;
  font-weight: 600;
  margin-bottom: 1rem;
}

.creative-template .creative-layout {
  display: grid;
  grid-template-columns: 1fr 280px;
  gap: 3rem;
  position: relative;
  z-index: 1;
}

.creative-template .contact-section h4,
.creative-template .cv-section h4 {
  font-size: 1rem;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 1rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.creative-template .creative-skills {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.creative-template .creative-skill-tag {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .cv-display {
    padding: 1rem;
  }
  
  .cv-content {
    padding: 2rem;
  }
  
  .modern-template .modern-layout,
  .professional-template .professional-content,
  .creative-template .creative-layout {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .classic-template .cv-contact {
    flex-direction: column;
    gap: 1rem;
  }
  
  .professional-template .contact-row {
    flex-direction: column;
    gap: 1rem;
  }
  
  .exp-header, .edu-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.25rem;
  }
}

/* Print Styles */
@media print {
  .preview-header {
    display: none;
  }
  
  .cv-display {
    padding: 0;
    background: white;
  }
  
  .cv-paper {
    box-shadow: none;
    border-radius: 0;
    max-width: none;
  }
  
  .cv-content {
    padding: 1rem;
  }
}

.skill-bar .bar .progress {
  width: var(--bar-width, 100%);
}
</style>

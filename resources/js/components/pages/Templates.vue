0.<template>
  <div class="page-wrapper templates-wrapper">
    <!-- Animated Background -->
    <div class="animated-bg">
      <div class="gradient-sphere sphere-1"></div>
      <div class="gradient-sphere sphere-2"></div>
      <div class="gradient-sphere sphere-3"></div>
    </div>

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container">
        <div class="hero-content">
          <div class="hero-badge">
            <span>🎨</span>
            <span>{{ t('our_templates') }}</span>
          </div>
          <h1 class="hero-title">
            {{ t('selectTemplate') }}<br />
            <span class="accent-text">{{ t('templates') }}</span>
          </h1>
          <p class="hero-subtitle">
            {{ t('template_description') }}
          </p>
        </div>
      </div>
    </section>

    <!-- Templates Section -->
    <section class="templates-section">
      <div class="container">
        <!-- Messages -->
        <div v-if="errorMessage || infoMessage" class="message-container">
          <div v-if="errorMessage" class="message error">
            <i class="fas fa-exclamation-triangle"></i>
            {{ errorMessage }}
          </div>
          <div v-if="infoMessage" class="message info">
            <i class="fas fa-info-circle"></i>
            {{ infoMessage }}
          </div>
        </div>

        <!-- Quick Start Card -->
        <div class="quick-start-card">
          <div class="card-glow"></div>
          <div class="start-content">
            <div class="start-icon">
              <i class="fas fa-rocket"></i>
            </div>
            <div class="start-info">
              <h2>{{ t('ready_to_start_career') }}</h2>
              <p>{{ t('home_subtitle') }}</p>
            </div>
            <button @click="startCreating" class="start-btn">
              <i class="fas fa-plus"></i>
              <span>{{ t('create_cv_now') }}</span>
              <div class="btn-shine"></div>
            </button>
          </div>
        </div>

        <!-- Templates Showcase -->
        <div class="templates-showcase">
          <div class="section-header">
            <h3 class="showcase-title">{{ t('choose_template') }}</h3>
            <p class="showcase-subtitle">{{ t('template_description') }}</p>
            
            <!-- Template Controls -->
            <div class="showcase-controls">
              <div class="view-toggle">
                <button 
                  @click="viewMode = 'grid'" 
                  :class="['toggle-btn', { active: viewMode === 'grid' }]"
                >
                  <i class="fas fa-th"></i>
                  Grid View
                </button>
                <button 
                  @click="viewMode = 'comparison'" 
                  :class="['toggle-btn', { active: viewMode === 'comparison' }]"
                >
                  <i class="fas fa-columns"></i>
                  Compare
                </button>
              </div>
              
              <div class="demo-controls" v-if="viewMode === 'grid'">
                <button 
                  @click="toggleAnimation" 
                  :class="['demo-btn', { active: animationsEnabled }]"
                >
                  <i class="fas fa-play"></i>
                  Live Demo
                </button>
              </div>
            </div>
          </div>
          
          <!-- Grid View -->
          <div v-if="viewMode === 'grid'" class="templates-grid">
            <div
              v-for="(template, index) in templates"
              :key="template.id"
              class="template-card"
              @click="selectTemplate(template.id)"
              @mouseenter="startTemplatePreview(template.id)"
              @mouseleave="stopTemplatePreview(template.id)"
              :style="{ '--delay': `${index * 0.1}s` }"
            >
              <div class="template-preview">
                <div class="preview-badge" v-if="template.id === 'modern'">
                  <i class="fas fa-crown"></i>
                  Popular
                </div>
                
                <div class="hover-hint" v-if="animationsEnabled">
                  <i class="fas fa-play"></i>
                  <span>Hover to preview</span>
                </div>
                
                <!-- Interactive CV Preview -->
                <div class="cv-preview-container" :class="[`template-${template.id}`, { 'animating': previewingTemplate === template.id && animationsEnabled }]">
                  
                  <!-- Classic Template -->
                  <div v-if="template.id === 'classic'" class="preview-classic">
                    <div class="cv-header">
                      <div class="preview-name" :class="{ 'typing': previewingTemplate === template.id }">
                        {{ previewData.name }}
                      </div>
                      <div class="preview-title" :class="{ 'typing': previewingTemplate === template.id }">
                        {{ previewData.title }}
                      </div>
                      <div class="cv-contact">
                        <span class="contact-item">
                          <i class="fas fa-envelope"></i>
                          {{ previewData.email }}
                        </span>
                        <span class="contact-item">
                          <i class="fas fa-phone"></i>
                          +1 (555) 123-4567
                        </span>
                      </div>
                    </div>
                    
                    <div class="cv-sections">
                      <div class="cv-section" :class="{ 'filling': previewingTemplate === template.id }">
                        <h3>Experience</h3>
                        <div class="section-content">
                          <div class="cv-line main-line" :style="{ '--delay': '0.3s' }"></div>
                          <div class="cv-line short-line" :style="{ '--delay': '0.5s' }"></div>
                          <div class="cv-line medium-line" :style="{ '--delay': '0.7s' }"></div>
                        </div>
                      </div>
                      
                      <div class="cv-section" :class="{ 'filling': previewingTemplate === template.id }">
                        <h3>Skills</h3>
                        <div class="skills-container">
                          <span v-for="skill in previewData.skills.slice(0, 4)" :key="skill" class="skill-tag">
                            {{ skill }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Modern Template -->
                  <div v-if="template.id === 'modern'" class="preview-modern">
                    <div class="modern-layout">
                      <div class="modern-sidebar">
                        <div class="profile-section">
                          <div class="profile-circle" :class="{ 'appearing': previewingTemplate === template.id }"></div>
                          <div class="preview-name" :class="{ 'typing': previewingTemplate === template.id }">
                            {{ previewData.name }}
                          </div>
                          <div class="preview-title" :class="{ 'typing': previewingTemplate === template.id }">
                            {{ previewData.title }}
                          </div>
                        </div>
                        
                        <div class="sidebar-section">
                          <h4>Skills</h4>
                          <div class="skill-bars">
                            <div class="skill-bar" v-for="(skill, index) in previewData.skills.slice(0, 4)" :key="skill" :style="{ '--bar-width': `${85 - index * 5}%` }">
                              <span class="skill-name">{{ skill }}</span>
                              <div class="bar">
                                <div class="progress" 
                                     :class="{ 'filling': previewingTemplate === template.id }"
                                     :style="{ '--bar-width': 'var(--bar-width, 100%)' }"></div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="modern-main">
                        <div class="cv-section" :class="{ 'filling': previewingTemplate === template.id }">
                          <h3>Experience</h3>
                          <div class="cv-line main-line" :style="{ '--delay': '0.8s' }"></div>
                          <div class="cv-line short-line" :style="{ '--delay': '1.0s' }"></div>
                          <div class="cv-line medium-line" :style="{ '--delay': '1.2s' }"></div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Professional Template -->
                  <div v-if="template.id === 'professional'" class="preview-professional">
                    <div class="cv-header centered">
                      <div class="preview-name" :class="{ 'typing': previewingTemplate === template.id }">
                        {{ previewData.name }}
                      </div>
                      <div class="preview-title" :class="{ 'typing': previewingTemplate === template.id }">
                        {{ previewData.title }}
                      </div>
                      <div class="professional-divider" :class="{ 'expanding': previewingTemplate === template.id }"></div>
                    </div>
                    
                    <div class="cv-content two-column">
                      <div class="cv-section" :class="{ 'filling': previewingTemplate === template.id }">
                        <div class="content-lines">
                          <div class="cv-line main-line" :style="{ '--delay': '0.7s' }"></div>
                          <div class="cv-line short-line" :style="{ '--delay': '0.9s' }"></div>
                        </div>
                      </div>
                      
                      <div class="cv-section" :class="{ 'filling': previewingTemplate === template.id }">
                        <div class="content-lines">
                          <div class="cv-line main-line" :style="{ '--delay': '1.1s' }"></div>
                          <div class="cv-line short-line" :style="{ '--delay': '1.3s' }"></div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Creative Template -->
                  <div v-if="template.id === 'creative'" class="preview-creative">
                    <div class="creative-shapes" :class="{ 'floating': previewingTemplate === template.id }">
                      <div class="shape shape-1"></div>
                      <div class="shape shape-2"></div>
                    </div>
                    
                    <div class="cv-content creative-layout">
                      <div class="preview-name creative-name" :class="{ 'typing': previewingTemplate === template.id }">
                        {{ previewData.name }}
                      </div>
                      <div class="preview-title creative-title" :class="{ 'typing': previewingTemplate === template.id }">
                        {{ previewData.title }}
                      </div>
                      
                      <div class="creative-grid">
                        <div class="cv-section" :class="{ 'filling': previewingTemplate === template.id }">
                          <div class="content-lines">
                            <div class="cv-line main-line" :style="{ '--delay': '0.5s' }"></div>
                            <div class="cv-line short-line" :style="{ '--delay': '0.7s' }"></div>
                          </div>
                        </div>
                        
                        <div class="creative-visual" :class="{ 'animating': previewingTemplate === template.id }">
                          <div class="visual-element" :style="{ '--delay': '0.9s' }"></div>
                          <div class="visual-element small" :style="{ '--delay': '1.1s' }"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Template Info -->
              <div class="template-info">
                <div class="template-header">
                  <h4 class="template-name">{{ template.name }}</h4>
                  <div class="template-badge" :class="`badge-${template.id}`">
                    <i :class="{
                      'fas fa-star': template.id === 'classic',
                      'fas fa-lightning-bolt': template.id === 'modern',
                      'fas fa-briefcase': template.id === 'professional',
                      'fas fa-palette': template.id === 'creative'
                    }"></i>
                  </div>
                </div>
                <p class="template-description">{{ template.description }}</p>
                <div class="template-features">
                  <span class="feature-tag">ATS-Friendly</span>
                  <span class="feature-tag" v-if="template.id === 'modern'">Premium</span>
                  <span class="feature-tag" v-if="template.id === 'creative'">Unique</span>
                </div>
              </div>

              <!-- Minimal Select Button -->
              <div class="template-action">
                <button class="action-btn" @click.stop="selectTemplate(template.id)">
                  <i class="fas fa-rocket"></i>
                  <span>{{ t('startWithTemplate') }}</span>
                </button>
              </div>
            </div>
          </div>
          
          <!-- Comparison View -->
          <div v-if="viewMode === 'comparison'" class="comparison-view">
            <div class="comparison-header">
              <h4>Template Comparison</h4>
              <p>Compare features and styles side by side</p>
            </div>
            
            <div class="comparison-grid">
              <div
                v-for="template in templates"
                :key="`compare-${template.id}`"
                class="comparison-card"
                @click="selectTemplate(template.id)"
              >
                <div class="comparison-preview">
                  <div class="preview-badge" v-if="template.id === 'modern'">
                    <i class="fas fa-crown"></i>
                    Popular
                  </div>
                  
                  <!-- Simplified CV Preview for Comparison -->
                  <div class="comparison-cv" :class="`template-${template.id}`">
                    <div v-if="template.id === 'classic'" class="classic-mini">
                      <div class="mini-header">
                        <div class="mini-name">{{ previewData.name }}</div>
                        <div class="mini-title">{{ previewData.title }}</div>
                      </div>
                      <div class="mini-sections">
                        <div class="mini-section">
                          <div class="mini-section-title">EXPERIENCE</div>
                          <div class="mini-lines">
                            <div class="mini-line"></div>
                            <div class="mini-line short"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div v-if="template.id === 'modern'" class="modern-mini">
                      <div class="mini-layout">
                        <div class="mini-sidebar">
                          <div class="mini-avatar"></div>
                          <div class="mini-skills">
                            <div class="mini-skill-bar"></div>
                            <div class="mini-skill-bar"></div>
                          </div>
                        </div>
                        <div class="mini-content">
                          <div class="mini-name">{{ previewData.name }}</div>
                          <div class="mini-lines">
                            <div class="mini-line"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div v-if="template.id === 'professional'" class="professional-mini">
                      <div class="mini-header centered">
                        <div class="mini-name">{{ previewData.name }}</div>
                        <div class="mini-divider"></div>
                      </div>
                      <div class="mini-grid">
                        <div class="mini-section">
                          <div class="mini-line"></div>
                        </div>
                        <div class="mini-section">
                          <div class="mini-line"></div>
                        </div>
                      </div>
                    </div>
                    
                    <div v-if="template.id === 'creative'" class="creative-mini">
                      <div class="mini-shapes">
                        <div class="mini-shape"></div>
                      </div>
                      <div class="mini-name creative">{{ previewData.name }}</div>
                      <div class="mini-lines">
                        <div class="mini-line colored"></div>
                        <div class="mini-visual">
                          <div class="mini-element"></div>
                          <div class="mini-element small"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="comparison-info">
                  <h5 class="template-name">{{ template.name }}</h5>
                  <p class="template-description">{{ template.description }}</p>
                  
                  <div class="template-features">
                    <div class="feature-list">
                      <div class="feature-item">
                        <i class="fas fa-check"></i>
                        <span>ATS-Friendly</span>
                      </div>
                      <div class="feature-item" v-if="template.id === 'modern'">
                        <i class="fas fa-star"></i>
                        <span>Most Popular</span>
                      </div>
                      <div class="feature-item" v-if="template.id === 'creative'">
                        <i class="fas fa-palette"></i>
                        <span>Creative Design</span>
                      </div>
                      <div class="feature-item" v-if="template.id === 'professional'">
                        <i class="fas fa-briefcase"></i>
                        <span>Business Focused</span>
                      </div>
                      <div class="feature-item">
                        <i class="fas fa-download"></i>
                        <span>PDF Export</span>
                      </div>
                    </div>
                  </div>
                  
                  <button class="comparison-select-btn" @click="selectTemplate(template.id)">
                    <i class="fas fa-rocket"></i>
                    Use This Template
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Page Actions -->
        <div class="page-actions">
          <router-link
            v-if="isAuthenticated"
            to="/dashboard"
            class="btn-secondary"
          >
            <i class="fas fa-arrow-left"></i>
            <span>{{ t('back') }} {{ t('dashboard') }}</span>
          </router-link>
          <div v-else class="auth-prompt">
            <p>{{ t('already_have_account') }}</p>
            <router-link to="/login" class="auth-link">{{ t('sign_in') }}</router-link>
            <span>{{ t('registerToCreate') }}</span>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'

export default {
  name: 'Templates',
  setup() {
    const router = useRouter()
    const { t } = useI18n()
    const errorMessage = ref('')
    const infoMessage = ref('')
    
    // New state for interactive features
    const viewMode = ref('grid')
    const animationsEnabled = ref(true)
    const previewingTemplate = ref(null)
    
    // Preview data for templates
    const previewData = ref({
      name: 'Sarah Johnson',
      title: 'Senior Developer',
      email: 'sarah.johnson@email.com',
      phone: '+1 (555) 123-4567',
      skills: ['JavaScript', 'React', 'Node.js', 'Python', 'TypeScript', 'AWS']
    })

    const isAuthenticated = computed(() => !!localStorage.getItem('auth_token'))

    const templates = computed(() => [
      {
        id: 'classic',
        name: t('classic'),
        description: t('elegant_professional'),
      },
      {
        id: 'modern',
        name: t('modern'),
        description: t('minimal_clean'),
      },
      {
        id: 'professional',
        name: t('professional'),
        description: t('serious_detailed'),
      },
      {
        id: 'creative',
        name: t('creative'),
        description: t('unique_modern'),
      },
    ])

    const selectTemplate = (templateId) => {
      if (!isAuthenticated.value) {
        infoMessage.value = t('registerToCreate')
        router.push({ name: 'login', query: { redirect: `/create-cv?template=${templateId}` } })
      } else {
        router.push({ path: '/create-cv', query: { template: templateId } })
      }
    }

    const startCreating = () => {
      if (!isAuthenticated.value) {
        infoMessage.value = t('registerToCreate')
        router.push({ name: 'login', query: { redirect: '/create-cv' } })
      } else {
        router.push('/create-cv')
      }
    }

    // Interactive preview methods
    const startTemplatePreview = (templateId) => {
      if (animationsEnabled.value) {
        previewingTemplate.value = templateId
      }
    }

    const stopTemplatePreview = (templateId) => {
      if (previewingTemplate.value === templateId) {
        previewingTemplate.value = null
      }
    }

    const toggleAnimation = () => {
      animationsEnabled.value = !animationsEnabled.value
      if (!animationsEnabled.value) {
        previewingTemplate.value = null
      }
    }

    return { 
      templates, 
      selectTemplate, 
      startCreating, 
      isAuthenticated, 
      errorMessage, 
      infoMessage, 
      t,
      // New reactive values and methods
      viewMode,
      animationsEnabled,
      previewingTemplate,
      previewData,
      startTemplatePreview,
      stopTemplatePreview,
      toggleAnimation
    }
  },
}
</script>

<style scoped>
/* Enhanced CSS Variables for Templates Page */
:root {
  --primary: #5B21B6;
  --primary-light: #7C3AED;
  --primary-dark: #4C1D95;
  --secondary: #0EA5E9;
  --secondary-light: #38BDF8;
  --accent: #F59E0B;
  --success: #10B981;
  --error: #EF4444;
  --warning: #F59E0B;
  --info: #3B82F6;
  
  /* Backgrounds */
  --bg-base: #FFFFFF;
  --bg-subtle: #FAFAFA;
  --bg-elevated: #FFFFFF;
  --bg-glass: rgba(255, 255, 255, 0.85);
  --bg-overlay: rgba(0, 0, 0, 0.6);
  
  /* Text Colors */
  --text-primary: #111827;
  --text-secondary: #6B7280;
  --text-muted: #9CA3AF;
  --text-inverse: #FFFFFF;
  
  /* Borders */
  --border: #E5E7EB;
  --border-light: #F3F4F6;
  --border-focus: #7C3AED;
  
  /* Shadows */
  --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  --shadow-colored: 0 10px 25px rgba(91, 33, 182, 0.15);
  
  /* Border Radius */
  --radius: 0.75rem;
  --radius-md: 0.875rem;
  --radius-lg: 1rem;
  --radius-xl: 1.25rem;
  --radius-2xl: 1.5rem;
  --radius-full: 9999px;
  
  /* Transitions */
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --transition-fast: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  --transition-bounce: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  
  /* Spacing */
  --space-xs: 0.25rem;
  --space-sm: 0.5rem;
  --space-md: 1rem;
  --space-lg: 1.5rem;
  --space-xl: 2rem;
  --space-2xl: 3rem;
  --space-3xl: 4rem;
  
  /* Animation Durations */
  --duration-fast: 150ms;
  --duration-normal: 300ms;
  --duration-slow: 500ms;
}

/* Dark Theme Variables */
body.dark-theme {
  --bg-base: #0F172A;
  --bg-subtle: #1E293B;
  --bg-elevated: #1E293B;
  --bg-glass: rgba(30, 41, 59, 0.85);
  --bg-overlay: rgba(0, 0, 0, 0.8);
  
  --text-primary: #F8FAFC;
  --text-secondary: #CBD5E1;
  --text-muted: #94A3B8;
  --text-inverse: #0F172A;
  
  --border: #334155;
  --border-light: #475569;
  --border-focus: #8B5CF6;
  
  --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
  --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
  --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
  --shadow-colored: 0 10px 25px rgba(139, 92, 246, 0.25);
}

.templates-wrapper {
  min-height: 100vh;
  background: var(--bg-base);
  position: relative;
  overflow: hidden;
}

/* Animated Background */
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
  filter: blur(120px);
  opacity: 0.7;
  animation: float 25s infinite ease-in-out;
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
  background: linear-gradient(135deg, var(--secondary) 0%, var(--accent) 100%);
  bottom: -175px;
  right: -175px;
  animation-delay: 8s;
}

.sphere-3 {
  width: 400px;
  height: 400px;
  background: linear-gradient(135deg, var(--accent) 0%, var(--success) 100%);
  top: 30%;
  right: -200px;
  animation-delay: 15s;
}

@keyframes float {
  0%, 100% { 
    transform: translate(0, 0) scale(1) rotate(0deg); 
    opacity: 0.7;
  }
  25% { 
    transform: translate(50px, -50px) scale(1.1) rotate(90deg); 
    opacity: 0.5;
  }
  50% { 
    transform: translate(-30px, 30px) scale(0.9) rotate(180deg); 
    opacity: 0.8;
  }
  75% { 
    transform: translate(30px, 20px) scale(1.05) rotate(270deg); 
    opacity: 0.6;
  }
}

/* Container */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
  position: relative;
  z-index: 2;
}

/* Hero Section */
.hero-section {
  padding: 60px 0 40px;
  text-align: center;
}

.hero-content {
  max-width: 700px;
  margin: 0 auto;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  padding: 0.5rem 1rem;
  border-radius: 100px;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-secondary);
  margin-bottom: 2rem;
  box-shadow: var(--shadow);
}

.hero-title {
  font-size: clamp(2.5rem, 6vw, 4rem);
  font-weight: 700;
  line-height: 1.1;
  margin-bottom: 1.5rem;
  color: var(--text-primary);
}

.accent-text {
  color: var(--primary);
  position: relative;
}

.hero-subtitle {
  font-size: 1.25rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 3rem;
}

/* Templates Section */
.templates-section {
  padding: 60px 0 120px;
}

/* Messages */
.message-container {
  margin-bottom: 2rem;
}

.message {
  padding: 1rem 1.5rem;
  border-radius: var(--radius-lg);
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
  box-shadow: var(--shadow);
}

.message.error {
  background: rgba(239, 68, 68, 0.1);
  color: #dc2626;
  border: 1px solid rgba(239, 68, 68, 0.3);
}

.message.info {
  background: rgba(59, 130, 246, 0.1);
  color: #2563eb;
  border: 1px solid rgba(59, 130, 246, 0.3);
}

/* Quick Start Card */
.quick-start-card {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
  border-radius: var(--radius-xl);
  padding: 3rem;
  margin-bottom: 4rem;
  color: white;
  position: relative;
  overflow: hidden;
  box-shadow: 0 20px 25px -5px rgba(91, 33, 182, 0.3);
}

.card-glow {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.2) 0%, transparent 50%);
  pointer-events: none;
}

.start-content {
  display: flex;
  align-items: center;
  gap: 2rem;
  position: relative;
  z-index: 1;
}

.start-icon {
  width: 80px;
  height: 80px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: var(--radius-xl);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  flex-shrink: 0;
}

.start-info {
  flex: 1;
}

.start-info h2 {
  font-size: 2.25rem;
  font-weight: 700;
  margin: 0 0 1rem;
  line-height: 1.2;
}

.start-info p {
  font-size: 1.125rem;
  margin: 0;
  opacity: 0.9;
  line-height: 1.6;
}

.start-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  background: white;
  color: var(--primary);
  border: none;
  border-radius: var(--radius-lg);
  font-size: 1.125rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  position: relative;
  overflow: hidden;
  flex-shrink: 0;
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

.start-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.start-btn:hover .btn-shine {
  left: 100%;
}

/* Templates Showcase */
.templates-showcase {
  margin-bottom: 3rem;
}

.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.showcase-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.showcase-subtitle {
  font-size: 1.125rem;
  color: var(--text-secondary);
  margin: 0 0 2rem;
}

/* Showcase Controls */
.showcase-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: var(--bg-glass);
  backdrop-filter: blur(10px);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow);
}

.view-toggle {
  display: flex;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  overflow: hidden;
  box-shadow: var(--shadow);
}

.toggle-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: transparent;
  color: var(--text-secondary);
  border: none;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition);
  position: relative;
}

.toggle-btn:hover {
  background: var(--bg-subtle);
  color: var(--text-primary);
}

.toggle-btn.active {
  background: var(--primary);
  color: var(--text-inverse);
  box-shadow: 0 2px 8px rgba(91, 33, 182, 0.3);
}

.toggle-btn.active::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent);
  pointer-events: none;
}

.demo-controls {
  display: flex;
  gap: 1rem;
}

.demo-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: var(--bg-elevated);
  color: var(--text-primary);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition);
  box-shadow: var(--shadow);
}

.demo-btn:hover {
  border-color: var(--primary);
  color: var(--primary);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.demo-btn.active {
  background: linear-gradient(135deg, var(--success), #059669);
  color: var(--text-inverse);
  border-color: var(--success);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.demo-btn.active i {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}

/* Templates Grid */
.templates-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.template-card {
  background: var(--bg-elevated);
  border: 2px solid var(--border);
  border-radius: var(--radius-xl);
  overflow: hidden;
  cursor: pointer;
  transition: var(--transition);
  box-shadow: var(--shadow);
  position: relative;
  animation: fadeInUp 0.6s ease-out both;
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

.template-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--primary), var(--secondary));
  opacity: 0;
  transition: var(--transition);
}

.template-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-xl);
  border-color: var(--primary);
}

.template-card:hover::before {
  opacity: 1;
}

.template-card:hover .cv-preview-container {
  animation: templateHover 0.6s ease-out;
}

@keyframes templateHover {
  0% { 
    transform: scale(1); 
    filter: brightness(1);
  }
  50% { 
    transform: scale(1.02); 
    filter: brightness(1.05);
  }
  100% { 
    transform: scale(1); 
    filter: brightness(1);
  }
}

/* Enhanced Template Preview */
.template-preview {
  height: 350px;
  background: linear-gradient(135deg, var(--bg-subtle) 0%, var(--bg-elevated) 100%);
  border-bottom: 1px solid var(--border);
  padding: 1.5rem;
  position: relative;
  overflow: hidden;
}

.template-preview::after {
  content: '';
  position: absolute;
  bottom: 10px;
  right: 10px;
  width: 8px;
  height: 8px;
  background: var(--primary);
  border-radius: 50%;
  opacity: 0;
  animation: breathe 2s ease-in-out infinite;
}

.template-card:hover .template-preview::after {
  opacity: 0;
}

@keyframes breathe {
  0%, 100% { 
    opacity: 0.3; 
    transform: scale(1);
  }
  50% { 
    opacity: 0.8; 
    transform: scale(1.2);
  }
}

.preview-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: linear-gradient(135deg, var(--accent), #F97316);
  color: var(--text-inverse);
  padding: 0.25rem 0.75rem;
  border-radius: var(--radius-full);
  font-size: 0.75rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.25rem;
  z-index: 10;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.hover-hint {
  position: absolute;
  bottom: 1rem;
  left: 1rem;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  color: var(--text-secondary);
  padding: 0.5rem 0.75rem;
  border-radius: var(--radius-lg);
  font-size: 0.75rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.375rem;
  opacity: 0.8;
  transition: var(--transition);
  border: 1px solid rgba(255, 255, 255, 0.5);
  z-index: 5;
}

.template-card:hover .hover-hint {
  opacity: 0;
  transform: translateY(10px);
}

.hover-hint i {
  font-size: 0.625rem;
  color: var(--primary);
}

.cv-preview-container {
  height: 100%;
  position: relative;
  transition: var(--transition);
  z-index: 1;
  overflow: hidden;
}

.cv-preview-container.animating {
  animation: previewGlow 0.5s ease-in-out;
}

@keyframes previewGlow {
  0% { box-shadow: 0 0 0 rgba(91, 33, 182, 0); }
  50% { box-shadow: 0 0 20px rgba(91, 33, 182, 0.3); }
  100% { box-shadow: 0 0 0 rgba(91, 33, 182, 0); }
}

/* Enhanced Interactive CV Elements */
.cv-header {
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid var(--border-light);
}

.cv-header.centered {
  text-align: center;
}

.preview-name {
  font-size: 1rem;
  font-weight: 800;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
  letter-spacing: 0.02em;
  overflow: hidden;
  white-space: nowrap;
}

.preview-name.typing {
  animation: typewriter 1.5s steps(15) forwards;
  border-right: 2px solid var(--primary);
}

.preview-name.creative-name {
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.preview-title {
  font-size: 0.75rem;
  color: var(--text-secondary);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  overflow: hidden;
  white-space: nowrap;
}

.preview-title.typing {
  animation: typewriter 1.5s steps(20) forwards;
  animation-delay: 0.5s;
  border-right: 2px solid var(--secondary);
}

.preview-title.creative-title {
  color: var(--accent);
}

@keyframes typewriter {
  from { width: 0; }
  to { width: 100%; }
}

.contact-info {
  display: flex;
  gap: 1rem;
  margin-top: 0.75rem;
  font-size: 0.625rem;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  color: var(--text-muted);
}

.contact-item i {
  font-size: 0.5rem;
  color: var(--primary);
}

.cv-sections {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.cv-section {
  position: relative;
}

.section-title {
  font-size: 0.625rem;
  font-weight: 800;
  color: var(--primary);
  margin-bottom: 0.75rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  position: relative;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -0.25rem;
  left: 0;
  height: 2px;
  width: 0;
  background: var(--primary);
  transition: width 1.5s ease;
}

.cv-section.filling .section-title::after {
  width: 2rem;
}

.section-content {
  margin-top: 0.5rem;
}

.content-line {
  height: 4px;
  background: linear-gradient(90deg, var(--text-muted), var(--border));
  border-radius: 2px;
  margin-bottom: 0.5rem;
  opacity: 0;
  transform: scaleX(0);
  transform-origin: left;
  transition: all 0.6s ease;
}

.cv-section.filling .content-line {
  opacity: 0.8;
  transform: scaleX(1);
}

.content-line.main { width: 100%; }
.content-line.large { width: 85%; }
.content-line.medium { width: 65%; }
.content-line.small { width: 45%; }
.content-line.short { width: 75%; }

.skills-container {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.skill-tag {
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  color: var(--text-inverse);
  padding: 0.25rem 0.5rem;
  border-radius: var(--radius);
  font-size: 0.625rem;
  font-weight: 600;
  opacity: 0;
  transform: translateY(10px);
  transition: all 0.4s ease;
}

.cv-section.filling .skill-tag {
  opacity: 1;
  transform: translateY(0);
}

.skill-tag:nth-child(1) { transition-delay: 0.2s; }
.skill-tag:nth-child(2) { transition-delay: 0.4s; }
.skill-tag:nth-child(3) { transition-delay: 0.6s; }
.skill-tag:nth-child(4) { transition-delay: 1.5s; }

/* CV Contact Info */
.cv-contact {
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-top: 0.75rem;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  color: var(--text-muted);
}

.contact-item i {
  width: 12px;
  color: var(--primary);
  font-size: 10px;
}

/* CV Lines */
.cv-line {
  height: 6px;
  background: #e2e8f0;
  border-radius: 3px;
  margin-bottom: 6px;
  opacity: 0;
  transform: scaleX(0);
  transform-origin: left;
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.cv-section.filled .cv-line,
.cv-section.filling .cv-line {
  opacity: 1;
  transform: scaleX(1);
  background: linear-gradient(90deg, var(--primary), var(--primary-light));
}

.cv-line.main-line {
  width: 100%;
}

.cv-line.short-line {
  width: 65%;
}

.cv-line.medium-line {
  width: 80%;
}

/* Content Lines Container */
.content-lines {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

/* Enhanced Classic Template */
.preview-classic {
  height: 100%;
  width: 100%;
  display: flex;
  flex-direction: column;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  position: absolute;
  top: 0;
  left: 0;
}

/* Enhanced Modern Template */
.preview-modern {
  display: flex;
  gap: 1rem;
  height: 100%;
  width: 100%;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  position: absolute;
  top: 0;
  left: 0;
}

.modern-sidebar {
  flex: 1;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
  padding: 1rem;
  border-radius: var(--radius-lg);
  color: var(--text-inverse);
  position: relative;
  overflow: hidden;
}

.modern-sidebar::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.2) 0%, transparent 50%);
  pointer-events: none;
}

.profile-section {
  text-align: center;
  margin-bottom: 1rem;
  position: relative;
  z-index: 1;
}

.profile-avatar {
  width: 32px;
  height: 32px;
  background: rgba(255, 255, 255, 0.3);
  border: 2px solid rgba(255, 255, 255, 0.5);
  border-radius: 50%;
  margin: 0 auto 0.75rem;
  position: relative;
  transition: var(--transition);
}

.profile-avatar.glowing {
  box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
  animation: avatarGlow 2s ease-in-out infinite alternate;
}

@keyframes avatarGlow {
  from { box-shadow: 0 0 20px rgba(255, 255, 255, 0.3); }
  to { box-shadow: 0 0 30px rgba(255, 255, 255, 0.7); }
}

.sidebar-section {
  margin-top: 1rem;
  position: relative;
  z-index: 1;
}

.skill-bars {
  margin-top: 0.75rem;
}

.skill-bar {
  margin-bottom: 0.75rem;
}

.skill-name {
  font-size: 0.625rem;
  font-weight: 600;
  margin-bottom: 0.25rem;
  display: block;
  opacity: 0.9;
}

.bar-container {
  height: 4px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 2px;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background: linear-gradient(90deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
  border-radius: 2px;
  width: 0;
  transition: width 1s ease;
  position: relative;
}

.progress-bar.filling {
  animation: progressFill 1s ease forwards;
}

@keyframes progressFill {
  from { width: 0; }
}

.progress-bar::after {
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

.modern-main {
  flex: 2;
  padding: 0.5rem 0;
}

/* Profile Circle */
.profile-circle {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  margin: 0 auto 12px;
  transition: all 0.3s ease;
  opacity: 0;
}

.profile-circle.appearing {
  opacity: 1;
  animation: profileAppear 0.6s ease-out;
}

@keyframes profileAppear {
  0% { transform: scale(0) rotate(180deg); opacity: 0; }
  100% { transform: scale(1) rotate(0deg); opacity: 1; }
}

/* Modern Sidebar Styles */
.modern-sidebar h4 {
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 6px;
  opacity: 0.9;
  color: rgba(255, 255, 255, 0.9);
}

.skill-name {
  font-size: 10px;
  font-weight: 500;
  margin-bottom: 3px;
  display: block;
  color: rgba(255, 255, 255, 0.9);
}

.bar {
  height: 4px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 2px;
  overflow: hidden;
  margin-bottom: 6px;
}

.progress {
  height: 100%;
  background: white;
  border-radius: 2px;
  transition: width 1s ease-out 0.5s;
}

/* Two Column Layout */
.cv-content.two-column {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

/* Creative Layout */
.cv-content.creative-layout {
  position: relative;
  z-index: 2;
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
}

.visual-element.small {
  width: 16px;
}

/* Enhanced Professional Template */
.preview-professional {
  height: 100%;
  width: 100%;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  position: absolute;
  top: 0;
  left: 0;
}

.professional-divider {
  height: 3px;
  background: linear-gradient(90deg, var(--primary), var(--secondary));
  width: 0;
  margin: 1rem auto;
  border-radius: 2px;
  transition: width 1s ease;
  position: relative;
}

.professional-divider.expanding {
  width: 60px;
}

.professional-divider::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
  animation: dividerShimmer 2s infinite;
}

@keyframes dividerShimmer {
  0% { left: -100%; }
  100% { left: 100%; }
}

.professional-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  margin-top: 1.5rem;
  text-align: left;
}

/* Enhanced Creative Template */
.preview-creative {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  overflow: hidden;
}

.creative-bg-shapes {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
  z-index: 0;
}

.creative-bg-shapes.floating .shape {
  animation: floatCreative 4s ease-in-out infinite;
}

.shape {
  position: absolute;
  border-radius: var(--radius);
  opacity: 0.1;
  transition: var(--transition);
}

.shape-1 {
  top: 1rem;
  right: 1rem;
  width: 20px;
  height: 20px;
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  border-radius: 50%;
  animation-delay: 0s;
}

.shape-2 {
  bottom: 2rem;
  left: 1rem;
  width: 16px;
  height: 16px;
  background: var(--accent);
  border-radius: 30%;
  animation-delay: 1s;
}

.shape-3 {
  top: 50%;
  right: 2rem;
  width: 12px;
  height: 12px;
  background: linear-gradient(135deg, var(--success), var(--info));
  border-radius: 20%;
  animation-delay: 2s;
}

@keyframes floatCreative {
  0%, 100% { 
    transform: translateY(0) rotate(0deg); 
    opacity: 0.1; 
  }
  25% { 
    transform: translateY(-10px) rotate(90deg); 
    opacity: 0.2; 
  }
  50% { 
    transform: translateY(-5px) rotate(180deg); 
    opacity: 0.15; 
  }
  75% { 
    transform: translateY(-15px) rotate(270deg); 
    opacity: 0.25; 
  }
}

.creative-content {
  position: relative;
  z-index: 1;
  height: 100%;
}

.creative-layout {
  display: flex;
  justify-content: space-between;
  margin-top: 1rem;
  gap: 1rem;
}

.creative-visual {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  align-items: flex-end;
}

.creative-visual.animating .visual-element {
  animation: creativePop 0.6s ease backwards;
}

.visual-element {
  border-radius: var(--radius);
  opacity: 0;
  transform: scale(0);
  transition: all 0.4s ease;
}

.creative-visual.animating .visual-element {
  opacity: 1;
  transform: scale(1);
}

.visual-element:nth-child(1) {
  width: 24px;
  height: 24px;
  background: linear-gradient(135deg, var(--accent), var(--warning));
}

.visual-element.small {
  width: 16px;
  height: 16px;
  background: linear-gradient(135deg, var(--primary), var(--info));
}

.visual-element:nth-child(3) {
  width: 20px;
  height: 20px;
  background: linear-gradient(135deg, var(--success), var(--secondary));
}

@keyframes creativePop {
  0% {
    opacity: 0;
    transform: scale(0) rotate(-180deg);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.2) rotate(-90deg);
  }
  100% {
    opacity: 1;
    transform: scale(1) rotate(0deg);
  }
}

/* Comparison View */
.comparison-view {
  margin-top: 2rem;
}

.comparison-header {
  text-align: center;
  margin-bottom: 3rem;
}

.comparison-header h4 {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 0.5rem;
}

.comparison-header p {
  font-size: 1.125rem;
  color: var(--text-secondary);
  margin: 0;
}

.comparison-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
}

.comparison-card {
  background: var(--bg-elevated);
  border: 2px solid var(--border);
  border-radius: var(--radius-xl);
  overflow: hidden;
  cursor: pointer;
  transition: var(--transition);
  box-shadow: var(--shadow);
  position: relative;
}

.comparison-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--primary), var(--secondary));
  opacity: 0;
  transition: var(--transition);
}

.comparison-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-xl);
  border-color: var(--primary);
}

.comparison-card:hover::before {
  opacity: 1;
}

.comparison-preview {
  height: 200px;
  background: linear-gradient(135deg, var(--bg-subtle) 0%, var(--bg-elevated) 100%);
  padding: 1rem;
  position: relative;
  overflow: hidden;
}

.comparison-cv {
  height: 100%;
  position: relative;
  font-size: 0.7rem;
}

/* Mini Template Previews for Comparison */
.classic-mini .mini-header {
  margin-bottom: 0.75rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid var(--border);
}

.mini-name {
  font-weight: 700;
  font-size: 0.75rem;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.mini-title {
  font-size: 0.625rem;
  color: var(--text-secondary);
  text-transform: uppercase;
  font-weight: 500;
}

.mini-sections {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.mini-section-title {
  font-size: 0.5rem;
  font-weight: 700;
  color: var(--primary);
  text-transform: uppercase;
  margin-bottom: 0.25rem;
}

.mini-lines {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.mini-line {
  height: 2px;
  background: var(--text-muted);
  border-radius: 1px;
  opacity: 0.6;
}

.mini-line:nth-child(1) { width: 100%; }
.mini-line:nth-child(2) { width: 70%; }
.mini-line.short { width: 60%; }
.mini-line.colored { background: linear-gradient(90deg, var(--primary), var(--secondary)); opacity: 0.8; }

/* Modern Mini */
.modern-mini .mini-layout {
  display: flex;
  gap: 0.5rem;
  height: 100%;
}

.mini-sidebar {
  flex: 1;
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  padding: 0.5rem;
  border-radius: var(--radius);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.mini-avatar {
  width: 16px;
  height: 16px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 50%;
}

.mini-skills {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  width: 100%;
}

.mini-skill-bar {
  height: 2px;
  background: rgba(255, 255, 255, 0.4);
  border-radius: 1px;
}

.mini-skill-bar:nth-child(1) { width: 90%; background: rgba(255, 255, 255, 0.8); }
.mini-skill-bar:nth-child(2) { width: 75%; background: rgba(255, 255, 255, 0.6); }

.mini-content {
  flex: 2;
  padding: 0.25rem;
}

/* Professional Mini */
.professional-mini .mini-header.centered {
  text-align: center;
  margin-bottom: 0.75rem;
}

.mini-divider {
  height: 2px;
  background: var(--primary);
  width: 20px;
  margin: 0.5rem auto;
  border-radius: 1px;
}

.mini-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.5rem;
}

/* Creative Mini */
.creative-mini {
  position: relative;
}

.mini-shapes {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
}

.mini-shape {
  width: 10px;
  height: 10px;
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  border-radius: 50%;
}

.mini-name.creative {
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 700;
}

.mini-visual {
  display: flex;
  gap: 0.25rem;
  margin-top: 0.5rem;
  justify-content: flex-end;
}

.mini-element {
  width: 12px;
  height: 12px;
  background: linear-gradient(135deg, var(--accent), var(--success));
  border-radius: var(--radius-sm);
}

.mini-element.small {
  width: 8px;
  height: 8px;
}

.comparison-info {
  padding: 1.5rem;
}

.comparison-info .template-name {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 0.5rem;
}

.comparison-info .template-description {
  font-size: 0.875rem;
  color: var(--text-secondary);
  line-height: 1.5;
  margin: 0 0 1rem;
}

.feature-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
  color: var(--text-secondary);
}

.feature-item i {
  font-size: 0.625rem;
  color: var(--success);
}

.comparison-select-btn {
  width: 100%;
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  color: var(--text-inverse);
  border: none;
  padding: 0.875rem 1.5rem;
  border-radius: var(--radius-lg);
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  box-shadow: 0 4px 12px rgba(91, 33, 182, 0.3);
}

.comparison-select-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(91, 33, 182, 0.4);
}

/* Template Info */
.template-info {
  padding: 1.5rem;
}

.template-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.template-name {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
}

.template-badge {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
  color: white;
}

.badge-classic { background: linear-gradient(135deg, #64748b, #475569); }
.badge-modern { background: linear-gradient(135deg, var(--primary), var(--primary-light)); }
.badge-professional { background: linear-gradient(135deg, #0f172a, #1e293b); }
.badge-creative { background: linear-gradient(135deg, #ec4899, #f97316); }

.template-description {
  font-size: 0.875rem;
  color: var(--text-secondary);
  line-height: 1.5;
  margin: 0 0 1rem;
}

.template-features {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.feature-tag {
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.25rem 0.75rem;
  background: var(--bg-subtle);
  color: var(--text-secondary);
  border-radius: 100px;
  border: 1px solid var(--border);
}

/* Minimal Template Action */
.template-action {
  position: absolute;
  bottom: 1rem;
  right: 1rem;
  opacity: 0;
  transform: translateY(10px);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 5;
}

.template-card:hover .template-action {
  opacity: 1;
  transform: translateY(0);
  transition-delay: 0.8s;
}

.action-btn {
  background: var(--primary);
  color: white;
  border: none;
  padding: 0.75rem 1.25rem;
  border-radius: var(--radius-lg);
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: var(--transition);
  box-shadow: 0 6px 20px rgba(91, 33, 182, 0.4);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.action-btn:hover {
  background: var(--primary-light);
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(91, 33, 182, 0.5);
}

.action-btn i {
  font-size: 0.875rem;
}

.action-btn span {
  font-size: 0.875rem;
  white-space: nowrap;
}

/* Page Actions */
.page-actions {
  text-align: center;
  padding: 2rem 0;
  border-top: 1px solid var(--border);
}

.btn-secondary {
  background: var(--bg-elevated);
  color: var(--text-primary);
  padding: 0.875rem 1.5rem;
  border: 2px solid var(--border);
  border-radius: var(--radius-lg);
  text-decoration: none;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: var(--transition);
  box-shadow: var(--shadow);
}

.btn-secondary:hover {
  border-color: var(--primary);
  color: var(--primary);
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.auth-prompt {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  color: var(--text-secondary);
  font-size: 0.875rem;
  flex-wrap: wrap;
}

.auth-prompt p {
  margin: 0;
}

.auth-link {
  color: var(--primary);
  text-decoration: none;
  font-weight: 600;
  transition: var(--transition);
}

.auth-link:hover {
  text-decoration: underline;
}

/* Enhanced Responsive Design */
@media (max-width: 768px) {
  .hero-section {
    padding: 40px 0 30px;
  }
  
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-subtitle {
    font-size: 1rem;
  }
  
  .quick-start-card {
    padding: 2rem;
  }
  
  .start-content {
    flex-direction: column;
    text-align: center;
    gap: 1.5rem;
  }
  
  .start-info h2 {
    font-size: 1.75rem;
  }
  
  /* Enhanced Controls for Mobile */
  .showcase-controls {
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
  }
  
  .view-toggle {
    order: 1;
  }
  
  .demo-controls {
    order: 2;
    justify-content: center;
  }
  
  .toggle-btn,
  .demo-btn {
    padding: 0.625rem 1rem;
    font-size: 0.8rem;
  }
  
  /* Grid Layouts */
  .templates-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .comparison-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  /* Template Previews */
  .template-preview {
    height: 280px;
    padding: 1rem;
  }
  
  .comparison-preview {
    height: 180px;
    padding: 0.75rem;
  }
  
  /* Mobile Touch Interactions */
  .template-card {
    cursor: pointer;
  }
  
  .hover-hint {
    display: none;
  }
  
  .template-card:hover .template-action {
    transition-delay: 0.3s;
  }
  
  .action-btn span {
    display: none;
  }
  
  .action-btn {
    padding: 0.75rem;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    justify-content: center;
  }
  
  /* Modern Template Mobile */
  .preview-modern {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .modern-sidebar {
    padding: 0.75rem;
  }
  
  .professional-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .creative-layout {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  /* Template Info */
  .template-info {
    padding: 1rem;
  }
  
  .showcase-title {
    font-size: 2rem;
  }
  
  /* Comparison View Mobile */
  .comparison-header h4 {
    font-size: 1.75rem;
  }
  
  .comparison-header p {
    font-size: 1rem;
  }
  
  .comparison-info {
    padding: 1rem;
  }
  
  .comparison-info .template-name {
    font-size: 1.125rem;
  }
  
  /* Auth Prompt */
  .auth-prompt {
    flex-direction: column;
    gap: 0.25rem;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 1rem;
  }
  
  .quick-start-card {
    padding: 1.5rem;
  }
  
  .start-info h2 {
    font-size: 1.5rem;
  }
  
  .start-info p {
    font-size: 1rem;
  }
  
  .template-preview {
    height: 200px;
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
}

/* Accessibility & Performance */
@media (prefers-reduced-motion: reduce) {
  .gradient-sphere,
  .template-card {
    animation: none;
  }
  
  .template-card:hover,
  .btn-secondary:hover,
  .start-btn:hover {
    transform: none;
  }
  
  .btn-shine {
    display: none;
  }
}

@media (prefers-color-scheme: dark) {
  .animated-bg {
    opacity: 0.2;
  }
}

/* CSS Variables */
:root {
  --primary: #5B21B6;
  --primary-light: #7C3AED;
  --secondary: #0EA5E9;
  --success: #10B981;
  --info: #0EA5E9;
  --error: #EF4444;
  --bg: #FFFFFF;
  --bg-subtle: #FAFAFA;
  --text: #111827;
  --text-muted: #6B7280;
  --border: #E5E7EB;
  --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
  --radius: 8px;
  --space-xs: 4px;
  --space-sm: 8px;
  --space-md: 16px;
  --space-lg: 24px;
  --space-xl: 32px;
  --space-xxl: 48px;
}

/* Base Styles */
.page-wrapper {
  min-height: 100vh;
  background: var(--bg);
  color: var(--text);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 var(--space-lg);
}

.section-spacing {
  padding: var(--space-xxl) 0;
}

.text-center {
  text-align: center;
}

/* Typography */
.section-title {
  font-size: 48px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: var(--space-md);
  line-height: 1.2;
}

.section-description {
  font-size: 20px;
  color: var(--text-muted);
  line-height: 1.6;
  max-width: 600px;
  margin: 0 auto;
}

/* Messages */
.message-container {
  margin-bottom: var(--space-xl);
}

.message {
  padding: var(--space-md);
  border-radius: var(--radius);
  margin-bottom: var(--space-md);
  text-align: center;
}

.message.error {
  background: #FEF2F2;
  color: var(--error);
  border: 1px solid #FECACA;
}

.message.info {
  background: #EFF6FF;
  color: var(--info);
  border: 1px solid #BFDBFE;
}

/* Quick Start Card */
.quick-start-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  padding: 3rem;
  margin-bottom: 4rem;
  color: white;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.start-content {
  display: flex;
  align-items: center;
  gap: 2rem;
  max-width: 800px;
  margin: 0 auto;
}

.start-icon {
  width: 80px;
  height: 80px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  backdrop-filter: blur(10px);
}

.start-info {
  flex: 1;
}

.start-info h2 {
  font-size: 2rem;
  margin: 0 0 1rem 0;
  font-weight: 700;
}

.start-info p {
  font-size: 1.125rem;
  margin: 0;
  opacity: 0.9;
  line-height: 1.6;
}

.start-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  background: white;
  color: #667eea;
  border: none;
  border-radius: 12px;
  font-size: 1.125rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.start-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

/* Templates Showcase */
.templates-showcase {
  margin-bottom: 4rem;
}

.showcase-title {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text);
  text-align: center;
  margin-bottom: 3rem;
}

/* Templates Grid */
.templates-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: var(--space-xl);
  margin-bottom: var(--space-xxl);
}

.template-card {
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: var(--shadow);
}

.template-card:hover {
  border-color: var(--primary);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

/* Template Preview */
.template-preview {
  height: 240px;
  background: var(--bg-subtle);
  border-bottom: 1px solid var(--border);
  padding: var(--space-lg);
  position: relative;
}

.preview-header {
  font-weight: 700;
  font-size: 12px;
  color: var(--text);
  margin-bottom: var(--space-sm);
}

.preview-header.centered {
  text-align: center;
}

.preview-line {
  height: 4px;
  background: var(--text-muted);
  border-radius: 2px;
  margin-bottom: var(--space-xs);
  opacity: 0.6;
}

.preview-line.small { width: 40%; }
.preview-line.medium { width: 60%; }
.preview-line.large { width: 75%; }
.preview-line.full { width: 100%; }

/* Classic Preview */
.preview-classic {
  display: flex;
  flex-direction: column;
  gap: var(--space-xs);
}

/* Modern Preview */
.preview-modern {
  display: flex;
  gap: var(--space-md);
  height: 100%;
}

.preview-sidebar {
  flex: 1;
  background: rgba(91, 33, 182, 0.1);
  padding: var(--space-md);
  border-radius: var(--radius);
}

.preview-content {
  flex: 2;
  padding: var(--space-sm) 0;
}

.preview-avatar {
  width: 24px;
  height: 24px;
  background: var(--primary);
  border-radius: 50%;
  margin: 0 auto var(--space-sm);
}

/* Professional Preview */
.preview-professional {
  text-align: center;
}

.preview-divider {
  height: 2px;
  background: var(--primary);
  width: 40px;
  margin: var(--space-sm) auto;
}

.preview-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-md);
  margin-top: var(--space-md);
}

/* Creative Preview */
.preview-creative {
  position: relative;
}

.creative-shape {
  position: absolute;
  top: var(--space-sm);
  right: var(--space-sm);
  width: 16px;
  height: 16px;
  background: var(--primary);
  border-radius: 50%;
}

.creative-accent {
  position: absolute;
  bottom: var(--space-sm);
  right: var(--space-lg);
  width: 12px;
  height: 12px;
  background: #F59E0B;
  border-radius: 50%;
}

/* Template Info */
.template-info {
  padding: var(--space-lg);
  text-align: center;
}

.template-name {
  font-size: 18px;
  font-weight: 600;
  color: var(--text);
  margin: 0 0 var(--space-sm) 0;
}

.template-description {
  font-size: 14px;
  color: var(--text-muted);
  line-height: 1.5;
  margin: 0;
}

/* Page Actions */
.page-actions {
  text-align: center;
  padding-top: var(--space-xl);
  border-top: 1px solid var(--border);
}

.btn-secondary {
  background: var(--bg);
  color: var(--text);
  padding: var(--space-md) var(--space-lg);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  text-decoration: none;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: var(--space-sm);
  transition: all 0.2s ease;
}

.btn-secondary:hover {
  border-color: var(--primary);
  color: var(--primary);
}

.auth-prompt {
  color: var(--text-muted);
  font-size: 14px;
}

.text-link {
  color: var(--primary);
  text-decoration: none;
}

.text-link:hover {
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
  .section-title {
    font-size: 32px;
  }

  .section-description {
    font-size: 16px;
  }

  .quick-start-card {
    padding: 2rem;
  }

  .start-content {
    flex-direction: column;
    text-align: center;
    gap: 1.5rem;
  }

  .start-info h2 {
    font-size: 1.5rem;
  }

  .templates-grid {
    grid-template-columns: 1fr;
    gap: var(--space-lg);
  }

  .template-preview {
    height: 180px;
    padding: var(--space-md);
  }

  .preview-modern {
    flex-direction: column;
    gap: var(--space-sm);
  }

  .preview-sidebar {
    padding: var(--space-sm);
  }

  .preview-grid {
    grid-template-columns: 1fr;
    gap: var(--space-sm);
  }

  .template-info {
    padding: var(--space-md);
  }
}

.animated-item {
  animation-delay: var(--delay, 0s);
}

.skill-bar .bar .progress {
  width: var(--bar-width, 100%);
}
</style>

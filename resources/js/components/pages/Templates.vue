<template>
  <div class="page-wrapper">
    <!-- Hero Section -->
    <section class="section-spacing">
      <div class="container">
        <div class="text-center">
          <h1 class="section-title">Create Your Professional Resume</h1>
          <p class="section-description">
            Build a stunning resume with our intuitive form and choose from beautiful templates as you go.
          </p>
        </div>
      </div>
    </section>

    <!-- Templates Grid -->
    <section class="section-spacing">
      <div class="container">
        <!-- Messages -->
        <div v-if="errorMessage || infoMessage" class="message-container">
          <div v-if="errorMessage" class="message error">{{ errorMessage }}</div>
          <div v-if="infoMessage" class="message info">{{ infoMessage }}</div>
        </div>

        <!-- Quick Start Card -->
        <div class="quick-start-card">
          <div class="start-content">
            <div class="start-icon">
              <i class="fas fa-rocket"></i>
            </div>
            <div class="start-info">
              <h2>Ready to Get Started?</h2>
              <p>Create your professional resume in minutes. You can choose and switch between templates as you build your resume.</p>
            </div>
            <button @click="startCreating" class="start-btn">
              <i class="fas fa-plus"></i>
              Start Creating
            </button>
          </div>
        </div>

        <!-- Templates Preview -->
        <div class="templates-showcase">
          <h3 class="showcase-title">Available Templates</h3>
          <div class="templates-grid">
            <div
              v-for="template in templates"
              :key="template.id"
              class="template-card"
            >
            <!-- Template Preview -->
            <div class="template-preview">
              <!-- Classic Template -->
              <div v-if="template.id === 'classic'" class="preview-classic">
                <div class="preview-header">JOHN DOE</div>
                <div class="preview-line full"></div>
                <div class="preview-line medium"></div>
                <div class="preview-line large"></div>
                <div class="preview-line small"></div>
                <div class="preview-line full"></div>
              </div>

              <!-- Modern Template -->
              <div v-if="template.id === 'modern'" class="preview-modern">
                <div class="preview-sidebar">
                  <div class="preview-avatar"></div>
                  <div class="preview-line small"></div>
                  <div class="preview-line medium"></div>
                </div>
                <div class="preview-content">
                  <div class="preview-header">JOHN DOE</div>
                  <div class="preview-line medium"></div>
                  <div class="preview-line full"></div>
                  <div class="preview-line large"></div>
                </div>
              </div>

              <!-- Professional Template -->
              <div v-if="template.id === 'professional'" class="preview-professional">
                <div class="preview-header centered">JOHN DOE</div>
                <div class="preview-divider"></div>
                <div class="preview-grid">
                  <div class="preview-left">
                    <div class="preview-line full"></div>
                    <div class="preview-line large"></div>
                  </div>
                  <div class="preview-right">
                    <div class="preview-line full"></div>
                    <div class="preview-line medium"></div>
                    <div class="preview-line full"></div>
                  </div>
                </div>
              </div>

              <!-- Creative Template -->
              <div v-if="template.id === 'creative'" class="preview-creative">
                <div class="creative-shape"></div>
                <div class="preview-header">JOHN DOE</div>
                <div class="preview-line full"></div>
                <div class="preview-line medium"></div>
                <div class="creative-accent"></div>
              </div>
            </div>

            <!-- Template Info -->
                          <div class="template-info">
                <h4 class="template-name">{{ template.name }}</h4>
                <p class="template-description">{{ template.description }}</p>
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
            Back to Dashboard
          </router-link>
          <p v-else class="auth-prompt">
            Have an account? <router-link to="/login" class="text-link">Sign in</router-link> to save and manage your CVs.
          </p>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'Templates',
  setup() {
    const router = useRouter()
    const errorMessage = ref('')
    const infoMessage = ref('')

    const isAuthenticated = computed(() => !!localStorage.getItem('auth_token'))

    const templates = ref([
      {
        id: 'classic',
        name: 'Classic',
        description: 'A traditional and clear design, ideal for most professions.',
      },
      {
        id: 'modern',
        name: 'Modern',
        description: 'A contemporary look with focus on design and readability.',
      },
      {
        id: 'professional',
        name: 'Professional',
        description: 'Elegant and structured, perfect for executive and corporate roles.',
      },
      {
        id: 'creative',
        name: 'Creative',
        description: 'A modern and dynamic design, ideal for creative and innovative fields.',
      },
    ])

    const selectTemplate = (templateId) => {
      if (!isAuthenticated.value) {
        infoMessage.value = 'Please sign in or register to continue with your chosen template.'
        router.push({ name: 'login', query: { redirect: `/create-cv?template=${templateId}` } })
      } else {
        router.push({ path: '/create-cv', query: { template: templateId } })
      }
    }

    const startCreating = () => {
      if (!isAuthenticated.value) {
        infoMessage.value = 'Please sign in or register to create your resume.'
        router.push({ name: 'login', query: { redirect: '/create-cv' } })
      } else {
        router.push('/create-cv')
      }
    }

    return { templates, selectTemplate, startCreating, isAuthenticated, errorMessage, infoMessage }
  },
}
</script>

<style scoped>
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
</style>

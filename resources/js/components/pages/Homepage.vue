<template>
  <div class="homepage-wrapper">
    <a href="#main-content" class="skip-to-content">Skip to main content</a>
    
    <!-- Simple Theme Toggle -->
    <button class="theme-toggle" @click="toggleTheme" :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'">
      <i class="fas" :class="isDark ? 'fa-moon' : 'fa-sun'"></i>
    </button>

    <!-- HERO SECTION -->
    <main id="main-content">
      <section class="hero-section">
        <div class="container">
          <div class="hero-content">
            <div class="hero-badge">
              <span>🚀</span>
              <span>{{ t('cv_created_badge') }}</span>
            </div>
            <h1 class="hero-title">
              {{ t('home_title_create') }}<br />
              <span class="accent-text">{{ typewriterText }}</span>
            </h1>
            <p class="hero-subtitle">
              {{ t('home_subtitle') }}
            </p>
            <div class="hero-actions">
              <router-link to="/register" class="btn-primary">
                {{ t('create_cv_now') }}
                <i class="fas fa-arrow-right"></i>
              </router-link>
              <router-link to="/templates" class="btn-secondary">
                {{ t('view_templates') }}
              </router-link>
            </div>
            <div class="hero-stats">
              <div class="stat">
                <span class="stat-number">{{ stats.cvCreated || '10,000' }}</span>
                <span class="stat-label">{{ t('cv_created') }}</span>
              </div>
              <div class="stat">
                <span class="stat-number">{{ stats.templates || '4' }}</span>
                <span class="stat-label">{{ t('templates_count') }}</span>
              </div>
              <div class="stat">
                <span class="stat-number">{{ stats.satisfaction || '98' }}%</span>
                <span class="stat-label">{{ t('satisfaction') }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- TRUSTED COMPANIES -->
      <section class="trusted-section">
        <div class="container">
          <p class="trusted-text">{{ t('used_by_professionals_at') }}</p>
          <div class="companies">
            <span class="company">Google</span>
            <span class="company">Microsoft</span>
            <span class="company">Amazon</span>
            <span class="company">Meta</span>
            <span class="company">Apple</span>
            <span class="company">Netflix</span>
          </div>
        </div>
      </section>

      <!-- TEMPLATE SHOWCASE -->
      <section class="templates-section">
        <div class="container">
          <div class="section-header">
            <span class="section-badge">{{ t('our_templates') }}</span>
            <h2 class="section-title">{{ t('designs_that_impress') }}</h2>
            <p class="section-subtitle">{{ t('template_description') }}</p>
          </div>
          
          <div class="templates-grid">
            <div class="template-card" @click="selectTemplate('classic')">
              <div class="template-preview">
                <div class="preview-placeholder">
                  <h4>Classic Template</h4>
                  <p>John Doe</p>
                  <p>Software Engineer</p>
                  <div class="preview-lines">
                    <div class="line"></div>
                    <div class="line short"></div>
                    <div class="line"></div>
                  </div>
                </div>
              </div>
              <div class="template-info">
                <h3>{{ t('classic') }}</h3>
                <p>{{ t('elegant_professional') }}</p>
                <span class="template-badge">{{ t('most_used') }}</span>
              </div>
            </div>
            
            <div class="template-card featured" @click="selectTemplate('modern')">
              <div class="template-preview">
                <div class="preview-placeholder modern">
                  <h4>Modern Template</h4>
                  <p>John Doe</p>
                  <p>Software Engineer</p>
                  <div class="preview-lines">
                    <div class="line"></div>
                    <div class="line short"></div>
                    <div class="line"></div>
                  </div>
                </div>
              </div>
              <div class="template-info">
                <h3>{{ t('modern') }}</h3>
                <p>{{ t('minimal_clean') }}</p>
                <span class="template-badge premium">{{ t('premium') }}</span>
              </div>
            </div>
            
            <div class="template-card" @click="selectTemplate('professional')">
              <div class="template-preview">
                <div class="preview-placeholder">
                  <h4>Professional Template</h4>
                  <p>John Doe</p>
                  <p>Software Engineer</p>
                  <div class="preview-lines">
                    <div class="line"></div>
                    <div class="line short"></div>
                    <div class="line"></div>
                  </div>
                </div>
              </div>
              <div class="template-info">
                <h3>{{ t('professional') }}</h3>
                <p>{{ t('serious_detailed') }}</p>
                <span class="template-badge new">{{ t('new') }}</span>
              </div>
            </div>
            
            <div class="template-card" @click="selectTemplate('creative')">
              <div class="template-preview">
                <div class="preview-placeholder creative">
                  <h4>Creative Template</h4>
                  <p>John Doe</p>
                  <p>Software Engineer</p>
                  <div class="preview-lines">
                    <div class="line"></div>
                    <div class="line short"></div>
                    <div class="line"></div>
                  </div>
                </div>
              </div>
              <div class="template-info">
                <h3>{{ t('creative') }}</h3>
                <p>{{ t('unique_modern') }}</p>
              </div>
            </div>
          </div>
          
          <div class="template-cta">
            <router-link to="/templates" class="btn-link">
              {{ t('view_all_templates') }}
              <i class="fas fa-arrow-right"></i>
            </router-link>
          </div>
        </div>
      </section>

      <!-- HOW IT WORKS -->
      <section class="process-section">
        <div class="container">
          <div class="section-header">
            <span class="section-badge">{{ t('process') }}</span>
            <h2 class="section-title">{{ t('how_it_works') }}</h2>
          </div>
          
          <div class="process-steps">
            <div class="step">
              <div class="step-number">1</div>
              <div class="step-content">
                <div class="step-icon">
                  <i class="fas fa-user-plus"></i>
                </div>
                <h3>{{ t('register') }}</h3>
                <p>{{ t('create_account_fast') }}</p>
              </div>
            </div>
            
            <div class="step">
              <div class="step-number">2</div>
              <div class="step-content">
                <div class="step-icon">
                  <i class="fas fa-palette"></i>
                </div>
                <h3>{{ t('choose_template') }}</h3>
                <p>{{ t('explore_templates_match') }}</p>
              </div>
            </div>
            
            <div class="step">
              <div class="step-number">3</div>
              <div class="step-content">
                <div class="step-icon">
                  <i class="fas fa-edit"></i>
                </div>
                <h3>{{ t('fill_details') }}</h3>
                <p>{{ t('add_info_intuitive_editor') }}</p>
              </div>
            </div>
            
            <div class="step">
              <div class="step-number">4</div>
              <div class="step-content">
                <div class="step-icon">
                  <i class="fas fa-download"></i>
                </div>
                <h3>{{ t('download') }}</h3>
                <p>{{ t('get_professional_cv_pdf') }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- FEATURES -->
      <section class="features-section">
        <div class="container">
          <div class="section-header">
            <span class="section-badge">{{ t('features') }}</span>
            <h2 class="section-title">{{ t('why_cv_atelier') }}</h2>
          </div>
          
          <div class="features-grid">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-bolt"></i>
              </div>
              <h3>{{ t('super_fast') }}</h3>
              <p>{{ t('create_cv_5_minutes') }}</p>
            </div>
            
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-shield-alt"></i>
              </div>
              <h3>{{ t('100_secure') }}</h3>
              <p>{{ t('data_encrypted_private') }}</p>
            </div>
            
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-magic"></i>
              </div>
              <h3>{{ t('ai_powered') }}</h3>
              <p>{{ t('intelligent_suggestions') }}</p>
            </div>
            
            <div class="feature-card premium">
              <div class="feature-icon">
                <i class="fas fa-crown"></i>
              </div>
              <h3>{{ t('exclusive_templates') }}</h3>
              <p>{{ t('access_premium_features') }}</p>
              <span class="premium-badge">{{ t('premium') }}</span>
            </div>
          </div>
        </div>
      </section>

      <!-- TESTIMONIALS -->
      <section class="testimonials-section">
        <div class="container">
          <div class="section-header">
            <span class="section-badge">{{ t('testimonials') }}</span>
            <h2 class="section-title">{{ t('what_users_say') }}</h2>
          </div>
          
          <div class="testimonials">
            <div class="testimonial" v-for="(testimonial, index) in testimonialData" :key="index">
              <div class="testimonial-content">
                <div class="stars">
                  <i class="fas fa-star" v-for="i in 5" :key="i"></i>
                </div>
                <p>{{ testimonial.text }}</p>
                <div class="author">
                  <img :src="testimonial.image" :alt="testimonial.name" />
                  <div>
                    <h4>{{ testimonial.name }}</h4>
                    <p>{{ testimonial.role }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA SECTION -->
      <section class="cta-section">
        <div class="container">
          <div class="cta-content">
            <h2>{{ t('ready_to_start_career') }}</h2>
            <p>{{ t('join_thousands_professionals') }}</p>
            <div class="cta-actions">
              <router-link to="/register" class="btn-primary">
                {{ t('start_now_free') }}
                <i class="fas fa-arrow-right"></i>
              </router-link>
              <router-link to="/templates" class="btn-secondary">
                {{ t('view_demo') }}
              </router-link>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script>
import { onMounted, onBeforeUnmount, ref } from 'vue'
import { useI18n } from 'vue-i18n'

export default {
  name: 'Homepage',
  setup() {
    const { t, locale } = useI18n()
    const isDark = ref(false)
    const typewriterText = ref('')
    const selectedTemplate = ref('modern')
    
    const typewriterPhrases = ref([
      'that gets you hired',
      'that stands out', 
      'that opens doors'
    ])
    
    let typewriterIndex = 0
    let charIndex = 0
    let isDeleting = false
    let typewriterTimeout = null
    
    const stats = ref({
      cvCreated: 10000,
      templates: 4,
      satisfaction: 98
    })

    const testimonialData = ref([
      {
        text: 'Amazing tool! Got my dream job within weeks.',
        name: 'Sarah Johnson',
        role: 'Software Engineer',
        image: 'https://i.pravatar.cc/150?img=1'
      },
      {
        text: 'Professional templates that really stand out.',
        name: 'Michael Chen',
        role: 'Product Designer',
        image: 'https://i.pravatar.cc/150?img=2'
      },
      {
        text: 'Simple, fast, and effective. Highly recommended!',
        name: 'Emily Brown',
        role: 'Marketing Manager',
        image: 'https://i.pravatar.cc/150?img=3'
      }
    ])

    const toggleTheme = () => {
      isDark.value = !isDark.value
      document.body.classList.toggle('dark-theme')
    }

    const selectTemplate = (template) => {
      selectedTemplate.value = template
      console.log('Template selected:', template)
    }

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

    onMounted(() => {
      isDark.value = document.body.classList.contains('dark-theme')
      
      // Start typewriter
      setTimeout(() => {
        typeWriter()
      }, 100)
    })

    onBeforeUnmount(() => {
      clearTimeout(typewriterTimeout)
    })

    return {
      isDark,
      typewriterText,
      selectedTemplate,
      stats,
      testimonialData,
      selectTemplate,
      toggleTheme,
      t
    }
  }
}
</script>

<style scoped>
/* CSS Variables */
:root {
  --primary: #5B21B6;
  --primary-light: #7C3AED;
  --secondary: #0EA5E9;
  --accent: #F59E0B;
  --success: #10B981;
  --bg: #FFFFFF;
  --bg-subtle: #FAFAFA;
  --text: #111827;
  --text-muted: #6B7280;
  --border: #E5E7EB;
  --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

body.dark-theme {
  --bg: #0F172A;
  --bg-subtle: #1E293B;
  --text: #F8FAFC;
  --text-muted: #CBD5E1;
  --border: #334155;
}

/* Base Styles */
.homepage-wrapper {
  min-height: 100vh;
  background: var(--bg);
  color: var(--text);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Theme Toggle */
.theme-toggle {
  position: fixed;
  top: 24px;
  right: 24px;
  z-index: 1000;
  width: 48px;
  height: 48px;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: var(--shadow);
}

.theme-toggle:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.15);
}

.theme-toggle i {
  font-size: 18px;
  color: var(--text);
}

/* Hero Section */
.hero-section {
  padding: 120px 0 80px;
  text-align: center;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--bg-subtle);
  border: 1px solid var(--border);
  padding: 8px 16px;
  border-radius: 100px;
  font-size: 14px;
  font-weight: 500;
  color: var(--text-muted);
  margin-bottom: 32px;
}

.hero-title {
  font-size: clamp(48px, 8vw, 72px);
  font-weight: 700;
  line-height: 1.1;
  margin-bottom: 24px;
  color: var(--text);
}

.accent-text {
  color: var(--primary);
}

.accent-text::after {
  content: '|';
  animation: blink 1s infinite;
  margin-left: 2px;
}

@keyframes blink {
  0%, 50% { opacity: 1; }
  51%, 100% { opacity: 0; }
}

.hero-subtitle {
  font-size: 20px;
  color: var(--text-muted);
  line-height: 1.6;
  margin-bottom: 40px;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.hero-actions {
  display: flex;
  justify-content: center;
  gap: 16px;
  margin-bottom: 60px;
  flex-wrap: wrap;
}

.btn-primary {
  background: var(--primary);
  color: white;
  padding: 16px 32px;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
}

.btn-primary:hover {
  background: var(--primary-light);
  transform: translateY(-1px);
}

.btn-secondary {
  background: transparent;
  color: var(--text);
  padding: 16px 32px;
  border: 1px solid var(--border);
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
}

.btn-secondary:hover {
  background: var(--bg-subtle);
  transform: translateY(-1px);
}

.hero-stats {
  display: flex;
  justify-content: center;
  gap: 48px;
  flex-wrap: wrap;
}

.stat {
  text-align: center;
}

.stat-number {
  display: block;
  font-size: 32px;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 4px;
}

.stat-label {
  font-size: 14px;
  color: var(--text-muted);
}

/* Trusted Companies */
.trusted-section {
  padding: 60px 0;
  background: var(--bg-subtle);
  text-align: center;
}

.trusted-text {
  font-size: 14px;
  color: var(--text-muted);
  margin-bottom: 24px;
}

.companies {
  display: flex;
  justify-content: center;
  gap: 40px;
  flex-wrap: wrap;
}

.company {
  font-size: 16px;
  font-weight: 600;
  color: var(--text-muted);
  opacity: 0.7;
  transition: opacity 0.2s ease;
}

.company:hover {
  opacity: 1;
}

/* Section Headers */
.section-header {
  text-align: center;
  margin-bottom: 80px;
}

.section-badge {
  display: inline-block;
  background: var(--bg-subtle);
  color: var(--primary);
  padding: 8px 16px;
  border-radius: 100px;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 16px;
  border: 1px solid var(--border);
}

.section-title {
  font-size: clamp(32px, 5vw, 48px);
  font-weight: 700;
  margin-bottom: 16px;
  color: var(--text);
}

.section-subtitle {
  font-size: 18px;
  color: var(--text-muted);
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
}

/* Templates Section */
.templates-section {
  padding: 120px 0;
}

.templates-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
  margin-bottom: 48px;
}

.template-card {
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
}

.template-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px 0 rgba(0, 0, 0, 0.1);
  border-color: var(--primary);
}

.template-card.featured {
  border-color: var(--primary);
  box-shadow: 0 0 0 1px var(--primary);
}

.template-preview {
  padding: 32px;
  background: var(--bg-subtle);
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.preview-placeholder {
  background: white;
  padding: 16px;
  border-radius: 8px;
  width: 180px;
  box-shadow: var(--shadow);
}

.preview-placeholder.modern {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.preview-placeholder.creative {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
}

.preview-placeholder h4 {
  font-size: 14px;
  font-weight: 700;
  margin-bottom: 8px;
}

.preview-placeholder p {
  font-size: 12px;
  margin-bottom: 4px;
  opacity: 0.8;
}

.preview-lines {
  margin-top: 12px;
}

.line {
  height: 2px;
  background: currentColor;
  margin-bottom: 4px;
  opacity: 0.3;
}

.line.short {
  width: 60%;
}

.template-info {
  padding: 24px;
  position: relative;
}

.template-info h3 {
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 8px;
  color: var(--text);
}

.template-info p {
  color: var(--text-muted);
  font-size: 14px;
  margin-bottom: 12px;
}

.template-badge {
  position: absolute;
  top: 16px;
  right: 16px;
  background: var(--bg-subtle);
  color: var(--text-muted);
  padding: 4px 12px;
  border-radius: 100px;
  font-size: 12px;
  font-weight: 600;
  border: 1px solid var(--border);
}

.template-badge.premium {
  background: var(--accent);
  color: white;
  border: none;
}

.template-badge.new {
  background: var(--success);
  color: white;
  border: none;
}

.template-cta {
  text-align: center;
}

.btn-link {
  color: var(--primary);
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: gap 0.2s ease;
}

.btn-link:hover {
  gap: 12px;
}

/* Process Section */
.process-section {
  padding: 120px 0;
  background: var(--bg-subtle);
}

.process-steps {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 40px;
}

.step {
  text-align: center;
  position: relative;
}

.step-number {
  width: 48px;
  height: 48px;
  background: var(--primary);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  margin: 0 auto 24px;
  font-size: 18px;
}

.step-icon {
  font-size: 32px;
  color: var(--primary);
  margin-bottom: 16px;
}

.step h3 {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 12px;
  color: var(--text);
}

.step p {
  color: var(--text-muted);
  line-height: 1.6;
}

/* Features Section */
.features-section {
  padding: 120px 0;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 32px;
}

.feature-card {
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 32px 24px;
  text-align: center;
  transition: all 0.2s ease;
  position: relative;
}

.feature-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px 0 rgba(0, 0, 0, 0.1);
}

.feature-card.premium {
  border-color: var(--accent);
}

.feature-icon {
  width: 64px;
  height: 64px;
  background: var(--bg-subtle);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 24px;
  font-size: 24px;
  color: var(--primary);
}

.feature-card h3 {
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 12px;
  color: var(--text);
}

.feature-card p {
  color: var(--text-muted);
  line-height: 1.6;
}

.premium-badge {
  position: absolute;
  top: 16px;
  right: 16px;
  background: var(--accent);
  color: white;
  padding: 4px 12px;
  border-radius: 100px;
  font-size: 12px;
  font-weight: 600;
}

/* Testimonials Section */
.testimonials-section {
  padding: 120px 0;
  background: var(--bg-subtle);
}

.testimonials {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 32px;
}

.testimonial {
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 32px;
}

.stars {
  color: var(--accent);
  margin-bottom: 16px;
  display: flex;
  gap: 4px;
}

.testimonial p {
  color: var(--text);
  line-height: 1.6;
  margin-bottom: 24px;
  font-style: italic;
}

.author {
  display: flex;
  align-items: center;
  gap: 16px;
}

.author img {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
}

.author h4 {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 4px;
  color: var(--text);
}

.author p {
  font-size: 14px;
  color: var(--text-muted);
  margin: 0;
  font-style: normal;
}

/* CTA Section */
.cta-section {
  padding: 120px 0;
  background: var(--primary);
  color: white;
  text-align: center;
}

.cta-content h2 {
  font-size: clamp(32px, 5vw, 48px);
  font-weight: 700;
  margin-bottom: 16px;
}

.cta-content p {
  font-size: 18px;
  opacity: 0.9;
  margin-bottom: 32px;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.cta-actions {
  display: flex;
  justify-content: center;
  gap: 16px;
  flex-wrap: wrap;
}

.cta-section .btn-primary {
  background: white;
  color: var(--primary);
}

.cta-section .btn-primary:hover {
  background: var(--bg-subtle);
}

.cta-section .btn-secondary {
  background: transparent;
  color: white;
  border-color: rgba(255, 255, 255, 0.3);
}

.cta-section .btn-secondary:hover {
  background: rgba(255, 255, 255, 0.1);
}

/* Responsive */
@media (max-width: 768px) {
  .hero-section {
    padding: 80px 0 60px;
  }
  
  .hero-actions,
  .cta-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .hero-actions > *,
  .cta-actions > * {
    width: 100%;
    max-width: 300px;
    justify-content: center;
  }
  
  .hero-stats {
    gap: 32px;
  }
  
  .companies {
    gap: 24px;
  }
  
  .process-steps {
    grid-template-columns: 1fr;
    gap: 32px;
  }
  
  .features-grid,
  .testimonials {
    grid-template-columns: 1fr;
    gap: 24px;
  }
  
  .templates-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
}

/* Skip to content */
.skip-to-content {
  position: absolute;
  left: -999px;
  top: auto;
  width: 1px;
  height: 1px;
  overflow: hidden;
  z-index: 10000;
  background: var(--bg);
  color: var(--text);
  padding: 8px 16px;
  border-radius: 4px;
  font-weight: 600;
  text-decoration: none;
}

.skip-to-content:focus {
  left: 16px;
  top: 16px;
  width: auto;
  height: auto;
  box-shadow: 0 0 0 2px var(--primary);
}
</style>


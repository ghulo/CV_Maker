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
            <!-- Left Side: Interactive Content -->
            <div class="hero-left">
              <div class="hero-badge">
                <i class="fas fa-sparkles"></i>
                <span>{{ t('cv_created_badge') }}</span>
              </div>
              
              <h1 class="hero-title">
                {{ t('home_title_create') }}<br />
                <span class="accent-text">{{ typewriterText }}</span>
              </h1>
              
              <p class="hero-subtitle">
                {{ t('home_subtitle') }}
              </p>

              <!-- Interactive CV Inputs -->
              <div class="hero-playground">
                <h3 class="playground-title">
                  <i class="fas fa-magic"></i>
                  Try it live - watch your CV update instantly!
                </h3>
                
                <div class="playground-inputs">
                  <div class="input-group">
                    <label>Your Name</label>
                    <input 
                      v-model="heroName" 
                      type="text" 
                      placeholder="Enter your name..."
                      class="hero-input"
                      @input="updateHeroCV"
                    />
                  </div>
                  
                  <div class="input-group">
                    <label>Job Title</label>
                    <input 
                      v-model="heroTitle" 
                      type="text" 
                      placeholder="e.g. Software Developer"
                      class="hero-input"
                      @input="updateHeroCV"
                    />
                  </div>
                  
                  <div class="input-group">
                    <label>Template Style</label>
                    <div class="template-switcher">
                      <button 
                        v-for="template in heroTemplates" 
                        :key="template.id"
                        @click="switchHeroTemplate(template.id)"
                        :class="['template-btn', { 'active': heroActiveTemplate === template.id }]"
                      >
                        <i :class="template.icon"></i>
                        {{ template.name }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="hero-actions">
                <router-link to="/register" class="btn-primary">
                  <i class="fas fa-rocket"></i>
                  <span>{{ t('create_cv_now') }}</span>
                  <div class="btn-shine"></div>
                </router-link>
                <router-link to="/templates" class="btn-secondary">
                  <i class="fas fa-eye"></i>
                  <span>{{ t('view_templates') }}</span>
                </router-link>
              </div>
            </div>

            <!-- Right Side: Live CV Preview -->
            <div class="hero-right">
              <div class="hero-cv-container">
                <div class="cv-preview-badge">
                  <i class="fas fa-bolt"></i>
                  Live Preview
                </div>
                
                <div class="hero-cv-preview" :class="[`template-${heroActiveTemplate}`, { 'updating': isUpdating }]">
                  <!-- Classic Template -->
                  <div v-if="heroActiveTemplate === 'classic'" class="cv-template classic-template">
                    <div class="cv-header">
                      <h2 class="cv-name" :class="{ 'typing': heroName }">
                        {{ heroName || 'Sarah Johnson' }}
                      </h2>
                      <p class="cv-title" :class="{ 'typing': heroTitle }">
                        {{ heroTitle || 'Senior Developer' }}
                      </p>
                      <div class="cv-contact">
                        <span class="contact-item">
                          <i class="fas fa-envelope"></i>
                          {{ heroName ? heroName.toLowerCase().replace(' ', '.') + '@email.com' : 'sarah.johnson@email.com' }}
                        </span>
                        <span class="contact-item">
                          <i class="fas fa-phone"></i>
                          +1 (555) 123-4567
                        </span>
                      </div>
                    </div>
                    
                    <div class="cv-sections">
                      <div class="cv-section" :class="{ 'filled': heroName || heroTitle }">
                        <h3>Experience</h3>
                        <div class="section-content">
                          <div class="experience-item">
                            <div class="cv-line main-line"></div>
                            <div class="cv-line short-line"></div>
                            <div class="cv-line medium-line"></div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="cv-section" :class="{ 'filled': heroName || heroTitle }">
                        <h3>Skills</h3>
                        <div class="skills-container">
                          <span class="skill-tag" v-for="skill in heroSkills.slice(0, 4)" :key="skill">{{ skill }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Modern Template -->
                  <div v-if="heroActiveTemplate === 'modern'" class="cv-template modern-template">
                    <div class="modern-layout">
                      <div class="modern-sidebar">
                        <div class="profile-section">
                          <div class="profile-circle" :class="{ 'appearing': heroName || heroTitle }"></div>
                          <h2 class="cv-name" :class="{ 'typing': heroName }">
                            {{ heroName || 'Sarah Johnson' }}
                          </h2>
                          <p class="cv-title" :class="{ 'typing': heroTitle }">
                            {{ heroTitle || 'Senior Developer' }}
                          </p>
                        </div>
                        
                        <div class="sidebar-section">
                          <h4>Skills</h4>
                          <div class="skill-bars">
                            <div class="skill-bar" v-for="(skill, index) in heroSkills.slice(0, 4)" :key="skill">
                              <span class="skill-name">{{ skill }}</span>
                              <div class="bar">
                                <div class="progress" 
                                     :class="{ 'filling': heroName || heroTitle }"
                                     :style="{ width: `${85 - index * 5}%` }"></div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div class="modern-main">
                        <div class="cv-section" :class="{ 'filled': heroName || heroTitle }">
                          <h3>Experience</h3>
                          <div class="cv-line main-line"></div>
                          <div class="cv-line short-line"></div>
                          <div class="cv-line medium-line"></div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Creative Template -->
                  <div v-if="heroActiveTemplate === 'creative'" class="cv-template creative-template">
                    <div class="creative-shapes" :class="{ 'floating': heroName || heroTitle }">
                      <div class="shape shape-1"></div>
                      <div class="shape shape-2"></div>
                    </div>
                    
                    <div class="cv-content creative-layout">
                      <h2 class="cv-name creative-name" :class="{ 'typing': heroName }">
                        {{ heroName || 'Sarah Johnson' }}
                      </h2>
                      <p class="cv-title creative-title" :class="{ 'typing': heroTitle }">
                        {{ heroTitle || 'Senior Developer' }}
                      </p>
                      
                      <div class="creative-grid">
                        <div class="cv-section" :class="{ 'filled': heroName || heroTitle }">
                          <div class="content-lines">
                            <div class="cv-line main-line"></div>
                            <div class="cv-line short-line"></div>
                          </div>
                        </div>
                        
                        <div class="creative-visual" :class="{ 'animating': heroName || heroTitle }">
                          <div class="visual-element"></div>
                          <div class="visual-element small"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Interactive Stats -->
              <div class="hero-stats">
                <div class="stat interactive-stat" @mouseenter="animateStat('cvs')" :class="{ 'animated': animatedStats.cvs }">
                  <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                  </div>
                  <span class="stat-number">{{ stats.cvCreated || '10,000' }}</span>
                  <span class="stat-label">{{ t('cv_created') }}</span>
                </div>
                
                <div class="stat interactive-stat" @mouseenter="animateStat('templates')" :class="{ 'animated': animatedStats.templates }">
                  <div class="stat-icon">
                    <i class="fas fa-palette"></i>
                  </div>
                  <span class="stat-number">{{ stats.templates || '4' }}</span>
                  <span class="stat-label">{{ t('templates_count') }}</span>
                </div>
                
                <div class="stat interactive-stat" @mouseenter="animateStat('satisfaction')" :class="{ 'animated': animatedStats.satisfaction }">
                  <div class="stat-icon">
                    <i class="fas fa-star"></i>
                  </div>
                  <span class="stat-number">{{ stats.satisfaction || '98' }}%</span>
                  <span class="stat-label">{{ t('satisfaction') }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- TRUSTED COMPANIES -->
      <section class="trusted-section">
        <div class="container">
          <div class="trusted-header">
            <p class="trusted-text">{{ t('used_by_professionals_at') }}</p>
          </div>
          
          <div class="companies-wrapper">
            <div class="companies-track">
              <!-- First set of companies -->
              <div class="company-item">
                <div class="company-logo">
                  <i class="fab fa-google"></i>
                </div>
                <span class="company-name">Google</span>
              </div>
              
              <div class="company-item">
                <div class="company-logo">
                  <i class="fab fa-microsoft"></i>
                </div>
                <span class="company-name">Microsoft</span>
              </div>
              
              <div class="company-item">
                <div class="company-logo">
                  <i class="fab fa-amazon"></i>
                </div>
                <span class="company-name">Amazon</span>
              </div>
              
              <div class="company-item">
                <div class="company-logo">
                  <i class="fab fa-meta"></i>
                </div>
                <span class="company-name">Meta</span>
              </div>
              
              <div class="company-item">
                <div class="company-logo">
                  <i class="fab fa-apple"></i>
                </div>
                <span class="company-name">Apple</span>
              </div>
              
              <div class="company-item">
                <div class="company-logo">
                  <i class="fab fa-netflix"></i>
                </div>
                <span class="company-name">Netflix</span>
              </div>
              
              <!-- Duplicate set for seamless loop -->
              <div class="company-item">
                <div class="company-logo">
                  <i class="fab fa-google"></i>
                </div>
                <span class="company-name">Google</span>
              </div>
              
              <div class="company-item">
                <div class="company-logo">
                  <i class="fab fa-microsoft"></i>
                </div>
                <span class="company-name">Microsoft</span>
              </div>
              
              <div class="company-item">
                <div class="company-logo">
                  <i class="fab fa-amazon"></i>
                </div>
                <span class="company-name">Amazon</span>
              </div>
              
              <div class="company-item">
                <div class="company-logo">
                  <i class="fab fa-meta"></i>
                </div>
                <span class="company-name">Meta</span>
              </div>
              
              <div class="company-item">
                <div class="company-logo">
                  <i class="fab fa-apple"></i>
                </div>
                <span class="company-name">Apple</span>
              </div>
              
              <div class="company-item">
                <div class="company-logo">
                  <i class="fab fa-netflix"></i>
                </div>
                <span class="company-name">Netflix</span>
              </div>
            </div>
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
            <div class="template-card" @click="selectTemplate('classic')" @mouseenter="startPreview('classic')" @mouseleave="stopPreview('classic')">
              <div class="template-preview">
                <div class="cv-preview classic-preview" :class="{ 'animating': previewing === 'classic' }">
                  <div class="cv-header">
                    <div class="cv-name" :class="{ 'typing': previewing === 'classic' }">
                      <span v-if="previewing === 'classic'">{{ previewName }}</span>
                      <span v-else>Sarah Johnson</span>
                    </div>
                    <div class="cv-title" :class="{ 'typing': previewing === 'classic' }">
                      <span v-if="previewing === 'classic'">{{ previewTitle }}</span>
                      <span v-else>Senior Developer</span>
                    </div>
                    <div class="cv-contact">
                      <span class="contact-item">
                        <i class="fas fa-envelope"></i>
                        sarah.johnson@email.com
                      </span>
                      <span class="contact-item">
                        <i class="fas fa-phone"></i>
                        +1 (555) 123-4567
                      </span>
                    </div>
                  </div>
                  <div class="cv-content">
                    <div class="cv-section" :class="{ 'filling': previewing === 'classic' }">
                      <h3>Experience</h3>
                      <div class="content-lines">
                        <div class="line" :style="{ animationDelay: '0.5s' }"></div>
                        <div class="line short" :style="{ animationDelay: '0.7s' }"></div>
                        <div class="line" :style="{ animationDelay: '0.9s' }"></div>
                      </div>
                    </div>
                    <div class="cv-section" :class="{ 'filling': previewing === 'classic' }">
                      <h3>Skills</h3>
                      <div class="skills-container">
                        <span class="skill-tag">JavaScript</span>
                        <span class="skill-tag">React</span>
                        <span class="skill-tag">Node.js</span>
                        <span class="skill-tag">Python</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="template-info">
                <h3>{{ t('classic') }}</h3>
                <p>{{ t('elegant_professional') }}</p>
                <span class="template-badge">{{ t('most_used') }}</span>
              </div>
            </div>
            
            <div class="template-card featured" @click="selectTemplate('modern')" @mouseenter="startPreview('modern')" @mouseleave="stopPreview('modern')">
              <div class="template-preview">
                <div class="cv-preview modern-preview" :class="{ 'animating': previewing === 'modern' }">
                  <div class="modern-layout">
                    <div class="cv-sidebar">
                      <div class="profile-section">
                        <div class="cv-photo" :class="{ 'appearing': previewing === 'modern' }"></div>
                        <div class="cv-name modern-name" :class="{ 'typing': previewing === 'modern' }">
                          <span v-if="previewing === 'modern'">{{ previewName }}</span>
                          <span v-else>Sarah Johnson</span>
                        </div>
                        <div class="cv-title modern-title" :class="{ 'typing': previewing === 'modern' }">
                          <span v-if="previewing === 'modern'">{{ previewTitle }}</span>
                          <span v-else>Senior Developer</span>
                        </div>
                      </div>
                      
                      <div class="sidebar-section" :class="{ 'filling': previewing === 'modern' }">
                        <h4>Skills</h4>
                        <div class="skill-bars">
                          <div class="skill-bar" :style="{ animationDelay: '0.8s' }">
                            <span class="skill-name">JavaScript</span>
                            <div class="bar">
                              <div class="progress" style="width: 90%"></div>
                            </div>
                          </div>
                          <div class="skill-bar" :style="{ animationDelay: '1.0s' }">
                            <span class="skill-name">React</span>
                            <div class="bar">
                              <div class="progress" style="width: 85%"></div>
                            </div>
                          </div>
                          <div class="skill-bar" :style="{ animationDelay: '1.2s' }">
                            <span class="skill-name">Node.js</span>
                            <div class="bar">
                              <div class="progress" style="width: 80%"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="cv-main">
                      <div class="cv-section" :class="{ 'filling': previewing === 'modern' }">
                        <h3>Experience</h3>
                        <div class="content-lines">
                          <div class="line" :style="{ animationDelay: '0.6s' }"></div>
                          <div class="line short" :style="{ animationDelay: '0.8s' }"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="template-info">
                <h3>{{ t('modern') }}</h3>
                <p>{{ t('minimal_clean') }}</p>
                <span class="template-badge premium">{{ t('premium') }}</span>
              </div>
            </div>
            
            <div class="template-card" @click="selectTemplate('professional')" @mouseenter="startPreview('professional')" @mouseleave="stopPreview('professional')">
              <div class="template-preview">
                <div class="cv-preview professional-preview" :class="{ 'animating': previewing === 'professional' }">
                  <div class="cv-header centered">
                    <div class="cv-name" :class="{ 'typing': previewing === 'professional' }">
                      <span v-if="previewing === 'professional'">{{ previewName }}</span>
                      <span v-else>Sarah Johnson</span>
                    </div>
                    <div class="cv-title" :class="{ 'typing': previewing === 'professional' }">
                      <span v-if="previewing === 'professional'">{{ previewTitle }}</span>
                      <span v-else>Senior Developer</span>
                    </div>
                    <div class="professional-divider" :class="{ 'expanding': previewing === 'professional' }"></div>
                  </div>
                  <div class="cv-content two-column">
                    <div class="cv-section" :class="{ 'filling': previewing === 'professional' }">
                      <div class="content-lines">
                        <div class="line" :style="{ animationDelay: '0.7s' }"></div>
                        <div class="line short" :style="{ animationDelay: '0.9s' }"></div>
                      </div>
                    </div>
                    <div class="cv-section" :class="{ 'filling': previewing === 'professional' }">
                      <div class="content-lines">
                        <div class="line" :style="{ animationDelay: '1.1s' }"></div>
                        <div class="line short" :style="{ animationDelay: '1.3s' }"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="template-info">
                <h3>{{ t('professional') }}</h3>
                <p>{{ t('serious_detailed') }}</p>
                <span class="template-badge new">{{ t('new') }}</span>
              </div>
            </div>
            
            <div class="template-card" @click="selectTemplate('creative')" @mouseenter="startPreview('creative')" @mouseleave="stopPreview('creative')">
              <div class="template-preview">
                <div class="cv-preview creative-preview" :class="{ 'animating': previewing === 'creative' }">
                  <div class="creative-shapes" :class="{ 'floating': previewing === 'creative' }">
                    <div class="shape shape-1"></div>
                    <div class="shape shape-2"></div>
                  </div>
                  <div class="cv-content creative-layout">
                    <div class="cv-name creative-name" :class="{ 'typing': previewing === 'creative' }">
                      <span v-if="previewing === 'creative'">{{ previewName }}</span>
                      <span v-else>Sarah Johnson</span>
                    </div>
                    <div class="cv-title creative-title" :class="{ 'typing': previewing === 'creative' }">
                      <span v-if="previewing === 'creative'">{{ previewTitle }}</span>
                      <span v-else>Senior Developer</span>
                    </div>
                    <div class="creative-grid">
                      <div class="cv-section" :class="{ 'filling': previewing === 'creative' }">
                        <div class="content-lines">
                          <div class="line" :style="{ animationDelay: '0.5s' }"></div>
                          <div class="line short" :style="{ animationDelay: '0.7s' }"></div>
                        </div>
                      </div>
                      <div class="creative-visual" :class="{ 'animating': previewing === 'creative' }">
                        <div class="visual-element" :style="{ animationDelay: '0.9s' }"></div>
                        <div class="visual-element small" :style="{ animationDelay: '1.1s' }"></div>
                      </div>
                    </div>
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
      <section class="process-section" ref="processSection">
        <div class="container">
          <div class="section-header">
            <span class="section-badge">{{ t('process') }}</span>
            <h2 class="section-title">{{ t('how_it_works') }}</h2>
            <p class="section-subtitle">Watch your professional CV come to life in 4 simple steps</p>
          </div>
          
          <div class="process-demo">
            <!-- Demo CV Preview -->
            <div class="demo-cv-container">
              <div class="demo-cv" :class="{ 'building': currentStep > 0 }">
                <!-- CV Header -->
                <div class="demo-cv-header" :class="{ 'filled': currentStep >= 3 }">
                  <div class="demo-name">
                    <span v-if="currentStep >= 3">{{ demoName }}</span>
                    <span v-else class="placeholder-text">Your Name</span>
                  </div>
                  <div class="demo-title">
                    <span v-if="currentStep >= 3">{{ demoTitle }}</span>
                    <span v-else class="placeholder-text">Your Title</span>
                  </div>
                </div>
                
                <!-- CV Sections -->
                <div class="demo-cv-sections">
                  <div class="demo-section" :class="{ 'filling': currentStep >= 3 }">
                    <div class="section-header">Experience</div>
                    <div class="section-content">
                      <div class="demo-line" :style="{ animationDelay: '0.5s' }"></div>
                      <div class="demo-line short" :style="{ animationDelay: '0.7s' }"></div>
                      <div class="demo-line" :style="{ animationDelay: '0.9s' }"></div>
                    </div>
                  </div>
                  
                  <div class="demo-section" :class="{ 'filling': currentStep >= 3 }">
                    <div class="section-header">Education</div>
                    <div class="section-content">
                      <div class="demo-line" :style="{ animationDelay: '1.1s' }"></div>
                      <div class="demo-line short" :style="{ animationDelay: '1.3s' }"></div>
                    </div>
                  </div>
                  
                  <div class="demo-section" :class="{ 'filling': currentStep >= 3 }">
                    <div class="section-header">Skills</div>
                    <div class="section-content">
                      <div class="skill-item" v-for="skill in demoSkills" :key="skill" :style="{ animationDelay: '1.5s' }">
                        {{ skill }}
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Template Selection Overlay -->
                <div v-if="currentStep === 2" class="template-selector-overlay">
                  <div class="template-options">
                    <div class="template-option" v-for="template in ['Classic', 'Modern', 'Creative']" :key="template" 
                         :class="{ 'selected': selectedDemoTemplate === template }"
                         @click="selectDemoTemplate(template)">
                      {{ template }}
                    </div>
                  </div>
                </div>
                
                <!-- Download Animation -->
                <div v-if="currentStep === 4" class="download-overlay">
                  <div class="download-animation">
                    <i class="fas fa-file-pdf"></i>
                    <div class="download-progress">
                      <div class="progress-bar"></div>
                    </div>
                    <span class="download-text">Generating PDF...</span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Interactive Steps -->
            <div class="process-steps">
              <div class="step" 
                   v-for="(step, index) in processSteps" 
                   :key="step.id"
                   :class="{ 'active': currentStep === index + 1, 'completed': currentStep > index + 1 }"
                   @click="setCurrentStep(index + 1)"
                   @mouseenter="previewStep(index + 1)"
                   @mouseleave="resetPreview">
                <div class="step-number">
                  <span v-if="currentStep > index + 1">
                    <i class="fas fa-check"></i>
                  </span>
                  <span v-else>{{ index + 1 }}</span>
                </div>
                
                <div class="step-content">
                  <div class="step-icon" :class="step.iconClass">
                    <i :class="step.icon"></i>
                  </div>
                  <h3>{{ step.title }}</h3>
                  <p>{{ step.description }}</p>
                  
                  <!-- Interactive Demo Elements -->
                  <div v-if="index === 0 && currentStep === 1" class="step-demo signup-demo">
                    <div class="demo-form">
                      <div class="demo-input" :class="{ 'typing': step1Animation }">
                        <span v-if="step1Animation">{{ demoEmail }}</span>
                        <span v-else class="placeholder">Enter your email</span>
                      </div>
                      <div class="demo-button" :class="{ 'ready': step1Animation }">Sign Up</div>
                    </div>
                  </div>
                  
                  <div v-if="index === 1 && currentStep === 2" class="step-demo template-demo">
                    <div class="demo-templates">
                      <div class="demo-template-card" v-for="template in ['Classic', 'Modern', 'Creative']" :key="template"
                           :class="{ 'highlighted': template === selectedDemoTemplate }">
                        {{ template }}
                      </div>
                    </div>
                  </div>
                  
                  <div v-if="index === 2 && currentStep === 3" class="step-demo editing-demo">
                    <div class="demo-editor">
                      <div class="editor-field" :class="{ 'typing': step3Animation }">
                        <label>Name:</label>
                        <span v-if="step3Animation" class="typing-text">{{ demoName }}</span>
                      </div>
                      <div class="editor-field" :class="{ 'typing': step3Animation }" style="animation-delay: 0.5s">
                        <label>Title:</label>
                        <span v-if="step3Animation" class="typing-text">{{ demoTitle }}</span>
                      </div>
                    </div>
                  </div>
                  
                  <div v-if="index === 3 && currentStep === 4" class="step-demo download-demo">
                    <div class="demo-download">
                      <i class="fas fa-file-pdf"></i>
                      <span class="download-status">Ready to download!</span>
                      <div class="download-btn">
                        <i class="fas fa-download"></i>
                        Download PDF
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="step-connector" v-if="index < 3"></div>
              </div>
            </div>
          </div>
          
          <div class="process-controls">
            <button class="control-btn" :class="{ 'disabled': currentStep === 1 }" @click="prevStep">
              <i class="fas fa-chevron-left"></i>
              Previous
            </button>
            <div class="step-indicators">
              <div class="indicator" v-for="n in 4" :key="n" 
                   :class="{ 'active': currentStep === n, 'completed': currentStep > n }"
                   @click="setCurrentStep(n)"></div>
            </div>
            <button class="control-btn" :class="{ 'disabled': currentStep === 4 }" @click="nextStep">
              Next
              <i class="fas fa-chevron-right"></i>
            </button>
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
                  <img 
                :src="testimonial.image" 
                :alt="testimonial.name"
                width="60" 
                height="60"
                loading="lazy"
                class="homepage-img"
              />
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
    const previewing = ref(null)
    const previewName = ref('')
    const previewTitle = ref('')
    
    // Process demo state
    const currentStep = ref(1)
    const selectedDemoTemplate = ref('Modern')
    const step1Animation = ref(false)
    const step3Animation = ref(false)
    const processSection = ref(null)
    
    // Hero playground state
    const heroName = ref('')
    const heroTitle = ref('')
    const heroActiveTemplate = ref('classic')
    const isUpdating = ref(false)
    const animatedStats = ref({
      cvs: false,
      templates: false,
      satisfaction: false
    })
    
    const heroTemplates = ref([
      { id: 'classic', name: 'Classic', icon: 'fas fa-file-alt' },
      { id: 'modern', name: 'Modern', icon: 'fas fa-laptop-code' },
      { id: 'creative', name: 'Creative', icon: 'fas fa-palette' }
    ])
    
    const heroSkills = ref(['JavaScript', 'React', 'Node.js', 'Python', 'Design', 'Leadership'])
    
    const typewriterPhrases = ref([
      'that gets you hired',
      'that stands out', 
      'that opens doors'
    ])
    
    const previewData = {
      names: ['Alex Johnson', 'Sarah Chen', 'Marcus Williams', 'Elena Rodriguez'],
      titles: ['Full Stack Developer', 'UI/UX Designer', 'Data Scientist', 'Product Manager']
    }
    
    // Demo data
    const demoName = ref('Sarah Johnson')
    const demoTitle = ref('Full Stack Developer')
    const demoEmail = ref('sarah.johnson@email.com')
    const demoSkills = ref(['React', 'Node.js', 'Python', 'AWS'])
    
    const processSteps = ref([
      {
        id: 1,
        title: 'Create Account',
        description: 'Sign up in seconds with your email',
        icon: 'fas fa-user-plus',
        iconClass: 'icon-primary'
      },
      {
        id: 2,
        title: 'Choose Template',
        description: 'Pick from professional templates',
        icon: 'fas fa-palette',
        iconClass: 'icon-secondary'
      },
      {
        id: 3,
        title: 'Fill Your Details',
        description: 'Add your information with our smart editor',
        icon: 'fas fa-edit',
        iconClass: 'icon-accent'
      },
      {
        id: 4,
        title: 'Download CV',
        description: 'Get your professional PDF instantly',
        icon: 'fas fa-download',
        iconClass: 'icon-success'
      }
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

    const startPreview = (template) => {
      previewing.value = template
      // Randomize preview content
      const randomName = previewData.names[Math.floor(Math.random() * previewData.names.length)]
      const randomTitle = previewData.titles[Math.floor(Math.random() * previewData.titles.length)]
      
      // Simulate typing effect
      previewName.value = ''
      previewTitle.value = ''
      
      let nameIndex = 0
      let titleIndex = 0
      
      const typePreviewName = () => {
        if (nameIndex < randomName.length && previewing.value === template) {
          previewName.value += randomName.charAt(nameIndex)
          nameIndex++
          setTimeout(typePreviewName, 50)
        } else if (previewing.value === template) {
          setTimeout(typePreviewTitle, 200)
        }
      }
      
      const typePreviewTitle = () => {
        if (titleIndex < randomTitle.length && previewing.value === template) {
          previewTitle.value += randomTitle.charAt(titleIndex)
          titleIndex++
          setTimeout(typePreviewTitle, 60)
        }
      }
      
      setTimeout(typePreviewName, 300)
    }

    const stopPreview = (template) => {
      if (previewing.value === template) {
        previewing.value = null
        previewName.value = ''
        previewTitle.value = ''
      }
    }

    // Process demo methods
    const setCurrentStep = (step) => {
      currentStep.value = step
      triggerStepAnimations(step)
    }

    const nextStep = () => {
      if (currentStep.value < 4) {
        currentStep.value++
        triggerStepAnimations(currentStep.value)
      }
    }

    const prevStep = () => {
      if (currentStep.value > 1) {
        currentStep.value--
        triggerStepAnimations(currentStep.value)
      }
    }

    const previewStep = (step) => {
      // Optional: add preview logic for hover states
    }

    const resetPreview = () => {
      // Optional: reset preview states
    }

    const selectDemoTemplate = (template) => {
      selectedDemoTemplate.value = template
    }

    const triggerStepAnimations = (step) => {
      // Reset animations
      step1Animation.value = false
      step3Animation.value = false
      
      // Trigger step-specific animations
      setTimeout(() => {
        if (step === 1) {
          step1Animation.value = true
        } else if (step === 3) {
          step3Animation.value = true
        }
      }, 300)
    }

    // Hero playground methods
    const updateHeroCV = () => {
      isUpdating.value = true
      setTimeout(() => {
        isUpdating.value = false
      }, 300)
    }

    const switchHeroTemplate = (templateId) => {
      heroActiveTemplate.value = templateId
      isUpdating.value = true
      setTimeout(() => {
        isUpdating.value = false
      }, 500)
    }

    const animateStat = (statType) => {
      animatedStats.value[statType] = true
      setTimeout(() => {
        animatedStats.value[statType] = false
      }, 1000)
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
      previewing,
      previewName,
      previewTitle,
      currentStep,
      selectedDemoTemplate,
      step1Animation,
      step3Animation,
      processSection,
      demoName,
      demoTitle,
      demoEmail,
      demoSkills,
      processSteps,
      heroName,
      heroTitle,
      heroActiveTemplate,
      isUpdating,
      animatedStats,
      heroTemplates,
      heroSkills,
      stats,
      testimonialData,
      selectTemplate,
      startPreview,
      stopPreview,
      setCurrentStep,
      nextStep,
      prevStep,
      previewStep,
      resetPreview,
      selectDemoTemplate,
      triggerStepAnimations,
      updateHeroCV,
      switchHeroTemplate,
      animateStat,
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
  --radius-full: 50%;
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
  background: linear-gradient(135deg, #fafbfc 0%, #f8fafc 100%);
  position: relative;
  overflow: hidden;
}

.hero-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(91, 33, 182, 0.2), transparent);
}

.hero-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 80px;
  align-items: center;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Left Side */
.hero-left {
  text-align: left;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  padding: 8px 16px;
  border-radius: 100px;
  font-size: 14px;
  font-weight: 600;
  color: var(--primary);
  margin-bottom: 32px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.hero-badge i {
  color: var(--primary);
}

.hero-title {
  font-size: clamp(40px, 6vw, 64px);
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
}

/* Hero Playground */
.hero-playground {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 32px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 16px 32px rgba(0, 0, 0, 0.1);
  margin-bottom: 40px;
  position: relative;
  overflow: hidden;
}

.hero-playground::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: var(--gradient-primary);
  border-radius: 20px 20px 0 0;
}

.playground-title {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 18px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 24px;
}

.playground-title i {
  color: var(--primary);
  font-size: 20px;
}

.playground-inputs {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.input-group label {
  font-size: 14px;
  font-weight: 600;
  color: var(--text);
}

.hero-input {
  padding: 16px 20px;
  border: 2px solid rgba(255, 255, 255, 0.5);
  border-radius: 12px;
  font-size: 16px;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  color: var(--text);
  transition: all 0.3s ease;
  font-family: inherit;
}

.hero-input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(91, 33, 182, 0.15);
  background: white;
}

.hero-input::placeholder {
  color: var(--text-muted);
}

.template-switcher {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.template-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  background: rgba(255, 255, 255, 0.7);
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  color: var(--text);
  cursor: pointer;
  transition: all 0.3s ease;
}

.template-btn:hover {
  background: rgba(255, 255, 255, 0.9);
  border-color: rgba(91, 33, 182, 0.3);
  transform: translateY(-2px);
}

.template-btn.active {
  background: var(--primary);
  color: white;
  border-color: var(--primary);
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(91, 33, 182, 0.3);
}

.template-btn i {
  font-size: 16px;
}

.hero-actions {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.btn-primary {
  background: var(--primary);
  color: white;
  padding: 18px 32px;
  border-radius: 12px;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 12px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: none;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 16px rgba(91, 33, 182, 0.3);
}

.btn-primary:hover {
  background: var(--primary-light);
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(91, 33, 182, 0.4);
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

.btn-secondary {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  color: var(--text);
  padding: 18px 32px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 12px;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 12px;
  transition: all 0.3s ease;
}

.btn-secondary:hover {
  background: white;
  border-color: var(--primary);
  color: var(--primary);
  transform: translateY(-2px);
}

/* Right Side */
.hero-right {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.hero-cv-container {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(248, 250, 252, 0.8));
  backdrop-filter: blur(20px);
  border-radius: 24px;
  padding: 32px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 24px 48px rgba(0, 0, 0, 0.1);
  position: relative;
  overflow: hidden;
}

.hero-cv-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--gradient-primary);
  border-radius: 24px 24px 0 0;
}

.cv-preview-badge {
  position: absolute;
  top: 20px;
  right: 20px;
  display: flex;
  align-items: center;
  gap: 6px;
  background: rgba(16, 185, 129, 0.9);
  color: white;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  z-index: 10;
}

.cv-preview-badge i {
  animation: pulse 2s infinite;
}

.hero-cv-preview {
  width: 100%;
  min-height: 400px;
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
}

.hero-cv-preview.updating {
  transform: scale(1.02);
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
}

/* CV Template Styles */
.cv-template {
  padding: 24px;
  height: 100%;
  font-family: 'Inter', sans-serif;
  font-size: 13px;
  line-height: 1.4;
}

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

/* Skills Container for Homepage */
.skills-container {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-top: 0.5rem;
}

.skill-tag {
  background: rgba(91, 33, 182, 0.1);
  color: var(--primary);
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 10px;
  font-weight: 600;
  opacity: 0;
  transform: translateY(10px);
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.cv-section.filled .skill-tag {
  opacity: 1;
  transform: translateY(0);
}

.skill-tag:nth-child(1) { transition-delay: 0.4s; }
.skill-tag:nth-child(2) { transition-delay: 0.5s; }
.skill-tag:nth-child(3) { transition-delay: 0.6s; }
.skill-tag:nth-child(4) { transition-delay: 0.7s; }

/* Classic Template */
.classic-template .cv-header {
  border-bottom: 2px solid #e2e8f0;
  padding-bottom: 16px;
  margin-bottom: 20px;
  transition: all 0.6s ease;
}

.classic-template .cv-name {
  font-size: 24px;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 8px;
  transition: all 0.6s ease;
}

.classic-template .cv-title {
  font-size: 16px;
  color: #4a5568;
  margin-bottom: 12px;
  transition: all 0.6s ease;
}

.classic-template .cv-name.typing,
.classic-template .cv-title.typing {
  color: var(--primary);
  position: relative;
}

.classic-template .cv-name.typing::after,
.classic-template .cv-title.typing::after {
  content: '|';
  animation: blink 1s infinite;
  margin-left: 2px;
}

.cv-contact {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: #64748b;
}

.contact-item i {
  width: 14px;
  color: var(--primary);
}

.cv-sections {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.cv-section h3 {
  font-size: 14px;
  font-weight: 700;
  color: var(--primary);
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 12px;
  opacity: 0;
  transform: translateX(-20px);
  transition: all 0.6s ease;
}

.cv-section.filled h3 {
  opacity: 1;
  transform: translateX(0);
}

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

.cv-section.filled .cv-line {
  opacity: 1;
  transform: scaleX(1);
  background: linear-gradient(90deg, var(--primary), var(--primary-light));
}

.cv-line.main-line {
  width: 100%;
  animation-delay: 0.3s;
}

.cv-line.short-line {
  width: 65%;
  animation-delay: 0.5s;
}

.cv-line.medium-line {
  width: 80%;
  animation-delay: 0.7s;
}

.skills-container {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.skill-tag {
  background: rgba(91, 33, 182, 0.1);
  color: var(--primary);
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  opacity: 0;
  transform: translateY(20px);
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.cv-section.filled .skill-tag {
  opacity: 1;
  transform: translateY(0);
}

.skill-tag:nth-child(1) { transition-delay: 0.4s; }
.skill-tag:nth-child(2) { transition-delay: 0.5s; }
.skill-tag:nth-child(3) { transition-delay: 0.6s; }
.skill-tag:nth-child(4) { transition-delay: 0.7s; }

/* Modern Template */
.modern-layout {
  display: flex;
  height: 100%;
}

.modern-sidebar {
  width: 35%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
  color: white;
}

.profile-section {
  text-align: center;
  margin-bottom: 24px;
}

.profile-circle {
  width: 60px;
  height: 60px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  margin: 0 auto 16px;
  animation: profileGlow 3s ease-in-out infinite;
}

@keyframes profileGlow {
  0%, 100% { box-shadow: 0 0 20px rgba(255, 255, 255, 0.3); }
  50% { box-shadow: 0 0 30px rgba(255, 255, 255, 0.5); }
}

.modern-sidebar .cv-name {
  font-size: 16px;
  font-weight: 700;
  margin-bottom: 6px;
  text-align: center;
}

.modern-sidebar .cv-title {
  font-size: 11px;
  opacity: 0.9;
  text-align: center;
  margin-bottom: 16px;
}

.profile-section {
  text-align: center;
  margin-bottom: 20px;
}

.sidebar-section h4 {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 12px;
  opacity: 0.9;
  color: rgba(255, 255, 255, 0.9);
}

.skill-name {
  font-size: 9px;
  font-weight: 500;
  margin-bottom: 3px;
  display: block;
  color: rgba(255, 255, 255, 0.9);
}

.skill-bars {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.skill-bar {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.bar {
  height: 4px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 2px;
  overflow: hidden;
  margin-bottom: 2px;
}

.progress {
  height: 100%;
  background: linear-gradient(90deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.8));
  border-radius: 2px;
  transition: width 1s ease-out 0.5s;
  position: relative;
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

.modern-main {
  flex: 1;
  padding: 20px;
  background: white;
}

/* Creative Template */
.creative-template {
  position: relative;
  background: linear-gradient(135deg, #ff6b6b10, #4ecdc410);
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
  animation: float 6s ease-in-out infinite;
}

.shape-1 {
  width: 40px;
  height: 40px;
  background: #ff6b6b;
  border-radius: 50%;
  top: 20px;
  right: 30px;
  animation-delay: 0s;
}

.shape-2 {
  width: 25px;
  height: 25px;
  background: #4ecdc4;
  transform: rotate(45deg);
  bottom: 40px;
  left: 30px;
  animation-delay: 2s;
}

.shape-3 {
  width: 30px;
  height: 30px;
  background: #45b7d1;
  border-radius: 20%;
  top: 50%;
  right: 20px;
  animation-delay: 4s;
}

@keyframes float {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  33% { transform: translateY(-15px) rotate(120deg); }
  66% { transform: translateY(10px) rotate(240deg); }
}

.creative-content {
  position: relative;
  z-index: 2;
}

.creative-name {
  font-size: 18px;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 6px;
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.creative-title {
  font-size: 12px;
  color: #ff6b6b;
  font-weight: 600;
  margin-bottom: 16px;
}

.creative-grid {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 20px;
  align-items: start;
}

.creative-visual {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.visual-element {
  width: 28px;
  height: 8px;
  background: linear-gradient(90deg, #ff6b6b, #4ecdc4);
  border-radius: 4px;
  opacity: 0;
  transform: translateX(20px);
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.visual-element::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  transition: left 0.6s ease;
}

.creative-visual.animating .visual-element::after {
  left: 100%;
}

.cv-section.filled .visual-element,
.creative-visual.animating .visual-element {
  opacity: 1;
  transform: translateX(0);
}

.visual-element:nth-child(1) { transition-delay: 0.8s; }
.visual-element:nth-child(2) { 
  transition-delay: 1.0s; 
  width: 20px;
}
.visual-element:nth-child(3) { transition-delay: 1.2s; }

/* Interactive Stats */
.hero-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.interactive-stat {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 20px;
  text-align: center;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 2px solid transparent;
  position: relative;
  overflow: hidden;
}

.interactive-stat::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: var(--gradient-primary);
  opacity: 0;
  transition: all 0.4s ease;
}

.interactive-stat:hover {
  transform: translateY(-8px);
  background: white;
  border-color: var(--primary);
  box-shadow: 0 16px 32px rgba(0, 0, 0, 0.1);
}

.interactive-stat:hover::before {
  opacity: 1;
}

.interactive-stat.animated {
  transform: translateY(-8px) scale(1.05);
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  color: white;
  box-shadow: 0 20px 40px rgba(91, 33, 182, 0.3);
}

.interactive-stat.animated::before {
  opacity: 1;
  background: white;
}

.stat-icon {
  width: 48px;
  height: 48px;
  background: var(--gradient-primary);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  color: white;
  font-size: 20px;
  transition: all 0.4s ease;
}

.interactive-stat.animated .stat-icon {
  background: white;
  color: var(--primary);
  transform: scale(1.1);
}

.stat-number {
  display: block;
  font-size: 28px;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 4px;
  transition: all 0.4s ease;
}

.interactive-stat.animated .stat-number {
  color: white;
}

.stat-label {
  font-size: 12px;
  color: var(--text-muted);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  transition: all 0.4s ease;
}

.interactive-stat.animated .stat-label {
  color: rgba(255, 255, 255, 0.9);
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
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 32px;
  margin-bottom: 48px;
}

.template-card {
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 20px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
}

.template-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 32px 64px rgba(0, 0, 0, 0.15);
  border-color: var(--primary-light);
}

.template-card.featured {
  border: 2px solid var(--primary);
  position: relative;
}

.template-card.featured::before {
  content: 'FEATURED';
  position: absolute;
  top: 16px;
  right: 16px;
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  color: white;
  padding: 6px 12px;
  font-size: 11px;
  font-weight: 700;
  border-radius: 20px;
  z-index: 10;
  letter-spacing: 0.5px;
  box-shadow: 0 4px 12px rgba(91, 33, 182, 0.3);
}

.template-preview {
  height: 280px;
  background: linear-gradient(135deg, #f7f8fa 0%, #f1f3f5 100%);
  position: relative;
  overflow: hidden;
  padding: 20px;
}

/* Enhanced CV Preview Animations */
.cv-preview {
  width: 100%;
  height: 100%;
  background: white;
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  position: relative;
  overflow: hidden;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 11px;
  line-height: 1.3;
}

.cv-preview.animating {
  transform: scale(1.03);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
}

.cv-preview::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--primary), var(--secondary));
  opacity: 0;
  transition: all 0.4s ease;
}

.cv-preview.animating::before {
  opacity: 1;
}

/* Enhanced Classic Template */
.classic-preview .cv-header {
  border-bottom: 2px solid #e2e8f0;
  padding-bottom: 12px;
  margin-bottom: 16px;
  transition: all 0.6s ease;
}

.classic-preview .cv-name {
  font-size: 16px;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 4px;
}

.classic-preview .cv-title {
  font-size: 12px;
  color: #4a5568;
  margin-bottom: 8px;
}

.classic-preview .cv-contact {
  display: flex;
  flex-direction: column;
  gap: 3px;
  margin-top: 8px;
}

.classic-preview .contact-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 9px;
  color: var(--text-muted);
}

.classic-preview .contact-item i {
  width: 10px;
  color: var(--primary);
  font-size: 8px;
}

.classic-preview h3 {
  font-size: 10px;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  opacity: 0;
  transform: translateX(-10px);
  transition: all 0.6s ease;
}

.classic-preview .cv-section.filling h3 {
  opacity: 1;
  transform: translateX(0);
}

.classic-preview .skills-container {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  margin-top: 6px;
}

.classic-preview .skill-tag {
  background: rgba(91, 33, 182, 0.1);
  color: var(--primary);
  padding: 3px 6px;
  border-radius: 8px;
  font-size: 8px;
  font-weight: 600;
  opacity: 0;
  transform: translateY(8px);
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.classic-preview .cv-section.filling .skill-tag {
  opacity: 1;
  transform: translateY(0);
}

.classic-preview .skill-tag:nth-child(1) { transition-delay: 0.4s; }
.classic-preview .skill-tag:nth-child(2) { transition-delay: 0.5s; }
.classic-preview .skill-tag:nth-child(3) { transition-delay: 0.6s; }
.classic-preview .skill-tag:nth-child(4) { transition-delay: 0.7s; }

/* Modern Template */
.modern-preview {
  display: flex;
  gap: 12px;
}

.modern-preview .cv-sidebar {
  width: 35%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 8px;
  padding: 12px;
  color: white;
}

.modern-preview .cv-photo {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  margin-bottom: 12px;
  transition: all 0.3s ease;
  opacity: 0;
}

.modern-preview .cv-photo.appearing {
  opacity: 1;
  animation: photoAppear 0.6s ease-out;
}

@keyframes photoAppear {
  0% { transform: scale(0) rotate(180deg); opacity: 0; }
  100% { transform: scale(1) rotate(0deg); opacity: 1; }
}

.modern-preview .sidebar-section .section-title {
  font-size: 10px;
  font-weight: 600;
  margin-bottom: 6px;
  opacity: 0.9;
}

.modern-preview .skill-bars {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.modern-preview .skill-bar {
  height: 4px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 2px;
  position: relative;
  overflow: hidden;
}

.modern-preview.animating .skill-bar::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  background: white;
  border-radius: 2px;
  animation: skillFill 1s ease-out forwards;
}

.modern-preview.animating .skill-bar:nth-child(1)::after { width: 90%; }
.modern-preview.animating .skill-bar:nth-child(2)::after { width: 75%; }
.modern-preview.animating .skill-bar:nth-child(3)::after { width: 85%; }

@keyframes skillFill {
  0% { width: 0; }
}

.modern-preview .cv-main {
  flex: 1;
  padding: 8px;
}

.modern-preview .modern-name {
  font-size: 16px;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 4px;
}

.modern-preview .modern-title {
  font-size: 12px;
  color: #667eea;
  font-weight: 600;
  margin-bottom: 12px;
}

/* Professional Template */
.professional-preview .cv-header.centered {
  text-align: center;
  padding-bottom: 12px;
  margin-bottom: 16px;
}

.professional-preview .cv-name {
  font-size: 16px;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 4px;
}

.professional-preview .cv-title {
  font-size: 12px;
  color: #4a5568;
  margin-bottom: 8px;
}

.professional-preview .professional-divider {
  height: 2px;
  background: linear-gradient(90deg, transparent, #cbd5e0, transparent);
  margin: 8px 0;
  transform: scaleX(0);
  transition: transform 0.6s ease;
}

.professional-preview .professional-divider.expanding {
  transform: scaleX(1);
}

.professional-preview .cv-content.two-column {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

/* Creative Template */
.creative-preview {
  position: relative;
  background: linear-gradient(135deg, #ff6b6b20, #4ecdc420);
}

.creative-preview .creative-shapes {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
}

.creative-preview .shape {
  position: absolute;
  opacity: 0.1;
  transition: all 0.6s ease;
}

.creative-preview .shape-1 {
  width: 30px;
  height: 30px;
  background: #ff6b6b;
  border-radius: 50%;
  top: 20px;
  right: 20px;
}

.creative-preview .shape-2 {
  width: 20px;
  height: 20px;
  background: #4ecdc4;
  transform: rotate(45deg);
  bottom: 20px;
  left: 20px;
}

.creative-preview .creative-shapes.floating .shape {
  opacity: 0.3;
  animation: float 3s ease-in-out infinite;
}

.creative-preview .creative-shapes.floating .shape-2 {
  animation-delay: 1.5s;
}

@keyframes float {
  0%, 100% { transform: translateY(0) rotate(45deg); }
  50% { transform: translateY(-10px) rotate(45deg); }
}

.creative-preview .cv-content.creative-layout {
  position: relative;
  z-index: 2;
}

.creative-preview .creative-name {
  font-size: 16px;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 4px;
}

.creative-preview .creative-title {
  font-size: 12px;
  color: #ff6b6b;
  font-weight: 600;
  margin-bottom: 12px;
}

.creative-preview .creative-grid {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 12px;
  align-items: start;
}

.creative-preview .creative-visual {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.creative-preview .visual-element {
  width: 24px;
  height: 8px;
  background: linear-gradient(90deg, #ff6b6b, #4ecdc4);
  border-radius: 4px;
  opacity: 0;
  transform: translateX(20px);
}

.creative-preview.animating .visual-element {
  animation: slideIn 0.6s ease-out forwards;
}

.creative-preview .visual-element.small {
  width: 16px;
}

@keyframes slideIn {
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Common Animation Classes */
.typing {
  position: relative;
}

.typing::after {
  content: '|';
  animation: blink 1s infinite;
  margin-left: 1px;
}

.content-lines {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.content-lines .line {
  height: 6px;
  background: #e2e8f0;
  border-radius: 3px;
  opacity: 0;
  transform: scaleX(0);
  transform-origin: left;
}

.cv-section.filling .line {
  animation: lineFill 0.8s ease-out forwards;
}

.content-lines .line.short {
  width: 60%;
}

@keyframes lineFill {
  to {
    opacity: 1;
    transform: scaleX(1);
  }
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
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  position: relative;
  overflow: hidden;
}

.process-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(91, 33, 182, 0.2), transparent);
}

.process-demo {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 80px;
  align-items: center;
  margin-bottom: 60px;
}

/* Demo CV Container */
.demo-cv-container {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(248, 250, 252, 0.8));
  backdrop-filter: blur(20px);
  border-radius: 24px;
  padding: 40px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  position: relative;
  overflow: hidden;
}

.demo-cv-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: var(--gradient-primary);
  opacity: 0;
  transition: all 0.6s ease;
}

.demo-cv-container:hover::before {
  opacity: 1;
}

.demo-cv {
  width: 100%;
  min-height: 400px;
  background: white;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.demo-cv.building {
  transform: scale(1.02);
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
}

/* Demo CV Header */
.demo-cv-header {
  border-bottom: 2px solid #e2e8f0;
  padding-bottom: 20px;
  margin-bottom: 24px;
  transition: all 0.6s ease;
}

.demo-cv-header.filled {
  border-color: var(--primary);
}

.demo-name {
  font-size: 24px;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 8px;
  transition: all 0.6s ease;
}

.demo-title {
  font-size: 16px;
  color: #4a5568;
  transition: all 0.6s ease;
}

.placeholder-text {
  color: #cbd5e0;
  font-style: italic;
}

/* Demo CV Sections */
.demo-cv-sections {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.demo-section {
  transition: all 0.6s ease;
}

.demo-section .section-header {
  font-size: 14px;
  font-weight: 700;
  color: var(--primary);
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 12px;
  opacity: 0;
  transform: translateX(-20px);
  transition: all 0.6s ease;
}

.demo-section.filling .section-header {
  opacity: 1;
  transform: translateX(0);
}

.demo-line {
  height: 8px;
  background: #e2e8f0;
  border-radius: 4px;
  margin-bottom: 8px;
  opacity: 0;
  transform: scaleX(0);
  transform-origin: left;
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.demo-section.filling .demo-line {
  opacity: 1;
  transform: scaleX(1);
  background: linear-gradient(90deg, var(--primary), var(--primary-light));
}

.demo-line.short {
  width: 65%;
}

.skill-item {
  display: inline-block;
  background: rgba(91, 33, 182, 0.1);
  color: var(--primary);
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  margin: 4px 8px 4px 0;
  opacity: 0;
  transform: translateY(20px);
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.demo-section.filling .skill-item {
  opacity: 1;
  transform: translateY(0);
}

/* Template Selector Overlay */
.template-selector-overlay {
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

.template-options {
  display: flex;
  gap: 20px;
  flex-direction: column;
  align-items: center;
}

.template-option {
  background: white;
  color: var(--primary);
  padding: 16px 32px;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  transform: translateY(20px);
  opacity: 0;
  animation: optionSlideIn 0.6s ease-out forwards;
}

.template-option:nth-child(1) { animation-delay: 0.1s; }
.template-option:nth-child(2) { animation-delay: 0.2s; }
.template-option:nth-child(3) { animation-delay: 0.3s; }

@keyframes optionSlideIn {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.template-option:hover {
  background: var(--primary-light);
  color: white;
  transform: translateY(-4px);
}

.template-option.selected {
  background: var(--primary);
  color: white;
  box-shadow: 0 8px 24px rgba(91, 33, 182, 0.4);
}

/* Download Overlay */
.download-overlay {
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
  animation: downloadFadeIn 0.6s ease-out;
}

@keyframes downloadFadeIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.download-animation {
  text-align: center;
  color: white;
}

.download-animation i {
  font-size: 48px;
  margin-bottom: 20px;
  animation: bounce 2s infinite;
}

@keyframes bounce {
  0%, 20%, 53%, 80%, 100% {
    transform: translate3d(0,0,0);
  }
  40%, 43% {
    transform: translate3d(0, -15px, 0);
  }
  70% {
    transform: translate3d(0, -7px, 0);
  }
  90% {
    transform: translate3d(0, -2px, 0);
  }
}

.download-progress {
  width: 200px;
  height: 4px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 2px;
  overflow: hidden;
  margin: 0 auto 16px;
}

.progress-bar {
  height: 100%;
  background: white;
  border-radius: 2px;
  animation: progressFill 2s ease-out;
}

@keyframes progressFill {
  from { width: 0%; }
  to { width: 100%; }
}

.download-text {
  font-weight: 600;
  font-size: 18px;
}

/* Process Steps */
.process-steps {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.step {
  display: flex;
  align-items: center;
  gap: 24px;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 24px;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 2px solid transparent;
  position: relative;
  overflow: hidden;
}

.step::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: var(--gradient-primary);
  opacity: 0;
  transition: all 0.4s ease;
}

.step:hover {
  transform: translateX(8px);
  background: rgba(255, 255, 255, 0.95);
  border-color: rgba(91, 33, 182, 0.2);
  box-shadow: 0 16px 32px rgba(0, 0, 0, 0.1);
}

.step:hover::before {
  opacity: 1;
}

.step.active {
  background: linear-gradient(135deg, rgba(91, 33, 182, 0.1), rgba(124, 58, 237, 0.05));
  border-color: var(--primary);
  transform: translateX(12px);
  box-shadow: 0 20px 40px rgba(91, 33, 182, 0.2);
}

.step.active::before {
  opacity: 1;
}

.step.completed {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(6, 182, 212, 0.05));
  border-color: var(--success);
}

.step.completed .step-number {
  background: var(--success);
}

.step-number {
  width: 56px;
  height: 56px;
  background: var(--primary);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 18px;
  flex-shrink: 0;
  transition: all 0.4s ease;
  box-shadow: 0 8px 16px rgba(91, 33, 182, 0.3);
}

.step.active .step-number {
  background: var(--primary-light);
  transform: scale(1.1);
  box-shadow: 0 12px 24px rgba(91, 33, 182, 0.4);
}

.step-content {
  flex: 1;
  text-align: left;
}

.step-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  margin-bottom: 16px;
  transition: all 0.4s ease;
}

.step-icon.icon-primary {
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  color: white;
}

.step-icon.icon-secondary {
  background: linear-gradient(135deg, var(--secondary), #38bdf8);
  color: white;
}

.step-icon.icon-accent {
  background: linear-gradient(135deg, var(--accent), #fbbf24);
  color: white;
}

.step-icon.icon-success {
  background: linear-gradient(135deg, var(--success), #34d399);
  color: white;
}

.step h3 {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 8px;
  color: var(--text);
  transition: all 0.4s ease;
}

.step.active h3 {
  color: var(--primary);
}

.step p {
  color: var(--text-muted);
  line-height: 1.6;
  margin-bottom: 16px;
}

.step-connector {
  position: absolute;
  right: -16px;
  top: 50%;
  transform: translateY(-50%);
  width: 2px;
  height: 40px;
  background: linear-gradient(180deg, var(--border), transparent);
}

/* Step Demo Elements */
.step-demo {
  margin-top: 16px;
  opacity: 0;
  transform: translateY(20px);
  animation: demoFadeIn 0.6s ease-out 0.3s forwards;
}

@keyframes demoFadeIn {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Signup Demo */
.demo-form {
  display: flex;
  gap: 12px;
  align-items: center;
}

.demo-input {
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  padding: 12px 16px;
  font-size: 14px;
  transition: all 0.4s ease;
  min-width: 200px;
}

.demo-input.typing {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(91, 33, 182, 0.15);
}

.demo-input .placeholder {
  color: #cbd5e0;
}

.demo-button {
  background: #e2e8f0;
  color: #64748b;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.4s ease;
}

.demo-button.ready {
  background: var(--primary);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(91, 33, 182, 0.3);
}

/* Template Demo */
.demo-templates {
  display: flex;
  gap: 12px;
}

.demo-template-card {
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  padding: 16px 20px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.demo-template-card.highlighted {
  border-color: var(--primary);
  background: var(--primary);
  color: white;
  transform: translateY(-2px);
}

/* Editing Demo */
.demo-editor {
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  padding: 16px;
}

.editor-field {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
}

.editor-field label {
  font-weight: 600;
  color: var(--text-muted);
  min-width: 60px;
}

.typing-text {
  color: var(--primary);
  position: relative;
}

.typing-text::after {
  content: '|';
  animation: blink 1s infinite;
  margin-left: 2px;
}

/* Download Demo */
.demo-download {
  display: flex;
  align-items: center;
  gap: 16px;
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(6, 182, 212, 0.1));
  border: 2px solid var(--success);
  border-radius: 12px;
  padding: 16px 20px;
}

.demo-download i {
  font-size: 24px;
  color: var(--success);
}

.download-status {
  font-weight: 600;
  color: var(--success);
  flex: 1;
}

.download-btn {
  background: var(--success);
  color: white;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  animation: pulse 2s infinite;
}

/* Process Controls */
.process-controls {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 32px;
  margin-top: 40px;
}

.control-btn {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 12px;
  padding: 12px 24px;
  color: var(--text);
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.control-btn:hover:not(.disabled) {
  background: white;
  border-color: var(--primary);
  color: var(--primary);
  transform: translateY(-2px);
}

.control-btn.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.step-indicators {
  display: flex;
  gap: 12px;
}

.indicator {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: rgba(91, 33, 182, 0.2);
  cursor: pointer;
  transition: all 0.3s ease;
}

.indicator.active {
  background: var(--primary);
  transform: scale(1.3);
}

.indicator.completed {
  background: var(--success);
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
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: var(--radius-full);
  display: block;
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
@media (max-width: 1024px) {
  .process-demo {
    grid-template-columns: 1fr;
    gap: 40px;
  }
  
  .demo-cv-container {
    order: 2;
    padding: 24px;
  }
  
  .demo-cv {
    min-height: 300px;
    padding: 24px;
  }
  
  .process-steps {
    order: 1;
  }
  
  .step {
    padding: 20px;
  }
  
  .step-number {
    width: 48px;
    height: 48px;
    font-size: 16px;
  }
}

@media (max-width: 1024px) {
  .hero-content {
    grid-template-columns: 1fr;
    gap: 60px;
  }
  
  .hero-left {
    text-align: center;
  }
  
  .hero-right {
    order: -1;
  }
  
  .hero-playground {
    padding: 24px;
  }
  
  .playground-inputs {
    gap: 16px;
  }
  
  .template-switcher {
    justify-content: center;
  }
  
  .hero-cv-container {
    padding: 24px;
  }
  
  .hero-cv-preview {
    min-height: 300px;
  }
}

@media (max-width: 768px) {
  .hero-section {
    padding: 80px 0 60px;
  }
  
  .hero-content {
    gap: 40px;
    padding: 0 16px;
  }
  
  .hero-title {
    font-size: clamp(32px, 8vw, 48px);
  }
  
  .hero-playground {
    padding: 20px;
  }
  
  .playground-title {
    font-size: 16px;
    text-align: center;
  }
  
  .template-switcher {
    flex-direction: column;
  }
  
  .template-btn {
    justify-content: center;
    padding: 14px 20px;
  }
  
  .hero-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .btn-primary,
  .btn-secondary {
    width: 100%;
    max-width: 280px;
    justify-content: center;
  }
  
  .hero-cv-container {
    padding: 16px;
  }
  
  .hero-cv-preview {
    min-height: 250px;
  }
  
  .cv-template {
    padding: 16px;
  }
  
  .classic-template .cv-name {
    font-size: 20px;
  }
  
  .classic-template .cv-title {
    font-size: 14px;
  }
  
  .modern-sidebar {
    padding: 16px;
  }
  
  .modern-main {
    padding: 16px;
  }
  
  .hero-stats {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .interactive-stat {
    padding: 16px;
  }
  
  .stat-icon {
    width: 40px;
    height: 40px;
    font-size: 18px;
  }
  
  .stat-number {
    font-size: 24px;
  }
  
  .hero-actions,
  .cta-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .process-section {
    padding: 80px 0;
  }
  
  .process-demo {
    gap: 32px;
  }
  
  .demo-cv-container {
    padding: 20px;
  }
  
  .demo-cv {
    min-height: 250px;
    padding: 20px;
  }
  
  .demo-name {
    font-size: 20px;
  }
  
  .demo-title {
    font-size: 14px;
  }
  
  .step {
    flex-direction: column;
    text-align: center;
    gap: 16px;
    padding: 20px;
  }
  
  .step-content {
    text-align: center;
  }
  
  .step-connector {
    display: none;
  }
  
  .process-controls {
    flex-direction: column;
    gap: 20px;
  }
  
  .control-btn {
    padding: 12px 20px;
    font-size: 14px;
  }
  
  .demo-form {
    flex-direction: column;
    align-items: stretch;
  }
  
  .demo-input {
    min-width: auto;
  }
  
  .demo-templates {
    flex-direction: column;
  }
  
  .template-options {
    flex-direction: row;
    flex-wrap: wrap;
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

/* Dark Theme Comprehensive Overrides */
body.dark-theme .homepage-wrapper {
  background: var(--bg);
  color: var(--text);
}

body.dark-theme .theme-toggle {
  background: var(--bg-subtle);
  border-color: var(--border);
}

body.dark-theme .theme-toggle i {
  color: var(--text);
}

body.dark-theme .hero-section {
  background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
}

body.dark-theme .hero-badge {
  background: rgba(30, 41, 59, 0.9);
  border-color: var(--border);
  color: var(--primary-light);
}

body.dark-theme .hero-title {
  color: var(--text);
}

body.dark-theme .accent-text {
  color: var(--primary-light);
}

body.dark-theme .hero-subtitle {
  color: var(--text-muted);
}

body.dark-theme .hero-playground {
  background: rgba(30, 41, 59, 0.8);
  border-color: var(--border);
}

body.dark-theme .playground-title {
  color: var(--text);
}

body.dark-theme .playground-title i {
  color: var(--primary-light);
}

body.dark-theme .input-group label {
  color: var(--text);
}

body.dark-theme .hero-input {
  background: var(--bg-subtle);
  border-color: var(--border);
  color: var(--text);
}

body.dark-theme .hero-input:focus {
  border-color: var(--primary-light);
  background: var(--bg);
}

body.dark-theme .hero-input::placeholder {
  color: var(--text-muted);
}

body.dark-theme .template-btn {
  background: var(--bg-subtle);
  border-color: var(--border);
  color: var(--text);
}

body.dark-theme .template-btn:hover {
  background: var(--bg);
  border-color: var(--primary-light);
}

body.dark-theme .template-btn.active {
  background: var(--primary-light);
  border-color: var(--primary-light);
}

body.dark-theme .btn-secondary {
  background: var(--bg-subtle);
  border-color: var(--border);
  color: var(--text);
}

body.dark-theme .btn-secondary:hover {
  background: var(--bg);
  border-color: var(--primary-light);
  color: var(--primary-light);
}

body.dark-theme .hero-cv-container {
  background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.8));
  border-color: var(--border);
}

body.dark-theme .hero-cv-preview {
  background: var(--bg);
}

body.dark-theme .interactive-stat {
  background: var(--bg-subtle);
  border-color: var(--border);
}

body.dark-theme .stat-content h3 {
  color: var(--text);
}

body.dark-theme .stat-content p {
  color: var(--text-muted);
}

body.dark-theme .hero-companies {
  background: var(--bg-subtle);
  border-color: var(--border);
}

body.dark-theme .companies-title {
  color: var(--text-muted);
}

body.dark-theme .company-logo {
  color: var(--text-muted);
}

body.dark-theme .section-title {
  color: var(--text);
}

body.dark-theme .section-subtitle {
  color: var(--text-muted);
}

body.dark-theme .template-card {
  background: var(--bg-subtle);
  border-color: var(--border);
}

body.dark-theme .template-card:hover {
  border-color: var(--primary-light);
}

body.dark-theme .template-preview {
  background: var(--bg);
}

body.dark-theme .template-info h3 {
  color: var(--text);
}

body.dark-theme .template-info p {
  color: var(--text-muted);
}

body.dark-theme .template-footer span {
  color: var(--text-muted);
}

body.dark-theme .process-section {
  background: var(--bg-subtle);
}

body.dark-theme .demo-cv-container {
  background: var(--bg-subtle);
  border-color: var(--border);
}

body.dark-theme .demo-cv {
  background: var(--bg);
}

body.dark-theme .step {
  background: var(--bg);
  border-color: var(--border);
}

body.dark-theme .step:hover {
  border-color: var(--primary-light);
}

body.dark-theme .step.active {
  border-color: var(--primary-light);
  background: var(--bg-subtle);
}

body.dark-theme .step-content h3 {
  color: var(--text);
}

body.dark-theme .step-content p {
  color: var(--text-muted);
}

body.dark-theme .demo-form {
  background: var(--bg-subtle);
  border-color: var(--border);
}

body.dark-theme .demo-input {
  background: var(--bg);
  border-color: var(--border);
  color: var(--text);
}

body.dark-theme .demo-input::placeholder {
  color: var(--text-muted);
}

body.dark-theme .demo-templates h4 {
  color: var(--text);
}

body.dark-theme .template-option {
  background: var(--bg);
  border-color: var(--border);
  color: var(--text);
}

body.dark-theme .template-option:hover {
  border-color: var(--primary-light);
}

body.dark-theme .template-option.active {
  background: var(--primary-light);
}

body.dark-theme .control-btn {
  background: var(--bg);
  border-color: var(--border);
  color: var(--text);
}

body.dark-theme .control-btn:hover:not(.disabled) {
  background: var(--bg-subtle);
  border-color: var(--primary-light);
  color: var(--primary-light);
}

body.dark-theme .indicator {
  background: rgba(124, 58, 237, 0.2);
}

body.dark-theme .indicator.active {
  background: var(--primary-light);
}

body.dark-theme .features-section {
  background: var(--bg);
}

body.dark-theme .feature-card {
  background: var(--bg-subtle);
  border-color: var(--border);
}

body.dark-theme .feature-card:hover {
  border-color: var(--primary-light);
}

body.dark-theme .feature-icon {
  background: var(--bg);
}

body.dark-theme .feature-card h3 {
  color: var(--text);
}

body.dark-theme .feature-card p {
  color: var(--text-muted);
}

body.dark-theme .testimonials-section {
  background: var(--bg-subtle);
}

body.dark-theme .testimonial {
  background: var(--bg);
  border-color: var(--border);
}

body.dark-theme .testimonial p {
  color: var(--text);
}

body.dark-theme .author h4 {
  color: var(--text);
}

body.dark-theme .author p {
  color: var(--text-muted);
}

body.dark-theme .cta-section {
  background: var(--primary);
}

body.dark-theme .cta-section .btn-primary {
  background: var(--bg);
  color: var(--primary);
}

body.dark-theme .cta-section .btn-primary:hover {
  background: var(--bg-subtle);
}

body.dark-theme .skip-to-content {
  background: var(--bg);
  color: var(--text);
}

/* Dark theme CV template specific overrides */
body.dark-theme .cv-template {
  color: var(--text);
}

body.dark-theme .contact-item {
  color: var(--text-muted);
}

body.dark-theme .contact-item i {
  color: var(--primary-light);
}

body.dark-theme .skill-tag {
  background: rgba(124, 58, 237, 0.2);
  color: var(--primary-light);
}

body.dark-theme .modern-sidebar {
  background: var(--primary);
}

body.dark-theme .modern-main {
  background: var(--bg);
}

.homepage-img {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: var(--radius-full, 50%);
  display: block;
}
</style>


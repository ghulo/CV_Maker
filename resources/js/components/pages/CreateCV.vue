<template>
  <div class="cv-creator">
    <!-- Onboarding Modal -->
    <div v-if="showOnboarding" class="onboarding-modal" @click="closeOnboarding">
      <div class="onboarding-content" @click.stop>
        <div class="onboarding-header">
          <h2>🎯 Welcome to CV Creator!</h2>
          <button @click="closeOnboarding" class="close-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="onboarding-steps">
          <div class="onboarding-step">
            <div class="step-icon">1️⃣</div>
            <div class="step-content">
              <h3>Personal Info</h3>
              <p>Start with your basic information - just takes 2 minutes</p>
            </div>
          </div>
          <div class="onboarding-step">
            <div class="step-icon">🤖</div>
            <div class="step-content">
              <h3>AI Assistant</h3>
              <p>Our AI will suggest content and help you write professional descriptions</p>
            </div>
          </div>
          <div class="onboarding-step">
            <div class="step-icon">👀</div>
            <div class="step-content">
              <h3>Live Preview</h3>
              <p>See your CV update in real-time as you build it</p>
            </div>
          </div>
        </div>
        <button @click="startCreating" class="start-btn">
          <i class="fas fa-rocket"></i>
          Let's Create Your CV!
        </button>
      </div>
    </div>

    <!-- Enhanced Header with Step Progress -->
    <header class="creator-header">
      <div class="header-content">
        <button @click="goBack" class="back-btn">
          <i class="fas fa-arrow-left"></i>
          <span>Back</span>
        </button>
        
        <div class="header-info">
          <h1>{{ currentStepData.title }}</h1>
          <p class="step-subtitle">{{ currentStepData.subtitle }}</p>
        </div>

        <div class="header-actions">
          <button @click="togglePreview" class="preview-toggle" :class="{ active: showPreview }">
            <i class="fas fa-eye"></i>
            <span v-if="!isMobile">{{ showPreview ? 'Hide Preview' : 'Show Preview' }}</span>
            <span v-else>{{ showPreview ? 'Hide' : 'Preview' }}</span>
          </button>
          
          <!-- Focus Mode Indicator -->
          <div v-if="!showPreview && !isMobile" class="focus-indicator">
            <i class="fas fa-bullseye"></i>
            <span>Focus Mode</span>
          </div>
          
          <div class="save-status" :class="autoSaveStatus">
            <i :class="autoSaveIcon"></i>
            <span v-if="!isMobile">{{ autoSaveText }}</span>
          </div>
        </div>
      </div>

      <!-- Enhanced Progress Bar -->
      <div class="progress-container">
        <div class="progress-bar">
          <div class="progress-fill" :style="{ width: overallProgress + '%' }"></div>
        </div>
        <div class="progress-info">
          <span class="progress-text">{{ Math.round(overallProgress) }}% Complete</span>
          <span class="time-estimate">{{ estimatedTimeLeft }}</span>
        </div>
      </div>

      <!-- Step Navigation -->
      <div class="step-navigation">
        <div class="steps-container">
          <div 
            v-for="(step, index) in wizardSteps" 
            :key="index"
            class="step-indicator"
            :class="getStepClass(index)"
            @click="navigateToStep(index)"
          >
            <div class="step-circle">
              <i v-if="index < currentStep" class="fas fa-check"></i>
              <i v-else-if="index === currentStep && loading" class="fas fa-spinner fa-spin"></i>
              <span v-else>{{ index + 1 }}</span>
            </div>
            <span class="step-label" v-if="!isMobile">{{ step.shortTitle }}</span>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content Area -->
    <div class="creator-main" :class="{ 
      'mobile-mode': isMobile, 
      'preview-active': showPreview,
      'preview-stacked': showPreview,
      'form-expanded': !showPreview
    }">


      <!-- Form Panel with Step Content -->
      <div class="form-panel" :class="{ 'expanded': !showPreview }">
        <div class="form-container">
          <!-- Messages and Achievements -->
          <div v-if="message.text" class="message-notification" :class="message.type">
            <i :class="{
              'fas fa-check-circle': message.type === 'success',
              'fas fa-exclamation-triangle': message.type === 'error',
              'fas fa-info-circle': message.type === 'info'
            }"></i>
            <span>{{ message.text }}</span>
          </div>

          <!-- Achievement Badges -->
          <div v-if="recentAchievements.length > 0" class="achievements">
            <div 
              v-for="achievement in recentAchievements" 
              :key="achievement.id"
              class="achievement-badge"
              :class="achievement.type"
            >
              <i :class="achievement.icon"></i>
              <span>{{ achievement.message }}</span>
            </div>
          </div>

          <!-- Dynamic Step Content -->
          <div class="step-content" :key="currentStep">
            <!-- Step 1: Personal Information -->
            <div v-if="currentStep === 0" class="form-step personal-info-step">
              <div class="step-intro">
                <h2>Let's start with your basic information</h2>
                <p>This helps us personalize your CV and AI suggestions</p>
              </div>

              <div class="form-section">
                <div class="form-group">
                  <label>CV Title</label>
                  <input 
                    v-model="cv.title" 
                    type="text" 
                    placeholder="e.g., Senior Developer Resume"
                    @input="onInputChange"
                    class="large-input"
                  />
                  <div class="form-hint">Give your CV a descriptive title</div>
                </div>

                <div class="form-grid grid-2">
                  <div class="form-group">
                    <label>First Name *</label>
                    <input 
                      v-model="cv.personalInfo.firstName" 
                      type="text" 
                      placeholder="John"
                      @input="onInputChange"
                      required
                    />
                  </div>
                  <div class="form-group">
                    <label>Last Name</label>
                    <input 
                      v-model="cv.personalInfo.lastName" 
                      type="text" 
                      placeholder="Doe"
                      @input="onInputChange"
                    />
                  </div>
                </div>

                <div class="form-grid grid-2">
                  <div class="form-group">
                    <label>Email *</label>
                    <input 
                      v-model="cv.personalInfo.email" 
                      type="email" 
                      placeholder="john@example.com"
                      @input="onInputChange"
                      required
                    />
                  </div>
                  <div class="form-group">
                    <label>Phone</label>
                    <input 
                      v-model="cv.personalInfo.phone" 
                      type="text" 
                      placeholder="+1 234 567 8900"
                      @input="onInputChange"
                    />
                  </div>
                </div>

                <div class="form-group">
                  <label>Address</label>
                  <input 
                    v-model="cv.personalInfo.address" 
                    type="text" 
                    placeholder="City, State, Country"
                    @input="onInputChange"
                  />
                </div>

                <!-- Modern Template Selection -->
                <div class="template-selection">
                  <div class="template-header">
                    <h3>🎨 Choose Your Template</h3>
                    <p>Select a design that matches your professional style</p>
                  </div>
                  <div class="template-grid">
                    <div 
                      v-for="template in templates" 
                      :key="template.id"
                      @click="selectTemplate(template.id)"
                      class="template-card"
                      :class="{ active: cv.selectedTemplate === template.id }"
                    >
                      <div class="template-preview">
                        <div class="template-icon">
                          <i :class="{
                            'fas fa-laptop-code': template.id === 'modern',
                            'fas fa-graduation-cap': template.id === 'classic', 
                            'fas fa-briefcase': template.id === 'professional',
                            'fas fa-palette': template.id === 'creative'
                          }"></i>
                        </div>
                        <div class="template-check" v-if="cv.selectedTemplate === template.id">
                          <i class="fas fa-check"></i>
                        </div>
                      </div>
                      <div class="template-info">
                        <h4>{{ template.name }}</h4>
                        <p>{{ template.description }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 2: Professional Summary -->
            <div v-else-if="currentStep === 1" class="form-step summary-step">
              <div class="step-intro">
                <h2>Professional Summary</h2>
                <p>A compelling summary that highlights your key strengths and experience</p>
              </div>

              <div class="form-section">
                <div class="form-group">
                  <label>Professional Summary</label>
                  <textarea 
                    v-model="cv.summary" 
                    placeholder="Brief overview of your professional background and goals..."
                    rows="6"
                    @input="onInputChange"
                    class="large-textarea"
                  ></textarea>
                  <div class="character-count">
                    {{ cv.summary?.length || 0 }}/300 characters
                    <span class="status" :class="getSummaryStatus()">
                      {{ getSummaryStatusText() }}
                    </span>
                  </div>
                </div>

                <!-- AI Summary Templates -->
                <div class="ai-templates">
                  <h4>🤖 AI-Generated Templates</h4>
                  <p>Click any template to use it as a starting point:</p>
                  <div class="template-options">
                    <div 
                      v-for="template in aiSummaryTemplates" 
                      :key="template.id"
                      @click="useSummaryTemplate(template)"
                      class="summary-template"
                    >
                      <div class="template-content">{{ template.content }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 3: Work Experience -->
            <div v-else-if="currentStep === 2" class="form-step experience-step">
              <div class="step-intro">
                <h2>Work Experience</h2>
                <p>Add your professional experience to showcase your career progression</p>
              </div>

              <div class="form-section">
                <div v-if="cv.experience.length === 0" class="empty-state">
                  <div class="empty-icon">💼</div>
                  <h3>No work experience added yet</h3>
                  <p>Add your work experience to strengthen your CV</p>
                  <button @click="addExperience" class="add-btn primary">
                    <i class="fas fa-plus"></i>
                    Add Your First Job
                  </button>
                </div>

                <div v-for="(exp, index) in cv.experience" :key="index" class="experience-card">
                  <div class="card-header">
                    <div class="card-number">{{ index + 1 }}</div>
                    <h4>{{ exp.position || 'New Position' }}</h4>
                    <button @click="removeExperience(index)" class="remove-btn">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>

                  <div class="card-content">
                    <div class="form-grid grid-2">
                      <div class="form-group">
                        <label>Position/Job Title *</label>
                        <input 
                          v-model="exp.position" 
                          type="text" 
                          placeholder="e.g., Software Engineer"
                          @input="onInputChange"
                        />
                      </div>
                      <div class="form-group">
                        <label>Company *</label>
                        <input 
                          v-model="exp.company" 
                          type="text" 
                          placeholder="e.g., Tech Corp"
                          @input="onInputChange"
                        />
                      </div>
                    </div>

                    <div class="form-grid grid-3">
                      <div class="form-group">
                        <label>Start Date</label>
                        <input 
                          v-model="exp.start_date" 
                          type="month"
                          @input="onInputChange"
                        />
                      </div>
                      <div class="form-group">
                        <label>End Date</label>
                        <input 
                          v-model="exp.end_date" 
                          type="month"
                          :disabled="exp.current"
                          @input="onInputChange"
                        />
                      </div>
                      <div class="form-group">
                        <label class="checkbox-label">
                          <input 
                            v-model="exp.current" 
                            type="checkbox"
                            @change="onInputChange"
                          />
                          Current Position
                        </label>
                      </div>
                    </div>

                    <div class="form-group">
                      <label>Job Description</label>
                      <textarea 
                        v-model="exp.description" 
                        placeholder="Describe your key responsibilities and achievements..."
                        rows="4"
                        @input="onInputChange"
                      ></textarea>
                      <div class="form-actions">
                        <button 
                          @click="enhanceExperienceDescription(index)" 
                          :disabled="loading || !exp.position" 
                          class="ai-enhance-btn"
                        >
                          <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-magic'"></i>
                          {{ exp.description ? 'Improve with AI' : 'Generate with AI' }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <button v-if="cv.experience.length > 0" @click="addExperience" class="add-more-btn">
                  <i class="fas fa-plus"></i>
                  Add Another Position
                </button>
              </div>
            </div>

            <!-- Step 4: Education & Skills -->
            <div v-else-if="currentStep === 3" class="form-step education-skills-step">
              <div class="step-intro">
                <h2>Education & Skills</h2>
                <p>Add your educational background and key skills</p>
              </div>

              <!-- Education Section -->
              <div class="form-section">
                <h3>🎓 Education</h3>
                
                <div v-if="cv.education.length === 0" class="mini-empty-state">
                  <p>Add your educational background</p>
                  <button @click="addEducation" class="add-btn">
                    <i class="fas fa-plus"></i>
                    Add Education
                  </button>
                </div>

                <div v-for="(edu, index) in cv.education" :key="index" class="education-card">
                  <div class="card-header">
                    <div class="card-number">{{ index + 1 }}</div>
                    <h4>{{ edu.degree || 'New Education' }}</h4>
                    <button @click="removeEducation(index)" class="remove-btn">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>

                  <div class="card-content">
                    <div class="form-grid grid-2">
                      <div class="form-group">
                        <label>Degree/Certificate</label>
                        <input 
                          v-model="edu.degree" 
                          type="text" 
                          placeholder="e.g., Bachelor of Science"
                          @input="onInputChange"
                        />
                      </div>
                      <div class="form-group">
                        <label>Institution</label>
                        <input 
                          v-model="edu.institution" 
                          type="text" 
                          placeholder="e.g., University Name"
                          @input="onInputChange"
                        />
                      </div>
                    </div>

                    <div class="form-grid grid-3">
                      <div class="form-group">
                        <label>Start Year</label>
                        <input 
                          v-model="edu.start_date" 
                          type="month"
                          @input="onInputChange"
                        />
                      </div>
                      <div class="form-group">
                        <label>End Year</label>
                        <input 
                          v-model="edu.end_date" 
                          type="month"
                          @input="onInputChange"
                        />
                      </div>
                      <div class="form-group">
                        <label>GPA (Optional)</label>
                        <input 
                          v-model="edu.gpa" 
                          type="text" 
                          placeholder="3.8"
                          @input="onInputChange"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <button v-if="cv.education.length > 0" @click="addEducation" class="add-more-btn">
                  <i class="fas fa-plus"></i>
                  Add Another Education
                </button>
              </div>

              <!-- Skills Section -->
              <div class="form-section">
                <h3>🛠️ Skills</h3>
                
                <div class="skills-input">
                  <input 
                    v-model="newSkill" 
                    type="text" 
                    placeholder="Type a skill and press Enter..."
                    @keydown.enter.prevent="addSkill"
                  />
                  <button @click="addSkill" class="add-skill-btn">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>

                <div v-if="cv.skills.length > 0" class="skills-list">
                  <div v-for="(skill, index) in cv.skills" :key="index" class="skill-tag">
                    <span>{{ skill.name }}</span>
                    <button @click="removeSkill(index)">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>

                <!-- Smart Skill Suggestions -->
                <div v-if="smartSkillSuggestions.length > 0" class="skill-suggestions">
                  <h4>💡 Suggested skills for {{ primaryJobTitle }}:</h4>
                  <div class="suggestions-grid">
                    <button 
                      v-for="suggestion in smartSkillSuggestions" 
                      :key="suggestion"
                      @click="addRecommendedSkill(suggestion)"
                      class="suggestion-btn"
                      :disabled="isSkillAdded(suggestion)"
                    >
                      <i :class="isSkillAdded(suggestion) ? 'fas fa-check' : 'fas fa-plus'"></i>
                      {{ suggestion }}
                    </button>
                  </div>
                </div>

                <!-- Interests -->
                <div class="interests-section">
                  <h4>🎯 Interests (Optional)</h4>
                  <div class="interests-input">
                    <input 
                      v-model="newInterest" 
                      type="text" 
                      placeholder="Type an interest and press Enter..."
                      @keydown.enter.prevent="addInterest"
                    />
                    <button @click="addInterest" class="add-interest-btn">
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>

                  <div v-if="cv.interests.length > 0" class="interests-list">
                    <div v-for="(interest, index) in cv.interests" :key="index" class="interest-tag">
                      <span>{{ interest.name }}</span>
                      <button @click="removeInterest(index)">
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 5: Review & Finalize -->
            <div v-else-if="currentStep === 4" class="form-step review-step">
              <div class="step-intro">
                <h2>🎉 Review & Finalize</h2>
                <p>Almost done! Review your CV and make any final adjustments</p>
              </div>

              <div class="review-section">
                <!-- CV Completeness Score -->
                <div class="completion-score">
                  <div class="score-circle" :class="getCompletionClass()">
                    <div class="score-number">{{ completionScore }}</div>
                    <div class="score-label">Score</div>
                  </div>
                  <div class="score-details">
                    <h3>CV Strength: {{ getCompletionLabel() }}</h3>
                    <p>{{ getCompletionMessage() }}</p>
                  </div>
                </div>

                <!-- Section Checklist -->
                <div class="section-checklist">
                  <h3>📋 Section Completeness</h3>
                  <div class="checklist-items">
                    <div class="checklist-item" :class="personalInfoStatus.class">
                      <i :class="personalInfoStatus.icon"></i>
                      <span>Personal Information</span>
                      <div class="item-status">{{ getPersonalInfoCompleteness() }}</div>
                    </div>
                    <div class="checklist-item" :class="getSummaryStatus()">
                      <i :class="getSummaryIcon()"></i>
                      <span>Professional Summary</span>
                      <div class="item-status">{{ getSummaryCompleteness() }}</div>
                    </div>
                    <div class="checklist-item" :class="experienceStatus.class">
                      <i :class="experienceStatus.icon"></i>
                      <span>Work Experience</span>
                      <div class="item-status">{{ getExperienceCompleteness() }}</div>
                    </div>
                    <div class="checklist-item" :class="educationStatus.class">
                      <i :class="educationStatus.icon"></i>
                      <span>Education</span>
                      <div class="item-status">{{ getEducationCompleteness() }}</div>
                    </div>
                    <div class="checklist-item" :class="skillsStatus.class">
                      <i :class="skillsStatus.icon"></i>
                      <span>Skills</span>
                      <div class="item-status">{{ getSkillsCompleteness() }}</div>
                    </div>
                  </div>
                </div>

                <!-- Final Actions -->
                <div class="final-actions">
                  <button 
                    @click="saveCv('published')" 
                    :disabled="loading || !canFinalize" 
                    class="finalize-btn"
                  >
                    <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-check-circle'"></i>
                    Complete My CV
                  </button>
                  
                  <p class="final-note">
                    Your CV will be saved and ready for download!
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Step Navigation Buttons -->
          <div class="step-navigation-buttons">
            <button 
              v-if="currentStep > 0" 
              @click="previousStep" 
              class="nav-btn secondary"
            >
              <i class="fas fa-chevron-left"></i>
              Previous
            </button>
            
            <div class="spacer"></div>
            
            <button 
              v-if="currentStep < wizardSteps.length - 1" 
              @click="nextStep" 
              class="nav-btn primary"
              :disabled="!canProceedToNextStep()"
            >
              Next
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Enhanced Preview Panel - Now positioned below form when active -->
      <div v-if="showPreview" class="preview-panel active" :class="{ 'stacked': showPreview }">
        <div class="preview-header">
          <h3>
            <i class="fas fa-eye"></i>
            Live Preview
          </h3>
          <div class="preview-actions">
            <button @click="downloadPDF" :disabled="loading" class="download-btn">
              <i class="fas fa-download"></i>
              <span v-if="!isMobile">PDF</span>
            </button>
            <button @click="togglePreview" class="close-preview">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <div class="preview-container">
          <!-- Only render template component when preview is visible -->
          <component 
            v-if="previewData"
            :is="currentTemplateComponent" 
            :cv="previewData"
          />
          <div v-else class="preview-placeholder">
            <i class="fas fa-file-alt"></i>
            <p>Add some information to see your CV preview</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="success-modal" @click="closeSuccessModal">
      <div class="success-content" @click.stop>
        <div class="success-animation">
          <div class="checkmark">
            <svg viewBox="0 0 52 52">
              <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
              <path class="checkmark-check" fill="none" d="m14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
          </div>
        </div>
        <h3>🎉 CV Created Successfully!</h3>
        <p>Your professional CV is ready to download and share with employers.</p>
        <div class="success-actions">
          <button @click="previewCV" class="btn primary">
            <i class="fas fa-eye"></i>
            View My CV
          </button>
          <button @click="goToDashboard" class="btn secondary">
            <i class="fas fa-home"></i>
            Dashboard
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Preview Toggle -->
    <div v-if="isMobile" class="mobile-preview-toggle">
      <button @click="togglePreview" class="preview-fab" :class="{ active: showPreview }">
        <i :class="showPreview ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
      </button>
    </div>

    <!-- Loading Overlay -->
    <div v-if="loading" class="loading-overlay">
      <div class="loading-content">
        <div class="loading-spinner"></div>
        <p>{{ loadingMessage }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import axios from 'axios'

// Import template components
import ClassicTemplate from '@/components/cv_templates/ClassicTemplate.vue'
import ModernTemplate from '@/components/cv_templates/ModernTemplate.vue'
import ProfessionalTemplate from '@/components/cv_templates/ProfessionalTemplate.vue'
import CreativeTemplate from '@/components/cv_templates/CreativeTemplate.vue'

// Import services
import AIService from '@/services/aiService.js'

const router = useRouter()
const route = useRoute()
const { t } = useI18n()

// Enhanced state management
const currentStep = ref(0)
const showPreview = ref(false) // Hidden by default for performance
const showSuccessModal = ref(false)
const showOnboarding = ref(false) // Disable onboarding initially to debug
const showAIPanel = ref(true)
const savedCvId = ref(null)
const loading = ref(false)
const loadingMessage = ref('Processing...')

// Mobile detection
const isMobile = ref(false)

// Form inputs
const newSkill = ref('')
const newInterest = ref('')

// Message handling
const message = ref({ text: '', type: '' })

// Achievements system
const recentAchievements = ref([])

// Performance optimization: Debounced reactive state
const isGeneratingAI = ref(false)
const autoSaveInProgress = ref(false)

// Wizard steps configuration
const wizardSteps = ref([
  { 
    id: 0, 
    title: 'Personal Information', 
    shortTitle: 'Personal',
    subtitle: 'Let\'s start with your basic information',
    timeEstimate: 3
  },
  { 
    id: 1, 
    title: 'Professional Summary', 
    shortTitle: 'Summary',
    subtitle: 'Craft a compelling professional summary',
    timeEstimate: 5
  },
  { 
    id: 2, 
    title: 'Work Experience', 
    shortTitle: 'Experience',
    subtitle: 'Add your professional experience',
    timeEstimate: 8
  },
  { 
    id: 3, 
    title: 'Education & Skills', 
    shortTitle: 'Skills',
    subtitle: 'Complete your educational background and skills',
    timeEstimate: 4
  },
  { 
    id: 4, 
    title: 'Review & Finalize', 
    shortTitle: 'Review',
    subtitle: 'Review and finalize your CV',
    timeEstimate: 2
  }
])

// Auto-save functionality - optimized
const autoSaveStatus = ref('idle')
const lastSaved = ref(null)
const autoSaveTimer = ref(null)
const saveDebounceTimer = ref(null)

// Available templates with enhanced info
const templates = ref([
  { 
    id: 'modern', 
    name: 'Modern', 
    description: 'Clean and contemporary design perfect for tech professionals'
  },
  { 
    id: 'classic', 
    name: 'Classic', 
    description: 'Traditional and professional layout suitable for any industry'
  },
  { 
    id: 'professional', 
    name: 'Professional', 
    description: 'Corporate-friendly design ideal for business roles'
  },
  { 
    id: 'creative', 
    name: 'Creative', 
    description: 'Unique and artistic layout for creative professionals'
  }
])

// AI suggestions
const aiSkillSuggestions = ref([
  'JavaScript', 'Python', 'React', 'Vue.js', 'Node.js', 'TypeScript', 
  'HTML/CSS', 'Git', 'SQL', 'MongoDB', 'Docker', 'AWS'
])

// CV data structure - properly initialized
const cv = ref({
  title: '',
  personalInfo: {
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    address: ''
  },
  summary: '',
  experience: [],
  education: [],
  skills: [],
  interests: [],
  selectedTemplate: route.query.template || 'modern'
})

// AI suggestions
const aiSuggestions = ref({
  summary: [],
  experience: [],
  skills: []
})

// Computed properties - optimized for performance
const completionClass = computed(() => {
  const score = completionScore.value
  if (score >= 80) return 'high'
  if (score >= 50) return 'medium'
  return 'low'
})

// Enhanced computed properties for wizard
const currentStepData = computed(() => {
  return wizardSteps.value[currentStep.value] || wizardSteps.value[0]
})

// Optimized progress calculation with caching
let cachedProgress = 0
let lastProgressUpdate = 0
const overallProgress = computed(() => {
  // Cache progress calculation to reduce computation
  const now = Date.now()
  if (now - lastProgressUpdate < 500) { // Cache for 500ms
    return cachedProgress
  }
  
  const stepProgress = (currentStep.value / (wizardSteps.value.length - 1)) * 100
  const currentStepCompletion = getCurrentStepCompletion() * 0.2
  cachedProgress = Math.min(100, stepProgress + currentStepCompletion)
  lastProgressUpdate = now
  
  return cachedProgress
})

const estimatedTimeLeft = computed(() => {
  let totalTime = 0
  for (let i = currentStep.value; i < wizardSteps.value.length; i++) {
    totalTime += wizardSteps.value[i].timeEstimate
  }
  
  if (totalTime === 0) return 'Done!'
  if (totalTime <= 1) return '< 1 min'
  return `~${totalTime} min left`
})

const autoSaveIcon = computed(() => {
  switch (autoSaveStatus.value) {
    case 'saving': return 'fas fa-spinner fa-spin'
    case 'saved': return 'fas fa-check'
    case 'error': return 'fas fa-exclamation-triangle'
    default: return 'fas fa-cloud'
  }
})

const autoSaveText = computed(() => {
  switch (autoSaveStatus.value) {
    case 'saving': return 'Saving...'
    case 'saved': return 'Saved'
    case 'error': return 'Error'
    default: return 'Auto-save'
  }
})

// Optimized primary job title computation
const primaryJobTitle = computed(() => {
  try {
    if (cv.value?.experience && cv.value.experience.length > 0 && cv.value.experience[0].position) {
      return cv.value.experience[0].position
    }
    return 'Professional'
  } catch (error) {
    return 'Professional'
  }
})

// Cached skill suggestions
let cachedSkillSuggestions = []
let lastSkillUpdate = 0
const smartSkillSuggestions = computed(() => {
  const now = Date.now()
  if (now - lastSkillUpdate < 2000) { // Cache for 2 seconds
    return cachedSkillSuggestions
  }
  
  const suggestions = AIService.getSkillSuggestions(primaryJobTitle.value)
  cachedSkillSuggestions = suggestions.filter(skill => 
    !cv.value.skills?.some(existing => 
      existing.name.toLowerCase() === skill.toLowerCase()
    )
  ).slice(0, 8)
  lastSkillUpdate = now
  
  return cachedSkillSuggestions
})

const aiSummaryTemplates = computed(() => {
  try {
    const personalInfo = cv.value?.personalInfo || {}
    const experience = cv.value?.experience || []
    const yearsExp = calculateYearsOfExperience()
    const jobTitle = primaryJobTitle.value
    
    return [
      {
        id: 1,
        content: `Experienced ${jobTitle} with ${yearsExp} years of proven expertise in delivering high-quality results. Strong background in problem-solving and team collaboration.`
      },
      {
        id: 2,
        content: `Results-oriented ${jobTitle} with ${yearsExp} years of experience delivering innovative solutions. Proven track record of success in fast-paced environments with strong analytical and problem-solving skills.`
      },
      {
        id: 3,
        content: `Dedicated ${jobTitle} passionate about leveraging technology to drive business success. Excellent communication skills and ability to work collaboratively in team environments.`
      }
    ]
  } catch (error) {
    return [
      {
        id: 1,
        content: 'Experienced professional with strong problem-solving skills and proven track record of delivering results.'
      }
    ]
  }
})

const currentStepAISuggestions = computed(() => {
  const suggestions = []
  
  switch (currentStep.value) {
    case 0: // Personal Info
      if (!cv.value.personalInfo?.firstName) {
        suggestions.push({ text: 'Add your first name', applied: false })
      }
      if (!cv.value.personalInfo?.email) {
        suggestions.push({ text: 'Add your email address', applied: false })
      }
      break
    case 1: // Summary
      if (!cv.value.summary || cv.value.summary.length < 50) {
        suggestions.push({ text: 'Write a compelling summary', applied: false })
      }
      break
    case 2: // Experience
      if (cv.value.experience?.length === 0) {
        suggestions.push({ text: 'Add your first job', applied: false })
      }
      break
    case 3: // Skills
      if (cv.value.skills?.length < 5) {
        suggestions.push({ text: 'Add more skills', applied: false })
      }
      break
  }
  
  return suggestions
})

const currentStepAIActions = computed(() => {
  const actions = []
  
  switch (currentStep.value) {
    case 1: // Summary
      actions.push({
        id: 'generate-summary',
        label: 'Generate Summary',
        icon: 'fas fa-magic'
      })
      break
    case 2: // Experience
      if (cv.value.experience?.length > 0) {
        actions.push({
          id: 'enhance-experience',
          label: 'Enhance Descriptions',
          icon: 'fas fa-magic'
        })
      }
      break
    case 3: // Skills
      actions.push({
        id: 'suggest-skills',
        label: 'Suggest Skills',
        icon: 'fas fa-lightbulb'
      })
      break
  }
  
  return actions
})

const aiAssistantMessage = computed(() => {
  const messages = {
    0: 'Let me help you get started!',
    1: 'I can help craft your summary',
    2: 'Need help with job descriptions?',
    3: 'I can suggest relevant skills',
    4: 'Ready to finalize your CV?'
  }
  return messages[currentStep.value] || 'I\'m here to help!'
})

// Section status indicators
const personalInfoStatus = computed(() => {
  const info = cv.value?.personalInfo || {}
  const hasRequired = info.firstName && info.lastName && info.email && info.phone
  const hasSummary = cv.value?.summary && cv.value.summary.length >= 50
  
  if (hasRequired && hasSummary) {
    return { class: 'complete', icon: 'fas fa-check-circle' }
  }
  return { class: 'incomplete', icon: 'fas fa-exclamation-circle' }
})

const experienceStatus = computed(() => {
  const experiences = cv.value?.experience || []
  if (experiences.length === 0) {
    return { class: 'incomplete', icon: 'fas fa-exclamation-circle' }
  }
  
  const completeExperiences = experiences.filter(exp => 
    exp.position && exp.company && exp.start_date
  )
  
  if (completeExperiences.length === experiences.length) {
    return { class: 'complete', icon: 'fas fa-check-circle' }
  }
  return { class: 'incomplete', icon: 'fas fa-exclamation-circle' }
})

const educationStatus = computed(() => {
  const educations = cv.value?.education || []
  if (educations.length === 0) {
    return { class: 'incomplete', icon: 'fas fa-exclamation-circle' }
  }
  
  const completeEducation = educations.filter(edu => 
    edu.degree && edu.institution && edu.start_date
  )
  
  if (completeEducation.length === educations.length) {
    return { class: 'complete', icon: 'fas fa-check-circle' }
  }
  return { class: 'incomplete', icon: 'fas fa-exclamation-circle' }
})

const skillsStatus = computed(() => {
  const skills = cv.value?.skills || []
  if (skills.length >= 5) {
    return { class: 'complete', icon: 'fas fa-check-circle' }
  }
  return { class: 'incomplete', icon: 'fas fa-exclamation-circle' }
})

// Optimized preview data - only computed when preview is visible
const previewData = computed(() => {
  if (!showPreview.value) {
    // Return minimal data when preview is hidden to save performance
    return null
  }
  
  // Ensure cv.value and all nested properties exist
  const personalInfo = cv.value?.personalInfo || {}
  const fullName = `${personalInfo.firstName || ''} ${personalInfo.lastName || ''}`.trim()
  
  // Map experience data to match template expectations
  const experiences = cv.value?.experience || []
  const workExperiences = experiences.length > 0 
    ? experiences.map(exp => ({
        job_title: exp.position || 'Position Title',
        company: exp.company || 'Company Name',
        start_date: exp.start_date || '',
        end_date: exp.end_date || '',
        description: exp.description || 'Job description will appear here...'
      }))
    : [{
        job_title: 'Software Developer',
        company: 'Tech Company',
        start_date: '2022-01',
        end_date: '',
        description: 'Add your work experience to see it displayed here...'
      }]
  
  // Map education data to match template expectations  
  const educationsData = cv.value?.education || []
  const educations = educationsData.length > 0
    ? educationsData.map(edu => ({
        degree: edu.degree || 'Degree',
        institution: edu.institution || 'Institution Name',
        start_date: edu.start_date || '',
        end_date: edu.end_date || ''
      }))
    : [{
        degree: 'Bachelor of Science',
        institution: 'University Name',
        start_date: '2018',
        end_date: '2022'
      }]
      
  // Skills with fallback
  const skillsData = cv.value?.skills || []
  const skills = skillsData.length > 0 
    ? skillsData 
    : [
        { name: 'JavaScript' },
        { name: 'React' },
        { name: 'Node.js' },
        { name: 'CSS' },
        { name: 'HTML' }
      ]
  
  return {
    personal_details: {
      full_name: fullName || 'Your Name',
      email: personalInfo.email || 'your.email@example.com',
      phone_number: personalInfo.phone || '+1 234 567 8900',
      address: personalInfo.address || 'Your Address'
    },
    summary: cv.value?.summary || 'Your professional summary will appear here...',
    work_experiences: workExperiences,
    educations: educations,
    skills: skills,
    interests: cv.value?.interests || []
  }
})

const currentTemplateComponent = computed(() => {
  switch (cv.value.selectedTemplate) {
    case 'classic': return ClassicTemplate
    case 'modern': return ModernTemplate
    case 'professional': return ProfessionalTemplate
    case 'creative': return CreativeTemplate
    default: return ModernTemplate
  }
})

// Optimized completion score with caching
let cachedCompletionScore = 0
let lastCompletionUpdate = 0
const completionScore = computed(() => {
  const now = Date.now()
  if (now - lastCompletionUpdate < 1000) { // Cache for 1 second
    return cachedCompletionScore
  }
  
  if (!cv.value) return 0
  
  let score = 0
  const weights = {
    personalInfo: 30,
    summary: 20,
    experience: 25,
    education: 15,
    skills: 10
  }
  
  // Personal info scoring
  const personalInfo = cv.value.personalInfo || {}
  const personalFields = Object.values(personalInfo).filter(val => val && val.trim())
  score += (personalFields.length / 5) * weights.personalInfo
  
  // Summary scoring
  if (cv.value.summary && cv.value.summary.trim().length >= 50) {
    score += weights.summary
  }
  
  // Experience scoring
  const experiences = cv.value.experience || []
  if (experiences.length > 0) {
    const completeExperiences = experiences.filter(exp => 
      exp.position && exp.company && exp.start_date
    )
    score += (completeExperiences.length / experiences.length) * weights.experience
  }
  
  // Education scoring
  const educations = cv.value.education || []
  if (educations.length > 0) {
    const completeEducation = educations.filter(edu => 
      edu.degree && edu.institution && edu.start_date
    )
    score += (completeEducation.length / educations.length) * weights.education
  }
  
  // Skills scoring
  const skills = cv.value.skills || []
  if (skills.length >= 3) {
    score += weights.skills
  }
  
  cachedCompletionScore = Math.round(score)
  lastCompletionUpdate = now
  return cachedCompletionScore
})

// Enhanced Methods for Wizard & UX
const togglePreview = () => {
  showPreview.value = !showPreview.value
  // Show message when preview is toggled
  if (showPreview.value) {
    showMessage('Live preview activated!', 'success')
  } else {
    showMessage('Live preview hidden', 'info')
  }
}

// Onboarding methods
const closeOnboarding = () => {
  showOnboarding.value = false
}

const startCreating = () => {
  showOnboarding.value = false
  showMessage('🎉 Welcome! Let\'s create your amazing CV!', 'success')
}

// Step navigation methods
const nextStep = () => {
  if (currentStep.value < wizardSteps.value.length - 1) {
    currentStep.value++
    triggerAutoSave()
    showStepAchievement()
  }
}

const previousStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--
  }
}

const navigateToStep = (stepIndex) => {
  if (stepIndex <= currentStep.value || canNavigateToStep(stepIndex)) {
    currentStep.value = stepIndex
  }
}

const canNavigateToStep = (stepIndex) => {
  // Allow navigation to completed steps and next uncompleted step
  return stepIndex <= currentStep.value + 1
}

const canProceedToNextStep = () => {
  switch (currentStep.value) {
    case 0: // Personal Info
      return cv.value.personalInfo?.firstName && cv.value.personalInfo?.email
    case 1: // Summary
      return cv.value.summary && cv.value.summary.length >= 20
    case 2: // Experience
      return cv.value.experience?.length > 0
    case 3: // Education & Skills
      return cv.value.skills?.length >= 3
    default:
      return true
  }
}

const getCurrentStepCompletion = () => {
  switch (currentStep.value) {
    case 0: // Personal Info
      const personalFields = ['firstName', 'lastName', 'email', 'phone', 'address']
      const completed = personalFields.filter(field => 
        cv.value.personalInfo?.[field]
      ).length
      return (completed / personalFields.length) * 100
    
    case 1: // Summary
      if (!cv.value.summary) return 0
      if (cv.value.summary.length < 50) return 30
      if (cv.value.summary.length < 100) return 70
      return 100
    
    case 2: // Experience
      if (cv.value.experience?.length === 0) return 0
      const completeExperiences = cv.value.experience.filter(exp => 
        exp.position && exp.company && exp.start_date
      ).length
      return (completeExperiences / cv.value.experience.length) * 100
    
    case 3: // Education & Skills
      let skillsScore = Math.min(100, (cv.value.skills?.length || 0) * 20)
      let educationScore = cv.value.education?.length > 0 ? 50 : 0
      return (skillsScore + educationScore) / 2
    
    default:
      return 100
  }
}

const getStepClass = (index) => {
  return {
    'completed': index < currentStep.value,
    'active': index === currentStep.value,
    'pending': index > currentStep.value,
    'clickable': canNavigateToStep(index)
  }
}

// Achievement system
const showStepAchievement = () => {
  const achievements = {
    1: { icon: 'fas fa-user', message: 'Personal info completed! 🎉', type: 'success' },
    2: { icon: 'fas fa-file-alt', message: 'Great summary! You\'re making progress! 📝', type: 'success' },
    3: { icon: 'fas fa-briefcase', message: 'Experience added! Looking professional! 💼', type: 'success' },
    4: { icon: 'fas fa-graduation-cap', message: 'Skills and education complete! 🎓', type: 'success' }
  }
  
  const achievement = achievements[currentStep.value]
  if (achievement) {
    recentAchievements.value.unshift({
      id: Date.now(),
      ...achievement
    })
    
    // Remove achievement after 5 seconds
    setTimeout(() => {
      recentAchievements.value = recentAchievements.value.filter(a => a.id !== achievement.id)
    }, 5000)
  }
}

// Enhanced AI methods
const toggleAIPanel = () => {
  showAIPanel.value = !showAIPanel.value
}

const applySuggestion = (suggestion) => {
  // Mark suggestion as applied
  suggestion.applied = true
  
  // Execute the suggestion
  switch (suggestion.text) {
    case 'Add your first name':
      document.querySelector('input[placeholder="John"]')?.focus()
      break
    case 'Add your email address':
      document.querySelector('input[type="email"]')?.focus()
      break
    case 'Write a compelling summary':
      generateAISummary()
      break
    case 'Add your first job':
      addExperience()
      break
    case 'Add more skills':
      document.querySelector('.skills-input input')?.focus()
      break
  }
}

const executeAIAction = async (action) => {
  loadingMessage.value = `${action.label}...`
  
  switch (action.id) {
    case 'generate-summary':
      await generateAISummary()
      break
    case 'enhance-experience':
      for (let i = 0; i < cv.value.experience.length; i++) {
        await enhanceExperienceDescription(i)
      }
      break
    case 'suggest-skills':
      // Add top 3 suggested skills
      const topSkills = smartSkillSuggestions.value.slice(0, 3)
      topSkills.forEach(skill => addRecommendedSkill(skill))
      showMessage(`Added ${topSkills.length} suggested skills!`, 'success')
      break
  }
}

const useSummaryTemplate = (template) => {
  cv.value.summary = template.content
  showMessage('Template applied! Feel free to customize it.', 'success')
  triggerAutoSave()
}

// Template helpers (simplified)
const getTemplateMiniComponent = (templateId) => {
  // For now, return a simple placeholder since mini templates might be causing issues
  return 'div'
}

// Summary helpers
const getSummaryStatus = () => {
  if (!cv.value.summary) return 'incomplete'
  if (cv.value.summary.length < 50) return 'warning'
  return 'complete'
}

const getSummaryStatusText = () => {
  if (!cv.value.summary) return 'Start writing'
  if (cv.value.summary.length < 50) return 'Add more detail'
  return 'Looking good!'
}

const getSummaryIcon = () => {
  const status = getSummaryStatus()
  return status === 'complete' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'
}

// Review step helpers
const getCompletionClass = () => {
  const score = completionScore.value
  if (score >= 80) return 'excellent'
  if (score >= 60) return 'good'
  if (score >= 40) return 'fair'
  return 'poor'
}

const getCompletionLabel = () => {
  const score = completionScore.value
  if (score >= 80) return 'Excellent'
  if (score >= 60) return 'Good'
  if (score >= 40) return 'Fair'
  return 'Needs Work'
}

const getCompletionMessage = () => {
  const score = completionScore.value
  if (score >= 80) return 'Your CV looks great! Ready to impress employers.'
  if (score >= 60) return 'Good progress! Consider adding more details.'
  if (score >= 40) return 'Getting there! A few more sections needed.'
  return 'Let\'s add more information to strengthen your CV.'
}

const getPersonalInfoCompleteness = () => {
  const info = cv.value.personalInfo || {}
  const fields = ['firstName', 'lastName', 'email', 'phone', 'address']
  const completed = fields.filter(field => info[field]).length
  return `${completed}/${fields.length} fields`
}

const getSummaryCompleteness = () => {
  if (!cv.value.summary) return 'Not started'
  if (cv.value.summary.length < 50) return 'Too short'
  return 'Complete'
}

const getExperienceCompleteness = () => {
  if (!cv.value.experience?.length) return 'No experience added'
  return `${cv.value.experience.length} position${cv.value.experience.length > 1 ? 's' : ''}`
}

const getEducationCompleteness = () => {
  if (!cv.value.education?.length) return 'No education added'
  return `${cv.value.education.length} entry${cv.value.education.length > 1 ? 'ies' : 'y'}`
}

const getSkillsCompleteness = () => {
  const count = cv.value.skills?.length || 0
  if (count === 0) return 'No skills added'
  if (count < 5) return `${count} skills (add more)`
  return `${count} skills`
}

const isSkillAdded = (skillName) => {
  return cv.value.skills?.some(skill => 
    skill.name.toLowerCase() === skillName.toLowerCase()
  )
}

// Optimized lifecycle hooks
onMounted(() => {
  // Initialize mobile state
  updateMobileState()
  
  // Use optimized resize handler with debouncing
  let resizeTimer = null
  const debouncedResize = () => {
    if (resizeTimer) clearTimeout(resizeTimer)
    resizeTimer = setTimeout(updateMobileState, 150)
  }
  
  window.addEventListener('resize', debouncedResize, { passive: true })
  
  // Check if returning user (disable onboarding for performance)
  const hasVisited = localStorage.getItem('cv_creator_visited')
  if (!hasVisited) {
    localStorage.setItem('cv_creator_visited', 'true')
    // showOnboarding.value = true // Commented out for better performance
  }
  
  // Store resize cleanup function
  window._cvCreatorResizeCleanup = () => {
    window.removeEventListener('resize', debouncedResize)
    if (resizeTimer) clearTimeout(resizeTimer)
  }
})

onUnmounted(() => {
  // Clean up resize listener
  if (window._cvCreatorResizeCleanup) {
    window._cvCreatorResizeCleanup()
    delete window._cvCreatorResizeCleanup
  }
})

const selectTemplate = (templateId) => {
  cv.value.selectedTemplate = templateId
  triggerAutoSave()
}

const addSkill = () => {
  console.log('addSkill called with:', newSkill.value)
  if (newSkill.value.trim()) {
    if (!cv.value.skills) cv.value.skills = []
    cv.value.skills.push({ name: newSkill.value.trim() })
    newSkill.value = ''
    console.log('Skill added, new skills:', cv.value.skills)
    // triggerAutoSave()
  }
}

const removeSkill = (index) => {
  if (cv.value.skills && cv.value.skills[index]) {
    cv.value.skills.splice(index, 1)
    triggerAutoSave()
  }
}

const addRecommendedSkill = (skillName) => {
  if (!cv.value.skills) cv.value.skills = []
  if (!cv.value.skills.find(skill => skill.name === skillName)) {
    cv.value.skills.push({ name: skillName })
    triggerAutoSave()
  }
}

const addInterest = () => {
  console.log('addInterest called with:', newInterest.value)
  if (newInterest.value.trim()) {
    if (!cv.value.interests) cv.value.interests = []
    cv.value.interests.push({ name: newInterest.value.trim() })
    newInterest.value = ''
    console.log('Interest added, new interests:', cv.value.interests)
    // triggerAutoSave()
  }
}

const removeInterest = (index) => {
  if (cv.value.interests && cv.value.interests[index]) {
    cv.value.interests.splice(index, 1)
    triggerAutoSave()
  }
}

const handleSave = () => {
  saveCv('published')
}

const triggerAutoSave = () => {
  onInputChange()
}

const downloadPDF = () => {
  // Implement PDF download functionality
  console.log('Downloading PDF...')
}

// Simple AI enhancement methods
const generateAISummary = async () => {
  loading.value = true
  try {
    // Simple placeholder functionality
    const personalInfo = cv.value?.personalInfo || {}
    const name = `${personalInfo.firstName || ''} ${personalInfo.lastName || ''}`.trim()
    const jobTitle = cv.value?.experience?.[0]?.position || 'Professional'
    
    cv.value.summary = `Dynamic and results-driven ${jobTitle} with proven expertise in delivering high-quality solutions. ${name || 'This professional'} brings strong analytical skills and a passion for innovation to every project.`
    
    showMessage('Summary generated successfully!', 'success')
  } catch (error) {
    showMessage('Failed to generate summary', 'error')
  } finally {
    loading.value = false
  }
}

const enhanceExperienceDescription = async (index) => {
  loading.value = true
  try {
    const experiences = cv.value?.experience || []
    if (experiences[index]) {
      const exp = experiences[index]
      if (exp.position && exp.company) {
        exp.description = `• Led key initiatives as ${exp.position} at ${exp.company}
• Collaborated with cross-functional teams to deliver impactful results
• Demonstrated expertise in problem-solving and strategic planning
• Contributed to organizational growth and operational excellence`
        
        showMessage('Experience description enhanced!', 'success')
      } else {
        showMessage('Please fill in position and company first', 'error')
      }
    }
  } catch (error) {
    showMessage('Failed to enhance description', 'error')
  } finally {
    loading.value = false
  }
}

const canFinalize = computed(() => {
  if (!cv.value) return false
  
  const personalInfo = cv.value.personalInfo || {}
  // More lenient - only require basic info for finalization
  return cv.value.title && 
         personalInfo.firstName && 
         personalInfo.email
})

// Simplified success feedback
const showSuccessFeedback = () => {
  // Simple success state - no confetti
  showSuccessModal.value = true
}

// Removed problematic validation function

// Removed typewriter effect for cleaner UX

// AI suggestions functionality simplified

const calculateYearsOfExperience = () => {
  if (cv.value.experience.length === 0) return 0
  
  let totalMonths = 0
  cv.value.experience.forEach(exp => {
    if (exp.start_date) {
      const start = new Date(exp.start_date)
      const end = exp.end_date ? new Date(exp.end_date) : new Date()
      const months = (end.getFullYear() - start.getFullYear()) * 12 + 
                    (end.getMonth() - start.getMonth())
      totalMonths += Math.max(0, months)
    }
  })
  
  return Math.floor(totalMonths / 12)
}

const generatePersonalizedSummary = (jobTitle, years) => {
  const templates = {
    entry: `Enthusiastic ${jobTitle} with ${years} year${years !== 1 ? 's' : ''} of experience, passionate about leveraging cutting-edge technologies to deliver innovative solutions. Proven ability to adapt quickly in fast-paced environments while maintaining attention to detail and quality standards.`,
    mid: `Results-driven ${jobTitle} with ${years} years of progressive experience in developing scalable solutions and leading cross-functional teams. Demonstrated expertise in strategic problem-solving and stakeholder management, with a track record of delivering projects that exceed expectations.`,
    senior: `Senior ${jobTitle} with ${years}+ years of comprehensive experience architecting enterprise-level solutions and mentoring high-performing teams. Strategic leader with deep technical expertise and proven ability to drive digital transformation initiatives that deliver measurable business impact.`
  }
  
  if (years <= 2) return templates.entry
  if (years <= 7) return templates.mid
  return templates.senior
}

const getIndustrySpecificSkills = () => {
  const jobTitle = cv.value.experience[0]?.position?.toLowerCase() || ''
  
  const skillMaps = {
    'software': ['React', 'Node.js', 'TypeScript', 'Docker', 'AWS', 'GraphQL', 'MongoDB'],
    'design': ['Figma', 'Adobe Creative Suite', 'Sketch', 'Prototyping', 'User Research', 'Wireframing'],
    'marketing': ['Google Analytics', 'SEO/SEM', 'Content Strategy', 'Social Media', 'Email Marketing'],
    'data': ['Python', 'SQL', 'Tableau', 'Machine Learning', 'Statistics', 'R', 'Power BI'],
    'project': ['Agile/Scrum', 'Jira', 'Risk Management', 'Stakeholder Management', 'Budget Planning']
  }
  
  for (const [key, skills] of Object.entries(skillMaps)) {
    if (jobTitle.includes(key)) {
      return skills.filter(skill => !cv.value.skills.some(existing => 
        existing.name.toLowerCase() === skill.toLowerCase()
      ))
    }
  }
  
  return ['Communication', 'Problem Solving', 'Team Collaboration', 'Time Management']
}

// Drag and drop functionality removed for simplicity



// Optimized main save function
const saveCv = async (status) => {
  if (loading.value || autoSaveInProgress.value) return
  
  loading.value = true
  loadingMessage.value = status === 'published' ? 'Finalizing your CV...' : 'Saving your CV...'
  
  try {
    const token = localStorage.getItem('auth_token')
    if (!token) {
      showMessage('Please login to save your CV', 'error')
      router.push('/login')
      return
    }

    // Enhanced validation
    const personalInfo = cv.value.personalInfo || {}
    if (!cv.value.title?.trim()) {
      showMessage('Please add a title for your CV', 'error')
      return
    }
    if (!personalInfo.firstName?.trim()) {
      showMessage('Please add your first name', 'error')
      return
    }
    if (!personalInfo.email?.trim()) {
      showMessage('Please add your email address', 'error')
      return
    }

    const payload = createOptimizedSavePayload(status === 'published')

    console.log('Saving CV with optimized payload:', payload)

    const response = await axios.post('/api/cvs', payload, {
      headers: { 
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      timeout: 20000 // Increased timeout for finalization
    })

    console.log('CV save response:', response.data)

    if (response.data.success) {
      // Store the CV ID for later use
      if (response.data.cv?.id) {
        savedCvId.value = response.data.cv.id
      }
      
      const successMessage = status === 'published' ? 'CV created successfully!' : 'CV saved successfully!'
      showMessage(successMessage, 'success')
      
      if (status === 'published') {
        // Small delay before showing modal for better UX
        setTimeout(() => {
          showSuccessModal.value = true
        }, 500)
      } else {
        // Auto-clear success message for drafts
        setTimeout(() => {
          if (message.value.type === 'success') {
            message.value.text = ''
          }
        }, 4000)
      }
    } else {
      const errorMsg = response.data.message || 'Failed to save CV'
      showMessage(`Error: ${errorMsg}`, 'error')
      console.error('Save failed:', response.data)
    }
  } catch (error) {
    console.error('Error saving CV:', error)
    
    if (error.response) {
      const status = error.response.status
      const data = error.response.data
      
      if (status === 401) {
        showMessage('Session expired. Please login again.', 'error')
        localStorage.removeItem('auth_token')
        router.push('/login')
      } else if (status === 422) {
        // Enhanced validation error handling
        const errors = data.errors || {}
        const errorMessages = []
        
        Object.keys(errors).forEach(field => {
          const fieldErrors = Array.isArray(errors[field]) ? errors[field] : [errors[field]]
          errorMessages.push(...fieldErrors)
        })
        
        if (errorMessages.length > 0) {
          showMessage('Please fix: ' + errorMessages.slice(0, 3).join(', '), 'error')
        } else {
          showMessage('Please check your information and try again.', 'error')
        }
      } else if (status === 413) {
        showMessage('CV data is too large. Please reduce content size.', 'error')
      } else if (status >= 500) {
        showMessage('Server error. Please try again in a few moments.', 'error')
      } else {
        showMessage(data.message || 'Failed to save CV. Please try again.', 'error')
      }
    } else if (error.code === 'ECONNABORTED') {
      showMessage('Save timeout. Please check your connection and try again.', 'error')
    } else if (error.request) {
      showMessage('Network error. Please check your connection.', 'error')
    } else {
      showMessage('Unexpected error. Please try again.', 'error')
    }
  } finally {
    loading.value = false
    loadingMessage.value = 'Processing...'
  }
}

const addExperience = () => {
  console.log('addExperience called')
  if (!cv.value.experience) cv.value.experience = []
  cv.value.experience.push({
    position: '',
    company: '',
    start_date: '',
    end_date: '',
    description: '',
    current: false
  })
  console.log('Experience added, new length:', cv.value.experience.length)
  // Don't trigger auto-save immediately to avoid validation issues
  // triggerAutoSave()
}

const removeExperience = (index) => {
  if (cv.value.experience && cv.value.experience[index]) {
    cv.value.experience.splice(index, 1)
    triggerAutoSave()
  }
}

const addEducation = () => {
  console.log('addEducation called')
  if (!cv.value.education) cv.value.education = []
  cv.value.education.push({
    degree: '',
    institution: '',
    start_date: '',
    end_date: '',
    gpa: '',
    description: ''
  })
  console.log('Education added, new length:', cv.value.education.length)
  // Don't trigger auto-save immediately to avoid validation issues
  // triggerAutoSave()
}

const removeEducation = (index) => {
  if (cv.value.education && cv.value.education[index]) {
    cv.value.education.splice(index, 1)
    triggerAutoSave()
  }
}

// Removed duplicate methods - using the ones defined earlier



const isSkillAlreadyAdded = (skillName) => {
  return cv.value.skills.some(skill => skill.name.toLowerCase() === skillName.toLowerCase())
}

// Removed step-based validation and navigation methods



// Simplified AI functionality

// Clean AI content generation
const generateSmartContent = async (type, context = {}) => {
  isGeneratingAI.value = true
  
  try {
    await new Promise(resolve => setTimeout(resolve, 2500))
    
    if (type === 'summary') {
      const jobTitle = cv.value.experience[0]?.position || 'Professional'
      const years = calculateYearsOfExperience()
      const skills = cv.value.skills.map(s => s.name).join(', ')
      
      let generatedSummary = ''
      
      if (years <= 2) {
        generatedSummary = `Dynamic and motivated ${jobTitle} with ${years} year${years !== 1 ? 's' : ''} of hands-on experience. Passionate about leveraging modern technologies and best practices to create innovative solutions. Strong foundation in ${skills || 'core technical skills'} with a commitment to continuous learning and professional growth.`
      } else if (years <= 7) {
        generatedSummary = `Results-driven ${jobTitle} with ${years} years of progressive experience in delivering high-quality solutions and leading collaborative projects. Proven expertise in ${skills || 'industry-standard technologies'} with a track record of exceeding expectations and driving measurable business outcomes.`
      } else {
        generatedSummary = `Senior ${jobTitle} with ${years}+ years of comprehensive experience architecting enterprise solutions and mentoring high-performing teams. Deep expertise in ${skills || 'cutting-edge technologies'} with proven ability to drive digital transformation initiatives and deliver strategic business value.`
      }
      
      // Simple content replacement
      cv.value.summary = generatedSummary
    }
    
    onInputChange()
    
  } finally {
    isGeneratingAI.value = false
  }
}

// Removed duplicate function - using calculateYearsOfExperience above

// Duplicate function removed - using the original validateField above

// Using the generateAISummary method defined earlier

// Removed particle system for cleaner aesthetic

// Removed old step-based navigation methods

// Using the enhanceExperienceDescription method defined earlier

// Template methods
const onTemplateChange = () => {
  onInputChange()
}

// Preview methods - using the ones defined earlier

// Auto-save functionality - heavily optimized
const canAutoSave = computed(() => {
  if (!cv.value || autoSaveInProgress.value) return false
  const personalInfo = cv.value.personalInfo || {}
  // Very lenient - just need firstName and email
  return personalInfo.firstName && personalInfo.email && cv.value.title
})

// Debounced progress calculation for performance
let progressTimer = null
const debouncedCalculateProgress = () => {
  if (progressTimer) {
    clearTimeout(progressTimer)
  }
  progressTimer = setTimeout(() => {
    // Trigger reactive update efficiently
    cachedProgress = -1 // Force recalculation
    overallProgress.value // Access to trigger update
  }, 200) // Reduced timer for better UX
}

// Optimized input change handler
const onInputChange = () => {
  // Debounced progress update
  debouncedCalculateProgress()
  
  // Only trigger auto-save if we have basic required info
  const personalInfo = cv.value?.personalInfo || {}
  if (!personalInfo.firstName || !personalInfo.email || !cv.value.title) {
    return
  }
  
  // Don't trigger if already saving or recently saved
  if (autoSaveStatus.value === 'saving' || autoSaveInProgress.value) return
  
  // Clear existing timers
  if (autoSaveTimer.value) {
    clearTimeout(autoSaveTimer.value)
  }
  if (saveDebounceTimer.value) {
    clearTimeout(saveDebounceTimer.value)
  }
  
  // Longer delay to reduce server load and improve performance
  saveDebounceTimer.value = setTimeout(() => {
    autoSave()
  }, 8000) // Increased to 8 seconds for better performance
}

// Optimized auto-save with better error handling and MySQL optimization
const autoSave = async () => {
  if (autoSaveStatus.value === 'saving' || autoSaveInProgress.value || !canAutoSave.value) return
  
  // Additional validation before attempting save
  const personalInfo = cv.value?.personalInfo || {}
  if (!personalInfo.firstName || !personalInfo.email || !cv.value.title) {
    return
  }
  
  autoSaveStatus.value = 'saving'
  autoSaveInProgress.value = true
  
  try {
    const token = localStorage.getItem('auth_token')
    if (!token) {
      autoSaveStatus.value = 'idle'
      autoSaveInProgress.value = false
      return
    }

    const payload = createOptimizedSavePayload(false)
    
    // Final validation before API call
    if (!payload.title || !payload.personalDetails?.firstName || !payload.personalDetails?.email) {
      autoSaveStatus.value = 'idle'
      autoSaveInProgress.value = false
      return
    }
    
    const response = await axios.post('/api/cvs', payload, {
      headers: { 
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      timeout: 15000 // Increased timeout for larger payloads
    })

    if (response.data.success) {
      autoSaveStatus.value = 'saved'
      lastSaved.value = new Date()
      if (response.data.cv?.id) {
        savedCvId.value = response.data.cv.id
      }
      
      // Clear saved status after 4 seconds
      setTimeout(() => {
        if (autoSaveStatus.value === 'saved') {
          autoSaveStatus.value = 'idle'
        }
      }, 4000)
    } else {
      autoSaveStatus.value = 'error'
      console.warn('Auto-save failed:', response.data.message)
      setTimeout(() => {
        if (autoSaveStatus.value === 'error') {
          autoSaveStatus.value = 'idle'
        }
      }, 4000)
    }
  } catch (error) {
    autoSaveStatus.value = 'error'
    
    // Only log significant errors, avoid spam
    if (error.response?.status !== 422) {
      console.warn('Auto-save error:', error.message)
    }
    
    setTimeout(() => {
      if (autoSaveStatus.value === 'error') {
        autoSaveStatus.value = 'idle'
      }
    }, 4000)
  } finally {
    autoSaveInProgress.value = false
  }
}

// AI Settings
const updateAISettings = (settings) => {
  // Handle AI enhancement settings
  console.log('AI Settings updated:', settings)
}

// Helper function to format dates for API (converts "2025-06" to "2025-06-01")
const formatDateForAPI = (dateStr) => {
  if (!dateStr) return ''
  
  // If it's already a full date, return as is
  if (dateStr.includes('-') && dateStr.split('-').length === 3) {
    return dateStr
  }
  
  // If it's just year-month (from month input), add day
  if (dateStr.match(/^\d{4}-\d{2}$/)) {
    return dateStr + '-01'
  }
  
  // If it's just a year, add month and day
  if (dateStr.match(/^\d{4}$/)) {
    return dateStr + '-01-01'
  }
  
  return dateStr
}

// Navigation methods
const goBack = () => {
  router.push('/dashboard')
}

const goToDashboard = () => {
  router.push('/dashboard')
}

const previewCV = () => {
  if (savedCvId.value) {
    router.push(`/cv/${savedCvId.value}/preview`)
  }
}

const closeSuccessModal = () => {
  showSuccessModal.value = false
}

// Utility methods
const showMessage = (text, type = 'success') => {
  message.value = { text, type }
  
  // Auto-clear messages based on type
  const clearTime = type === 'error' ? 6000 : type === 'success' ? 4000 : 3000
  setTimeout(() => {
    if (message.value.text === text) { // Only clear if message hasn't changed
      message.value = { text: '', type: '' }
    }
  }, clearTime)
}

// Performance monitoring
const logPerformanceMetric = (metric, value) => {
  if (process.env.NODE_ENV === 'development') {
    console.log(`CV Creator Performance - ${metric}:`, value)
  }
}

// Removed step descriptions for single-page form

// Initialize component
onMounted(() => {
  console.log('Component mounted, CV data:', cv.value)
  // Don't auto-add entries - let user add them manually
  // Skills and interests start empty by default to encourage user input
})

// Optimized cleanup and performance improvements
onUnmounted(() => {
  // Clear all timers to prevent memory leaks
  if (autoSaveTimer.value) {
    clearTimeout(autoSaveTimer.value)
    autoSaveTimer.value = null
  }
  if (saveDebounceTimer.value) {
    clearTimeout(saveDebounceTimer.value)
    saveDebounceTimer.value = null
  }
  if (progressTimer) {
    clearTimeout(progressTimer)
    progressTimer = null
  }
  
  // Reset performance optimization flags
  autoSaveInProgress.value = false
  isGeneratingAI.value = false
  
  // Clear cached values
  cachedProgress = 0
  cachedCompletionScore = 0
  cachedSkillSuggestions = []
  lastProgressUpdate = 0
  lastCompletionUpdate = 0
  lastSkillUpdate = 0
})

// Enhanced mobile detection
const updateMobileState = () => {
  const newIsMobile = window.innerWidth <= 768
  if (newIsMobile !== isMobile.value) {
    isMobile.value = newIsMobile
    // Auto-hide preview on mobile for performance
    if (newIsMobile && showPreview.value) {
      showPreview.value = false
    }
  }
}

// Optimized save methods for better MySQL performance
const createOptimizedSavePayload = (isFinalized = false) => {
  // Pre-filter and validate data to reduce payload size
  const personalInfo = cv.value.personalInfo || {}
  const validExperience = (cv.value.experience || []).filter(exp => 
    exp.position && exp.position.trim() && exp.company && exp.company.trim()
  )
  const validEducation = (cv.value.education || []).filter(edu => 
    edu.degree && edu.degree.trim() && edu.institution && edu.institution.trim()
  )
  const validSkills = (cv.value.skills || []).filter(skill => 
    skill.name && skill.name.trim()
  )
  const validInterests = (cv.value.interests || []).filter(interest => 
    interest.name && interest.name.trim()
  )

  return {
    title: (cv.value.title || '').trim() || 'Untitled CV',
    personalDetails: {
      firstName: (personalInfo.firstName || '').trim(),
      lastName: (personalInfo.lastName || '').trim(),
      email: (personalInfo.email || '').trim(),
      phone: (personalInfo.phone || '').trim(),
      address: (personalInfo.address || '').trim()
    },
    summary: (cv.value.summary || '').trim(),
    experience: validExperience.map(exp => ({
      title: exp.position.trim(),
      company: exp.company.trim(),
      startDate: formatDateForAPI(exp.start_date),
      endDate: exp.current ? null : formatDateForAPI(exp.end_date),
      description: (exp.description || '').trim(),
      isCurrent: Boolean(exp.current)
    })),
    education: validEducation.map(edu => ({
      degree: edu.degree.trim(),
      university: edu.institution.trim(),
      startDate: formatDateForAPI(edu.start_date),
      endDate: formatDateForAPI(edu.end_date),
      gpa: edu.gpa ? parseFloat(edu.gpa) : null
    })),
    skills: validSkills.map(skill => ({
      name: skill.name.trim(),
      level: skill.level || 3
    })),
    interests: validInterests.map(interest => ({
      name: interest.name.trim()
    })),
    selectedTemplate: cv.value.selectedTemplate || 'modern',
    isFinalized: Boolean(isFinalized),
    status: isFinalized ? 'published' : 'draft'
  }
}

// Legacy function for backward compatibility
const createSavePayload = (isFinalized = false) => {
  return createOptimizedSavePayload(isFinalized)
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

/* CSS Variables for Theme Support */
:root {
  --bg-primary: #ffffff;
  --bg-secondary: #fdfdfd;
  --bg-tertiary: #f8fafc;
  --text-primary: #0f172a;
  --text-secondary: #64748b;
  --text-muted: #94a3b8;
  --border-color: rgba(226, 232, 240, 0.8);
  --accent-primary: #3b82f6;
  --accent-secondary: #8b5cf6;
  --shadow-light: rgba(0, 0, 0, 0.08);
  --shadow-medium: rgba(0, 0, 0, 0.12);
}

/* Dark Theme Variables */
.dark {
  --bg-primary: #0f172a;
  --bg-secondary: #1e293b;
  --bg-tertiary: #334155;
  --text-primary: #f8fafc;
  --text-secondary: #cbd5e1;
  --text-muted: #94a3b8;
  --border-color: rgba(71, 85, 105, 0.3);
  --accent-primary: #60a5fa;
  --accent-secondary: #a78bfa;
  --shadow-light: rgba(0, 0, 0, 0.3);
  --shadow-medium: rgba(0, 0, 0, 0.4);
}

.cv-creator {
  min-height: 100vh;
  background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--border-color) 100%);
  display: flex;
  flex-direction: column;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  transition: all 0.3s ease;
}

/* Message Notification */
.message-notification {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.25rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  font-weight: 500;
  animation: slideInDown 0.3s ease-out;
}

.message-notification.success {
  background: linear-gradient(135deg, #ecfdf5, #d1fae5);
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.message-notification.error {
  background: linear-gradient(135deg, #fef2f2, #fee2e2);
  color: #991b1b;
  border: 1px solid #fca5a5;
}

.message-notification.info {
  background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
  color: #0c4a6e;
  border: 1px solid #bae6fd;
}

@keyframes slideInDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Enhanced Card Styles */
.experience-card, .education-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  margin-bottom: 1.5rem;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  text-align: left; /* Cards content should be left-aligned */
}

.experience-card:hover, .education-card:hover {
  border-color: #3b82f6;
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.1);
  transform: translateY(-2px);
}

.card-header {
  background: linear-gradient(135deg, #f8fafc, #f1f5f9);
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  border-bottom: 1px solid #e2e8f0;
}

.card-number {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.875rem;
}

.card-header h4 {
  flex: 1;
  margin: 0;
  font-weight: 600;
  color: #1e293b;
  font-size: 1.1rem;
}

.remove-btn {
  background: none;
  border: none;
  color: #ef4444;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  transition: all 0.2s ease;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.remove-btn:hover {
  background: rgba(239, 68, 68, 0.1);
  transform: scale(1.1);
}

.card-content {
  padding: 1.5rem;
}

/* Empty States */
.empty-state {
  text-align: center;
  padding: 3rem 2rem;
  background: linear-gradient(135deg, #f8fafc, #f1f5f9);
  border-radius: 16px;
  border: 2px dashed #cbd5e1;
  margin-bottom: 2rem;
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.empty-state h3 {
  margin: 0 0 0.5rem 0;
  font-weight: 600;
  color: #374151;
  font-size: 1.25rem;
}

.empty-state p {
  margin: 0 0 1.5rem 0;
  color: #64748b;
}

.mini-empty-state {
  text-align: center;
  padding: 2rem 1rem;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px dashed #cbd5e1;
  margin-bottom: 1rem;
}

.mini-empty-state p {
  margin: 0 0 1rem 0;
  color: #64748b;
  font-size: 0.9rem;
}

/* Enhanced Buttons */
.add-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 1rem;
}

.add-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
}

.add-btn.primary {
  font-size: 1.1rem;
  padding: 1.25rem 2.5rem;
}

.add-more-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.5rem;
  background: white;
  color: #3b82f6;
  border: 1px solid #3b82f6;
  border-radius: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 1rem;
}

.add-more-btn:hover {
  background: #3b82f6;
  color: white;
  transform: translateY(-1px);
}

.ai-enhance-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.ai-enhance-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.ai-enhance-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* AI Templates */
.ai-templates {
  background: linear-gradient(135deg, #faf5ff, #f3e8ff);
  border: 1px solid #e9d5ff;
  border-radius: 16px;
  padding: 1.5rem;
  margin-top: 1.5rem;
}

.ai-templates h4 {
  margin: 0 0 0.5rem 0;
  font-weight: 600;
  color: #7c3aed;
}

.ai-templates p {
  margin: 0 0 1rem 0;
  color: #64748b;
  font-size: 0.9rem;
}

.template-options {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.summary-template {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.summary-template:hover {
  border-color: #8b5cf6;
  background: rgba(139, 92, 246, 0.05);
  transform: translateY(-1px);
}

.template-content {
  font-size: 0.9rem;
  line-height: 1.5;
  color: #374151;
}

/* Skills & Interests */
.skills-input, .interests-input {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 1rem;
  text-align: left; /* Keep input areas left-aligned */
}

.skills-input input, .interests-input input {
  flex: 1;
  padding: 0.875rem 1.25rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.skills-input input:focus, .interests-input input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  outline: none;
}

.add-skill-btn, .add-interest-btn {
  padding: 0.875rem 1.25rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.add-skill-btn:hover, .add-interest-btn:hover {
  background: #2563eb;
  transform: translateY(-1px);
}

.skills-list, .interests-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.skill-tag, .interest-tag {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, #dbeafe, #bfdbfe);
  color: #1e40af;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
}

.skill-tag button, .interest-tag button {
  background: none;
  border: none;
  color: #1e40af;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  transition: all 0.2s ease;
}

.skill-tag button:hover, .interest-tag button:hover {
  background: rgba(30, 64, 175, 0.2);
}

/* Skill Suggestions */
.skill-suggestions {
  background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
  border: 1px solid #7dd3fc;
  border-radius: 16px;
  padding: 1.5rem;
  margin-top: 1.5rem;
}

.skill-suggestions h4 {
  margin: 0 0 1rem 0;
  font-weight: 600;
  color: #0369a1;
}

.suggestions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 0.75rem;
}

.suggestion-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
}

.suggestion-btn:not(:disabled):hover {
  border-color: #3b82f6;
  background: #3b82f6;
  color: white;
  transform: translateY(-1px);
}

.suggestion-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background: #f1f5f9;
}

/* Review Step */
.review-section {
  max-width: 800px;
  margin: 0 auto;
}

.completion-score {
  display: flex;
  align-items: center;
  gap: 2rem;
  background: white;
  border-radius: 20px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
}

.score-circle {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  flex-shrink: 0;
}

.score-circle.excellent {
  background: linear-gradient(135deg, #10b981, #059669);
}

.score-circle.good {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.score-circle.fair {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}

.score-circle.poor {
  background: linear-gradient(135deg, #ef4444, #dc2626);
}

.score-number {
  font-size: 2rem;
  line-height: 1;
}

.score-label {
  font-size: 0.875rem;
  opacity: 0.9;
}

.score-details h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.score-details p {
  margin: 0;
  color: #64748b;
  line-height: 1.5;
}

.section-checklist {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
}

.section-checklist h3 {
  margin: 0 0 1.5rem 0;
  font-weight: 600;
  color: #1e293b;
}

.checklist-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.checklist-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 12px;
  border-left: 4px solid #e2e8f0;
}

.checklist-item.complete {
  border-left-color: #10b981;
  background: linear-gradient(135deg, #ecfdf5, #d1fae5);
}

.checklist-item.incomplete {
  border-left-color: #f59e0b;
  background: linear-gradient(135deg, #fffbeb, #fef3c7);
}

.checklist-item i {
  width: 24px;
  text-align: center;
}

.checklist-item.complete i {
  color: #10b981;
}

.checklist-item.incomplete i {
  color: #f59e0b;
}

.checklist-item span {
  flex: 1;
  font-weight: 500;
  color: #374151;
}

.item-status {
  font-size: 0.875rem;
  color: #64748b;
  font-weight: 500;
}

.final-actions {
  text-align: center;
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
}

.finalize-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1.25rem 2.5rem;
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  border: none;
  border-radius: 16px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-bottom: 1rem;
}

.finalize-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
}

.finalize-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.final-note {
  margin: 0;
  color: #64748b;
  font-size: 0.9rem;
}

/* Enhanced Preview Panel */
.preview-panel {
  background: white;
  border-radius: 20px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  border: 1px solid #e2e8f0;
  position: sticky;
  top: 2rem;
  max-height: calc(100vh - 4rem);
}

.preview-header {
  padding: 1.5rem;
  background: linear-gradient(135deg, #f8fafc, #f1f5f9);
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.preview-header h3 {
  margin: 0;
  font-weight: 600;
  color: #1e293b;
}

.preview-actions {
  display: flex;
  gap: 0.75rem;
}

.download-btn, .close-preview {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.download-btn:hover, .close-preview:hover {
  background: #2563eb;
  transform: translateY(-1px);
}

.preview-container {
  padding: 1.5rem;
  overflow-y: auto;
  max-height: calc(100vh - 8rem);
}

.preview-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 300px;
  color: #94a3b8;
  text-align: center;
  background: linear-gradient(135deg, #f8fafc, #f1f5f9);
  border-radius: 12px;
  border: 2px dashed #cbd5e1;
}

.preview-placeholder i {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.preview-placeholder p {
  margin: 0;
  font-size: 1rem;
  font-weight: 500;
}

/* Mobile Enhancements */
@media (max-width: 768px) {
  .form-container {
    padding: 2rem 1.5rem;
  }
  
  .completion-score {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .score-circle {
    width: 100px;
    height: 100px;
  }
  
  .score-number {
    font-size: 1.75rem;
  }
  
  .template-grid {
    grid-template-columns: 1fr;
  }
  
  .suggestions-grid {
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
  }
  
  .nav-btn {
    padding: 0.875rem 1.5rem;
    font-size: 0.9rem;
  }
  
  .checklist-item {
    padding: 0.75rem;
  }
  
  .ai-assistant {
    bottom: 1rem;
    right: 1rem;
    left: 1rem;
    width: auto;
  }
}

/* Additional hover effects */
.form-group input:focus,
.form-group textarea:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  outline: none;
}

.form-group input,
.form-group textarea {
  transition: all 0.3s ease;
}

/* Essential Enhanced Styles */
.onboarding-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(8px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.onboarding-content {
  background: white;
  border-radius: 24px;
  padding: 3rem;
  max-width: 600px;
  width: 100%;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
  text-align: center;
}

.onboarding-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
}

.onboarding-header h2 {
  margin: 0;
  font-size: 2rem;
  font-weight: 700;
  color: #1e293b;
}

.onboarding-steps {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-bottom: 2.5rem;
}

.onboarding-step {
  display: flex;
  align-items: center;
  gap: 1rem;
  text-align: left;
  padding: 1rem;
  border-radius: 12px;
  background: #f8fafc;
}

.step-icon {
  font-size: 2rem;
  flex-shrink: 0;
}

.start-btn {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: 16px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  justify-content: center;
}

.start-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
}

.progress-container {
  padding: 1rem 2rem 0;
  max-width: 1400px;
  margin: 0 auto;
}

.progress-bar {
  height: 4px;
  background: #f1f5f9;
  border-radius: 2px;
  overflow: hidden;
  margin-bottom: 0.5rem;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #3b82f6, #10b981);
  border-radius: 2px;
  transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.progress-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.875rem;
  color: #64748b;
}

.step-navigation {
  padding: 1rem 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.steps-container {
  display: flex;
  justify-content: center;
  gap: 2rem;
}

.step-indicator {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.step-indicator.clickable:hover {
  transform: translateY(-2px);
}

.step-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.875rem;
  transition: all 0.3s ease;
  border: 2px solid #e2e8f0;
  background: white;
  color: #64748b;
}

.step-indicator.completed .step-circle {
  background: #10b981;
  border-color: #10b981;
  color: white;
}

.step-indicator.active .step-circle {
  background: #3b82f6;
  border-color: #3b82f6;
  color: white;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.step-label {
  font-size: 0.75rem;
  font-weight: 500;
  color: #64748b;
}

.step-indicator.active .step-label {
  color: #3b82f6;
  font-weight: 600;
}

.focus-indicator {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  color: #166534;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 500;
  border: 1px solid #86efac;
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

.save-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 500;
}

.save-status.saving {
  background: #fef3c7;
  color: #92400e;
}

.save-status.saved {
  background: #d1fae5;
  color: #065f46;
}

.save-status.error {
  background: #fee2e2;
  color: #991b1b;
}





.form-step {
  animation: fadeInUp 0.5s ease-out;
}

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

.step-intro {
  margin-bottom: 2rem;
  text-align: center;
}

.step-intro h2 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 0.5rem 0;
}

.step-intro p {
  color: #64748b;
  font-size: 1rem;
  margin: 0;
}

.step-navigation-buttons {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 2.5rem auto 0 auto; /* Center the navigation */
  padding: 2rem 2.5rem;
  background: linear-gradient(135deg, var(--bg-primary), var(--bg-secondary));
  border-radius: 16px;
  width: 100%;
  max-width: 850px; /* Match form section width */
  backdrop-filter: blur(10px);
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 8px var(--shadow-light);
}

.nav-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1.25rem 2.5rem;
  border-radius: 20px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 1rem;
  letter-spacing: 0.025em;
  position: relative;
  overflow: hidden;
}

.nav-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.6s ease;
}

.nav-btn:hover::before {
  left: 100%;
}

.nav-btn.primary {
  background: linear-gradient(135deg, var(--accent-primary), #2563eb, #1d4ed8);
  color: white;
  border: none;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.nav-btn.primary:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: 0 12px 30px rgba(59, 130, 246, 0.4);
  background: linear-gradient(135deg, #2563eb, #1d4ed8, #1e40af);
}

.nav-btn.secondary {
  background: linear-gradient(135deg, var(--bg-primary), var(--bg-tertiary));
  color: var(--text-secondary);
  border: 2px solid var(--border-color);
  box-shadow: 0 2px 8px var(--shadow-light);
}

.nav-btn.secondary:hover {
  background: linear-gradient(135deg, var(--bg-tertiary), var(--border-color));
  border-color: var(--accent-primary);
  color: var(--accent-primary);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.15);
}

.nav-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
  transform: none !important;
  box-shadow: none !important;
}

.spacer {
  flex: 1;
}

.mobile-preview-toggle {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  z-index: 100;
}

.preview-fab {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.preview-fab::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  transform: translate(-50%, -50%);
  transition: all 0.3s ease;
}

.preview-fab:hover {
  transform: scale(1.1) rotate(5deg);
  box-shadow: 0 12px 35px rgba(59, 130, 246, 0.5);
}

.preview-fab:hover::before {
  width: 100%;
  height: 100%;
}

.preview-fab.active {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
}

.preview-fab.active:hover {
  box-shadow: 0 12px 35px rgba(239, 68, 68, 0.5);
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(4px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
}

.loading-content {
  text-align: center;
}

.loading-spinner {
  width: 60px;
  height: 60px;
  border: 4px solid #f1f5f9;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.success-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(8px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.success-content {
  background: white;
  border-radius: 24px;
  padding: 3rem;
  max-width: 500px;
  width: 100%;
  text-align: center;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
  .creator-main {
    grid-template-columns: 1fr;
    gap: 1rem;
    padding: 1rem;
  }
  
  .creator-main.mobile-mode.preview-active .form-panel {
    display: none;
  }
  
  .step-navigation .steps-container {
    gap: 1rem;
  }
  
  .step-indicator .step-label {
    display: none;
  }
  
  .ai-assistant {
    bottom: 1rem;
    right: 1rem;
    left: 1rem;
    width: auto;
  }
}

/* Header */
.creator-header {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid var(--border-color);
  padding: 1.25rem 2rem;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 1px 3px var(--shadow-light);
}

/* Dark theme header */
.dark .creator-header {
  background: rgba(15, 23, 42, 0.95);
}

.header-content {
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.back-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.25rem;
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  color: var(--text-secondary);
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
  box-shadow: 0 1px 3px var(--shadow-light);
}

.back-btn:hover {
  background: var(--bg-tertiary);
  border-color: var(--accent-primary);
  color: var(--accent-primary);
  transform: translateY(-1px);
}

.header-info h1 {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 0.5rem 0;
  letter-spacing: -0.025em;
}

.completion-badge {
  font-size: 0.875rem;
  padding: 0.375rem 1rem;
  border-radius: 20px;
  font-weight: 600;
  letter-spacing: 0.025em;
  text-transform: uppercase;
  font-size: 0.75rem;
}

.completion-badge.low {
  background: linear-gradient(135deg, #fef3c7, #fde68a);
  color: #92400e;
}

.completion-badge.medium {
  background: linear-gradient(135deg, #dbeafe, #bfdbfe);
  color: #1e40af;
}

.completion-badge.high {
  background: linear-gradient(135deg, #d1fae5, #a7f3d0);
  color: #065f46;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.preview-toggle, .save-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.25rem;
  border: 1px solid var(--border-color);
  border-radius: 12px;
  background: var(--bg-primary);
  color: var(--text-secondary);
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
  box-shadow: 0 1px 3px var(--shadow-light);
}

.preview-toggle.active {
  background: linear-gradient(135deg, var(--accent-primary), #2563eb);
  color: white;
  border-color: var(--accent-primary);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.save-btn {
  background: var(--bg-tertiary);
}

.save-btn:hover {
  border-color: #10b981;
  color: #10b981;
}

/* Main Layout - Modern Centered Design */
.creator-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  max-width: 1200px; /* Standardized width */
  margin: 0 auto;
  width: 100%;
  gap: 2rem;
  padding: 2rem;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  align-items: center;
}

/* When preview is on - keep consistent width */
.creator-main.preview-stacked {
  max-width: 1200px; /* Consistent width */
}

/* Legacy class for backward compatibility */
.creator-main.form-expanded {
  max-width: 1200px;
}

.form-panel {
  background: linear-gradient(135deg, var(--bg-primary, #ffffff), var(--bg-secondary, #fdfdfd));
  border-radius: 24px; /* Reduced border radius for better proportion */
  box-shadow: 
    0 10px 40px rgba(0, 0, 0, 0.08),
    0 4px 12px rgba(0, 0, 0, 0.04);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
  border: 1px solid var(--border-color, rgba(226, 232, 240, 0.8));
  width: 100%;
  max-width: 1000px; /* Standardized form panel width */
  position: relative;
}

/* Dark theme form panel */
.dark .form-panel {
  box-shadow: 
    0 20px 60px var(--shadow-medium),
    0 8px 24px var(--shadow-medium),
    inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

/* Ensure form sections content is properly aligned */
.form-section > .form-grid {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.form-section > .form-grid > .form-group {
  width: 100%;
  max-width: 100%;
}

/* Center all form section content */
.form-section .template-grid,
.form-section .skills-list,
.form-section .experience-item,
.form-section .education-item,
.form-section .empty-state {
  width: 100%;
  max-width: 100%;
}

.form-panel::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.3), transparent);
}

.form-container {
  padding: 3rem 2.5rem;
  max-width: 900px; /* Standardized container width */
  margin: 0 auto;
  transition: all 0.4s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* Enhanced form container when preview is active */
.creator-main.preview-stacked .form-container {
  padding: 3rem 2.5rem;
  max-width: 900px; /* Keep consistent width */
}

/* Focus mode - same consistent width */
.creator-main:not(.preview-stacked) .form-container {
  padding: 3rem 2.5rem;
  max-width: 900px; /* Consistent width for comfortable editing */
}

/* Template Switcher */
.template-switcher {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin: 0 auto 3rem auto;
  padding: 1.5rem;
  background: linear-gradient(135deg, var(--bg-tertiary), var(--bg-secondary));
  border-radius: 16px;
  border: 1px solid var(--border-color);
  width: 100%;
  max-width: 1000px;
}

.switcher-label {
  font-weight: 600;
  color: var(--text-primary);
  font-size: 1rem;
}

.template-options {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.template-option {
  padding: 0.75rem 1.5rem;
  border: 1px solid var(--border-color);
  border-radius: 12px;
  background: var(--bg-primary);
  color: var(--text-secondary);
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.875rem;
  font-weight: 500;
  box-shadow: 0 1px 3px var(--shadow-light);
}

.template-option.active {
  background: linear-gradient(135deg, var(--accent-primary), #2563eb);
  color: white;
  border-color: var(--accent-primary);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.template-option:hover:not(.active) {
  border-color: var(--accent-primary);
  color: var(--accent-primary);
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
}

/* Premium Form Sections */
.form-section {
  margin-bottom: 3rem;
  padding: 2.5rem 2rem;
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  background: linear-gradient(135deg, var(--bg-primary), var(--bg-secondary));
  border-radius: 16px;
  border: 1px solid var(--border-color);
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 12px var(--shadow-light);
  width: 100%;
  max-width: 850px; /* Consistent max width */
}

.form-section:not(:last-child)::after {
  content: '';
  position: absolute;
  bottom: -1rem;
  left: 50%;
  transform: translateX(-50%);
  width: 120px;
  height: 4px;
  background: linear-gradient(90deg, transparent, #3b82f6, #8b5cf6, transparent);
  border-radius: 2px;
  opacity: 0.6;
}

.form-section:last-child {
  margin-bottom: 2rem;
}

/* Premium Typography */
.step-intro {
  text-align: center;
  margin-bottom: 2.5rem;
  padding: 0 1rem;
  width: 100%;
  max-width: 700px;
}

.step-intro h2 {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.75rem;
  letter-spacing: -0.02em;
  line-height: 1.3;
}

.step-intro p {
  font-size: 1.1rem;
  color: var(--text-secondary);
  margin: 0;
  font-weight: 400;
  line-height: 1.5;
  max-width: 500px;
  margin: 0 auto;
}

/* Premium Section Headers */
.form-section h3 {
  font-size: 1.75rem;
  font-weight: 700;
  background: linear-gradient(135deg, var(--text-primary), var(--text-secondary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 2.5rem;
  text-align: center;
  position: relative;
}

.form-section h3::after {
  content: '';
  position: absolute;
  bottom: -0.75rem;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
  border-radius: 2px;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
}

.section-header h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
  letter-spacing: -0.025em;
}

.optional {
  color: #94a3b8;
  font-weight: 400;
  font-size: 0.875rem;
}

.section-status {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.section-status.complete {
  background: linear-gradient(135deg, #dcfce7, #bbf7d0);
  color: #16a34a;
}

.section-status.incomplete {
  background: linear-gradient(135deg, #fef3c7, #fde68a);
  color: #d97706;
}

.section-status.optional {
  background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
  color: #64748b;
}

/* Premium Form Grid */
.form-grid {
  display: grid;
  gap: 1.5rem;
  margin-bottom: 2rem;
  transition: all 0.4s ease;
  width: 100%;
  max-width: 750px; /* Consistent width */
  margin: 0 auto 2rem auto; /* Center the grid */
}

.grid-2 {
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.grid-3 {
  grid-template-columns: 1fr 1fr 1fr;
  gap: 1.5rem;
}

/* Form group enhancements */
.form-group {
  position: relative;
}

.form-group:focus-within label {
  color: #3b82f6;
  opacity: 1;
  transform: translateY(-1px);
}

/* Responsive grid adjustments */
@media (max-width: 768px) {
  .grid-2,
  .grid-3 {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
}

/* Enhanced spacing when preview is active */
.creator-main.preview-stacked .form-grid {
  gap: 2.5rem;
}

.creator-main.preview-stacked .form-section {
  margin-bottom: 5rem;
  padding-bottom: 4rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  text-align: left;
  width: 100%;
}

.form-group label {
  font-weight: 600;
  color: var(--text-secondary);
  margin-bottom: 0.75rem;
  font-size: 0.875rem;
  letter-spacing: 0.025em;
  display: block;
  text-transform: uppercase;
  opacity: 0.9;
  transition: all 0.3s ease;
  text-align: left;
}

.checkbox-label {
  flex-direction: row !important;
  align-items: center;
  gap: 0.75rem;
}

/* Premium Form Inputs */
.form-group input,
.form-group textarea {
  padding: 1.25rem 1.75rem;
  border: 2px solid var(--border-color);
  border-radius: 20px;
  font-size: 1rem;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  background: linear-gradient(135deg, var(--bg-primary), var(--bg-secondary));
  font-family: inherit;
  font-weight: 500;
  color: var(--text-primary);
  width: 100%;
  box-sizing: border-box;
  position: relative;
  box-shadow: 0 2px 4px var(--shadow-light);
}

.form-group input:hover,
.form-group textarea:hover {
  border-color: var(--text-muted);
  box-shadow: 0 4px 12px var(--shadow-light);
  transform: translateY(-1px);
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--accent-primary);
  box-shadow: 0 0 0 8px rgba(59, 130, 246, 0.12), 0 8px 24px rgba(59, 130, 246, 0.15);
  background: var(--bg-primary);
  transform: translateY(-3px);
}

.form-group input::placeholder,
.form-group textarea::placeholder {
  color: var(--text-muted);
  font-weight: 400;
}

/* Large input styling */
.large-input {
  font-size: 1.1rem;
  padding: 1.5rem 2rem;
  font-weight: 600;
}

.large-textarea {
  font-size: 1rem;
  line-height: 1.6;
  min-height: 140px;
  resize: vertical;
}

.form-actions {
  margin-top: 1rem;
}

.ai-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #8b5cf6, #a855f7);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.ai-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
}

/* Empty States */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: var(--text-secondary);
  background: linear-gradient(135deg, var(--bg-tertiary), var(--bg-secondary));
  border-radius: 16px;
  border: 1px dashed var(--border-color);
  margin: 2rem auto;
  width: 100%;
  max-width: 100%;
}

.empty-state i {
  font-size: 4rem;
  color: var(--text-muted);
  margin-bottom: 1.5rem;
}

.empty-state p {
  margin-bottom: 2rem;
  font-size: 1.125rem;
  font-weight: 500;
}

.add-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.add-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

/* Experience/Education Items */
.experience-item,
.education-item {
  padding: 2rem;
  border: 1.5px solid var(--border-color);
  border-radius: 16px;
  margin-bottom: 1.5rem;
  background: linear-gradient(135deg, var(--bg-secondary), var(--bg-tertiary));
  transition: all 0.3s ease;
  width: 100%;
  max-width: 100%;
}

.experience-item:hover,
.education-item:hover {
  border-color: var(--accent-primary);
  transform: translateY(-2px);
  box-shadow: 0 8px 25px var(--shadow-light);
}

.item-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
}

.item-number {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
  font-weight: 700;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.remove-btn {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: none;
  background: linear-gradient(135deg, #fee2e2, #fecaca);
  color: #dc2626;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.remove-btn:hover {
  background: linear-gradient(135deg, #fecaca, #fca5a5);
  transform: scale(1.1);
}

.add-more-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  border: 2px dashed var(--border-color);
  border-radius: 12px;
  background: none;
  color: var(--text-secondary);
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
  justify-content: center;
  font-weight: 500;
}

.add-more-btn:hover {
  border-color: var(--accent-primary);
  color: var(--accent-primary);
  background: rgba(59, 130, 246, 0.05);
}

/* Skills */
.skills-input {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  width: 100%;
}

.skills-input input {
  flex: 1;
  padding: 1rem 1.25rem;
  border: 1.5px solid var(--border-color);
  border-radius: 12px;
  background: var(--bg-secondary);
  transition: all 0.3s ease;
  color: var(--text-primary);
}

.skills-input input:focus {
  border-color: var(--accent-primary);
  background: var(--bg-primary);
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.add-skill-btn {
  padding: 1rem 1.5rem;
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.add-skill-btn:hover {
  transform: translateY(-2px);
}

/* Modern Template Selection */
.template-selection {
  margin-top: 3rem;
  padding: 2rem 0;
}

.template-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.template-header h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.template-header p {
  color: var(--text-secondary);
  font-size: 1rem;
  margin: 0;
}

.template-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.25rem;
  max-width: 750px;
  margin: 0 auto;
  justify-content: center;
  width: 100%;
}

.template-card {
  border: 2px solid var(--border-color);
  border-radius: 16px;
  padding: 1.25rem;
  cursor: pointer;
  transition: all 0.3s ease;
  background: var(--bg-primary);
  position: relative;
  overflow: hidden;
  text-align: center;
}

.template-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(99, 102, 241, 0.05));
  opacity: 0;
  transition: opacity 0.3s ease;
}

.template-card:hover {
  border-color: var(--accent-primary);
  transform: translateY(-4px);
  box-shadow: 0 12px 40px rgba(59, 130, 246, 0.15);
}

.template-card:hover::before {
  opacity: 1;
}

.template-card.active {
  border-color: var(--accent-primary);
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(99, 102, 241, 0.05));
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(59, 130, 246, 0.2);
}

.template-preview {
  height: 100px;
  background: linear-gradient(135deg, #f8fafc, #f1f5f9);
  border-radius: 16px;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  border: 1px solid #e2e8f0;
}

.template-icon {
  font-size: 2rem;
  color: #64748b;
  transition: all 0.3s ease;
}

.template-card:hover .template-icon {
  color: #3b82f6;
  transform: scale(1.1);
}

.template-card.active .template-icon {
  color: #3b82f6;
}

.template-check {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  width: 24px;
  height: 24px;
  background: #10b981;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.75rem;
  animation: checkBounce 0.3s ease-out;
}

@keyframes checkBounce {
  0% { transform: scale(0); }
  50% { transform: scale(1.2); }
  100% { transform: scale(1); }
}

.template-info {
  position: relative;
  z-index: 1;
}

.template-info h4 {
  margin: 0 0 0.75rem 0;
  font-weight: 600;
  color: var(--text-primary);
  font-size: 1.1rem;
}

.template-info p {
  margin: 0;
  font-size: 0.9rem;
  color: var(--text-secondary);
  line-height: 1.5;
}

.skills-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  justify-content: center;
  width: 100%;
  max-width: 700px;
}

.skill-tag {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1rem;
  background: linear-gradient(135deg, #eff6ff, #dbeafe);
  color: #1e40af;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
  border: 1px solid #bfdbfe;
  transition: all 0.3s ease;
}

.skill-tag:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(30, 64, 175, 0.3);
}

.skill-tag button {
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 0;
  width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.skill-tag button:hover {
  background: rgba(220, 38, 38, 0.1);
  color: #dc2626;
  transform: scale(1.2);
}

.skill-suggestions {
  margin-top: 1.5rem;
  padding: 1.5rem;
  background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
  border-radius: 16px;
  border: 1px solid #bae6fd;
  width: 100%;
  max-width: 700px;
  text-align: center;
}

.skill-suggestions h4 {
  font-size: 0.875rem;
  font-weight: 600;
  color: #0c4a6e;
  margin-bottom: 1rem;
  text-align: center;
}

.suggestions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 0.75rem;
  justify-content: center;
}

.suggestion-btn {
  padding: 0.625rem 1rem;
  border: 1px solid #bae6fd;
  border-radius: 20px;
  background: white;
  color: #0369a1;
  cursor: pointer;
  font-size: 0.8rem;
  font-weight: 500;
  transition: all 0.3s ease;
  text-align: center;
}

.suggestion-btn:hover {
  border-color: #3b82f6;
  background: #3b82f6;
  color: white;
  transform: translateY(-1px);
}

/* Interests */
.interests-input {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.interests-input input {
  flex: 1;
  padding: 1rem 1.25rem;
  border: 1.5px solid #e2e8f0;
  border-radius: 12px;
  background: #fafafa;
  transition: all 0.3s ease;
}

.add-interest-btn {
  padding: 1rem 1.5rem;
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.add-interest-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

.interests-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  justify-content: center;
  width: 100%;
  max-width: 700px;
}

.interest-tag {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1rem;
  background: linear-gradient(135deg, #ecfdf5, #d1fae5);
  color: #065f46;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
  border: 1px solid #a7f3d0;
  transition: all 0.3s ease;
}

.interest-tag:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(6, 95, 70, 0.3);
}

.interest-tag button {
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 0;
  width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.interest-tag button:hover {
  background: rgba(220, 38, 38, 0.1);
  color: #dc2626;
  transform: scale(1.2);
}

/* Form Footer */
.form-footer {
  display: flex;
  gap: 1.5rem;
  justify-content: flex-end;
  padding-top: 3rem;
  margin-top: 3rem;
  border-top: 1px solid #e2e8f0;
}

.save-draft-btn, .finish-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.875rem;
}

.save-draft-btn {
  background: white;
  color: #64748b;
  border: 1.5px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.save-draft-btn:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
  transform: translateY(-1px);
}

.finish-btn {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.finish-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.finish-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

/* Modern Preview Panel */
.preview-panel {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  border: 1px solid #e2e8f0;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
  width: 100%;
  max-width: 850px;
  margin-top: 2rem;
  animation: slideInUp 0.3s ease-out;
}

.preview-panel.stacked {
  position: relative;
  height: auto;
  min-height: 600px;
  max-height: 80vh;
}

.preview-panel.active {
  display: block;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.preview-header {
  padding: 1.5rem;
  background: linear-gradient(135deg, #f8fafc, #f1f5f9);
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 10;
  backdrop-filter: blur(10px);
}

.preview-header h3 {
  font-size: 1.125rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.preview-actions {
  display: flex;
  gap: 0.75rem;
}

.download-btn, .close-preview {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.25rem;
  border: none;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.download-btn {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.download-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.close-preview {
  background: #f1f5f9;
  color: #64748b;
  border: 1px solid #e2e8f0;
}

.close-preview:hover {
  background: #e2e8f0;
  color: #475569;
  transform: translateY(-1px);
}

.preview-container {
  padding: 2rem;
  overflow-y: auto;
  background: #f8fafc;
  height: calc(100% - 88px);
  max-height: calc(80vh - 88px);
}

/* Success Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.success-modal {
  background: white;
  border-radius: 20px;
  max-width: 450px;
  width: 90%;
  padding: 3rem;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  border: 1px solid #e2e8f0;
}

.success-modal h2 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 1rem 0;
}

.success-modal p {
  color: #64748b;
  margin: 0 0 2rem 0;
  font-size: 1rem;
  line-height: 1.6;
}

.success-modal .actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.success-modal button {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.875rem;
}

.success-modal .primary-btn {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.success-modal .primary-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

.success-modal .secondary-btn {
  background: #f8fafc;
  color: #64748b;
  border: 1px solid #e2e8f0;
}

.success-modal .secondary-btn:hover {
  background: #f1f5f9;
  transform: translateY(-1px);
}

/* Auto-save indicator */
.auto-save-indicator {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  padding: 0.75rem 1.25rem;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  font-size: 0.875rem;
  font-weight: 500;
  z-index: 50;
  transition: all 0.3s ease;
}

.auto-save-indicator.saving {
  color: #3b82f6;
  border-color: #3b82f6;
}

.auto-save-indicator.saved {
  color: #10b981;
  border-color: #10b981;
}

.auto-save-indicator.error {
  color: #ef4444;
  border-color: #ef4444;
}

/* Enhanced Responsive Design */
@media (max-width: 1200px) {
  .creator-main {
    max-width: 1000px;
    padding: 1.5rem;
    gap: 1.5rem;
  }
  
  .creator-main.preview-stacked {
    max-width: 1000px;
  }
  
  .form-panel {
    max-width: 900px;
  }
  
  .form-container {
    padding: 2.5rem 2rem;
    max-width: 800px;
  }
  
  .form-section {
    max-width: 750px;
  }
}

@media (max-width: 768px) {
  .creator-header {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .header-info {
    text-align: center;
  }
  
  .header-actions {
    justify-content: center;
  }
  
  .creator-main {
    padding: 1rem;
    gap: 1rem;
    max-width: 100%;
  }
  
  .creator-main.preview-stacked {
    max-width: 100%;
  }
  
  .form-panel {
    max-width: 100%;
  }
  
  .form-container {
    padding: 1.5rem 1rem;
    max-width: 100%;
  }
  
  .form-section {
    padding: 1.5rem 1rem;
    margin-bottom: 2rem;
    max-width: 100%;
  }
  
  .form-grid {
    gap: 1rem;
    margin-bottom: 1.5rem;
    max-width: 100%;
  }
  
  .step-intro {
    margin-bottom: 2rem;
    padding: 0 0.5rem;
    max-width: 100%;
  }
  
  .step-intro h2 {
    font-size: 1.75rem;
    line-height: 1.3;
  }
  
  .step-intro p {
    font-size: 1rem;
  }
  
  .template-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
    max-width: 100%;
  }
  
  .template-card {
    padding: 1rem;
  }
  
  .template-preview {
    height: 80px;
    margin-bottom: 1rem;
  }
  
  .template-icon {
    font-size: 1.5rem;
  }
  
  .skills-list,
  .interests-list {
    max-width: 100%;
    justify-content: flex-start;
  }
  
  .preview-panel {
    margin-top: 1.5rem;
    max-width: 100%;
  }
  
  .step-navigation-buttons {
    padding: 1.5rem 1rem;
    max-width: 100%;
    margin: 2rem auto 0 auto;
  }
  
  .nav-btn {
    padding: 1rem 1.5rem;
    font-size: 0.875rem;
  }
}

@media (max-width: 480px) {
  .header-content h1 {
    font-size: 1.5rem;
  }
  
  .form-container {
    padding: 1.5rem 1rem;
  }
  
  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .section-header h2 {
    font-size: 1.25rem;
  }
  
  .template-option {
    font-size: 0.75rem;
    padding: 0.5rem 1rem;
  }
}

/* Loading states */
.loading-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid #f3f4f6;
  border-radius: 50%;
  border-top-color: #3b82f6;
  animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Form helpers and validation */
.form-hint {
  font-size: 0.75rem;
  color: var(--text-muted);
  margin-top: 0.5rem;
  text-align: left;
}

.character-count {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.75rem;
  color: var(--text-muted);
  margin-top: 0.5rem;
  text-align: left;
}

.character-count .status {
  font-weight: 500;
}

.character-count .status.complete {
  color: #10b981;
}

.character-count .status.warning {
  color: #f59e0b;
}

.character-count .status.incomplete {
  color: #64748b;
}

.form-group.error input,
.form-group.error textarea {
  border-color: #ef4444;
  box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

.form-group.error label {
  color: #ef4444;
}

.error-message {
  color: #ef4444;
  font-size: 0.75rem;
  margin-top: 0.5rem;
  font-weight: 500;
  text-align: left;
}

/* Smooth transitions for all interactive elements */
* {
  scroll-behavior: smooth;
}

button, input, textarea, select {
  transition: all 0.3s ease;
}

/* Focus styles for accessibility */
button:focus-visible,
input:focus-visible,
textarea:focus-visible {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.success-modal {
  background: white;
  border-radius: 12px;
  max-width: 400px;
  width: 90%;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.success-content {
  padding: 2rem;
  text-align: center;
}

.success-content i {
  font-size: 3rem;
  color: #10b981;
  margin-bottom: 1rem;
}

.success-content h3 {
  font-size: 1.5rem;
  color: #1e293b;
  margin: 0 0 0.5rem 0;
}

.success-content p {
  color: #64748b;
  margin-bottom: 2rem;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.primary-btn,
.secondary-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.primary-btn {
  background: #3b82f6;
  color: white;
}

.primary-btn:hover {
  background: #2563eb;
}

.secondary-btn {
  background: #f8fafc;
  color: #64748b;
  border: 1px solid #e2e8f0;
}

.secondary-btn:hover {
  background: #f1f5f9;
}

/* Tablet and Medium Screens */
@media (max-width: 1024px) {
  .creator-main {
    max-width: 750px;
    padding: 1.5rem;
  }
  
  .creator-main.preview-stacked {
    max-width: 750px;
  }
  
  .form-panel {
    max-width: 700px;
  }
  
  .form-container {
    padding: 2rem 1.5rem;
    max-width: 650px;
  }
  
  .form-section {
    padding: 2rem 1.5rem;
    max-width: 650px;
  }
  
  .form-grid {
    max-width: 600px;
  }
  
  .template-grid {
    max-width: 600px;
  }
  
  .preview-panel {
    max-width: 700px;
  }
  
  .preview-panel.stacked {
    min-height: 400px;
    max-height: 60vh;
  }
}

@media (max-width: 768px) {
  .header-content {
    padding: 0 1rem;
  }
  
  .form-container {
    padding: 1rem;
  }
  
  .grid-2,
  .grid-3 {
    grid-template-columns: 1fr;
  }
  
  .template-switcher {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .template-options {
    flex-wrap: wrap;
  }
  
  .form-footer {
    flex-direction: column;
  }
  
  .header-actions {
    gap: 0.5rem;
  }
  
  .preview-toggle span,
  .save-btn span {
    display: none;
  }
}
</style>
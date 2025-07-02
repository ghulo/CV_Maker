<template>
  <div class="ai-suggestions-panel" :class="{ 'expanded': isExpanded }">
    <!-- AI Assistant Header -->
    <div class="ai-header" @click="togglePanel">
      <div class="ai-icon">
        <i class="fas fa-brain" :class="{ 'pulse': isGenerating, 'thinking': isAnalyzing }"></i>
      </div>
      <div class="ai-title">
        <h3>{{ $t('ai.assistant') }}</h3>
        <p>{{ $t('ai.assistant_tagline') }}</p>
      </div>
      <button class="toggle-btn">
        <i class="fas" :class="isExpanded ? 'fa-chevron-down' : 'fa-chevron-up'"></i>
      </button>
    </div>

    <!-- AI Content -->
    <div class="ai-content" v-if="isExpanded">
      <!-- AI Greeting Message -->
      <div class="ai-greeting" v-if="!cvAnalysis || isFirstTime">
        <div class="greeting-message">
          <span class="greeting-emoji">👋</span>
          <p>{{ currentGreeting }}</p>
        </div>
        <div class="motivational-message" v-if="motivationalMessage">
          <p>{{ motivationalMessage }}</p>
        </div>
      </div>

      <!-- CV Analysis Score -->
      <div class="cv-score-section" v-if="cvAnalysis">
        <div class="score-header">
          <h4>{{ $t('ai.strength_score') }}</h4>
          <div class="score-circle" :class="getScoreClass(cvAnalysis.score)">
            <span class="score-number">{{ cvAnalysis.score }}</span>
            <span class="score-total">/100</span>
          </div>
        </div>
        
        <div class="score-breakdown">
          <div class="completeness-section">
            <div class="section-label">
              <span>{{ $t('ai.completeness') }}</span>
              <span class="percentage">{{ cvAnalysis.completeness }}%</span>
            </div>
            <div class="progress-bar-container">
              <div class="progress-bar" :style="{ '--bar-width': cvAnalysis.completeness }"></div>
            </div>
          </div>
          
          <div class="ats-section" v-if="cvAnalysis.atsScore">
            <div class="section-label">
              <span>{{ $t('ai.ats_score') }}</span>
              <span class="percentage">{{ cvAnalysis.atsScore }}%</span>
            </div>
            <div class="progress-bar-container">
              <div class="progress-bar ats-bar" :style="{ '--bar-width': cvAnalysis.atsScore }"></div>
            </div>
          </div>

          <div class="industry-alignment" v-if="cvAnalysis.industryAlignment">
            <span class="alignment-label">{{ $t('ai.industry_alignment') }}:</span>
            <span class="alignment-value" :class="cvAnalysis.industryAlignment">
              {{ $t(`ai.${cvAnalysis.industryAlignment}`) }}
            </span>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="quick-actions" v-if="quickActions.length > 0">
        <h4>{{ $t('ai.actions.analyze_now') }}</h4>
        <div class="action-buttons">
          <button 
            v-for="action in quickActions" 
            :key="action.id"
            @click="executeQuickAction(action)"
            :class="['action-btn', action.type]"
            :disabled="isGenerating"
          >
            <i :class="action.icon"></i>
            <span>{{ $t(action.label) }}</span>
          </button>
        </div>
      </div>

      <!-- Suggestions Tabs -->
      <div class="suggestions-tabs">
        <button 
          v-for="tab in visibleTabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="['tab', { active: activeTab === tab.id }]"
        >
          <i :class="tab.icon"></i>
          {{ $t(tab.label) }}
          <span v-if="getTabBadgeCount(tab.id)" class="badge">{{ getTabBadgeCount(tab.id) }}</span>
        </button>
      </div>

      <!-- Tab Content -->
      <div class="tab-content">
        <!-- Skills Suggestions -->
        <div v-if="activeTab === 'skills'" class="suggestions-section">
          <div class="section-header">
            <h4>{{ $t('ai.recommended_skills') }}</h4>
            <span class="powered-by">{{ $t('ai.status.ready') }}</span>
          </div>
          
          <div class="skills-context" v-if="primaryJobTitle">
            <p>{{ $t('ai.prompts.skills_strategic') }}</p>
            <span class="job-context">{{ $t('Based on your role as') }} <strong>{{ primaryJobTitle }}</strong></span>
          </div>

          <div class="skills-list">
            <div 
              v-for="skill in enhancedSkillSuggestions" 
              :key="skill.name"
              class="skill-suggestion"
              :class="{ 'already-added': skill.alreadyAdded }"
            >
              <div class="skill-info">
                <span class="skill-name">{{ skill.name }}</span>
                <span v-if="skill.level" class="skill-level">{{ $t(`ai.${skill.level}`) }}</span>
                <span v-if="skill.trending" class="trending-badge">🔥 {{ $t('ai.trending_skills') }}</span>
              </div>
              <button 
                @click="addRecommendedSkill(skill)"
                :disabled="skill.alreadyAdded || isGenerating"
                class="add-skill-btn"
              >
                <i :class="skill.alreadyAdded ? 'fas fa-check' : 'fas fa-plus'"></i>
                {{ skill.alreadyAdded ? $t('ai.actions.applied') : $t('ai.actions.apply_suggestion') }}
              </button>
            </div>
          </div>

          <button @click="generateMoreSkills" class="generate-more-btn" :disabled="isGenerating">
            <i class="fas fa-refresh" :class="{ 'spinning': isGenerating }"></i>
            {{ $t('ai.actions.generate_more') }}
          </button>
        </div>

        <!-- Summary Suggestions -->
        <div v-if="activeTab === 'summary'" class="suggestions-section">
          <div class="section-header">
            <h4>{{ $t('ai.summary_generator') }}</h4>
            <span class="powered-by">{{ $t('ai.status.ready') }}</span>
          </div>

          <div class="summary-context">
            <p>{{ $t('ai.prompts.summary_help') }}</p>
            <div class="context-info" v-if="experienceYears || primaryJobTitle">
              <span v-if="experienceYears">{{ experienceYears }} {{ $t('years experience') }}</span>
              <span v-if="primaryJobTitle">{{ $t('as') }} {{ primaryJobTitle }}</span>
            </div>
          </div>

          <div class="summary-templates" v-if="summaryTemplates.length > 0">
            <div 
              v-for="(template, index) in summaryTemplates" 
              :key="index"
              class="summary-template"
            >
              <div class="template-header">
                <span class="template-type">{{ $t(`ai.${template.type}`) }}</span>
                <span class="template-length">{{ template.content.split(' ').length }} {{ $t('words') }}</span>
              </div>
              <div class="template-content">
                <p>{{ template.content }}</p>
              </div>
              <div class="template-actions">
                <button @click="useSummaryTemplate(template)" class="use-template-btn">
                  <i class="fas fa-magic"></i>
                  {{ $t('ai.actions.use_template') }}
                </button>
                <button @click="customizeSummary(template)" class="customize-btn">
                  <i class="fas fa-edit"></i>
                  {{ $t('ai.actions.customize') }}
                </button>
              </div>
            </div>
          </div>

          <button @click="generateSummary" class="generate-btn" :disabled="isGenerating">
            <i class="fas fa-sparkles" :class="{ 'spinning': isGenerating }"></i>
            {{ isGenerating ? $t('ai.generating') : $t('ai.generate') }}
          </button>
        </div>

        <!-- Experience Enhancement -->
        <div v-if="activeTab === 'experience'" class="suggestions-section">
          <div class="section-header">
            <h4>{{ $t('ai.experience_enhancer') }}</h4>
            <span class="powered-by">{{ $t('ai.status.ready') }}</span>
          </div>

          <div class="experience-context">
            <p>{{ $t('ai.prompts.experience_boost') }}</p>
          </div>

          <div class="experience-suggestions" v-if="experienceSuggestions.length > 0">
            <div 
              v-for="(suggestion, index) in experienceSuggestions" 
              :key="index"
              class="experience-suggestion"
            >
              <div class="suggestion-content">
                <p>{{ suggestion }}</p>
              </div>
              <div class="suggestion-actions">
                <button @click="useExperienceSuggestion(suggestion)" class="use-suggestion-btn">
                  <i class="fas fa-plus"></i>
                  {{ $t('ai.actions.apply_suggestion') }}
                </button>
              </div>
            </div>
          </div>

          <button @click="generateExperienceSuggestions" class="generate-btn" :disabled="isGenerating">
            <i class="fas fa-lightbulb" :class="{ 'spinning': isGenerating }"></i>
            {{ $t('ai.actions.generate_more') }}
          </button>
        </div>

        <!-- Interests Suggestions -->
        <div v-if="activeTab === 'interests'" class="suggestions-section">
          <div class="section-header">
            <h4>{{ $t('ai.interests_section') }}</h4>
            <span class="powered-by">{{ $t('ai.status.ready') }}</span>
          </div>

          <div class="interests-context">
            <p>{{ $t('ai.prompts.interests_personality') }}</p>
          </div>

          <div class="interests-list">
            <div 
              v-for="interest in interestSuggestions" 
              :key="interest"
              class="interest-suggestion"
            >
              <span class="interest-name">{{ interest }}</span>
              <button @click="addInterest(interest)" class="add-interest-btn">
                <i class="fas fa-heart"></i>
                {{ $t('ai.actions.apply_suggestion') }}
              </button>
            </div>
          </div>
        </div>

        <!-- Optimization Tips -->
        <div v-if="activeTab === 'optimize'" class="suggestions-section">
          <div class="section-header">
            <h4>{{ $t('ai.improvement_tips') }}</h4>
            <span class="powered-by">{{ $t('ai.status.ready') }}</span>
          </div>
          
          <!-- Strengths -->
          <div v-if="cvAnalysis && cvAnalysis.strengths.length > 0" class="strengths-section">
            <h5>{{ $t('ai.strengths') }}</h5>
            <div class="strengths-list">
              <div 
                v-for="strength in cvAnalysis.strengths" 
                :key="strength"
                class="strength-item"
              >
                <i class="fas fa-check-circle"></i>
                <span>{{ strength }}</span>
              </div>
            </div>
          </div>

          <!-- Improvements -->
          <div v-if="cvAnalysis && cvAnalysis.improvements.length > 0" class="improvements-section">
            <h5>{{ $t('ai.improvements') }}</h5>
            <div class="improvements-list">
              <div 
                v-for="improvement in cvAnalysis.improvements" 
                :key="improvement.title"
                class="improvement-item"
                :class="improvement.priority"
              >
                <div class="improvement-header">
                  <div class="improvement-title">
                    <i :class="getPriorityIcon(improvement.priority)"></i>
                    <span>{{ improvement.title }}</span>
                  </div>
                  <span class="impact-badge" :class="improvement.priority">
                    {{ improvement.impact }}
                  </span>
                </div>
                <p class="improvement-description">{{ improvement.description }}</p>
                <p class="improvement-action">{{ improvement.action }}</p>
              </div>
            </div>
          </div>

          <!-- Weaknesses -->
          <div v-if="cvAnalysis && cvAnalysis.weaknesses.length > 0" class="weaknesses-section">
            <h5>{{ $t('ai.weaknesses') }}</h5>
            <div class="weaknesses-list">
              <div 
                v-for="weakness in cvAnalysis.weaknesses" 
                :key="weakness"
                class="weakness-item"
              >
                <i class="fas fa-exclamation-triangle"></i>
                <span>{{ weakness }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- AI Status Footer -->
      <div class="ai-status-footer">
        <div class="status-indicator" :class="aiStatus">
          <i :class="getStatusIcon()"></i>
          <span>{{ $t(`ai.status.${aiStatus}`) }}</span>
        </div>
        <div class="feedback-section" v-if="showFeedback">
          <span>{{ $t('ai.feedback.analysis_helpful') }}</span>
          <div class="feedback-buttons">
            <button @click="provideFeedback(true)" class="feedback-btn positive">
              <i class="fas fa-thumbs-up"></i>
            </button>
            <button @click="provideFeedback(false)" class="feedback-btn negative">
              <i class="fas fa-thumbs-down"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted, inject } from 'vue'
import AIService from '@/services/aiService.js'

export default {
  name: 'AISuggestionsPanel',
  props: {
    cvData: {
      type: Object,
      required: true
    },
    currentStep: {
      type: Number,
      default: 0
    }
  },
  emits: ['update-cv', 'add-skill', 'add-interest', 'update-summary', 'add-experience-point'],
  setup(props, { emit }) {
    const t = inject('t') || ((key) => key)
    const currentLanguage = inject('currentLanguage') || ref('en')

    // Reactive data
    const isExpanded = ref(false)
    const activeTab = ref('skills')
    const isGenerating = ref(false)
    const isAnalyzing = ref(false)
    const cvAnalysis = ref(null)
    const summaryTemplates = ref([])
    const experienceSuggestions = ref([])
    const aiStatus = ref('ready')
    const showFeedback = ref(false)
    const isFirstTime = ref(true)
    const motivationalMessage = ref('')

    // Initialize AI service with language
    const aiService = new AIService()
    watch(currentLanguage, (newLang) => {
      aiService.setLanguage(newLang)
      refreshContent()
    }, { immediate: true })

    const tabs = [
      { id: 'skills', label: 'ai.skills_section', icon: 'fas fa-cogs' },
      { id: 'summary', label: 'ai.summary_generator', icon: 'fas fa-user' },
      { id: 'experience', label: 'ai.experience_enhancer', icon: 'fas fa-briefcase' },
      { id: 'interests', label: 'ai.interests_section', icon: 'fas fa-heart' },
      { id: 'optimize', label: 'ai.improvement_tips', icon: 'fas fa-chart-line' }
    ]

    // Computed properties
    const currentGreeting = computed(() => {
      return aiService.getAIGreeting(props.cvData, props.currentStep)
    })

    const visibleTabs = computed(() => {
      // Show tabs based on current step and available content
      const stepRelevantTabs = {
        0: ['skills', 'optimize'],
        1: ['summary', 'optimize'],
        2: ['experience', 'optimize'],
        3: ['skills', 'optimize'],
        4: ['interests', 'optimize'],
        5: ['skills', 'summary', 'experience', 'interests', 'optimize']
      }
      
      const relevantIds = stepRelevantTabs[props.currentStep] || tabs.map(t => t.id)
      return tabs.filter(tab => relevantIds.includes(tab.id))
    })

    const primaryJobTitle = computed(() => {
      if (props.cvData.experience && props.cvData.experience.length > 0) {
        return props.cvData.experience[0].title || 'professional'
      }
      return 'professional'
    })

    const experienceYears = computed(() => {
      return aiService.calculateExperienceYears(props.cvData.experience || [])
    })

    const enhancedSkillSuggestions = computed(() => {
      const suggestions = aiService.getSkillSuggestions(
        primaryJobTitle.value, 
        '', 
        experienceYears.value >= 5 ? 'advanced' : 'intermediate'
      )
      
      const userSkills = (props.cvData.skills || []).map(skill => 
        typeof skill === 'string' ? skill : skill.name
      )

      return suggestions.map(skill => ({
        name: skill,
        alreadyAdded: userSkills.includes(skill),
        level: Math.random() > 0.7 ? 'trending' : 'recommended',
        trending: Math.random() > 0.8
      }))
    })

    const interestSuggestions = computed(() => {
      return aiService.getInterestSuggestions ? 
        aiService.getInterestSuggestions(primaryJobTitle.value) : 
        ['Professional Development', 'Technology Trends', 'Team Sports', 'Reading']
    })

    const quickActions = computed(() => {
      const actions = []
      
      if (!cvAnalysis.value) {
        actions.push({
          id: 'analyze',
          type: 'primary',
          icon: 'fas fa-search',
          label: 'ai.analyze'
        })
      }
      
      if (props.currentStep === 1 && !props.cvData.summary) {
        actions.push({
          id: 'generate-summary',
          type: 'success',
          icon: 'fas fa-magic',
          label: 'ai.generate'
        })
      }

      return actions
    })

    // Methods
    const togglePanel = () => {
      isExpanded.value = !isExpanded.value
      if (isExpanded.value && !cvAnalysis.value) {
        analyzeCV()
      }
    }

    const analyzeCV = async () => {
      isAnalyzing.value = true
      aiStatus.value = 'processing'
      
      try {
        // Simulate AI thinking time for realism
        await new Promise(resolve => setTimeout(resolve, 1500))
        
        cvAnalysis.value = aiService.analyzeCV(props.cvData)
        aiStatus.value = 'complete'
        showFeedback.value = true
        isFirstTime.value = false
        
        // Generate motivational message
        motivationalMessage.value = aiService.getMotivationalMessage('encouraging')
        
      } catch (error) {
        console.error('AI Analysis failed:', error)
        aiStatus.value = 'error'
      } finally {
        isAnalyzing.value = false
      }
    }

    const generateSummary = async () => {
      isGenerating.value = true
      
      try {
        await new Promise(resolve => setTimeout(resolve, 2000)) // Realistic delay
        
        const templates = []
        for (let i = 0; i < 3; i++) {
          const summary = aiService.generateProfessionalSummary(
            props.cvData.personalInfo,
            props.cvData.experience || [],
            props.cvData.skills || [],
            primaryJobTitle.value
          )
          
          const type = i === 0 ? 'professional' : i === 1 ? 'creative' : 'results_focused'
          templates.push({
            type,
            content: summary,
            wordCount: summary.split(' ').length
          })
        }
        
        summaryTemplates.value = templates
        
      } catch (error) {
        console.error('Summary generation failed:', error)
      } finally {
        isGenerating.value = false
      }
    }

    const generateExperienceSuggestions = async () => {
      isGenerating.value = true
      
      try {
        await new Promise(resolve => setTimeout(resolve, 1500))
        
        if (aiService.getExperienceSuggestions) {
          experienceSuggestions.value = aiService.getExperienceSuggestions(
            primaryJobTitle.value,
            'medium',
            ''
          )
        }
        
      } catch (error) {
        console.error('Experience suggestions failed:', error)
      } finally {
        isGenerating.value = false
      }
    }

    const generateMoreSkills = async () => {
      isGenerating.value = true
      
      try {
        await new Promise(resolve => setTimeout(resolve, 1000))
        // This would trigger a re-computation of enhancedSkillSuggestions
        // Force refresh by changing the experience level
        
      } catch (error) {
        console.error('Skill generation failed:', error)
      } finally {
        isGenerating.value = false
      }
    }

    const executeQuickAction = async (action) => {
      switch (action.id) {
        case 'analyze':
          await analyzeCV()
          break
        case 'generate-summary':
          activeTab.value = 'summary'
          await generateSummary()
          break
      }
    }

    const useSummaryTemplate = (template) => {
      emit('update-summary', template.content)
      motivationalMessage.value = t('ai.feedback.suggestion_applied')
    }

    const customizeSummary = (template) => {
      // Open summary editor with template as starting point
      emit('update-summary', template.content)
      activeTab.value = 'summary'
    }

    const addRecommendedSkill = (skill) => {
      if (!skill.alreadyAdded) {
        emit('add-skill', skill.name)
        skill.alreadyAdded = true
      }
    }

    const addInterest = (interest) => {
      emit('add-interest', interest)
    }

    const useExperienceSuggestion = (suggestion) => {
      emit('add-experience-point', suggestion)
    }

    const refreshContent = () => {
      if (cvAnalysis.value) {
        analyzeCV()
      }
    }

    const provideFeedback = (positive) => {
      showFeedback.value = false
      motivationalMessage.value = t('ai.feedback.feedback_thanks')
    }

    // Utility methods
    const getScoreClass = (score) => {
      if (score >= 80) return 'excellent'
      if (score >= 60) return 'good'
      if (score >= 40) return 'fair'
      return 'poor'
    }

    const getTabBadgeCount = (tabId) => {
      switch (tabId) {
        case 'skills':
          return enhancedSkillSuggestions.value.filter(s => !s.alreadyAdded).length
        case 'optimize':
          return cvAnalysis.value ? cvAnalysis.value.improvements.length : 0
        default:
          return 0
      }
    }

    const getPriorityIcon = (priority) => {
      const icons = {
        high: 'fas fa-exclamation-circle',
        medium: 'fas fa-info-circle',
        low: 'fas fa-lightbulb'
      }
      return icons[priority] || 'fas fa-info-circle'
    }

    const getStatusIcon = () => {
      const icons = {
        ready: 'fas fa-check-circle',
        processing: 'fas fa-spinner fa-spin',
        complete: 'fas fa-check-circle',
        error: 'fas fa-exclamation-triangle',
        offline: 'fas fa-wifi'
      }
      return icons[aiStatus.value] || 'fas fa-question-circle'
    }

    // Watch for changes
    watch(() => props.cvData, () => {
      if (cvAnalysis.value) {
        // Debounce analysis updates
        setTimeout(() => analyzeCV(), 1000)
      }
    }, { deep: true })

    onMounted(() => {
      motivationalMessage.value = aiService.getMotivationalMessage('motivational')
    })

    return {
      // Reactive data
      isExpanded,
      activeTab,
      isGenerating,
      isAnalyzing,
      cvAnalysis,
      summaryTemplates,
      experienceSuggestions,
      aiStatus,
      showFeedback,
      isFirstTime,
      motivationalMessage,
      
      // Computed
      tabs,
      visibleTabs,
      currentGreeting,
      primaryJobTitle,
      experienceYears,
      enhancedSkillSuggestions,
      interestSuggestions,
      quickActions,
      
      // Methods
      togglePanel,
      analyzeCV,
      generateSummary,
      generateExperienceSuggestions,
      generateMoreSkills,
      executeQuickAction,
      useSummaryTemplate,
      customizeSummary,
      addRecommendedSkill,
      addInterest,
      useExperienceSuggestion,
      provideFeedback,
      getScoreClass,
      getTabBadgeCount,
      getPriorityIcon,
      getStatusIcon
    }
  }
}
</script>

<style scoped>
.ai-suggestions-panel {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 400px;
  max-height: 600px;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  transition: all 0.3s ease;
}

.ai-suggestions-panel.expanded {
  max-height: 80vh;
}

.ai-header {
  padding: 16px;
  display: flex;
  align-items: center;
  cursor: pointer;
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  color: white;
  border-radius: 12px 12px 0 0;
}

.ai-icon {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12px;
}

.ai-icon i {
  font-size: 18px;
}

.ai-icon i.pulse {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { opacity: 1; }
  50% { opacity: 0.5; }
  100% { opacity: 1; }
}

.ai-title {
  flex: 1;
}

.ai-title h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}

.ai-title p {
  margin: 0;
  font-size: 12px;
  opacity: 0.9;
}

.toggle-btn {
  background: none;
  border: none;
  color: white;
  font-size: 16px;
  cursor: pointer;
}

.ai-content {
  padding: 20px;
  max-height: 500px;
  overflow-y: auto;
}

/* CV Score Section */
.cv-score-section {
  margin-bottom: 20px;
  padding: 16px;
  background: var(--bg-subtle);
  border-radius: 8px;
}

.score-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.score-circle {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
  color: white;
}

.score-circle.excellent { background: var(--success); }
.score-circle.good { background: var(--primary); }
.score-circle.fair { background: var(--accent); }
.score-circle.poor { background: var(--error); }

.completeness-bar {
  height: 6px;
  background: var(--border);
  border-radius: 3px;
  margin-bottom: 8px;
}

.progress-bar, .ats-bar {
  width: var(--bar-width, 100%);
}

/* Tabs */
.suggestions-tabs {
  display: flex;
  margin-bottom: 16px;
  border-bottom: 1px solid var(--border);
}

.tab {
  flex: 1;
  padding: 8px 4px;
  border: none;
  background: none;
  color: var(--text-muted);
  font-size: 12px;
  cursor: pointer;
  text-align: center;
  border-bottom: 2px solid transparent;
  transition: all 0.2s ease;
}

.tab.active {
  color: var(--primary);
  border-bottom-color: var(--primary);
}

.tab i {
  display: block;
  margin-bottom: 4px;
  font-size: 14px;
}

/* Suggestions */
.suggestions-section h4 {
  margin: 0 0 8px 0;
  font-size: 16px;
  color: var(--text);
}

.helper-text {
  font-size: 14px;
  color: var(--text-muted);
  margin-bottom: 12px;
}

.suggestion-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  margin: 4px;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 20px;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.suggestion-chip:hover:not(:disabled) {
  background: var(--primary);
  color: white;
  border-color: var(--primary);
}

.suggestion-chip:disabled {
  background: var(--success);
  color: white;
  border-color: var(--success);
  cursor: not-allowed;
}

.suggestion-textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid var(--border);
  border-radius: 6px;
  font-size: 14px;
  line-height: 1.5;
  margin-bottom: 12px;
  background: var(--bg-subtle);
}

.use-suggestion-btn {
  background: var(--primary);
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
}

.experience-suggestions .suggestion-item {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  padding: 8px;
  margin-bottom: 8px;
  border: 1px solid var(--border);
  border-radius: 6px;
  font-size: 14px;
}

.suggestion-item p {
  flex: 1;
  margin: 0;
  line-height: 1.4;
}

.mini-add-btn {
  background: var(--primary);
  color: white;
  border: none;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  font-size: 10px;
  cursor: pointer;
  flex-shrink: 0;
}

/* Issues and Optimization */
.issues-list, .suggestions-list {
  margin-bottom: 16px;
}

.issues-list h5, .suggestions-list h5 {
  font-size: 14px;
  margin-bottom: 8px;
  color: var(--text);
}

.issue-item, .suggestion-item.improvement {
  display: flex;
  gap: 12px;
  padding: 12px;
  border-radius: 6px;
  margin-bottom: 8px;
}

.issue-item.warning {
  background: var(--warning-light);
  border-left: 3px solid var(--warning);
}

.issue-item.error {
  background: var(--error-light);
  border-left: 3px solid var(--error);
}

.suggestion-item.improvement {
  background: var(--info-light);
  border-left: 3px solid var(--info);
}

.issue-icon, .suggestion-icon {
  color: var(--warning);
  margin-top: 2px;
}

.issue-item.error .issue-icon {
  color: var(--error);
}

.suggestion-icon {
  color: var(--info);
}

.issue-content, .suggestion-content {
  flex: 1;
}

.issue-content h6, .suggestion-content h6 {
  margin: 0 0 4px 0;
  font-size: 13px;
  font-weight: 600;
}

.issue-content p, .suggestion-content p {
  margin: 0 0 4px 0;
  font-size: 12px;
  line-height: 1.4;
}

.issue-action, .suggestion-action {
  font-size: 11px;
  font-weight: 500;
  color: var(--primary);
}

/* Responsive */
@media (max-width: 768px) {
  .ai-suggestions-panel {
    width: calc(100vw - 40px);
    right: 20px;
    left: 20px;
  }
}
</style> 
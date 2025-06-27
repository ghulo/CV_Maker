<template>
  <div class="ai-suggestions-panel" :class="{ 'expanded': isExpanded }">
    <!-- AI Assistant Header -->
    <div class="ai-header" @click="togglePanel">
      <div class="ai-icon">
        <i class="fas fa-brain" :class="{ 'pulse': isGenerating }"></i>
      </div>
      <div class="ai-title">
        <h3>AI Assistant</h3>
        <p>Get intelligent suggestions for your CV</p>
      </div>
      <button class="toggle-btn">
        <i class="fas" :class="isExpanded ? 'fa-chevron-down' : 'fa-chevron-up'"></i>
      </button>
    </div>

    <!-- AI Content -->
    <div class="ai-content" v-if="isExpanded">
      <!-- CV Analysis Score -->
      <div class="cv-score-section" v-if="cvAnalysis">
        <div class="score-header">
          <h4>CV Strength Score</h4>
          <div class="score-circle" :class="getScoreClass(cvAnalysis.score)">
            {{ cvAnalysis.score }}/100
          </div>
        </div>
        <div class="completeness-bar">
          <div class="progress-bar" :style="{ width: cvAnalysis.completeness + '%' }"></div>
        </div>
        <p>{{ cvAnalysis.completeness }}% Complete</p>
      </div>

      <!-- Suggestions Tabs -->
      <div class="suggestions-tabs">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="['tab', { active: activeTab === tab.id }]"
        >
          <i :class="tab.icon"></i>
          {{ tab.label }}
        </button>
      </div>

      <!-- Tab Content -->
      <div class="tab-content">
        <!-- Skills Suggestions -->
        <div v-if="activeTab === 'skills'" class="suggestions-section">
          <h4>Recommended Skills</h4>
          <p class="helper-text">Based on your role, here are popular skills to add:</p>
          <div class="skill-suggestions">
            <button 
              v-for="skill in skillSuggestions" 
              :key="skill"
              @click="addSkill(skill)"
              class="suggestion-chip"
              :disabled="isSkillAdded(skill)"
            >
              <i class="fas fa-plus" v-if="!isSkillAdded(skill)"></i>
              <i class="fas fa-check" v-else></i>
              {{ skill }}
            </button>
          </div>
        </div>

        <!-- Summary Suggestions -->
        <div v-if="activeTab === 'summary'" class="suggestions-section">
          <h4>Professional Summary Template</h4>
          <p class="helper-text">AI-generated summary based on your experience:</p>
          <div class="summary-suggestion">
            <textarea 
              v-model="generatedSummary" 
              rows="4" 
              readonly
              class="suggestion-textarea"
            ></textarea>
            <button @click="useSummary" class="use-suggestion-btn">
              <i class="fas fa-magic"></i>
              Use This Summary
            </button>
          </div>
        </div>

        <!-- Experience Suggestions -->
        <div v-if="activeTab === 'experience'" class="suggestions-section">
          <h4>Work Experience Ideas</h4>
          <p class="helper-text">Common responsibilities for your role:</p>
          <div class="experience-suggestions">
            <div 
              v-for="(suggestion, index) in experienceSuggestions" 
              :key="index"
              class="suggestion-item"
            >
              <p>{{ suggestion }}</p>
              <button @click="addExperiencePoint(suggestion)" class="mini-add-btn">
                <i class="fas fa-plus"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Interests Suggestions -->
        <div v-if="activeTab === 'interests'" class="suggestions-section">
          <h4>Professional Interests</h4>
          <p class="helper-text">Interests that align with your profession:</p>
          <div class="interest-suggestions">
            <button 
              v-for="interest in interestSuggestions" 
              :key="interest"
              @click="addInterest(interest)"
              class="suggestion-chip"
              :disabled="isInterestAdded(interest)"
            >
              <i class="fas fa-plus" v-if="!isInterestAdded(interest)"></i>
              <i class="fas fa-check" v-else></i>
              {{ interest }}
            </button>
          </div>
        </div>

        <!-- Optimization Tips -->
        <div v-if="activeTab === 'optimize'" class="suggestions-section">
          <h4>Optimization Tips</h4>
          
          <!-- Issues -->
          <div v-if="cvAnalysis && cvAnalysis.issues.length > 0" class="issues-list">
            <h5>Issues to Fix:</h5>
            <div 
              v-for="issue in cvAnalysis.issues" 
              :key="issue.title"
              class="issue-item"
              :class="issue.type"
            >
              <div class="issue-icon">
                <i class="fas fa-exclamation-triangle" v-if="issue.type === 'warning'"></i>
                <i class="fas fa-times-circle" v-if="issue.type === 'error'"></i>
              </div>
              <div class="issue-content">
                <h6>{{ issue.title }}</h6>
                <p>{{ issue.description }}</p>
                <span class="issue-action">{{ issue.action }}</span>
              </div>
            </div>
          </div>

          <!-- Suggestions -->
          <div v-if="cvAnalysis && cvAnalysis.suggestions.length > 0" class="suggestions-list">
            <h5>Improvement Suggestions:</h5>
            <div 
              v-for="suggestion in cvAnalysis.suggestions" 
              :key="suggestion.title"
              class="suggestion-item improvement"
            >
              <div class="suggestion-icon">
                <i class="fas fa-lightbulb"></i>
              </div>
              <div class="suggestion-content">
                <h6>{{ suggestion.title }}</h6>
                <p>{{ suggestion.description }}</p>
                <span class="suggestion-action">{{ suggestion.action }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted } from 'vue'
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
    const isExpanded = ref(false)
    const activeTab = ref('skills')
    const isGenerating = ref(false)
    const cvAnalysis = ref(null)
    const generatedSummary = ref('')

    const tabs = [
      { id: 'skills', label: 'Skills', icon: 'fas fa-cogs' },
      { id: 'summary', label: 'Summary', icon: 'fas fa-user' },
      { id: 'experience', label: 'Experience', icon: 'fas fa-briefcase' },
      { id: 'interests', label: 'Interests', icon: 'fas fa-heart' },
      { id: 'optimize', label: 'Tips', icon: 'fas fa-chart-line' }
    ]

    // Computed suggestions based on CV data
    const primaryJobTitle = computed(() => {
      if (props.cvData.experience && props.cvData.experience.length > 0) {
        return props.cvData.experience[0].title || 'professional'
      }
      return 'professional'
    })

    const skillSuggestions = computed(() => {
      return AIService.getSkillSuggestions(primaryJobTitle.value)
    })

    const experienceSuggestions = computed(() => {
      return AIService.getExperienceSuggestions(primaryJobTitle.value)
    })

    const interestSuggestions = computed(() => {
      return AIService.getInterestSuggestions(primaryJobTitle.value)
    })

    // Methods
    const togglePanel = () => {
      isExpanded.value = !isExpanded.value
      if (isExpanded.value) {
        analyzeCV()
      }
    }

    const analyzeCV = () => {
      cvAnalysis.value = AIService.analyzeCV(props.cvData)
      generateSummary()
    }

    const generateSummary = () => {
      isGenerating.value = true
      setTimeout(() => {
        generatedSummary.value = AIService.generateSummaryTemplate(
          props.cvData.personalInfo, 
          props.cvData.experience
        )
        isGenerating.value = false
      }, 1000)
    }

    const addSkill = (skillName) => {
      emit('add-skill', { name: skillName, rating: 3 })
    }

    const addInterest = (interestName) => {
      emit('add-interest', { name: interestName })
    }

    const addExperiencePoint = (description) => {
      emit('add-experience-point', description)
    }

    const useSummary = () => {
      emit('update-summary', generatedSummary.value)
    }

    const isSkillAdded = (skillName) => {
      return props.cvData.skills?.some(skill => 
        skill.name.toLowerCase() === skillName.toLowerCase()
      )
    }

    const isInterestAdded = (interestName) => {
      return props.cvData.interests?.some(interest => 
        interest.name.toLowerCase() === interestName.toLowerCase()
      )
    }

    const getScoreClass = (score) => {
      if (score >= 80) return 'excellent'
      if (score >= 60) return 'good'
      if (score >= 40) return 'fair'
      return 'poor'
    }

    // Watch for CV changes and re-analyze
    watch(() => props.cvData, () => {
      if (isExpanded.value) {
        analyzeCV()
      }
    }, { deep: true })

    // Auto-set relevant tab based on current step
    watch(() => props.currentStep, (newStep) => {
      const stepToTab = {
        0: 'summary',
        1: 'experience',
        3: 'skills',
        4: 'interests'
      }
      if (stepToTab[newStep]) {
        activeTab.value = stepToTab[newStep]
      }
    })

    onMounted(() => {
      analyzeCV()
    })

    return {
      isExpanded,
      activeTab,
      isGenerating,
      cvAnalysis,
      generatedSummary,
      tabs,
      skillSuggestions,
      experienceSuggestions,
      interestSuggestions,
      togglePanel,
      addSkill,
      addInterest,
      addExperiencePoint,
      useSummary,
      isSkillAdded,
      isInterestAdded,
      getScoreClass
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

.progress-bar {
  height: 100%;
  background: var(--primary);
  border-radius: 3px;
  transition: width 0.3s ease;
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
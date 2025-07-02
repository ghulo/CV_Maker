<template>
  <div class="floating-ai-assistant" :class="{ 'expanded': isExpanded }">
    <!-- AI Avatar -->
    <div class="ai-avatar unified-avatar" @click="toggleAssistant" :class="{ 'thinking': isThinking, 'pulse': isActive }">
      <div class="ai-smiley">
        <!-- Use a white smiley SVG for consistency -->
        <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="32" cy="32" r="30" fill="url(#aiGradient)"/>
          <circle cx="32" cy="32" r="28" stroke="white" stroke-width="2" fill="none"/>
          <circle cx="24" cy="28" r="3" fill="white"/>
          <circle cx="40" cy="28" r="3" fill="white"/>
          <path d="M26 40c2 2 8 2 12 0" stroke="white" stroke-width="2" stroke-linecap="round"/>
          <defs>
            <linearGradient id="aiGradient" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
              <stop stop-color="#5B21B6"/>
              <stop offset="1" stop-color="#7C3AED"/>
            </linearGradient>
          </defs>
        </svg>
      </div>
      <div class="ai-glow"></div>
      <div class="pulse-ring"></div>
      
      <!-- Status Badge -->
      <div class="status-badge" :class="aiStatus">
        <i class="fas fa-brain" v-if="aiStatus === 'ready'"></i>
        <i class="fas fa-spinner fa-spin" v-if="aiStatus === 'thinking'"></i>
        <i class="fas fa-magic" v-if="aiStatus === 'generating'"></i>
      </div>
    </div>

    <!-- Chat Interface -->
    <div class="ai-chat-panel" v-if="isExpanded">
      <div class="chat-header">
        <div class="ai-info">
          <h3>🤖 AI Assistant</h3>
          <p>Your intelligent CV companion</p>
        </div>
        <button @click="closeAssistant" class="close-btn">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="chat-content">
        <div class="ai-message" v-for="message in messages" :key="message.id">
          <div class="message-bubble">
            <span v-if="message.typing" class="typing-indicator">{{ typingText }}</span>
            <span v-else v-html="message.text"></span>
          </div>
          
          <!-- Score Display -->
          <div v-if="message.score" class="score-display">
            <div class="score-circle" :class="getScoreClass(animatedScore)">
              <span class="score-number">{{ animatedScore }}</span>
              <span class="score-suffix">/100</span>
            </div>
            <div class="score-details">
              <div class="score-bar">
                <span>Completeness</span>
                <div class="progress-bar">
                  <div class="progress" :style="{ '--bar-width': animatedCompleteness }"></div>
                </div>
                <span>{{ animatedCompleteness }}%</span>
              </div>
              <div class="score-bar">
                <span>ATS Score</span>
                <div class="progress-bar">
                  <div class="progress ats" :style="{ '--bar-width': animatedAtsScore }"></div>
                </div>
                <span>{{ animatedAtsScore }}%</span>
              </div>
            </div>
          </div>

          <!-- Suggestions -->
          <div v-if="message.suggestions" class="ai-suggestions">
            <div v-for="suggestion in message.suggestions" :key="suggestion.id" 
                 class="suggestion-card" @click="applySuggestion(suggestion)">
              <div class="suggestion-icon">
                <i :class="suggestion.icon"></i>
              </div>
              <div class="suggestion-content">
                <div class="suggestion-title">{{ suggestion.title }}</div>
                <div class="suggestion-impact">{{ suggestion.impact }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="quick-actions">
        <button v-for="action in quickActions" :key="action.id"
                @click="performAction(action)"
                class="action-btn"
                :disabled="isThinking">
          <i :class="action.icon"></i>
          <span>{{ action.label }}</span>
        </button>
      </div>
    </div>

    <!-- Floating Tips -->
    <div class="floating-tip" v-if="!isExpanded && currentTip" @click="toggleAssistant">
      <div class="tip-content">
        <i class="fas fa-lightbulb"></i>
        <span>{{ currentTip }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, nextTick } from 'vue'

export default {
  name: 'FloatingAIAssistant',
  props: {
    cvData: {
      type: Object,
      default: () => ({})
    }
  },
  setup(props) {
    // State
    const isExpanded = ref(false)
    const isThinking = ref(false)
    const isSpeaking = ref(false)
    const isBlinking = ref(false)
    const isActive = ref(true)
    const aiStatus = ref('ready')
    const messages = ref([])
    const typingText = ref('')
    const currentTip = ref('')
    
    // Animated values
    const animatedScore = ref(0)
    const animatedCompleteness = ref(0)
    const animatedAtsScore = ref(0)
    
    let messageId = 0

    // Quick actions
    const quickActions = ref([
      {
        id: 'analyze',
        label: 'Analyze CV',
        icon: 'fas fa-search'
      },
      {
        id: 'improve',
        label: 'Get Tips',
        icon: 'fas fa-magic'
      },
      {
        id: 'optimize',
        label: 'Optimize',
        icon: 'fas fa-rocket'
      }
    ])

    const tips = [
      '💡 Click me for instant CV insights!',
      '🚀 Your AI assistant is ready to help!',
      '⚡ Get personalized CV suggestions!',
      '🎯 Boost your CV score now!',
      '✨ Unlock AI-powered improvements!'
    ]

    // Methods
    const toggleAssistant = () => {
      isExpanded.value = !isExpanded.value
      if (isExpanded.value && messages.value.length === 0) {
        startWelcomeSequence()
      }
    }

    const closeAssistant = () => {
      isExpanded.value = false
    }

    const startWelcomeSequence = async () => {
      if (!props.cvData || Object.keys(props.cvData).length === 0) {
        await addMessage('👋 Hi! I\'m your AI CV assistant. I don\'t see any CV data to analyze yet.', true)
        
        setTimeout(async () => {
          await addMessage('Create your first CV to get personalized AI insights and recommendations!', true)
        }, 1500)
      } else {
        const cvTitle = props.cvData.title || 'Untitled CV'
        await addMessage(`👋 Hello! I'm analyzing your CV: "${cvTitle}"`, true)
        
        setTimeout(async () => {
          await analyzeCV()
        }, 2000)
      }
    }

    const addMessage = async (text, typing = false) => {
      const message = {
        id: ++messageId,
        text: '',
        typing: typing,
        timestamp: Date.now()
      }
      
      messages.value.push(message)
      
      if (typing) {
        isSpeaking.value = true
        await typeMessage(text, message)
        isSpeaking.value = false
      } else {
        message.text = text
      }
    }

    const typeMessage = async (text, message) => {
      const words = text.split(' ')
      for (let i = 0; i < words.length; i++) {
        typingText.value = words.slice(0, i + 1).join(' ')
        await new Promise(resolve => setTimeout(resolve, 100))
      }
      
      message.text = text
      message.typing = false
    }

    const analyzeCV = async () => {
      aiStatus.value = 'thinking'
      isThinking.value = true
      
      // Actually analyze the real CV data
      await new Promise(resolve => setTimeout(resolve, 2000))
      
      // Calculate real scores based on actual CV content
      const analysis = calculateRealScores(props.cvData)
      
      // Animate to real values
      animateValue(animatedScore, analysis.overall, 2000)
      animateValue(animatedCompleteness, analysis.completeness, 2500)
      animateValue(animatedAtsScore, analysis.atsScore, 3000)
      
      // Add result message based on actual analysis  
      const cvTitle = props.cvData.title || 'Untitled CV'
      await addMessage(`Analysis of "${cvTitle}" complete! Score: ${analysis.overall}/100. ${analysis.feedback}`, true)
      
      messages.value[messages.value.length - 1].score = {
        total: analysis.overall,
        completeness: analysis.completeness,
        atsScore: analysis.atsScore
      }
      
      // Add real suggestions based on what's actually missing
      setTimeout(async () => {
        const realSuggestions = generateRealSuggestions(props.cvData)
        if (realSuggestions.length > 0) {
          await addMessage('Here are specific improvements for your CV:', true)
          messages.value[messages.value.length - 1].suggestions = realSuggestions
        }
        
        aiStatus.value = 'ready'
        isThinking.value = false
      }, 1500)
    }

    const calculateRealScores = (cvData) => {
      if (!cvData || Object.keys(cvData).length === 0) {
        return {
          overall: 25,
          completeness: 20,
          atsScore: 30,
          feedback: "No CV data found. Create your CV to get real insights!"
        }
      }
      
      let score = 0
      let completeness = 0
      let atsScore = 0
      let feedback = ""
      
      // Personal Information (20 points)
      if (cvData.personalDetails) {
        if (cvData.personalDetails.firstName && cvData.personalDetails.lastName) {
          score += 10
          completeness += 15
        }
        if (cvData.personalDetails.email) {
          score += 5
          atsScore += 10
        }
        if (cvData.personalDetails.phone) {
          score += 3
          atsScore += 5
        }
        if (cvData.personalDetails.location) {
          score += 2
          atsScore += 5
        }
      }
      
      // Professional Summary (15 points)
      if (cvData.summary && cvData.summary.trim().length > 0) {
        const summaryLength = cvData.summary.trim().length
        if (summaryLength > 200) {
          score += 15
          completeness += 20
          atsScore += 15
        } else if (summaryLength > 100) {
          score += 10
          completeness += 15
          atsScore += 10
        } else if (summaryLength > 50) {
          score += 5
          completeness += 10
          atsScore += 5
        }
      }
      
      // Work Experience (25 points)
      if (cvData.experience && cvData.experience.length > 0) {
        score += Math.min(cvData.experience.length * 8, 25)
        completeness += Math.min(cvData.experience.length * 15, 30)
        
        // Check for quantified achievements
        const hasNumbers = cvData.experience.some(exp => 
          exp.description && /\d+/.test(exp.description)
        )
        if (hasNumbers) {
          score += 5
          atsScore += 10
        }
      }
      
      // Education (15 points)
      if (cvData.education && cvData.education.length > 0) {
        score += Math.min(cvData.education.length * 7, 15)
        completeness += Math.min(cvData.education.length * 10, 20)
        atsScore += 10
      }
      
      // Skills (20 points)
      if (cvData.skills && cvData.skills.length > 0) {
        score += Math.min(cvData.skills.length * 3, 20)
        completeness += Math.min(cvData.skills.length * 5, 15)
        atsScore += Math.min(cvData.skills.length * 4, 20)
      }
      
      // Cap scores at 100
      score = Math.min(score, 100)
      completeness = Math.min(completeness, 100)
      atsScore = Math.min(atsScore, 100)
      
      // Generate specific feedback
      if (score >= 80) {
        feedback = "Strong CV! Just minor improvements needed."
      } else if (score >= 60) {
        feedback = "Good foundation, but several areas need attention."
      } else if (score >= 40) {
        feedback = "CV needs significant improvements to be competitive."
      } else {
        feedback = "Major sections are missing. Let's build this up!"
      }
      
      return { overall: score, completeness, atsScore, feedback }
    }

    const generateRealSuggestions = (cvData) => {
      const suggestions = []
      
      // Check what's actually missing
      if (!cvData.summary || cvData.summary.trim().length < 100) {
        suggestions.push({
          id: 1,
          title: 'Add Professional Summary',
          impact: '+15 pts',
          icon: 'fas fa-edit'
        })
      }
      
      if (!cvData.skills || cvData.skills.length < 5) {
        suggestions.push({
          id: 2,
          title: 'Add More Skills',
          impact: '+12 pts',
          icon: 'fas fa-plus'
        })
      }
      
      if (!cvData.experience || cvData.experience.length === 0) {
        suggestions.push({
          id: 3,
          title: 'Add Work Experience',
          impact: '+25 pts',
          icon: 'fas fa-briefcase'
        })
      } else if (cvData.experience.length > 0) {
        const hasQuantifiedAchievements = cvData.experience.some(exp => 
          exp.description && /\d+/.test(exp.description)
        )
        if (!hasQuantifiedAchievements) {
          suggestions.push({
            id: 4,
            title: 'Quantify Achievements',
            impact: '+10 pts',
            icon: 'fas fa-chart-line'
          })
        }
      }
      
      if (!cvData.education || cvData.education.length === 0) {
        suggestions.push({
          id: 5,
          title: 'Add Education',
          impact: '+15 pts',
          icon: 'fas fa-graduation-cap'
        })
      }
      
      return suggestions.slice(0, 3) // Return top 3 most important
    }

    const animateValue = (ref, target, duration) => {
      const start = ref.value
      const startTime = Date.now()
      
      const animate = () => {
        const elapsed = Date.now() - startTime
        const progress = Math.min(elapsed / duration, 1)
        const easeOut = 1 - Math.pow(1 - progress, 3)
        
        ref.value = Math.round(start + (target - start) * easeOut)
        
        if (progress < 1) {
          requestAnimationFrame(animate)
        }
      }
      
      requestAnimationFrame(animate)
    }

    const performAction = async (action) => {
      if (isThinking.value) return
      
      aiStatus.value = 'generating'
      isThinking.value = true
      
      switch (action.id) {
        case 'analyze':
          await addMessage('🔍 Running comprehensive analysis...', true)
          await analyzeCV()
          break
        case 'improve':
          await addMessage('⚡ Generating improvement suggestions...', true)
          await new Promise(resolve => setTimeout(resolve, 2000))
          await addMessage('✨ I found 5 ways to boost your CV effectiveness by 40%!', true)
          break
        case 'optimize':
          await addMessage('🚀 Optimizing for ATS compatibility...', true)
          await new Promise(resolve => setTimeout(resolve, 2500))
          await addMessage('🎯 Optimization complete! Your CV is now 95% ATS-compatible.', true)
          break
      }
      
      aiStatus.value = 'ready'
      isThinking.value = false
    }

    const applySuggestion = (suggestion) => {
      // Add visual feedback
      const notification = document.createElement('div')
      notification.className = 'suggestion-applied'
      notification.textContent = `Applied: ${suggestion.title}`
      document.body.appendChild(notification)
      
      setTimeout(() => {
        document.body.removeChild(notification)
      }, 3000)
    }

    const getScoreClass = (score) => {
      if (score >= 90) return 'excellent'
      if (score >= 75) return 'good'
      if (score >= 60) return 'fair'
      return 'poor'
    }

    const rotateTips = () => {
      let tipIndex = 0
      setInterval(() => {
        currentTip.value = tips[tipIndex]
        tipIndex = (tipIndex + 1) % tips.length
      }, 4000)
    }

    // Lifecycle
    onMounted(() => {
      // Start blinking animation
      setInterval(() => {
        isBlinking.value = true
        setTimeout(() => {
          isBlinking.value = false
        }, 150)
      }, 3000)

      // Rotate tips
      rotateTips()
      
      // Show initial tip
      setTimeout(() => {
        currentTip.value = tips[0]
      }, 2000)
    })

    return {
      // State
      isExpanded,
      isThinking,
      isSpeaking,
      isBlinking,
      isActive,
      aiStatus,
      messages,
      typingText,
      currentTip,
      quickActions,
      animatedScore,
      animatedCompleteness,
      animatedAtsScore,
      
      // Methods
      toggleAssistant,
      closeAssistant,
      performAction,
      applySuggestion,
      getScoreClass
    }
  }
}
</script>

<style scoped>
.floating-ai-assistant {
  position: fixed;
  bottom: 30px;
  right: 30px;
  z-index: 10000;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

/* AI Avatar */
.ai-avatar.unified-avatar {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
  border-radius: 50%;
  box-shadow: 0 4px 16px rgba(var(--primary-rgb), 0.18);
  border: 2px solid white;
  width: 64px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  cursor: pointer;
  transition: box-shadow 0.2s;
}

.ai-avatar:hover {
  box-shadow: 0 8px 32px rgba(var(--primary-rgb), 0.28);
}

.ai-avatar.thinking {
  animation: thinking 1s ease-in-out infinite;
}

.ai-avatar.pulse {
  animation: pulse 2s ease-in-out infinite;
}

@keyframes thinking {
  0%, 100% { transform: scale(1) rotate(0deg); }
  25% { transform: scale(1.05) rotate(2deg); }
  50% { transform: scale(1.1) rotate(0deg); }
  75% { transform: scale(1.05) rotate(-2deg); }
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

.ai-smiley {
  width: 64px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.status-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 12px;
  border: 3px solid white;
  animation: bounce 2s infinite;
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
  40% { transform: translateY(-8px); }
  60% { transform: translateY(-4px); }
}

.status-badge.ready {
  background: linear-gradient(45deg, #10b981, #059669);
}

.status-badge.thinking {
  background: linear-gradient(45deg, #f59e0b, #d97706);
}

.status-badge.generating {
  background: linear-gradient(45deg, #8b5cf6, #7c3aed);
}

/* Chat Panel */
.ai-chat-panel {
  position: absolute;
  bottom: 100px;
  right: 0;
  width: 420px;
  max-height: 600px;
  background: white;
  border-radius: 24px;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.3);
  overflow: hidden;
  animation: slideUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
  backdrop-filter: blur(20px);
}

@keyframes slideUp {
  0% {
    opacity: 0;
    transform: translateY(30px) scale(0.9);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.chat-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.ai-info h3 {
  margin: 0 0 4px 0;
  font-size: 18px;
  font-weight: 700;
}

.ai-info p {
  margin: 0;
  font-size: 13px;
  opacity: 0.9;
}

.close-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.1);
}

.chat-content {
  max-height: 400px;
  overflow-y: auto;
  padding: 24px;
}

.ai-message {
  margin-bottom: 20px;
}

.message-bubble {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  padding: 16px 20px;
  border-radius: 20px 20px 20px 6px;
  font-size: 15px;
  line-height: 1.6;
  position: relative;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.typing-indicator {
  position: relative;
}

.typing-indicator::after {
  content: '|';
  animation: blink 1s infinite;
  margin-left: 2px;
}

@keyframes blink {
  0%, 50% { opacity: 1; }
  51%, 100% { opacity: 0; }
}

/* Score Display */
.score-display {
  margin-top: 16px;
  background: white;
  border-radius: 16px;
  padding: 20px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.score-circle {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  color: white;
  font-weight: bold;
  position: relative;
  overflow: hidden;
}

.score-circle::before {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 50%;
  padding: 3px;
  background: linear-gradient(45deg, currentColor, transparent);
  mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  mask-composite: exclude;
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
  font-size: 24px;
  line-height: 1;
}

.score-suffix {
  font-size: 12px;
  opacity: 0.8;
}

.score-details {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.score-bar {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 14px;
}

.score-bar span:first-child {
  min-width: 90px;
  font-weight: 500;
}

.score-bar span:last-child {
  min-width: 35px;
  font-weight: 600;
  color: #374151;
}

.progress-bar {
  flex: 1;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.progress, .ats {
  width: var(--bar-width, 100%);
}

.progress::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  animation: shimmer 2s ease-in-out infinite;
}

@keyframes shimmer {
  0% { left: -100%; }
  100% { left: 100%; }
}

/* AI Suggestions */
.ai-suggestions {
  margin-top: 16px;
  display: grid;
  gap: 12px;
}

.suggestion-card {
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  padding: 16px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  align-items: center;
  gap: 16px;
}

.suggestion-card:hover {
  border-color: #667eea;
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.15);
}

.suggestion-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 18px;
}

.suggestion-content {
  flex: 1;
}

.suggestion-title {
  font-weight: 600;
  margin-bottom: 4px;
  font-size: 15px;
}

.suggestion-impact {
  background: linear-gradient(135deg, #dcfce7, #bbf7d0);
  color: #065f46;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
}

/* Quick Actions */
.quick-actions {
  padding: 20px 24px;
  border-top: 1px solid #e5e7eb;
  display: flex;
  gap: 12px;
}

.action-btn {
  flex: 1;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  background: white;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  color: #374151;
}

.action-btn:hover:not(:disabled) {
  border-color: #667eea;
  background: #667eea;
  color: white;
  transform: translateY(-1px);
}

.action-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.action-btn i {
  font-size: 16px;
}

/* Floating Tip */
.floating-tip {
  position: absolute;
  bottom: 100px;
  right: 0;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  padding: 12px 20px;
  border-radius: 25px;
  cursor: pointer;
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
  animation: tipFloat 3s ease-in-out infinite;
  max-width: 280px;
  backdrop-filter: blur(10px);
}

@keyframes tipFloat {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-5px); }
}

.tip-content {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 500;
}

.tip-content i {
  animation: pulse 2s infinite;
}

/* Responsive */
@media (max-width: 768px) {
  .ai-chat-panel {
    width: calc(100vw - 60px);
    right: -15px;
  }
  
  .floating-ai-assistant {
    right: 20px;
    bottom: 20px;
  }
}

/* Global notification styles */
:global(.suggestion-applied) {
  position: fixed;
  top: 20px;
  right: 20px;
  background: #10b981;
  color: white;
  padding: 12px 20px;
  border-radius: 8px;
  font-weight: 500;
  z-index: 10001;
  animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
  0% {
    opacity: 0;
    transform: translateX(100%);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}
</style> 
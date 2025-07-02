<template>
  <div class="gemini-chat-wrapper">
    <!-- AI Avatar Chat Toggle Button -->
    <button
      v-if="!isOpen"
      @click="toggleChat"
      class="chat-toggle-btn ai-avatar-button"
    >
      <div class="ai-avatar-chat">
        <div class="ai-face">
          <div class="ai-eyes">
            <div class="eye"></div>
            <div class="eye"></div>
          </div>
          <div class="ai-mouth"></div>
        </div>
        <div class="ai-pulse-rings">
          <div class="pulse-ring ring-1"></div>
          <div class="pulse-ring ring-2"></div>
          <div class="pulse-ring ring-3"></div>
        </div>
      </div>
    </button>

    <!-- Chat Window -->
    <div
      v-if="isOpen"
      :class="['chat-window', { 'fullscreen': isFullscreen }]"
    >
      <!-- Header -->
      <div class="chat-header">
        <div class="chat-header-info">
          <div class="ai-avatar-mini">
            <svg class="ai-icon" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
          </div>
          <div class="header-text">
            <h3 class="ai-title">{{ currentPersonality.name }}</h3>
            <p class="ai-subtitle">{{ currentPersonality.tagline }}</p>
          </div>
        </div>
        <div class="header-controls">
          <!-- Fullscreen Toggle -->
          <button @click="toggleFullscreen" class="control-btn" :title="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'">
            <svg class="control-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!isFullscreen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 3v3a2 2 0 01-2 2H3m18 0h-3a2 2 0 01-2-2V3m0 18v-3a2 2 0 012-2h3M3 16h3a2 2 0 012 2v3"></path>
            </svg>
          </button>
          <!-- Close Button -->
          <button @click="toggleChat" class="control-btn close-btn">
            <svg class="control-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Chat Messages -->
      <div ref="chatContainer" class="chat-messages">
        <!-- Welcome Message -->
        <div v-if="messages.length === 0" class="welcome-message">
          <div class="welcome-content">
            <p class="welcome-text">{{ currentPersonality.greeting }}</p>
            <p class="welcome-subtext">{{ currentPersonality.helpText }}</p>
            
            <!-- Quick Actions based on context -->
            <div v-if="contextualQuickActions.length > 0" class="quick-actions">
              <button 
                v-for="action in contextualQuickActions" 
                :key="action.id"
                @click="handleQuickAction(action)"
                class="quick-action-btn"
              >
                <i :class="action.icon"></i>
                {{ action.label }}
              </button>
            </div>
          </div>
        </div>

        <!-- Chat Messages -->
        <div v-for="(message, index) in messages" :key="index" class="message-wrapper" :class="message.type">
          <div class="message-bubble" :class="message.type">
            <p class="message-text" v-html="renderMarkdown(message.text)"></p>
            <div class="message-meta">
              <span class="message-time">{{ formatTime(message.timestamp) }}</span>
              <span v-if="message.type === 'ai' && message.source" class="message-source">
                {{ message.source === 'gemini_ai' ? '🤖 AI' : '📝 Template' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Typing Indicator -->
        <div v-if="isTyping" class="typing-wrapper">
          <div class="typing-bubble">
            <div class="typing-dots">
              <div class="typing-dot"></div>
              <div class="typing-dot"></div>
              <div class="typing-dot"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Chat Input -->
      <div class="chat-input-section">
        <div class="input-wrapper">
          <input
            v-model="currentMessage"
            @keypress.enter="sendMessage"
            @keydown="handleTyping"
            type="text"
            :placeholder="currentPersonality.inputPlaceholder"
            class="chat-input"
            :disabled="isTyping"
          />
          <button
            @click="sendMessage"
            :disabled="!currentMessage.trim() || isTyping"
            class="send-btn"
          >
            <svg class="send-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import aiService from '../../services/aiService'
import MarkdownIt from 'markdown-it'

const md = new MarkdownIt()

export default {
  name: 'GeminiChatAssistant',
  props: {
    cvData: {
      type: Object,
      default: () => ({})
    }
  },
  setup(props) {
    const route = useRoute()
    
    // Reactive state - Fixed the main issue!
    const isOpen = ref(false)
    const isFullscreen = ref(false)
    const messages = ref([])
    const currentMessage = ref('')
    const isTyping = ref(false)
    const conversationHistory = ref([])
    const chatContainer = ref(null)
    
    // Define different AI personalities based on context
    const aiPersonalities = {
      // CV-related pages
      cv: {
        name: "CV Assistant",
        tagline: "Your professional CV expert",
        greeting: "👋 Hi! I'm your AI CV assistant. I can help you create amazing CVs, suggest improvements, and answer questions about your professional profile!",
        helpText: "Ask me about CV writing, skills, experiences, or career advice!",
        inputPlaceholder: "Ask me about your CV...",
        context: "cv",
        expertise: ["cv_writing", "career_advice", "skills", "experience", "professional_development"]
      },
      
      // General pages (homepage, about, etc.)
      general: {
        name: "AI Assistant",
        tagline: "Your intelligent companion",
        greeting: "👋 Hello! I'm your AI assistant. I can help you with various questions, provide information, or just have a friendly conversation!",
        helpText: "Ask me anything - I'm here to help!",
        inputPlaceholder: "Ask me anything...",
        context: "general",
        expertise: ["general_knowledge", "conversation", "information", "assistance"]
      },
      
      // Contact page
      contact: {
        name: "Support Assistant",
        tagline: "Here to help you connect",
        greeting: "👋 Hi! I'm here to help you with any questions or concerns. I can provide support information or help you get in touch!",
        helpText: "Ask me about our services, support, or how to get help!",
        inputPlaceholder: "How can I help you today?",
        context: "support",
        expertise: ["support", "contact_info", "services", "help"]
      },
      
      // Templates page
      templates: {
        name: "Design Assistant", 
        tagline: "Your template expert",
        greeting: "👋 Hi! I'm your design assistant. I can help you choose the perfect CV template and understand design best practices!",
        helpText: "Ask me about template selection, design tips, or visual presentation!",
        inputPlaceholder: "Ask about templates and design...",
        context: "design",
        expertise: ["templates", "design", "layout", "visual_presentation", "branding"]
      },
      
      // FAQ page
      faq: {
        name: "Knowledge Assistant",
        tagline: "Your question answering expert", 
        greeting: "👋 Hi! I can help answer your questions about our platform, CV creation, or provide additional information beyond our FAQ!",
        helpText: "Ask me to clarify any FAQ items or provide more detailed explanations!",
        inputPlaceholder: "What would you like to know?",
        context: "knowledge",
        expertise: ["faq", "platform_info", "explanations", "guidance"]
      }
    }
    
    // Determine current personality based on route
    const currentPersonality = computed(() => {
      const routeName = route.name?.toLowerCase() || ''
      const routePath = route.path?.toLowerCase() || ''
      
      // CV-related routes
      if (routeName.includes('cv') || routeName.includes('create') || routeName.includes('edit') || 
          routeName.includes('dashboard') || routePath.includes('/cv/') || 
          routePath.includes('/create') || routePath.includes('/edit') || routePath.includes('/dashboard')) {
        return aiPersonalities.cv
      }
      
      // Specific page routes
      if (routeName.includes('contact') || routePath.includes('/contact')) {
        return aiPersonalities.contact
      }
      
      if (routeName.includes('template') || routePath.includes('/template')) {
        return aiPersonalities.templates
      }
      
      if (routeName.includes('faq') || routePath.includes('/faq')) {
        return aiPersonalities.faq
      }
      
      // Default to general
      return aiPersonalities.general
    })
    
    // Contextual quick actions based on current page
    const contextualQuickActions = computed(() => {
      const personality = currentPersonality.value
      
      switch (personality.context) {
        case 'cv':
          return [
            { id: 'analyze', label: 'Analyze CV', icon: 'fas fa-chart-line' },
            { id: 'skills', label: 'Suggest Skills', icon: 'fas fa-lightbulb' },
            { id: 'improve', label: 'Get Tips', icon: 'fas fa-magic' }
          ]
        
        case 'design':
          return [
            { id: 'template_advice', label: 'Template Advice', icon: 'fas fa-palette' },
            { id: 'design_tips', label: 'Design Tips', icon: 'fas fa-paint-brush' },
            { id: 'layout_help', label: 'Layout Help', icon: 'fas fa-th-large' }
          ]
        
        case 'support':
          return [
            { id: 'contact_info', label: 'Contact Info', icon: 'fas fa-phone' },
            { id: 'help_topics', label: 'Help Topics', icon: 'fas fa-question-circle' },
            { id: 'report_issue', label: 'Report Issue', icon: 'fas fa-bug' }
          ]
        
        case 'general':
          return [
            { id: 'features', label: 'Platform Features', icon: 'fas fa-star' },
            { id: 'getting_started', label: 'Getting Started', icon: 'fas fa-play' },
            { id: 'tips', label: 'General Tips', icon: 'fas fa-lightbulb' }
          ]
        
        default:
          return []
      }
    })

    // Methods
    const toggleChat = () => {
      isOpen.value = !isOpen.value
      if (isOpen.value) {
        // Auto-focus input when chat opens
        nextTick(() => {
          const input = document.querySelector('.chat-input')
          if (input) input.focus()
        })
      }
    }

    const toggleFullscreen = () => {
      isFullscreen.value = !isFullscreen.value
    }

    const sendMessage = async () => {
      if (!currentMessage.value.trim() || isTyping.value) return

      const userMessage = {
        type: 'user',
        text: currentMessage.value.trim(),
        timestamp: new Date()
      }

      messages.value.push(userMessage)
      conversationHistory.value.push({
        user: userMessage.text,
        timestamp: userMessage.timestamp
      })

      const messageToSend = currentMessage.value.trim()
      currentMessage.value = ''
      isTyping.value = true
      
      scrollToBottom()

      try {
        // Include context information in the request
        const contextualCvData = currentPersonality.value.context === 'cv' ? props.cvData : {}
        const enhancedMessage = enhanceMessageWithContext(messageToSend)
        
        const response = await aiService.sendChatMessage(
          enhancedMessage,
          contextualCvData,
          conversationHistory.value,
          currentPersonality.value.context
        )

        const aiMessage = {
          type: 'ai',
          text: response.response,
          timestamp: new Date(response.timestamp),
          source: response.source
        }

        messages.value.push(aiMessage)
        conversationHistory.value.push({
          assistant: aiMessage.text,
          timestamp: aiMessage.timestamp
        })

      } catch (error) {
        console.error('Chat error:', error)
        const errorMessage = {
          type: 'ai',
          text: getContextualErrorMessage(),
          timestamp: new Date(),
          source: 'error'
        }
        messages.value.push(errorMessage)
      } finally {
        isTyping.value = false
        scrollToBottom()
      }
    }

    const enhanceMessageWithContext = (message) => {
      const context = currentPersonality.value.context
      const expertise = currentPersonality.value.expertise.join(', ')
      
      return `[Context: ${context}, Expertise: ${expertise}] ${message}`
    }

    const getContextualErrorMessage = () => {
      switch (currentPersonality.value.context) {
        case 'cv':
          return "I'm having trouble with CV analysis right now. Please try again in a moment, or feel free to ask me general questions about CV writing!"
        case 'design':
          return "I'm having trouble with design suggestions right now. Please try again, or ask me about general template selection!"
        case 'support':
          return "I'm having trouble accessing support information right now. Please try again, or contact our support team directly!"
        default:
          return "I'm having trouble responding right now. Please try again in a moment!"
      }
    }

    const handleQuickAction = (action) => {
      // Send a contextual message based on the quick action
      const actionMessages = {
        // CV context
        analyze: "Can you analyze my CV and provide improvement suggestions?",
        skills: "What skills should I add to my CV?",
        improve: "How can I improve my CV?",
        
        // Design context
        template_advice: "Which template would be best for my profession?",
        design_tips: "What are some design best practices for CVs?",
        layout_help: "How should I organize the layout of my CV?",
        
        // Support context
        contact_info: "How can I contact support?",
        help_topics: "What help topics are available?",
        report_issue: "I'd like to report an issue with the platform",
        
        // General context
        features: "What features does this platform offer?",
        getting_started: "How do I get started with creating a CV?",
        tips: "Do you have any general tips for success?"
      }
      
      currentMessage.value = actionMessages[action.id] || action.label
      sendMessage()
    }

    const handleTyping = () => {
      // Add typing indicator logic if needed
    }

    const scrollToBottom = () => {
      nextTick(() => {
        if (chatContainer.value) {
          chatContainer.value.scrollTop = chatContainer.value.scrollHeight
        }
      })
    }

    const formatTime = (timestamp) => {
      const date = new Date(timestamp)
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    }

    const clearChat = () => {
      messages.value = []
      conversationHistory.value = []
    }

    // Handle escape key to close fullscreen or chat
    const handleKeydown = (e) => {
      if (e.key === 'Escape') {
        if (isFullscreen.value) {
          isFullscreen.value = false
        } else if (isOpen.value) {
          isOpen.value = false
        }
      }
    }

    onMounted(() => {
      document.addEventListener('keydown', handleKeydown)
    })

    onBeforeUnmount(() => {
      document.removeEventListener('keydown', handleKeydown)
    })

    // Watch for route changes to clear conversation when context changes
    watch(currentPersonality, (newPersonality, oldPersonality) => {
      if (oldPersonality && newPersonality.context !== oldPersonality.context) {
        clearChat()
      }
    }, { deep: true })

    // Watch for CV data changes
    watch(() => props.cvData, () => {
      // React to CV data changes only if in CV context
      if (currentPersonality.value.context === 'cv') {
        // CV data changed, could provide contextual updates
      }
    }, { deep: true })

    return {
      isOpen,
      isFullscreen,
      messages,
      currentMessage,
      isTyping,
      conversationHistory,
      chatContainer,
      currentPersonality,
      contextualQuickActions,
      toggleChat,
      toggleFullscreen,
      sendMessage,
      handleQuickAction,
      handleTyping,
      scrollToBottom,
      formatTime,
      clearChat,
      renderMarkdown: (markdownText) => md.render(markdownText)
    }
  }
}
</script>

<style scoped>
/* Gemini Chat Assistant - Using Master.css Design System */

.gemini-chat-wrapper {
  position: fixed;
  bottom: var(--space-xl);
  right: var(--space-xl);
  z-index: var(--z-modal);
}

/* AI Avatar Chat Toggle Button */
.chat-toggle-btn.ai-avatar-button {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  background: rgba(102, 126, 234, 0.9);
  border: 3px solid rgba(255, 255, 255, 0.3);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
  transition: all 0.3s ease;
  position: relative;
  backdrop-filter: blur(20px);
  animation: ai-float 6s ease-in-out infinite;
  overflow: visible;
}

.chat-toggle-btn.ai-avatar-button:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
  background: rgba(102, 126, 234, 1);
}

/* AI Avatar Face */
.ai-avatar-chat {
  color: white;
  position: relative;
  z-index: 3;
}

.ai-face {
  position: relative;
  z-index: 3;
}

.ai-eyes {
  display: flex;
  gap: 10px;
  margin-bottom: 8px;
  justify-content: center;
}

.eye {
  width: 8px;
  height: 8px;
  background: white;
  border-radius: 50%;
  transition: all 0.2s ease;
  animation: ai-blink 4s ease-in-out infinite;
}

.ai-mouth {
  width: 16px;
  height: 8px;
  background: white;
  border-radius: 0 0 16px 16px;
  margin: 0 auto;
  transition: all 0.2s ease;
  animation: ai-smile 3s ease-in-out infinite;
}

/* AI Pulse Rings */
.ai-pulse-rings {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

.pulse-ring {
  position: absolute;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  animation-fill-mode: both;
}

.ring-1 {
  inset: -15px;
  animation: ai-pulse-1 4s ease-out infinite;
}

.ring-2 {
  inset: -25px;
  animation: ai-pulse-2 4s ease-out infinite 1.5s;
}

.ring-3 {
  inset: -35px;
  animation: ai-pulse-3 4s ease-out infinite 3s;
}

/* AI Animations */
@keyframes ai-float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-5px); }
}

@keyframes ai-blink {
  0%, 90%, 100% { height: 8px; }
  95% { height: 2px; }
}

@keyframes ai-smile {
  0%, 50%, 100% { border-radius: 0 0 16px 16px; }
  25% { border-radius: 0 0 20px 20px; }
}

@keyframes ai-pulse-1 {
  0% { transform: scale(0.8); opacity: 1; }
  100% { transform: scale(1.2); opacity: 0; }
}

@keyframes ai-pulse-2 {
  0% { transform: scale(0.8); opacity: 1; }
  100% { transform: scale(1.4); opacity: 0; }
}

@keyframes ai-pulse-3 {
  0% { transform: scale(0.8); opacity: 1; }
  100% { transform: scale(1.6); opacity: 0; }
}

/* Chat Window */
.chat-window {
  position: absolute;
  bottom: 80px;
  right: 0;
  width: 400px;
  max-height: 600px;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-2xl);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  animation: slideInUp 0.3s ease-out;
  transition: all 0.3s ease;
}

/* Fullscreen Mode */
.chat-window.fullscreen {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100vw;
  max-height: 100vh;
  height: 100vh;
  border-radius: 0;
  z-index: var(--z-overlay);
  animation: slideInFullscreen 0.3s ease-out;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes slideInFullscreen {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Chat Header */
.chat-header {
  background: var(--gradient-primary);
  color: var(--white);
  padding: var(--space-lg);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
}

.chat-header-info {
  display: flex;
  align-items: center;
  gap: var(--space-md);
}

.ai-avatar-mini {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: var(--radius-full);
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(10px);
}

.ai-icon {
  width: 20px;
  height: 20px;
}

.header-text {
  display: flex;
  flex-direction: column;
}

.ai-title {
  font-size: var(--font-size-base);
  font-weight: 600;
  margin: 0;
  color: var(--white);
  line-height: var(--line-height-tight);
}

.ai-subtitle {
  font-size: var(--font-size-xs);
  margin: 0;
  opacity: 0.9;
  color: var(--white);
}

.header-controls {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
}

.control-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: var(--white);
  width: 32px;
  height: 32px;
  border-radius: var(--radius-full);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: var(--transition-all);
}

.control-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.1);
}

.control-btn.close-btn:hover {
  transform: rotate(90deg) scale(1.1);
}

.control-icon {
  width: 16px;
  height: 16px;
}

/* Chat Messages */
.chat-messages {
  flex: 1;
  padding: var(--space-lg);
  overflow-y: auto;
  background: var(--bg-surface);
  min-height: 300px;
}

.chat-window.fullscreen .chat-messages {
  min-height: calc(100vh - 160px); /* Adjust for header and input */
}

.chat-messages::-webkit-scrollbar {
  width: 4px;
}

.chat-messages::-webkit-scrollbar-track {
  background: var(--bg-secondary);
  border-radius: var(--radius-sm);
}

.chat-messages::-webkit-scrollbar-thumb {
  background: var(--border);
  border-radius: var(--radius-sm);
}

.chat-messages::-webkit-scrollbar-thumb:hover {
  background: var(--border-hover);
}

/* Welcome Message */
.welcome-message {
  text-align: center;
  margin-bottom: var(--space-lg);
}

.welcome-content {
  background: var(--bg-elevated);
  border-radius: var(--radius-lg);
  padding: var(--space-lg);
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--border-light);
}

.welcome-text {
  margin: 0 0 var(--space-sm) 0;
  color: var(--text-primary);
  font-weight: 500;
}

.welcome-subtext {
  margin: 0 0 var(--space-md) 0;
  font-size: var(--font-size-sm);
  color: var(--text-muted);
}

/* Quick Actions */
.quick-actions {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-sm);
  justify-content: center;
}

.quick-action-btn {
  background: var(--primary);
  color: var(--white);
  border: none;
  padding: var(--space-sm) var(--space-md);
  border-radius: var(--radius-full);
  font-size: var(--font-size-xs);
  cursor: pointer;
  transition: var(--transition-all);
  display: flex;
  align-items: center;
  gap: var(--space-xs);
}

.quick-action-btn:hover {
  background: var(--primary-dark);
  transform: translateY(-1px);
}

.quick-action-btn i {
  font-size: var(--font-size-xs);
}

/* Message Wrappers */
.message-wrapper {
  margin-bottom: var(--space-md);
  display: flex;
}

.message-wrapper.user {
  justify-content: flex-end;
}

.message-wrapper.ai {
  justify-content: flex-start;
}

/* Message Bubbles */
.message-bubble {
  max-width: 280px;
  padding: var(--space-md);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
  position: relative;
}

.chat-window.fullscreen .message-bubble {
  max-width: 60%;
}

.message-bubble.user {
  background: var(--primary);
  color: var(--white);
  border-bottom-right-radius: var(--radius-sm);
}

.message-bubble.ai {
  background: var(--bg-elevated);
  color: var(--text-primary);
  border: 1px solid var(--border-light);
  border-bottom-left-radius: var(--radius-sm);
}

.message-text {
  margin: 0 0 var(--space-sm) 0;
  line-height: var(--line-height-relaxed);
  font-size: var(--font-size-sm);
}

.message-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-sm);
}

.message-time {
  font-size: var(--font-size-xs);
  opacity: 0.7;
}

.message-source {
  font-size: var(--font-size-xs);
  opacity: 0.6;
}

/* Typing Indicator */
.typing-wrapper {
  display: flex;
  justify-content: flex-start;
  margin-bottom: var(--space-md);
}

.typing-bubble {
  background: var(--bg-elevated);
  border: 1px solid var(--border-light);
  padding: var(--space-md);
  border-radius: var(--radius-lg);
  border-bottom-left-radius: var(--radius-sm);
  box-shadow: var(--shadow-sm);
}

.typing-dots {
  display: flex;
  gap: var(--space-xs);
}

.typing-dot {
  width: 8px;
  height: 8px;
  background: var(--text-muted);
  border-radius: var(--radius-full);
  animation: typingBounce 1.4s infinite ease-in-out;
}

.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes typingBounce {
  0%, 80%, 100% {
    transform: scale(0);
  }
  40% {
    transform: scale(1);
  }
}

/* Chat Input Section */
.chat-input-section {
  padding: var(--space-lg);
  border-top: 1px solid var(--border-light);
  background: var(--bg-elevated);
  flex-shrink: 0;
}

.input-wrapper {
  display: flex;
  gap: var(--space-sm);
  align-items: end;
}

.chat-input {
  flex: 1;
  padding: var(--space-md);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  font-size: var(--font-size-sm);
  color: var(--text-primary);
  background: var(--bg-base);
  transition: var(--transition-all);
  font-family: inherit;
  resize: none;
}

.chat-input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
}

.chat-input:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background: var(--bg-secondary);
}

.send-btn {
  background: var(--primary);
  color: var(--white);
  border: none;
  width: 44px;
  height: 44px;
  border-radius: var(--radius-lg);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: var(--transition-all);
  flex-shrink: 0;
}

.send-btn:hover:not(:disabled) {
  background: var(--primary-dark);
  transform: translateY(-1px);
  box-shadow: var(--shadow-primary);
}

.send-btn:disabled {
  background: var(--text-muted);
  cursor: not-allowed;
  transform: none;
}

.send-icon {
  width: 18px;
  height: 18px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .gemini-chat-wrapper {
    bottom: var(--space-lg);
    right: var(--space-lg);
  }
  
  .chat-window {
    width: calc(100vw - 32px);
    right: -16px;
    max-width: 380px;
  }
  
  .chat-toggle-btn.ai-avatar-button {
    width: 60px;
    height: 60px;
  }
  
  .ai-eyes {
    gap: 8px;
    margin-bottom: 6px;
  }
  
  .eye {
    width: 6px;
    height: 6px;
  }
  
  .ai-mouth {
    width: 12px;
    height: 6px;
  }

  .message-bubble {
    max-width: 240px;
  }

  .chat-window.fullscreen .message-bubble {
    max-width: 80%;
  }
}

/* High Contrast Support */
@media (prefers-contrast: high) {
  .message-bubble {
    border-width: 2px;
  }
  
  .chat-input:focus {
    border-width: 2px;
  }
}

/* Dark Theme Support */
body.dark-theme .chat-messages {
  background: var(--bg-base);
}

body.dark-theme .typing-dot {
  background: var(--text-secondary);
}
</style> 
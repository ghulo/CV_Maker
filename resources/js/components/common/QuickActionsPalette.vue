<template>
  <Teleport to="body">
    <div
      v-if="isVisible"
      class="palette-overlay"
      @click="close"
      @keydown.esc="close"
    >
      <div
        ref="paletteRef"
        class="command-palette"
        @click.stop
      >
        <!-- Search Header -->
        <div class="palette-header">
          <div class="search-container">
            <svg class="search-icon" viewBox="0 0 24 24">
              <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
            </svg>
            
            <input
              ref="searchRef"
              v-model="searchQuery"
              type="text"
              :placeholder="$t('palette.searchPlaceholder')"
              class="search-input"
              @keydown="handleKeydown"
            />
            
            <div class="search-shortcuts">
              <kbd>{{ metaKey }}</kbd>
              <kbd>K</kbd>
            </div>
          </div>
          
          <!-- Quick Filters -->
          <div class="quick-filters">
            <button
              v-for="filter in quickFilters"
              :key="filter.key"
              class="filter-btn"
              :class="{ 'active': activeFilter === filter.key }"
              @click="setFilter(filter.key)"
            >
              <component :is="filter.icon" class="filter-icon" />
              {{ filter.label }}
            </button>
          </div>
        </div>
        
        <!-- Results Container -->
        <div class="results-container">
          <!-- Recent Actions -->
          <div v-if="showRecent && recentActions.length" class="result-section">
            <div class="section-header">
              <svg viewBox="0 0 24 24">
                <path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
              </svg>
              {{ $t('palette.recent') }}
            </div>
            <div class="result-items">
              <button
                v-for="(action, index) in recentActions"
                :key="`recent-${index}`"
                class="result-item"
                :class="{ 'selected': selectedIndex === index }"
                @click="executeAction(action)"
              >
                <div class="item-icon">
                  <component :is="action.icon" />
                </div>
                <div class="item-content">
                  <div class="item-title">{{ action.title }}</div>
                  <div class="item-description">{{ action.description }}</div>
                </div>
                <div class="item-shortcut">
                  <kbd v-for="key in action.shortcut" :key="key">{{ key }}</kbd>
                </div>
              </button>
            </div>
          </div>
          
          <!-- Filtered Results -->
          <div v-if="filteredResults.length" class="result-section">
            <div class="section-header">
              <component :is="getSectionIcon()" />
              {{ getSectionTitle() }}
            </div>
            <div class="result-items">
              <button
                v-for="(action, index) in filteredResults"
                :key="`result-${index}`"
                class="result-item"
                :class="{ 
                  'selected': selectedIndex === (showRecent ? recentActions.length + index : index),
                  'ai-action': action.category === 'ai',
                  'danger': action.danger
                }"
                @click="executeAction(action)"
              >
                <div class="item-icon">
                  <component :is="action.icon" />
                </div>
                <div class="item-content">
                  <div class="item-title">{{ action.title }}</div>
                  <div class="item-description">{{ action.description }}</div>
                </div>
                <div class="item-meta">
                  <div v-if="action.shortcut" class="item-shortcut">
                    <kbd v-for="key in action.shortcut" :key="key">{{ key }}</kbd>
                  </div>
                  <div v-if="action.category === 'ai'" class="ai-badge">
                    <svg viewBox="0 0 24 24">
                      <path d="M12 2l2.09 6.26L20 10l-5.91 1.74L12 18l-2.09-6.26L4 10l5.91-1.74L12 2z"/>
                    </svg>
                    AI
                  </div>
                </div>
              </button>
            </div>
          </div>
          
          <!-- No Results -->
          <div v-if="!filteredResults.length && !showRecent" class="no-results">
            <div class="no-results-icon">
              <svg viewBox="0 0 24 24">
                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
              </svg>
            </div>
            <div class="no-results-text">
              <div class="no-results-title">{{ $t('palette.noResults') }}</div>
              <div class="no-results-subtitle">
                {{ $t('palette.tryDifferentKeywords') }}
              </div>
            </div>
          </div>
        </div>
        
        <!-- Footer -->
        <div class="palette-footer">
          <div class="footer-shortcuts">
            <div class="shortcut-hint">
              <kbd>↵</kbd>
              <span>{{ $t('palette.execute') }}</span>
            </div>
            <div class="shortcut-hint">
              <kbd>↑↓</kbd>
              <span>{{ $t('palette.navigate') }}</span>
            </div>
            <div class="shortcut-hint">
              <kbd>Esc</kbd>
              <span>{{ $t('palette.close') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false
  },
  actions: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => ['all', 'navigation', 'cv', 'ai', 'settings']
  },
  maxResults: {
    type: Number,
    default: 8
  },
  enableRecent: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['close', 'action'])

// Refs
const paletteRef = ref(null)
const searchRef = ref(null)
const searchQuery = ref('')
const selectedIndex = ref(0)
const activeFilter = ref('all')
const recentActions = ref([])

// Computed
const metaKey = computed(() => {
  return navigator.platform.includes('Mac') ? '⌘' : 'Ctrl'
})

const quickFilters = computed(() => [
  { key: 'all', label: t('palette.filters.all'), icon: 'AllIcon' },
  { key: 'navigation', label: t('palette.filters.navigation'), icon: 'NavigationIcon' },
  { key: 'cv', label: t('palette.filters.cv'), icon: 'DocumentIcon' },
  { key: 'ai', label: t('palette.filters.ai'), icon: 'SparklesIcon' },
  { key: 'settings', label: t('palette.filters.settings'), icon: 'CogIcon' }
])

const filteredResults = computed(() => {
  let results = props.actions
  
  // Filter by category
  if (activeFilter.value !== 'all') {
    results = results.filter(action => action.category === activeFilter.value)
  }
  
  // Filter by search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    results = results.filter(action => {
      return (
        action.title.toLowerCase().includes(query) ||
        action.description.toLowerCase().includes(query) ||
        (action.keywords && action.keywords.some(keyword => 
          keyword.toLowerCase().includes(query)
        ))
      )
    })
  }
  
  // Limit results
  return results.slice(0, props.maxResults)
})

const showRecent = computed(() => {
  return props.enableRecent && 
         !searchQuery.value.trim() && 
         activeFilter.value === 'all' && 
         recentActions.value.length > 0
})

const totalResults = computed(() => {
  let total = filteredResults.value.length
  if (showRecent.value) {
    total += recentActions.value.length
  }
  return total
})

// Methods
const handleKeydown = (event) => {
  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault()
      selectedIndex.value = Math.min(selectedIndex.value + 1, totalResults.value - 1)
      scrollToSelected()
      break
      
    case 'ArrowUp':
      event.preventDefault()
      selectedIndex.value = Math.max(selectedIndex.value - 1, 0)
      scrollToSelected()
      break
      
    case 'Enter':
      event.preventDefault()
      executeSelectedAction()
      break
      
    case 'Escape':
      event.preventDefault()
      close()
      break
      
    case 'Tab':
      event.preventDefault()
      cycleFilter()
      break
  }
}

const executeSelectedAction = () => {
  const allActions = showRecent.value 
    ? [...recentActions.value, ...filteredResults.value]
    : filteredResults.value
    
  const action = allActions[selectedIndex.value]
  if (action) {
    executeAction(action)
  }
}

const executeAction = (action) => {
  // Add to recent actions
  addToRecent(action)
  
  // Execute the action
  if (action.handler) {
    action.handler()
  }
  
  emit('action', action)
  close()
}

const addToRecent = (action) => {
  if (!props.enableRecent) return
  
  // Remove if already exists
  recentActions.value = recentActions.value.filter(a => a.id !== action.id)
  
  // Add to beginning
  recentActions.value.unshift(action)
  
  // Keep only last 5
  recentActions.value = recentActions.value.slice(0, 5)
  
  // Save to localStorage
  try {
    localStorage.setItem('palette-recent', JSON.stringify(recentActions.value))
  } catch (error) {
    console.warn('Failed to save recent actions:', error)
  }
}

const loadRecent = () => {
  if (!props.enableRecent) return
  
  try {
    const saved = localStorage.getItem('palette-recent')
    if (saved) {
      recentActions.value = JSON.parse(saved)
    }
  } catch (error) {
    console.warn('Failed to load recent actions:', error)
    recentActions.value = []
  }
}

const setFilter = (filterKey) => {
  activeFilter.value = filterKey
  selectedIndex.value = 0
  focusSearch()
}

const cycleFilter = () => {
  const currentIndex = quickFilters.value.findIndex(f => f.key === activeFilter.value)
  const nextIndex = (currentIndex + 1) % quickFilters.value.length
  setFilter(quickFilters.value[nextIndex].key)
}

const scrollToSelected = () => {
  nextTick(() => {
    const selectedElement = paletteRef.value?.querySelector('.result-item.selected')
    if (selectedElement) {
      selectedElement.scrollIntoView({
        block: 'nearest',
        behavior: 'smooth'
      })
    }
  })
}

const getSectionIcon = () => {
  const filterConfig = quickFilters.value.find(f => f.key === activeFilter.value)
  return filterConfig ? filterConfig.icon : 'AllIcon'
}

const getSectionTitle = () => {
  if (searchQuery.value.trim()) {
    return t('palette.searchResults', { query: searchQuery.value })
  }
  
  const filterConfig = quickFilters.value.find(f => f.key === activeFilter.value)
  return filterConfig ? filterConfig.label : t('palette.actions')
}

const focusSearch = () => {
  nextTick(() => {
    searchRef.value?.focus()
  })
}

const close = () => {
  searchQuery.value = ''
  selectedIndex.value = 0
  activeFilter.value = 'all'
  emit('close')
}

const reset = () => {
  searchQuery.value = ''
  selectedIndex.value = 0
  activeFilter.value = 'all'
}

// Watchers
watch(() => props.isVisible, (visible) => {
  if (visible) {
    reset()
    loadRecent()
    focusSearch()
  }
})

watch([searchQuery, activeFilter], () => {
  selectedIndex.value = 0
})

// Global keyboard shortcut
const handleGlobalKeydown = (event) => {
  if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
    event.preventDefault()
    if (!props.isVisible) {
      emit('open')
    } else {
      close()
    }
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleGlobalKeydown)
  loadRecent()
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleGlobalKeydown)
})

// Icon Components (you would import these from your icon library)
const AllIcon = () => h('svg', { viewBox: '0 0 24 24' }, [
  h('path', { d: 'M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z' })
])

const NavigationIcon = () => h('svg', { viewBox: '0 0 24 24' }, [
  h('path', { d: 'M12 2l3.09 6.26L22 9l-5.91 1.74L13 17l-2.09-6.26L5 9l5.91-1.74L12 2z' })
])

const DocumentIcon = () => h('svg', { viewBox: '0 0 24 24' }, [
  h('path', { d: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z' })
])

const SparklesIcon = () => h('svg', { viewBox: '0 0 24 24' }, [
  h('path', { d: 'M12 2l2.09 6.26L20 10l-5.91 1.74L12 18l-2.09-6.26L4 10l5.91-1.74L12 2z' })
])

const CogIcon = () => h('svg', { viewBox: '0 0 24 24' }, [
  h('path', { d: 'M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM12 14a2 2 0 1 1 0-4 2 2 0 0 1 0 4z' })
])

// Expose methods
defineExpose({
  close,
  focusSearch
})
</script>

<style scoped>
.palette-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  z-index: 10000;
  display: flex;
  align-items: start;
  justify-content: center;
  padding: 10vh 1rem 1rem;
  animation: overlayIn 0.2s ease-out;
}

@keyframes overlayIn {
  0% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}

.command-palette {
  width: 100%;
  max-width: 640px;
  max-height: 70vh;
  background: white;
  border-radius: 12px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  border: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  animation: paletteIn 0.2s ease-out;
}

@keyframes paletteIn {
  0% {
    opacity: 0;
    transform: scale(0.95) translateY(-20px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.palette-header {
  padding: 1.25rem;
  border-bottom: 1px solid #e5e7eb;
  background: #fafafa;
}

.search-container {
  position: relative;
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
}

.search-icon {
  position: absolute;
  left: 1rem;
  width: 20px;
  height: 20px;
  fill: #9ca3af;
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 3rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 1rem;
  background: white;
  transition: all 0.2s ease;
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-shortcuts {
  position: absolute;
  right: 1rem;
  display: flex;
  gap: 0.25rem;
}

.quick-filters {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.filter-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 6px;
  font-size: 0.75rem;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-btn:hover {
  background: #f3f4f6;
  border-color: #9ca3af;
}

.filter-btn.active {
  background: #3b82f6;
  border-color: #3b82f6;
  color: white;
}

.filter-icon {
  width: 14px;
  height: 14px;
  fill: currentColor;
}

.results-container {
  flex: 1;
  overflow-y: auto;
  max-height: 400px;
}

.result-section {
  padding: 0.75rem 0;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1.25rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  background: #f9fafb;
  border-bottom: 1px solid #f3f4f6;
}

.section-header svg {
  width: 14px;
  height: 14px;
  fill: currentColor;
}

.result-items {
  padding: 0.5rem 0;
}

.result-item {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem 1.25rem;
  border: none;
  background: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
}

.result-item:hover,
.result-item.selected {
  background: #f3f4f6;
}

.result-item.selected {
  background: #eff6ff;
  border-left: 3px solid #3b82f6;
}

.result-item.ai-action {
  background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
}

.result-item.ai-action.selected {
  background: linear-gradient(135deg, #dbeafe 0%, #bae6fd 100%);
}

.result-item.danger:hover {
  background: #fef2f2;
  color: #dc2626;
}

.item-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: #f3f4f6;
  flex-shrink: 0;
}

.item-icon svg {
  width: 20px;
  height: 20px;
  fill: #6b7280;
}

.result-item.ai-action .item-icon {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.result-item.ai-action .item-icon svg {
  fill: white;
}

.item-content {
  flex: 1;
  min-width: 0;
}

.item-title {
  font-weight: 600;
  font-size: 0.875rem;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.item-description {
  font-size: 0.75rem;
  color: #6b7280;
  line-height: 1.4;
}

.item-meta {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.item-shortcut {
  display: flex;
  gap: 0.25rem;
}

.ai-badge {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.25rem 0.5rem;
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  color: white;
  border-radius: 4px;
  font-size: 0.625rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.ai-badge svg {
  width: 10px;
  height: 10px;
  fill: currentColor;
}

.no-results {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1.25rem;
  text-align: center;
}

.no-results-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
}

.no-results-icon svg {
  width: 32px;
  height: 32px;
  fill: #9ca3af;
}

.no-results-title {
  font-weight: 600;
  font-size: 1rem;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.no-results-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
}

.palette-footer {
  padding: 0.75rem 1.25rem;
  border-top: 1px solid #e5e7eb;
  background: #fafafa;
}

.footer-shortcuts {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
}

.shortcut-hint {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
  color: #6b7280;
}

kbd {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 1.5rem;
  height: 1.5rem;
  padding: 0 0.375rem;
  font-size: 0.625rem;
  font-weight: 600;
  background: white;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
  .palette-overlay {
    padding: 5vh 0.5rem 0.5rem;
  }
  
  .command-palette {
    max-height: 80vh;
  }
  
  .palette-header {
    padding: 1rem;
  }
  
  .search-input {
    font-size: 0.875rem;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
  }
  
  .quick-filters {
    gap: 0.375rem;
  }
  
  .filter-btn {
    padding: 0.375rem 0.5rem;
  }
  
  .result-item {
    padding: 0.625rem 1rem;
    gap: 0.75rem;
  }
  
  .item-icon {
    width: 36px;
    height: 36px;
  }
  
  .item-icon svg {
    width: 18px;
    height: 18px;
  }
  
  .footer-shortcuts {
    gap: 1rem;
  }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
  .command-palette {
    background: #1f2937;
    border-color: #374151;
  }
  
  .palette-header {
    background: #111827;
    border-color: #374151;
  }
  
  .search-input {
    background: #374151;
    border-color: #4b5563;
    color: #f9fafb;
  }
  
  .search-input:focus {
    border-color: #3b82f6;
  }
  
  .filter-btn {
    background: #374151;
    border-color: #4b5563;
    color: #d1d5db;
  }
  
  .filter-btn:hover {
    background: #4b5563;
  }
  
  .section-header {
    background: #111827;
    border-color: #374151;
    color: #9ca3af;
  }
  
  .result-item:hover,
  .result-item.selected {
    background: #374151;
  }
  
  .result-item.selected {
    background: #1e3a8a;
  }
  
  .item-title {
    color: #f9fafb;
  }
  
  .item-description {
    color: #d1d5db;
  }
  
  .palette-footer {
    background: #111827;
    border-color: #374151;
  }
  
  kbd {
    background: #374151;
    border-color: #4b5563;
    color: #d1d5db;
  }
}
</style> 
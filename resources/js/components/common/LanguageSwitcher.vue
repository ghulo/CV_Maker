<template>
  <div class="language-switcher">
    <button
      @click="toggleDropdown"
      class="language-btn"
      :aria-expanded="isOpen"
      aria-haspopup="true"
    >
      <FontAwesomeIcon 
        :icon="['fas', 'globe']" 
        class="language-icon"
      />
      <span class="language-text">{{ currentLanguageDisplay }}</span>
      <FontAwesomeIcon 
        :icon="['fas', 'chevron-down']" 
        class="dropdown-arrow"
        :class="{ 'rotate-180': isOpen }"
      />
    </button>
    
    <Transition name="dropdown">
      <div v-if="isOpen" class="language-dropdown">
        <button
          v-for="language in languages"
          :key="language.code"
          @click="changeLanguage(language.code)"
          class="language-option"
          :class="{ 'active': language.code === currentLocale }"
        >
          <span class="flag">{{ language.flag }}</span>
          <span class="name">{{ language.name }}</span>
        </button>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()
const isOpen = ref(false)

const languages = [
  { code: 'en', name: 'English', flag: '🇺🇸' },
  { code: 'sq', name: 'Shqip', flag: '🇦🇱' }
]

const currentLocale = computed(() => locale.value)

const currentLanguageDisplay = computed(() => {
  const current = languages.find(lang => lang.code === currentLocale.value)
  return current ? current.name : 'English'
})

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const changeLanguage = (langCode) => {
  locale.value = langCode
  localStorage.setItem('preferred-language', langCode)
  isOpen.value = false
  
  // Emit a custom event for other components to listen to
  window.dispatchEvent(new CustomEvent('language-changed', { 
    detail: { language: langCode } 
  }))
}

const closeDropdown = (event) => {
  if (!event.target.closest('.language-switcher')) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown)
})
</script>

<style scoped>
.language-switcher {
  position: relative;
  display: inline-block;
}

.language-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
  transition: all 200ms;
  background-color: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: var(--text-secondary);
  backdrop-filter: blur(4px);
}

.language-icon {
  font-size: 0.875rem;
}

.language-text {
  font-size: 0.875rem;
  font-weight: 500;
  display: none;
}

@media (min-width: 640px) {
  .language-text {
    display: block;
  }
}

.dropdown-arrow {
  font-size: 0.75rem;
  transition: transform 200ms;
}

.language-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 0.5rem;
  padding: 0.5rem 0;
  min-width: 140px;
  background-color: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(12px);
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  z-index: 50;
}

body.dark-theme .language-dropdown {
  background-color: rgba(31, 41, 55, 0.95);
  border-color: #374151;
}

.language-option {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 1rem;
  text-align: left;
  color: #374151;
  transition: background-color 150ms, color 150ms;
  border: none;
  background: none;
  cursor: pointer;
}

body.dark-theme .language-option {
  color: #e5e7eb;
}

.language-option:hover {
  background-color: #f3f4f6;
}

body.dark-theme .language-option:hover {
  background-color: #374151;
}

.language-option.active {
  background-color: #eff6ff;
  color: #2563eb;
}

body.dark-theme .language-option.active {
  background-color: rgba(37, 99, 235, 0.3);
  color: #60a5fa;
}

.flag {
  font-size: 1.125rem;
}

.name {
  font-size: 0.875rem;
  font-weight: 500;
}

/* Dropdown animation */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 200ms;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(0.25rem);
}

.dropdown-enter-to,
.dropdown-leave-from {
  opacity: 1;
  transform: scale(1) translateY(0);
}

.rotate-180 {
  transform: rotate(180deg);
}
</style> 
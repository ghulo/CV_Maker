<template>
  <header :class="['header', { 'scrolled': isScrolled }]">
    <div class="header-content-wrapper">
      <!-- New Logo Branding -->
      <router-link to="/" class="header-logo-link">
        <span class="header-logo-svg">
          <!-- Inline SVG logo for premium branding -->
          <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="40" height="40">
            <rect width="64" height="64" rx="16" fill="#10131A"/>
            <path d="M16 48C16 35 32 35 32 48" stroke="#2D9CDB" stroke-width="3" stroke-linecap="round"/>
            <path d="M48 16C35 16 35 32 48 32" stroke="#2D9CDB" stroke-width="3" stroke-linecap="round"/>
            <circle cx="32" cy="32" r="10" stroke="#F2C94C" stroke-width="3"/>
            <circle cx="32" cy="32" r="5" fill="#2D9CDB"/>
          </svg>
        </span>
        <span class="header-logo-text">
          <span class="brand-title">CV Atelier</span>
        </span>
      </router-link>
      <div class="header-right-nav-container">
        <!-- Desktop Navigation Links -->
        <nav id="desktop-nav-menu-id">
          <router-link to="/" :class="{ active: $route.name === 'Homepage' }">{{ t('home') }}</router-link>
          <router-link to="/templates" :class="{ active: $route.name === 'Templates' }">{{ t('templates') }}</router-link>
          <router-link to="/contact" :class="{ active: $route.name === 'Contact' }">{{ t('contact') }}</router-link>
          <router-link v-if="!isAuthenticated" to="/login" class="auth-link login-link">{{ t('login') }}</router-link>
          <router-link v-if="!isAuthenticated" to="/register" class="auth-link signup-link btn btn-accent">{{ t('signup') }}</router-link>
          <router-link v-if="isAuthenticated" to="/dashboard" :class="{ active: $route.name === 'Dashboard' }" class="auth-link profile-link">{{ t('dashboard') }}</router-link>
          <router-link v-if="isAuthenticated" to="/profile" :class="{ active: $route.name === 'Profile' }" class="auth-link profile-link">{{ t('profile') }}</router-link>
          <button v-if="isAuthenticated" @click="logout" class="auth-link logout-link btn">{{ t('logout') }}</button>
        </nav>
        <!-- Language Switcher -->
        <LanguageSwitcher />
        <!-- Theme Toggle Button -->
        <button id="theme-toggle-button" @click="toggleTheme" aria-label="Toggle theme" class="icon-button">
          <i class="fas" :class="{ 'fa-sun': isDarkTheme, 'fa-moon': !isDarkTheme }"></i>
        </button>
        <!-- Hamburger Icon -->
        <button id="main-menu-toggle" class="hamburger-icon icon-button" @click="toggleMainMenu" aria-label="Toggle main menu" :aria-expanded="isMainMenuOpen">
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>
    <!-- Mobile Overlay -->
    <transition name="fade">
      <div v-if="isMainMenuOpen" class="main-nav-overlay" @click.self="closeMainMenu">
        <div :class="['main-nav-menu', { 'open': isMainMenuOpen }]">
          <div class="main-nav-header">
            <router-link to="/" class="header-logo-link" @click="closeMainMenu">
              <span class="header-logo-svg">
                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="36" height="36">
                  <rect width="64" height="64" rx="16" fill="#10131A"/>
                  <path d="M16 48C16 35 32 35 32 48" stroke="#2D9CDB" stroke-width="3" stroke-linecap="round"/>
                  <path d="M48 16C35 16 35 32 48 32" stroke="#2D9CDB" stroke-width="3" stroke-linecap="round"/>
                  <circle cx="32" cy="32" r="10" stroke="#F2C94C" stroke-width="3"/>
                  <circle cx="32" cy="32" r="5" fill="#2D9CDB"/>
                </svg>
              </span>
              <span class="header-logo-text"><span class="brand-title">CV Atelier</span></span>
            </router-link>
            <button @click="closeMainMenu" class="close-main-menu-btn icon-button" aria-label="Close main menu">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <nav class="main-nav-menu-links">
            <router-link to="/" :class="{ active: $route.name === 'Homepage' }" @click="closeMainMenu">{{ t('home') }}</router-link>
            <router-link to="/templates" :class="{ active: $route.name === 'Templates' }" @click="closeMainMenu">{{ t('templates') }}</router-link>
            <router-link to="/contact" :class="{ active: $route.name === 'Contact' }" @click="closeMainMenu">{{ t('contact') }}</router-link>
            <div v-if="!isAuthenticated" class="auth-links-group">
              <router-link to="/login" class="auth-link login-link" @click="closeMainMenu">{{ t('login') }}</router-link>
              <router-link to="/register" class="auth-link signup-link btn btn-accent" @click="closeMainMenu">{{ t('signup') }}</router-link>
            </div>
            <div v-if="isAuthenticated" class="profile-links-group">
              <router-link to="/dashboard" :class="{ active: $route.name === 'Dashboard' }" class="profile-link" @click="closeMainMenu">{{ t('dashboard') }}</router-link>
              <router-link to="/profile" :class="{ active: $route.name === 'Profile' }" class="profile-link" @click="closeMainMenu">{{ t('profile') }}</router-link>
              <button @click="logoutAndCloseMainMenu" class="logout-btn">{{ t('logout') }}</button>
            </div>
            <!-- Language Switcher for Mobile -->
            <div class="mobile-language-switcher">
              <LanguageSwitcher />
            </div>
          </nav>
        </div>
      </div>
    </transition>
  </header>
</template>

<script>
  import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
  import { useRouter, useRoute } from 'vue-router'
  import { useI18n } from 'vue-i18n'
  import { toggleTheme as toggleThemeFunction } from '../../script.js'
  import LanguageSwitcher from '../common/LanguageSwitcher.vue'
  import axios from 'axios'

  export default {
    name: 'Navigation',
    components: {
      LanguageSwitcher
    },
    setup() {
      const { t } = useI18n()
      const router = useRouter()
      const route = useRoute()
      const isAuthenticated = ref(false)
      const isMainMenuOpen = ref(false) // New state for the large menu
      const isScrolled = ref(false)
      const isDarkTheme = ref(document.body.classList.contains('dark-theme')) // Initialize based on current body class

      const checkAuth = () => {
        const token = localStorage.getItem('auth_token')
        isAuthenticated.value = !!token
        return isAuthenticated.value
      }

      const logout = async () => {
        try {
          await axios.post('/api/logout')
        } catch (error) {
          console.error('Logout failed:', error)
        } finally {
          localStorage.removeItem('auth_token')
          localStorage.removeItem('user')
          delete axios.defaults.headers.common['Authorization']
          checkAuth()
          router.push('/login')
        }
      }

      const toggleMainMenu = () => {
        isMainMenuOpen.value = !isMainMenuOpen.value
        document.body.classList.toggle('main-menu-open', isMainMenuOpen.value) // Use main-menu-open class on body
      }

      const closeMainMenu = () => {
        isMainMenuOpen.value = false
        document.body.classList.remove('main-menu-open')
      }

      const logoutAndCloseMainMenu = async () => {
        await logout()
        closeMainMenu()
      }

      const handleClickOutside = (event) => {
        const mainMenuToggle = document.getElementById('main-menu-toggle')
        const mainNavMenu = document.querySelector('.main-nav-menu') // Target the menu itself

        if (isMainMenuOpen.value && mainMenuToggle && mainNavMenu && 
            !mainMenuToggle.contains(event.target) && !mainNavMenu.contains(event.target) &&
            !event.target.closest('.main-nav-menu')) { // Ensure clicks inside the menu don't close it
          closeMainMenu()
        }
      }

      const handleScroll = () => {
        isScrolled.value = window.scrollY > 0
      }

      // Update isDarkTheme reactivity when toggleThemeFunction is called
      const localToggleTheme = () => {
        toggleThemeFunction()
        isDarkTheme.value = document.body.classList.contains('dark-theme')
      }

      onMounted(() => {
        checkAuth()
        window.addEventListener('storage', checkAuth)
        document.addEventListener('mousedown', handleClickOutside)
        window.addEventListener('scroll', handleScroll)
        
        // Listen for language changes to refresh navigation
        window.addEventListener('language-changed', () => {
          // Force re-render by triggering reactivity
          checkAuth()
        })
        
        // The theme initialization is now handled solely by script.js
      })

      onBeforeUnmount(() => {
        document.removeEventListener('mousedown', handleClickOutside)
        window.removeEventListener('storage', checkAuth)
        window.removeEventListener('scroll', handleScroll)
        window.removeEventListener('language-changed', checkAuth)
      })

      watch(route, (newRoute, oldRoute) => {
        checkAuth()
        closeMainMenu() // Close the menu on route change
      })

      return {
        isAuthenticated,
        logout,
        isDarkTheme,
        toggleTheme: localToggleTheme, // Use the local wrapper
        isMainMenuOpen,
        toggleMainMenu,
        closeMainMenu,
        logoutAndCloseMainMenu,
        isScrolled,
        t // Add translation function
      }
    },
  }
</script>

<style scoped>
  .header {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.05);
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: var(--z-sticky);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 72px;
    display: flex;
    align-items: center;
  }
  
  body.dark-theme .header {
    background: rgba(15, 23, 42, 0.8);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  }
  
  .header.scrolled {
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    height: 64px;
  }
  
  body.dark-theme .header.scrolled {
    background: rgba(15, 23, 42, 0.95);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  }
  
  .header-content-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    height: 100%;
  }
  
  .header-logo-link {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    color: var(--text-primary);
    transition: transform 0.3s ease;
  }
  
  .header-logo-link:hover {
    transform: scale(1.05);
  }
  
  .header-logo-svg {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(91, 33, 182, 0.2);
    transition: all 0.3s ease;
  }
  
  .header-logo-link:hover .header-logo-svg {
    box-shadow: 0 6px 20px rgba(91, 33, 182, 0.3);
    transform: rotate(-5deg);
  }
  
  .header-logo-svg svg {
    width: 24px;
    height: 24px;
  }
  
  .header-logo-svg svg rect {
    fill: transparent;
  }
  
  .header-logo-svg svg path,
  .header-logo-svg svg circle {
    stroke: white;
    fill: white;
  }
  
  .header-logo-text .brand-title {
    font-family: 'Inter', sans-serif;
    font-size: 20px;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -0.02em;
  }
  
  .header-right-nav-container {
    display: flex;
    align-items: center;
    gap: 24px;
  }
  
  nav#desktop-nav-menu-id {
    display: flex;
    align-items: center;
    gap: 32px;
  }
  
  nav#desktop-nav-menu-id a {
    color: var(--text-secondary);
    font-size: 15px;
    font-weight: 500;
    position: relative;
    padding: 8px 0;
    transition: color 0.2s ease;
    text-decoration: none;
    cursor: pointer;
    z-index: 10;
    pointer-events: auto;
  }
  
  nav#desktop-nav-menu-id a::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--primary);
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  nav#desktop-nav-menu-id a:hover {
    color: var(--primary);
  }
  
  nav#desktop-nav-menu-id a:hover::after,
  nav#desktop-nav-menu-id a.active::after {
    width: 100%;
  }
  
  nav#desktop-nav-menu-id a.active {
    color: var(--primary);
    font-weight: 600;
  }
  
  .auth-link.signup-link {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white !important;
    padding: 10px 24px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 14px rgba(91, 33, 182, 0.25);
  }
  
  .auth-link.signup-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(91, 33, 182, 0.35);
  }
  
  .auth-link.signup-link:hover::after {
    width: 0 !important;
  }
  
  .icon-button {
    background: rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(0, 0, 0, 0.06);
    border-radius: 10px;
    font-size: 18px;
    cursor: pointer;
    color: var(--text-secondary);
    transition: all 0.2s ease;
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  body.dark-theme .icon-button {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.06);
  }
  
  .icon-button:hover {
    color: var(--primary);
    background: rgba(91, 33, 182, 0.08);
    border-color: rgba(91, 33, 182, 0.2);
    transform: translateY(-2px);
  }
  
  .hamburger-icon {
    display: none;
  }
  
  /* Mobile Menu - Full Screen Overlay */
  .main-nav-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    z-index: var(--z-overlay);
    display: flex;
    justify-content: flex-end;
  }
  
  .main-nav-menu {
    width: 320px;
    height: 100%;
    background: var(--bg-primary);
    box-shadow: -10px 0 30px rgba(0, 0, 0, 0.1);
    transform: translateX(100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow-y: auto;
    display: flex;
    flex-direction: column;
  }
  
  .main-nav-menu.open {
    transform: translateX(0);
  }
  
  body.dark-theme .main-nav-menu {
    background: var(--bg-secondary);
    box-shadow: -10px 0 30px rgba(0, 0, 0, 0.3);
  }
  
  .main-nav-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border);
  }
  
  .main-nav-menu-links {
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }
  
  .main-nav-menu-links a {
    color: var(--text-primary);
    font-size: 16px;
    font-weight: 500;
    text-decoration: none;
    padding: 12px 16px;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  
  .main-nav-menu-links a:hover {
    background: rgba(91, 33, 182, 0.08);
    color: var(--primary);
    transform: translateX(4px);
  }
  
  .main-nav-menu-links a.active {
    background: rgba(91, 33, 182, 0.1);
    color: var(--primary);
    font-weight: 600;
  }
  
  .auth-links-group,
  .profile-links-group {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  
  .logout-btn {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
    border: none;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: left;
  }
  
  .logout-btn:hover {
    background: rgba(239, 68, 68, 0.2);
    transform: translateX(4px);
  }
  
  .mobile-language-switcher {
    margin-top: auto;
    padding: 24px;
    border-top: 1px solid var(--border);
  }
  
  /* Fade transition */
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.3s ease;
  }
  
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
  
  @media (max-width: 900px) {
    nav#desktop-nav-menu-id {
      gap: 20px;
    }
    
    nav#desktop-nav-menu-id a {
      font-size: 14px;
    }
  }
  
  @media (max-width: 768px) {
    nav#desktop-nav-menu-id,
    .desktop-only {
      display: none;
    }
    
    .hamburger-icon {
      display: flex;
    }
    
    .header {
      height: 64px;
    }
    
    .header-content-wrapper {
      padding: 0 16px;
    }
    
    .header-logo-svg {
      width: 38px;
      height: 38px;
    }
    
    .header-logo-text .brand-title {
      font-size: 18px;
    }
  }
</style>
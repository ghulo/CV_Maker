<template>
  <header class="header">
    <div class="header-content-wrapper">
      <router-link to="/" class="header-logo-link-style">
        <span class="header-gemini-icon">CV</span>
        <div class="header-logo-text">
          <h3>CV Maker</h3>
          <p class="header-subtitle">Build your future</p>
        </div>
      </router-link>

      <!-- Desktop Navigation -->
      <nav id="desktop-nav-menu-id" class="hidden md:flex">
        <router-link to="/" :class="{ active: $route.name === 'home' }">Home</router-link>
        <router-link to="/about" :class="{ active: $route.name === 'about' }">About Us</router-link>
        <router-link to="/contact" :class="{ active: $route.name === 'contact' }">Contact</router-link>
        <router-link to="/faq" :class="{ active: $route.name === 'faq' }">FAQ</router-link>

        <!-- Guest Links -->
        <div class="auth-links-container" v-if="!isAuthenticated">
          <router-link to="/login" class="auth-link login-link">Login</router-link>
          <router-link to="/register" class="auth-link signup-link btn">Sign Up</router-link>
        </div>

        <!-- Authenticated Profile Dropdown -->
        <div v-if="isAuthenticated" class="profile-menu-container" ref="profileMenu">
          <button @click="toggleProfileMenu" class="profile-menu-button" aria-label="Profile menu">
            <i class="fas fa-user-circle"></i>
          </button>
          <div v-if="isProfileMenuOpen" class="profile-dropdown">
            <router-link to="/dashboard" class="dropdown-item">Dashboard</router-link>
            <router-link to="/templates" class="dropdown-item">Templates</router-link>
            <router-link to="/profile" class="dropdown-item">Profile</router-link>
            <div class="dropdown-divider"></div>
            <button @click="logout" class="dropdown-item logout-btn">Logout</button>
          </div>
        </div>
      </nav>

      <!-- Mobile Menu Toggle -->
      <button id="mobile-menu-toggle" class="md:hidden hamburger-icon" @click="toggleMobileMenu" aria-label="Toggle mobile menu" :aria-expanded="isMobileMenuOpen">
        <i class="fas fa-bars"></i>
      </button>

      <button id="theme-toggle-button" @click="toggleTheme" aria-label="Toggle theme">
        <i class="fas" :class="{ 'fa-sun': isDarkTheme, 'fa-moon': !isDarkTheme }"></i>
      </button>
    </div>

    <!-- Mobile Navigation Overlay -->
    <div v-if="isMobileMenuOpen" class="mobile-nav-overlay md:hidden">
      <div class="mobile-nav-header">
        <router-link to="/" class="header-logo-link-style" @click="closeMobileMenu">
          <span class="header-gemini-icon">CV</span>
          <div class="header-logo-text">
            <h3>CV Maker</h3>
            <p class="header-subtitle">Build your future</p>
          </div>
        </router-link>
        <button @click="closeMobileMenu" class="close-mobile-menu-btn" aria-label="Close mobile menu">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <nav class="mobile-nav-menu">
        <router-link to="/" :class="{ active: $route.name === 'home' }" @click="closeMobileMenu">Home</router-link>
        <router-link to="/about" :class="{ active: $route.name === 'about' }" @click="closeMobileMenu">About Us</router-link>
        <router-link to="/contact" :class="{ active: $route.name === 'contact' }" @click="closeMobileMenu">Contact</router-link>
        <router-link to="/faq" :class="{ active: $route.name === 'faq' }" @click="closeMobileMenu">FAQ</router-link>

        <div class="mobile-auth-links-container" v-if="!isAuthenticated">
          <router-link to="/login" class="auth-link login-link" @click="closeMobileMenu">Login</router-link>
          <router-link to="/register" class="auth-link signup-link btn" @click="closeMobileMenu">Sign Up</router-link>
        </div>

        <div v-if="isAuthenticated" class="mobile-profile-links">
          <router-link to="/dashboard" class="dropdown-item" @click="closeMobileMenu">Dashboard</router-link>
          <router-link to="/templates" class="dropdown-item" @click="closeMobileMenu">Templates</router-link>
          <router-link to="/profile" class="dropdown-item" @click="closeMobileMenu">Profile</router-link>
          <div class="dropdown-divider"></div>
          <button @click="logoutAndCloseMobileMenu" class="dropdown-item logout-btn">Logout</button>
        </div>
      </nav>
    </div>
  </header>
</template>

<script>
  import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
  import { useRouter, useRoute } from 'vue-router'
  import { toggleTheme as toggleThemeFunction } from '../../script.js'
  import axios from 'axios'

  export default {
    name: 'Navigation',
    setup() {
      const router = useRouter()
      const route = useRoute()
      const isAuthenticated = ref(false)
      const isProfileMenuOpen = ref(false)
      const profileMenu = ref(null)
      const isMobileMenuOpen = ref(false)

      const checkAuth = () => {
        isAuthenticated.value = !!localStorage.getItem('auth_token')
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
          isProfileMenuOpen.value = false
          checkAuth()
          router.push('/login')
        }
      }

      const toggleProfileMenu = () => {
        isProfileMenuOpen.value = !isProfileMenuOpen.value
      }

      const toggleMobileMenu = () => {
        isMobileMenuOpen.value = !isMobileMenuOpen.value
        document.body.classList.toggle('overflow-hidden', isMobileMenuOpen.value);
      };

      const closeMobileMenu = () => {
        isMobileMenuOpen.value = false;
        document.body.classList.remove('overflow-hidden');
      };

      const logoutAndCloseMobileMenu = async () => {
        await logout();
        closeMobileMenu();
      };

      const handleClickOutside = (event) => {
        if (profileMenu.value && !profileMenu.value.contains(event.target)) {
          isProfileMenuOpen.value = false
        }
        if (isMobileMenuOpen.value && !event.target.closest('#mobile-menu-toggle') && !event.target.closest('.mobile-nav-overlay')) {
          closeMobileMenu();
        }
      }

      onMounted(() => {
        checkAuth()
        window.addEventListener('storage', checkAuth)
        document.addEventListener('mousedown', handleClickOutside)
      })

      onBeforeUnmount(() => {
        document.removeEventListener('mousedown', handleClickOutside)
        window.removeEventListener('storage', checkAuth)
      })

      watch(route, () => {
        checkAuth()
        isProfileMenuOpen.value = false
        closeMobileMenu();
      })

      const isDarkTheme = ref(document.body.classList.contains('dark-theme'))

      const toggleTheme = () => {
        toggleThemeFunction()
        isDarkTheme.value = document.body.classList.contains('dark-theme')
      }

      return {
        isAuthenticated,
        logout,
        isDarkTheme,
        toggleTheme,
        isProfileMenuOpen,
        toggleProfileMenu,
        profileMenu,
        isMobileMenuOpen,
        toggleMobileMenu,
        closeMobileMenu,
        logoutAndCloseMobileMenu,
      }
    },
  }
</script>

<style scoped>
  .header {
    background-color: var(--neutral-light);
    border-bottom: 1px solid var(--neutral-border);
    position: sticky;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
  }

  .header.scrolled {
    background-color: var(--header-bg-scrolled);
    backdrop-filter: blur(var(--glass-blur-amount)) saturate(var(--glass-saturation));
    -webkit-backdrop-filter: blur(var(--glass-blur-amount)) saturate(var(--glass-saturation));
    box-shadow: var(--header-shadow-scrolled);
    border-color: rgba(0, 0, 0, 0.08);
  }

  body.dark-theme .header.scrolled {
    background-color: var(--dark-header-bg-scrolled);
    box-shadow: var(--dark-header-shadow-scrolled);
    border-color: rgba(255, 255, 255, 0.1);
  }

  .header-content-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: var(--space-md) var(--space-lg);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  /* Styles related to the logo are now in header.css */

  nav a {
    text-decoration: none;
    color: var(--muted-text);
    font-weight: 500;
    margin: 0 var(--space-md);
    transition: color 0.3s ease;
    position: relative;
    padding: 5px 0;
  }

  nav a:hover,
  nav a.active {
    color: var(--primary);
  }

  nav a::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: var(--primary);
    transform: scaleX(0);
    transform-origin: bottom right;
    transition: transform 0.3s ease-out;
  }

  nav a:hover::after,
  nav a.active::after {
    transform: scaleX(1);
    transform-origin: bottom left;
  }

  .auth-links-container .auth-link {
    margin-left: var(--space-lg);
  }

  .auth-links-container .signup-link {
    margin-left: var(--space-md);
  }

  .profile-menu-container {
    position: relative;
    display: inline-block;
    margin-left: var(--space-lg);
  }

  .profile-menu-button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.8em;
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    transition: color 0.3s ease;
  }

  .profile-menu-button:hover {
    color: var(--primary-hover);
  }

  .profile-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    background-color: var(--neutral-light);
    border: 1px solid var(--neutral-border);
    border-radius: var(--radius-sm);
    box-shadow: var(--shadow-light);
    min-width: 180px;
    padding: var(--space-xs) 0;
    z-index: 1000;
  }

  .profile-dropdown .dropdown-item {
    display: block;
    padding: var(--space-sm) var(--space-md);
    color: var(--neutral-text);
    text-decoration: none;
    transition: background-color 0.2s ease, color 0.2s ease;
  }

  .profile-dropdown .dropdown-item:hover {
    background-color: var(--neutral-border);
    color: var(--primary);
  }

  .profile-dropdown .dropdown-divider {
    border-top: 1px solid var(--neutral-border);
    margin: var(--space-xs) 0;
  }

  .profile-dropdown .logout-btn {
    width: 100%;
    text-align: left;
    border: none;
    background: none;
    cursor: pointer;
  }

  .profile-dropdown .logout-btn:hover {
    background-color: var(--message-error-bg);
    color: var(--message-error-color);
  }

  #theme-toggle-button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.5em;
    color: var(--muted-text);
    margin-left: var(--space-lg);
    transition: color 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  #theme-toggle-button:hover {
    color: var(--primary);
  }

  /* Dark Theme Overrides */
  body.dark-theme .header {
    background-color: var(--dark-neutral-container);
    border-color: var(--dark-neutral-border);
  }

  body.dark-theme .header-logo-text h3 {
    color: var(--dark-neutral-text);
  }

  body.dark-theme .header-logo-text .header-subtitle,
  body.dark-theme nav a {
    color: var(--dark-muted-text);
  }

  body.dark-theme nav a:hover,
  body.dark-theme nav a.active {
    color: var(--dark-primary);
  }

  body.dark-theme nav a::after {
    background-color: var(--dark-primary);
  }

  body.dark-theme .profile-menu-button {
    color: var(--dark-primary);
  }
  body.dark-theme .profile-menu-button:hover {
    color: var(--dark-primary-hover);
  }

  body.dark-theme .profile-dropdown {
    background-color: var(--dark-neutral-container);
    border-color: var(--dark-neutral-border);
    box-shadow: var(--shadow-dark);
  }
  body.dark-theme .profile-dropdown .dropdown-item {
    color: var(--dark-neutral-text);
  }
  body.dark-theme .profile-dropdown .dropdown-item:hover {
    background-color: var(--dark-neutral-border);
    color: var(--dark-primary);
  }
  body.dark-theme .profile-dropdown .dropdown-divider {
    border-color: var(--dark-divider-color);
  }
  body.dark-theme .profile-dropdown .logout-btn:hover {
    background-color: var(--dark-message-error-bg);
    color: var(--dark-message-error-color);
  }

  body.dark-theme #theme-toggle-button {
    color: var(--dark-muted-text);
  }
  body.dark-theme #theme-toggle-button:hover {
    color: var(--dark-primary);
  }

  /* Mobile Navigation Styles */
  .hamburger-icon {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.5em;
    color: var(--muted-text);
    margin-left: var(--space-md);
    transition: color 0.3s ease;
  }

  .hamburger-icon:hover {
    color: var(--primary);
  }

  .mobile-nav-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: var(--neutral-light);
    z-index: 1001;
    display: flex;
    flex-direction: column;
    transform: translateX(100%);
    transition: transform 0.4s var(--animation-ease-out);
  }

  .mobile-nav-overlay.is-open {
    transform: translateX(0);
  }

  .mobile-nav-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-md) var(--space-lg);
    border-bottom: 1px solid var(--neutral-border);
  }

  .close-mobile-menu-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.8em;
    color: var(--muted-text);
    transition: color 0.3s ease;
  }

  .close-mobile-menu-btn:hover {
    color: var(--primary);
  }

  .mobile-nav-menu {
    display: flex;
    flex-direction: column;
    padding: var(--space-lg);
    flex-grow: 1;
    overflow-y: auto;
  }

  .mobile-nav-menu a,
  .mobile-nav-menu button {
    display: block;
    padding: var(--space-md) var(--space-sm);
    text-align: center;
    text-decoration: none;
    color: var(--neutral-text);
    font-size: 1.2em;
    font-weight: 500;
    border-bottom: 1px solid var(--divider-color);
    transition: background-color 0.2s ease, color 0.2s ease;
  }

  .mobile-nav-menu a:hover,
  .mobile-nav-menu a.active,
  .mobile-nav-menu button:hover {
    background-color: var(--neutral-border);
    color: var(--primary);
  }

  .mobile-nav-menu .auth-link,
  .mobile-nav-menu .dropdown-item {
    text-align: center;
    padding: var(--space-md) var(--space-sm);
  }

  .mobile-nav-menu .signup-link {
    margin-top: var(--space-md);
  }

  .mobile-auth-links-container, .mobile-profile-links {
    margin-top: var(--space-xl);
    border-top: 1px solid var(--divider-color);
    padding-top: var(--space-lg);
  }

  /* Dark theme for mobile navigation */
  body.dark-theme .mobile-nav-overlay {
    background-color: var(--dark-neutral-container);
  }
  body.dark-theme .mobile-nav-header {
    border-color: var(--dark-neutral-border);
  }
  body.dark-theme .close-mobile-menu-btn {
    color: var(--dark-muted-text);
  }
  body.dark-theme .close-mobile-menu-btn:hover {
    color: var(--dark-primary);
  }
  body.dark-theme .mobile-nav-menu a,
  body.dark-theme .mobile-nav-menu button {
    color: var(--dark-neutral-text);
    border-color: var(--dark-divider-color);
  }
  body.dark-theme .mobile-nav-menu a:hover,
  body.dark-theme .mobile-nav-menu a.active,
  body.dark-theme .mobile-nav-menu button:hover {
    background-color: var(--dark-neutral-border);
    color: var(--dark-primary);
  }

</style>

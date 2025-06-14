<template>
  <header class="header">
    <div class="header-content-wrapper">
      <router-link to="/" class="logo-link">
        <span class="gemini-icon">CV</span>
        <div class="logo-text">
          <h1>CV Maker</h1>
          <p class="header-subtitle">Build your future</p>
        </div>
      </router-link>

      <nav id="desktop-nav-menu-id">
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

      <button id="theme-toggle-button" @click="toggleTheme" aria-label="Toggle theme">
        <i class="fas" :class="{ 'fa-sun': isDarkTheme, 'fa-moon': !isDarkTheme }"></i>
      </button>
    </div>
  </header>
</template>

<script>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { toggleTheme as toggleThemeFunction } from '../../script.js'
import axios from 'axios';

export default {
  name: 'Navigation',
  setup() {
    const router = useRouter();
    const route = useRoute();
    const isAuthenticated = ref(false);
    const isProfileMenuOpen = ref(false);
    const profileMenu = ref(null);

    const checkAuth = () => {
      isAuthenticated.value = !!localStorage.getItem('auth_token');
    };

    const logout = async () => {
      try {
        await axios.post('/api/logout');
      } catch (error) {
        console.error('Logout failed:', error);
      } finally {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        delete axios.defaults.headers.common['Authorization'];
        isProfileMenuOpen.value = false;
        checkAuth();
        router.push('/login');
      }
    };

    const toggleProfileMenu = () => {
      isProfileMenuOpen.value = !isProfileMenuOpen.value;
    };
    
    const handleClickOutside = (event) => {
      if (profileMenu.value && !profileMenu.value.contains(event.target)) {
        isProfileMenuOpen.value = false;
      }
    };

    onMounted(() => {
      checkAuth();
      window.addEventListener('storage', checkAuth);
      document.addEventListener('mousedown', handleClickOutside);
    });
    
    onBeforeUnmount(() => {
        document.removeEventListener('mousedown', handleClickOutside);
        window.removeEventListener('storage', checkAuth);
    });

    watch(route, () => {
      checkAuth();
      isProfileMenuOpen.value = false; // Close menu on route change
    });
    
    const isDarkTheme = ref(document.body.classList.contains('dark-theme'));
    
    const toggleTheme = () => {
        toggleThemeFunction();
        isDarkTheme.value = document.body.classList.contains('dark-theme');
    };

    return {
      isAuthenticated,
      logout,
      isDarkTheme,
      toggleTheme,
      isProfileMenuOpen,
      toggleProfileMenu,
      profileMenu
    }
  }
}
</script>

<style scoped>
/* Main Header and Nav structure */
.header {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(10px);
  padding: 0.5rem 2rem; /* Adjusted padding for height */
  position: sticky;
  top: 0;
  z-index: 1000;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  transition: background-color 0.3s, border-color 0.3s;
}

.dark-theme .header {
  background: rgba(20, 20, 22, 0.7);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.header-content-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  height: 60px; /* Enforce a consistent height */
}

nav {
  display: flex;
  align-items: center;
  gap: 1rem;
}

/* Logo */
.logo-link {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: inherit;
}
.gemini-icon {
  font-weight: bold;
  background-color: #4A90E2;
  color: white;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  font-size: 1.2rem;
  margin-right: 0.5rem;
}
.logo-text h1 {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0;
}
.header-subtitle {
  font-size: 0.8rem;
  opacity: 0.7;
  margin: 0;
}

/* Navigation Links */
nav a {
  text-decoration: none;
  color: #333;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  transition: background-color 0.2s, color 0.2s;
}

.dark-theme nav a {
  color: #f1f1f1;
}

nav a:hover {
  background-color: #f0f0f0;
}

.dark-theme nav a:hover {
  background-color: #333;
}

nav a.active {
  font-weight: bold;
  color: #4A90E2;
}

/* Auth Links (Login/Sign Up) */
.auth-links-container {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-left: 1rem;
}

.signup-link.btn {
  background-color: #4A90E2;
  color: white;
  padding: 0.6rem 1.2rem;
}

.signup-link.btn:hover {
  background-color: #357ABD;
}

/* Profile Dropdown Menu */
.profile-menu-container {
  position: relative;
  margin-left: 1rem;
}

.profile-menu-button {
  background: none;
  border: none;
  cursor: pointer;
  color: #333;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
}

.dark-theme .profile-menu-button {
  color: #f1f1f1;
}

.profile-menu-button:hover {
  background-color: #f0f0f0;
}
.dark-theme .profile-menu-button:hover {
  background-color: #333;
}

.profile-menu-button i {
  font-size: 1.8rem; /* Make icon larger */
}

.profile-dropdown {
  position: absolute;
  top: 120%;
  right: 0;
  background: white;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  width: 200px;
  overflow: hidden;
  z-index: 1100;
  display: flex;
  flex-direction: column;
}

.dark-theme .profile-dropdown {
  background: #25282c;
  border-color: #444;
}

.profile-dropdown .dropdown-item {
  padding: 0.75rem 1.25rem;
  color: #333;
  text-decoration: none;
  display: block;
  width: 100%;
  text-align: left;
  border: none;
  background: none;
  cursor: pointer;
}

.dark-theme .profile-dropdown .dropdown-item {
  color: #f1f1f1;
}

.profile-dropdown .dropdown-item:hover {
  background-color: #f0f0f0;
}
.dark-theme .profile-dropdown .dropdown-item:hover {
  background-color: #333;
}

.dropdown-divider {
  height: 1px;
  background-color: #eee;
  margin: 0.5rem 0;
}
.dark-theme .dropdown-divider {
  background-color: #444;
}

/* Theme Toggle Button */
#theme-toggle-button {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.5rem;
  color: #333;
}
.dark-theme #theme-toggle-button {
  color: #f1f1f1;
}
</style>

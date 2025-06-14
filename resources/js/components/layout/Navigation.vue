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
        <router-link to="/templates" :class="{ active: $route.name === 'templates' }">Templates</router-link>
        <router-link v-if="!isAuthenticated" to="/login" class="auth-link login-link">Login</router-link>
        <router-link v-if="!isAuthenticated" to="/register" class="auth-link signup-link btn">Sign Up</router-link>
        <router-link v-if="isAuthenticated" to="/dashboard" class="auth-link">Dashboard</router-link>
        <button v-if="isAuthenticated" @click="logout" class="auth-link logout-btn">Logout</button>
      </nav>

      <button id="theme-toggle-button" @click="toggleTheme" aria-label="Toggle theme">
        <i class="fas" :class="{ 'fa-sun': isDarkTheme, 'fa-moon': !isDarkTheme }"></i>
      </button>
    </div>
  </header>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { toggleTheme as toggleThemeFunction } from '../../script.js'

export default {
  name: 'Navigation',
  setup() {
    const router = useRouter()
    const isDarkTheme = ref(false)
    const isMobileMenuOpen = ref(false)
    
    const isAuthenticated = computed(() => {
      return !!localStorage.getItem('auth_token')
    })

    const toggleTheme = () => {
      toggleThemeFunction()
    }

    const logout = () => {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      router.push({ name: 'login' })
    }

    onMounted(() => {
      // Logic to set initial theme is now in script.js
    })

    return {
      isDarkTheme,
      isAuthenticated,
      toggleTheme,
      logout,
      isMobileMenuOpen
    }
  }
}
</script>

<style scoped>
@reference "tailwindcss/theme";

.header {
  height: 60px;
}

.header-content-wrapper {
  padding: 6px var(--space-md);
}

.logo-link {
  display: flex;
  align-items: center;
}

.gemini-icon {
  width: 40px;
  height: 40px;
  font-size: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-text h1 {
  font-size: 1.4em;
}

.header-subtitle {
  font-size: 0.75em;
}

#desktop-nav-menu-id a {
  padding: 6px 12px;
  font-size: 0.9em;
}

.logout-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: inherit;
  font-size: inherit;
  text-decoration: none;
  padding: 6px 12px;
  font-size: 0.9em;
}

.logout-btn:hover {
  color: var(--primary-color);
}
</style>

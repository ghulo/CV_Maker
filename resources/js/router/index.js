import { createRouter, createWebHistory } from 'vue-router'
import Homepage from '../components/pages/Homepage.vue'
import AboutUs from '../components/pages/AboutUs.vue'
import Contact from '../components/pages/Contact.vue'
import FAQ from '../components/pages/FAQ.vue'
import Login from '../components/auth/Login.vue'
import Register from '../components/auth/Register.vue'
import Dashboard from '../components/pages/Dashboard.vue'
import Templates from '../components/pages/Templates.vue'
import Profile from '../components/pages/Profile.vue'
import CreateCV from '../components/pages/CreateCV.vue'
import EditCV from '../components/pages/EditCV.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: Homepage
  },
  {
    path: '/about',
    name: 'about',
    component: AboutUs
  },
  {
    path: '/contact',
    name: 'contact',
    component: Contact
  },
  {
    path: '/faq',
    name: 'faq',
    component: FAQ
  },
  {
    path: '/profile',
    name: 'profile',
    component: Profile,
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: { requiresGuest: true }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/templates',
    name: 'templates',
    component: Templates
  },
  {
    path: '/cv/create',
    name: 'cv.create',
    component: CreateCV,
    meta: { requiresAuth: true }
  },
  {
    path: '/cv/:id/edit',
    name: 'cv.edit',
    component: EditCV,
    meta: { requiresAuth: true },
    props: true
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guards for authentication
router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('auth_token')
  
  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: 'login', query: { redirect: to.fullPath } })
  } else if (to.meta.requiresGuest && isAuthenticated) {
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router

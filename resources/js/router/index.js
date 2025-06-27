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
import PrivacyPolicy from '../components/pages/PrivacyPolicy.vue'
import TermsOfService from '../components/pages/TermsOfService.vue'
import { useLiquidAnimations } from '../liquidAnimations'

const routes = [
  {
    path: '/',
    name: 'home',
    component: Homepage,
  },
  {
    path: '/about',
    name: 'about',
    component: AboutUs,
  },
  {
    path: '/contact',
    name: 'contact',
    component: Contact,
  },
  {
    path: '/faq',
    name: 'faq',
    component: FAQ,
  },
  {
    path: '/privacy-policy',
    name: 'privacy-policy',
    component: PrivacyPolicy,
  },
  {
    path: '/terms-of-service',
    name: 'terms-of-service',
    component: TermsOfService,
  },
  {
    path: '/profile',
    name: 'profile',
    component: Profile,
    meta: { requiresAuth: true },
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { requiresGuest: true },
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: { requiresGuest: true },
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: '/templates',
    name: 'templates',
    component: Templates,
  },
  {
    path: '/create-cv',
    name: 'create-cv',
    component: CreateCV,
    meta: { requiresAuth: true },
  },
  {
    path: '/edit-cv/:id',
    name: 'edit-cv',
    component: EditCV,
    props: true,
    meta: { requiresAuth: true },
  },
  {
    path: '/cv/:id/preview',
    name: 'cv.preview',
    component: () => import('../components/pages/PreviewCV.vue'),
    meta: { requiresAuth: true },
    props: true,
  },
  // Redirect old preview URL pattern to new one
  {
    path: '/preview-cv/:id',
    redirect: to => {
      return { path: `/cv/${to.params.id}/preview` }
    },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('../components/pages/NotFound.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
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

// Refresh animations after route change
router.afterEach((to, from) => {
  // Small delay to ensure DOM is updated
  setTimeout(() => {
    const liquidAnimations = useLiquidAnimations()
    liquidAnimations.refresh()
  }, 50)
})

export default router

import { createRouter, createWebHistory } from 'vue-router'
import authService from '@/services/authService'

// Lazy load all components for better performance
const routes = [
  {
    path: '/',
    name: 'Homepage',
    component: () => import('@/components/pages/Homepage.vue'),
    meta: { 
      title: 'CV Maker - Create Professional CVs in Minutes',
      description: 'Create professional CVs with our AI-powered platform. Choose from modern templates and build your career story.',
      requiresAuth: false
    }
  },
  {
    path: '/create-cv',
    name: 'CreateCV',
    component: () => import('@/components/pages/CreateCV.vue'),
    meta: { 
      title: 'Create Your CV',
      description: 'Step-by-step CV creation with AI assistance',
      requiresAuth: true
    }
  },
  {
    path: '/edit-cv/:id',
    name: 'EditCV',
    component: () => import('@/components/pages/EditCV.vue'),
    props: true,
    meta: { 
      title: 'Edit Your CV',
      description: 'Edit and improve your existing CV',
      requiresAuth: true
    }
  },
  {
    path: '/preview-cv/:id',
    name: 'PreviewCV', 
    component: () => import('@/components/pages/PreviewCV.vue'),
    props: true,
    meta: { 
      title: 'Preview Your CV',
      description: 'Preview and download your professional CV',
      requiresAuth: true
    }
  },
  {
    path: '/cv/:id/preview',
    name: 'cv.preview', 
    component: () => import('@/components/pages/PreviewCV.vue'),
    props: true,
    meta: { 
      title: 'Preview Your CV',
      description: 'Preview and download your professional CV',
      requiresAuth: true
    }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/components/pages/Dashboard.vue'),
    meta: { 
      title: 'Your Dashboard',
      description: 'Manage all your CVs in one place',
      requiresAuth: true
    }
  },
  {
    path: '/templates',
    name: 'Templates',
    component: () => import('@/components/pages/Templates.vue'),
    meta: { 
      title: 'CV Templates',
      description: 'Choose from professional CV templates',
      requiresAuth: false
    }
  },
  {
    path: '/contact',
    name: 'Contact',
    component: () => import('@/components/pages/Contact.vue'),
    meta: { 
      title: 'Contact Us',
      description: 'Get in touch with our support team',
      requiresAuth: false
    }
  },
  {
    path: '/about',
    name: 'AboutUs',
    component: () => import('@/components/pages/AboutUs.vue'),
    meta: { 
      title: 'About CV Maker',
      description: 'Learn more about our mission and team',
      requiresAuth: false
    }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('@/components/pages/Profile.vue'),
    meta: { 
      title: 'Your Profile',
      description: 'Manage your account settings',
      requiresAuth: true
    }
  },
  {
    path: '/faq',
    name: 'FAQ',
    component: () => import('@/components/pages/FAQ.vue'),
    meta: { 
      title: 'Frequently Asked Questions',
      description: 'Find answers to common questions',
      requiresAuth: false
    }
  },
  {
    path: '/privacy',
    name: 'PrivacyPolicy',
    component: () => import('@/components/pages/PrivacyPolicy.vue'),
    meta: { 
      title: 'Privacy Policy',
      description: 'Our commitment to protecting your privacy',
      requiresAuth: false
    }
  },
  {
    path: '/terms',
    name: 'TermsOfService',
    component: () => import('@/components/pages/TermsOfService.vue'),
    meta: { 
      title: 'Terms of Service',
      description: 'Terms and conditions for using our service',
      requiresAuth: false
    }
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/components/auth/Login.vue'),
    meta: { 
      title: 'Login to CV Maker',
      description: 'Sign in to your account',
      requiresAuth: false,
      guest: true
    }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/components/auth/Register.vue'),
    meta: { 
      title: 'Create Your Account',
      description: 'Join thousands of professionals using CV Maker',
      requiresAuth: false,
      guest: true
    }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/components/pages/NotFound.vue'),
    meta: { 
      title: 'Page Not Found',
      description: 'The page you are looking for does not exist',
      requiresAuth: false
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    // Better scroll behavior for SPA
    if (savedPosition) {
      return savedPosition
    } else if (to.hash) {
      return { el: to.hash, behavior: 'smooth' }
    } else {
      return { top: 0, behavior: 'smooth' }
    }
  }
})

// Global navigation guards
router.beforeEach(async (to, from, next) => {
  // Set page title and meta
  document.title = to.meta.title || 'CV Maker'
  
  // Update meta description
  const metaDescription = document.querySelector('meta[name="description"]')
  if (metaDescription && to.meta.description) {
    metaDescription.setAttribute('content', to.meta.description)
  }

  // Authentication logic
  const isAuthenticated = authService.isAuthenticated()
  
  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: 'Login', query: { redirect: to.fullPath } })
  } else if (to.meta.guest && isAuthenticated) {
    next({ name: 'Dashboard' })
  } else {
    next()
  }
})

export default router


/**
 * Applies a specified theme by adding or removing the 'dark-theme' class from the body.
 * @param {string} theme - The theme to apply ('dark' or 'light').
 */
function applyTheme(theme) {
  document.body.classList.remove('dark-theme', 'light-theme')
  if (theme === 'dark') {
    document.body.classList.add('dark-theme')
  } else {
    document.body.classList.add('light-theme')
  }
}

/**
 * Toggles the theme between 'light' and 'dark' and saves the preference.
 */
export function toggleTheme() {
  const isDarkMode = document.body.classList.contains('dark-theme')
  const newTheme = isDarkMode ? 'light' : 'dark'
  applyTheme(newTheme)
  localStorage.setItem('theme', newTheme)
  
  // Dispatch a theme change event that components can listen to
  window.dispatchEvent(new CustomEvent('theme-changed', { 
    detail: { theme: newTheme } 
  }))
}

/**
 * Initializes the theme based on saved preference in localStorage or system preference.
 */
function initializeTheme() {
  const savedTheme = localStorage.getItem('theme')
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches

  if (savedTheme) {
    applyTheme(savedTheme)
  } else {
    // Use system preference as default
    applyTheme(prefersDark ? 'dark' : 'light')
    localStorage.setItem('theme', prefersDark ? 'dark' : 'light')
  }
}

/**
 * Initializes all global visual effects.
 * This should be called once when the main App component is mounted.
 */
export function initializeEffects() {
  initializeTheme()
  initScrollReveal()

  // Add event listener for system theme changes
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!localStorage.getItem('theme')) { // Only auto-switch if user hasn't set a preference
      applyTheme(e.matches ? 'dark' : 'light')
    }
  })
}

// ===============================
// Scroll-reveal (fade / slide-in)
// ===============================

let scrollRevealObserver = null

function createScrollRevealObserver() {
  // If IntersectionObserver isn't supported, just make everything visible
  if (!('IntersectionObserver' in window)) {
    document.querySelectorAll('.reveal-on-scroll').forEach((el) => {
      el.classList.add('reveal-visible')
    })
    return null
  }

  const options = {
    threshold: 0.15,
  }

  return new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('reveal-visible')
        observer.unobserve(entry.target)
      }
    })
  }, options)
}

export function initScrollReveal() {
  // Disconnect previous observer (in case of HMR / route change)
  if (scrollRevealObserver) scrollRevealObserver.disconnect()

  scrollRevealObserver = createScrollRevealObserver()
  if (!scrollRevealObserver) return // legacy browser fallback handled above

  document.querySelectorAll('.reveal-on-scroll').forEach((el) => {
    el.classList.remove('reveal-visible')
    scrollRevealObserver.observe(el)
  })
}

export function reinitializeScrollReveal() {
  initScrollReveal()
}

// Add minimal CSS via JS for `.reveal-on-scroll` if it doesn't exist – ensures no FOUC
if (typeof window !== 'undefined') {
  const styleId = 'scroll-reveal-inline-style'
  if (!document.getElementById(styleId)) {
    const style = document.createElement('style')
    style.id = styleId
    style.innerHTML = `
      .reveal-on-scroll { opacity: 0; transform: translateY(20px); transition: opacity 0.6s ease, transform 0.6s ease; }
      .reveal-on-scroll.reveal-visible { opacity: 1; transform: none; }
    `
    document.head.appendChild(style)
  }
}

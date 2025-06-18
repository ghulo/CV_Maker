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
}

/**
 * Initializes the theme based on saved preference in localStorage or system preference.
 */
function initializeTheme() {
  const savedTheme = localStorage.getItem('theme')
  const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches

  if (savedTheme) {
    applyTheme(savedTheme)
  } else if (prefersDark) {
    applyTheme('dark')
  } else {
    applyTheme('light')
  }
}

/**
 * Initializes IntersectionObserver for scroll-reveal animations.
 * @param {string} selector - The CSS selector for elements to reveal.
 */
function initScrollReveal(selector = '.reveal-on-scroll') {
  const revealElements = document.querySelectorAll(selector)

  if (revealElements.length === 0) return

  const revealObserver = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const delay = parseInt(entry.target.getAttribute('data-reveal-delay') || '0', 10)
          setTimeout(() => {
            entry.target.classList.add('is-visible')
          }, delay)
          observer.unobserve(entry.target)
        }
      })
    },
    {
      threshold: 0.1,
    }
  )

  revealElements.forEach((el) => {
    el.classList.remove('is-visible')
    revealObserver.observe(el)
  })
}

/**
 * Initializes all global visual effects.
 * This should be called once when the main App component is mounted.
 */
export function initializeEffects() {
  initializeTheme()

  // Add event listener for the theme toggle button inside the main initialization
  const themeToggleButton = document.getElementById('theme-toggle-button')
  if (themeToggleButton) {
    themeToggleButton.addEventListener('click', toggleTheme)
  }

  initScrollReveal()
}

/**
 * Re-initializes scroll reveal effects.
 * Useful after a route change when new elements are added to the DOM.
 */
export function reinitializeScrollReveal() {
  // A short delay may be necessary for Vue to update the DOM
  setTimeout(() => {
    initScrollReveal()
  }, 250)
}

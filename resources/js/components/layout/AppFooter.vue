<template>
  <footer class="site-footer">
    <div class="footer-container">
      <div class="footer-grid">
        <div class="footer-column footer-about">
          <router-link to="/" class="footer-logo">
            <span class="footer-logo-svg">
              <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="40" height="40">
                <rect width="64" height="64" rx="16" fill="transparent"/>
                <path d="M16 48C16 35 32 35 32 48" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round"/>
                <path d="M48 16C35 16 35 32 48 32" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round"/>
                <circle cx="32" cy="32" r="10" stroke="var(--accent)" stroke-width="2.5"/>
                <circle cx="32" cy="32" r="5" fill="var(--primary)"/>
              </svg>
            </span>
            <span class="footer-logo-text">
              <h3>CV Atelier</h3>
              <p class="footer-subtitle">Build your future</p>
            </span>
          </router-link>
          <p class="footer-about-text">
            Craft professional CVs with ease. Our tools and templates help you stand out and land your dream job.
          </p>
        </div>
        <div class="footer-column">
          <h3 class="footer-column-title">Quick Links</h3>
          <ul class="footer-links-list">
            <li><router-link to="/">Home</router-link></li>
            <li><router-link to="/about">About Us</router-link></li>
            <li><router-link to="/templates">Templates</router-link></li>
            <li><router-link to="/contact">Contact</router-link></li>
          </ul>
        </div>
        <div class="footer-column">
          <h3 class="footer-column-title">Legal & Support</h3>
          <ul class="footer-links-list">
            <li><router-link to="/faq">FAQ</router-link></li>
            <li><router-link to="/privacy">Privacy Policy</router-link></li>
            <li><router-link to="/terms">Terms of Service</router-link></li>
          </ul>
        </div>
        <div class="footer-column">
          <h3 class="footer-column-title">Follow Us</h3>
          <div class="footer-social-links">
            <a href="#" aria-label="Facebook" class="footer-social-icon"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Twitter" class="footer-social-icon"><i class="fab fa-twitter"></i></a>
            <a href="#" aria-label="LinkedIn" class="footer-social-icon"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" aria-label="Instagram" class="footer-social-icon"><i class="fab fa-instagram"></i></a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p class="footer-copyright">
          &copy; {{ new Date().getFullYear() }} CV Atelier. All rights reserved.
        </p>
        <p class="footer-tagline">Designed to help you succeed.</p>
      </div>
    </div>
  </footer>
</template>

<script>
  export default {
    name: 'AppFooter',
    setup() {
      return {}
    },
  }
</script>

<style scoped>
  .site-footer {
    background: var(--bg-secondary);
    color: var(--text-secondary);
    padding: var(--space-xxl) var(--space-lg) var(--space-xl) var(--space-lg);
    border-top: 1px solid var(--border);
    margin-top: auto;
    width: 100%;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
    z-index: 0;
    transition: var(--transition-all);
  }
  .footer-container {
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
  }
  .footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--space-xl);
    text-align: left;
    margin-bottom: var(--space-xl);
  }
  .footer-column-title {
    font-size: var(--font-size-lg);
    font-weight: 700;
    margin-bottom: var(--space-md);
    color: var(--text-primary);
    letter-spacing: normal;
    text-transform: none;
  }
  .footer-links-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  .footer-links-list li {
    margin-bottom: var(--space-sm);
  }
  .footer-links-list a {
    color: var(--text-secondary);
    text-decoration: none;
    transition: var(--transition-all);
    font-size: var(--font-size-base);
    border-bottom: 1px solid transparent;
    padding-bottom: 2px;
  }
  .footer-links-list a:hover {
    color: var(--primary);
    border-bottom: 1px solid var(--primary);
  }
  .footer-about-text,
  .footer-subtitle {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    line-height: var(--line-height-normal);
    margin-top: var(--space-xs);
  }
  .footer-logo {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    text-decoration: none;
    color: var(--text-primary);
    margin-bottom: var(--space-md);
  }
  .footer-logo-svg {
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border-radius: var(--radius-full);
    box-shadow: none;
    padding: 0;
  }
  .footer-logo-text h3 {
    font-size: var(--font-size-xl);
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    letter-spacing: normal;
    text-transform: none;
    background: none;
    -webkit-background-clip: unset;
    -webkit-text-fill-color: unset;
    background-clip: unset;
  }
  .footer-social-links {
    display: flex;
    gap: var(--space-md);
    margin-top: var(--space-md);
  }
  .footer-social-icon {
    color: var(--text-secondary);
    font-size: 1.5em;
    transition: var(--transition-all);
  }
  .footer-social-icon:hover {
    color: var(--primary);
    transform: translateY(-2px) scale(1.05);
  }
  .footer-bottom {
    text-align: center;
    margin-top: var(--space-xxl);
    padding-top: var(--space-lg);
    border-top: 1px solid var(--border);
  }
  .footer-copyright,
  .footer-tagline {
    font-size: var(--font-size-sm);
    color: var(--text-tertiary);
  }
  .footer-copyright {
    margin-bottom: var(--space-xs);
  }
  @media (max-width: 768px) {
    .site-footer {
      padding: var(--space-xl) var(--space-md);
    }
    .footer-grid {
      grid-template-columns: 1fr;
      text-align: center;
      gap: var(--space-lg);
    }
    .footer-column-title {
      margin-top: var(--space-lg);
      margin-bottom: var(--space-md);
    }
    .footer-logo {
      justify-content: center;
      margin-bottom: var(--space-lg);
    }
    .footer-social-links {
      justify-content: center;
      margin-top: var(--space-lg);
    }
    .footer-links-list li {
      margin-bottom: var(--space-xs);
    }
  }

  /* Dark Theme Overrides */
  body.dark-theme .site-footer {
    background: var(--bg-elevated);
    color: var(--text-secondary);
    border-color: var(--border);
  }

  body.dark-theme .footer-column-title {
    color: var(--text-primary);
  }

  body.dark-theme .footer-links-list a {
    color: var(--text-secondary);
  }

  body.dark-theme .footer-links-list a:hover {
    color: var(--primary-light);
    border-bottom-color: var(--primary-light);
  }

  body.dark-theme .footer-about-text,
  body.dark-theme .footer-subtitle {
    color: var(--text-secondary);
  }

  body.dark-theme .footer-logo {
    color: var(--text-primary);
  }

  body.dark-theme .footer-logo-text h3 {
    color: var(--text-primary);
  }

  body.dark-theme .footer-social-icon {
    color: var(--text-secondary);
  }

  body.dark-theme .footer-social-icon:hover {
    color: var(--primary-light);
  }

  body.dark-theme .footer-bottom {
    border-color: var(--border);
  }

  body.dark-theme .footer-copyright,
  body.dark-theme .footer-tagline {
    color: var(--text-tertiary);
  }
</style>

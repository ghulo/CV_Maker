<template>
  <footer class="site-footer reveal-on-scroll">
    <div class="footer-container">
      <div class="footer-grid">
        <div class="footer-column footer-about">
          <div class="footer-logo-box">
            <router-link to="/" class="footer-logo-link-style">
              <span class="footer-gemini-icon">CV</span>
              <div class="footer-logo-text">
                <h3>CV Maker</h3>
                <p class="footer-subtitle">Build your future</p>
              </div>
            </router-link>
          </div>
          <p class="footer-about-text">
            Craft professional CVs with ease. Our tools and templates help you
            stand out and land your dream job.
          </p>
        </div>

        <div class="footer-column">
          <h3 class="footer-column-title">Quick Links</h3>
          <ul class="footer-links-list">
            <li><router-link to="/">Home</router-link></li>
            <li><router-link to="/about">About Us</router-link></li>
            <li><router-link to="/login">Login</router-link></li>
            <li><router-link to="/register">Sign Up</router-link></li>
          </ul>
        </div>

        <div class="footer-column">
          <h3 class="footer-column-title">Support & Legal</h3>
          <ul class="footer-links-list">
            <li><router-link to="/faq">FAQ</router-link></li>
            <li><router-link to="/contact">Contact Us</router-link></li>
            <li><router-link to="/privacy">Privacy Policy</router-link></li>
            <li><router-link to="/terms">Terms of Service</router-link></li>
          </ul>
        </div>

        <div class="footer-column">
          <h3 class="footer-column-title">Follow Us</h3>
          <div class="footer-social-links">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <p class="footer-copyright">
          &copy; {{ new Date().getFullYear() }} CV Maker. All rights reserved.
        </p>
        <p class="footer-tagline">Designed to help you succeed.</p>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: "Footer",
};
</script>

<style scoped>
/*
  This scoped style block ensures these styles ONLY apply to this component.
  This is a surgical fix for the scroll-reveal animation issue without
  touching the main, fragile app.css file.
*/
.reveal-on-scroll.is-visible {
  opacity: 1;
  transform: translateY(0);
}

.site-footer {
    background: var(--footer-bg);
    color: var(--footer-text);
    padding: var(--space-xl) var(--space-lg);
    border-top: 1px solid var(--divider-color);
    margin-top: auto; /* Push footer to the bottom */
    width: 100%;
    flex-shrink: 0; /* Prevent footer from shrinking */
    transition: background var(--theme-transition-duration) ease, color var(--theme-transition-duration) ease, border-color var(--theme-transition-duration) ease;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
}

.footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: var(--space-xl);
    text-align: left;
    margin-bottom: var(--space-xl);
}

.footer-column-title {
    font-size: 1.1em;
    font-weight: 600;
    margin-bottom: var(--space-md);
    color: var(--neutral-text);
}

body.dark-theme .footer-column-title {
    color: var(--dark-neutral-text);
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
    color: var(--footer-link-color);
    text-decoration: none;
    transition: color 0.2s;
}

.footer-links-list a:hover {
    color: var(--footer-link-hover-color);
}

.footer-about-text, .footer-subtitle {
    font-size: 0.9em;
    color: var(--muted-text);
    line-height: 1.6;
}

.footer-logo-link-style {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    text-decoration: none;
    margin-bottom: var(--space-md);
}

.footer-gemini-icon {
    width: 40px;
    height: 40px;
    background-color: var(--primary);
    color: white;
    font-weight: 800;
    font-size: 1.2em;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: var(--radius-sm);
    flex-shrink: 0;
}

.footer-logo-text h3 {
    font-size: 1.2em;
    font-weight: 800;
    line-height: 1;
    margin: 0;
    color: var(--neutral-text);
}

body.dark-theme .footer-logo-text h3 {
    color: var(--dark-neutral-text);
}

.footer-social-links {
    display: flex;
    gap: var(--space-md);
}

.footer-social-links a {
    color: var(--muted-text);
    font-size: 1.2em;
    transition: color 0.2s ease;
}

.footer-social-links a:hover {
    color: var(--primary);
}

body.dark-theme .footer-social-links a:hover {
    color: var(--dark-primary);
}

.footer-bottom {
    margin-top: var(--space-xl);
    padding-top: var(--space-xl);
    border-top: 1px solid var(--divider-color);
    text-align: center;
    font-size: 0.85em;
    color: var(--muted-text);
}

body.dark-theme .footer-bottom {
    border-top-color: var(--dark-divider-color);
    color: var(--dark-muted-text);
}
</style>

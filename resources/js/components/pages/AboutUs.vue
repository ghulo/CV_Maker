<template>
  <div class="page-wrapper">
    <!-- Hero Section -->
    <section class="section-spacing">
      <div class="container">
        <div class="text-center">
          <h1 class="section-title">Building Your Future, One CV at a Time</h1>
          <p class="section-description">
            CV Maker is built with passion to empower individuals in their professional journey. 
            We believe a great CV can open doors and create opportunities.
          </p>
        </div>
      </div>
    </section>

    <!-- Mission Section -->
    <section class="section-spacing">
      <div class="container">
        <div class="mission-section">
          <h2 class="mission-title">Our Mission & Vision</h2>
          <div class="mission-content">
            <p>
              Our mission is to provide an intuitive and powerful platform that simplifies 
              the process of creating professional CVs. We aim to make CV design accessible 
              to everyone, helping you present your skills and experience in the best possible way.
            </p>
            <p>
              Our vision is to be the top choice for individuals looking to make a strong 
              impression in the job market, offering innovative tools and ongoing support.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Story Section -->
    <section class="section-spacing">
      <div class="container">
        <div class="text-center">
          <h2 class="section-title">Our Story</h2>
          <p class="section-description">
            CV Maker started as a small project with a big idea: to simplify CV creation. 
            We've evolved based on user feedback and industry trends, always keeping you at the center.
          </p>
        </div>
        
        <div class="story-grid">
          <div class="story-item">
            <div class="story-icon">
              <i class="fas fa-lightbulb"></i>
            </div>
            <h3>The Idea</h3>
            <p>
              It all started with a simple idea to help people create CVs without hassle.
            </p>
          </div>
          
          <div class="story-item">
            <div class="story-icon">
              <i class="fas fa-code"></i>
            </div>
            <h3>Development</h3>
            <p>
              We worked diligently to build a powerful and easy-to-use platform.
            </p>
          </div>
          
          <div class="story-item">
            <div class="story-icon">
              <i class="fas fa-users"></i>
            </div>
            <h3>Community</h3>
            <p>
              With our users' support, we continue to grow and improve.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="section-spacing">
      <div class="container">
        <div class="cta-section">
          <h2 class="cta-title">Join Our Community!</h2>
          <p class="cta-description">
            We're here to support you at every step of your professional journey.
          </p>
          <router-link to="/contact" class="btn-primary">
            <i class="fas fa-envelope"></i>
            Contact Us
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'AboutUs',
}
</script>

<style scoped>
/* CSS Variables */
:root {
  --primary: #5B21B6;
  --primary-light: #7C3AED;
  --secondary: #0EA5E9;
  --success: #10B981;
  --bg: #FFFFFF;
  --bg-subtle: #FAFAFA;
  --text: #111827;
  --text-muted: #6B7280;
  --border: #E5E7EB;
  --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
  --radius: 8px;
  --space-xs: 4px;
  --space-sm: 8px;
  --space-md: 16px;
  --space-lg: 24px;
  --space-xl: 32px;
  --space-xxl: 48px;
}

/* Base Styles */
.page-wrapper {
  min-height: 100vh;
  background: var(--bg);
  color: var(--text);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 var(--space-lg);
}

.section-spacing {
  padding: var(--space-xxl) 0;
}

.text-center {
  text-align: center;
}

/* Typography */
.section-title {
  font-size: 48px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: var(--space-md);
  line-height: 1.2;
}

.section-description {
  font-size: 20px;
  color: var(--text-muted);
  line-height: 1.6;
  max-width: 600px;
  margin: 0 auto;
}

/* Mission Section */
.mission-section {
  background: var(--bg-subtle);
  border-radius: var(--radius);
  padding: var(--space-xxl);
  max-width: 800px;
  margin: 0 auto;
}

.mission-title {
  font-size: 32px;
  font-weight: 600;
  color: var(--text);
  margin-bottom: var(--space-lg);
  text-align: center;
}

.mission-content {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
}

.mission-content p {
  font-size: 18px;
  color: var(--text-muted);
  line-height: 1.6;
  margin: 0;
}

/* Story Section */
.story-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: var(--space-xl);
  margin-top: var(--space-xxl);
}

.story-item {
  text-align: center;
  padding: var(--space-xl);
}

.story-icon {
  width: 80px;
  height: 80px;
  background: var(--primary);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto var(--space-lg);
  font-size: 24px;
}

.story-item h3 {
  font-size: 24px;
  font-weight: 600;
  color: var(--text);
  margin-bottom: var(--space-md);
}

.story-item p {
  font-size: 16px;
  color: var(--text-muted);
  line-height: 1.6;
  margin: 0;
}

/* CTA Section */
.cta-section {
  background: var(--bg-subtle);
  border-radius: var(--radius);
  padding: var(--space-xxl);
  text-align: center;
  max-width: 600px;
  margin: 0 auto;
}

.cta-title {
  font-size: 32px;
  font-weight: 600;
  color: var(--text);
  margin-bottom: var(--space-md);
}

.cta-description {
  font-size: 18px;
  color: var(--text-muted);
  margin-bottom: var(--space-xl);
}

/* Buttons */
.btn-primary {
  background: var(--primary);
  color: white;
  padding: var(--space-md) var(--space-lg);
  border-radius: var(--radius);
  text-decoration: none;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: var(--space-sm);
  transition: background-color 0.2s ease;
}

.btn-primary:hover {
  background: var(--primary-light);
}

/* Responsive */
@media (max-width: 768px) {
  .section-title {
    font-size: 32px;
  }

  .section-description {
    font-size: 16px;
  }

  .mission-section {
    padding: var(--space-xl);
  }

  .mission-title {
    font-size: 28px;
  }

  .mission-content p {
    font-size: 16px;
  }

  .story-grid {
    grid-template-columns: 1fr;
    gap: var(--space-lg);
  }

  .story-item {
    padding: var(--space-lg);
  }

  .story-icon {
    width: 60px;
    height: 60px;
    font-size: 20px;
  }

  .story-item h3 {
    font-size: 20px;
  }

  .cta-section {
    padding: var(--space-xl);
  }

  .cta-title {
    font-size: 28px;
  }

  .cta-description {
    font-size: 16px;
  }
}
</style>

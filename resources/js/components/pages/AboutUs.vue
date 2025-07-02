<template>
  <div class="page-wrapper about-page">
    <section class="hero-section section-spacing">
      <div class="container">
        <div class="text-center">
          <h1 class="section-title">{{ t('aboutTitle') }}</h1>
          <p class="section-description">
            {{ t('ourMissionDescription') }}
          </p>
        </div>
      </div>
    </section>

    <section class="mission-section section-spacing">
      <div class="container">
        <div class="mission-card card">
          <h2 class="mission-title">{{ t('ourMission') }}</h2>
          <div class="mission-content">
            <p class="mission-text">
              {{ t('ourMissionDescription') }}
            </p>
            <p class="mission-text">
              {{ t('ourValues') }}
            </p>
          </div>
        </div>
      </div>
    </section>

    <section class="values-section section-spacing">
      <div class="container">
        <div class="text-center mb-xl">
          <h2 class="section-title">{{ t('ourValues') }}</h2>
          <p class="section-description">
            {{ t('qualityDescription') }}
          </p>
        </div>
        
        <div class="values-grid">
          <div class="value-card card">
            <div class="value-icon">
              <i class="fas fa-lightbulb"></i>
            </div>
            <h3 class="value-title">{{ t('innovation') }}</h3>
            <p class="value-description">
              {{ t('innovationDescription') }}
            </p>
          </div>
          
          <div class="value-card card">
            <div class="value-icon">
              <i class="fas fa-code"></i>
            </div>
            <h3 class="value-title">{{ t('quality') }}</h3>
            <p class="value-description">
              {{ t('qualityDescription') }}
            </p>
          </div>
          
          <div class="value-card card">
            <div class="value-icon">
              <i class="fas fa-users"></i>
            </div>
            <h3 class="value-title">{{ t('support') }}</h3>
            <p class="value-description">
              {{ t('supportDescription') }}
            </p>
          </div>
        </div>
      </div>
    </section>

    <section class="cta-section section-spacing">
      <div class="container">
        <div class="cta-card card text-center">
          <h2 class="cta-title">{{ t('join_thousands_professionals') }}</h2>
          <p class="cta-description">
            {{ t('supportDescription') }}
          </p>
          <router-link to="/contact" class="btn btn-primary">
            <i class="fas fa-envelope"></i>
            {{ t('contact') }}
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<style scoped>
.about-page {
  padding-top: var(--header-height);
  background-color: var(--bg-secondary);
  min-height: 100vh;
}

.section-spacing {
  padding: var(--space-xxl) 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 var(--space-lg);
}

.text-center {
  text-align: center;
}

.mb-xl {
  margin-bottom: var(--space-xl);
}

/* Typography */
.section-title {
  font-family: var(--font-heading);
  font-size: var(--font-size-4xl);
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: var(--space-md);
  line-height: var(--line-height-tight);
  letter-spacing: var(--letter-spacing-tight);
}

.section-description {
  font-size: var(--font-size-lg);
  color: var(--text-secondary);
  line-height: var(--line-height-normal);
  max-width: 600px;
  margin: 0 auto;
}

/* Mission Section */
.mission-card {
  max-width: 800px;
  margin: 0 auto;
  padding: var(--space-xxl);
}

.mission-title {
  font-family: var(--font-heading);
  font-size: var(--font-size-3xl);
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: var(--space-lg);
  text-align: center;
}

.mission-content {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
}

.mission-text {
  font-size: var(--font-size-lg);
  color: var(--text-secondary);
  line-height: var(--line-height-relaxed);
  margin: 0;
}

/* Values Section */
.values-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: var(--space-xl);
  margin-top: var(--space-xxl);
}

.value-card {
  text-align: center;
  padding: var(--space-xl);
  transition: var(--transition-all);
}

.value-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}

.value-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  color: white;
  border-radius: var(--radius-full);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto var(--space-lg);
  font-size: var(--font-size-2xl);
  box-shadow: var(--shadow-md);
}

.value-title {
  font-family: var(--font-heading);
  font-size: var(--font-size-2xl);
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: var(--space-md);
}

.value-description {
  font-size: var(--font-size-base);
  color: var(--text-secondary);
  line-height: var(--line-height-relaxed);
  margin: 0;
}

/* CTA Section */
.cta-card {
  max-width: 600px;
  margin: 0 auto;
  padding: var(--space-xxl);
}

.cta-title {
  font-family: var(--font-heading);
  font-size: var(--font-size-3xl);
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: var(--space-md);
}

.cta-description {
  font-size: var(--font-size-lg);
  color: var(--text-secondary);
  margin-bottom: var(--space-xl);
  line-height: var(--line-height-relaxed);
}

/* Responsive Design */
@media (max-width: 768px) {
  .about-page {
    padding-top: var(--header-height-compact);
  }

  .section-spacing {
    padding: var(--space-xl) 0;
  }

  .container {
    padding: 0 var(--space-md);
  }

  .section-title {
    font-size: var(--font-size-3xl);
  }

  .section-description {
    font-size: var(--font-size-base);
  }

  .mission-card,
  .cta-card {
    padding: var(--space-xl);
  }

  .mission-title,
  .cta-title {
    font-size: var(--font-size-2xl);
  }

  .mission-text,
  .cta-description {
    font-size: var(--font-size-base);
  }

  .values-grid {
    grid-template-columns: 1fr;
    gap: var(--space-lg);
  }

  .value-card {
    padding: var(--space-lg);
  }

  .value-icon {
    width: 60px;
    height: 60px;
    font-size: var(--font-size-xl);
  }

  .value-title {
    font-size: var(--font-size-xl);
  }
}

@media (max-width: 480px) {
  .section-title {
    font-size: var(--font-size-2xl);
  }

  .mission-title,
  .cta-title {
    font-size: var(--font-size-xl);
  }

  .value-icon {
    width: 50px;
    height: 50px;
    font-size: var(--font-size-lg);
  }
}
</style>

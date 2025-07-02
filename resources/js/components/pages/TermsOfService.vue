<template>
  <div class="page-container content-page">
    <section class="section-spacing text-center">
      <div class="container">
        <h1 class="section-title">Terms of Service</h1>
        <p class="section-subtitle">
          These terms govern your use of our CV creation service.
        </p>
      </div>
    </section>

    <section class="container mb-xxl">
      <div class="card p-xl">
        <h2 class="card-title mb-md">1. Acceptance of Terms</h2>
        <p>By accessing or using CV Atelier, you agree to be bound by these Terms of Service. If you disagree with any part of these terms, then you may not access the Service.</p>

        <h2 class="card-title mb-md">2. Description of Service</h2>
        <p>CV Atelier is a web-based platform that allows users to create, edit, and download professional CVs and resumes. We provide templates, design tools, and features to help you build compelling professional documents.</p>

        <h2 class="card-title mb-md">3. User Accounts</h2>
        <p>When you create an account with us, you must provide information that is accurate, complete, and current at all times. You are responsible for safeguarding the password and for all activities that occur under your account.</p>

        <h2 class="card-title mb-md">4. User Content</h2>
        <p>Our Service allows you to post, link, store, share and otherwise make available certain information, text, graphics, or other material ("Content"). You are responsible for the Content that you post to the Service, including its legality, reliability, and appropriateness.</p>
        <p>By posting Content to the Service, you grant us the right and license to use, modify, publicly perform, publicly display, reproduce, and distribute such Content on and through the Service.</p>

        <h2 class="card-title mb-md">5. Prohibited Uses</h2>
        <p>You may not use our Service:</p>
        <ul class="list-disc list-inside mb-lg">
          <li>For any unlawful purpose or to solicit others to perform unlawful acts</li>
          <li>To violate any international, federal, provincial, or state regulations, rules, laws, or local ordinances</li>
          <li>To infringe upon or violate our intellectual property rights or the intellectual property rights of others</li>
          <li>To harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate</li>
          <li>To submit false or misleading information</li>
        </ul>

        <h2 class="card-title mb-md">6. Intellectual Property Rights</h2>
        <p>The Service and its original content, features, and functionality are and will remain the exclusive property of CV Atelier and its licensors. The Service is protected by copyright, trademark, and other laws.</p>

        <h2 class="card-title mb-md">7. Privacy Policy</h2>
        <p>Your privacy is important to us. Please review our Privacy Policy, which also governs your use of the Service, to understand our practices.</p>

        <h2 class="card-title mb-md">8. Termination</h2>
        <p>We may terminate or suspend your account immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Terms.</p>

        <h2 class="card-title mb-md">9. Limitation of Liability</h2>
        <p>In no event shall CV Atelier, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses.</p>

        <h2 class="card-title mb-md">10. Disclaimer</h2>
        <p>The information on this Service is provided on an "as is" basis. To the fullest extent permitted by law, CV Atelier excludes all representations, warranties, conditions, and terms.</p>

        <h2 class="card-title mb-md">11. Governing Law</h2>
        <p>These Terms shall be interpreted and governed by the laws of the jurisdiction in which CV Atelier operates, without regard to its conflict of law provisions.</p>

        <h2 class="card-title mb-md">12. Changes to Terms</h2>
        <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material, we will try to provide at least 30 days notice prior to any new terms taking effect.</p>

        <p class="text-sm text-tertiary mt-lg">Last updated: December 2024</p>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'TermsOfService',
}
</script>

<style scoped>
.content-page {
  padding-top: var(--header-height-compact);
  padding-bottom: var(--space-section-vertical);
  background-color: var(--bg-secondary);
}

.card {
  max-width: 900px;
  margin: var(--space-xl) auto;
  padding: var(--space-xl);
}

.section-title {
  font-family: var(--font-heading);
  font-size: var(--font-size-4xl);
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: var(--space-md);
  line-height: var(--line-height-tight);
  letter-spacing: var(--letter-spacing-tight);
}

.section-subtitle {
  font-size: var(--font-size-lg);
  color: var(--text-secondary);
  line-height: var(--line-height-normal);
  max-width: 700px;
  margin: 0 auto var(--space-xl);
}

.card-title {
  font-size: var(--font-size-xl);
  font-weight: 600;
  color: var(--primary);
  margin-bottom: var(--space-md);
}

.card-body p {
  margin-bottom: var(--space-md);
  color: var(--text-secondary);
  line-height: var(--line-height-relaxed);
}

.card-body ul {
  margin-bottom: var(--space-lg);
  color: var(--text-secondary);
  line-height: var(--line-height-normal);
}

.card-body li {
  margin-bottom: var(--space-xs);
}

.list-disc {
  list-style-type: disc;
}

.list-inside {
  list-style-position: inside;
}

.text-sm {
  font-size: var(--font-size-sm);
}

.text-tertiary {
  color: var(--text-tertiary);
}

@media (max-width: 768px) {
  .section-title {
    font-size: var(--font-size-3xl);
  }
  .section-subtitle {
    font-size: var(--font-size-base);
  }
  .card {
    padding: var(--space-lg);
  }
  .card-title {
    font-size: var(--font-size-lg);
  }
  .card-body p, .card-body ul, .card-body li {
    font-size: var(--font-size-sm);
  }
}

/* Dark Theme Overrides */
body.dark-theme .content-page {
  background-color: var(--bg-base);
  color: var(--text-primary);
}

body.dark-theme .card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  color: var(--text-primary);
}

body.dark-theme .section-title {
  color: var(--text-primary);
}

body.dark-theme .section-subtitle {
  color: var(--text-secondary);
}

body.dark-theme .card-title {
  color: var(--primary-light);
}

body.dark-theme .card-body p {
  color: var(--text-secondary);
}

body.dark-theme .card-body ul {
  color: var(--text-secondary);
}

body.dark-theme .card-body li {
  color: var(--text-secondary);
}

body.dark-theme .text-tertiary {
  color: var(--text-muted);
}
</style> 
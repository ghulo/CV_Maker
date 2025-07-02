<template>
  <div class="page-container content-page">
    <section class="section-spacing text-center">
      <div class="container">
        <h1 class="section-title">Privacy Policy</h1>
        <p class="section-subtitle">
          Learn how we collect, use, and protect your personal information.
        </p>
      </div>
    </section>

    <section class="container mb-xxl">
      <div class="card p-xl">
        <h2 class="card-title mb-md">1. Information We Collect</h2>
        <p>We collect several types of information to provide and improve our CV creation service:</p>
        <ul class="list-disc list-inside mb-lg">
          <li><strong>Personal Information:</strong> Name, email address, phone number, and address when you provide them.</li>
          <li><strong>CV Content:</strong> Work experience, education, skills, interests, and other information you enter into your CV.</li>
          <li><strong>Usage Data:</strong> Information about how you access and use our service, including your IP address, browser type, pages visited, and time spent on our service.</li>
          <li><strong>Device Information:</strong> Information about the device you use to access our service.</li>
        </ul>

        <h2 class="card-title mb-md">2. How We Use Your Information</h2>
        <p>We use the collected information for various purposes:</p>
        <ul class="list-disc list-inside mb-lg">
          <li>To provide and maintain our CV creation service</li>
          <li>To manage your user account and provide customer support</li>
          <li>To process your requests and transactions</li>
          <li>To improve our website and develop new features</li>
          <li>To communicate with you about updates, promotions, and support</li>
          <li>To ensure the security and integrity of our service</li>
          <li>To comply with legal obligations</li>
        </ul>

        <h2 class="card-title mb-md">3. Information Sharing and Disclosure</h2>
        <p>We do not sell, trade, or otherwise transfer your personal information to third parties except in the following circumstances:</p>
        <ul class="list-disc list-inside mb-lg">
          <li><strong>Service Providers:</strong> We may share information with trusted third-party service providers who assist us in operating our service</li>
          <li><strong>Legal Requirements:</strong> We may disclose information if required to do so by law or in response to valid requests by public authorities</li>
          <li><strong>Business Transfers:</strong> In the event of a merger, acquisition, or sale of assets, your information may be transferred</li>
          <li><strong>Protection of Rights:</strong> We may disclose information to protect our rights, property, or safety, or that of our users or others</li>
        </ul>

        <h2 class="card-title mb-md">4. Data Security</h2>
        <p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the internet or electronic storage is 100% secure.</p>

        <h2 class="card-title mb-md">5. Data Retention</h2>
        <p>We retain your personal information only for as long as necessary to fulfill the purposes outlined in this Privacy Policy, unless a longer retention period is required or permitted by law. When you delete your account, we will delete your personal information, except where we are required to retain it for legal purposes.</p>

        <h2 class="card-title mb-md">6. Your Rights</h2>
        <p>Depending on your location, you may have certain rights regarding your personal information:</p>
        <ul class="list-disc list-inside mb-lg">
          <li>Right to access your personal information</li>
          <li>Right to correct inaccurate or incomplete information</li>
          <li>Right to delete your personal information</li>
          <li>Right to restrict or object to processing</li>
          <li>Right to data portability</li>
          <li>Right to withdraw consent</li>
        </ul>

        <h2 class="card-title mb-md">7. Cookies and Tracking Technologies</h2>
        <p>We use cookies and similar tracking technologies to enhance your experience on our service. Cookies help us remember your preferences, analyze website traffic, and improve our service functionality.</p>

        <h2 class="card-title mb-md">8. Children's Privacy</h2>
        <p>Our service is not intended for individuals under the age of 18. We do not knowingly collect personal information from children under 18. If you are a parent or guardian and believe your child has provided us with personal information, please contact us.</p>

        <h2 class="card-title mb-md">9. International Data Transfers</h2>
        <p>Your information may be transferred to and processed in countries other than your own. We ensure that such transfers are made in accordance with applicable data protection laws and with appropriate safeguards in place.</p>

        <h2 class="card-title mb-md">10. Changes to This Privacy Policy</h2>
        <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date. We encourage you to review this Privacy Policy periodically.</p>

        <h2 class="card-title mb-md">11. Contact Us</h2>
        <p>If you have any questions about this Privacy Policy or our privacy practices, please contact us through our contact page or email us directly.</p>

        <p class="text-sm text-tertiary mt-lg">Last updated: December 2024</p>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'PrivacyPolicy',
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
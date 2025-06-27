<template>
  <div class="page-wrapper">
    <!-- Hero Section -->
    <section class="section-spacing">
      <div class="container">
        <div class="text-center">
          <h1 class="section-title">Frequently Asked Questions</h1>
          <p class="section-description">
            Find answers to common questions about creating CVs, managing your account, and using our platform.
          </p>
        </div>
      </div>
    </section>

    <!-- FAQ Content -->
    <section class="section-spacing">
      <div class="container">
        <!-- General Questions -->
        <div class="faq-section">
          <h2 class="faq-section-title">
            <i class="fas fa-question-circle"></i>
            General Questions
          </h2>
          <div class="faq-list">
            <div
              class="faq-item"
              v-for="(item, index) in generalQuestions"
              :key="index"
            >
              <button class="faq-question" @click="toggleAccordion(item)">
                <span>{{ item.question }}</span>
                <i class="fas" :class="item.open ? 'fa-minus' : 'fa-plus'"></i>
              </button>
              <div class="faq-answer" :class="{ open: item.open }">
                <div class="faq-answer-content" v-html="item.answer"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- CV Creation Questions -->
        <div class="faq-section">
          <h2 class="faq-section-title">
            <i class="fas fa-file-alt"></i>
            CV Creation & Templates
          </h2>
          <div class="faq-list">
            <div
              class="faq-item"
              v-for="(item, index) in creationQuestions"
              :key="index"
            >
              <button class="faq-question" @click="toggleAccordion(item)">
                <span>{{ item.question }}</span>
                <i class="fas" :class="item.open ? 'fa-minus' : 'fa-plus'"></i>
              </button>
              <div class="faq-answer" :class="{ open: item.open }">
                <div class="faq-answer-content">{{ item.answer }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Account Questions -->
        <div class="faq-section">
          <h2 class="faq-section-title">
            <i class="fas fa-user-cog"></i>
            Account & Support
          </h2>
          <div class="faq-list">
            <div
              class="faq-item"
              v-for="(item, index) in accountQuestions"
              :key="index"
            >
              <button class="faq-question" @click="toggleAccordion(item)">
                <span>{{ item.question }}</span>
                <i class="fas" :class="item.open ? 'fa-minus' : 'fa-plus'"></i>
              </button>
              <div class="faq-answer" :class="{ open: item.open }">
                <div class="faq-answer-content" v-html="item.answer"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="section-spacing">
      <div class="container">
        <div class="cta-section">
          <h2 class="cta-title">Can't find what you're looking for?</h2>
          <p class="cta-description">
            Contact our support team - we're here to help!
          </p>
          <router-link to="/contact" class="btn-primary">
            <i class="fas fa-envelope"></i>
            Contact Support
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { ref } from 'vue'

export default {
  name: 'Faq',
  setup() {
    const generalQuestions = ref([
      {
        question: 'What is CV Maker?',
        answer:
          'CV Maker is an online platform designed to help you quickly and easily create professional, modern, and effective Curriculum Vitae (CV). We offer a range of templates and tools to make the process smooth.',
        open: false,
      },
      {
        question: 'Is CV Maker free to use?',
        answer:
          'CV Maker offers essential features for free, allowing you to build and download a basic CV. Some templates, advanced features, or download options may be part of a premium plan in the future. We aim to provide excellent value at all levels.',
        open: false,
      },
      {
        question: 'Do I need to create an account to use CV Maker?',
        answer:
          'Yes, creating an account is necessary to save your progress, access your CVs from different devices, manage multiple versions, and use all features of our platform.',
        open: false,
      },
    ])

    const creationQuestions = ref([
      {
        question: 'What types of templates do you offer?',
        answer:
          'We offer a diverse range of professionally designed templates, including modern, classic, creative, and minimalist styles. Our templates are ATS-friendly (Applicant Tracking System) to help your CV stand out.',
        open: false,
      },
      {
        question: 'Can I customize the templates?',
        answer:
          'Yes, you can customize various aspects of the templates, such as fonts, colors, and section order, to match your personal brand and the requirements of the job you\'re applying for.',
        open: false,
      },
      {
        question: 'In what formats can I download my CV?',
        answer:
          'You can download your completed CV primarily in PDF format, which is the industry standard for job applications. We may offer other formats in the future.',
        open: false,
      },
      {
        question: 'Can I edit my CV after saving it?',
        answer:
          'Absolutely! All your CVs are saved in your account. You can access them anytime to make changes, updates, or download new copies.',
        open: false,
      },
    ])

    const accountQuestions = ref([
      {
        question: 'How do I reset my password?',
        answer:
          'If you\'ve forgotten your password, you can click the "Forgot Password?" link on the <a href="/login" class="text-link">login page</a>. Follow the instructions sent to your registered email address to reset it.',
        open: false,
      },
      {
        question: 'Is my personal data secure?',
        answer:
          'We take the privacy and security of your data very seriously. Please review our <a href="/privacy-policy" class="text-link">Privacy Policy</a> for detailed information on how we collect, use, and protect your data.',
        open: false,
      },
      {
        question: 'Who can I contact if I need help?',
        answer:
          'If you have questions not covered in this FAQ or encounter any issues, please don\'t hesitate to contact us through our <a href="/contact" class="text-link">Contact Us</a> page. We\'re happy to help!',
        open: false,
      },
    ])

    const toggleAccordion = (item) => {
      item.open = !item.open
    }

    return { generalQuestions, creationQuestions, accountQuestions, toggleAccordion }
  },
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

/* FAQ Sections */
.faq-section {
  margin-bottom: var(--space-xxl);
}

.faq-section-title {
  font-size: 24px;
  font-weight: 600;
  color: var(--text);
  margin-bottom: var(--space-lg);
  display: flex;
  align-items: center;
  gap: var(--space-sm);
}

.faq-section-title i {
  color: var(--primary);
}

.faq-list {
  max-width: 800px;
  margin: 0 auto;
}

/* FAQ Items */
.faq-item {
  border: 1px solid var(--border);
  border-radius: var(--radius);
  margin-bottom: var(--space-sm);
  overflow: hidden;
  background: var(--bg);
  transition: all 0.2s ease;
}

.faq-item:hover {
  border-color: var(--primary);
  box-shadow: var(--shadow);
}

.faq-question {
  width: 100%;
  background: none;
  border: none;
  padding: var(--space-lg);
  text-align: left;
  font-size: 16px;
  font-weight: 500;
  color: var(--text);
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: background-color 0.2s ease;
}

.faq-question:hover {
  background: var(--bg-subtle);
}

.faq-question i {
  color: var(--primary);
  font-size: 14px;
  transition: transform 0.2s ease;
}

.faq-answer {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease, padding 0.3s ease;
}

.faq-answer.open {
  max-height: 300px;
  border-top: 1px solid var(--border);
}

.faq-answer-content {
  padding: var(--space-lg);
  color: var(--text-muted);
  line-height: 1.6;
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
  font-size: 28px;
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

/* Links */
.text-link {
  color: var(--primary);
  text-decoration: none;
}

.text-link:hover {
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
  .section-title {
    font-size: 32px;
  }

  .section-description {
    font-size: 16px;
  }

  .faq-section-title {
    font-size: 20px;
  }

  .faq-question {
    padding: var(--space-md);
    font-size: 14px;
  }

  .faq-answer-content {
    padding: var(--space-md);
    font-size: 14px;
  }

  .cta-section {
    padding: var(--space-xl);
  }

  .cta-title {
    font-size: 24px;
  }

  .cta-description {
    font-size: 16px;
  }
}
</style>

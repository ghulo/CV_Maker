<template>
  <div class="page-container faq-page animate-in">
    <header class="page-intro-text reveal-on-scroll">
      <h1>Frequently Asked Questions</h1>
      <p>
        Find answers to common questions about creating CVs, managing your account, and using our platform.
      </p>
    </header>

    <section class="faq-section reveal-on-scroll glassmorphic-card" data-reveal-delay="200">
      <h2 class="faq-section-title">
        <i class="fas fa-question-circle"></i> General Questions
      </h2>
      <div class="faq-grid">
        <div class="faq-item" v-for="(item, index) in generalQuestions" :key="index" @click="toggleAccordion(item)">
          <button class="faq-question">
            <span>{{ item.question }}</span>
            <i class="fas" :class="item.open ? 'fa-minus' : 'fa-plus'"></i>
          </button>
          <div class="faq-answer" :class="{ open: item.open }">
            <p v-html="item.answer"></p>
          </div>
        </div>
      </div>
    </section>

    <section class="faq-section reveal-on-scroll glassmorphic-card" data-reveal-delay="400">
      <h2 class="faq-section-title">
        <i class="fas fa-file-alt"></i> CV Creation & Templates
      </h2>
      <div class="faq-grid">
         <div class="faq-item" v-for="(item, index) in creationQuestions" :key="index" @click="toggleAccordion(item)">
          <button class="faq-question">
            <span>{{ item.question }}</span>
            <i class="fas" :class="item.open ? 'fa-minus' : 'fa-plus'"></i>
          </button>
          <div class="faq-answer" :class="{ open: item.open }">
            <p>{{ item.answer }}</p>
          </div>
        </div>
      </div>
    </section>
    
    <section class="faq-section reveal-on-scroll glassmorphic-card" data-reveal-delay="600">
      <h2 class="faq-section-title">
        <i class="fas fa-user-cog"></i> Account & Support
      </h2>
      <div class="faq-grid">
        <div class="faq-item" v-for="(item, index) in accountQuestions" :key="index" @click="toggleAccordion(item)">
          <button class="faq-question">
            <span>{{ item.question }}</span>
            <i class="fas" :class="item.open ? 'fa-minus' : 'fa-plus'"></i>
          </button>
          <div class="faq-answer" :class="{ open: item.open }">
            <p v-html="item.answer"></p>
          </div>
        </div>
      </div>
    </section>

    <section class="homepage-final-cta reveal-on-scroll" data-reveal-delay="800">
      <div class="cta-content">
        <h4>Can't find the answer you're looking for?</h4>
        <router-link to="/contact" class="btn-create btn-large btn btn-primary">
          <i class="fas fa-envelope"></i> Contact Support
        </router-link>
      </div>
    </section>
  </div>
</template>

<script>
import { ref } from 'vue';

export default {
  name: 'Faq',
  setup() {
    const generalQuestions = ref([
      { question: 'What is CV Maker?', answer: 'CV Maker is an online platform designed to help you create professional, modern, and effective Curriculum Vitae (CVs) or resumes quickly and easily. We provide a range of templates and tools to make the process seamless.', open: false },
      { question: 'Is CV Maker free to use?', answer: 'CV Maker offers core features for free, allowing you to build and download a basic CV. Some advanced templates, features, or download options might be part of a premium plan in the future. We aim to provide excellent value at all levels.', open: false },
      { question: 'Do I need to create an account to use CV Maker?', answer: 'Yes, creating an account is necessary to save your progress, access your CVs from different devices, manage multiple versions, and utilize all the features of our platform.', open: false }
    ]);

    const creationQuestions = ref([
        { question: 'What kind of templates do you offer?', answer: 'We offer a diverse range of professionally designed templates, including modern, classic, creative, and minimalist styles. Our templates are ATS-friendly (Applicant Tracking System) to help your CV get noticed.', open: false },
        { question: 'Can I customize the templates?', answer: 'Yes, you can customize various aspects of the templates, such as fonts, colors, and section order, to match your personal brand and the requirements of the job you\'re applying for.', open: false },
        { question: 'In what formats can I download my CV?', answer: 'You can download your completed CV primarily in PDF format, which is the industry standard for job applications. We may offer other formats in the future.', open: false },
        { question: 'Can I edit my CV after saving it?', answer: 'Absolutely! All your CVs are saved to your account. You can log in anytime to make edits, updates, or download new copies.', open: false }
    ]);

    const accountQuestions = ref([
        { question: 'How do I reset my password?', answer: 'If you\'ve forgotten your password, you can click the "Forgot Password?" link on the <a href="/login" class="text-link">login page</a>. Follow the instructions sent to your registered email address to reset it.', open: false },
        { question: 'Is my personal data safe?', answer: 'We take your privacy and data security very seriously. Please review our <a href="/privacy-policy" class="text-link">Privacy Policy</a> for detailed information on how we collect, use, and protect your data.', open: false },
        { question: 'Who can I contact if I need help?', answer: 'If you have any questions not covered in this FAQ or encounter any issues, please don\'t hesitate to reach out to us via our <a href="/contact" class="text-link">Contact Us</a> page. We\'re happy to assist you!', open: false }
    ]);
    
    const toggleAccordion = (item) => {
      item.open = !item.open;
    };

    return { generalQuestions, creationQuestions, accountQuestions, toggleAccordion };
  }
}
</script>

<style scoped>
.faq-answer {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.5s ease-in-out, padding 0.5s ease-in-out, margin 0.5s ease-in-out, opacity 0.3s ease-in-out;
  padding: 0 1.5rem;
  margin-top: 0;
  opacity: 0;
  border-top: 1px solid transparent;
}
.faq-answer.open {
  max-height: 200px; /* Adjust as needed */
  opacity: 1;
  padding: 1.5rem;
  margin-top: -1px; /* Prevents double border */
  border-top-color: var(--divider-color);
}
body.dark-theme .faq-answer.open {
    border-top-color: var(--dark-divider-color);
}
</style>

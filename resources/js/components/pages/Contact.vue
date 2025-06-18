<template>
  <main class="main">
    <div class="contact-form-container login-form-container animate-in">
      <h2 class="reveal-on-scroll">Na Kontaktoni ose Dërgoni Feedback</h2>
      <p class="form-intro-text reveal-on-scroll" data-reveal-delay="100">
        Keni pyetje, sugjerime, apo dëshironi të raportoni një problem? Përdorni formularin më
        poshtë. Vlerësimi juaj na ndihmon të përmirësohemi!
      </p>

      <div id="contact-message-area" class="form-message" style="display: none"></div>

      <form id="contact-feedback-form" @submit.prevent="submitForm">
        <div class="form-group reveal-on-scroll" data-reveal-delay="150">
          <label for="contact-name">Emri Juaj (opsionale)</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-user input-icon"></i>
            <input
              type="text"
              id="contact-name"
              name="contact_name"
              placeholder="Emri juaj"
              v-model="form.name"
            />
          </div>
        </div>
        <div class="form-group reveal-on-scroll" data-reveal-delay="200">
          <label for="contact-email">Email Juaj (opsionale, për përgjigje)</label>
          <div class="input-icon-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input
              type="email"
              id="contact-email"
              name="contact_email"
              placeholder="email@shembull.com"
              v-model="form.email"
            />
          </div>
        </div>
        <div class="form-group reveal-on-scroll" data-reveal-delay="250">
          <label for="feedback-subject">Subjekti <span class="required">*</span></label>
          <div class="input-icon-wrapper">
            <i class="fas fa-tag input-icon"></i>
            <input
              type="text"
              id="feedback-subject"
              name="feedback_subject"
              required
              placeholder="P.sh., Sugjerim për model të ri, Problem teknik"
              v-model="form.subject"
            />
          </div>
        </div>
        <div class="form-group full-width reveal-on-scroll" data-reveal-delay="300">
          <label for="feedback-message"
            >Mesazhi / Feedback-u Juaj <span class="required">*</span></label
          >
          <textarea
            id="feedback-message"
            name="feedback_message"
            rows="6"
            required
            placeholder="Shkruani detajet këtu..."
            v-model="form.message"
          ></textarea>
        </div>
        <button
          type="submit"
          class="btn-primary-form btn btn-primary reveal-on-scroll"
          data-reveal-delay="350"
        >
          Dërgo Mesazhin <i class="fas fa-paper-plane icon-right"></i>
        </button>
      </form>
      <p
        class="form-footer-note reveal-on-scroll"
        data-reveal-delay="400"
      >
        Ju lutemi vini re: Ky formular është për demonstrim. Përpunimi aktual i feedback-ut kërkon
        implementim në server.
      </p>
    </div>
  </main>
</template>

<script>
  import { reactive } from 'vue'

  export default {
    name: 'Contact',
    setup() {
      const form = reactive({
        name: '',
        email: '',
        subject: '',
        message: '',
      })

      const submitForm = () => {
        // This is a placeholder for form submission.
        // In a real app, this would send data to an API endpoint.
        const messageArea = document.getElementById('contact-message-area')
        messageArea.style.display = 'block'
        messageArea.className = 'form-message message success'
        messageArea.textContent = 'Faleminderit për mesazhin tuaj! (Ky është një demonstrim)'

        // Reset form
        form.name = ''
        form.email = ''
        form.subject = ''
        form.message = ''
      }

      return {
        form,
        submitForm,
      }
    },
  }
</script>

<style scoped>
  @reference "tailwindcss/theme";

  .login-form-container {
    /* Reusing login form container style */
    max-width: 800px;
  }

  .form-footer-note {
      font-size: 0.8em;
      text-align: center;
      margin-top: var(--space-md);
      color: var(--muted-text);
  }
</style>

<template>
  <div class="page-wrapper contact-wrapper">
    <section class="section-spacing">
      <div class="form-container reveal-on-scroll reveal-scale">
        <div class="text-center mb-xl">
          <h1 class="section-title">Na Kontaktoni ose Dërgoni Feedback</h1>
          <p class="section-description">
            Keni pyetje, sugjerime, apo dëshironi të raportoni një problem? Përdorni formularin më
            poshtë. Vlerësimi juaj na ndihmon të përmirësohemi!
          </p>
        </div>

        <!-- Success/Error Message Area -->
        <div v-if="message.text" :class="['message', message.type]">
          <i :class="['icon', message.type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle']"></i>
          {{ message.text }}
        </div>

        <form @submit.prevent="submitForm" class="contact-form">
          <div class="form-grid">
            <div class="form-group">
              <label for="contact-name" class="form-label">Emri Juaj</label>
              <div class="input-icon-wrapper">
                <i class="fas fa-user input-icon"></i>
                <input
                  type="text"
                  id="contact-name"
                  name="contact_name"
                  placeholder="Emri juaj"
                  v-model="form.name"
                  class="form-input"
                />
              </div>
            </div>
            <div class="form-group">
              <label for="contact-email" class="form-label required">Email Juaj</label>
              <div class="input-icon-wrapper">
                <i class="fas fa-envelope input-icon"></i>
                <input
                  type="email"
                  id="contact-email"
                  name="contact_email"
                  required
                  placeholder="email@shembull.com"
                  v-model="form.email"
                  class="form-input"
                />
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="feedback-subject" class="form-label required">Subjekti</label>
            <div class="input-icon-wrapper">
              <i class="fas fa-tag input-icon"></i>
              <input
                type="text"
                id="feedback-subject"
                name="feedback_subject"
                required
                placeholder="P.sh., Sugjerim për model të ri, Problem teknik"
                v-model="form.subject"
                class="form-input"
              />
            </div>
          </div>
          <div class="form-group">
            <label for="feedback-message" class="form-label required">Mesazhi / Feedback-u Juaj</label>
            <textarea
              id="feedback-message"
              name="feedback_message"
              rows="6"
              required
              placeholder="Shkruani detajet këtu..."
              v-model="form.message"
              class="form-textarea"
            ></textarea>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-with-icon" :disabled="loading">
              <font-awesome-icon v-if="loading" icon="fa-solid fa-spinner" class="fa-spin icon" />
              <i v-else class="fas fa-paper-plane icon"></i>
              Dërgo Mesazhin
            </button>
          </div>
        </form>
      </div>
    </section>
  </div>
</template>

<script>
  import { reactive, ref, onMounted } from 'vue'
  import axios from 'axios'

  export default {
    name: 'Contact',
    setup() {
      const form = reactive({
        name: '',
        email: '',
        subject: '',
        message: '',
      })
      const loading = ref(false)
      const message = reactive({ text: '', type: '' })

      const submitForm = async () => {
        loading.value = true
        message.text = ''
        message.type = ''

        try {
          const response = await axios.post('/api/contact', form)
          if (response.data.success) {
            message.text = response.data.message || 'Mesazhi u dërgua me sukses!'
            message.type = 'success'
            // Reset form
            form.name = '';
            form.email = '';
            form.subject = '';
            form.message = '';
          } else {
            message.text = response.data.message || 'Gabim gjatë dërgimit të mesazhit.'
            message.type = 'error'
          }
        } catch (error) {
          console.error('Contact form submission failed:', error.response ? error.response.data : error.message)
          message.text = error.response?.data?.message || 'Ndodhi një gabim. Ju lutem provoni përsëri më vonë.'
          message.type = 'error'
        } finally {
          loading.value = false
          // Optionally, hide message after a few seconds
          setTimeout(() => {
            message.text = '';
          }, 5000);
        }
      }

      onMounted(() => {
        // reinitializeScrollReveal();
      });

      return {
        form,
        submitForm,
        loading,
        message,
      }
    },
  }
</script>

<style scoped>
  /* Page Wrapper and Section Spacing (re-using global utility classes) */
  .contact-wrapper {
    padding-top: var(--space-xxl);
    padding-bottom: var(--space-xxl);
  }

  .section-spacing {
    padding: var(--space-section-vertical) var(--space-lg);
  }

  .text-center {
    text-align: center;
  }

  .mb-xl {
    margin-bottom: var(--space-xl);
  }

  /* Form Container */
  .form-container {
    max-width: 800px;
    margin: var(--space-xxl) auto;
  }

  /* Section Title and Description (re-using global utility classes) */
  .section-title {
    font-family: var(--font-heading);
    font-size: var(--font-size-4xl);
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: var(--space-md);
    line-height: 1.2;
    letter-spacing: -0.02em;
  }

  .section-description {
    font-size: var(--font-size-lg);
    color: var(--text-secondary);
    line-height: var(--line-height-normal);
    max-width: 800px;
    margin: 0 auto var(--space-xl);
  }

  /* Form Specific Layout */
  .contact-form {
    display: flex;
    flex-direction: column;
    gap: var(--space-lg); /* Space between form groups */
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--space-lg);
  }

  /* Message Styling (Global message classes will apply) */
  .message {
    padding: var(--space-md);
    border-radius: var(--radius);
    margin-bottom: var(--space-lg);
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    font-size: var(--font-size-base);
  }

  .message.success {
    background-color: var(--success-light);
    color: var(--success-dark);
    border: 1px solid var(--success);
  }

  .message.error {
    background-color: var(--error-light);
    color: var(--error-dark);
    border: 1px solid var(--error);
  }

  .message .icon {
    font-size: var(--font-size-lg);
  }

  .form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: var(--space-lg);
  }

  /* Responsive Adjustments */
  @media (max-width: 768px) {
    .section-spacing {
      padding: var(--space-xl) var(--space-md);
    }

    .section-title {
      font-size: var(--font-size-3xl);
    }

    .section-description {
      font-size: var(--font-size-base);
      margin-bottom: var(--space-lg);
    }

    .form-container {
      margin: var(--space-lg) auto;
    }

    .form-grid {
      grid-template-columns: 1fr;
    }

    .form-actions {
      justify-content: center;
    }

    .form-actions .btn {
      width: 100%;
    }
  }

  /* Dark Theme Overrides */
  body.dark-theme .contact-wrapper {
    background: var(--bg-base);
    color: var(--text-primary);
  }

  body.dark-theme .section-title {
    color: var(--text-primary);
  }

  body.dark-theme .section-description {
    color: var(--text-secondary);
  }

  body.dark-theme .form-container {
    background: var(--bg-elevated);
  }

  body.dark-theme .contact-form {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: var(--space-xl);
  }

  body.dark-theme .form-label {
    color: var(--text-primary);
  }

  body.dark-theme .form-input,
  body.dark-theme .form-textarea {
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    color: var(--text-primary);
  }

  body.dark-theme .form-input:focus,
  body.dark-theme .form-textarea:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(91, 33, 182, 0.1);
  }

  body.dark-theme .form-input::placeholder,
  body.dark-theme .form-textarea::placeholder {
    color: var(--text-muted);
  }

  body.dark-theme .input-icon {
    color: var(--text-muted);
  }

  body.dark-theme .btn.btn-primary {
    background: var(--primary);
    color: var(--text-inverse);
    border-color: var(--primary);
  }

  body.dark-theme .btn.btn-primary:hover {
    background: var(--primary-light);
  }

  body.dark-theme .message.success {
    background: rgba(16, 185, 129, 0.1);
    border-color: var(--success);
    color: var(--success-light);
  }

  body.dark-theme .message.error {
    background: rgba(239, 68, 68, 0.1);
    border-color: var(--error);
    color: var(--error-light);
  }
</style>

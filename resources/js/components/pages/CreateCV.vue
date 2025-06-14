<template>
  <main class="main">
    <div class="p-4 sm:p-6 lg:p-8 bg-neutral-bg dark:bg-dark-neutral-bg min-h-screen">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-neutral-text dark:text-dark-neutral-text">Krijo CV të re</h1>
        <p class="text-muted-text dark:text-dark-muted-text mt-1">Plotësoni informacionin e mëposhtëm për të ndërtuar CV-në tuaj.</p>
      </div>

      <div class="bg-neutral-light dark:bg-dark-neutral-container p-6 rounded-lg shadow-light glassmorphic-card">
        <!-- Step Navigation (Progress Bar) -->
        <div class="mb-6 flex justify-between items-center text-sm font-medium text-muted-text dark:text-dark-muted-text">
          <div v-for="(stepName, index) in stepNames" :key="index"
               :class="['flex-1 text-center py-2 relative',
                        index === currentStep ? 'text-primary dark:text-primary-accent' : '',
                        index < currentStep ? 'text-success dark:text-success-darker' : '']">
            <span class="block">{{ stepName }}</span>
            <div class="absolute bottom-0 left-0 w-full h-1"
                 :class="[
                    index === currentStep ? 'bg-primary dark:bg-primary-accent' : '',
                    index < currentStep ? 'bg-success dark:bg-success-darker' : 'bg-neutral-border dark:bg-dark-neutral-border'
                 ]"></div>
          </div>
        </div>

        <form @submit.prevent="nextStep" class="space-y-6">
          <!-- Step 0: CV Title and Personal Information -->
          <div v-if="currentStep === 0">
            <div>
              <label for="cv-title" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Titulli i CV-së</label>
              <input type="text" id="cv-title" v-model="cv.title" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm" placeholder="P.sh. CV për Programer">
            </div>
            <div>
              <h3 class="text-lg font-semibold text-neutral-text dark:text-dark-neutral-text mt-6 mb-3">Informacione Personale</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="first-name" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Emri</label>
                  <input type="text" id="first-name" v-model="cv.personalInfo.firstName" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm">
                </div>
                <div>
                  <label for="last-name" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Mbiemri</label>
                  <input type="text" id="last-name" v-model="cv.personalInfo.lastName" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm">
                </div>
                <div>
                  <label for="email" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Email</label>
                  <input type="email" id="email" v-model="cv.personalInfo.email" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm">
                </div>
                <div>
                  <label for="phone" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Telefoni</label>
                  <input type="tel" id="phone" v-model="cv.personalInfo.phone" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm">
                </div>
                <div class="md:col-span-2">
                  <label for="address" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Adresa</label>
                  <input type="text" id="address" v-model="cv.personalInfo.address" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm">
                </div>
              </div>
            </div>
            <div class="form-group full-width mt-6">
              <label for="summary" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Përmbledhja Profesionale / Rreth Meje</label>
              <textarea id="summary" v-model="cv.summary" rows="5" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm" placeholder="Përshkruani shkurtimisht veten, aftësitë kryesore dhe objektivat e karrierës..."></textarea>
              <p class="form-field-description text-muted-text dark:text-dark-muted-text mt-1">
                Ky profil i shkurtër mund të përdoret si titull profesional në disa modele CV-je nëse është konciz, ose si një seksion i dedikuar "Rreth Meje".
              </p>
            </div>
          </div>

          <!-- Step 1: Experience -->
          <div v-if="currentStep === 1">
            <h3 class="text-lg font-semibold text-neutral-text dark:text-dark-neutral-text mb-3">Përvoja e Punës</h3>
            <div v-for="(exp, index) in cv.experience" :key="index" class="mb-4 p-4 border border-neutral-border dark:border-dark-neutral-border rounded-md space-y-3 glassmorphic-card">
              <div>
                <label :for="`job-title-${index}`" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Titulli i Pozicionit</label>
                <input type="text" :id="`job-title-${index}`" v-model="exp.title" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm">
              </div>
              <div>
                <label :for="`company-${index}`" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Kompania</label>
                <input type="text" :id="`company-${index}`" v-model="exp.company" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm">
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label :for="`start-date-${index}`" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Data e Fillimit</label>
                  <input type="date" :id="`start-date-${index}`" v-model="exp.startDate" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-sm">
                </div>
                <div>
                  <label :for="`end-date-${index}`" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Data e Mbarimit</label>
                  <input type="date" :id="`end-date-${index}`" v-model="exp.endDate" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-sm">
                </div>
              </div>
              <div>
                <label :for="`description-${index}`" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Përshkrimi i Detyrave</label>
                <textarea :id="`description-${index}`" v-model="exp.description" rows="3" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm"></textarea>
              </div>
              <div class="flex justify-end">
                <button type="button" @click="removeExperience(index)" class="btn-text-danger text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-600 text-sm">Fshij Përvojën</button>
              </div>
            </div>
            <button type="button" @click="addExperience" class="btn-secondary w-full py-2 mt-4 text-primary dark:text-primary-accent border border-primary dark:border-primary-accent hover:bg-primary-hover hover:text-white dark:hover:bg-primary-hover-dark dark:hover:text-white transition-colors duration-300">
              <i class="fas fa-plus mr-2"></i>Shto Përvojë
            </button>
          </div>

          <!-- Step 2: Education -->
          <div v-if="currentStep === 2">
            <h3 class="text-lg font-semibold text-neutral-text dark:text-dark-neutral-text mb-3">Edukimi</h3>
            <div v-for="(edu, index) in cv.education" :key="index" class="mb-4 p-4 border border-neutral-border dark:border-dark-neutral-border rounded-md space-y-3 glassmorphic-card">
              <div>
                <label :for="`degree-${index}`" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Titulli/Diploma</label>
                <input type="text" :id="`degree-${index}`" v-model="edu.degree" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm">
              </div>
              <div>
                <label :for="`university-${index}`" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Universiteti/Institucioni</label>
                <input type="text" :id="`university-${index}`" v-model="edu.university" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm">
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label :for="`education-start-date-${index}`" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Data e Fillimit</label>
                  <input type="date" :id="`education-start-date-${index}`" v-model="edu.startDate" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-sm">
                </div>
                <div>
                  <label :for="`education-end-date-${index}`" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Data e Mbarimit</label>
                  <input type="date" :id="`education-end-date-${index}`" v-model="edu.endDate" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-sm">
                </div>
              </div>
              <div class="flex justify-end">
                <button type="button" @click="removeEducation(index)" class="btn-text-danger text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-600 text-sm">Fshij Edukimin</button>
              </div>
            </div>
            <button type="button" @click="addEducation" class="btn-secondary w-full py-2 mt-4 text-primary dark:text-primary-accent border border-primary dark:border-primary-accent hover:bg-primary-hover hover:text-white dark:hover:bg-primary-hover-dark dark:hover:text-white transition-colors duration-300">
              <i class="fas fa-plus mr-2"></i>Shto Edukim
            </button>
          </div>

          <!-- Step 3: Skills -->
          <div v-if="currentStep === 3">
            <h3 class="text-lg font-semibold text-neutral-text dark:text-dark-neutral-text mb-3">Aftësitë</h3>
            <div v-for="(skill, index) in cv.skills" :key="index" class="mb-4 p-4 border border-neutral-border dark:border-dark-neutral-border rounded-md space-y-3 glassmorphic-card">
              <div>
                <label :for="`skill-name-${index}`" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Emri i Aftësisë</label>
                <input type="text" :id="`skill-name-${index}`" v-model="skill.name" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm">
              </div>
              <div>
                <label :for="`skill-level-${index}`" class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text">Niveli i Aftësisë</label>
                <select :id="`skill-level-${index}`" v-model="skill.level" class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-sm">
                  <option value="">Zgjidh Nivelin</option>
                  <option value="Beginner">Fillestar</option>
                  <option value="Intermediate">Mesatar</option>
                  <option value="Advanced">I Avancuar</option>
                  <option value="Expert">Ekspert</option>
                </select>
              </div>
              <div class="flex justify-end">
                <button type="button" @click="removeSkill(index)" class="btn-text-danger text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-600 text-sm">Fshij Aftësinë</button>
              </div>
            </div>
            <button type="button" @click="addSkill" class="btn-secondary w-full py-2 mt-4 text-primary dark:text-primary-accent border border-primary dark:border-primary-accent hover:bg-primary-hover hover:text-white dark:hover:bg-primary-hover-dark dark:hover:text-white transition-colors duration-300">
              <i class="fas fa-plus mr-2"></i>Shto Aftësi
            </button>
          </div>

          <!-- Navigation Buttons -->
          <div class="flex justify-between pt-6 border-t border-neutral-border dark:border-dark-neutral-border mt-8">
            <button type="button" @click="prevStep" v-if="currentStep > 0" class="btn-secondary flex items-center px-4 py-2 text-primary dark:text-primary-accent border border-primary dark:border-primary-accent hover:bg-primary-hover hover:text-white dark:hover:bg-primary-hover-dark dark:hover:text-white transition-colors duration-300">
              <i class="fas fa-arrow-left mr-2"></i>
              Hapi Paraprak
            </button>
            <button type="submit" class="btn-primary flex items-center px-4 py-2 ml-auto" v-if="currentStep < stepNames.length - 1">
              Hapi Tjetër
              <i class="fas fa-arrow-right ml-2"></i>
            </button>
            <button type="button" @click="saveCv" v-if="currentStep === stepNames.length - 1" class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-hover transition-colors duration-300">
              <i class="fas fa-save mr-2"></i>
              Ruaj CV-në
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

export default {
  name: 'CreateCV',
  setup() {
    const route = useRoute();
    const router = useRouter();
    const template_to_use_for_form = ref(route.query.template || 'classic');

    const currentStep = ref(0);
    const stepNames = [
      'Informacione Personale',
      'Përvoja e Punës',
      'Edukimi',
      'Aftësitë',
    ];

    const cv = reactive({
      title: '',
      personalInfo: {
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        address: '',
      },
      summary: '',
      experience: [],
      education: [],
      skills: [],
      selected_template: template_to_use_for_form.value,
    });

    // Methods for Experience
    const addExperience = () => {
      cv.experience.push({
        title: '',
        company: '',
        startDate: '',
        endDate: '',
        description: '',
      });
    };
    const removeExperience = (index) => {
      cv.experience.splice(index, 1);
    };

    // Methods for Education
    const addEducation = () => {
      cv.education.push({
        degree: '',
        university: '',
        startDate: '',
                endDate: '',
      });
    };
    const removeEducation = (index) => {
      cv.education.splice(index, 1);
    };

    // Methods for Skills
    const addSkill = () => {
      cv.skills.push({
        name: '',
        level: '',
      });
    };
    const removeSkill = (index) => {
      cv.skills.splice(index, 1);
    };

    const nextStep = async () => {
      // Perform validation for the current step before proceeding
      // For now, we'll just move to the next step

      if (currentStep.value < stepNames.length - 1) {
        currentStep.value++;
      } else {
        // This is the last step, so save the CV
        await saveCv();
      }
    };

    const prevStep = () => {
      if (currentStep.value > 0) {
        currentStep.value--;
      }
    };

    const saveCv = async () => {
      try {
        const dataToSave = {
          cv_title: cv.title,
          personal_details: {
            full_name: `${cv.personalInfo.firstName} ${cv.personalInfo.lastName}`.trim(),
            email: cv.personalInfo.email,
            phone_number: cv.personalInfo.phone,
            address: cv.personalInfo.address,
          },
          summary: cv.summary,
          experiences: cv.experience,
          educations: cv.education,
          skills: cv.skills,
          selected_template: cv.selected_template,
        };

        // The crucial fix: POST to /api/cvs (plural)
        const response = await axios.post('/api/cvs', dataToSave);

        if (response.data.success) {
          const cvId = response.data.cv.id;
          router.push({ name: 'cv.edit', params: { id: cvId }, query: { step: 'experience' } });
        } else {
          console.error("Server reported error:", response.data.message);
          // Handle server-side validation errors or other issues
        }
      } catch (error) {
        console.error("Error saving CV:", error.response?.data || error.message);
        // Display user-friendly error message
      }
    };

    onMounted(() => {
      // Reinitialize scroll reveal for the new multi-step content
      const reinitializeScrollReveal = () => {
        const revealElements = document.querySelectorAll('.reveal-on-scroll');
        const revealObserver = new IntersectionObserver((entries, observer) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              const delay = parseInt(entry.target.getAttribute('data-reveal-delay') || '0', 10);
              setTimeout(() => entry.target.classList.add('is-revealed'), delay);
              observer.unobserve(entry.target);
            }
          });
        }, { threshold: 0.1 });
        revealElements.forEach(el => revealObserver.observe(el));
      };
      reinitializeScrollReveal();
    });

    return {
      currentStep,
      stepNames,
      cv,
      addExperience,
      removeExperience,
      addEducation,
      removeEducation,
      addSkill,
      removeSkill,
      nextStep,
      prevStep,
      saveCv,
      template_to_use_for_form, // Still needed for display
    };
  },
};
</script>

<style scoped>
@reference "tailwindcss/theme";

.reveal-on-scroll {
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.8s cubic-bezier(0.645, 0.045, 0.355, 1), transform 0.8s cubic-bezier(0.645, 0.045, 0.355, 1);
}
.reveal-on-scroll.is-revealed {
  opacity: 1;
  transform: translateY(0);
}

.form-field-description {
  font-size: 0.85em;
  color: var(--muted-text);
  margin-top: 0.5rem;
}

.form-actions-sticky {
  position: sticky;
  bottom: 0;
  background-color: var(--neutral-light); /* Or your desired background */
  padding: 1rem;
  border-top: 1px solid var(--neutral-border);
  z-index: 100;
  margin-left: -1.5rem; /* Adjust to align with page container padding */
  margin-right: -1.5rem;
  width: calc(100% + 3rem); /* Compensate for negative margins */
  box-shadow: var(--shadow);
}

body.dark-theme .form-actions-sticky {
  background-color: var(--dark-neutral-container);
  border-top-color: var(--dark-neutral-border);
  box-shadow: var(--dark-shadow);
}
</style>



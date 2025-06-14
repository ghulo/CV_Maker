<template>
  <main class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg">
      <!-- Header -->
      <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-2">Krijo CV të re</h1>
        <p class="text-lg text-gray-600">Plotësoni informacionin e mëposhtëm për të ndërtuar CV-në tuaj.</p>
      </div>

      <div class="form-card">
        <!-- Step Navigation -->
        <div class="flex justify-between items-center mb-8 border-b-2 border-gray-200 pb-4">
          <button v-for="(stepName, index) in stepNames" :key="index"
                  @click="goToStep(index)"
                  :disabled="index > currentStep"
                  :class="['flex-1 text-center py-3 px-2 mx-1 rounded-md text-sm font-medium transition-all duration-300 ease-in-out',
                           { 'bg-indigo-600 text-white shadow-md': index === currentStep },
                           { 'bg-gray-200 text-gray-700 hover:bg-indigo-100 hover:text-indigo-700': index !== currentStep && index > currentStep },
                           { 'bg-green-500 text-white': index < currentStep }]">
            <span class="block mb-1">{{ index + 1 }}.</span> {{ stepName }}
          </button>
        </div>
        
        <!-- Overall Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-8">
          <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500 ease-out" :style="{ width: progressPercentage + '%' }"></div>
        </div>

        <form @submit.prevent="nextStep" class="space-y-8">
          <!-- Step 0: CV Title and Personal Information -->
          <div v-if="currentStep === 0" class="fade-in">
            <div class="mb-6">
              <label for="cv-title" class="block text-sm font-medium text-gray-700 mb-2">Titulli i CV-së</label>
              <input type="text" id="cv-title" v-model="cv.title" placeholder="P.sh. CV për Programer"
                     class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            
            <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Informacione Personale</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="form-group">
                <label for="first-name" class="block text-sm font-medium text-gray-700 mb-2">Emri <span class="text-red-500">*</span></label>
                <input type="text" id="first-name" v-model="cv.personalInfo.firstName" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              </div>
              <div class="form-group">
                <label for="last-name" class="block text-sm font-medium text-gray-700 mb-2">Mbiemri <span class="text-red-500">*</span></label>
                <input type="text" id="last-name" v-model="cv.personalInfo.lastName" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              </div>
              <div class="form-group">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                <input type="email" id="email" v-model="cv.personalInfo.email" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              </div>
              <div class="form-group">
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Telefoni</label>
                <input type="tel" id="phone" v-model="cv.personalInfo.phone"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              </div>
              <div class="form-group md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Adresa</label>
                <input type="text" id="address" v-model="cv.personalInfo.address"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              </div>
            </div>

            <div class="mt-8">
              <label for="summary" class="block text-sm font-medium text-gray-700 mb-2">Përmbledhja Profesionale / Rreth Meje <span class="text-red-500">*</span></label>
              <textarea id="summary" v-model="cv.summary" rows="5" placeholder="Përshkruani shkurtimisht veten, aftësitë kryesore dhe objektivat e karrierës..." required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm resize-y"></textarea>
              <p class="mt-2 text-sm text-gray-500">
                Ky profil i shkurtër mund të përdoret si titull profesional në disa modele CV-je nëse është konciz, ose si një seksion i dedikuar "Rreth Meje".
              </p>
            </div>
          </div>

          <!-- Step 1: Experience -->
          <div v-if="currentStep === 1" class="fade-in">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Përvoja e Punës</h3>
            <div v-for="(exp, index) in cv.experience" :key="index" class="relative bg-gray-50 p-6 rounded-lg shadow-sm mb-6 border border-gray-200">
               <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="form-group">
                    <label :for="`job-title-${index}`" class="block text-sm font-medium text-gray-700 mb-2">Titulli i Pozicionit <span class="text-red-500">*</span></label>
                    <input type="text" :id="`job-title-${index}`" v-model="exp.title" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  </div>
                  <div class="form-group">
                    <label :for="`company-${index}`" class="block text-sm font-medium text-gray-700 mb-2">Kompania <span class="text-red-500">*</span></label>
                    <input type="text" :id="`company-${index}`" v-model="exp.company" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  </div>
                  <div class="form-group">
                    <label :for="`start-date-${index}`" class="block text-sm font-medium text-gray-700 mb-2">Data e Fillimit <span class="text-red-500">*</span></label>
                    <input type="date" :id="`start-date-${index}`" v-model="exp.startDate" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  </div>
                  <div class="form-group">
                    <label :for="`end-date-${index}`" class="block text-sm font-medium text-gray-700 mb-2">Data e Mbarimit</label>
                    <input type="date" :id="`end-date-${index}`" v-model="exp.endDate"
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                     <p class="mt-2 text-sm text-gray-500">Lëreni bosh nëse jeni ende duke punuar këtu.</p>
                  </div>
                  <div class="form-group md:col-span-2">
                    <label :for="`description-${index}`" class="block text-sm font-medium text-gray-700 mb-2">Përshkrimi i Detyrave</label>
                    <textarea :id="`description-${index}`" v-model="exp.description" rows="3"
                              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm resize-y"></textarea>
                  </div>
                </div>
              <button type="button" @click="removeExperience(index)" title="Fshij Përvojën"
                      class="absolute top-3 right-3 text-red-500 hover:text-red-700 transition-colors duration-200">
                <i class="fas fa-times-circle text-xl"></i>
              </button>
            </div>
            <button type="button" @click="addExperience"
                    class="mt-4 w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
              <i class="fas fa-plus mr-2"></i> Shto Përvojë
            </button>
          </div>

          <!-- Step 2: Education -->
          <div v-if="currentStep === 2" class="fade-in">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Edukimi</h3>
            <div v-for="(edu, index) in cv.education" :key="index" class="relative bg-gray-50 p-6 rounded-lg shadow-sm mb-6 border border-gray-200">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="form-group">
                    <label :for="`degree-${index}`" class="block text-sm font-medium text-gray-700 mb-2">Titulli/Diploma <span class="text-red-500">*</span></label>
                    <input type="text" :id="`degree-${index}`" v-model="edu.degree" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  </div>
                  <div class="form-group">
                    <label :for="`university-${index}`" class="block text-sm font-medium text-gray-700 mb-2">Universiteti/Institucioni <span class="text-red-500">*</span></label>
                    <input type="text" :id="`university-${index}`" v-model="edu.university" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  </div>
                  <div class="form-group">
                    <label :for="`education-start-date-${index}`" class="block text-sm font-medium text-gray-700 mb-2">Data e Fillimit</label>
                    <input type="date" :id="`education-start-date-${index}`" v-model="edu.startDate"
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  </div>
                  <div class="form-group">
                    <label :for="`education-end-date-${index}`" class="block text-sm font-medium text-gray-700 mb-2">Data e Mbarimit <span class="text-red-500">*</span></label>
                    <input type="date" :id="`education-end-date-${index}`" v-model="edu.endDate" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  </div>
                </div>
              <button type="button" @click="removeEducation(index)" title="Fshij Edukimin"
                      class="absolute top-3 right-3 text-red-500 hover:text-red-700 transition-colors duration-200">
                <i class="fas fa-times-circle text-xl"></i>
              </button>
            </div>
            <button type="button" @click="addEducation"
                    class="mt-4 w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
              <i class="fas fa-plus mr-2"></i> Shto Edukim
            </button>
          </div>

          <!-- Step 3: Skills -->
          <div v-if="currentStep === 3" class="fade-in">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Aftësitë</h3>
             <div v-for="(skill, index) in cv.skills" :key="index" class="relative bg-gray-50 p-6 rounded-lg shadow-sm mb-6 border border-gray-200">
               <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="form-group">
                    <label :for="`skill-name-${index}`" class="block text-sm font-medium text-gray-700 mb-2">Emri i Aftësisë <span class="text-red-500">*</span></label>
                    <input type="text" :id="`skill-name-${index}`" v-model="skill.name" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                  </div>
                  <div class="form-group">
                    <label :for="`skill-level-${index}`" class="block text-sm font-medium text-gray-700 mb-2">Niveli i Aftësisë</label>
                    <select :id="`skill-level-${index}`" v-model="skill.level"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                      <option value="">Zgjidh Nivelin</option>
                      <option value="Beginner">Fillestar</option>
                      <option value="Intermediate">Mesatar</option>
                      <option value="Advanced">I Avancuar</option>
                      <option value="Expert">Ekspert</option>
                    </select>
                  </div>
                </div>
              <button type="button" @click="removeSkill(index)" title="Fshij Aftësinë"
                      class="absolute top-3 right-3 text-red-500 hover:text-red-700 transition-colors duration-200">
                <i class="fas fa-times-circle text-xl"></i>
              </button>
            </div>
            <button type="button" @click="addSkill"
                    class="mt-4 w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
              <i class="fas fa-plus mr-2"></i> Shto Aftësi
            </button>
          </div>

          <!-- Navigation Buttons -->
          <div class="flex justify-between items-center mt-10 pt-6 border-t border-gray-200">
            <button type="button" @click="prevStep" v-if="currentStep > 0"
                    class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              <i class="fas fa-arrow-left mr-3"></i>
              Hapi Paraprak
            </button>
            <div v-else></div> <!-- Placeholder to keep next button to the right -->
            <button type="submit"
                    :class="['inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                             { 'ml-auto': currentStep === 0 }]"
                    v-if="currentStep < stepNames.length - 1">
              Hapi Tjetër
              <i class="fas fa-arrow-right ml-3"></i>
            </button>
            <button type="button" @click="saveCv" v-if="currentStep === stepNames.length - 1"
                    class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              <i class="fas fa-save mr-3"></i>
              Ruaj CV-në
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
</template>

<style scoped>
/* Add a simple fade-in transition for step content */
.fade-in-enter-active, .fade-in-leave-active {
  transition: opacity 0.5s ease;
}
.fade-in-enter-from, .fade-in-leave-to {
  opacity: 0;
}
</style>

<script>
import { ref, reactive, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { useConfirmationModal } from '../../composables/useConfirmationModal.js';

export default {
  name: 'CreateCV',
  setup() {
    const route = useRoute();
    const router = useRouter();
    const { showModal } = useConfirmationModal();

    const template_to_use_for_form = ref(route.query.template || 'classic');
    const isSaving = ref(false);

    const currentStep = ref(0);
    const stepNames = [
      'Personale',
      'Përvoja',
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
    
    // Progress Bar
    const progressPercentage = computed(() => {
      return ((currentStep.value + 1) / stepNames.length) * 100;
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

    // Navigation
    const nextStep = () => {
      if (currentStep.value < stepNames.length - 1) {
        currentStep.value++;
      }
    };

    const prevStep = () => {
      if (currentStep.value > 0) {
        currentStep.value--;
      }
    };

    const goToStep = (index) => {
      if (index <= currentStep.value) {
        currentStep.value = index;
      }
    };

    const saveCv = async () => {
      if (isSaving.value) return;
      isSaving.value = true;

      if (!cv.personalInfo.firstName || !cv.personalInfo.email) {
        await showModal({
          title: 'Verifikim i Formulari',
          message: 'Ju lutemi plotësoni të paktën emrin dhe emailin tuaj në seksionin e informacioneve personale.',
          confirmButtonText: 'OK',
          confirmButtonClass: 'btn-primary',
          cancelButtonText: ''
        });
        isSaving.value = false;
        return;
      }

      const cvData = {
        title: cv.title,
        personal_details: {
          full_name: `${cv.personalInfo.firstName || ''} ${cv.personalInfo.lastName || ''}`.trim(),
          email: cv.personalInfo.email,
          phone_number: cv.personalInfo.phone,
          address: cv.personalInfo.address,
        },
        summary: cv.summary,
        work_experiences: cv.experience.map(exp => ({
          job_title: exp.title,
          company: exp.company,
          start_date: exp.startDate,
          end_date: exp.endDate,
          description: exp.description,
        })),
        educations: cv.education.map(edu => ({
          degree: edu.degree,
          institution: edu.university,
          start_date: edu.startDate,
          end_date: edu.endDate,
        })),
        skills: cv.skills,
        selected_template: cv.selected_template,
      };

      try {
        const response = await axios.post('/api/cvs', cvData);
        console.log('CV saved response:', response.data);

        if (response.data.success) {
          await showModal({
            title: 'Sukses!',
            message: 'CV-ja juaj u ruajt me sukses! Tani mund ta shkarkoni ose ta modifikoni më tej.',
            confirmButtonText: 'Shiko CV-në',
            confirmButtonClass: 'bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg',
            cancelButtonText: 'Më vonë',
            cancelButtonClass: 'bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg'
          }).then((result) => {
            if (result.isConfirmed) {
              router.push({ name: 'cv.preview', params: { id: response.data.cv.id } });
            }
          });
        } else {
          const errorMessages = response.data.errors ? Object.values(response.data.errors).flat().join('\n') : 'Një gabim i panjohur ndodhi.';
          await showModal({
            title: 'Gabim!',
            message: `Gabim gjatë ruajtjes së CV-së:\n${errorMessages}`,
            confirmButtonText: 'OK',
            confirmButtonClass: 'bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg',
            cancelButtonText: ''
          });
        }
      } catch (error) {
        console.error('Error saving CV:', error.response ? error.response.data : error.message);
        let errorMessage = 'Gabim gjatë ruajtjes së CV-së. Ju lutem provoni përsëri.';

        if (error.response && error.response.data && error.response.data.errors) {
          const detailedErrors = Object.values(error.response.data.errors).flat().join('\n');
          errorMessage = `Gabim gjatë ruajtjes së CV-së:\n${detailedErrors}`;
        } else if (error.message) {
          errorMessage = `Gabim i papritur: ${error.message}`;
        }
        
        await showModal({
          title: 'Gabim i Papritur!',
          message: errorMessage,
          confirmButtonText: 'OK',
          confirmButtonClass: 'bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg',
          cancelButtonText: ''
        });
      } finally {
        isSaving.value = false;
      }
    };
    
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
      progressPercentage,
      goToStep,
      isSaving
    };
  }
}
</script>



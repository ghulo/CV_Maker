<template>
  <div class="p-4 sm:p-6 lg:p-8 bg-neutral-bg dark:bg-dark-neutral-bg min-h-screen">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-neutral-text dark:text-dark-neutral-text">{{ isEditing ? 'Redakto CV' : 'Krijo CV të re' }}</h1>
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
            {{ isEditing ? 'Ruaj Ndryshimet' : 'Ruaj CV-në' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

export default {
  name: 'CvEditor',
  props: ['id'],
  setup(props) {
    const router = useRouter()
    const route = useRoute()

    const currentStep = ref(0)
    const stepNames = ['Informacione Personale', 'Përvoja e Punës', 'Edukimi', 'Aftësitë'] // Add more steps as needed

    const cv = ref({
      title: '',
      personalInfo: {
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        address: '',
      },
      experience: [],
      education: [],
      skills: [],
      summary: '',
    })

    const isEditing = computed(() => !!props.id)

    const addExperience = () => {
      cv.value.experience.push({
        title: '',
        company: '',
        startDate: '',
        endDate: '',
        description: '',
      })
    }

    const removeExperience = (index) => {
      cv.value.experience.splice(index, 1)
    }

    const addEducation = () => {
      cv.value.education.push({
        degree: '',
        university: '',
        startDate: '',
        endDate: '',
      })
    }

    const removeEducation = (index) => {
      cv.value.education.splice(index, 1)
    }

    const addSkill = () => {
      cv.value.skills.push({
        name: '',
        level: '',
      })
    }

    const removeSkill = (index) => {
      cv.value.skills.splice(index, 1)
    }

    const nextStep = () => {
      if (currentStep.value < stepNames.length - 1) {
        currentStep.value++
      } else {
        // This is the last step, so save the CV
        saveCv()
      }
    }

    const prevStep = () => {
      if (currentStep.value > 0) {
        currentStep.value--
      }
    }

    const fetchCvData = async () => {
      if (!isEditing.value) return
      try {
        const response = await axios.get(`/api/cvs/${props.id}`)
        if (response.data.success) {
          // Map existing CV data to new multi-step structure
          const fetchedCv = response.data.cv
          cv.value.title = fetchedCv.cv_title || ''
          
          // Personal Info
          const fullName = fetchedCv.personal_details?.full_name || '';
          const nameParts = fullName.split(' ', 2);
          cv.value.personalInfo.firstName = nameParts[0] || '';
          cv.value.personalInfo.lastName = nameParts[1] || '';
          cv.value.personalInfo.email = fetchedCv.personal_details?.email || '';
          cv.value.personalInfo.phone = fetchedCv.personal_details?.phone_number || '';
          cv.value.personalInfo.address = fetchedCv.personal_details?.address || '';

          // Experience, Education, Skills
          cv.value.experience = fetchedCv.experiences || [];
          cv.value.education = fetchedCv.educations || [];
          cv.value.skills = fetchedCv.skills || [];
          cv.value.summary = fetchedCv.summary || '';
        }
      } catch (error) {
        console.error('Error fetching CV data:', error)
        router.push({ name: 'dashboard' })
      }
    }

    const saveCv = async () => {
      try {
        let response
        const dataToSave = {
          cv_title: cv.value.title,
          personal_details: {
            full_name: `${cv.value.personalInfo.firstName} ${cv.value.personalInfo.lastName}`.trim(),
            email: cv.value.personalInfo.email,
            phone_number: cv.value.personalInfo.phone,
            address: cv.value.personalInfo.address,
          },
          summary: cv.value.summary,
          experiences: cv.value.experience,
          educations: cv.value.education,
          skills: cv.value.skills,
        }

        if (isEditing.value) {
          // Update existing CV
          response = await axios.put(`/api/cvs/${props.id}`, dataToSave)
        } else {
          // Create new CV
          response = await axios.post('/api/cvs', dataToSave)
        }

        if (response.data.success) {
          router.push({ name: 'dashboard' })
        }
      } catch (error) {
        console.error('Error saving CV:', error)
        // You might want to display an error message to the user here
      }
    }

    onMounted(() => {
      fetchCvData()
    })

    return {
      currentStep,
      stepNames,
      cv,
      isEditing,
      addExperience,
      removeExperience,
      addEducation,
      removeEducation,
      addSkill,
      removeSkill,
      nextStep,
      prevStep,
      saveCv,
    }
  },
}
</script>

<style scoped>
@reference "tailwindcss/theme";
</style>

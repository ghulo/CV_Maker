<template>
  <div class="page-wrapper edit-cv-wrapper">
    <section class="form-container">
      <div class="text-center mb-xl">
        <h1 class="section-title">{{ t('cv.editCV') }}</h1>
        <p class="section-description">
          {{ t('cv.editDescription') }}
        </p>
      </div>

      <div v-if="loadingCv" class="loading-spinner-wrapper">
        <LoadingSpinner :size="'large'" />
        <p class="text-muted mt-md">{{ t('common.loading') }}...</p>
      </div>

      <div v-else-if="error" class="message error">
        <i class="icon fas fa-exclamation-triangle"></i>
        <p class="message-text">{{ error }}</p>
      </div>

      <form v-else @submit.prevent="saveCv" class="cv-form-sections">
        <!-- Personal Details Section -->
        <div class="form-section fade-in">
          <h2 class="form-section-title">
            <i class="fas fa-user section-icon"></i>
            {{ t('cv.personalDetails') }}
          </h2>

          <div class="card card-md mb-lg">
            <div class="form-grid">
              <div class="form-group">
                <label for="cv-title" class="form-label required">
                  {{ t('form.cvTitle') }}
                </label>
                <input
                  type="text"
                  id="cv-title"
                  v-model="cv.title"
                  required
                  class="form-input"
                  :placeholder="t('form.cvTitlePlaceholder')"
                />
              </div>
              <div class="form-group">
                <label for="first-name" class="form-label required">
                  {{ t('form.firstName') }}
                </label>
                <input
                  type="text"
                  id="first-name"
                  v-model="cv.personalDetails.firstName"
                  required
                  class="form-input"
                  :placeholder="t('form.firstNamePlaceholder')"
                />
              </div>
              <div class="form-group">
                <label for="last-name" class="form-label">
                  {{ t('form.lastName') }}
                </label>
                <input
                  type="text"
                  id="last-name"
                  v-model="cv.personalDetails.lastName"
                  class="form-input"
                  :placeholder="t('form.lastNamePlaceholder')"
                />
              </div>
              <div class="form-group">
                <label for="email" class="form-label required">
                  {{ t('form.email') }}
                </label>
                <input
                  type="email"
                  id="email"
                  v-model="cv.personalDetails.email"
                  required
                  class="form-input"
                  :placeholder="t('form.emailPlaceholder')"
                />
              </div>
              <div class="form-group">
                <label for="phone" class="form-label">
                  {{ t('form.phone') }}
                </label>
                <input
                  type="tel"
                  id="phone"
                  v-model="cv.personalDetails.phone"
                  class="form-input"
                  :placeholder="t('form.phonePlaceholder')"
                />
              </div>
              <div class="form-group form-grid-full">
                <label for="address" class="form-label">
                  {{ t('form.address') }}
                </label>
                <input
                  type="text"
                  id="address"
                  v-model="cv.personalDetails.address"
                  class="form-input"
                  :placeholder="t('form.addressPlaceholder')"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Summary Section -->
        <div class="form-section fade-in">
          <h2 class="form-section-title">
            <i class="fas fa-file-text section-icon"></i>
            {{ t('cv.summary') }}
          </h2>
          <div class="card card-md mb-lg">
            <div class="form-group">
              <label for="summary" class="form-label">
                {{ t('form.summary') }}
              </label>
              <textarea
                id="summary"
                v-model="cv.summary"
                rows="4"
                class="form-textarea"
                :placeholder="t('form.summaryPlaceholder')"
              ></textarea>
              <p class="form-helper">
                {{ t('form.summaryHelper') }}
              </p>
            </div>
          </div>
        </div>

        <!-- Work Experience Section -->
        <div class="form-section fade-in">
          <h2 class="form-section-title">
            <i class="fas fa-briefcase section-icon"></i>
            {{ t('cv.workExperience') }}
          </h2>

          <div
            v-for="(exp, index) in cv.experience"
            :key="index"
            class="card card-md mb-lg relative experience-item"
          >
            <div class="form-grid">
              <div class="form-group">
                <label :for="`job-title-${index}`" class="form-label required">
                  {{ t('form.jobTitle') }}
                </label>
                <input
                  type="text"
                  :id="`job-title-${index}`"
                  v-model="exp.title"
                  required
                  class="form-input"
                  :placeholder="t('form.jobTitlePlaceholder')"
                />
              </div>
              <div class="form-group">
                <label :for="`company-${index}`" class="form-label required">
                  {{ t('form.company') }}
                </label>
                <input
                  type="text"
                  :id="`company-${index}`"
                  v-model="exp.company"
                  required
                  class="form-input"
                  :placeholder="t('form.companyPlaceholder')"
                />
              </div>
              <div class="form-group">
                <label :for="`start-date-${index}`" class="form-label required">
                  {{ t('form.startDate') }}
                </label>
                <input
                  type="date"
                  :id="`start-date-${index}`"
                  v-model="exp.startDate"
                  required
                  class="form-input"
                />
              </div>
              <div class="form-group">
                <label :for="`end-date-${index}`" class="form-label">
                  {{ t('form.endDate') }}
                </label>
                <input
                  type="date"
                  :id="`end-date-${index}`"
                  v-model="exp.endDate"
                  class="form-input"
                />
                <p class="form-helper">
                  {{ t('form.endDateHelper') }}
                </p>
              </div>
              <div class="form-group form-grid-full">
                <label :for="`description-${index}`" class="form-label">
                  {{ t('form.jobDescription') }}
                </label>
                <textarea
                  :id="`description-${index}`"
                  v-model="exp.description"
                  rows="3"
                  class="form-textarea"
                  :placeholder="t('form.jobDescriptionPlaceholder')"
                ></textarea>
              </div>
            </div>
            <button
              type="button"
              @click="removeExperience(index)"
              :title="t('common.delete')"
              class="btn btn-danger btn-icon remove-item-btn"
            >
              <i class="fas fa-times-circle"></i>
            </button>
          </div>
          <button
            type="button"
            @click="addExperience"
            class="btn btn-secondary btn-full-width mt-lg"
          >
            <i class="fas fa-plus icon"></i> {{ t('common.addExperience') }}
          </button>
        </div>

        <!-- Education Section -->
        <div class="form-section fade-in">
          <h2 class="form-section-title">
            <i class="fas fa-graduation-cap section-icon"></i>
            {{ t('cv.education') }}
          </h2>
          <div
            v-for="(edu, index) in cv.education"
            :key="index"
            class="card card-md mb-lg relative education-item"
          >
            <div class="form-grid">
              <div class="form-group">
                <label :for="`degree-${index}`" class="form-label required">
                  {{ t('form.degree') }}
                </label>
                <input
                  type="text"
                  :id="`degree-${index}`"
                  v-model="edu.degree"
                  required
                  class="form-input"
                  :placeholder="t('form.degreePlaceholder')"
                />
              </div>
              <div class="form-group">
                <label :for="`university-${index}`" class="form-label required">
                  {{ t('form.institution') }}
                </label>
                <input
                  type="text"
                  :id="`university-${index}`"
                  v-model="edu.university"
                  required
                  class="form-input"
                  :placeholder="t('form.institutionPlaceholder')"
                />
              </div>
              <div class="form-group">
                <label :for="`edu-start-date-${index}`" class="form-label required">
                  {{ t('form.startDate') }}
                </label>
                <input
                  type="date"
                  :id="`edu-start-date-${index}`"
                  v-model="edu.startDate"
                  required
                  class="form-input"
                />
              </div>
              <div class="form-group">
                <label :for="`edu-end-date-${index}`" class="form-label">
                  {{ t('form.endDate') }}
                </label>
                <input
                  type="date"
                  :id="`edu-end-date-${index}`"
                  v-model="edu.endDate"
                  class="form-input"
                />
                <p class="form-helper">
                  {{ t('form.eduEndDateHelper') }}
                </p>
              </div>
              <div class="form-group form-grid-full">
                <label :for="`edu-description-${index}`" class="form-label">
                  {{ t('form.description') }} ({{ t('common.optional') }})
                </label>
                <textarea
                  :id="`edu-description-${index}`"
                  v-model="edu.description"
                  rows="3"
                  class="form-textarea"
                  :placeholder="t('form.eduDescriptionPlaceholder')"
                ></textarea>
              </div>
            </div>
            <button
              type="button"
              @click="removeEducation(index)"
              :title="t('common.delete')"
              class="btn btn-danger btn-icon remove-item-btn"
            >
              <i class="fas fa-times-circle"></i>
            </button>
          </div>
          <button
            type="button"
            @click="addEducation"
            class="btn btn-secondary btn-full-width mt-lg"
          >
            <i class="fas fa-plus icon"></i> {{ t('common.addEducation') }}
          </button>
        </div>

        <!-- Skills Section -->
        <div class="form-section fade-in">
          <h2 class="form-section-title">
            <i class="fas fa-tools section-icon"></i>
            {{ t('cv.skills') }}
          </h2>
          <p class="section-description">
            {{ t('cv.skillsDescription') }}
          </p>
          <div
            v-for="(skill, index) in cv.skills"
            :key="index"
            class="card card-md mb-lg relative skill-item"
          >
            <div class="form-grid">
              <div class="form-group">
                <label :for="`skill-name-${index}`" class="form-label required">
                  {{ t('form.skillName') }}
                </label>
                <input
                  type="text"
                  :id="`skill-name-${index}`"
                  v-model="skill.name"
                  required
                  class="form-input"
                  :placeholder="t('form.skillNamePlaceholder')"
                />
              </div>
              <div class="form-group">
                <label :for="`skill-rating-${index}`" class="form-label">
                  {{ t('form.skillLevel') }} (1-5)
                </label>
                <input
                  type="number"
                  :id="`skill-rating-${index}`"
                  v-model="skill.level"
                  min="1"
                  max="5"
                  class="form-input"
                />
                <p class="form-helper">
                  {{ t('form.skillLevelHelper') }}
                </p>
              </div>
            </div>
            <button
              type="button"
              @click="removeSkill(index)"
              :title="t('common.delete')"
              class="btn btn-danger btn-icon remove-item-btn"
            >
              <i class="fas fa-times-circle"></i>
            </button>
          </div>
          <button
            type="button"
            @click="addSkill"
            class="btn btn-secondary btn-full-width mt-lg"
          >
            <i class="fas fa-plus icon"></i> {{ t('common.addSkill') }}
          </button>
        </div>

        <!-- Interests Section -->
        <div class="form-section fade-in">
          <h2 class="form-section-title">
            <i class="fas fa-heart section-icon"></i>
            {{ t('cv.interests') }}
          </h2>
          <p class="section-description">
            {{ t('cv.interestsDescription') }}
          </p>
          <div
            v-for="(interest, index) in cv.interests"
            :key="index"
            class="card card-md mb-lg relative interest-item"
          >
            <div class="form-group">
              <label :for="`interest-name-${index}`" class="form-label required">
                {{ t('form.interestName') }}
              </label>
              <input
                type="text"
                :id="`interest-name-${index}`"
                v-model="interest.name"
                required
                class="form-input"
                :placeholder="t('form.interestNamePlaceholder')"
              />
            </div>
            <button
              type="button"
              @click="removeInterest(index)"
              :title="t('common.delete')"
              class="btn btn-danger btn-icon remove-item-btn"
            >
              <i class="fas fa-times-circle"></i>
            </button>
          </div>
          <button
            type="button"
            @click="addInterest"
            class="btn btn-secondary btn-full-width mt-lg"
          >
            <i class="fas fa-plus icon"></i> {{ t('common.addInterest') }}
          </button>
        </div>

        <!-- Action Buttons -->
        <div class="form-actions-sticky">
          <button
            type="submit"
            :disabled="loading"
            class="btn btn-primary ml-auto btn-with-icon"
          >
            <font-awesome-icon v-if="loading" icon="fa-solid fa-spinner" class="fa-spin icon" />
            <i v-else class="fas fa-save icon"></i>
            {{ t('common.saveChanges') }}
          </button>
        </div>
      </form>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import axios from 'axios'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const route = useRoute()
const router = useRouter()
const { t } = useI18n()

const cvId = route.params.id
const cv = ref({
  title: '',
  personalDetails: {},
  summary: '',
  experience: [],
  education: [],
  skills: [],
  interests: [],
  selectedTemplate: 'classic',
  isFinalized: false,
})

const loadingCv = ref(true)
const loading = ref(false)
const error = ref(null)

const addInitialItems = () => {
  // Ensure arrays exist and have at least one item for editing
  if (!cv.value.experience || cv.value.experience.length === 0) addExperience()
  if (!cv.value.education || cv.value.education.length === 0) addEducation()
  if (!cv.value.skills || cv.value.skills.length === 0) addSkill()
  if (!cv.value.interests || cv.value.interests.length === 0) addInterest()
  
  // Ensure personalDetails object exists
  if (!cv.value.personalDetails) {
    cv.value.personalDetails = {
      firstName: '',
      lastName: '',
      email: '',
      phone: '',
      address: '',
      dateOfBirth: '',
      placeOfBirth: '',
      gender: '',
      nationality: '',
      zipCode: '',
      maritalStatus: '',
      drivingLicense: ''
    }
  }
}

const fetchCvData = async () => {
  loadingCv.value = true
  error.value = null
  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.get(`/api/cvs/${cvId}`, {
      headers: { Authorization: `Bearer ${token}` },
    })
    if (response.data.success && response.data.cv) {
      const fetchedCv = response.data.cv
      cv.value.title = fetchedCv.title || ''
      cv.value.personalDetails = fetchedCv.personalDetails || {}
      cv.value.summary = fetchedCv.summary || ''
      cv.value.experience = fetchedCv.experience && fetchedCv.experience.length > 0 ? 
        fetchedCv.experience.map(exp => ({...exp, startDate: exp.startDate || '', endDate: exp.endDate || ''})) : []
      cv.value.education = fetchedCv.education && fetchedCv.education.length > 0 ? 
        fetchedCv.education.map(edu => ({...edu, startDate: edu.startDate || '', endDate: edu.endDate || ''})) : []
      cv.value.skills = fetchedCv.skills && fetchedCv.skills.length > 0 ? 
        fetchedCv.skills.map(skill => ({...skill, level: skill.level || 3})) : []
      cv.value.interests = fetchedCv.interests && fetchedCv.interests.length > 0 ? fetchedCv.interests : []
      cv.value.selectedTemplate = fetchedCv.selectedTemplate || 'classic'
      cv.value.isFinalized = fetchedCv.isFinalized || false
      addInitialItems()
    } else {
      error.value = response.data.message || t('errors.cvNotFound')
    }
  } catch (err) {
    error.value = t('errors.cvLoadError')
    console.error(err)
  } finally {
    loadingCv.value = false
  }
}

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
  if (cv.value.experience.length === 0) addExperience()
}

const addEducation = () => {
  cv.value.education.push({
    degree: '',
    university: '',
    startDate: '',
    endDate: '',
    description: '',
  })
}

const removeEducation = (index) => {
  cv.value.education.splice(index, 1)
  if (cv.value.education.length === 0) addEducation()
}

const addSkill = () => {
  cv.value.skills.push({ name: '', level: 3 })
}

const removeSkill = (index) => {
  cv.value.skills.splice(index, 1)
  if (cv.value.skills.length === 0) addSkill()
}

const addInterest = () => {
  cv.value.interests.push({ name: '', description: '' })
}

const removeInterest = (index) => {
  cv.value.interests.splice(index, 1)
  if (cv.value.interests.length === 0) addInterest()
}

const saveCv = async () => {
  loading.value = true
  error.value = null
  try {
    const token = localStorage.getItem('auth_token')
    if (!token) {
      router.push('/login')
      return
    }

    const payload = {
      id: cvId,
      title: cv.value.title,
      personalDetails: cv.value.personalDetails,
      summary: cv.value.summary,
      experience: cv.value.experience.map((exp) => ({ ...exp, is_current_job: !exp.endDate })),
      education: cv.value.education,
      skills: cv.value.skills,
      interests: cv.value.interests,
      selectedTemplate: cv.value.selectedTemplate,
      isFinalized: cv.value.isFinalized,
    }

    const response = await axios.put(`/api/cvs/${cvId}`, payload, {
      headers: { Authorization: `Bearer ${token}` },
    })

    if (response.data.success) {
      alert(t('common.saveSuccess'))
      router.push({ name: 'cv.preview', params: { id: cvId } })
    } else {
      error.value = response.data.message || t('errors.saveError')
    }
  } catch (err) {
    error.value = err.response?.data?.message || t('errors.saveError')
    console.error('Error saving CV:', err.response ? err.response.data : err.message)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchCvData()
})
</script>

<style scoped>
.edit-cv-wrapper {
  padding-top: var(--header-height-compact);
  padding-bottom: var(--space-xxl);
  background-color: var(--bg-secondary);
}

.form-container {
  max-width: 900px;
  margin: var(--space-xxl) auto;
  padding: var(--space-xxl);
  background-color: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
}

.text-center {
  text-align: center;
}

.mb-xl {
  margin-bottom: var(--space-xl);
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

.section-description {
  font-size: var(--font-size-lg);
  color: var(--text-secondary);
  line-height: var(--line-height-normal);
  max-width: 700px;
  margin: 0 auto var(--space-xl);
}

.loading-spinner-wrapper {
  padding: var(--space-xxl);
  text-align: center;
}

.message {
  padding: var(--space-md);
  border-radius: var(--radius);
  margin-bottom: var(--space-lg);
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  font-size: var(--font-size-base);
}

.message.error {
  background-color: var(--danger-light);
  color: var(--danger-dark);
  border: 1px solid var(--danger);
}

.message .icon {
  font-size: var(--font-size-lg);
}

.cv-form-sections {
  margin-top: var(--space-xl);
}

.form-section {
  background-color: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: var(--space-xl);
  margin-bottom: var(--space-xxl);
  box-shadow: var(--shadow-md);
}

.form-section-title {
  font-family: var(--font-heading);
  font-size: var(--font-size-3xl);
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: var(--space-lg);
  padding-bottom: var(--space-md);
  border-bottom: 1px solid var(--border-light);
  display: flex;
  align-items: center;
  gap: var(--space-md);
}

.section-icon {
  color: var(--primary);
  font-size: var(--font-size-2xl);
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: var(--space-lg);
}

.form-grid-full {
  grid-column: 1 / -1;
}

.remove-item-btn {
  position: absolute;
  top: var(--space-md);
  right: var(--space-md);
  z-index: var(--z-elevate);
  width: 36px;
  height: 36px;
  font-size: var(--font-size-md);
  border-radius: var(--radius-full);
  background-color: var(--danger);
  color: white;
  box-shadow: var(--shadow-sm);
  transition: var(--transition-all);
}

.remove-item-btn:hover {
  background-color: var(--danger-dark);
  box-shadow: var(--shadow-md);
  transform: translateY(-1px);
}

.btn-full-width {
  width: 100%;
}

.form-actions-sticky {
  position: sticky;
  bottom: 0;
  left: 0;
  width: 100%;
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: var(--space-md) var(--space-xl);
  background-color: var(--bg-card);
  border-top: 1px solid var(--border-light);
  box-shadow: var(--shadow-sm);
  z-index: var(--z-sticky);
  gap: var(--space-md);
}

.icon {
  margin-right: var(--space-xs);
}

.btn-with-icon .icon {
  margin-right: var(--space-xs);
}

@media (max-width: 768px) {
  .edit-cv-wrapper {
    padding: var(--space-md);
  }

  .form-container {
    margin: var(--space-lg) auto;
    padding: var(--space-lg);
  }

  .section-title {
    font-size: var(--font-size-3xl);
  }

  .section-description {
    font-size: var(--font-size-base);
  }

  .form-section-title {
    font-size: var(--font-size-2xl);
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-actions-sticky {
    flex-direction: column;
    gap: var(--space-sm);
    padding: var(--space-md);
  }

  .form-actions-sticky .btn {
    width: 100%;
    margin-left: 0;
  }

  .remove-item-btn {
    position: static;
    width: 100%;
    margin-top: var(--space-md);
    border-radius: var(--radius);
  }
}
</style> 
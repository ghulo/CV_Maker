<template>
  <div class="page-wrapper edit-cv-wrapper">
    <section class="form-container">
      <!-- Header -->
      <div class="text-center mb-xl">
        <h1 class="section-title">Redakto CV</h1>
        <p class="section-description">
          Plotësoni informacionin e mëposhtëm për të ndërtuar CV-në tuaj.
        </p>
      </div>

      <div v-if="loadingCv" class="loading-spinner-wrapper">
        <i class="fas fa-spinner fa-spin loading-spinner"></i>
        <p class="text-muted mt-md">Duke ngarkuar të dhënat e CV-së...</p>
      </div>

      <div v-else-if="error" class="message error">
        <i class="icon fas fa-exclamation-triangle"></i>
        <p class="message-text">{{ error }}</p>
      </div>

      <form v-else @submit.prevent="saveCv" class="cv-form-sections">
        <!-- Work Experience Section -->
        <div class="form-section fade-in">
          <h2 class="form-section-title">Përvoja e Punës</h2>

          <div
            v-for="(exp, index) in cv.experience"
            :key="index"
            class="card card-md mb-lg relative experience-item"
          >
            <div class="form-grid">
              <div class="form-group">
                <label :for="`job-title-${index}`" class="form-label required">Titulli i Pozicionit</label>
                <input
                  type="text"
                  :id="`job-title-${index}`"
                  v-model="exp.title"
                  required
                  class="form-input"
                />
              </div>
              <div class="form-group">
                <label :for="`company-${index}`" class="form-label required">Kompania</label>
                <input
                  type="text"
                  :id="`company-${index}`"
                  v-model="exp.company"
                  required
                  class="form-input"
                />
              </div>
              <div class="form-group">
                <label :for="`start-date-${index}`" class="form-label required">Data e Fillimit</label>
                <input
                  type="date"
                  :id="`start-date-${index}`"
                  v-model="exp.startDate"
                  required
                  class="form-input"
                />
              </div>
              <div class="form-group">
                <label :for="`end-date-${index}`" class="form-label">Data e Mbarimit</label>
                <input
                  type="date"
                  :id="`end-date-${index}`"
                  v-model="exp.endDate"
                  class="form-input"
                />
                <p class="form-helper">
                  Lëreni bosh nëse jeni ende duke punuar këtu.
                </p>
              </div>
              <div class="form-group form-grid-full">
                <label :for="`description-${index}`" class="form-label">Përshkrimi i Detyrave</label>
                <textarea
                  :id="`description-${index}`"
                  v-model="exp.description"
                  rows="3"
                  class="form-textarea"
                ></textarea>
              </div>
            </div>
            <button
              type="button"
              @click="removeExperience(index)"
              title="Fshij Përvojën"
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
            <i class="fas fa-plus icon"></i> Shto Përvojë
          </button>
        </div>

        <!-- Education Section -->
        <div class="form-section fade-in">
          <h2 class="form-section-title">Edukimi</h2>
          <div
            v-for="(edu, index) in cv.education"
            :key="index"
            class="card card-md mb-lg relative education-item"
          >
            <div class="form-grid">
              <div class="form-group">
                <label :for="`degree-${index}`" class="form-label required">Titulli i Diplomës / Fusha e Studimit</label>
                <input
                  type="text"
                  :id="`degree-${index}`"
                  v-model="edu.degree"
                  required
                  class="form-input"
                />
              </div>
              <div class="form-group">
                <label :for="`university-${index}`" class="form-label required">Institucioni / Universiteti</label>
                <input
                  type="text"
                  :id="`university-${index}`"
                  v-model="edu.university"
                  required
                  class="form-input"
                />
              </div>
              <div class="form-group">
                <label :for="`edu-start-date-${index}`" class="form-label required">Data e Fillimit</label>
                <input
                  type="date"
                  :id="`edu-start-date-${index}`"
                  v-model="edu.startDate"
                  required
                  class="form-input"
                />
              </div>
              <div class="form-group">
                <label :for="`edu-end-date-${index}`" class="form-label">Data e Mbarimit</label>
                <input
                  type="date"
                  :id="`edu-end-date-${index}`"
                  v-model="edu.endDate"
                  class="form-input"
                />
                <p class="form-helper">
                  Lëreni bosh nëse jeni ende duke studiuar këtu.
                </p>
              </div>
              <div class="form-group form-grid-full">
                <label :for="`edu-description-${index}`" class="form-label">Përshkrimi (opsionale)</label>
                <textarea
                  :id="`edu-description-${index}`"
                  v-model="edu.description"
                  rows="3"
                  class="form-textarea"
                ></textarea>
              </div>
            </div>
            <button
              type="button"
              @click="removeEducation(index)"
              title="Fshij Edukimin"
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
            <i class="fas fa-plus icon"></i> Shto Edukim
          </button>
        </div>

        <!-- Skills Section -->
        <div class="form-section fade-in">
          <h2 class="form-section-title">Aftësitë</h2>
          <p class="section-description">
            Shtoni aftësitë tuaja teknike dhe të buta. Këto do të shfaqen si lista ose shirita
            progresi në varësi të modelit të zgjedhur të CV-së.
          </p>
          <div
            v-for="(skill, index) in cv.skills"
            :key="index"
            class="card card-md mb-lg relative skill-item"
          >
            <div class="form-grid">
              <div class="form-group">
                <label :for="`skill-name-${index}`" class="form-label required">Emri i Aftësisë</label>
                <input
                  type="text"
                  :id="`skill-name-${index}`"
                  v-model="skill.name"
                  required
                  class="form-input"
                />
              </div>
              <div class="form-group">
                <label :for="`skill-rating-${index}`" class="form-label">Niveli (1-5)</label>
                <input
                  type="number"
                  :id="`skill-rating-${index}`"
                  v-model="skill.level"
                  min="1"
                  max="5"
                  class="form-input"
                />
                <p class="form-helper">
                  Opsionale. Përdoret për aftësitë me shiritat e progresit.
                </p>
              </div>
            </div>
            <button
              type="button"
              @click="removeSkill(index)"
              title="Fshij Aftësinë"
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
            <i class="fas fa-plus icon"></i> Shto Aftësi
          </button>
        </div>

        <!-- Interests Section -->
        <div class="form-section fade-in">
          <h2 class="form-section-title">Interesat</h2>
          <p class="section-description">
            Shtoni interesat tuaja për të treguar më shumë personalitet. Këto do të shfaqen si
            një listë.
          </p>
          <div
            v-for="(interest, index) in cv.interests"
            :key="index"
            class="card card-md mb-lg relative interest-item"
          >
            <div class="form-group">
              <label :for="`interest-name-${index}`" class="form-label required">Emri i Interesit</label>
              <input
                type="text"
                :id="`interest-name-${index}`"
                v-model="interest.name"
                required
                class="form-input"
              />
            </div>
            <button
              type="button"
              @click="removeInterest(index)"
              title="Fshij Interesin"
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
            <i class="fas fa-plus icon"></i> Shto Interes
          </button>
        </div>

        <!-- Action Buttons (Save) -->
        <div class="form-actions-sticky">
          <button
            type="submit"
            :disabled="loading"
            class="btn btn-primary ml-auto btn-with-icon"
          >
            <font-awesome-icon v-if="loading" icon="fa-solid fa-spinner" class="fa-spin icon" />
            <i v-else class="fas fa-save icon"></i>
            Ruaj Ndryshimet
          </button>
        </div>
      </form>
    </section>
  </div>
</template>

<script setup>
  import { ref, onMounted, reactive, watch } from 'vue'
  import { useRoute, useRouter } from 'vue-router'
  import axios from 'axios'

  const route = useRoute()
  const router = useRouter()

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

  const loadingCv = ref(true) // New loading state for fetching CV
  const loading = ref(false) // For save operations
  const error = ref(null)

  // Dummy initial items for new entries if lists are empty
  const addInitialItems = () => {
    if (cv.value.experience.length === 0) addExperience()
    if (cv.value.education.length === 0) addEducation()
    if (cv.value.skills.length === 0) addSkill()
    if (cv.value.interests.length === 0) addInterest()
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
        // Map fetched data to cv.value structure
        cv.value.title = fetchedCv.title || ''
        cv.value.personalDetails = fetchedCv.personalDetails || {}
        cv.value.summary = fetchedCv.summary || ''
        // Ensure arrays are initialized correctly and contain objects with required keys
        cv.value.experience = fetchedCv.experience && fetchedCv.experience.length > 0 ? fetchedCv.experience.map(exp => ({...exp, startDate: exp.startDate || '', endDate: exp.endDate || ''})) : []
        cv.value.education = fetchedCv.education && fetchedCv.education.length > 0 ? fetchedCv.education.map(edu => ({...edu, startDate: edu.startDate || '', endDate: edu.endDate || ''})) : []
        cv.value.skills = fetchedCv.skills && fetchedCv.skills.length > 0 ? fetchedCv.skills.map(skill => ({...skill, level: skill.level || 3})) : [] // Ensure level is set
        cv.value.interests = fetchedCv.interests && fetchedCv.interests.length > 0 ? fetchedCv.interests : []
        cv.value.selectedTemplate = fetchedCv.selectedTemplate || 'classic'
        cv.value.isFinalized = fetchedCv.isFinalized || false
        addInitialItems() // Add empty items if a section is empty after fetching
      } else {
        error.value = response.data.message || 'CV nuk u gjet.'
      }
    } catch (err) {
      error.value = 'Gabim gjatë ngarkimit të CV-së. Ju lutem provoni përsëri.'
      console.error(err)
      // Optionally redirect on severe error
      // router.push({ name: 'dashboard' });
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
    cv.value.skills.push({ name: '', level: 3 }) // Default level
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
    error.value = null // Clear previous errors
    try {
      const token = localStorage.getItem('auth_token')
      if (!token) {
        router.push('/login')
        return
      }

      const payload = {
        id: cvId,
        title: cv.value.title,
        personalDetails: cv.value.personalInfo,
        summary: cv.value.summary,
        experience: cv.value.experience.map((exp) => ({ ...exp, is_current_job: !exp.endDate })),
        education: cv.value.education,
        skills: cv.value.skills,
        interests: cv.value.interests,
        selectedTemplate: cv.value.selectedTemplate,
        isFinalized: cv.value.isFinalized, // Assuming it remains as it was fetched, or updated elsewhere
      }

      const response = await axios.put(`/api/cvs/${cvId}`, payload, {
        headers: { Authorization: `Bearer ${token}` },
      })

      if (response.data.success) {
        alert('CV u ruajt me sukses!')
        // Optionally, navigate to preview or dashboard
        router.push({ name: 'cv.preview', params: { id: cvId } })
      } else {
        error.value = response.data.message || 'Gabim gjatë ruajtjes së CV-së.'
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Ndodhi një gabim gjatë ruajtjes së CV-së. Ju lutem provoni përsëri.'
      console.error('Error saving CV:', err.response ? err.response.data : err.message)
    } finally {
      loading.value = false
    }
  }

  onMounted(() => {
    fetchCvData()
  })

  watch(cv, () => {
    // reinitializeScrollReveal()
  }, { deep: true })
</script>

<style scoped>
  .edit-cv-wrapper {
    padding-top: var(--header-height-compact);
    padding-bottom: var(--space-xxl);
    background-color: var(--bg-secondary); /* Consistent page background */
  }

  .form-container {
    max-width: 900px;
    margin: var(--space-xxl) auto;
    padding: var(--space-xxl);
    background-color: var(--bg-card); /* Consistent with card background */
    border: 1px solid var(--border); /* Consistent border */
    border-radius: var(--radius-lg); /* Consistent radius */
    box-shadow: var(--shadow-md); /* Consistent shadow */
  }

  .text-center {
    text-align: center;
  }

  .mb-xl {
    margin-bottom: var(--space-xl);
  }

  /* Section Title and Description (re-using global utility classes) */
  .section-title {
    font-family: var(--font-heading);
    font-size: var(--font-size-4xl);
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: var(--space-md);
    line-height: var(--line-height-tight); /* Consistent line height */
    letter-spacing: var(--letter-spacing-tight); /* Consistent letter spacing */
  }

  .section-description {
    font-size: var(--font-size-lg);
    color: var(--text-secondary);
    line-height: var(--line-height-normal);
    max-width: 700px; /* Consistent narrower width */
    margin: 0 auto var(--space-xl);
  }

  /* Loading and Error States */
  .loading-spinner-wrapper,
  .empty-state-container {
    padding: var(--space-xxl);
    text-align: center;
  }

  .loading-spinner {
    font-size: var(--font-size-4xl);
    color: var(--primary);
  }

  .message {
    padding: var(--space-md); /* Consistent padding */
    border-radius: var(--radius); /* Consistent radius */
    margin-bottom: var(--space-lg);
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    font-size: var(--font-size-base);
  }

  .message.error {
    background-color: var(--danger-light); /* Consistent with global message styles */
    color: var(--danger-dark); /* Consistent with global message styles */
    border: 1px solid var(--danger); /* Consistent with global message styles */
  }

  .message .icon {
    font-size: var(--font-size-lg);
  }

  /* Form Sections */
  .cv-form-sections {
    margin-top: var(--space-xl);
  }

  .form-section {
    background-color: var(--bg-card); /* Consistent with card background */
    border: 1px solid var(--border); /* Consistent border */
    border-radius: var(--radius-lg); /* Consistent radius */
    padding: var(--space-xl);
    margin-bottom: var(--space-xxl);
    box-shadow: var(--shadow-md); /* Consistent shadow */
  }

  .form-section-title {
    font-family: var(--font-heading);
    font-size: var(--font-size-3xl);
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: var(--space-lg);
    padding-bottom: var(--space-md);
    border-bottom: 1px solid var(--border-light); /* Lighter border */
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
    width: 36px; /* Consistent size */
    height: 36px; /* Consistent size */
    font-size: var(--font-size-md); /* Consistent font size */
    border-radius: var(--radius-full);
    background-color: var(--danger); /* Explicit danger color */
    color: white; /* White icon */
    box-shadow: var(--shadow-sm); /* Subtle shadow */
    transition: var(--transition-all);
  }

  .remove-item-btn:hover {
    background-color: var(--danger-dark); /* Darker danger on hover */
    box-shadow: var(--shadow-md); /* Slightly more prominent shadow */
    transform: translateY(-1px);
  }

  .remove-item-btn i {
    font-size: 1em;
  }

  .btn-full-width {
    width: 100%;
  }

  /* Action Buttons at bottom */
  .form-actions-sticky {
    position: sticky;
    bottom: 0;
    left: 0;
    width: 100%;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: var(--space-md) var(--space-xl); /* Consistent padding */
    background-color: var(--bg-card); /* Consistent background */
    border-top: 1px solid var(--border-light); /* Consistent border */
    box-shadow: var(--shadow-sm); /* Consistent shadow */
    z-index: var(--z-sticky);
    gap: var(--space-md);
  }

  /* Font Awesome Icon spacing */
  .icon {
    margin-right: var(--space-xs);
  }

  .btn-with-icon .icon {
    margin-right: var(--space-xs);
  }

  /* Responsive */
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
      margin-left: 0 !important;
    }

    .remove-item-btn {
      position: static;
      width: 100%;
      margin-top: var(--space-md);
      border-radius: var(--radius); /* Consistent radius for full-width */
    }
  }

  /* Dark Theme Overrides - Adjusted to use global variables where possible */
  body.dark-theme .form-container,
  body.dark-theme .form-section {
    background-color: var(--bg-card);
    border-color: var(--border);
    box-shadow: var(--shadow-md);
  }

  body.dark-theme .section-title,
  body.dark-theme .form-section-title {
    color: var(--text-primary);
  }

  body.dark-theme .section-description {
    color: var(--text-secondary);
  }

  body.dark-theme .loading-spinner {
    color: var(--primary);
  }

  body.dark-theme .message.error {
    background-color: var(--danger-dark);
    color: var(--danger-light);
    border-color: var(--danger);
  }

  body.dark-theme .form-section-title {
    border-bottom-color: var(--border-light);
  }

  body.dark-theme .form-actions-sticky {
    background-color: var(--bg-card);
    border-top-color: var(--border-light);
    box-shadow: var(--shadow-sm);
  }
</style> 
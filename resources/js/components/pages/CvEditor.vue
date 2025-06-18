<template>
  <main class="main">
    <div class="page-container animate-in">
      <!-- Header -->
      <section class="homepage-hero reveal-on-scroll">
        <div class="hero-bg-decoration" aria-hidden="true"></div>
        <h2 class="reveal-on-scroll">
          {{ isEditing ? 'Redakto CV' : 'Krijo CV të re' }}
        </h2>
        <p class="reveal-on-scroll" data-reveal-delay="100">
          Plotësoni informacionin e mëposhtëm për të ndërtuar CV-në tuaj.
        </p>
      </section>

      <!-- Main Editor and Preview Layout -->
      <section class="cv-editor-layout-section homepage-value-prop">
        <div class="value-prop-container glassmorphic-card">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Editor Column (2/3 width on large screens) -->
            <div class="lg:col-span-2 bg-neutral-light dark:bg-dark-neutral-container p-6 rounded-lg shadow-light">
              <!-- Step Navigation (Progress Bar) -->
              <div
                class="mb-6 flex flex-col md:flex-row justify-between items-stretch md:items-center text-sm font-medium text-muted-text dark:text-dark-muted-text"
              >
                <div
                  v-for="(stepName, index) in stepNames"
                  :key="index"
                  @click="goToStep(index)"
                  :class="[
                    'flex-1 text-center py-3 px-4 relative cursor-pointer transition-all duration-300 rounded-full',
                    index === currentStep
                      ? 'bg-primary-hover text-white dark:bg-dark-primary-hover dark:text-dark-text-light font-semibold shadow-md'
                      : 'text-muted-text dark:text-dark-muted-text hover:bg-neutral-hover dark:hover:bg-dark-neutral-hover hover:text-primary dark:hover:text-dark-primary-hover',
                    index < currentStep
                      ? 'bg-success text-white dark:bg-success-darker dark:text-dark-text-light'
                      : '',
                  ]"
                >
                  <span class="block z-10 relative">{{ stepName }}</span>
                  <div
                    class="absolute inset-0 rounded-full"
                    :class="[
                      index === currentStep
                        ? 'bg-primary dark:bg-dark-primary scale-x-100'
                        : 'scale-x-0',
                      'transform origin-left transition-transform duration-300 ease-out',
                    ]"
                  ></div>
                </div>
              </div>

              <form @submit.prevent="nextStep" class="space-y-6">
                <!-- Step 0: CV Title and Personal Information -->
                <div v-if="currentStep === 0" class="animate-fade-in-up">
                  <div class="form-group--floating">
                    <input
                      type="text"
                      id="cv-title"
                      v-model="cv.title"
                      class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm"
                      placeholder="P.sh. CV për Programer"
                    />
                    <label
                      for="cv-title"
                      class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                      >Titulli i CV-së</label
                    >
                  </div>
                  <div>
                    <h3
                      class="text-lg font-semibold text-neutral-text dark:text-dark-neutral-text mt-6 mb-3"
                    >
                      Informacione Personale
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div class="form-group--floating">
                        <input
                          type="text"
                          id="first-name"
                          v-model="cv.personalInfo.firstName"
                          class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm"
                          placeholder=" "
                        />
                        <label
                          for="first-name"
                          class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                          >Emri</label
                        >
                      </div>
                      <div class="form-group--floating">
                        <input
                          type="text"
                          id="last-name"
                          v-model="cv.personalInfo.lastName"
                          class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm"
                          placeholder=" "
                        />
                        <label
                          for="last-name"
                          class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                          >Mbiemri</label
                        >
                      </div>
                      <div class="form-group--floating">
                        <input
                          type="email"
                          id="email"
                          v-model="cv.personalInfo.email"
                          class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm"
                          placeholder=" "
                        />
                        <label
                          for="email"
                          class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                          >Email</label
                        >
                      </div>
                      <div class="form-group--floating">
                        <input
                          type="tel"
                          id="phone"
                          v-model="cv.personalInfo.phone"
                          class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm"
                          placeholder=" "
                        />
                        <label
                          for="phone"
                          class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                          >Telefoni</label
                        >
                      </div>
                      <div class="md:col-span-2 form-group--floating">
                        <input
                          type="text"
                          id="address"
                          v-model="cv.personalInfo.address"
                          class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm"
                          placeholder=" "
                        />
                        <label
                          for="address"
                          class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                          >Adresa</label
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Step 1: Experience -->
                <div v-if="currentStep === 1" class="animate-fade-in-up">
                  <h3 class="text-lg font-semibold text-neutral-text dark:text-dark-neutral-text mb-3">
                    Përvoja e Punës
                  </h3>
                  <div
                    v-for="(exp, index) in cv.experience"
                    :key="index"
                    class="mb-4 p-4 border border-neutral-border dark:border-dark-neutral-border rounded-md space-y-3 glassmorphic-card"
                  >
                    <div class="form-group--floating">
                      <input
                        type="text"
                        :id="`job-title-${index}`"
                        v-model="exp.title"
                        class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm"
                        placeholder=" "
                      />
                      <label
                        :for="`job-title-${index}`"
                        class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                        >Titulli i Pozicionit</label
                      >
                    </div>
                    <div class="form-group--floating">
                      <input
                        type="text"
                        :id="`company-${index}`"
                        v-model="exp.company"
                        class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm"
                        placeholder=" "
                      />
                      <label
                        :for="`company-${index}`"
                        class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                        >Kompania</label
                      >
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div class="form-group--floating">
                        <input
                          type="date"
                          :id="`start-date-${index}`"
                          v-model="exp.startDate"
                          class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-sm"
                          placeholder=" "
                        />
                        <label
                          :for="`start-date-${index}`"
                          class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                          >Data e Fillimit</label
                        >
                      </div>
                      <div class="form-group--floating">
                        <input
                          type="date"
                          :id="`end-date-${index}`"
                          v-model="exp.endDate"
                          class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-sm"
                          placeholder=" "
                        />
                        <label
                          :for="`end-date-${index}`"
                          class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                          >Data e Mbarimit</label
                        >
                      </div>
                    </div>
                    <div class="form-group--floating">
                      <textarea
                        :id="`description-${index}`"
                        v-model="exp.description"
                        rows="3"
                        class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm"
                        placeholder=" "
                      ></textarea>
                      <label
                        :for="`description-${index}`"
                        class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                        >Përshkrimi i Detyrave</label
                      >
                    </div>
                    <div class="flex justify-end">
                      <button
                        type="button"
                        @click="removeExperience(index)"
                        class="btn-text-danger text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-600 text-sm"
                      >
                        Fshij Përvojën
                      </button>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="addExperience"
                    class="btn-secondary w-full py-2 mt-4 text-primary dark:text-primary-accent border border-primary dark:border-primary-accent hover:bg-primary-hover hover:text-white dark:hover:bg-primary-hover-dark dark:hover:text-white transition-colors duration-300"
                  >
                    <i class="fas fa-plus mr-2"></i>Shto Përvojë
                  </button>
                </div>

                <!-- Step 2: Education -->
                <div v-if="currentStep === 2" class="animate-fade-in-up">
                  <h3 class="text-lg font-semibold text-neutral-text dark:text-dark-neutral-text mb-3">
                    Edukimi
                  </h3>
                  <div
                    v-for="(edu, index) in cv.education"
                    :key="index"
                    class="mb-4 p-4 border border-neutral-border dark:border-dark-neutral-border rounded-md space-y-3 glassmorphic-card"
                  >
                    <div class="form-group--floating">
                      <input
                        type="text"
                        :id="`degree-${index}`"
                        v-model="edu.degree"
                        class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm"
                        placeholder=" "
                      />
                      <label
                        :for="`degree-${index}`"
                        class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                        >Titulli/Diploma</label
                      >
                    </div>
                    <div class="form-group--floating">
                      <input
                        type="text"
                        :id="`university-${index}`"
                        v-model="edu.institution"
                        class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm"
                        placeholder=" "
                      />
                      <label
                        :for="`university-${index}`"
                        class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                        >Universiteti/Institucioni</label
                      >
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div class="form-group--floating">
                        <input
                          type="date"
                          :id="`education-start-date-${index}`"
                          v-model="edu.startDate"
                          class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-sm"
                          placeholder=" "
                        />
                        <label
                          :for="`education-start-date-${index}`"
                          class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                          >Data e Fillimit</label
                        >
                      </div>
                      <div class="form-group--floating">
                        <input
                          type="date"
                          :id="`education-end-date-${index}`"
                          v-model="edu.endDate"
                          class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary text-sm"
                          placeholder=" "
                        />
                        <label
                          :for="`education-end-date-${index}`"
                          class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                          >Data e Mbarimit</label
                        >
                      </div>
                    </div>
                    <div class="flex justify-end">
                      <button
                        type="button"
                        @click="removeEducation(index)"
                        class="btn-text-danger text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-600 text-sm"
                      >
                        Fshij Edukimin
                      </button>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="addEducation"
                    class="btn-secondary w-full py-2 mt-4 text-primary dark:text-primary-accent border border-primary dark:border-primary-accent hover:bg-primary-hover hover:text-white dark:hover:bg-primary-hover-dark dark:hover:text-white transition-colors duration-300"
                  >
                    <i class="fas fa-plus mr-2"></i>Shto Edukim
                  </button>
                </div>

                <!-- Step 3: Skills -->
                <div v-if="currentStep === 3" class="animate-fade-in-up">
                  <h3 class="text-lg font-semibold text-neutral-text dark:text-dark-neutral-text mb-3">
                    Aftësitë
                  </h3>
                  <div
                    v-for="(skill, index) in cv.skills"
                    :key="index"
                    class="mb-4 p-4 border border-neutral-border dark:border-dark-neutral-border rounded-md space-y-3 glassmorphic-card"
                  >
                    <div class="form-group--floating">
                      <input
                        type="text"
                        :id="`skill-name-${index}`"
                        v-model="skill.name"
                        class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm"
                        placeholder=" "
                      />
                      <label
                        :for="`skill-name-${index}`"
                        class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                        >Emri i Aftësisë</label
                      >
                    </div>
                    <div class="form-group--floating">
                      <input
                        type="range"
                        :id="`skill-level-${index}`"
                        v-model.number="skill.level"
                        min="0"
                        max="5"
                        step="1"
                        class="mt-1 block w-full h-2 bg-neutral-border dark:bg-dark-neutral-border rounded-lg appearance-none cursor-pointer range-slider"
                      />
                      <label
                        :for="`skill-level-${index}`"
                        class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                        >Niveli i Aftësisë: {{ getSkillLevelText(skill.level) }}</label
                      >
                      <div class="flex justify-between text-xs text-muted-text dark:text-dark-muted-text mt-1">
                        <span>Në fillim</span>
                        <span>Ekspert</span>
                      </div>
                    </div>
                    <div class="flex justify-end">
                      <button
                        type="button"
                        @click="removeSkill(index)"
                        class="btn-text-danger text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-600 text-sm"
                      >
                        Fshij Aftësinë
                      </button>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="addSkill"
                    class="btn-secondary w-full py-2 mt-4 text-primary dark:text-primary-accent border border-primary dark:border-primary-accent hover:bg-primary-hover hover:text-white dark:hover:bg-primary-hover-dark dark:hover:text-white transition-colors duration-300"
                  >
                    <i class="fas fa-plus mr-2"></i>Shto Aftësi
                  </button>
                </div>

                <!-- Step 4: Interests -->
                <div v-if="currentStep === 4" class="animate-fade-in-up">
                  <h3 class="text-lg font-semibold text-neutral-text dark:text-dark-neutral-text mb-3">
                    Interesat
                  </h3>
                  <div
                    v-for="(interest, index) in cv.interests"
                    :key="index"
                    class="mb-4 p-4 border border-neutral-border dark:border-dark-neutral-border rounded-md space-y-3 glassmorphic-card"
                  >
                    <div class="form-group--floating">
                      <input
                        type="text"
                        :id="`interest-${index}`"
                        v-model="interest.name"
                        class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm"
                        placeholder=" "
                      />
                      <label
                        :for="`interest-${index}`"
                        class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                        >Emri i Interesit</label
                      >
                    </div>
                    <div class="flex justify-end">
                      <button
                        type="button"
                        @click="removeInterest(index)"
                        class="btn-text-danger text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-600 text-sm"
                      >
                        Fshij Interesin
                      </button>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="addInterest"
                    class="btn-secondary w-full py-2 mt-4 text-primary dark:text-primary-accent border border-primary dark:border-primary-accent hover:bg-primary-hover hover:text-white dark:hover:bg-primary-hover-dark dark:hover:text-white transition-colors duration-300"
                  >
                    <i class="fas fa-plus mr-2"></i>Shto Interes
                  </button>
                </div>

                <!-- Step 5: Summary -->
                <div v-if="currentStep === 5" class="animate-fade-in-up">
                  <h3 class="text-lg font-semibold text-neutral-text dark:text-dark-neutral-text mb-3">
                    Përmbledhja Personale
                  </h3>
                  <div class="form-group--floating">
                    <textarea
                      id="summary"
                      v-model="cv.summary"
                      rows="6"
                      class="mt-1 block w-full px-3 py-2 bg-neutral-light dark:bg-dark-neutral-bg border border-neutral-border dark:border-dark-neutral-border rounded-md shadow-sm placeholder-muted-text dark:placeholder-dark-muted-text focus:outline-none focus:ring-primary focus:border-primary text-sm"
                      placeholder=" "
                    ></textarea>
                    <label
                      for="summary"
                      class="block text-sm font-medium text-neutral-text dark:text-dark-neutral-text"
                      >Shkruani një përmbledhje të shkurtër për veten tuaj</label
                    >
                  </div>
                </div>

                <!-- Step 6: Finalization -->
                <div v-if="currentStep === 6" class="animate-fade-in-up">
                  <h3 class="text-lg font-semibold text-neutral-text dark:text-dark-neutral-text mb-3">
                    Finalizimi
                  </h3>
                  <p class="text-muted-text dark:text-dark-muted-text mb-4">
                    Pothuajse keni mbaruar! Rishikoni CV-në tuaj dhe klikoni "Ruaj" për të përfunduar.
                  </p>
                  <!-- Add any final review elements here if needed -->
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between items-center mt-6">
                  <button
                    type="button"
                    @click="prevStep"
                    v-if="currentStep > 0"
                    class="btn-secondary px-6 py-2 rounded-md transition-all duration-300 ease-in-out transform hover:scale-105"
                  >
                    <i class="fas fa-arrow-left mr-2"></i>Mbrapa
                  </button>
                  <div class="flex-grow"></div>
                  <span v-if="isSaving" class="text-sm text-primary dark:text-dark-primary mr-4 animate-pulse">
                    Ruajtja...
                  </span>
                  <button
                    type="submit"
                    class="btn-primary px-6 py-2 rounded-md transition-all duration-300 ease-in-out transform hover:scale-105"
                  >
                    {{ currentStep === stepNames.length - 1 ? 'Ruaj CV' : 'Vazhdo' }}
                    <i class="fas fa-arrow-right ml-2"></i>
                  </button>
                </div>

                <!-- Undo/Redo Buttons -->
                <div class="flex justify-center mt-4 space-x-4">
                  <button
                    type="button"
                    @click="undo"
                    :disabled="!canUndo"
                    class="btn-outline px-4 py-2 rounded-md transition-all duration-300 ease-in-out transform hover:scale-105"
                    :class="{ 'opacity-50 cursor-not-allowed': !canUndo }"
                  >
                    <i class="fas fa-undo mr-2"></i>Undo
                  </button>
                  <button
                    type="button"
                    @click="redo"
                    :disabled="!canRedo"
                    class="btn-outline px-4 py-2 rounded-md transition-all duration-300 ease-in-out transform hover:scale-105"
                    :class="{ 'opacity-50 cursor-not-allowed': !canRedo }"
                  >
                    <i class="fas fa-redo mr-2"></i>Redo
                  </button>
                </div>
              </form>
            </div>

            <!-- CV Preview Column (1/3 width on large screens) -->
            <div class="lg:col-span-1">
              <CvPreviewCard :cv="cv" class="reveal-on-scroll h-full" />
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import CvPreviewCard from "../components/CvPreviewCard.vue"; // Import the CvPreviewCard component
import { usePage } from "@inertiajs/vue3";
import { useDebounceFn } from "@vueuse/core";

// Define the structure for a new CV
const getDefaultCv = () => ({
  id: null,
  title: "Untitled CV", // Default title
  personalInfo: {
    firstName: "",
    lastName: "",
    email: "",
    phone: "",
    address: "",
  },
  experience: [
    {
      title: "Job Title",
      company: "Company Name",
      startDate: "",
      endDate: "",
      description: "Key responsibilities and achievements.",
    },
  ],
  education: [
    {
      degree: "Degree/Major",
      institution: "University/Institution",
      startDate: "",
      endDate: "",
      description: "Coursework, honors, or thesis details.",
    },
  ],
  skills: [{ name: "Skill Name", level: 3 }], // Default skill with a mid-level
  interests: [{ name: "Interest" }],
  summary: "A brief and compelling summary of your professional background and career goals.",
});

const { props } = usePage();
const cv = ref(props.cv ? props.cv.data : getDefaultCv());
const isEditing = ref(!!props.cv);
const currentStep = ref(0);
const isSaving = ref(false);
const saveTimeout = ref(null);
const saveSuccessTimeout = ref(null);
const isUndoingRedoing = ref(false); // Flag to prevent saving when undo/redo is active

// Undo/Redo specific refs
const history = ref([JSON.parse(JSON.stringify(cv.value))]); // Initialize history with current CV state
const historyPointer = ref(0); // Points to the current state in history

const debouncedSaveCv = useDebounceFn(async () => {
  if (isUndoingRedoing.value) {
    isUndoingRedoing.value = false;
    return;
  }
  isSaving.value = true;
  try {
    const url = isEditing.value ? `/api/cvs/${cv.value.id}` : "/api/cvs";
    const method = isEditing.value ? "put" : "post";

    const dataToSave = {
      title: cv.value.title,
      personalDetails: cv.value.personalInfo,
      experience: cv.value.experience,
      education: cv.value.education.map(edu => ({
        degree: edu.degree,
        university: edu.institution,
        startDate: edu.startDate,
        endDate: edu.endDate,
        description: edu.description,
      })),
      skills: cv.value.skills,
      interests: cv.value.interests,
      summary: cv.value.summary,
      selectedTemplate: cv.value.selectedTemplate,
    };

    const response = await axios[method](url, dataToSave, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
      }
    });

    if (!isEditing.value && response.data.cv && response.data.cv.id) {
      cv.value.id = response.data.cv.id;
      isEditing.value = true;
    }
    isSaving.value = false;
    clearTimeout(saveSuccessTimeout.value);

    if (historyPointer.value < history.value.length - 1) {
      history.value = history.value.slice(0, historyPointer.value + 1);
    }
    history.value.push(JSON.parse(JSON.stringify(cv.value)));
    historyPointer.value = history.value.length - 1;

  } catch (error) {
    console.error("Error saving CV:", error);
    isSaving.value = false;
  }
}, 1000);

// Watch for changes in cv and trigger debounced save
watch(
  cv,
  () => {
    if (!isUndoingRedoing.value) { // Only save if not an undo/redo operation
      debouncedSaveCv();
    }
  },
  { deep: true }
);

const stepNames = [
  "Informacione Personale",
  "Përvoja e Punës",
  "Edukimi",
  "Aftësitë",
  "Interesat",
  "Përmbledhja",
  "Finalizimi",
];

const nextStep = () => {
  if (currentStep.value < stepNames.length - 1) {
    currentStep.value++;
  } else {
    saveCv();
  }
};

const prevStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--;
  }
};

const goToStep = (index) => {
  currentStep.value = index;
};

const saveCv = async () => {
  // This function is now mainly for explicit manual saving or final save
  // Auto-save handles most of the saving
  isSaving.value = true;
  try {
    const url = isEditing.value ? `/api/cvs/${cv.value.id}` : "/api/cvs";
    const method = isEditing.value ? "put" : "post";

    const dataToSave = {
      title: cv.value.title,
      personalDetails: cv.value.personalInfo,
      experience: cv.value.experience,
      education: cv.value.education.map(edu => ({
        degree: edu.degree,
        university: edu.institution,
        startDate: edu.startDate,
        endDate: edu.endDate,
        description: edu.description,
      })),
      skills: cv.value.skills,
      interests: cv.value.interests,
      summary: cv.value.summary,
      selectedTemplate: cv.value.selectedTemplate,
    };

    const response = await axios[method](url, dataToSave, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
      }
    });

    if (!isEditing.value && response.data.cv && response.data.cv.id) {
      cv.value.id = response.data.cv.id;
      isEditing.value = true;
    }
    isSaving.value = false;
    clearTimeout(saveSuccessTimeout.value);

    if (historyPointer.value < history.value.length - 1) {
      history.value = history.value.slice(0, historyPointer.value + 1);
    }
    history.value.push(JSON.parse(JSON.stringify(cv.value)));
    historyPointer.value = history.value.length - 1;

  } catch (error) {
    console.error("Error saving CV:", error);
    isSaving.value = false;
  }
};

// Undo functionality
const undo = () => {
  if (historyPointer.value > 0) {
    isUndoingRedoing.value = true;
    historyPointer.value--;
    cv.value = JSON.parse(JSON.stringify(history.value[historyPointer.value]));
  }
};

// Redo functionality
const redo = () => {
  if (historyPointer.value < history.value.length - 1) {
    isUndoingRedoing.value = true;
    historyPointer.value++;
    cv.value = JSON.parse(JSON.stringify(history.value[historyPointer.value]));
  }
};

const addExperience = () => {
  cv.value.experience.push({
    title: "",
    company: "",
    startDate: "",
    endDate: "",
    description: "",
  });
};

const removeExperience = (index) => {
  cv.value.experience.splice(index, 1);
};

const addEducation = () => {
  cv.value.education.push({
    degree: "",
    institution: "",
    startDate: "",
    endDate: "",
    description: "",
  });
};

const removeEducation = (index) => {
  cv.value.education.splice(index, 1);
};

const addSkill = () => {
  cv.value.skills.push({ name: "", level: 0 });
};

const removeSkill = (index) => {
  cv.value.skills.splice(index, 1);
};

// Helper to convert skill level number to descriptive text
const getSkillLevelText = (level) => {
  switch (level) {
    case 0:
      return "Në fillim";
    case 1:
      return "Elementar";
    case 2:
      return "Mesatar";
    case 3:
      return "I mirë";
    case 4:
      return "Shumë i mirë";
    case 5:
      return "Ekspert";
    default:
      return "Në fillim";
  }
};

const addInterest = () => {
  cv.value.interests.push({ name: "" });
};

const removeInterest = (index) => {
  cv.value.interests.splice(index, 1);
};
</script>

<style scoped>
/* Add any component-specific styles here if necessary */
/* This is currently empty, but can be used for custom styles that don't fit global or layout.css */
</style>
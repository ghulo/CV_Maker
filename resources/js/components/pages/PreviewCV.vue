<template>
  <div class="bg-neutral-bg dark:bg-dark-neutral-bg min-h-screen p-4 sm:p-6 lg:p-8">
    <div class="max-w-4xl mx-auto bg-neutral-light dark:bg-dark-neutral-container shadow-lg rounded-lg overflow-hidden glassmorphic-card">
      <div v-if="loading" class="p-8 text-center">
        <p class="text-muted-text dark:text-dark-muted-text">Duke ngarkuar CV-në...</p>
      </div>
      <div v-else-if="error" class="p-8 text-center text-tertiary-accent">
        <p>{{ error }}</p>
      </div>
      <div v-else class="cv-content p-8 sm:p-10 lg:p-12 text-neutral-text dark:text-dark-neutral-text">
        <!-- Header: Personal Details -->
        <header class="text-center mb-10 border-b border-divider-color dark:border-dark-divider-color pb-6">
          <h1 class="text-4xl font-bold text-neutral-text dark:text-dark-neutral-text">{{ cv.personal_details.full_name }}</h1>
          <div class="flex justify-center items-center space-x-6 mt-3 text-muted-text dark:text-dark-muted-text">
            <span>{{ cv.personal_details.email }}</span>
            <span>•</span>
            <span>{{ cv.personal_details.phone_number }}</span>
            <span>•</span>
            <span>{{ cv.personal_details.address }}</span>
          </div>
        </header>

        <!-- Main Content -->
        <main class="space-y-8">
          <!-- Summary Section -->
          <section class="glassmorphic-card">
            <h2 class="text-2xl font-bold text-neutral-text dark:text-dark-neutral-text border-b-2 border-primary dark:border-dark-primary pb-2 mb-6">Përmbledhja Profesionale</h2>
            <p class="text-subtle-text dark:text-dark-subtle-text leading-relaxed">{{ cv.summary }}</p>
          </section>

          <!-- Work Experience Section -->
          <section class="glassmorphic-card">
            <h2 class="text-2xl font-bold text-neutral-text dark:text-dark-neutral-text border-b-2 border-primary dark:border-dark-primary pb-2 mb-6">Përvoja e Punës</h2>
            <div v-for="(exp, index) in cv.experiences" :key="index" class="mb-6">
              <h3 class="text-xl font-semibold text-neutral-text dark:text-dark-neutral-text">{{ exp.job_title }}</h3>
              <p class="text-md text-muted-text dark:text-dark-muted-text font-medium">{{ exp.company }} | {{ formatDate(exp.start_date) }} - {{ exp.end_date ? formatDate(exp.end_date) : 'Tani' }}</p>
              <p class="text-subtle-text dark:text-dark-subtle-text mt-2">{{ exp.description }}</p>
            </div>
          </section>

          <!-- Education Section -->
          <section class="glassmorphic-card">
            <h2 class="text-2xl font-bold text-neutral-text dark:text-dark-neutral-text border-b-2 border-primary dark:border-dark-primary pb-2 mb-6">Edukimi</h2>
            <div v-for="(edu, index) in cv.educations" :key="index" class="mb-4">
              <h3 class="text-xl font-semibold text-neutral-text dark:text-dark-neutral-text">{{ edu.degree }}</h3>
              <p class="text-md text-muted-text dark:text-dark-muted-text font-medium">{{ edu.institution }} | {{ formatDate(edu.start_date) }} - {{ edu.end_date ? formatDate(edu.end_date) : 'Tani' }}</p>
            </div>
          </section>

          <!-- Skills Section -->
          <section class="glassmorphic-card">
            <h2 class="text-2xl font-bold text-neutral-text dark:text-dark-neutral-text border-b-2 border-primary dark:border-dark-primary pb-2 mb-6">Aftësitë</h2>
            <div class="flex flex-wrap gap-2">
              <span v-for="(skill, index) in cv.skills" :key="index" class="bg-primary/10 text-primary dark:bg-dark-primary/20 dark:text-dark-primary text-sm font-medium px-3 py-1 rounded-full">
                {{ skill.name }}
              </span>
            </div>
          </section>
        </main>
      </div>
    </div>
    <div class="max-w-4xl mx-auto mt-6 flex justify-end">
      <button @click="goBack" class="bg-muted-text hover:bg-neutral-text text-white font-bold py-2 px-4 rounded-lg mr-4 transition-colors duration-300">
        Kthehu Pas
      </button>
      <button @click="downloadCv" class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300">
        <i class="fas fa-download mr-2"></i>Shkarko si PDF
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const cvId = route.params.id;

const cv = ref(null);
const loading = ref(true);
const error = ref(null);

const fetchCv = async () => {
  try {
    const response = await axios.get(`/api/cvs/${cvId}`);
    if (response.data.success) {
      cv.value = response.data.cv;
    } else {
      error.value = response.data.message || 'CV nuk u gjet.';
    }
  } catch (err) {
    error.value = 'Gabim gjatë ngarkimit të CV-së. Ju lutem provoni përsëri.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const options = { year: 'numeric', month: 'long' };
  return new Date(dateString).toLocaleDateString('sq-AL', options);
};

const goBack = () => {
  router.back();
}

const downloadCv = () => {
  const downloadUrl = `/api/cvs/${cvId}/download`;
  window.open(downloadUrl, '_blank');
}

onMounted(() => {
  fetchCv();
});
</script>

<style scoped>
@reference "tailwindcss/theme";
</style>

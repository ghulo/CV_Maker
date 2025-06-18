<template>
  <div
    class="professional-cv-content p-8 sm:p-10 lg:p-12 text-gray-800 dark:text-gray-200 bg-white dark:bg-dark-neutral-bg shadow-lg rounded-lg"
  >
    <!-- Header: Name and Summary -->
    <header class="text-center mb-10 pb-6 border-b border-gray-200 dark:border-gray-700">
      <h1 class="text-4xl sm:text-5xl font-extrabold text-primary-600 dark:text-primary-400 mb-2">
        {{ cv.personal_details.full_name }}
      </h1>
      <p class="text-lg text-gray-700 dark:text-gray-300 mb-4">{{ cv.summary }}</p>
      <div
        class="flex flex-wrap justify-center items-center space-x-4 text-sm text-gray-600 dark:text-gray-400 mt-3"
      >
        <span v-if="cv.personal_details.email" class="flex items-center"
          ><i class="fas fa-envelope mr-1"></i> {{ cv.personal_details.email }}</span
        >
        <span v-if="cv.personal_details.phone_number" class="flex items-center"
          ><i class="fas fa-phone mr-1"></i> {{ cv.personal_details.phone_number }}</span
        >
        <span v-if="cv.personal_details.address" class="flex items-center"
          ><i class="fas fa-map-marker-alt mr-1"></i> {{ cv.personal_details.address }}</span
        >
      </div>
    </header>

    <!-- Main Content in two columns -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <!-- Left Column: Skills, Education -->
      <div class="md:col-span-1">
        <section class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-sm">
          <h2
            class="text-xl font-bold text-gray-800 dark:text-gray-200 border-b-2 border-primary-500 dark:border-primary-400 pb-2 mb-4 flex items-center"
          >
            <i class="fas fa-lightbulb mr-3 text-primary-500 dark:text-primary-400"></i> Aftësitë
          </h2>
          <ul class="list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300 text-base">
            <li v-for="(skill, index) in cv.skills" :key="index">
              {{ skill.name }}
              <span v-if="skill.level" class="text-sm opacity-80">({{ skill.level }})</span>
            </li>
          </ul>
        </section>

        <section class="p-6 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-sm">
          <h2
            class="text-xl font-bold text-gray-800 dark:text-gray-200 border-b-2 border-primary-500 dark:border-primary-400 pb-2 mb-4 flex items-center"
          >
            <i class="fas fa-graduation-cap mr-3 text-primary-500 dark:text-primary-400"></i>
            Edukimi
          </h2>
          <div
            v-for="(edu, index) in cv.educations"
            :key="index"
            class="mb-4 last:mb-0 pb-3 last:pb-0 border-b last:border-b-0 border-gray-200 dark:border-gray-700"
          >
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ edu.degree }}</h3>
            <p class="text-md text-gray-600 dark:text-gray-400 font-medium mt-1">
              {{ edu.institution }} | {{ formatDate(edu.start_date) }} -
              {{ edu.end_date ? formatDate(edu.end_date) : 'Tani' }}
            </p>
          </div>
        </section>
      </div>

      <!-- Right Column: Work Experience -->
      <div class="md:col-span-2">
        <section class="p-6 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-sm">
          <h2
            class="text-xl font-bold text-gray-800 dark:text-gray-200 border-b-2 border-primary-500 dark:border-primary-400 pb-2 mb-4 flex items-center"
          >
            <i class="fas fa-briefcase mr-3 text-primary-500 dark:text-primary-400"></i> Përvoja e
            Punës
          </h2>
          <div
            v-for="(exp, index) in cv.work_experiences"
            :key="index"
            class="mb-6 last:mb-0 pb-4 last:pb-0 border-b last:border-b-0 border-gray-200 dark:border-gray-700"
          >
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ exp.job_title }}</h3>
            <p class="text-md text-gray-600 dark:text-gray-400 font-medium mt-1">
              {{ exp.company }} | {{ formatDate(exp.start_date) }} -
              {{ exp.end_date ? formatDate(exp.end_date) : 'Tani' }}
            </p>
            <p class="text-gray-700 dark:text-gray-300 mt-2 text-base">{{ exp.description }}</p>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { defineProps } from 'vue'

  const props = defineProps({
    cv: {
      type: Object,
      required: true,
    },
  })

  const formatDate = (dateString) => {
    if (!dateString) return ''
    const options = { year: 'numeric', month: 'long' }
    return new Date(dateString).toLocaleDateString('sq-AL', options)
  }
</script>

<style scoped>
  /* Add any specific styles for the professional template here */
</style>

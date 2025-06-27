<template>
  <div
    class="modern-cv-content p-8 sm:p-10 lg:p-12 text-neutral-text bg-card-bg shadow-elevated rounded-lg flex flex-col md:flex-row"
  >
    <!-- Left Column: Personal Info, Skills, Contact -->
    <div
      class="modern-sidebar w-full md:w-1/3 pr-0 md:pr-8 mb-8 md:mb-0 border-b md:border-b-0 md:border-r border-color"
    >
      <h1 class="text-3xl sm:text-4xl font-extrabold text-primary-text mb-4">
        {{ cv.personal_details.full_name }}
      </h1>
      <div class="space-y-2 text-sm text-muted-text">
        <p v-if="cv.personal_details.email" class="flex items-center">
          <i class="fas fa-envelope mr-2 text-accent-blue"></i>
          {{ cv.personal_details.email }}
        </p>
        <p v-if="cv.personal_details.phone_number" class="flex items-center">
          <i class="fas fa-phone mr-2 text-accent-blue"></i>
          {{ cv.personal_details.phone_number }}
        </p>
        <p v-if="cv.personal_details.address" class="flex items-center">
          <i class="fas fa-map-marker-alt mr-2 text-accent-blue"></i>
          {{ cv.personal_details.address }}
        </p>
      </div>

      <section class="mt-8 pt-4 border-t border-color">
        <h2
          class="text-xl font-bold text-primary-text border-b-2 border-accent-blue pb-2 mb-4 flex items-center"
        >
          <i class="fas fa-lightbulb mr-3 text-accent-blue"></i> Aftësitë
        </h2>
        <div class="flex flex-wrap" style="gap: 0.5rem;">
          <span
            v-for="(skill, index) in cv.skills"
            :key="index"
            class="bg-accent-blue-light text-accent-blue-darker text-sm font-medium px-4 py-1.5 rounded-full"
          >
            {{ skill.name }}
            <span v-if="skill.level" class="text-xs opacity-80">({{ skill.level }})</span>
          </span>
        </div>
      </section>
    </div>

    <!-- Right Column: Summary, Experience, Education -->
    <div class="modern-main-content w-full md:w-2/3 pl-0 md:pl-8">
      <section class="mb-8 pb-4 border-b border-color">
        <h2
          class="text-xl font-bold text-primary-text border-b-2 border-accent-blue pb-2 mb-4 flex items-center"
        >
          <i class="fas fa-user-tie mr-3 text-accent-blue"></i> Përmbledhja
          Profesionale
        </h2>
        <p class="text-muted-text leading-relaxed text-base">{{ cv.summary }}</p>
      </section>

      <section class="mb-8 pb-4 border-b border-color">
        <h2
          class="text-xl font-bold text-primary-text border-b-2 border-accent-blue pb-2 mb-4 flex items-center"
        >
          <i class="fas fa-briefcase mr-3 text-accent-blue"></i> Përvoja e
          Punës
        </h2>
        <div v-for="(exp, index) in cv.work_experiences" :key="index" class="mb-6 last:mb-0">
          <h3 class="text-lg font-semibold text-primary-text">{{ exp.job_title }}</h3>
          <p class="text-md text-secondary-text font-medium mt-1">
            {{ exp.company }} | {{ formatDate(exp.start_date) }} -
            {{ exp.end_date ? formatDate(exp.end_date) : 'Tani' }}
          </p>
          <p class="text-muted-text mt-2 text-sm">{{ exp.description }}</p>
        </div>
      </section>

      <section>
        <h2
          class="text-xl font-bold text-primary-text border-b-2 border-accent-blue pb-2 mb-4 flex items-center"
        >
          <i class="fas fa-graduation-cap mr-3 text-accent-blue"></i> Edukimi
        </h2>
        <div v-for="(edu, index) in cv.educations" :key="index" class="mb-4 last:mb-0">
          <h3 class="text-lg font-semibold text-primary-text">{{ edu.degree }}</h3>
          <p class="text-md text-secondary-text font-medium mt-1">
            {{ edu.institution }} | {{ formatDate(edu.start_date) }} -
            {{ edu.end_date ? formatDate(edu.end_date) : 'Tani' }}
          </p>
        </div>
      </section>
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
  /* Add any specific styles for the modern template here */
</style>

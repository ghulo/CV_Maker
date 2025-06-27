<template>
  <div
    class="cv-content p-8 sm:p-10 lg:p-12 text-neutral-text bg-card-bg shadow-elevated rounded-lg"
  >
    <!-- Header: Personal Details -->
    <header class="text-center mb-10 pb-6 border-b border-color">
      <h1 class="text-4xl sm:text-5xl font-extrabold text-primary-text mb-2">
        {{ cv.personal_details.full_name }}
      </h1>
      <div
        class="flex flex-col sm:flex-row justify-center items-center space-y-1 sm:space-y-0 sm:space-x-6 mt-3 text-muted-text text-sm"
      >
        <span v-if="cv.personal_details.email"
          ><i class="fas fa-envelope mr-1"></i> {{ cv.personal_details.email }}</span
        >
        <span v-if="cv.personal_details.phone_number"
          ><i class="fas fa-phone mr-1"></i> {{ cv.personal_details.phone_number }}</span
        >
        <span v-if="cv.personal_details.address"
          ><i class="fas fa-map-marker-alt mr-1"></i> {{ cv.personal_details.address }}</span
        >
      </div>
    </header>

    <!-- Main Content -->
    <main class="space-y-10">
      <!-- Summary Section -->
      <section class="bg-neutral-bg card p-6">
        <h2
          class="text-2xl font-bold text-primary-text border-b-2 border-accent-blue pb-2 mb-4 flex items-center"
        >
          <i class="fas fa-user-tie mr-3 text-accent-blue"></i> Përmbledhja
          Profesionale
        </h2>
        <p class="text-muted-text leading-relaxed text-base">{{ cv.summary }}</p>
      </section>

      <!-- Work Experience Section -->
      <section class="bg-neutral-bg card p-6">
        <h2
          class="text-2xl font-bold text-primary-text border-b-2 border-accent-blue pb-2 mb-4 flex items-center"
        >
          <i class="fas fa-briefcase mr-3 text-accent-blue"></i> Përvoja e
          Punës
        </h2>
        <div
          v-for="(exp, index) in cv.work_experiences"
          :key="index"
          class="mb-6 pb-4 last:mb-0 last:pb-0 border-b last:border-b-0 border-color"
        >
          <h3 class="text-xl font-semibold text-primary-text">{{ exp.job_title }}</h3>
          <p class="text-md text-secondary-text font-medium mt-1">
            {{ exp.company }} | {{ formatDate(exp.start_date) }} -
            {{ exp.end_date ? formatDate(exp.end_date) : 'Tani' }}
          </p>
          <p class="text-muted-text mt-2 text-sm">{{ exp.description }}</p>
        </div>
      </section>

      <!-- Education Section -->
      <section class="bg-neutral-bg card p-6">
        <h2
          class="text-2xl font-bold text-primary-text border-b-2 border-accent-blue pb-2 mb-4 flex items-center"
        >
          <i class="fas fa-graduation-cap mr-3 text-accent-blue"></i> Edukimi
        </h2>
        <div
          v-for="(edu, index) in cv.educations"
          :key="index"
          class="mb-4 pb-3 last:mb-0 last:pb-0 border-b last:border-b-0 border-color"
        >
          <h3 class="text-xl font-semibold text-primary-text">{{ edu.degree }}</h3>
          <p class="text-md text-secondary-text font-medium mt-1">
            {{ edu.institution }} | {{ formatDate(edu.start_date) }} -
            {{ edu.end_date ? formatDate(edu.end_date) : 'Tani' }}
          </p>
        </div>
      </section>

      <!-- Skills Section -->
      <section class="bg-neutral-bg card p-6">
        <h2
          class="text-2xl font-bold text-primary-text border-b-2 border-accent-blue pb-2 mb-4 flex items-center"
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
    </main>
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
  /* Tailwind utility classes are largely used directly in the template. */
  /* Any specific custom styles for the classic template can be added here. */
</style>

<template>
  <div class="professional-mini-cv-content p-4 text-neutral-text dark:text-dark-neutral-text border border-neutral-border dark:border-dark-neutral-border rounded-lg scale-75 origin-top-left overflow-hidden h-64 w-full relative">
    <!-- Header: Name and Summary (scaled) -->
    <header class="text-center mb-4 pb-2 border-b border-divider-color dark:border-dark-divider-color">
      <h3 class="text-lg font-bold text-primary dark:text-primary-accent mb-1">{{ cv.personal_details.full_name || 'Emri Mbiemri' }}</h3>
      <p class="text-xs text-muted-text dark:text-dark-muted-text line-clamp-1">{{ cv.summary || 'Përmbledhja Profesionale' }}</p>
    </header>

    <!-- Main Content in two columns (scaled) -->
    <div class="grid grid-cols-3 gap-4 h-full-minus-header">
      <!-- Left Column: Skills, Education (scaled) -->
      <div class="col-span-1 pr-2">
        <section class="mb-4">
          <h4 class="text-sm font-semibold text-neutral-text dark:text-dark-neutral-text border-b border-primary dark:border-dark-primary pb-1 mb-2">Aftësitë</h4>
          <ul class="list-disc list-inside space-y-0.5 text-xs">
            <li v-for="(skill, index) in (cv.skills || []).slice(0, 3)" :key="index" class="text-subtle-text dark:text-dark-subtle-text">
              {{ skill.name }}
            </li>
            <li v-if="cv.skills && cv.skills.length > 3" class="text-xs text-muted-text">...</li>
          </ul>
        </section>

        <section>
          <h4 class="text-sm font-semibold text-neutral-text dark:text-dark-neutral-text border-b border-primary dark:border-dark-primary pb-1 mb-2">Edukimi</h4>
          <div v-if="cv.educations && cv.educations.length">
            <p class="text-xs font-medium text-neutral-text dark:text-dark-neutral-text">{{ cv.educations[0].degree || 'Titulli' }}</p>
            <p class="text-xs text-muted-text dark:text-dark-muted-text">{{ cv.educations[0].institution || 'Universiteti' }}</p>
          </div>
          <p v-else class="text-xs text-muted-text dark:text-dark-muted-text">Nuk ka edukim.</p>
        </section>
      </div>

      <!-- Right Column: Work Experience (scaled) -->
      <div class="col-span-2 pl-2">
        <section>
          <h4 class="text-sm font-semibold text-neutral-text dark:text-dark-neutral-text border-b border-primary dark:border-dark-primary pb-1 mb-2">Përvoja</h4>
          <div v-if="cv.work_experiences && cv.work_experiences.length">
            <p class="text-xs font-medium text-neutral-text dark:text-dark-neutral-text">{{ cv.work_experiences[0].job_title || 'Titulli i Punës' }}</p>
            <p class="text-xs text-muted-text dark:text-dark-muted-text">{{ cv.work_experiences[0].company || 'Kompania' }}</p>
            <p class="text-xs text-subtle-text dark:text-dark-subtle-text line-clamp-2">{{ cv.work_experiences[0].description || 'Përshkrim i shkurtër detyrash.' }}</p>
          </div>
          <p v-else class="text-xs text-muted-text dark:text-dark-muted-text">Nuk ka përvojë.</p>
        </section>
      </div>
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-neutral-light dark:from-dark-neutral-container via-transparent to-transparent opacity-90"></div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  cv: {
    type: Object,
    required: true
  }
});
</script>

<style scoped>
.professional-mini-cv-content {
  transform-origin: top left;
  overflow: hidden;
}
.line-clamp-1 {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 1;
  overflow: hidden;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
  overflow: hidden;
}
.h-full-minus-header {
  height: calc(100% - 40px); /* Adjust based on actual header height */
}
</style> 
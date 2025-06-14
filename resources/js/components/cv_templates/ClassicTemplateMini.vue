<template>
  <div class="classic-mini-cv-content p-4 text-neutral-text dark:text-dark-neutral-text border border-neutral-border dark:border-dark-neutral-border rounded-lg scale-75 origin-top-left overflow-hidden h-64 w-full relative">
    <!-- Scaled down classic template layout -->
    <div class="relative h-full">
      <header class="text-center mb-4 pb-2 border-b border-divider-color dark:border-dark-divider-color">
        <h3 class="text-lg font-bold text-neutral-text dark:text-dark-neutral-text">{{ cv.personal_details.full_name || 'Emri Mbiemri' }}</h3>
        <p class="text-xs text-muted-text dark:text-dark-muted-text">{{ cv.personal_details.email || 'email@example.com' }}</p>
      </header>

      <main class="space-y-3">
        <section>
          <h4 class="text-sm font-semibold text-neutral-text dark:text-dark-neutral-text border-b border-primary dark:border-dark-primary pb-1 mb-2">Përmbledhja</h4>
          <p class="text-xs text-subtle-text dark:text-dark-subtle-text line-clamp-3">{{ cv.summary || 'Përshkrim i shkurtër i karrierës...' }}</p>
        </section>

        <section>
          <h4 class="text-sm font-semibold text-neutral-text dark:text-dark-neutral-text border-b border-primary dark:border-dark-primary pb-1 mb-2">Përvoja</h4>
          <div v-if="cv.work_experiences && cv.work_experiences.length">
            <p class="text-xs font-medium text-neutral-text dark:text-dark-neutral-text">{{ cv.work_experiences[0].job_title || 'Titulli i Punës' }}</p>
            <p class="text-xs text-muted-text dark:text-dark-muted-text">{{ cv.work_experiences[0].company || 'Kompania' }}</p>
          </div>
          <p v-else class="text-xs text-muted-text dark:text-dark-muted-text">Nuk ka përvojë të shtuar.</p>
        </section>

        <section>
          <h4 class="text-sm font-semibold text-neutral-text dark:text-dark-neutral-text border-b border-primary dark:border-dark-primary pb-1 mb-2">Edukimi</h4>
          <div v-if="cv.educations && cv.educations.length">
            <p class="text-xs font-medium text-neutral-text dark:text-dark-neutral-text">{{ cv.educations[0].degree || 'Titulli' }}</p>
            <p class="text-xs text-muted-text dark:text-dark-muted-text">{{ cv.educations[0].institution || 'Universiteti' }}</p>
          </div>
          <p v-else class="text-xs text-muted-text dark:text-dark-muted-text">Nuk ka edukim të shtuar.</p>
        </section>
      </main>
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
.classic-mini-cv-content {
  /* Adjust dimensions and scaling as needed */
  transform-origin: top left;
  /* Hide scrollbars but allow content to exist for scaling */
  overflow: hidden;
}
.line-clamp-3 {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
  overflow: hidden;
}
</style> 
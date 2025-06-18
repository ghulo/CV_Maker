<template>
  <div
    class="creative-mini-cv-content p-4 text-neutral-text dark:text-dark-neutral-text border border-neutral-border dark:border-dark-neutral-border rounded-lg scale-75 origin-top-left overflow-hidden h-64 w-full relative"
  >
    <!-- Scaled down creative template layout -->
    <div class="relative h-full">
      <div class="absolute top-0 left-0 w-full h-2 bg-primary-600 dark:bg-primary-500"></div>
      <header class="text-center mt-4 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
          {{ cv.personalDetails.fullName || 'Emri Mbiemri' }}
        </h3>
        <p class="text-xs text-primary-600 dark:text-primary-400 font-semibold">
          {{ cv.summary || 'Pozicioni' }}
        </p>
      </header>

      <main class="grid grid-cols-3 gap-2">
        <!-- Left Column: Skills (scaled) -->
        <div class="col-span-1 pr-1">
          <section class="mb-2">
            <h4
              class="text-sm font-semibold text-neutral-text dark:text-dark-neutral-text border-b border-primary dark:border-primary-400 pb-0.5 mb-1"
            >
              Aftësitë
            </h4>
            <ul class="list-none space-y-0.5 text-xs">
              <li v-for="(skill, index) in (cv.skills || []).slice(0, 2)" :key="index">
                {{ skill.name }}
              </li>
              <li v-if="cv.skills && cv.skills.length > 2" class="text-xs text-muted-text">...</li>
            </ul>
          </section>
        </div>

        <!-- Right Columns: Experience, Education (scaled) -->
        <div class="col-span-2 pl-1">
          <section class="mb-2">
            <h4
              class="text-sm font-semibold text-neutral-text dark:text-dark-neutral-text border-b border-primary dark:border-primary-400 pb-0.5 mb-1"
            >
              Përvoja
            </h4>
            <div v-if="cv.experience && cv.experience.length">
              <p class="text-xs font-medium text-neutral-text dark:text-dark-neutral-text">
                {{ cv.experience[0].title || 'Titulli i Punës' }}
              </p>
              <p class="text-xs text-muted-text dark:text-dark-muted-text">
                {{ cv.experience[0].company || 'Kompania' }}
              </p>
            </div>
            <p v-else class="text-xs text-muted-text dark:text-dark-muted-text">Nuk ka përvojë.</p>
          </section>

          <section>
            <h4
              class="text-sm font-semibold text-neutral-text dark:text-dark-neutral-text border-b border-primary dark:border-primary-400 pb-0.5 mb-1"
            >
              Edukimi
            </h4>
            <div v-if="cv.education && cv.education.length">
              <p class="text-xs font-medium text-neutral-text dark:text-dark-neutral-text">
                {{ cv.education[0].degree || 'Titulli' }}
              </p>
              <p class="text-xs text-muted-text dark:text-dark-muted-text">
                {{ cv.education[0].university || 'Universiteti' }}
              </p>
            </div>
            <p v-else class="text-xs text-muted-text dark:text-dark-muted-text">Nuk ka edukim.</p>
          </section>
        </div>
      </main>
    </div>
    <div
      class="absolute inset-0 bg-gradient-to-t from-neutral-light dark:from-dark-neutral-container via-transparent to-transparent opacity-90"
    ></div>
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
</script>

<style scoped>
  .creative-mini-cv-content {
    transform-origin: top left;
    overflow: hidden;
  }
</style>

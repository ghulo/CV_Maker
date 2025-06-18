<template>
  <div
    class="modern-mini-cv-content p-4 text-neutral-text dark:text-dark-neutral-text border border-neutral-border dark:border-dark-neutral-border rounded-lg scale-75 origin-top-left overflow-hidden h-64 w-full relative flex"
  >
    <!-- Left Column: Personal Info (scaled) -->
    <div
      class="modern-mini-sidebar w-1/3 pr-4 border-r border-divider-color dark:border-dark-divider-color"
    >
      <h3 class="text-base font-bold text-primary dark:text-primary-accent mb-2">
        {{ cv.personalDetails.fullName || 'Emri Mbiemri' }}
      </h3>
      <p class="text-xs text-muted-text dark:text-dark-muted-text">
        {{ cv.personalDetails.email || 'email@example.com' }}
      </p>
      <p class="text-xs text-muted-text dark:text-dark-muted-text mb-4">
        {{ cv.personalDetails.phone || '555-1234' }}
      </p>

      <section class="mt-4">
        <h4
          class="text-sm font-semibold text-neutral-text dark:text-dark-neutral-text border-b border-primary dark:border-dark-primary pb-1 mb-2"
        >
          Aftësitë
        </h4>
        <div class="flex flex-wrap gap-1">
          <span
            v-for="(skill, index) in (cv.skills || []).slice(0, 3)"
            :key="index"
            class="bg-primary/10 text-primary dark:bg-dark-primary/20 dark:text-dark-primary text-xs px-2 py-0.5 rounded-full"
          >
            {{ skill.name }}
          </span>
          <span v-if="cv.skills && cv.skills.length > 3" class="text-xs text-muted-text">...</span>
        </div>
      </section>
    </div>

    <!-- Right Column: Summary, Experience, Education (scaled) -->
    <div class="modern-mini-main-content w-2/3 pl-4">
      <section class="mb-4">
        <h4
          class="text-sm font-semibold text-neutral-text dark:text-dark-neutral-text border-b border-primary dark:border-dark-primary pb-1 mb-2"
        >
          Përmbledhja
        </h4>
        <p class="text-xs text-subtle-text dark:text-dark-subtle-text line-clamp-3">
          {{ cv.summary || 'Përshkrim i shkurtër i karrierës...' }}
        </p>
      </section>

      <section class="mb-4">
        <h4
          class="text-sm font-semibold text-neutral-text dark:text-dark-neutral-text border-b border-primary dark:border-dark-primary pb-1 mb-2"
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
        <p v-else class="text-xs text-muted-text dark:text-dark-muted-text">
          Nuk ka përvojë të shtuar.
        </p>
      </section>

      <section>
        <h4
          class="text-sm font-semibold text-neutral-text dark:text-dark-neutral-text border-b border-primary dark:border-dark-primary pb-1 mb-2"
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
        <p v-else class="text-xs text-muted-text dark:text-dark-muted-text">
          Nuk ka edukim të shtuar.
        </p>
      </section>
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
  .modern-mini-cv-content {
    transform-origin: top left;
    overflow: hidden;
  }
  .line-clamp-3 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    overflow: hidden;
  }
</style>

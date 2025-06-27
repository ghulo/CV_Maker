<template>
  <div
    class="professional-mini-cv-content p-4 text-neutral-text border border-color rounded-lg scale-75 origin-top-left overflow-hidden h-64 w-full relative bg-card-bg shadow-elevated"
  >
    <!-- Header: Name and Summary (scaled) -->
    <header
      class="text-center mb-4 pb-2 border-b border-color"
    >
      <h3 class="text-lg font-bold text-primary-text mb-1">
        {{ cv.personalDetails?.fullName || cv.firstName && cv.lastName ? `${cv.firstName} ${cv.lastName}` : 'Emri Mbiemri' }}
      </h3>
      <p class="text-xs text-muted-text line-clamp-1">
        {{ cv.summary || 'Përmbledhja Profesionale' }}
      </p>
    </header>

    <!-- Main Content in two columns (scaled) -->
    <div class="grid grid-cols-3 gap-4 h-full-minus-header">
      <!-- Left Column: Skills, Education (scaled) -->
      <div class="col-span-1 pr-2">
        <section class="mb-4">
          <h4
            class="text-sm font-semibold text-primary-text border-b border-accent-blue pb-1 mb-2"
          >
            Aftësitë
          </h4>
          <ul class="list-disc list-inside space-y-0.5 text-xs text-muted-text">
            <li
              v-for="(skill, index) in (cv.skills || []).slice(0, 3)"
              :key="index"
              class="text-muted-text"
            >
              {{ skill.name }}
            </li>
            <li v-if="cv.skills && cv.skills.length > 3" class="text-xs text-muted-text">...</li>
          </ul>
        </section>

        <section>
          <h4
            class="text-sm font-semibold text-primary-text border-b border-accent-blue pb-1 mb-2"
          >
            Edukimi
          </h4>
          <div v-if="cv.education && cv.education.length">
            <p class="text-xs font-medium text-primary-text">
              {{ cv.education?.[0]?.degree || 'Titulli' }}
            </p>
            <p class="text-xs text-muted-text">
              {{ cv.education?.[0]?.university || cv.education?.[0]?.institution || 'Universiteti' }}
            </p>
          </div>
          <p v-else class="text-xs text-muted-text">Nuk ka edukim.</p>
        </section>
      </div>

      <!-- Right Column: Work Experience (scaled) -->
      <div class="col-span-2 pl-2">
        <section>
          <h4
            class="text-sm font-semibold text-primary-text border-b border-accent-blue pb-1 mb-2"
          >
            Përvoja
          </h4>
          <div v-if="cv.experience && cv.experience.length || cv.workExperience && cv.workExperience.length">
            <p class="text-xs font-medium text-primary-text">
              {{ cv.experience?.[0]?.title || cv.workExperience?.[0]?.position || 'Titulli i Punës' }}
            </p>
            <p class="text-xs text-muted-text">
              {{ cv.experience?.[0]?.company || cv.workExperience?.[0]?.company || 'Kompania' }}
            </p>
            <p class="text-xs text-muted-text line-clamp-2">
              {{ cv.experience?.[0]?.description || cv.workExperience?.[0]?.description || 'Përshkrim i shkurtër detyrash.' }}
            </p>
          </div>
          <p v-else class="text-xs text-muted-text">Nuk ka përvojë.</p>
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
</script>

<style scoped>
  .professional-mini-cv-content {
    background: #fff;
    border: 2px solid #EAF6FB;
    border-radius: var(--radius-xl);
    box-shadow: 0 4px 24px #2D9CDB11;
    color: #10131A;
    transition: box-shadow 0.3s, border-color 0.3s;
  }
  .professional-mini-cv-content:hover {
    box-shadow: 0 12px 36px #2D9CDB22;
    border-color: #2D9CDB;
  }
  h3, h4 {
    font-family: var(--font-heading);
    font-weight: 700;
    color: #2D9CDB;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }
  h4 {
    color: #F2C94C;
    border-bottom: 2px solid #2D9CDB;
  }
  .text-muted-text, p {
    color: #10131A;
  }
  .line-clamp-1, .line-clamp-2 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .line-clamp-1 { -webkit-line-clamp: 1; }
  .line-clamp-2 { -webkit-line-clamp: 2; }
  .h-full-minus-header {
    height: calc(100% - 40px);
  }
</style>

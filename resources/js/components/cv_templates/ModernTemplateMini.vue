<template>
  <div
    class="modern-mini-cv-content p-4 text-neutral-text border border-color rounded-lg scale-75 origin-top-left overflow-hidden h-64 w-full relative flex bg-card-bg shadow-elevated"
  >
    <!-- Left Column: Personal Info (scaled) -->
    <div
      class="modern-mini-sidebar w-1/3 pr-4 border-r border-color"
    >
      <h3 class="text-base font-bold text-primary-text mb-2">
        {{ cv.personalDetails?.fullName || cv.firstName && cv.lastName ? `${cv.firstName} ${cv.lastName}` : 'Emri Mbiemri' }}
      </h3>
      <p class="text-xs text-muted-text">
        {{ cv.personalDetails?.email || cv.email || 'email@example.com' }}
      </p>
      <p class="text-xs text-muted-text mb-4">
        {{ cv.personalDetails?.phone || cv.phone || '555-1234' }}
      </p>

      <section class="mt-4">
        <h4
          class="text-sm font-semibold text-primary-text border-b border-accent-blue pb-1 mb-2"
        >
          Aftësitë
        </h4>
        <div class="flex flex-wrap gap-1">
          <span
            v-for="(skill, index) in (cv.skills || []).slice(0, 3)"
            :key="index"
            class="bg-accent-blue-light text-accent-blue-darker text-xs px-2 py-0.5 rounded-full"
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
          class="text-sm font-semibold text-primary-text border-b border-accent-blue pb-1 mb-2"
        >
          Përmbledhja
        </h4>
        <p class="text-xs text-muted-text line-clamp-3">
          {{ cv.summary || 'Përshkrim i shkurtër i karrierës...' }}
        </p>
      </section>

      <section class="mb-4">
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
        </div>
        <p v-else class="text-xs text-muted-text">Nuk ka përvojë të shtuar.</p>
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
        <p v-else class="text-xs text-muted-text">Nuk ka edukim të shtuar.</p>
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
</script>

<style scoped>
  .modern-mini-cv-content {
    background: #fff;
    border: 2px solid #EAF6FB;
    border-radius: var(--radius-xl);
    box-shadow: 0 4px 24px #2D9CDB11;
    color: #10131A;
    transition: box-shadow 0.3s, border-color 0.3s;
  }
  .modern-mini-cv-content:hover {
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
  .bg-accent-blue-light {
    background: #EAF6FB;
  }
  .text-accent-blue-darker {
    color: #2D9CDB;
  }
  .line-clamp-3 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    overflow: hidden;
  }
</style>

<template>
  <div
    class="classic-mini-cv-content p-4 text-neutral-text border border-color rounded-lg scale-75 origin-top-left overflow-hidden h-64 w-full relative bg-card-bg shadow-elevated"
  >
    <!-- Scaled down classic template layout -->
    <div class="relative h-full">
      <header
        class="text-center mb-4 pb-2 border-b border-color"
      >
        <h3 class="text-lg font-bold text-primary-text">
          {{ cv.personalDetails?.fullName || cv.firstName && cv.lastName ? `${cv.firstName} ${cv.lastName}` : 'Emri Mbiemri' }}
        </h3>
        <p class="text-xs text-muted-text">
          {{ cv.personalDetails?.email || cv.email || 'email@example.com' }}
        </p>
      </header>

      <main class="space-y-3">
        <section>
          <h4
            class="text-sm font-semibold text-primary-text border-b border-accent-blue pb-1 mb-2"
          >
            Përmbledhja
          </h4>
          <p class="text-xs text-muted-text line-clamp-3">
            {{ cv.summary || 'Përshkrim i shkurtër i karrierës...' }}
          </p>
        </section>

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
          </div>
          <p v-else class="text-xs text-muted-text">
            Nuk ka përvojë të shtuar.
          </p>
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
          <p v-else class="text-xs text-muted-text">
            Nuk ka edukim të shtuar.
          </p>
        </section>
      </main>
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
  .classic-mini-cv-content {
    background: #fff;
    border: 2px solid #EAF6FB;
    border-radius: var(--radius-xl);
    box-shadow: 0 4px 24px #2D9CDB11;
    color: #10131A;
    transition: box-shadow 0.3s, border-color 0.3s;
  }
  .classic-mini-cv-content:hover {
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
  .line-clamp-3 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    overflow: hidden;
  }
</style>

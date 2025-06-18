<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div
      class="max-w-4xl mx-auto bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden relative"
    >
      <div class="p-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6 text-center">
          Parashikimi i CV-së
        </h1>

        <div v-if="loading" class="space-y-4 animate-pulse">
          <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-3/4 mx-auto"></div>
          <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-1/2 mx-auto"></div>
          <div class="h-64 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
          <div class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-5/6 mx-auto"></div>
        </div>
        <div
          v-else-if="error"
          class="p-8 text-center bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 rounded-md"
        >
          <p class="font-medium">{{ error }}</p>
          <p class="text-sm mt-2">Ju lutem provoni përsëri ose kontaktoni mbështetjen.</p>
        </div>
        <div v-else-if="cv">
          <!-- Dynamic Template Rendering -->
          <component :is="currentTemplateComponent" :cv="cv"></component>
        </div>
        <div v-else class="p-8 text-center text-gray-600 dark:text-gray-400">
          <p>Nuk ka të dhëna për CV. Ju lutem kthehuni prapa dhe krijoni një CV.</p>
        </div>
      </div>
    </div>
    <div class="max-w-4xl mx-auto mt-8 flex justify-end space-x-4">
      <button
        @click="goBack"
        class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
      >
        <i class="fas fa-arrow-left mr-3"></i>
        Kthehu Pas
      </button>
      <button
        @click="downloadCv"
        class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
      >
        <i class="fas fa-download mr-3"></i>
        Shkarko si PDF
      </button>
    </div>
  </div>
  <!-- Download Options Modal -->
  <DownloadOptionsModal
    :is-visible="showDownloadModal"
    :cv-id="currentCvToDownload.id"
    :cv-title="currentCvToDownload.title"
    @confirm="handleDownloadConfirm"
    @cancel="showDownloadModal = false"
    v-if="currentCvToDownload"
  />
</template>

<script setup>
  import { ref, onMounted, computed } from 'vue'
  import { useRoute, useRouter } from 'vue-router'
  import axios from 'axios'
  import ClassicTemplate from '@/components/cv_templates/ClassicTemplate.vue'
  import ModernTemplate from '@/components/cv_templates/ModernTemplate.vue'
  import ProfessionalTemplate from '@/components/cv_templates/ProfessionalTemplate.vue'
  import CreativeTemplate from '@/components/cv_templates/CreativeTemplate.vue'
  import DownloadOptionsModal from '@/components/common/DownloadOptionsModal.vue'

  const route = useRoute()
  const router = useRouter()
  const cvId = route.params.id

  const cv = ref(null)
  const loading = ref(true)
  const error = ref(null)
  const showDownloadModal = ref(false)
  const currentCvToDownload = ref(null)

  const fetchCv = async () => {
    try {
      const token = localStorage.getItem('auth_token')
      const response = await axios.get(`/api/cvs/${cvId}`, {
        headers: { Authorization: `Bearer ${token}` },
      })
      if (response.data.success) {
        cv.value = response.data.cv
      } else {
        error.value = response.data.message || 'CV nuk u gjet.'
      }
    } catch (err) {
      error.value = 'Gabim gjatë ngarkimit të CV-së. Ju lutem provoni përsëri.'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  const currentTemplateComponent = computed(() => {
    if (!cv.value) return null
    switch (cv.value.selected_template) {
      case 'classic':
        return ClassicTemplate
      case 'modern':
        return ModernTemplate
      case 'professional':
        return ProfessionalTemplate
      case 'creative':
        return CreativeTemplate
      // Add cases for other templates here
      default:
        return ClassicTemplate // Fallback to classic template
    }
  })

  const goBack = () => {
    router.back()
  }

  const downloadCv = () => {
    currentCvToDownload.value = { id: cvId, title: cv.value.title }
    showDownloadModal.value = true
  }

  const handleDownloadConfirm = ({ id, title, style, quality }) => {
    showDownloadModal.value = false
    const downloadUrl = `/api/cvs/${id}/download?style=${style}&quality=${quality}`
    window.open(downloadUrl, '_blank')
  }

  onMounted(() => {
    fetchCv()
  })
</script>

<style scoped>
  /* No custom styles needed, Tailwind handles it */
</style>

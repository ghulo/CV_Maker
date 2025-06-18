<template>
  <main class="main">
    <div class="page-container animate-in">
      <section class="dashboard-hero homepage-hero">
        <div class="hero-bg-decoration" aria-hidden="true"></div>
        <h2 class="reveal-on-scroll">{{ totalCvs > 0 ? 'Menaxhoni CV-të Tuaja, Zgjidhni Të Ardhmen Tuaj.' : 'Krijo CV-në Tënde Të Parë Me Lehtësi!' }}</h2>
        <p class="reveal-on-scroll" data-reveal-delay="100">
          {{ totalCvs > 0 ? 'Këtu mund të shihni, modifikoni, shkarkoni dhe menaxhoni të gjitha CV-të tuaja të krijuara.' : 'Nuk keni krijuar ende asnjë CV. Filloni duke zgjedhur një model dhe ndërtoni profilin tuaj profesional.' }}
        </p>
        <router-link
          to="/templates"
          class="btn-create btn-large btn btn-primary reveal-on-scroll"
          data-reveal-delay="200"
          v-if="totalCvs === 0"
        >
          <i class="fas fa-plus"></i> Krijo CV Tani
        </router-link>
      </section>

      <section class="dashboard-stats homepage-value-prop">
        <div
          class="value-prop-container reveal-on-scroll glassmorphic-card"
          data-reveal-delay="100"
        >
          <div class="value-prop-headline">
            <h3 class="reveal-on-scroll" data-reveal-delay="200">Përmbledhje e Shpejtë e CV-ve Tua.</h3>
          </div>
          <div class="dashboard-stats-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <div class="stat-card reveal-on-scroll glassmorphic-card" data-reveal-delay="250">
              <div class="stat-icon-wrapper"><i class="fas fa-file-invoice"></i></div>
              <div class="stat-info">
                <p class="stat-label">Total CVs Created</p>
                <span class="stat-value">{{ totalCvs }}</span>
              </div>
            </div>
            <div class="stat-card reveal-on-scroll glassmorphic-card" data-reveal-delay="300">
              <div class="stat-icon-wrapper"><i class="fas fa-hourglass-half"></i></div>
              <div class="stat-info">
                <p class="stat-label">CV-të në Draft</p>
                <span class="stat-value">{{ draftCvs }}</span>
              </div>
            </div>
            <div class="stat-card reveal-on-scroll glassmorphic-card" data-reveal-delay="350">
              <div class="stat-icon-wrapper"><i class="fas fa-download"></i></div>
              <div class="stat-info">
                <p class="stat-label">Shkarkime Totale (Placeholder)</p>
                <span class="stat-value">N/A</span>
              </div>
            </div>
            <div class="stat-card reveal-on-scroll glassmorphic-card" data-reveal-delay="400">
              <div class="stat-icon-wrapper"><i class="fas fa-eye"></i></div>
              <div class="stat-info">
                <p class="stat-label">Shikime Totale (Placeholder)</p>
                <span class="stat-value">N/A</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="cv-list-section homepage-how-it-works">
        <div class="list-controls-header">
          <h3 class="reveal-on-scroll">CV-të e Mia</h3>
          <router-link to="/templates" class="btn btn-primary reveal-on-scroll" data-reveal-delay="450">
            <i class="fas fa-plus"></i> Krijo CV Të Re
          </router-link>
        </div>

        <!-- Search, Filter, Sort Controls -->
        <div class="flex flex-col sm:flex-row gap-4 mb-6 mt-4">
          <div class="relative flex-grow reveal-on-scroll" data-reveal-delay="500">
            <input
              type="text"
              v-model="searchTerm"
              placeholder="Kërko sipas titullit, emrit, apo përmbledhjes..."
              class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-dark-neutral-bg dark:border-dark-neutral-border dark:text-dark-neutral-text"
            />
            <i
              class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"
            ></i>
          </div>

          <select
            v-model="filterTemplate"
            class="w-full sm:w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-dark-neutral-bg dark:border-dark-neutral-border dark:text-dark-neutral-text reveal-on-scroll"
            data-reveal-delay="550"
          >
            <option value="all">Filtro sipas Modelit</option>
            <option value="classic">Klasik</option>
            <option value="modern">Modern</option>
            <option value="professional">Profesional</option>
            <option value="creative">Kreativ</option>
          </select>

          <select
            v-model="sortBy"
            class="w-full sm:w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-dark-neutral-bg dark:border-dark-neutral-border dark:text-dark-neutral-text reveal-on-scroll"
            data-reveal-delay="600"
          >
            <option value="updated_at_desc">Rendit sipas: Më të Reja</option>
            <option value="updated_at_asc">Rendit sipas: Më të Vjetra</option>
            <option value="title_asc">Rendit sipas: Titullit (A-Z)</option>
            <option value="title_desc">Rendit sipas: Titullit (Z-A)</option>
          </select>
        </div>

        <div v-if="loading" class="loading-spinner reveal-on-scroll" data-reveal-delay="650">
          <i class="fas fa-spinner fa-spin"></i>
        </div>

        <div
          v-else-if="cvs.length === 0"
          class="empty-state-container reveal-on-scroll"
          data-reveal-delay="650"
        >
          <div class="empty-state-icon"><i class="fas fa-file-alt"></i></div>
          <h3>Asnjë CV nuk u Gjet</h3>
          <p>Nuk keni krijuar ende asnjë CV. Filloni duke krijuar një të re!</p>
        </div>

        <div
          v-else
          class="cv-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-6"
        >
          <CvPreviewCard
            v-for="(cv_item, index) in cvs"
            :key="cv_item.id"
            :cv="cv_item"
            @preview="previewCv"
            @edit="editCv"
            @download="downloadCv"
            @delete="confirmDelete"
            class="reveal-on-scroll"
            :data-reveal-delay="700 + (index * 70)"
          />
        </div>
      </section>
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
  </main>
</template>

<script>
  import { ref, onMounted, computed, nextTick } from 'vue'
  import { useRouter } from 'vue-router'
  import axios from 'axios'
  import CvPreviewCard from '@/components/CvPreviewCard.vue'
  import { useConfirmationModal } from '../../composables/useConfirmationModal.js'
  import DownloadOptionsModal from '../common/DownloadOptionsModal.vue'

  export default {
    name: 'Dashboard',
    components: { CvPreviewCard, DownloadOptionsModal },
    setup() {
      const cvs_all = ref([]) // Stores all fetched CVs
      const loading = ref(true)
      const cvToDelete = ref(null)
      const searchTerm = ref('')
      const filterTemplate = ref('all')
      const sortBy = ref('updated_at_desc') // Default sort: most recently updated

      const { showModal } = useConfirmationModal()
      const router = useRouter()

      const showDownloadModal = ref(false)
      const currentCvToDownload = ref(null)

      const fetchCvs = async () => {
        try {
          const token = localStorage.getItem('auth_token')
          const response = await axios.get('/api/my-cvs', {
            headers: { Authorization: `Bearer ${token}` },
          })
          cvs_all.value = response.data.cvs
        } catch (error) {
          console.error('Error fetching user CVs:', error)
        } finally {
          loading.value = false
        }
      }

      const totalCvs = computed(() => cvs_all.value.length)
      const draftCvs = computed(() => cvs_all.value.filter(cv => !cv.isFinalized).length)

      const filteredAndSortedCvs = computed(() => {
        let tempCvs = [...cvs_all.value]

        // 1. Filter by search term
        if (searchTerm.value) {
          const lowerSearchTerm = searchTerm.value.toLowerCase()
          tempCvs = tempCvs.filter(
            (cv) =>
              (cv.title && cv.title.toLowerCase().includes(lowerSearchTerm)) ||
              (cv.personalDetails &&
                cv.personalDetails.fullName &&
                cv.personalDetails.fullName.toLowerCase().includes(lowerSearchTerm)) ||
              (cv.summary && cv.summary.toLowerCase().includes(lowerSearchTerm))
          )
        }

        // 2. Filter by template
        if (filterTemplate.value !== 'all') {
          tempCvs = tempCvs.filter((cv) => cv.selectedTemplate === filterTemplate.value)
        }

        // 3. Sort
        tempCvs.sort((a, b) => {
          switch (sortBy.value) {
            case 'title_asc':
              return (a.title || '').localeCompare(b.title || '')
            case 'title_desc':
              return (b.title || '').localeCompare(a.title || '')
            case 'updated_at_asc':
              return new Date(a.updatedAt) - new Date(b.updatedAt)
            case 'updated_at_desc':
              return new Date(b.updatedAt) - new Date(a.updatedAt)
            default:
              return 0
          }
        })

        return tempCvs
      })

      const formatDate = (dateString) => {
        if (!dateString) return 'N/A'
        const options = { year: 'numeric', month: 'long', day: 'numeric' }
        return new Date(dateString).toLocaleDateString(undefined, options)
      }

      const confirmDelete = async (id) => {
        const confirmed = await showModal({
          title: 'Fshij Konfirmimin',
          message: 'Jeni të sigurt që dëshironi të fshini këtë CV? Ky veprim nuk mund të zhbëhet.',
          confirmButtonText: 'Fshij',
          cancelButtonText: 'Anulo',
          confirmButtonClass: 'btn-danger',
        })

        if (confirmed) {
          cvToDelete.value = id
          await deleteCv()
        } else {
          cvToDelete.value = null
        }
      }

      const deleteCv = async () => {
        if (!cvToDelete.value) return
        try {
          const token = localStorage.getItem('auth_token')
          await axios.delete(`/api/cvs/${cvToDelete.value}`, {
            headers: { Authorization: `Bearer ${token}` },
          })
          cvs_all.value = cvs_all.value.filter((cv) => cv.id !== cvToDelete.value)
          await showModal({
            title: 'Sukses!',
            message: 'CV-ja u fshi me sukses.',
            confirmButtonText: 'OK',
            confirmButtonClass: 'btn-primary',
            cancelButtonText: '',
          })
        } catch (error) {
          console.error('Error deleting CV:', error)
          await showModal({
            title: 'Gabim!',
            message: 'Gabim gjatë fshirjes së CV-së. Ju lutem provoni përsëri.',
            confirmButtonText: 'OK',
            confirmButtonClass: 'btn-danger',
            cancelButtonText: '',
          })
        } finally {
          cvToDelete.value = null
        }
      }

      const downloadCv = (id, title) => {
        currentCvToDownload.value = { id, title };
        showDownloadModal.value = true;
      };

      const handleDownloadConfirm = async ({ id, title, style, quality }) => {
        showDownloadModal.value = false;
        try {
          const token = localStorage.getItem('auth_token');
          const response = await axios.get(`/api/cvs/${id}/download?style=${style}&quality=${quality}`, {
            headers: { Authorization: `Bearer ${token}` },
            responseType: 'blob',
          });
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement('a');
          link.href = url;
          const safeTitle = title ? title.replace(/[^a-z0-9]/gi, '_').toLowerCase() : 'cv';
          link.setAttribute('download', `${safeTitle}_${style}_${quality}.pdf`);
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        } catch (error) {
          console.error('Error downloading CV:', error);
          await showModal({
            title: 'Gabim Shkarkimi!',
            message: 'Gabim gjatë shkarkimit të CV-së. Ju lutem provoni përsëri.',
            confirmButtonText: 'OK',
            confirmButtonClass: 'btn-danger',
            cancelButtonText: '',
          });
        }
      };

      const previewCv = (id) => {
        router.push({ name: 'cv.preview', params: { id: id } })
      }

      const editCv = (id) => {
        router.push({ name: 'cv.edit', params: { id: id } })
      }

      onMounted(() => {
        fetchCvs()
        // Ensure DOM is updated before observing elements
        nextTick(() => {
          const revealElements = document.querySelectorAll('.reveal-on-scroll')
          const revealObserver = new IntersectionObserver(
            (entries, observer) => {
              entries.forEach((entry) => {
                if (entry.isIntersecting) {
                  const delay = parseInt(entry.target.getAttribute('data-reveal-delay') || '0', 10)
                  setTimeout(() => entry.target.classList.add('is-revealed'), delay)
                  observer.unobserve(entry.target)
                }
              })
            },
            { threshold: 0.1 }
          )
          revealElements.forEach((el) => revealObserver.observe(el))
        })
      })

      return {
        cvs: filteredAndSortedCvs,
        loading,
        totalCvs,
        draftCvs,
        formatDate,
        confirmDelete,
        deleteCv,
        downloadCv,
        previewCv,
        editCv,
        showDeleteModal: false,
        cvToDelete,
        searchTerm,
        filterTemplate,
        sortBy,
        showDownloadModal,
        currentCvToDownload,
        handleDownloadConfirm,
      }
    },
  }
</script>

<style scoped>
  @reference "tailwindcss/theme";

  .dashboard-hero {
    padding-top: 4rem;
    padding-bottom: 4rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    border-radius: var(--radius);
    background: linear-gradient(135deg, var(--neutral-light-alt) 0%, var(--neutral-bg) 100%);
    color: var(--neutral-text);
    margin-bottom: var(--space-xl);
  }

  body.dark-theme .dashboard-hero {
    background: linear-gradient(135deg, var(--dark-neutral-container) 0%, var(--dark-neutral-bg) 100%);
    color: var(--dark-neutral-text);
  }

  .dashboard-hero h2 {
    font-size: 2.8em;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--heading-color);
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.2;
  }

  body.dark-theme .dashboard-hero h2 {
    color: var(--dark-heading-color);
  }

  .dashboard-hero p {
    font-size: 1.1em;
    color: var(--muted-text);
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
    margin-bottom: 2rem;
  }

  body.dark-theme .dashboard-hero p {
    color: var(--dark-muted-text);
  }

  .hero-bg-decoration {
    position: absolute;
    top: -50px;
    left: -50px;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(var(--primary-rgb), 0.1) 0%, transparent 70%);
    border-radius: 50%;
    filter: blur(80px);
    z-index: 0;
  }

  .dashboard-stats {
    margin-top: 0;
    padding-top: 2rem;
    padding-bottom: 4rem;
  }

  .value-prop-container {
    background: var(--neutral-light);
    border: 1px solid var(--neutral-border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-light);
    padding: var(--space-xl);
    transition: all 0.4s var(--animation-ease-out);
    transform-style: preserve-3d;
    position: relative;
    overflow: hidden;
    z-index: 2;
  }

  body.dark-theme .value-prop-container {
    background: rgba(var(--dark-neutral-container-rgb), var(--dark-glass-bg-opacity));
    border: 1px solid rgba(255, 255, 255, var(--dark-glass-border-opacity));
    box-shadow: 0 8px 30px rgba(0, 0, 0, var(--dark-glass-shadow-opacity)), var(--dark-glass-inner-shadow);
  }

  .value-prop-headline h3 {
    font-size: 2.2em;
    font-weight: 700;
    color: var(--heading-color);
    margin-bottom: 1.5rem;
    text-align: center;
  }

  body.dark-theme .value-prop-headline h3 {
    color: var(--dark-heading-color);
  }

  .dashboard-stats-grid {
    gap: 1.5rem;
  }

  .stat-card {
    background-color: var(--surface-raised);
    border: 1px solid var(--border-color-soft);
    border-radius: var(--radius);
    padding: var(--space-md);
    display: flex;
    align-items: center;
    gap: var(--space-md);
    transition: all var(--animation-duration-normal) var(--animation-ease-out);
    color: var(--neutral-text);
  }

  body.dark-theme .stat-card {
    background-color: var(--dark-neutral-bg);
    border-color: var(--dark-neutral-border);
  }

  .stat-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: var(--shadow-hover);
  }

  .stat-icon-wrapper {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(var(--primary-rgb), 0.1);
    flex-shrink: 0;
  }

  body.dark-theme .stat-icon-wrapper {
    background-color: rgba(var(--dark-primary-rgb), 0.15);
  }

  .stat-icon-wrapper i {
    font-size: 1.5rem;
    color: var(--primary);
  }

  body.dark-theme .stat-icon-wrapper i {
    color: var(--dark-primary);
  }

  .stat-info {
    line-height: 1.3;
  }

  .stat-label {
    font-size: 0.9em;
    color: var(--muted-text);
    margin-bottom: 4px;
  }

  body.dark-theme .stat-label {
    color: var(--dark-muted-text);
  }

  .stat-value {
    font-size: 1.6em;
    font-weight: 700;
    color: var(--neutral-text);
  }

  body.dark-theme .stat-value {
    color: var(--dark-neutral-text);
  }

  .cv-list-section {
    margin-top: 2rem;
    padding-top: 2rem;
    padding-bottom: 4rem;
  }

  .list-controls-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .list-controls-header h3 {
    font-size: 2.2em;
    font-weight: 700;
    color: var(--heading-color);
    margin: 0;
  }

  body.dark-theme .list-controls-header h3 {
    color: var(--dark-heading-color);
  }

  .cv-grid {
    gap: 1.5rem;
  }

  .empty-state-container {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--neutral-bg);
    border: 1px solid var(--neutral-border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-light);
    margin-top: 2rem;
  }

  body.dark-theme .empty-state-container {
    background: var(--dark-neutral-container);
    border-color: var(--dark-neutral-border);
  }

  .empty-state-icon {
    font-size: 4rem;
    color: var(--primary);
    margin-bottom: 1rem;
  }

  body.dark-theme .empty-state-icon {
    color: var(--dark-primary);
  }

  .empty-state-container h3 {
    font-size: 1.8em;
    color: var(--heading-color);
    margin-bottom: 0.5rem;
  }

  body.dark-theme .empty-state-container h3 {
    color: var(--dark-heading-color);
  }

  .empty-state-container p {
    color: var(--muted-text);
    font-size: 1.1em;
  }

  body.dark-theme .empty-state-container p {
    color: var(--dark-muted-text);
  }

  .loading-spinner {
    text-align: center;
    padding: 2rem;
    font-size: 3rem;
    color: var(--primary);
  }

  body.dark-theme .loading-spinner {
    color: var(--dark-primary);
  }

  /* General page container styles - copied from AboutUs.vue/global.css implicit */
  .page-container {
    max-width: 1200px;
    margin: var(--space-xl) auto;
    padding: var(--space-xl);
    position: relative;
    z-index: 2;
  }

  /* Glassmorphic card styles - copied/adapted from AboutUs.vue/global.css implicit */
  .glassmorphic-card {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: var(--radius);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
    transition: all 0.3s ease-out;
  }

  body.dark-theme .glassmorphic-card {
    background: rgba(var(--dark-neutral-container-rgb), var(--dark-glass-bg-opacity));
    border: 1px solid rgba(255, 255, 255, var(--dark-glass-border-opacity));
    box-shadow: 0 8px 30px rgba(0, 0, 0, var(--dark-glass-shadow-opacity)), var(--dark-glass-inner-shadow);
  }

  /* Reveal-on-scroll animations - copied from AboutUs.vue/global.css implicit */
  .reveal-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition:
      opacity 0.8s cubic-bezier(0.645, 0.045, 0.355, 1),
      transform 0.8s cubic-bezier(0.645, 0.045, 0.355, 1);
  }
  .reveal-on-scroll.is-revealed {
    opacity: 1;
    transform: translateY(0);
  }
</style>

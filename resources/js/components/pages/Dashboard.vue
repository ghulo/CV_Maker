<template>
  <main class="main">
    <div class="dashboard-overview-container page-container animate-in glassmorphic-card">
      <h2 class="reveal-on-scroll">Dashboard</h2>
      <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">A quick overview of your progress and activities.</p>
      
      <div class="dashboard-stats-grid">
        <div class="stat-card reveal-on-scroll glassmorphic-card" data-reveal-delay="200">
          <div class="stat-icon-wrapper"><i class="fas fa-file-invoice"></i></div>
          <div class="stat-info">
            <p class="stat-label">Total CVs Created</p>
            <span class="stat-value">{{ totalCvs }}</span>
          </div>
        </div>
        <div class="stat-card reveal-on-scroll glassmorphic-card" data-reveal-delay="250">
          <div class="stat-icon-wrapper"><i class="fas fa-hourglass-half"></i></div>
          <div class="stat-info">
            <p class="stat-label">CV-të në Draft</p>
            <span class="stat-value">{{ draftCvs }}</span>
          </div>
        </div>
        <div class="stat-card reveal-on-scroll glassmorphic-card" data-reveal-delay="300">
          <div class="stat-icon-wrapper"><i class="fas fa-download"></i></div>
          <div class="stat-info">
            <p class="stat-label">Shkarkime Totale (Placeholder)</p>
            <span class="stat-value">N/A</span>
          </div>
        </div>
        <div class="stat-card reveal-on-scroll glassmorphic-card" data-reveal-delay="350">
          <div class="stat-icon-wrapper"><i class="fas fa-eye"></i></div>
          <div class="stat-info">
            <p class="stat-label">Shikime Totale (Placeholder)</p>
            <span class="stat-value">N/A</span>
          </div>
        </div>
      </div>
    </div>

    <div class="cv-list-container page-container animate-in glassmorphic-card" data-reveal-delay="400">
      <div class="list-controls-header">
        <h2 class="reveal-on-scroll">My CVs</h2>
        <router-link to="/templates" class="btn btn-primary">
          <i class="fas fa-plus"></i> Create New CV
        </router-link>
      </div>

      <!-- Search, Filter, Sort Controls -->
      <div class="flex flex-col sm:flex-row gap-4 mb-6 mt-4">
        <div class="relative flex-grow">
          <input
            type="text"
            v-model="searchTerm"
            placeholder="Kërko sipas titullit, emrit, apo përmbledhjes..."
            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-dark-neutral-bg dark:border-dark-neutral-border dark:text-dark-neutral-text"
          />
          <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
        </div>

        <select v-model="filterTemplate" class="w-full sm:w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-dark-neutral-bg dark:border-dark-neutral-border dark:text-dark-neutral-text">
          <option value="all">Filtro sipas Modelit</option>
          <option value="classic">Klasik</option>
          <option value="modern">Modern</option>
          <option value="professional">Profesional</option>
          <option value="creative">Kreativ</option>
        </select>

        <select v-model="sortBy" class="w-full sm:w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-dark-neutral-bg dark:border-dark-neutral-border dark:text-dark-neutral-text">
          <option value="updated_at_desc">Rendit sipas: Më të Reja</option>
          <option value="updated_at_asc">Rendit sipas: Më të Vjetra</option>
          <option value="title_asc">Rendit sipas: Titullit (A-Z)</option>
          <option value="title_desc">Rendit sipas: Titullit (Z-A)</option>
        </select>
      </div>

      <div v-if="loading" class="loading-spinner">
        <i class="fas fa-spinner fa-spin"></i>
      </div>

      <div v-else-if="cvs.length === 0" class="empty-state-container">
        <div class="empty-state-icon"><i class="fas fa-file-alt"></i></div>
        <h3>No CVs Found</h3>
        <p>You haven't created any CVs yet. Get started by creating a new one!</p>
      </div>

      <div v-else class="cv-grid">
        <CvPreviewCard
          v-for="cv_item in cvs"
          :key="cv_item.id"
          :cv="cv_item"
          @preview="previewCv"
          @edit="editCv"
          @download="downloadCv"
          @delete="confirmDelete"
        />
      </div>
    </div>
  </main>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import CvPreviewCard from '@/components/CvPreviewCard.vue';
import { useConfirmationModal } from '../../composables/useConfirmationModal.js';

export default {
  name: 'Dashboard',
  components: { CvPreviewCard },
  setup() {
    const cvs_all = ref([]); // Stores all fetched CVs
    const loading = ref(true);
    const cvToDelete = ref(null);
    const searchTerm = ref('');
    const filterTemplate = ref('all');
    const sortBy = ref('updated_at_desc'); // Default sort: most recently updated

    const { showModal } = useConfirmationModal();

    const fetchCvs = async () => {
      try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/my-cvs', {
          headers: { Authorization: `Bearer ${token}` }
        });
        cvs_all.value = response.data.data;
      } catch (error) {
        console.error('Error fetching user CVs:', error);
      } finally {
        loading.value = false;
      }
    };

    const totalCvs = computed(() => cvs_all.value.length);

    const filteredAndSortedCvs = computed(() => {
      let tempCvs = [...cvs_all.value];

      // 1. Filter by search term
      if (searchTerm.value) {
        const lowerSearchTerm = searchTerm.value.toLowerCase();
        tempCvs = tempCvs.filter(cv =>
          (cv.title && cv.title.toLowerCase().includes(lowerSearchTerm)) ||
          (cv.personal_details && cv.personal_details.full_name && cv.personal_details.full_name.toLowerCase().includes(lowerSearchTerm)) ||
          (cv.summary && cv.summary.toLowerCase().includes(lowerSearchTerm))
        );
      }

      // 2. Filter by template
      if (filterTemplate.value !== 'all') {
        tempCvs = tempCvs.filter(cv => cv.template_id === filterTemplate.value);
      }

      // 3. Sort
      tempCvs.sort((a, b) => {
        switch (sortBy.value) {
          case 'title_asc':
            return (a.title || '').localeCompare(b.title || '');
          case 'title_desc':
            return (b.title || '').localeCompare(a.title || '');
          case 'updated_at_asc':
            return new Date(a.updated_at) - new Date(b.updated_at);
          case 'updated_at_desc':
            return new Date(b.updated_at) - new Date(a.updated_at);
          default:
            return 0;
        }
      });

      return tempCvs;
    });

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    };

    const confirmDelete = async (id) => {
      const confirmed = await showModal({
        title: 'Fshij Konfirmimin',
        message: 'Jeni të sigurt që dëshironi të fshini këtë CV? Ky veprim nuk mund të zhbëhet.',
        confirmButtonText: 'Fshij',
        cancelButtonText: 'Anulo',
        confirmButtonClass: 'btn-danger'
      });

      if (confirmed) {
        cvToDelete.value = id;
        await deleteCv();
      } else {
        cvToDelete.value = null;
      }
    };

    const deleteCv = async () => {
      if (!cvToDelete.value) return;
      try {
        const token = localStorage.getItem('auth_token');
        await axios.delete(`/api/cvs/${cvToDelete.value}`, {
          headers: { Authorization: `Bearer ${token}` }
        });
        cvs_all.value = cvs_all.value.filter(cv => cv.id !== cvToDelete.value);
        await showModal({
          title: 'Sukses!',
          message: 'CV-ja u fshi me sukses.',
          confirmButtonText: 'OK',
          confirmButtonClass: 'btn-primary',
          cancelButtonText: ''
        });
      } catch (error) {
        console.error('Error deleting CV:', error);
        await showModal({
          title: 'Gabim!',
          message: 'Gabim gjatë fshirjes së CV-së. Ju lutem provoni përsëri.',
          confirmButtonText: 'OK',
          confirmButtonClass: 'btn-danger',
          cancelButtonText: ''
        });
      } finally {
        cvToDelete.value = null;
      }
    };
    
    const downloadCv = async (id, title) => {
        try {
            const token = localStorage.getItem('auth_token');
            const response = await axios.get(`/api/cvs/${id}/download`, {
                headers: { Authorization: `Bearer ${token}` },
                responseType: 'blob',
            });
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            const safeTitle = title ? title.replace(/[^a-z0-9]/gi, '_').toLowerCase() : 'cv';
            link.setAttribute('download', `${safeTitle}.pdf`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (error) {
            console.error('Error downloading CV:', error);
        }
    };

    const previewCv = (id) => {
        router.push({ name: 'cv.preview', params: { id: id } });
    };

    const editCv = (id) => {
        router.push({ name: 'cv.edit', params: { id: id } });
    };

    onMounted(fetchCvs);

    return {
      cvs: filteredAndSortedCvs,
      loading,
      totalCvs,
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
    };
  },
};
</script>

<style scoped>
.cv-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

.modal-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
    display: flex; justify-content: center; align-items: center;
    z-index: 1000;
}
.modal-content {
    background: var(--neutral-light);
    padding: var(--space-lg);
    border-radius: var(--radius);
    box-shadow: var(--shadow-hover);
    width: 90%; max-width: 400px;
}
body.dark-theme .modal-content { background: var(--dark-neutral-container); }
.modal-title { font-size: 1.5em; font-weight: 600; margin-bottom: var(--space-md); }
.modal-actions { margin-top: var(--space-lg); display: flex; justify-content: flex-end; gap: var(--space-sm); }
</style>

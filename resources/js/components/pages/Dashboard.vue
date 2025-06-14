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

      <div v-if="loading" class="loading-spinner">
        <i class="fas fa-spinner fa-spin"></i>
      </div>

      <div v-else-if="cvs.length === 0" class="empty-state-container">
        <div class="empty-state-icon"><i class="fas fa-file-alt"></i></div>
        <h3>No CVs Found</h3>
        <p>You haven't created any CVs yet. Get started by creating a new one!</p>
      </div>

      <ul v-else class="cv-list">
        <li v-for="cv in cvs" :key="cv.id" class="cv-item">
          <div class="cv-item-main-info">
            <div class="cv-item-icon"><i class="fas fa-file-invoice"></i></div>
            <div class="cv-info">
              <strong>{{ cv.cv_title || 'Untitled CV' }}</strong>
              <span class="cv-template">Template: {{ cv.template_name || 'N/A' }}</span>
              <span class="cv-last-updated">Last Updated: {{ formatDate(cv.updated_at) }}</span>
            </div>
          </div>
          <div class="cv-actions">
            <button @click="previewCv(cv.id)" class="btn-icon" title="Preview"><i class="fas fa-eye"></i></button>
            <button @click="downloadCv(cv.id, cv.cv_title)" class="btn-icon" title="Download PDF"><i class="fas fa-download"></i></button>
            <router-link :to="{ name: 'cv.edit', params: { id: cv.id } }" class="btn-icon" title="Edit"><i class="fas fa-edit"></i></router-link>
            <button @click="confirmDelete(cv.id)" class="btn-icon delete" title="Delete"><i class="fas fa-trash"></i></button>
          </div>
        </li>
      </ul>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal-overlay">
        <div class="modal-content">
            <h3 class="modal-title">Confirm Deletion</h3>
            <p>Are you sure you want to delete this CV? This action cannot be undone.</p>
            <div class="modal-actions">
                <button @click="showDeleteModal = false" class="btn btn-secondary">Cancel</button>
                <button @click="deleteCv" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
  </main>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

export default {
  name: 'Dashboard',
  setup() {
    const cvs = ref([]);
    const loading = ref(true);
    const showDeleteModal = ref(false);
    const cvToDelete = ref(null);

    const fetchCvs = async () => {
      try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/my-cvs', {
          headers: { Authorization: `Bearer ${token}` }
        });
        cvs.value = response.data.data;
      } catch (error) {
        console.error('Error fetching user CVs:', error);
      } finally {
        loading.value = false;
      }
    };

    const totalCvs = computed(() => cvs.value.length);

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    };

    const confirmDelete = (id) => {
      cvToDelete.value = id;
      showDeleteModal.value = true;
    };

    const deleteCv = async () => {
        if (!cvToDelete.value) return;
        try {
            const token = localStorage.getItem('auth_token');
            await axios.delete(`/api/cvs/${cvToDelete.value}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            cvs.value = cvs.value.filter(cv => cv.id !== cvToDelete.value);
        } catch (error) {
            console.error('Error deleting CV:', error);
        } finally {
            showDeleteModal.value = false;
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
        console.log(`Previewing CV with ID: ${id}`);
    };

    onMounted(fetchCvs);

    return {
      cvs,
      loading,
      totalCvs,
      formatDate,
      confirmDelete,
      deleteCv,
      downloadCv,
      previewCv,
      showDeleteModal,
      cvToDelete,
    };
  },
};
</script>

<style scoped>
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

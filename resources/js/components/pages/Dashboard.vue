<template>
  <main class="main">
    <div class="dashboard-overview-container page-container animate-in glassmorphic-card">
      <h2 class="reveal-on-scroll">Përmbledhje e Panelit</h2>
      <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">Një vështrim i shpejtë i progresit dhe aktiviteteve tuaja.</p>

      <div class="dashboard-stats-grid">
        <div class="stat-card reveal-on-scroll glassmorphic-card" data-reveal-delay="200">
          <div class="stat-icon-wrapper"><i class="fas fa-file-invoice"></i></div>
          <div class="stat-info">
            <p class="stat-label">Totali i CV-ve të Krijuara</p>
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

    <div class="dashboard-recent-activity-container page-container animate-in glassmorphic-card" data-reveal-delay="400">
      <h2 class="reveal-on-scroll">Aktiviteti i Fundit</h2>
      <p class="page-intro-text reveal-on-scroll" data-reveal-delay="450">Shikoni CV-të tuaja të modifikuara së fundmi.</p>
      <ul class="recent-cv-list" v-if="recentCvs.length">
        <li v-for="cv in recentCvs" :key="cv.id" class="recent-cv-item reveal-on-scroll glassmorphic-card">
          <strong>{{ cv.cv_title || `${cv.emri} ${cv.mbiemri}` || 'CV e Paemërtuar' }}</strong> - Modifikuar: {{ formatDate(cv.updated_at) }}
          <router-link :to="`/cv/${cv.id}/edit`" class="btn-icon"><i class="fas fa-arrow-right"></i></router-link>
        </li>
      </ul>
      <p v-else class="empty-recent-activity">Nuk ka aktivitet të fundit për të shfaqur.</p>
    </div>

    <div class="dashboard-analytics-section page-container animate-in glassmorphic-card" data-reveal-delay="500">
      <h2 class="reveal-on-scroll">Analiza e CV-ve</h2>
      <p class="page-intro-text reveal-on-scroll" data-reveal-delay="550">Shpërndarja e CV-ve tuaja sipas llojit të modelit.</p>
      
      <div class="chart-container reveal-on-scroll glassmorphic-card" data-reveal-delay="600">
        <div v-if="Object.keys(cvsByTemplate).length > 0" class="template-distribution-grid">
          <div v-for="(count, template) in cvsByTemplate" :key="template" class="template-stat-card glassmorphic-card">
            <span class="template-name">{{ template || 'Pa Specifikuar' }}</span>
            <span class="template-count">{{ count }} CV</span>
          </div>
        </div>
        <p v-else class="empty-chart-data">Nuk ka të dhëna për modele CV-sh për të shfaqur.</p>
      </div>

      <div class="chart-container reveal-on-scroll glassmorphic-card" data-reveal-delay="650">
        <h3>CV-të sipas Statusit</h3>
        <apexchart type="pie" :options="cvStatusChartOptions" :series="cvStatusSeries"></apexchart>
      </div>

      <div class="chart-container reveal-on-scroll glassmorphic-card" data-reveal-delay="700">
        <h3>CV-të e Krijuara Gjatë Kohës</h3>
        <apexchart type="line" height="350" :options="cvsCreatedOverTimeOptions" :series="cvsCreatedOverTimeSeries"></apexchart>
      </div>
    </div>

    <div class="cv-list-container page-container animate-in glassmorphic-card">
      <h2 class="reveal-on-scroll">CV-të e Mia të Ruajtura</h2>
      <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">Këtu mund të shikoni, modifikoni, ose fshini CV-të që keni krijuar.</p>

      <div class="message-area">
        <div v-if="error" class="message error" role="alert">{{ error }}</div>
        <div v-if="successMessage" class="message success" role="alert">{{ successMessage }}</div>
      </div>

      <div v-if="loading" class="loading-spinner"></div>

      <div v-else-if="!cvs.length" class="empty-state-container reveal-on-scroll glassmorphic-card" data-reveal-delay="150">
        <i class="fas fa-folder-open empty-state-icon"></i>
        <h3>Nuk Keni CV Ende</h3>
        <p>Duket sikur nuk keni krijuar asnjë CV. Filloni tani duke zgjedhur një model profesional.</p>
        <router-link to="/templates" class="btn-create btn-large btn btn-primary" style="margin-top: 1rem;">
          <i class="fas fa-plus-circle"></i> Krijo CV të Re Tani
        </router-link>
      </div>

      <template v-else>
        <div class="list-controls-header reveal-on-scroll" data-reveal-delay="150">
          <div class="filter-sort-area">
            <div class="form-group">
              <label for="search-cvs"><i class="fas fa-search"></i> Kërko CV:</label>
              <input type="text" id="search-cv-s" v-model="searchTerm" placeholder="Titulli, Emri, Modeli...">
            </div>
            <div class="form-group">
              <label for="sort-cvs"><i class="fas fa-sort-amount-down"></i> Rendit sipas:</label>
              <select id="sort-cvs" v-model="sortKey">
                <option value="updated_at_desc">Më të rejat (Modifikuar)</option>
                <option value="updated_at_asc">Më të vjetrat (Modifikuar)</option>
                <option value="title_asc">Titullit (A-Z)</option>
                <option value="title_desc">Titullit (Z-A)</option>
              </select>
            </div>
        </div>
          <router-link to="/templates" class="btn btn-primary btn-create-list-header">
            <i class="fas fa-plus"></i> Krijo të Re
          </router-link>
        </div>

        <ul class="cv-list" id="cv-list">
          <li v-for="(cv, index) in filteredAndSortedCvs" :key="cv.id" class="cv-item reveal-on-scroll glassmorphic-card" :data-reveal-delay="200 + index * 50">
            <div class="cv-item-main-info">
              <div class="cv-item-icon">
                <i :class="getTemplateIcon(cv.selected_template)"></i>
      </div>
              <div class="cv-info">
                <strong>{{ cv.cv_title || `${cv.emri} ${cv.mbiemri}` || 'CV e Paemërtuar' }}</strong>
                <span class="cv-template">Modeli: {{ cv.selected_template || 'Klasik' }}</span>
                <span class="cv-last-updated">Përditësuar: {{ formatDate(cv.updated_at) }}</span>
              </div>
            </div>
            <div class="cv-actions">
              <router-link :to="`/cv/${cv.id}/edit`" class="btn-icon" title="Redakto">
              <i class="fas fa-edit"></i>
            </router-link>
              <a :href="`/api/cvs/${cv.id}/download`" target="_blank" class="btn-icon" title="Shkarko PDF">
              <i class="fas fa-download"></i>
              </a>
              <button @click="handleDelete(cv.id)" class="btn-icon delete" title="Fshij">
              <i class="fas fa-trash-alt"></i>
            </button>
          </div>
          </li>
        </ul>
      </template>
    </div>
  </main>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
// Ensure ApexCharts is imported if not globally registered, but we did that in app.js
// import VueApexCharts from 'vue3-apexcharts'; 

export default {
  name: 'Dashboard',
  setup() {
    const router = useRouter();
    const cvs = ref([]);
    const loading = ref(true);
    const error = ref('');
    const successMessage = ref('');
    const searchTerm = ref('');
    const sortKey = ref('updated_at_desc');

    const fetchCvs = async () => {
      try {
        const response = await axios.get('/api/my-cvs');
        cvs.value = response.data.cvs;

        console.log("Fetched CVs:", cvs.value);

      } catch (err) {
        if (err.response?.status === 401) {
          router.push('/login');
        } else {
          error.value = 'Ndodhi një gabim gjatë marrjes së CV-ve.';
        }
      } finally {
        loading.value = false;
      }
    };

    const handleDelete = async (id) => {
      if (confirm('Jeni i sigurt që dëshironi të fshini këtë CV? Ky veprim nuk mund të kthehet pas.')) {
        try {
          await axios.delete(`/api/cvs/${id}`);
          cvs.value = cvs.value.filter(cv => cv.id !== id);
          successMessage.value = 'CV u fshi me sukses.';
          setTimeout(() => successMessage.value = '', 3000);
        } catch (err) {
          error.value = 'Ndodhi një gabim gjatë fshirjes së CV-së.';
    }
      }
    };

    const formatDate = (dateString) => {
      const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
      return new Date(dateString).toLocaleDateString('sq-AL', options);
    };

    const getTemplateIcon = (template) => {
      switch (template?.toLowerCase()) {
        case 'modern': return 'fas fa-drafting-compass';
        case 'professional': return 'fas fa-briefcase';
        default: return 'fas fa-file-alt';
      }
    };
    
    const filteredAndSortedCvs = computed(() => {
      let result = [...cvs.value];

      // Filter
      if (searchTerm.value) {
        const lowerSearch = searchTerm.value.toLowerCase();
        result = result.filter(cv =>
          (cv.cv_title || '').toLowerCase().includes(lowerSearch) ||
          (cv.emri || '').toLowerCase().includes(lowerSearch) ||
          (cv.mbiemri || '').toLowerCase().includes(lowerSearch) ||
          (cv.selected_template || '').toLowerCase().includes(lowerSearch)
        );
      }

      // Sort
      const [key, direction] = sortKey.value.split('_');
      result.sort((a, b) => {
        let valA = a[key] || '';
        let valB = b[key] || '';

        if (key === 'updated_at') {
          valA = new Date(valA);
          valB = new Date(valB);
        }
        
        if (valA < valB) return direction === 'asc' ? -1 : 1;
        if (valA > valB) return direction === 'asc' ? 1 : -1;
        return 0;
      });

      return result;
    });

    // New computed properties for dashboard stats
    const totalCvs = computed(() => cvs.value.length);
    const draftCvs = computed(() => cvs.value.filter(cv => cv.is_finalized === false || cv.is_finalized === undefined || cv.is_finalized === null).length); // Account for false, undefined, or null

    // Get most recent CVs, sorted by updated_at (descending)
    const recentCvs = computed(() => {
      return [...cvs.value].sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at)).slice(0, 5);
    });

    // New computed property for CVs by Template Type
    const cvsByTemplate = computed(() => {
      return cvs.value.reduce((acc, cv) => {
        const templateName = cv.selected_template || 'Pa Specifikuar';
        acc[templateName] = (acc[templateName] || 0) + 1;
        return acc;
      }, {});
    });

    console.log("CVs by Template:", cvsByTemplate.value);

    // Chart data for CVs by Status (Pie Chart)
    const cvStatusSeries = computed(() => {
      const finalized = cvs.value.filter(cv => cv.is_finalized === true).length; // Explicitly check for true
      const draft = cvs.value.filter(cv => cv.is_finalized === false || cv.is_finalized === undefined || cv.is_finalized === null).length; // Account for false, undefined, or null
      return [finalized, draft];
    });

    const cvStatusChartOptions = computed(() => ({
      chart: {
        type: 'pie',
        toolbar: { show: false },
      },
      labels: ['Të Finalizuara', 'Në Draft'],
      colors: ['#00E396', '#FF4560'],
      responsive: [{
        breakpoint: 480,
        options: {
          chart: { width: 200 },
          legend: { position: 'bottom' },
        },
      }],
      dataLabels: {
        formatter: function (val, opts) {
          const total = opts.w.globals.series.reduce((a, b) => a + b, 0);
          const percentage = (val / total * 100).toFixed(1);
          return `${opts.w.globals.labels[opts.seriesIndex]}: ${percentage}%`;
        }
      },
      legend: { show: true, position: 'bottom' },
      tooltip: { enabled: true },
    }));

    // Chart data for CVs Created Over Time (Line Chart)
    const cvsCreatedOverTimeSeries = computed(() => {
      const monthlyCounts = {};
      cvs.value.forEach(cv => {
        // Ensure created_at is a valid date string before processing
        if (cv.created_at) {
          const date = new Date(cv.created_at);
          // Check if the date is valid after parsing
          if (!isNaN(date.getTime())) {
            const monthYear = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}`;
            monthlyCounts[monthYear] = (monthlyCounts[monthYear] || 0) + 1;
          }
        }
      });

      const sortedMonths = Object.keys(monthlyCounts).sort();
      const seriesData = sortedMonths.map(month => monthlyCounts[month]);

      return [{
        name: 'CV të Krijuara',
        data: seriesData,
      }];
    });

    const cvsCreatedOverTimeOptions = computed(() => ({
      chart: {
        height: 350,
        type: 'line',
        toolbar: { show: false },
      },
      stroke: { curve: 'smooth' },
      xaxis: {
        // Use sorted months as categories, ensure it's not empty
        categories: Object.keys(cvsCreatedOverTimeSeries.value.length > 0 ? cvsCreatedOverTimeSeries.value[0].data : {}).sort(), // Use sorted months as categories
        labels: {
          formatter: function(val) {
            if (!val) return ''; // Handle empty labels
            const [year, month] = val.split('-');
            const date = new Date(year, parseInt(month, 10) - 1);
            return date.toLocaleString('sq-AL', { month: 'short', year: '2-digit' });
          }
        }
      },
      yaxis: {
        title: { text: 'Numri i CV-ve' },
        min: 0,
        tickAmount: 5,
      },
      tooltip: { enabled: true },
      colors: ['#5E55F8'],
      grid: { show: false },
    }));

    onMounted(() => {
      fetchCvs();
      reinitializeScrollReveal(); // Ensure scroll reveal animations are re-initialized for dashboard content
    });

    return {
      cvs,
      loading,
      error,
      successMessage,
      searchTerm,
      sortKey,
      filteredAndSortedCvs,
      handleDelete,
      formatDate,
      getTemplateIcon,
      totalCvs,
      draftCvs,
      recentCvs,
      cvsByTemplate,
      cvStatusSeries,
      cvStatusChartOptions,
      cvsCreatedOverTimeSeries,
      cvsCreatedOverTimeOptions,
    };
  }
};
</script>

<style scoped>
@reference "tailwindcss/theme";
/* Specific styles for this component can go here if needed */
</style>

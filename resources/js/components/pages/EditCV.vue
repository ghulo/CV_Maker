<template>
  <main class="main">
    <form @submit.prevent="saveCv" class="cv-form page-container animate-in">
      <h2 class="reveal-on-scroll">Shto Eksperiencën, Edukimin & Aftësitë</h2>
      <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">
        Shtoni detajet për përvojën tuaj të punës, edukimin dhe aftësitë. Mund të shtoni më shumë se një hyrje për punë dhe edukim.
      </p>

      <div class="message-area">
        <!-- Error and success messages here -->
      </div>

      <!-- Work Experience Section -->
      <div class="form-section reveal-on-scroll glassmorphic-card" data-reveal-delay="150">
        <h3>Eksperienca e Punës</h3>
        <div v-if="cv.work_experiences && cv.work_experiences.length" class="existing-items-list">
          <h4>Eksperiencat Ekzistuese:</h4>
          <ul>
            <li v-for="work in cv.work_experiences" :key="work.id" class="glassmorphic-card">
              <strong>{{ work.job_title }} te {{ work.company }}</strong>
              <p>{{ work.city_country }} ({{ work.start_date }} - {{ work.end_date || 'Tani' }})</p>
            </li>
          </ul>
          <hr>
        </div>
        <h4>Shto Eksperiencë të Re Pune (opsionale):</h4>
        <div class="form-grid">
          <div class="form-group"><label>Pozita</label><input type="text" v-model="newWork.job_title"></div>
          <div class="form-group"><label>Kompania</label><input type="text" v-model="newWork.company"></div>
          <div class="form-group"><label>Qyteti, Shteti</label><input type="text" v-model="newWork.city_country" placeholder="P.sh. Prishtinë, Kosovë"></div>
          <div class="form-group"><label>Data e Fillimit</label><input type="date" v-model="newWork.start_date"></div>
          <div class="form-group"><label>Data e Mbarimit</label><input type="date" v-model="newWork.end_date" :disabled="newWork.is_current_job"></div>
          <div class="form-group full-width checkbox-group">
            <input type="checkbox" v-model="newWork.is_current_job" @change="newWork.end_date = ''">
            <label>Punë Aktuale</label>
          </div>
          <div class="form-group full-width"><label>Përshkrim i Detyrave</label><textarea rows="4" v-model="newWork.job_description"></textarea></div>
          <div class="form-group full-width">
            <button @click.prevent="addWorkExperience" class="btn btn-secondary" style="align-self: flex-start;">Shto Përvojë</button>
          </div>
        </div>
      </div>

      <!-- Education Section -->
      <div class="form-section reveal-on-scroll glassmorphic-card" data-reveal-delay="200">
        <h3>Edukimi</h3>
        <div v-if="cv.educations && cv.educations.length" class="existing-items-list">
          <h4>Edukimet Ekzistuese:</h4>
          <ul>
            <li v-for="edu in cv.educations" :key="edu.id" class="glassmorphic-card">
              <strong>{{ edu.degree }} - {{ edu.field_of_study }}</strong>
              <p>{{ edu.school }}, {{ edu.city_country }} (Viti: {{ edu.graduation_year }})</p>
            </li>
          </ul>
          <hr>
        </div>
        <h4>Shto Edukim të Ri (opsionale):</h4>
        <div class="form-grid">
            <div class="form-group"><label>Institucioni Arsimor</label><input type="text" v-model="newEducation.school"></div>
            <div class="form-group"><label>Diploma / Certifikata</label><input type="text" v-model="newEducation.degree"></div>
            <div class="form-group"><label>Fusha e Studimit</label><input type="text" v-model="newEducation.field_of_study"></div>
            <div class="form-group"><label>Qyteti, Shteti</label><input type="text" v-model="newEducation.city_country"></div>
            <div class="form-group"><label>Viti i Diplomimit</label><input type="text" v-model="newEducation.graduation_year" placeholder="YYYY ose Në vazhdim"></div>
            <div class="form-group full-width"><label>Përshkrim Shtesë</label><textarea rows="3" v-model="newEducation.edu_description"></textarea></div>
            <div class="form-group full-width">
              <button @click.prevent="addEducation" class="btn btn-secondary" style="align-self: flex-start;">Shto Edukim</button>
            </div>
        </div>
      </div>

      <!-- Interests/Skills Section -->
      <div class="form-section reveal-on-scroll glassmorphic-card" data-reveal-delay="250">
        <h3>Interesat / Aftësitë (opsionale)</h3>
        <div class="form-group full-width">
            <label for="interests_description">Përshkruani interesat, hobitë dhe aftësitë tuaja</label>
            <textarea name="interests_description" id="interests_description" rows="4" v-model="cv.interests" placeholder="P.sh., Lexim, Vullnetarizëm, Sporte, JavaScript, Komunikim..."></textarea>
        </div>
      </div>

      <div class="page-actions form-actions-sticky reveal-on-scroll" data-reveal-delay="300">
        <router-link :to="{ name: 'cv.edit', params: { id: cv.id } }" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Kthehu te Info Personale
        </router-link>
        <button type="submit" class="btn-primary-form btn btn-primary">
          Ruaj dhe Shiko CV-në <i class="fas fa-eye icon-right"></i>
        </button>
      </div>
    </form>
  </main>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useConfirmationModal } from '../../composables/useConfirmationModal.js';

export default {
  name: 'EditCV',
  props: ['id'],
  setup(props) {
    const router = useRouter();
    const { showModal } = useConfirmationModal();

    const cv = ref({
      id: props.id,
      title: '',
      personal_details: {},
      summary: '',
      work_experiences: [],
      educations: [],
      skills: [],
      interests: '',
      selected_template: 'classic',
    });

    const newWork = reactive({ job_title: '', company: '', city_country: '', start_date: '', end_date: '', is_current_job: false, job_description: '' });
    const newEducation = reactive({ school: '', degree: '', field_of_study: '', city_country: '', graduation_year: '', edu_description: '' });
    
    const fetchCv = async () => {
      try {
        const response = await axios.get(`/api/cvs/${props.id}`, { headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }});
        if (response.data.success && response.data.cv) {
            const fetchedCv = response.data.cv;
            cv.value.id = fetchedCv.id;
            cv.value.title = fetchedCv.title || '';
            cv.value.personal_details = fetchedCv.personal_details || {};
            cv.value.summary = fetchedCv.summary || '';
            cv.value.work_experiences = fetchedCv.work_experiences || [];
            cv.value.educations = fetchedCv.educations || [];
            cv.value.skills = fetchedCv.skills || [];
            cv.value.interests = fetchedCv.interests || '';
            cv.value.selected_template = fetchedCv.template_id || 'classic';
        } else {
            console.error("CV data not found or success is false:", response.data);
        }
      } catch (error) {
        console.error("Error fetching CV data:", error.response ? error.response.data : error.message);
      }
    };

    const addWorkExperience = () => {
      cv.value.work_experiences.push({ ...newWork });
      Object.keys(newWork).forEach(key => newWork[key] = key === 'is_current_job' ? false : '');
    };

    const addEducation = () => {
      cv.value.educations.push({ ...newEducation });
      Object.keys(newEducation).forEach(key => newEducation[key] = '');
    };

    const saveCv = async () => {
      try {
        const payload = {
          id: cv.value.id,
          title: cv.value.title,
          personal_details: cv.value.personal_details,
          summary: cv.value.summary,
          work_experiences: cv.value.work_experiences,
          education: cv.value.educations,
          skills: cv.value.skills,
          interests: cv.value.interests,
          template_id: cv.value.selected_template,
        };

        const response = await axios.put(`/api/cvs/${props.id}`, payload, { headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }});
        
        if (response.data.success) {
          await showModal({
            title: 'Sukses!',
            message: 'CV-ja u ruajt me sukses.',
            confirmButtonText: 'OK',
            confirmButtonClass: 'btn-primary',
            cancelButtonText: ''
          });
          router.push({ name: 'cv.preview', params: { id: props.id } });
        } else {
          const errorMessages = response.data.errors ? Object.values(response.data.errors).flat().join('\n') : 'Një gabim i panjohur ndodhi.';
          await showModal({
            title: 'Gabim!',
            message: `Gabim gjatë ruajtjes së CV-së:\n${errorMessages}`,
            confirmButtonText: 'OK',
            confirmButtonClass: 'btn-danger',
            cancelButtonText: ''
          });
        }
      } catch (error) {
        console.error("Error saving CV:", error.response ? error.response.data : error.message);
        let errorMessage = 'Gabim gjatë ruajtjes së CV-së. Ju lutem provoni përsëri.';

        if (error.response && error.response.data && error.response.data.errors) {
          const detailedErrors = Object.values(error.response.data.errors).flat().join('\n');
          errorMessage = `Gabim gjatë ruajtjes së CV-së:\n${detailedErrors}`;
        } else if (error.message) {
          errorMessage = `Gabim i papritur: ${error.message}`;
        }
        
        await showModal({
          title: 'Gabim i Papritur!',
          message: errorMessage,
          confirmButtonText: 'OK',
          confirmButtonClass: 'btn-danger',
          cancelButtonText: ''
        });
      }
    };

    onMounted(() => {
        fetchCv();
        const revealElements = document.querySelectorAll('.reveal-on-scroll');
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = parseInt(entry.target.getAttribute('data-reveal-delay') || '0', 10);
                setTimeout(() => entry.target.classList.add('is-revealed'), delay);
                observer.unobserve(entry.target);
            }
            });
        }, { threshold: 0.1 });
        revealElements.forEach(el => revealObserver.observe(el));
    });

    return { cv, newWork, newEducation, addWorkExperience, addEducation, saveCv };
  },
};
</script>

<style scoped>
@reference "tailwindcss/theme";

.reveal-on-scroll {
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.8s cubic-bezier(0.645, 0.045, 0.355, 1), transform 0.8s cubic-bezier(0.645, 0.045, 0.355, 1);
}
.reveal-on-scroll.is-revealed {
  opacity: 1;
  transform: translateY(0);
}
.existing-items-list {
  margin-bottom: 2rem;
}
.existing-items-list h4 {
  font-weight: 600;
  margin-bottom: 1rem;
}
.existing-items-list ul {
  list-style-type: none;
  padding-left: 0;
}
.existing-items-list li {
  background: var(--surface-raised);
  padding: 1rem;
  border-radius: var(--radius-md);
  margin-bottom: 1rem;
  border: 1px solid var(--border-color-soft);
}
.existing-items-list hr {
  border: none;
  border-top: 1px solid var(--border-color);
  margin: 2rem 0;
}
.checkbox-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.checkbox-group input {
    width: auto;
}
</style>

<template>
  <div class="page-wrapper profile-wrapper">
    <section class="section-spacing">
      <div class="container">
        <header class="section-header text-center">
          <h1 class="section-title">Profili i Përdoruesit</h1>
          <p class="section-description">Menaxhoni detajet dhe cilësimet e llogarisë tuaj.</p>
        </header>

        <div v-if="user" class="profile-layout-grid">
          <div class="profile-main-content">
            <!-- My Details Section -->
            <div class="profile-section card reveal-on-scroll reveal-left">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-user-circle icon"></i>
                  Detajet e Mia
                </h3>
              </div>
              <div class="card-body details-grid">
                <div class="profile-avatar">
                  <i class="fas fa-user"></i>
                </div>
                <div class="details-info">
                  <div class="profile-detail">
                    <strong class="detail-label">Emri:</strong>
                    <span class="detail-value">{{ user.name }}</span>
                  </div>
                  <div class="profile-detail">
                    <strong class="detail-label">Email:</strong>
                    <span class="detail-value">{{ user.email }}</span>
                  </div>
                  <div class="profile-detail">
                    <strong class="detail-label">Anëtarësuar Më:</strong>
                    <span class="detail-value">{{ formatDate(user.created_at) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Change Password Section -->
            <div class="profile-section card reveal-on-scroll reveal-left delay-1">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-lock icon"></i>
                  Ndrysho Fjalëkalimin
                </h3>
              </div>
              <div class="card-body">
                <form @submit.prevent="updatePassword" class="space-y-lg">
                  <div v-if="message" :class="messageClass" class="message" role="alert">
                    <i :class="['icon', messageType === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle']"></i>
                    <p class="message-text">{{ message }}</p>
                  </div>
                  <div class="form-group">
                    <label for="current_password" class="form-label">Fjalëkalimi Aktual</label>
                    <input
                      type="password"
                      id="current_password"
                      v-model="passwordForm.current_password"
                      required
                      placeholder="Vendosni fjalëkalimin aktual"
                      class="form-input"
                    />
                  </div>
                  <div class="form-group">
                    <label for="password" class="form-label">Fjalëkalimi i Ri</label>
                    <input
                      type="password"
                      id="password"
                      v-model="passwordForm.password"
                      required
                      placeholder="Vendosni fjalëkalimin e ri"
                      class="form-input"
                    />
                  </div>
                  <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmo Fjalëkalimin e Ri</label>
                    <input
                      type="password"
                      id="password_confirmation"
                      v-model="passwordForm.password_confirmation"
                      required
                      placeholder="Konfirmo fjalëkalimin e ri"
                      class="form-input"
                    />
                  </div>
                  <button type="submit" :disabled="loading" class="btn btn-primary btn-full-width btn-with-icon">
                    <font-awesome-icon v-if="loading" icon="fa-solid fa-spinner" class="fa-spin icon" />
                    <i v-else class="fas fa-save icon"></i>
                    {{ loading ? 'Duke Ruajtur...' : 'Ndrysho Fjalëkalimin' }}
                  </button>
                </form>
              </div>
            </div>
          </div>

          <div class="profile-sidebar">
            <!-- Danger Zone Section -->
            <div class="profile-section danger-zone card reveal-on-scroll reveal-right">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-exclamation-triangle icon"></i>
                  Zona e Rrezikut
                </h3>
              </div>
              <div class="card-body">
                <h4 class="danger-title">Fshini Llogarinë Tuaj</h4>
                <p class="danger-description">
                  Pasi të fshini llogarinë tuaj, nuk ka kthim mbrapa. Ju lutemi jini të sigurt.
                </p>
                <button @click="confirmDeleteAccount" class="btn btn-danger btn-full-width mt-lg">
                  Fshij Llogarinë
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="loading-spinner-wrapper">
          <i class="fas fa-spinner fa-spin loading-spinner"></i>
          <p class="text-muted mt-md">Duke ngarkuar profilin...</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
  import { ref, onMounted, computed } from 'vue'
  import { useRouter } from 'vue-router'
  import axios from 'axios'
  import { useConfirmationModal } from '../../composables/useConfirmationModal.js'

  const user = ref(null)
  const loading = ref(false)
  const message = ref('')
  const messageType = ref('')
  const router = useRouter()
  const { showModal } = useConfirmationModal()

  const passwordForm = ref({
    current_password: '',
    password: '',
    password_confirmation: '',
  })

  const messageClass = computed(() => {
    if (!message.value) return ''
    return (
      {
        success: 'message success',
        error: 'message error',
      }[messageType.value] || 'message info'
    )
  })

  const fetchUser = async () => {
    try {
      const token = localStorage.getItem('auth_token')
      if (!token) {
        router.push('/login')
        return
      }
      const response = await axios.get('/api/user', {
        headers: { Authorization: `Bearer ${token}` },
      })
      user.value = response.data.user // Assuming API returns { success: true, user: {...} }
    } catch (error) {
      console.error('Error fetching user:', error)
      if (error.response && error.response.status === 401) {
        localStorage.removeItem('auth_token')
        router.push('/login')
      } else {
        message.value = 'Gabim gjatë ngarkimit të të dhënave të përdoruesit.'
        messageType.value = 'error'
      }
    }
  }

  const updatePassword = async () => {
    loading.value = true
    message.value = ''
    messageType.value = ''
    try {
      const token = localStorage.getItem('auth_token')
      const response = await axios.put('/api/user/password', passwordForm.value, {
        headers: { Authorization: `Bearer ${token}` },
      })
      message.value = response.data.message || 'Fjalëkalimi u ndryshua me sukses!'
      messageType.value = 'success'
      passwordForm.value = { current_password: '', password: '', password_confirmation: '' }
    } catch (error) {
      message.value = error.response?.data?.message || 'Ndodhi një gabim.'
      if (error.response?.data?.errors) {
        message.value = Object.values(error.response.data.errors).flat().join(' ')
      }
      messageType.value = 'error'
    } finally {
      loading.value = false
      setTimeout(() => {
        message.value = ''
      }, 6000) // Message disappears after 6 seconds
    }
  }

  const confirmDeleteAccount = async () => {
    const confirmed = await showModal({
      title: 'Konfirmo Fshirjen e Llogarisë',
      message: 'Jeni absolutisht i sigurt që dëshironi të fshini llogarinë tuaj? Ky veprim është i pakthyeshëm dhe do të fshijë përgjithmonë të gjitha të dhënat tuaja, përfshirë CV-të e krijuara.',
      confirmButtonText: 'Fshij Llogarinë',
      cancelButtonText: 'Anulo',
      confirmButtonClass: 'btn-danger',
    })

    if (confirmed) {
      deleteAccount()
    }
  }

  const deleteAccount = async () => {
    loading.value = true
    message.value = ''
    messageType.value = ''
    try {
      const token = localStorage.getItem('auth_token')
      await axios.delete('/api/user', {
        headers: { Authorization: `Bearer ${token}` },
      })
      localStorage.removeItem('auth_token')
      // No need for a modal here, successful deletion means redirect or success page
      router.push({ name: 'home', query: { message: 'Llogaria juaj u fshi me sukses.', type: 'success' } })
    } catch (error) {
      message.value = error.response?.data?.message || 'Ndodhi një gabim gjatë fshirjes së llogarisë tuaj.'
      messageType.value = 'error'
      console.error('Error deleting account:', error)
    } finally {
      loading.value = false
      setTimeout(() => {
        message.value = ''
      }, 6000)
    }
  }

  const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    const options = { year: 'numeric', month: 'long', day: 'numeric' }
    return new Date(dateString).toLocaleDateString(undefined, options)
  }

  onMounted(fetchUser)
</script>

<style scoped>
  /* Page and Section Layout */
  .profile-wrapper {
    padding-top: var(--space-xxl);
    padding-bottom: var(--space-xxl);
  }

  .section-spacing {
    padding: var(--space-section-vertical) var(--space-lg);
  }

  .container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 var(--space-md);
  }

  .section-header {
    margin-bottom: var(--space-xl);
  }

  .section-title {
    font-family: var(--font-heading);
    font-size: var(--font-size-4xl);
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: var(--space-md);
    line-height: 1.2;
    letter-spacing: -0.02em;
  }

  .section-description {
    font-size: var(--font-size-lg);
    color: var(--text-secondary);
    line-height: var(--line-height-normal);
    max-width: 800px;
    margin: 0 auto;
  }

  .profile-layout-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--space-xl);
    margin-top: var(--space-xl);
  }

  @media (min-width: 1024px) {
    .profile-layout-grid {
      grid-template-columns: 2fr 1fr; /* Main content 2/3, sidebar 1/3 */
    }
  }

  .profile-main-content {
    display: flex;
    flex-direction: column;
    gap: var(--space-xl);
  }

  .profile-sidebar {
    display: flex;
    flex-direction: column;
    gap: var(--space-xl);
  }

  .profile-section {
    background-color: var(--bg-surface);
    border: 1px solid var(--border-default);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    box-shadow: var(--shadow);
    transition: var(--transition-all);
  }

  .profile-section .card-header .card-title {
    font-size: var(--font-size-2xl);
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--space-sm);
  }

  .profile-section .card-header .card-title .icon {
    font-size: 1em;
    color: var(--primary);
  }

  /* My Details specific styles */
  .details-grid {
    display: flex;
    align-items: center;
    gap: var(--space-xl);
    margin-top: var(--space-lg);
  }

  .profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: var(--radius-full);
    background-color: var(--neutral-100);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: var(--font-size-4xl);
    color: var(--neutral-400);
  }

  .details-info {
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
  }

  .profile-detail {
    display: flex;
    flex-direction: column;
    font-size: var(--font-size-base);
  }

  .profile-detail .detail-label {
    font-weight: 600;
    color: var(--text-muted);
    margin-bottom: var(--space-xs);
    font-size: var(--font-size-sm);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .profile-detail .detail-value {
    color: var(--text-primary);
    font-size: var(--font-size-md);
  }

  /* Danger Zone specific styles */
  .danger-zone {
    border-color: var(--error);
  }

  .danger-zone .card-header .card-title {
    color: var(--error-dark);
  }

  .danger-zone .card-header .card-title .icon {
    color: var(--error);
  }

  .danger-zone .danger-title {
    font-size: var(--font-size-lg);
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--space-sm);
  }

  .danger-zone .danger-description {
    font-size: var(--font-size-base);
    color: var(--text-secondary);
    line-height: var(--line-height-normal);
  }

  /* Loading State */
  .loading-spinner-wrapper {
    text-align: center;
    padding: var(--space-xxl);
  }

  .loading-spinner {
    font-size: var(--font-size-5xl);
    color: var(--primary);
  }

  /* Messages (re-using global message classes) */
  .message {
    padding: var(--space-md);
    border-radius: var(--radius);
    margin-bottom: var(--space-lg);
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    font-size: var(--font-size-base);
  }

  .message.success {
    background-color: var(--success-light);
    color: var(--success-dark);
    border: 1px solid var(--success);
  }

  .message.error {
    background-color: var(--error-light);
    color: var(--error-dark);
    border: 1px solid var(--error);
  }

  .message .icon {
    font-size: var(--font-size-lg);
    flex-shrink: 0;
  }

  .message-text {
    flex-grow: 1;
  }

  /* Form elements inherit from global forms.css */

  /* Responsive Adjustments */
  @media (max-width: 768px) {
    .section-spacing {
      padding: var(--space-xl) var(--space-md);
    }

    .section-title {
      font-size: var(--font-size-3xl);
    }

    .section-description {
      font-size: var(--font-size-base);
      margin-bottom: var(--space-lg);
    }

    .profile-layout-grid {
      gap: var(--space-lg);
    }

    .details-grid {
      flex-direction: column;
      align-items: center;
      text-align: center;
      gap: var(--space-md);
    }

    .profile-avatar {
      margin-bottom: var(--space-sm);
    }

    .profile-detail {
      align-items: center;
    }

    .profile-section .card-header .card-title {
      font-size: var(--font-size-xl);
    }

    .danger-zone .danger-title,
    .danger-zone .danger-description {
      text-align: center;
    }
  }

  /* Dark Mode Overrides */
  body.dark-theme .profile-section {
    background-color: var(--bg-surface);
    border-color: var(--border-default);
    box-shadow: var(--shadow-dark);
  }

  body.dark-theme .section-title,
  body.dark-theme .profile-section .card-header .card-title,
  body.dark-theme .profile-detail .detail-value,
  body.dark-theme .danger-zone .danger-title {
    color: var(--text-primary);
  }

  body.dark-theme .section-description,
  body.dark-theme .profile-detail .detail-label,
  body.dark-theme .danger-zone .danger-description {
    color: var(--text-secondary);
  }

  body.dark-theme .profile-avatar {
    background-color: var(--neutral-800);
    color: var(--neutral-500);
  }

  body.dark-theme .profile-section .card-header .card-title .icon {
    color: var(--primary-400);
  }

  body.dark-theme .danger-zone {
    border-color: var(--error);
  }

  body.dark-theme .danger-zone .card-header .card-title {
    color: var(--error-light);
  }

  body.dark-theme .danger-zone .card-header .card-title .icon {
    color: var(--error);
  }

  body.dark-theme .loading-spinner {
    color: var(--primary-400);
  }

  body.dark-theme .message.success {
    background-color: var(--success-dark);
    color: white;
    border-color: var(--success);
  }

  body.dark-theme .message.error {
    background-color: var(--error-dark);
    color: white;
    border-color: var(--error);
  }
</style>

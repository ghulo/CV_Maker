<template>
    <div class="page-container profile-container">
        <header class="page-header">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">User Profile</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Manage your account details and settings.</p>
        </header>

        <div v-if="user" class="profile-layout-grid">
            <div class="profile-main-content">
                <!-- My Details Section -->
                <div class="profile-section glassmorphic-card">
                    <h3 class="section-title">
                        <font-awesome-icon icon="fa-solid fa-user-circle" class="mr-3 text-indigo-400" />
                        My Details
                    </h3>
                    <div class="details-grid">
                        <div class="profile-avatar">
                            <font-awesome-icon icon="fa-solid fa-user" class="text-5xl text-gray-400 dark:text-gray-500" />
                        </div>
                        <div class="details-info">
                            <div class="profile-detail">
                                <strong>Name:</strong>
                                <span>{{ user.name }}</span>
                            </div>
                            <div class="profile-detail">
                                <strong>Email:</strong>
                                <span>{{ user.email }}</span>
                            </div>
                            <div class="profile-detail">
                                <strong>Joined:</strong>
                                <span>{{ formatDate(user.created_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Password Section -->
                <div class="profile-section glassmorphic-card">
                    <h3 class="section-title">
                        <font-awesome-icon icon="fa-solid fa-lock" class="mr-3 text-indigo-400" />
                        Change Password
                    </h3>
                    <form @submit.prevent="updatePassword" class="space-y-6">
                        <div v-if="message" :class="messageClass" class="message" role="alert">
                            {{ message }}
                        </div>
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" v-model="passwordForm.current_password" required placeholder="Enter your current password">
                        </div>
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" id="password" v-model="passwordForm.password" required placeholder="Enter your new password">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input type="password" id="password_confirmation" v-model="passwordForm.password_confirmation" required placeholder="Confirm your new password">
                        </div>
                        <button type="submit" :disabled="loading" class="btn btn-primary w-full">
                            <font-awesome-icon v-if="loading" icon="fa-solid fa-spinner" class="fa-spin mr-2" />
                            {{ loading ? 'Saving...' : 'Update Password' }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="profile-sidebar">
                <!-- Danger Zone Section -->
                <div class="profile-section danger-zone glassmorphic-card">
                    <h3 class="section-title text-red-500">
                        <font-awesome-icon icon="fa-solid fa-exclamation-triangle" class="mr-3" />
                        Danger Zone
                    </h3>
                    <div>
                        <h4 class="font-semibold text-gray-800 dark:text-white">Delete Your Account</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Once you delete your account, there is no going back. Please be certain.</p>
                    </div>
                    <button @click="confirmDeleteAccount" class="btn btn-danger w-full mt-4">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-20">
            <font-awesome-icon icon="fa-solid fa-spinner" class="text-5xl text-indigo-500 fa-spin" />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const user = ref(null);
const loading = ref(false);
const message = ref('');
const messageType = ref('');
const router = useRouter();

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const messageClass = computed(() => {
    if (!message.value) return '';
    return {
        'success': 'message success',
        'error': 'message error',
    }[messageType.value] || 'message info';
});

const fetchUser = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        router.push('/login');
        return;
    }
    const response = await axios.get('/api/user', {
      headers: { Authorization: `Bearer ${token}` },
    });
    user.value = response.data;
  } catch (error) {
    console.error('Error fetching user:', error);
    if (error.response && error.response.status === 401) {
        localStorage.removeItem('auth_token');
        router.push('/login');
    }
  }
};

const updatePassword = async () => {
  loading.value = true;
  message.value = '';
  try {
    const token = localStorage.getItem('auth_token');
    const response = await axios.put('/api/user/password', passwordForm.value, {
      headers: { Authorization: `Bearer ${token}` },
    });
    message.value = response.data.message || 'Password updated successfully!';
    messageType.value = 'success';
    passwordForm.value = { current_password: '', password: '', password_confirmation: '' };
  } catch (error) {
    message.value = error.response?.data?.message || 'An error occurred.';
    if (error.response?.data?.errors) {
        message.value = Object.values(error.response.data.errors).flat().join(' ');
    }
    messageType.value = 'error';
  } finally {
    loading.value = false;
    setTimeout(() => { message.value = '' }, 6000);
  }
};

const confirmDeleteAccount = () => {
    // Replace default confirm with a more robust modal in a future enhancement
    if (window.confirm('Are you absolutely sure you want to delete your account? This action is irreversible and will permanently delete all your data.')) {
        deleteAccount();
    }
}

const deleteAccount = async () => {
    try {
        const token = localStorage.getItem('auth_token');
        await axios.delete('/api/user', {
            headers: { Authorization: `Bearer ${token}` },
        });
        localStorage.removeItem('auth_token');
        // Optionally, show a global message via a store or event bus
        router.push('/');
    } catch (error) {
        message.value = error.response?.data?.message || 'There was an error deleting your account.';
        messageType.value = 'error';
    }
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

onMounted(fetchUser);

</script>

<style scoped>
/* Using a more component-specific and BEM-like approach for clarity */
.profile-container {
    max-width: 1200px;
    margin: auto;
}

.page-header {
    text-align: center;
    margin-bottom: 2rem;
}

.profile-layout-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
}

@media (min-width: 1024px) {
    .profile-layout-grid {
        grid-template-columns: 2fr 1fr;
    }
}

.profile-main-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.profile-section {
    padding: 2rem;
    transition: all 0.3s ease-in-out;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--divider-color);
    display: flex;
    align-items: center;
}

.details-grid {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background-color: var(--neutral-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.details-info {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.profile-detail {
    display: flex;
    flex-direction: column;
    font-size: 0.95rem;
}

.profile-detail strong {
    font-weight: 600;
    color: var(--muted-text);
    margin-bottom: 0.15rem;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.profile-detail span {
    color: var(--neutral-text);
    font-size: 1rem;
}

body.dark-theme .profile-detail span {
    color: var(--dark-neutral-text);
}


.danger-zone .section-title {
    color: #ef4444; /* red-500 */
    border-color: #ef4444;
}

body.dark-theme .danger-zone .section-title {
    color: #f87171; /* red-400 */
    border-color: #f87171;
}

.danger-zone h4 {
    font-size: 1.1rem;
    font-weight: 600;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input {
    width: 100%;
    padding: 0.75rem 1rem;
    border-radius: var(--radius-sm);
    border: 1px solid var(--neutral-border);
    background-color: var(--neutral-bg);
    color: var(--neutral-text);
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-group input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--form-focus-glow-light);
}

body.dark-theme .form-group input {
    background-color: #1f2937; /* gray-800 */
    border-color: #4b5563; /* gray-600 */
    color: var(--dark-neutral-text);
}

body.dark-theme .form-group input:focus {
    border-color: var(--dark-primary);
    box-shadow: 0 0 0 3px var(--form-focus-glow-dark);
}

.message {
    padding: 0.75rem 1.25rem;
    border-radius: var(--radius-sm);
    text-align: center;
    font-weight: 500;
    border: 1px solid transparent;
}

.message.success {
    background-color: var(--message-success-bg);
    color: var(--message-success-color);
    border-color: var(--message-success-border);
}

.message.error {
    background-color: var(--message-error-bg);
    color: var(--message-error-color);
    border-color: var(--message-error-border);
}

body.dark-theme .message.success {
    background-color: var(--dark-message-success-bg);
    color: var(--dark-message-success-color);
    border-color: var(--dark-message-success-border);
}

body.dark-theme .message.error {
    background-color: var(--dark-message-error-bg);
    color: var(--dark-message-error-color);
    border-color: var(--dark-message-error-border);
}

</style>

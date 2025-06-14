<template>
  <div class="profile-container animate-in">
    <h2>User Profile</h2>
    <div v-if="user" class="profile-layout-grid">
      <!-- User Details Section -->
      <div class="profile-section">
        <h3>My Details</h3>
        <div class="profile-detail">
          <strong>Name:</strong> {{ user.name }}
        </div>
        <div class="profile-detail">
          <strong>Email:</strong> {{ user.email }}
        </div>
        <div class="profile-detail">
          <strong>Joined:</strong> {{ formatDate(user.created_at) }}
        </div>
      </div>

      <!-- Change Password Section -->
      <div class="profile-section">
        <h3>Change Password</h3>
        <form @submit.prevent="updatePassword">
          <div v-if="message" :class="['message', messageType]">{{ message }}</div>
          <div class="form-group">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" v-model="passwordForm.current_password" required class="form-control">
          </div>
          <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" v-model="passwordForm.password" required class="form-control">
          </div>
          <div class="form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" id="password_confirmation" v-model="passwordForm.password_confirmation" required class="form-control">
          </div>
          <button type="submit" class="btn btn-primary" :disabled="loading">
            <span v-if="loading">Saving...</span>
            <span v-else>Update Password</span>
          </button>
        </form>
      </div>
    </div>
    <div v-else class="loading-spinner">
      <i class="fas fa-spinner fa-spin"></i>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
  name: 'Profile',
  setup() {
    const user = ref(null);
    const loading = ref(false);
    const message = ref('');
    const messageType = ref('');

    const passwordForm = ref({
      current_password: '',
      password: '',
      password_confirmation: '',
    });

    const fetchUser = async () => {
      try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.get('/api/user', {
          headers: { Authorization: `Bearer ${token}` },
        });
        user.value = response.data;
      } catch (error) {
        console.error('Error fetching user:', error);
      }
    };

    const updatePassword = async () => {
      loading.value = true;
      message.value = '';
      try {
        const token = localStorage.getItem('auth_token');
        await axios.put('/api/profile', passwordForm.value, {
          headers: { Authorization: `Bearer ${token}` },
        });
        message.value = 'Password updated successfully!';
        messageType.value = 'success';
        // Clear form
        passwordForm.value.current_password = '';
        passwordForm.value.password = '';
        passwordForm.value.password_confirmation = '';
      } catch (error) {
        message.value = error.response?.data?.message || 'An error occurred.';
        messageType.value = 'error';
        console.error('Error updating password:', error);
      } finally {
        loading.value = false;
      }
    };

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    };

    onMounted(fetchUser);

    return {
      user,
      loading,
      passwordForm,
      updatePassword,
      formatDate,
      message,
      messageType,
    };
  },
};
</script>

<style>.page-container { padding: 40px; }</style>

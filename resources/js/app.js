import './bootstrap';
import '../css/app.css';
import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router';
import VueApexCharts from 'vue3-apexcharts';
import { initializeEffects } from './script'; // Import initializeEffects

const app = createApp(App);
app.use(router);
app.use(VueApexCharts);

// Wait until the router is ready before mounting the app
// This ensures all async components are loaded before the app is displayed.
router.isReady().then(() => {
  app.mount('#vue-app');
  
  // Initialize global effects after the app is mounted and router is ready
  initializeEffects();
}); 
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
app.mount('#vue-app');

// Initialize global effects after the app is mounted
initializeEffects(); 
import './bootstrap';
import '../css/app.css';
import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router';
import VueApexCharts from 'vue3-apexcharts';

/* import font awesome icon component */
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { fas } from '@fortawesome/free-solid-svg-icons'

import { initializeEffects } from './script'; // Import initializeEffects

library.add(fas);

const app = createApp(App);
app.use(router);
app.use(VueApexCharts);
app.component('font-awesome-icon', FontAwesomeIcon);

// Wait until the router is ready before mounting the app
// This ensures all async components are loaded before the app is displayed.
router.isReady().then(() => {
  app.mount('#vue-app');
  
  // Initialize global effects after the app is mounted and router is ready
  initializeEffects();
}); 
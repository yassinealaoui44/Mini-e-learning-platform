import '../css/app.css';
import './bootstrap';
import { createApp } from 'vue';
import App from '@/src/App.vue';
import router from '@/src/router';
import { installI18n } from '@/i18n';
import { initializeSession } from '@/src/stores/session';
import '@/src/styles/spa.css';

const app = createApp(App);

installI18n(app);
app.use(router);

initializeSession();

app.mount('#app');

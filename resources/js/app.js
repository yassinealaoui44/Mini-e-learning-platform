import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';

// 🚀 ADD THIS LINE HERE:
const appName = 'E-Learning'; 

createInertiaApp({
    // Now 'appName' is defined, so this line won't crash anymore
    title: (title) => `${title} - ${appName}`, 
    
    resolve: (name) => resolvePageComponent(
        `./Pages/${name}.vue`, 
        import.meta.glob('./Pages/**/*.vue')
    ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
});
import './bootstrap';
import '../css/app.css';
import 'flowbite'; 
import 'element-plus/dist/index.css' 

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import ElementPlus from 'element-plus'
import { createI18n } from 'vue-i18n'
import English from './Pages/lang/english';
import Khmer from './Pages/lang/khmer';




const i18n = createI18n({
  legacy: false, // using Composition API
  locale: 'km',
  fallbackLocale: 'en',
  globalInjection: true,
  messages: {
    en: English,
    km: Khmer
  }
})


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(ElementPlus)
            .use(i18n)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },

});

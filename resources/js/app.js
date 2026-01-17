import "./bootstrap";
import '../css/app.css';
import '@fontsource/open-sans/index.css';
import '@fontsource/ubuntu/index.css';
if (typeof global === 'undefined') {
    window.global = window;
}
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { i18nVue } from "laravel-vue-i18n";
import { route as ziggyRoute } from 'ziggy-js';
import { Inertia } from '@inertiajs/inertia';
import { createPinia } from "pinia";
import axios from "axios";

// FontAwesome
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faPencilAlt, faTrashCan } from "@fortawesome/free-solid-svg-icons";
import { faXTwitter } from '@fortawesome/free-brands-svg-icons';
library.add(faPencilAlt, faTrashCan, faXTwitter);

// ToastBus
import { toastBus } from './utils/toastBus';
import Toast from './Application/Components/Content/Toast.vue';

window.toast = (type, message, duration = 5000) => // Dauer auf realistische Zahl setzen
    toastBus.emit({ type, message, duration });

// Sichere Ziggy-Route
function safeRoute(...args) {
    try {
        return ziggyRoute(...args);
    } catch (error) {
        if (error.message.includes('is not in the route list')) {
            console.error(`Ziggy: Route '${args[0]}' nicht gefunden. Leite auf 404 um.`);
            Inertia.visit('/404');
            return '/404';
        }
        throw error;
    }
}
window.route = safeRoute;

// Tooltip Plugin
import { TippyPlugin } from "tippy.vue";

// Rechte-Logik
import { loadAllRights, hasRight, isRightsReady } from "@/utils/rights";

// Axios CSRF absichern


axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
  console.error('CSRF token not found!');
}
axios.defaults.withCredentials = true;


// Rechte laden und App starten
loadAllRights().then(() => {
    const appName = window.app_name || "Starter Eleven";

    createInertiaApp({
        title: title => `${title} - ${appName}`,
        resolve: name =>
            resolvePageComponent(`./Application/${name}.vue`, import.meta.glob("./Application/**/*.vue")),
        setup({ el, App, props, plugin }) {
            if (!el) {
                console.error("Inertia mount Element #app nicht gefunden!");
                return;
            }

            const app = createApp({ render: () => h(App, props) });

            // Pinia
            app.use(createPinia());

            // Plugins
            app.use(plugin)
               .use(ZiggyVue)
               .use(i18nVue, {
                   resolve: async lang => {
                       const langs = import.meta.glob("../../lang/*.json");
                       if (!langs[`../../lang/${lang}.json`]) return {};
                       return await langs[`../../lang/${lang}.json`]();
                   },
               })
               .use(TippyPlugin);

            // Globale Rechtefunktionen
            app.config.globalProperties.$hasRight = hasRight;
            app.config.globalProperties.$isRightsReady = isRightsReady;

            // Globale Komponenten
            app.component("font-awesome-icon", FontAwesomeIcon);
            app.component("ToastB", Toast);

            // App mounten
            app.mount(el);

            // Einmaliger Reload nach Logout
            if (props.flash?.needsReload && !sessionStorage.getItem('needsReload')) {
                sessionStorage.setItem('needsReload', '1');
                window.location.reload();
            } else if (sessionStorage.getItem('needsReload')) {
                sessionStorage.removeItem('needsReload');
            }

            // Dark Mode laden
            async function loadDarkMode() {
                let mode = localStorage.getItem("theme") || "light";
                try {
                    const response = await fetch("/api/dark-mode");
                    const data = await response.json();
                    mode = data.darkMode || mode;
                } catch (error) {
                    console.warn("Dark Mode API Fehler, Standard wird benutzt:", error);
                }
                localStorage.setItem("theme", mode);
                document.documentElement.setAttribute("data-theme", mode);
                document.querySelectorAll(".ai-button").forEach(aibut => {
                    aibut.src = `/images/icons/ai-${mode}.png`;
                });
            }
            loadDarkMode(); // Direkt ausführen, kein DOMContentLoaded nötig bei Vue

            // URL-Parameter parsen
            window.$_GET = {};
            const query = window.location.search.substring(1);
            if (query) {
                query.split('&').forEach(param => {
                    const [key, val] = param.split('=').map(decodeURIComponent);
                    window.$_GET[key] = val;
                });
            }

            // Vor Verlassen Session aufräumen
            let oldUrl = window.location.href;
            window.addEventListener('beforeunload', function () {
                const newUrl = document.activeElement?.href || '';
                if (newUrl && newUrl !== oldUrl) navigator.sendBeacon('/unset-session.php');
            });
        },
        progress: {
            color: "#0EA5E9",
        },
    });
}).catch(error => {
    console.error("Fehler beim Laden der Benutzerrechte: ", error);
});


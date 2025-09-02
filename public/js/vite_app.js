import "./bootstrap";
import '../css/app.css';

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { i18nVue } from "laravel-vue-i18n";
import { route as ziggyRoute } from 'ziggy-js';
import { Inertia } from '@inertiajs/inertia';
import { createPinia } from "pinia";

// FontAwesome
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faPencilAlt, faTrashCan } from "@fortawesome/free-solid-svg-icons";
import { faXTwitter } from '@fortawesome/free-brands-svg-icons';

library.add(faPencilAlt, faTrashCan, faXTwitter);

// Globale Ziggy-Route mit Fallback
function safeRoute(...args) {
    try {
        return ziggyRoute(...args);
    } catch (error) {
        if (error.message.includes('is not in the route list')) {
            console.warn(`Ziggy: Route '${args[0]}' nicht gefunden. Leite auf 404 um.`);
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

// Schriftarten
import "@fontsource/open-sans/index.css";
import "@fontsource/ubuntu/index.css";

// Axios CSRF
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Rechte laden und Inertia-App starten
loadAllRights().then(() => {
    const appName = import.meta.env.VITE_APP_NAME || "Starter Eleven";

    createInertiaApp({
        title: (title) => `${title} - ${appName}`,
        resolve: (name) =>
            resolvePageComponent(
                `./Application/${name}.vue`,
                import.meta.glob("./Application/**/*.vue")
            ),
        setup({ el, App, props, plugin }) {
            const app = createApp({ render: () => h(App, props) });

            // Pinia
            app.use(createPinia());

            // Plugins
            app.use(plugin)
               .use(ZiggyVue)
               .use(i18nVue, {
                    resolve: async (lang) => {
                        const langs = import.meta.glob("../../lang/*.json");
                        return await langs[`../../lang/${lang}.json`]();
                    },
               })
               .use(TippyPlugin);

            // Globale Rechtefunktionen
            app.config.globalProperties.$hasRight = hasRight;
            app.config.globalProperties.$isRightsReady = isRightsReady;

            // Globale Komponenten
            app.component("font-awesome-icon", FontAwesomeIcon);

            // App mounten
            app.mount(el);

            // === EINMALIGER RELOAD NACH LOGOUT ===
            if (props.flash?.needsReload && !sessionStorage.getItem('needsReload')) {
                sessionStorage.setItem('needsReload', '1');
                console.log("Reload nach Logout");
                window.location.reload();
            } else if (sessionStorage.getItem('needsReload')) {
                // Nach einmaligem Reload Flag entfernen
                sessionStorage.removeItem('needsReload');
            }
            // ======================================

            // Dark Mode laden
            async function loadDarkMode() {
                let mode = localStorage.getItem("theme") || "light";
                if (window.darkMode) mode = window.darkMode;
                else {
                    try {
                        const response = await fetch("/api/dark-mode");
                        const data = await response.json();
                        mode = data.darkMode || "light";
                    } catch (error) {
                        console.error("Fehler beim Laden des Dark Modes:", error);
                    }
                }
                localStorage.setItem("theme", mode);
                document.documentElement.setAttribute("data-theme", mode);
                document.querySelectorAll(".ai-button").forEach(aibut => {
                    aibut.src = `/images/icons/ai-${mode}.png`;
                });
            }
            document.addEventListener("DOMContentLoaded", loadDarkMode);

            // Optional: URL-Parameter parsen
            window.$_GET = {};
            if (document.location.toString().includes('?')) {
                document.location.toString()
                    .replace(/^.*?\?/, '')
                    .replace(/#.*$/, '')
                    .split('&')
                    .forEach(param => {
                        const [key, val] = decodeURIComponent(param).split('=');
                        window.$_GET[key] = val;
                    });
            }

            // Vor dem Verlassen Session aufräumen
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

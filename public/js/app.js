import "./bootstrap";
import '../css/app.css';
import 'shariff';

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { i18nVue } from "laravel-vue-i18n";
// import { route } from "ziggy-js";
// import { getapp} from "helpers";
import { createPinia } from "pinia";  // <--- Pinia importieren

// Schriftarten
import "@fontsource/open-sans/index.css";
import "@fontsource/ubuntu/index.css";

// Rechte-Logik
import { loadAllRights, hasRight, isRightsReady } from "@/utils/rights";

// Tooltip Plugin
import { TippyPlugin } from "tippy.vue";

// FontAwesome
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPencilAlt, faTrashCan } from "@fortawesome/free-solid-svg-icons";
library.add(faPencilAlt, faTrashCan);

const appName = window.app_name	 || "Starter Eleven";

// Rechte laden
loadAllRights()
  .then(() => {
    // Erstelle Inertia-App
    createInertiaApp({
      title: (title) => `${title} - ${appName}`,
      resolve: (name) =>
        resolvePageComponent(
          `./Application/${name}.vue`,
          import.meta.glob("./Application/**/*.vue")
        ),
      setup({ el, App, props, plugin }) {
        // App erstellen
        const app = createApp({ render: () => h(App, props) });

        // Pinia verwenden
        const pinia = createPinia();
        app.use(pinia); // <--- Pinia als Plugin registrieren

        // Globale Rechtefunktionen registrieren
        app.config.globalProperties.$hasRight = hasRight;
        app.config.globalProperties.$isRightsReady = isRightsReady;

        // Plugins hinzufügen
        app
          .use(plugin)
          .use(ZiggyVue)
          .use(i18nVue, {
            resolve: async (lang) => {
              const langs = import.meta.glob("../../lang/*.json");
              return await langs[`../../lang/${lang}.json`]();
            },
          })
          .use(TippyPlugin, {
            tippyDefaults: {}, // Tooltip-Einstellungen
          });

        // Globale Komponenten
        app.component("font-awesome-icon", FontAwesomeIcon);

        // App mounten
        app.mount(el);
      },
      progress: {
        color: "#0EA5E9", // Fortschrittsbalken-Farbe
      },
    });
  })
  .catch(() => {
    // Fehlerbehandlung
    return "";
    // const fallback = document.createElement("div");
    // fallback.innerHTML = `
    //   <div style="padding:2rem;text-align:center;font-family:sans-serif">
    //     <h2>Fehler beim Laden der Benutzerrechte</h2>
    //     <p>Bitte laden Sie die Seite neu oder melden Sie sich erneut an.</p>
    //   </div>
    // `;
    // document.body.appendChild(fallback);
    // console.error("Fehler beim Laden der Benutzerrechte: ", error);
  });

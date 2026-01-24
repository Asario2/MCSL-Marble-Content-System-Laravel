<template>
  <div
    v-if="isLoading"
    id="loader"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-all"
    style="z-index:999999999"
  >
    <div class="text-center">
      <svg
        class="animate-spin h-10 w-10 text-primary-sun-500"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
      </svg>
      <p class="mt-4 text-primary-sun-100 text-sm">Bitte warten...</p>
    </div>
  </div>
  sdf
</template>

<script>
console.error("🔥 LOADER FILE WIRD GELADEN 🔥");

import axios from "axios";

export default {
  name: "Loader",

  data() {
     return {
    isLoading: false,
    pendingRequests: 0,
    loadingTimer: null,
    loaderEnabled: true, // 🔑 DAS IST DER SCHALTER
  };
  },

  mounted() {
    console.log("🟢 Loader mounted");
    this.monitorAxios();

    // ⏱ Initialen Seitenload nach kurzer Zeit beenden
    setTimeout(() => {
    this.loaderEnabled = false;
    this.pendingRequests = 0;
    this.hideLoader();
    console.log("🟢 Initial Load abgeschlossen → Loader deaktiviert");
  }, 800);
  },

  methods: {
    showLoaderWithDelay() {
      if (!this.loaderEnabled) {
        console.log("🚫 Loader blockiert (loaderEnabled = false)");
        return;
      }

      if (this.loadingTimer) return;

      console.log("⏳ Loader Delay gestartet");

      this.loadingTimer = setTimeout(() => {
        console.log("🔵 Loader sichtbar");
        this.isLoading = true;
      }, 150);
    },

    hideLoader() {
      console.log("🟣 Loader verstecken");

      clearTimeout(this.loadingTimer);
      this.loadingTimer = null;
      this.isLoading = false;
    },

    setLoading(state) {
      console.log("🔄 setLoading:", state);
      state ? this.showLoaderWithDelay() : this.hideLoader();
    },

    checkLoading() {
      console.log("🔍 checkLoading", this.pendingRequests);

      if (this.pendingRequests <= 0) {
        this.pendingRequests = 0;
        this.setLoading(false);
      }
    },

    monitorAxios() {
      axios.interceptors.request.use((config) => {
        console.log("⬆️ Axios Request", {
          url: config.url,
          skipLoading: config.skipLoading,
          loaderEnabled: this.loaderEnabled,
        });

        // 🔕 explizit kein Loader
        if (config.skipLoading === true) {
          console.log("🔕 skipLoading aktiv → kein Loader");
          return config;
        }

        if (!this.loaderEnabled) {
          console.log("🚫 Loader global deaktiviert");
          return config;
        }

        this.pendingRequests++;
        console.log("➕ pendingRequests:", this.pendingRequests);

        this.setLoading(true);
        return config;
      });

      axios.interceptors.response.use(
        (response) => {
          if (!response.config?.skipLoading && this.loaderEnabled) {
            this.pendingRequests--;
            console.log("⬇️ Axios Response", {
              url: response.config.url,
              pending: this.pendingRequests,
            });
            this.checkLoading();
          }
          return response;
        },
        (error) => {
          if (!error.config?.skipLoading && this.loaderEnabled) {
            this.pendingRequests--;
            console.log("❌ Axios Error", {
              url: error.config?.url,
              pending: this.pendingRequests,
            });
            this.checkLoading();
          }
          return Promise.reject(error);
        }
      );
    },
        monitorInertia() {
      console.log("🟣 monitorInertia aktiviert");

      Inertia.on("start", (event) => {
        const skip = event.detail?.visit?.skipLoading;
        console.log("🚀 Inertia start", { skip });

        if (skip || !this.initialLoadDone) return;

        this.pendingRequests++;
        this.showLoaderWithDelay();
      });

      Inertia.on("finish", (event) => {
        const skip = event.detail?.visit?.skipLoading;
        console.log("🏁 Inertia finish", { skip });

        if (skip || !this.initialLoadDone) return;

        this.pendingRequests--;
        this.checkLoading();
      });
    },

  },
};
</script>

<template>
  <div
    v-if="isLoading"
    id="loader"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-all"
    style="z-index:999999999"
  >
    <div class="text-center">
      <svg
        class="animate-spin h-10 w-10 text-primary-sun-500 mx-auto"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        />
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8v8H4z"
        />
      </svg>

      <p class="mt-4 text-primary-sun-100 text-sm">
        Bitte warten...
      </p>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { Inertia } from "@inertiajs/inertia";

console.error("🔥 LOADER FILE WIRD GELADEN 🔥");

export default {
  name: "Loader",

  data() {
    return {
      isLoading: false,
      pendingRequests: 0,
      loadingTimer: null,
      initialLoadDone: false,
    };
  },

  mounted() {
    console.log("🟢 Loader mounted");

    // Axios Interceptors nur EINMAL
    if (!axios.__LOADER_INSTALLED__) {
      axios.__LOADER_INSTALLED__ = true;
      this.monitorAxios();
      console.log("✅ Axios Loader Interceptor installiert");
    }

    // Inertia Events
    this.monitorInertia();

    // ⏱ Initialen Seitenload sauber beenden
    setTimeout(() => {
      this.initialLoadDone = true;
      this.pendingRequests = 0;
      this.hideLoader();
      console.log("🟢 Initial Load abgeschlossen");
    }, 800);
  },

  methods: {
    showLoaderWithDelay() {
      if (this.loadingTimer || this.isLoading) return;

      console.log("⏳ Loader Delay gestartet");

      this.loadingTimer = setTimeout(() => {
        this.isLoading = true;
        console.log("🔵 Loader sichtbar");
      }, 150);
    },

    hideLoader() {
      console.log("🟣 Loader verstecken");

      clearTimeout(this.loadingTimer);
      this.loadingTimer = null;
      this.isLoading = false;
    },

    checkLoading() {
      console.log("🔍 checkLoading", this.pendingRequests);

      if (this.pendingRequests <= 0) {
        this.pendingRequests = 0;
        this.hideLoader();
      }
    },

    monitorAxios() {
      axios.interceptors.request.use((config) => {
        console.log("⬆️ Axios Request", {
          url: config.url,
          skipLoading: config.skipLoading,
        });

        if (config.skipLoading === true) {
          console.log("🔕 skipLoading aktiv → kein Loader");
          return config;
        }

        this.pendingRequests++;
        console.log("➕ pendingRequests:", this.pendingRequests);

        this.showLoaderWithDelay();
        return config;
      });

      axios.interceptors.response.use(
        (response) => {
          if (!response.config?.skipLoading) {
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
          if (!error.config?.skipLoading) {
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

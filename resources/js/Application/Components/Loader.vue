<template>
        <div v-if="isLoading" id="loader" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-all" style='z-index:999999999'>
        <div class="text-center">
            <svg class="animate-spin h-10 w-10 text-primary-sun-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <p class="mt-4 text-primary-sun-100 text-sm">Bitte warten...</p>
        </div>
        </div>
  </template>

  <script>
  import throttle from 'lodash/throttle';
  import axios from "axios";
  export default {
    name: 'Loader',

    data() {
      return {
        isLoading: true,
        pendingRequests: 0,
        imagesLoaded: false,
      };
    },

    mounted() {
        const params = new URLSearchParams(window.location.search);
    const search = params.get("search");

    // Wenn search gesetzt ist, verstecke das Loading-Div
    if (search && search.trim() !== "") {

      this.isLoading = false;
      this.loading = false;
    }
    else{
        this.isLoading = true;
    }
      this.monitorAxios();
      this.waitForImagesToLoad();
//       this.isLoading = true;
//   setTimeout(() => this.isLoading = false, 3000);

    },
    watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.loading = true;

        // Entfernt leere Werte
        const query = pickBy(this.form, (value) => value !== "" && value !== null);

        // Falls kein Wert mehr übrig ist, search explizit auf null setzen
        const params = Object.keys(query).length
          ? { ...query, table: this.table }
          : { search: null, table: this.table };

        // Inertia Request
        Inertia.get(
          this.route("admin.tables.show", params),
          this.form,
          { preserveState: true, replace: true }
        );

        this.loading = false;
      }, 500), // 500ms Throttle
    },
  },


    methods: {
      setLoading(state) {
        this.isLoading = state;
      },

      checkLoading() {
        if (this.pendingRequests <= 0 && this.imagesLoaded) {
          this.setLoading(false);
        }
      },

      monitorAxios() {
        axios.interceptors.request.use((config) => {
          this.pendingRequests++;
          this.setLoading(true);
          return config;
        });

        axios.interceptors.response.use(
          (response) => {
            this.pendingRequests--;
            this.checkLoading();
            return response;
          },
          (error) => {
            this.pendingRequests--;
            this.checkLoading();
            return Promise.reject(error);
          }
        );
      },

      waitForImagesToLoad() {
        const images = document.querySelectorAll('img');
        const totalImages = images.length;
        let loadedCount = 0;

        if (totalImages === 0) {
          this.imagesLoaded = true;
          this.checkLoading();
          return;
        }

        const onImageLoad = () => {
          loadedCount++;
          if (loadedCount === totalImages) {
            this.imagesLoaded = true;
            this.checkLoading();
          }
        };

        images.forEach((img) => {
          if (img.complete) {
            onImageLoad();
          } else {
            img.addEventListener('load', onImageLoad);
            img.addEventListener('error', onImageLoad);
          }
        });
      },
    }
  };
  </script>


<template>
    <div>
      <!-- Veröffentlicht -->
      <button v-if="isPublished" @click="togglePub" title="Veröffentlicht">
        <svg class="w-6 h-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
          <circle cx="12" cy="12" r="10"/>
        </svg>
      </button>

      <!-- Unveröffentlicht -->
      <button v-else @click="togglePub" title="Unveröffentlicht">
        <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
          <circle cx="12" cy="12" r="10"/>
        </svg>
      </button>
    </div>
  </template>

  <script>
  import axios from "axios";
  import { route } from "ziggy-js";

  export default {
    name: "TogglePublish",
    props: {
      table: { type: String, required: true },   // z. B. "articles"
      id: { type: Number, required: true },      // Datensatz-ID
      published: { type: Boolean, default: false }, // aktueller Wert aus DB
      public: {type:Number},
    },
    data() {
      return {
        isPublished: this.published,
        loading: false
      };
    },
    methods: {
      async togglePub() {
        if (this.loading) return;
        //this.loading = true;

        const newStatus = this.isPublished ? 0 : 1;

        try {
          const response = await axios.post(route("toggle.pub"), {
            table: this.table,
            id: this.id,
            pub: newStatus,
            public: this.public,
          });

          this.isPublished = newStatus; // UI aktualisieren
//           console.log("Gespeichert:", response.data);
        } catch (error) {
          console.error("Fehler beim Speichern:", error.response?.data || error.message);
        } finally {
          this.loading = false;
        }
      }
    }
  };
  </script>


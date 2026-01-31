<template>
    <div class="relative">
      <input
        type="text"
        v-model="searchQuery"
        @input="fetchResults"
        placeholder="Suchen..."
        class="w-full p-2 border rounded-lg bg-layout-sun-100 dark:bg-layout-night-200 dark:text-white"
      />
      <div v-if="loading" class="absolute right-3 top-2 text-gray-500 dark:text-gray-400">
        ⏳
      </div>

      <ul v-if="results.length" class="mt-2 border rounded-lg bg-white dark:bg-gray-800">
        <li
          v-for="result in results"
          :key="result.id"
          class="p-2 border-b hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer"
        >
          {{ result.name ?? result.title ?? "Kein Name" }}
        </li>
      </ul>
    </div>
  </template>

  <script>
    import axios from "axios";
    import { Inertia } from '@inertiajs/inertia';
    import {route} from 'ziggy-js';
  export default {
    props: {
      table: String, // Übergibt die Tabelle, in der gesucht wird
    },
    data() {
      return {
        searchQuery: "",
        results: [],
        loading: false,
      };
    },
    methods: {
      async fetchResults() {
        if (this.searchQuery.length < 2) {
          this.results = [];
          return;
        }

        //// this.loading = true;
        try {
            Inertia.get(route('admin.tables.show'),
  { search: this.searchQuery, table: this.table },
  { preserveState: false, replace: true,skipLoading:true });


        // this.results = response.data;
        } catch (error) {
          console.error("Fehler bei der Suche:", error);
        }
        this.loading = false;
      },
    },
  };
  </script>


<template>
    <div>
      <label for="category" class="block mb-2 text-sm font-medium text-layout-sun-900 dark:text-layout-night-900">
        Kategorie wählen:
      </label>
      <select
        id="category_id"
        v-model="selectedCategory"
        class="w-full wf_2 w-fully p-2.5 text-sm rounded-lg block border border-layout-sun-300 text-layout-sun-900 bg-layout-sun-50 placeholder-layout-sun-400 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:border-layout-night-300 dark:text-layout-night-900 dark:bg-layout-night-50 dark:placeholder-layout-night-400 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
      >
        <option value="0" disabled>Bitte wählen</option>
        <option v-for="category in sortedCategories" :key="category.id" :value="category.id">
          {{ category.name }}
        </option>
      </select>
      <label v-if="selectedCategory" for="medium" class="block mb-2 text-sm font-medium text-layout-sun-900 dark:text-layout-night-900">
        Medium wählen:
      </label>
      <select
        v-if="selectedCategory && formattedMediums(selectedCategory).length > 0"
        id="type_id"
        v-model="selectedMedium"
        class="w-full wf_2 w-fully p-2.5 text-sm rounded-lg block border border-layout-sun-300 text-layout-sun-900 bg-layout-sun-50 placeholder-layout-sun-400 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:border-layout-night-300 dark:text-layout-night-900 dark:bg-layout-night-50 dark:placeholder-layout-night-400 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
      >
        <option value="0" disabled>Bitte wählen</option>
        <option v-for="medium in formattedMediums(selectedCategory)" :key="medium.id" :value="medium.id">
          {{ medium.name }}
        </option>
      </select>
    </div>
  </template>

<script>
import axios from "axios";
import { CleanId,CleanTable } from '@/helpers';
export default {
  data() {
    return {
      selectedCategory: null,
      selectedMedium: null,
      categories: [],
      table: CleanTable(),
      id: CleanId(),
    };
  },
  props: {


  },
  computed: {
    sortedCategories() {
      return [...this.categories].sort((a, b) => a.name.localeCompare(b.name));
    },
    formattedMediums() {
      return (categoryId) => {
        const catId = Number(categoryId);
        const category = this.categories.find(cat => Number(cat.id) === catId);
        return category?.types?.length
          ? [...category.types].sort((a, b) => a.name.localeCompare(b.name))
          : [];
      };
    }
  },
  async created() {
    try {
        // console.log(`/api/GetCat/${this.table}/${this.id}`);
      // 1. Lade alle Kategorien mit ihren Types
      const response = await axios.get(route("ArtAct", [this.table, this.id]));
      this.categories = response.data.categories || [];

      // 2. Lade gespeicherte Werte (type_id & categorie_id)
      const defaultResponse = await axios.get(`/api/GetCat/${this.table}/${this.id}`);
      const defaultData = defaultResponse.data;

      // 3. Setze defaults, wenn möglich
      if (defaultData.categorie_id) {
        this.selectedCategory = defaultData.categorie_id;
      } else if (this.categories.length > 0) {
        this.selectedCategory = this.categories[0].id;
      }

      const availableMediums = this.formattedMediums(this.selectedCategory);

      if (defaultData.type_id && availableMediums.find(m => m.id == defaultData.type_id)) {
        this.selectedMedium = defaultData.type_id;
      } else if (availableMediums.length > 0) {
        this.selectedMedium = availableMediums[0].id;
      }
    } catch (error) {
      console.error("❌ Fehler beim Laden der Daten:", error);
    }
  }
};
</script>

  <style scoped>
@media (min-width: 1024px) {
.maxx{
    max-width:200% !important;
    min-width:200% !important;
}
}
  </style>

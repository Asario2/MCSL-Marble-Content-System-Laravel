<template>
    <div>
      <div class="relative">
        <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
          <icon-search class="block mr-3 h-5 w-5" />
        </div>
        <input
          ref="input"
          type="text"
          id="default-search"
          class="block p-4 pl-10 w-full rounded-lg border text-sm text-layout-sun-900 bg-layout-sun-50 border-layout-sun-300 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:text-layout-night-900 dark:bg-layout-night-50 dark:border-layout-night-300 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
          autocomplete="off"
          name="search"
          :placeholder="placeholder"
          :value="modelValue"
          @submit.prevent="searchData"
          @input="$emit('update:modelValue', $event.target.value)"
        />
        <button
          type="submit"
          class="absolute right-2 bottom-2 font-medium rounded-lg text-sm px-4 py-2 cursor-pointer border-2 focus:ring focus:outline-none bg-primary-sun-500 text-primary-sun-100 hover:text-primary-sun-900 hover:bg-layout-sun-100 hover:border-primary-sun-600 focus:border-primary-sun-600 dark:bg-primary-night-500 dark:text-primary-night-100 dark:hover:text-primary-night-900 dark:hover:bg-layout-night-100 dark:hover:border-primary-night-600 dark:focus:border-primary-night-600"
          @click.prevent="$emit('reset')"

          >
          Suche zurücksetzen
        </button>
      </div>

      <div class="w-full mt-4">
        <div
          class="p-1 text-sm my-2 text-left font-medium tracking-wide text-layout-sun-600 dark:text-layout-night-600"
          v-if="searchText"
        >
          <span v-html="searchText"></span>
        </div>
      </div>
    </div>
  </template>

  <script>
  import IconSearch from "@/Application/Components/Icons/Search.vue";

  export default {
    name: "Components_Lists_SearchFilter",
    emits: ["update:modelValue", "reset"],
    components: {
      IconSearch,
    },
    props: {
      modelValue: {
        type: String,
        default: null,
      },
      placeholder: {
        type: String,
        default: "Suchen",
      },
      searchText: {
        type: String,
      },
    },
    methods: {
      focus() {
        this.$refs.input?.focus();
      },
    },
  };
  </script>


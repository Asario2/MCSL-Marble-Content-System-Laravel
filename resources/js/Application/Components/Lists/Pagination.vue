<template>
  <div
    v-if="links && links.length > 1"
    class="flex items-center justify-center flex-wrap mt-6 -mb-1 text-xs md:text-base
           bg-transparent text-layout-sun-700 dark:text-layout-night-700"
  >
    <template v-for="(link, index) in links" :key="index">

      <!-- Disabled -->
      <div
        v-if="!link.url"
        class="flex items-center px-3 py-0.5 mx-1 mb-1 rounded-md cursor-not-allowed opacity-50"
      >
        <span v-html="translateLabel(link.label)" />
      </div>

      <!-- Active -->
      <Link
        v-else-if="link.active"
        :href="link.url"
        preserve-state
        replace
        class="flex items-center px-2.5 py-0.5 mx-1 mb-1 h-7
               rounded-md border font-bold
               border-primary-sun-500 text-primary-sun-900
               dark:border-primary-night-500 dark:text-primary-night-900"
      >
        <span v-html="link.label" />
      </Link>

      <!-- Normal -->
      <Link
        v-else
        :href="link.url"
        preserve-state
        replace
        class="flex items-center px-2.5 py-0.5 mx-1 mb-1 h-7
               rounded-md border transition-colors
               hover:bg-layout-sun-200 hover:text-layout-sun-800
               dark:hover:bg-layout-night-200 dark:hover:text-layout-night-800"
      >
        <span v-html="translateLabel(link.label)" />
      </Link>

    </template>
  </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";

export default {
  name: "Pagination",

  components: {
    Link,
  },

  props: {
    links: {
      type: Array,
      required: true,
    },
  },

  methods: {
    translateLabel(label) {
      if (label === "pagination.previous") return "« Zurück";
      if (label === "pagination.next") return "Weiter »";
      return label;
    },
  },
};
</script>


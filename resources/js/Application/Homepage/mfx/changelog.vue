
<template>
    <div class="prose max-w-none p-4">
      <div v-if="loading">Lade Changelog...</div>
      <div v-else v-html="changelogHtml"></div>
    </div>
  </template>

  <script>
  import { marked } from 'marked';
  import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
  export default {

  components: { MetaHeader },
    data() {
      return {
        changelogMd: '',
        changelogHtml: '',
        loading: true,
      };
    },
    mounted() {
      // Ersetze URL hier durch deine tatsächliche GitHub raw URL zur CHANGELOG.md
      const url = 'https://raw.githubusercontent.com/Asario2/MCSL-based-on-Starter-Eleven/main/CHANGELOG.md';

      fetch(url)
        .then(res => {
          if (!res.ok) throw new Error('Fehler beim Laden der Datei');
          return res.text();
        })
        .then(md => {
          this.changelogMd = md;
          this.changelogHtml = this.linkit(marked.parse(md));
          this.loading = false;
        })
        .catch(err => {
          console.error(err);
          this.changelogHtml = '<p>Fehler beim Laden des Changelogs.</p>';
          this.loading = false;
        });
    },
    methods:{
        linkit(str)
        {
            return str.replace(/(?<!&)#(\d+)/g, "<a href='https://github.com/Asario2/MCSL-based-on-Starter-Eleven/issues/$1'>#$1</a>");
        }
    }
  };
  </script>

  <style scoped>
  .prose {
    /* optional Tailwind-Prose oder eigene Styles */
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont,
      "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif,
      "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
    color: #333;
  }
  </style>


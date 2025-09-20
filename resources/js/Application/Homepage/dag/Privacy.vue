
<template>
    <Layout>
    <MetaHeader title="Datenschutzerklärung" />
        <div class="prose prose-layout dark:prose-invert max-w-none my-2 bg-layout-sun-200 dark:bg-layout-night-200 lg:rounded-lg p-6">
            <h1 class="text-black">Datenschutzerklärung</h1>
            <div v-html="privacy" />
        </div>
    </Layout>
  </template>

  <script>
  import Layout from "@/Application/Homepage/Shared/dag/Layout.vue";
  import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
  import { nextTick } from "vue";
  export default {
    components: {
    Layout, MetaHeader},
    props: {
      privacy: String,
    },
    mounted() {
        this.scrollToHashAnchor();
        window.addEventListener("hashchange", this.scrollToHashAnchor);
        },
        beforeUnmount() {
        window.removeEventListener("hashchange", this.scrollToHashAnchor);
        },
  methods: {
    scrollToHashAnchor() {
  const hash = window.location.hash;
  if (hash && hash.startsWith("#")) {
    nextTick(() => {
      const el = document.getElementById(hash.substring(1));
      if (el) {
        const y = el.getBoundingClientRect().top + window.pageYOffset - 300;
        window.scrollTo({ top: y, behavior: "smooth" });
      }
    });
}
    },
},
};
  </script>

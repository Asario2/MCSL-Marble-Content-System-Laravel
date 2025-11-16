<template>
    <Layout>
    <MetaHeader title="Datenschutzerklärung" />
        <div class="prose prose-layout dark:prose-invert max-w-none bg-layout-sun-100 dark:bg-layout-night-100 p-7">
            <div v-html="privacy" />
        </div>
    </Layout>
  </template>

  <script>
  import Layout from "@/Application/Homepage/Shared/Layout.vue";
  import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
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
      setTimeout(() => {
        const el = document.getElementById(hash.substring(1));
        if (el) {
          const y = el.getBoundingClientRect().top + window.pageYOffset - 134;
          window.scrollTo({ top: y, behavior: "smooth" });
        }
      }, 50); // etwas Delay, bis DOM fertig gerendert ist
    }
  }
}
};
  </script>


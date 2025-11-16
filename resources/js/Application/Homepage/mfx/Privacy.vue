
<template>
    <Layout>
    <MetaHeader title="Datenschutzerklärung" />
        <div class="prose prose-layout dark:prose-invert max-w-none">
            <div v-html="privacy" />
        </div>
    </Layout>
  </template>

  <script>
  import Layout from "@/Application/Homepage/Shared/mfx/Layout.vue";
  import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
  import { CleanTable } from '@/helpers';
  export default {
    components: {
    Layout, MetaHeader},
    props: {
      privacy: String,
    },
    mounted() {
        this.scrollToHashAnchor();
        window.addEventListener("hashchange", this.scrollToHashAnchor);
        getscro();
    },
        beforeUnmount() {
        window.removeEventListener("hashchange", this.scrollToHashAnchor);

        },

  methods: {
    getscro(){

        if(CleanTable() != "dag"){
            return "136";
        }
        else if(CleanTable() == "dag")
        {
            return "250";
        }
    },
    scrollToHashAnchor() {
    const hash = window.location.hash;
    if (hash && hash.startsWith("#")) {
      setTimeout(() => {
        const el = document.getElementById(hash.substring(1));
        if (el) {
          const y = el.getBoundingClientRect().top + window.pageYOffset - this.getscro();
          window.scrollTo({ top: y, behavior: "smooth" });
        }
      }, 50); // etwas Delay, bis DOM fertig gerendert ist
    }
  }
}
};
  </script>


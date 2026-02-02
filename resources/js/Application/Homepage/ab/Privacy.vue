<template>
  <Layout>
    <MetaHeader title="Datenschutzerklärung" />

    <div
      class="bg-layout-sun-100 dark:bg-layout-night-100 p-7"
    >
      <div ref="content" v-html="ch(processedHtml)"></div>

      <!-- ContactCard wird versteckt gerendert und später eingefügt -->
      <ContactCard
        v-if="vcardData"
        ref="vcard"
        :data="vcardData"
        style="display:none"
      />

    </div>
  </Layout>
</template>

<script>
import Layout from "@/Application/Homepage/Shared/Layout.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import ContactCard from "@/Components/vcard.vue"; // <-- einbinden

export default {

  name: "PrivacyPage",
  components: { Layout, MetaHeader, ContactCard },

  props: {
    privacy: String,   // enthält DB-HTML MIT {{ vcard }}
    vcardData: Object  // Daten für die vCard, vom Eltern-Component übergeben
  },

  data() {
    return {
      processedHtml: "" // finaler HTML-Text mit Placeholder
    };
  },

  mounted() {
    // 1) {{ vcard }} ersetzen durch ein HTML-Placeholder-Tag
    this.processedHtml = this.privacy.replace(
      "{{ vcard }}",
      "<vcard-placeholder></vcard-placeholder>"
    );

    // 2) Komponente nach Rendern einfügen
    this.$nextTick(() => {
      const placeholder = this.$refs.content.querySelector("vcard-placeholder");

      if (placeholder && this.$refs.vcard) {
        placeholder.replaceWith(this.$refs.vcard.$el);
        this.$refs.vcard.$el.style.display = "";
      }
    });

    this.scrollToHashAnchor();
    window.addEventListener("hashchange", this.scrollToHashAnchor);
  },

  beforeUnmount() {
    window.removeEventListener("hashchange", this.scrollToHashAnchor);
  },

  methods: {
    ch(txt){
            return txt
            .replace(/\n<li>/g, '<li>')   // Zeilenumbruch vor <li> entfernen
            .replace(/\n/g, '<br />');    // alle übrigen \n in <br />

        },
    scrollToHashAnchor() {
      const hash = window.location.hash;
      if (hash && hash.startsWith("#")) {
        setTimeout(() => {
          const el = document.getElementById(hash.substring(1));
          if (el) {
            const y = el.getBoundingClientRect().top + window.pageYOffset - 134;
            window.scrollTo({ top: y, behavior: "smooth" });
          }
        }, 50);
      }
    }
  }
};
</script>

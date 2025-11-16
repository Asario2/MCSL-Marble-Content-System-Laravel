<template>
    <div class="rim">
    <layout :header-url="$page.props.saas_url + '/'">
        <MetaHeader title="Meine Links" />
            <page-content>
                <template #content>
                <div class="grid grid-cols-12 gap-6">
                    <!-- Linke Spalte: Text (9 von 12) -->
                    <div class="col-span-12 md:col-span-9">
                        <h1 class="text-black blackcan">Meine Links</h1>
                        <div v-for="(entry, index) in data[0]" :key="index" class="mb-6">
                            <!-- Flex-Box: Bild links, Text rechts -->
                            <div class="flex gap-4">
                                <!-- Linkes Bild -->
                                <div class="flex-shrink-0">
                                    <a :href="entry.url" target="_blank">
                                    <img
                                        v-if="entry.img_bild"
                                        :src="'/images/_dag/links/' + entry.img_bild"
                                        alt="Bild"
                                        class="w-[150px] h-[150px] object-cover rounded shadow"
                                    />
                                    </a>
                                </div>

                                <!-- Rechter Text-Bereich -->
                                <div class="flex-1">
                                    <!-- Headline -->
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="font-bold text-black dark:text-black mt-[-3px]" v-html="rumLaut(entry.headline)"></h3>
                                    </div>

                                    <!-- Message -->
                                    <div class="text-gray-800 dark:text-gray-200" v-html="rumLaut(entry.message)"></div>
                                    <span><a :href="entry.url" target="_blank">{{ entry.url }}</a></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rechte Spalte: Spruch des Monats -->
                    <div class="col-span-12 md:col-span-3 flex flex-col gap-4">
                        <Poems :quotes="quotes" :rumLaut="rumLaut" />
                    </div>
                </div>
                </template>
            </page-content>
        </layout>
    </div>
    </template>

<script>
import { defineComponent } from "vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import Layout from "@/Application/Homepage/Shared/dag/Layout.vue";
import { selectionHelper, GetSettings,rumLaut } from "@/helpers";
import editbtns from "@/Application/Components/Form/editbtns.vue";
import PageContent from "@/Application/Components/Content/PageContent.vue";
import PageTitle from "@/Application/Components/Content/PageTitle.vue";
import PageParagraph from "@/Application/Components/Content/PageParagraph.vue";
import emailview from "@/Application/Components/Form/email.vue";
import Poems from "@/Application/Components/poems.vue";



export default defineComponent({
    name: "Homepage_Home",

    components: {
        Layout,
        PageContent,
        PageTitle,
        PageParagraph,
        emailview,
        Poems,
        editbtns, MetaHeader},
    props:{
        news:[Array,Object],
        data: [Array,Object],

    },
    data() {
    return {
      form: {
        name: '',
        email: '',
        subject: '',
        message: '',
        captcha: '',
        accepted: false
      },
      quotes:[],
    }
  },
  mounted() {
    // Prüfen, ob im SessionStorage ein Reload nötig ist
    if (sessionStorage.getItem("force_reload") === "true") {
      sessionStorage.removeItem("force_reload"); // nur einmal ausführen
      window.location.reload(true);
    }
    this.loadQuote();
  },
    methods: {
        rumLaut,
        async loadQuote() {
      try {
        const response = await axios.get('/api/spruch-des-monats'); // deine API-Route
        this.quotes = [response.data]; //  { text: "..." }
//         console.log(this.quotes);
      } catch (error) {
        console.error("Fehler beim Laden des Spruchs:", error);
      }
    },
        cleanHtml(html) {
      const result = rumLaut(html);
     // console.log("rumLaut output:", result);
      return result;

    },
    async submitForm() {
      try {
        const response = await axios.post('/contact/send', this.form)
        alert('Nachricht erfolgreich gesendet!')
        this.resetForm()
      } catch (error) {
        alert('Fehler beim Senden der Nachricht.')
      }
    },
    resetForm() {
      this.form = {
        name: '',
        email: '',
        subject: '',
        message: '',
        captcha: '',
        accepted: false
      }
    }
  },
});
</script>
<style scoped>
/*.input {
  @apply w-full px-4 py-2 border rounded-md dark:bg-zinc-800 dark:text-white;
}

.btn {
  @apply px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50;
}*/
P{
    background-color:rgba(0,0,0,0) !important;
}
.short{
    line-height:32px;
    margin-top:-10px !important;
    margin-bottom:-10px !important;
}
</style>


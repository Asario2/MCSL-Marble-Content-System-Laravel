<template>
    <div class="rim">
    <layout :header-url="$page.props.saas_url + '/'">
        <MetaHeader title="Home" />
            <page-content>
                <template #content>
                <div class="grid grid-cols-12 gap-6">
                    <!-- Linke Spalte: Text (9 von 12) -->
                    <div class="col-span-12 md:col-span-9">
                        <h1 class="text-black blackcan">Willkommen liebe Besucher</h1>
                        <b>
                            Mein Name ist Monika Dargies, geb.Mai, komme aus Bad Bramstedt und betreibe Familienforschung seit 2005.<br /><br />
                            Die genealogischen Informationen dieser Homepage werden privat zusammengetragen und ich bitte darum, dass diese nicht einfach nur abgeschrieben werden. Ich freue mich über jeden, der mir eine Email schreibt zwecks Datenaustausch.<br /><br />
                            Meine Vorfahren stammen väterlicherseits "Mai" aus Schlesien, "Williges" aus Helsa und mütterlicherseits "Hagemann" aus Vorpommern, "Meyn" aus Haseldorfer Marsch. Des Weiteren ist die Familie meines Mannes Dargies/Fago aus Ostpreussen vertreten.<br /><br />
                            Ein besonderer Dank gilt Asario, der diese Website für mich erstellt hat.
                        </b>
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
        editbtns, MetaHeader,
    Poems},
    props:{
        news:[Array,Object],
        text: [Array,Object],

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
        console.log(this.quotes);
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
.input {
  @apply w-full px-4 py-2 border rounded-md dark:bg-zinc-800 dark:text-white;
}

.btn {
  @apply px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50;
}
P{
    background-color:rgba(0,0,0,0) !important;
}

</style>

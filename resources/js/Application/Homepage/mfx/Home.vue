<template>
    <layout :header-url="$page.props.saas_url + '/'">
    <MetaHeader title="Home" />
        <page-content>
            <template #content>
                <div>
                <page-title>
                    <template #title>
                        <span class="dark:text-layout-night-1050 text-layout-sun-1000">MarbleFX</span>
                </template>
                </page-title>
                <page-paragraph>
                    <template #paragraph>
                        <span  class="text-layout-sun-1000 dark:text-layout-night-1000">
                            MarbleFX ihr Webdienstleister
                        </span>
                    </template>
                </page-paragraph>
                <div class="grid grid-cols-12 gap-0">
                    <div class="col-span-6 p-1 md:p-4 lg:rounded-lg ">
                    <div v-for="item in news.data" :key="item.id">
                    <div class="bg-layout-sun-50 dark:bg-layout-night-50 lg:rounded-lg p-2 mb-6  border border-layout-sun-1000 dark:border-layout-night-1050" >
                        <span class="dark:text-layout-night-1050 font-bold text-layout-sun-1000 text-2xl" v-html="cleanHtml(item.headline)"></span> <editbtns :id="item.id" table="news"></editbtns><br />
                        <span class="text-layout-sun-1000 dark:text-layout-night-1000" v-html="cleanHtml(item.message)"></span>

                    </div>

                </div>

                </div>

                <div class="col-span-6 p-1 md:p-4 lg:rounded-lg ">

                <div class="bg-layout-sun-50 dark:bg-layout-night-50 lg:rounded-lg p-2 mb-6  border border-layout-sun-1000 dark:border-layout-night-1050">
                    <div v-if="text" class="text-layout-sun-1000 dark:text-layout-night-1000">
                        <span class="text-2xl text-layout-sun-1000 dark:text-layout-night-1050 font-bold">{{ text.headline }}</span> <editbtns id="14" table="texts"></editbtns>
                        <div v-html="text.text"></div>
                    </div>
                    <div v-else>
                    <p class="text-gray-500 italic">Kein Willkommenstext vorhanden.</p>
                    </div>
                </div>

                <emailview></emailview>

                    </div>

                    </div>

            </div>
                </template>
        </page-content>
    </layout>
</template>
<script>
import { defineComponent } from "vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import Layout from "@/Application/Homepage/Shared/mfx/Layout.vue";
import { selectionHelper, GetSettings,rumLaut } from "@/helpers";
import editbtns from "@/Application/Components/Form/editbtns.vue";
import PageContent from "@/Application/Components/Content/PageContent.vue";
import PageTitle from "@/Application/Components/Content/PageTitle.vue";
import PageParagraph from "@/Application/Components/Content/PageParagraph.vue";
import emailview from "@/Application/Components/Form/email.vue";

export default defineComponent({
    name: "Homepage_Home",

    components: {
        Layout,
        PageContent,
        PageTitle,
        PageParagraph,
        emailview,
        editbtns, MetaHeader},
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
      }
    }
  },
  mounted() {
    // Prüfen, ob im SessionStorage ein Reload nötig ist
    if (sessionStorage.getItem("force_reload") === "true") {
      sessionStorage.removeItem("force_reload"); // nur einmal ausführen
      window.location.reload(true);
    }
  },
    methods: {
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
</style>


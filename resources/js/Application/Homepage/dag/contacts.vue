<template>
    <layout :header-url="$page.props.saas_url + '/'">
    <MetaHeader title="Contacts" />
        <page-content>
            <template #content>
                <div>
                <page-title>
                    <template #title>
                        <span class="dark:text-layout-night-1050 text-layout-sun-1000">{{text?.headline}}</span> <editbtns id="16" table="texts"></editbtns>
                </template>
                </page-title>
                <div class="w-full p-1 md:p-4 lg:rounded-lg">
                <div class="bg-layout-sun-50 dark:bg-layout-night-50 dark:bg-layout-night-501  lg:rounded-lg p-2 mb-6 border border-layout-sun-1000 dark:border-layout-night-1050">
                    <div v-if="text" class="text-layout-sun-1000 dark:text-layout-night-1000">
                    <div v-html="text.text"></div>
                    </div>
                    <div v-else>
                    <p class="text-gray-500 italic">Kein Willkommenstext vorhanden.</p>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6 w-full">
                    <!-- Linke Spalte: 4 von 12 -->
                    <div class="col-span-12 md:col-span-6">
                    <h2 v-html="contacts.headline" class="text-xl font-semibold mb-2"></h2>
                    <p v-html="rumLaut(contacts.text)" class="text-layout-sun-900 dark:text-layout-night-900"></p>
                    <div class="relative">
                    <img :src="'/images/_mfx/web/contacts_ai.jpg'" class="mx-auto mw-[100%] mt-[12px] ai-image-corner"/><AiButton :nohome="true" :dma="dmaa"></AiButton>
                </div>
                </div>

                    <!-- Rechte Spalte: 8 von 12 -->
                    <div class="col-span-12 md:col-span-6 flex justify-end">
                    <div class="w-full md:w-auto">
                        <h2 class="text-xl font-semibold mb-4">Kontaktformular</h2>
                        <emailview :nohead="true"></emailview>
                    </div>
                    </div>
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
import Layout from "@/Application/Homepage/Shared/dag/Layout.vue";
import AiButton from "@/Application/Components/Content/AiButton.vue";
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
        editbtns,
        AiButton,
        MetaHeader},
    props:{
        news:[Array,Object],
        text: [Array,Object],
        contacts: [Array,Object],
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
    },
    rumLaut,

  },
});
</script>
<style scoped>

</style>

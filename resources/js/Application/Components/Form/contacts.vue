<template>
    <layout header-title="Home" :header-url="$page.props.saas_url + '/'">
        <page-content>
            <template #content>
                <div>
                <page-title>
                    <template #title>
                        <span class="dark:text-layout-night-1050 text-layout-sun-1000">{{text?.headline}}</span><editbtns id="14" table="texts"></editbtns>
                </template>
                </page-title>
                <div class="col-span-6 p-1 md:p-4 lg:rounded-lg ">

                <div class="bg-layout-sun-50 dark:bg-layout-night-50 lg:rounded-lg p-2 mb-6  border border-layout-sun-1000 dark:border-layout-night-1050">
                    <div v-if="text" class="text-layout-sun-1000 dark:text-layout-night-1000">
                        <div v-html="text.text"></div>
                    </div>
                    <div v-else>
                    <p class="text-gray-500 italic">Kein Willkommenstext vorhanden.</p>
                    </div>
                </div>

                <emailview></emailview>

                    </div>

                    </div>

            </template>
        </page-content>
    </layout>
</template>
<script>
import { defineComponent } from "vue";
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
        editbtns,
    },
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


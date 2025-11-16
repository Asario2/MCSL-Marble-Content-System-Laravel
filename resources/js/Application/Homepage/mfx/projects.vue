<template>
    <layout :header-url="$page.props.saas_url + '/'">
    <MetaHeader title="Projelte" />
        <page-content>
            <template #content>
                <div>
                <page-title>
                    <template #title>
                        <span class="dark:text-layout-night-1050 text-layout-sun-1000">Projects</span>
                </template>
                </page-title>
                <div class="space-y-3">
    <div
        v-for="item in data"
        :key="item.id"
        class="min-h-[102px] bg-layout-sun-50 dark:bg-layout-night-50 lg:rounded-lg border border-layout-sun-1000 dark:border-layout-night-1050 grid grid-cols-12 gap-4"
    >
        <!-- Bild -->
        <div class="col-span-12 sm:col-span-2">
            <img
                :src="'/images/_mfx/projects/img_thumb/' + item.img_thumb"
                :alt="item.name"
                :title="item.name"
                class="w-full md:h-[100px] sm:h-[196px] object-cover object-top lg:rounded-lg bordah"
            />
        </div>

        <!-- Headline, Umfang, URL -->
        <div class="col-span-12 sm:col-span-5 space-y-1">
            <div class="flex items-start flex-wrap gap-2">
                <div
                    class="text-layout-sun-1000 dark:text-layout-night-1050 font-bold"
                    v-html="cleanHtml(item.name)"
                ></div>
                &nbsp;<editbtns :id="item?.id" table="projects" />
            </div>
            <div
                class="text-layout-sun-1000 dark:text-layout-night-1000"
                v-html="cleanHtml(item.Umfang)"
            ></div>
            <div class="text-sm">
                <template v-if="item.url !== 'Offline'">
                    <a :href="item.url" class="text-blue-600 hover:underline">
                        {{ item.url }}
                    </a>
                </template>
                <template v-else>
                    <span class="text-gray-500">Offline</span>
                </template>
            </div>
        </div>

        <!-- Aufgaben -->
        <div class="col-span-12 sm:col-span-3 text-sm text-gray-700 dark:text-gray-300">
            <b>Aufgaben:</b><br />
            <div v-for="sheet in sheets[item.id]" :key="sheet.nick + sheet.aufgabe">
                {{ sheet.aufgabe }} ({{ sheet.nick }})<br />
            </div>
        </div>

        <!-- Icons -->
        <div class="col-span-12 sm:col-span-2 flex justify-end items-center gap-2 pr-2">
            <img
                v-if="item.xis_mcsl"
                :src="'/images/_mfx/icons/made_with_mcsl.png'"
                alt="mcsl"
            />
            <img
                v-else-if="item.xis_mcs"
                :src="'/images/_mfx/icons/made_with_mcs.png'"
                alt="mcs"
            />
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
import Layout from "@/Application/Homepage/Shared/mfx/Layout.vue";
import { selectionHelper, GetSettings,rumLaut } from "@/helpers";
import PageContent from "@/Application/Components/Content/PageContent.vue";
import PageTitle from "@/Application/Components/Content/PageTitle.vue";
import PageParagraph from "@/Application/Components/Content/PageParagraph.vue";
import emailview from "@/Application/Components/Form/email.vue";
import editbtns from "@/Application/Components/Form/editbtns.vue";

export default defineComponent({
    name: "Homepage_Projects",

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
        data: [Array,Object],
        item: [Array,Object],
        sheets: [Array,Object],
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
<style scoped>
    /* .input {
    @apply w-full px-4 py-2 border rounded-md dark:bg-zinc-800 dark:text-white;
    }

    .btn {
    @apply px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50;
    } */
.bordah{
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}
</style>


<template>
    <layout header-url="$page.props.saas_url + '/'">
    <MetaHeader title="Infos" />
        <page-content>
            <template #content>
                <div>
                <page-title>
                    <template #title>
                        <span class="dark:text-layout-night-1050 text-layout-sun-1000">Infos</span>
                </template>
                </page-title>
                <div class="grid grid-cols-12 sm:gap-0">
                    <div
                    v-for="item in data"
                    :key="item.id"
                    class="col-span-6 p-4 lg:rounded-lg"
                    >
                    <a :href="'infos/show/' + item.id" class="block">
                        <div
                        class="bg-layout-sun-50 dark:bg-layout-night-50 lg:rounded-lg pr-2 border border-layout-sun-1000 dark:border-layout-night-1050 flex flex-col md:flex-row min-h-[104px] sm:gap-0"
                        >
                        <!-- Bildcontainer -->
                        <div
                            class="w-full md:w-[110px] h-[104px] md:h-[104px] relative rounded-lg overflow-hidden mb-4 md:mb-0 nor_border  p-0 sm:gap-0"
                        >
                            <img
                            :src="`/images/_mfx/infos/img_thumb/${item.img_thumb}`"
                            :alt="item.headline"
                            :title="item.headline"
                            class="absolute top-0 left-0 w-full h-full object-cover nor_border"
                            />
                        </div>

                        <!-- Textcontainer -->
                        <div
                            class="flex flex-col justify-start text-layout-sun-1000 dark:text-layout-night-1000 p-2 sm:gap-0"
                        >
                            <div class="flex items-center dark:text-layout-night-1050 text-layout-sun-1000">
                            <span class="font-bold mt-[-2px]" v-html="cleanHtml(item.headline)"></span>
                            <editbtns :id="item?.id" table="infos" class="ml-3" />
                            </div>
                            <span class="lha" v-html="cleanHtml(item.summary)"></span>
                        </div>
                        </div>
                    </a>
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
    name: "Homepage_Home",

    components: {
        Layout,
        PageContent,
        PageTitle,
        PageParagraph,
        emailview,
        editbtns,
        MetaHeader,
    },
    props:{
        news:[Array,Object],
        text: [Array,Object],
        data: [Array,Object],
        item: [Array,Object],

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
.input {
  @apply w-full px-4 py-2 border rounded-md dark:bg-zinc-800 dark:text-white;
}

.btn {
  @apply px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50;
}
.nor_border{
    border-top-right-radius:0;
    border-bottom-right-radius:0;
    max-height:100px;
    min-width:125px;
    max-width:110% !important;
    margin-right:0px;
}
.lha{
line-height:1.3rem !important;
font-size: 1.0rem !important;
}
</style>

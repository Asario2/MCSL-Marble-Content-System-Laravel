<template>
    <layout :header-url="$page.props.saas_url + '/'">
    <MetaHeader title="Images" />
        <page-content>
            <template #content>
                <div>
                <page-title>
                    <template #title>
                        <span class="dark:text-layout-night-1050 text-layout-sun-1000">Images</span>
                </template>
                </page-title>
                <div class="grid grid-cols-12">
  <div v-for="item in data.data" :key="item.id" class="col-span-6 p-4 lg:rounded-lg">
    <a :href="'/home/images/show/' + item.id" class="block tw">
      <div
        class="bg-layout-sun-50 dark:bg-layout-night-50 lg:rounded-lg pr-2 border border-layout-sun-1000 dark:border-layout-night-1050
               flex flex-col md:flex-row items-start min-h-[104px]"
      >
        <!-- Bild -->
        <img
          :src="'/images/_mfx/images/img_thumb/thumbs/' + item.img_thumb"
          :alt="item.name"
          :title="item.name"
          class="w-full h-auto md:w-[104px] max-w-[320px] md:h-[104px] object-cover rounded-lg nor_border flex-shrink-0 mr-[-3px]"
        />

        <!-- Textcontainer -->
        <div class="mt-3 md:mt-0 md:ml-4 flex flex-col flex-1">
          <div class="flex items-center space-x-3">
            <span
              class="dark:text-layout-night-1050 text-layout-sun-1000 font-bold p-2"
              v-html="cleanHtml(item.name)"
            ></span>
            <editbtns :id="item?.id" table="images" />
          </div>
          <span
            class="text-layout-sun-1000 dark:text-layout-night-1000 mt-1 p-2"
            v-html="cleanHtml(item.message)"
          ></span>
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
import { marked } from 'marked';
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
        MetaHeader
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
            if(!html){
                html = '';
            }
            html = marked(html);
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

}
</style>

<template>
    <component :is="layoutComponent"
               header-title="Blog"
               :header-url="$page.props.saas_url + '/blogs'"
               :header-image="$page.props.saas_url + '/images/blogimages/Blog_Idee_480x360.jpg'">

      <MetaHeader title="Künstliche Inteligenz" />

      <section class="bg-layout-sun-0 text-layout-sun-800 dark:bg-layout-night-0 dark:text-layout-night-800">
        <div class="w-full max-w-7xl mx-auto mt-5">
          <div v-if="data.length">
            <div v-for="(item, index) in data" :key="index"
                 class="group hover:no-underline focus:no-underline lg:grid lg:grid-cols-12 lg:gap-6 bg-layout-sun-100 dark:bg-layout-night-100">

              <!-- Bild-Bereich -->
              <div class="relative lg:col-span-7">
                <img :src="`${impath}`"
                     :alt="`Bild von ${item.headline}`"
                     :class="imclass"/>
                <AiButton :nohome="nohomee" :dma="dmaa"></AiButton>
              </div>

              <!-- Text-Bereich -->
              <div class="p-6 space-y-2 lg:col-span-5">
                <div class="flex justify-end">
                  <div v-if="item.author_name2"
                       class="text-sm bg-primary-sun-500 text-primary-sun-900 dark:bg-primary-night-500 dark:text-primary-night-900 font-semibold px-2.5 py-0.5 rounded-lg">
                  </div>
                </div>

                <h2 class="text-xl font-semibold sm:text-2xl font-title group-hover:underline">
                  {{ item.headline }}
                </h2>

                <div class="text-xs text-layout-sun-600 dark:text-layout-night-600">
                  Von: {{ item.author_name }}
                </div>

                <markdown :markdown="item.text"></markdown>
              </div>
            </div>
          </div>
          <div v-else>
            <p>Keine Daten gefunden</p>
          </div>
        </div>
      </section>

    </component>
  </template>


<script>
import { defineComponent } from "vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
//import Layout from "@/Application/Homepage/Shared/Layout{SD()}.vue";

import PageTitle from "@/Application/Components/Content/PageTitle.vue";
import AiButton from "@/Application/Components/Content/AiButton.vue";
import BlogPreviewBig from "@/Application/Homepage/Shared/BlogPreviewBig.vue";
import BlogPreviewSmall from "@/Application/Homepage/Shared/BlogPreviewSmall.vue";

import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
import { defineAsyncComponent } from "vue";
import Alert from "@/Application/Components/Content/Alert.vue";
import Markdown from "@/Application/Components/Content/Markdown.vue";
import mapValues from "lodash/mapValues";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import { GetRights,SD } from "@/helpers";
import { hasRight,loadAllRights,isRightsReady } from '@/utils/rights';



import { VueMarkdownIt } from "vue3-markdown-it";




export default defineComponent({
    name: "Homepage_AiContent",

    components: {

        Markdown: defineAsyncComponent(() => import("vue3-markdown-it")),


        PageTitle,
        BlogPreviewBig,
        BlogPreviewSmall,
        SearchFilter,
        Alert,
        AiButton,
        Markdown, MetaHeader},
    data() {
    return {
      rightsData: {}, // Hier speichern wir die Rechte für den User
      rightsReady: false,
      layoutComponent: null,
    };
  },
    props: {
    data:{
        required:true,
        default: [],
    },
    // darkMode:  {
    //     required: false,
    //     // default: "dark",
    // },
    blogarticle: {
            type: String,
        },

    },
    created() {
    this.loadLayout();
  },
    watch: {

    },
    computed:{
        impath(){
            return '/images/_'+ SD() +'/ai-teaser-light.jpg';
        },
        imclass(){
            if(SD() === "ab")
            {
                // return "object-cover w-full h-64 sm:h-96 rounded bg-layout-sun-500 dark:bg-layout-night-500 ai-image-corner"
            }
            return "object-cover w-full rounded bg-layout-sun-500 dark:bg-layout-night-500 ai-image-corner";

        },
        nohomee(){
            if(SD() === "ab")
            {
               return false;
            }
            return true;
        },
        darkMode(){
            this.darkMode = localStorage.getItem("theme");
        },
        isRightsReady() {
      return this.$isRightsReady; // Zugriff auf globale Methode
    },
    hasRight() {
      return this.$hasRight; // Zugriff auf globale Methode
    },

    },
    methods: {
        SD,
        async loadLayout() {
    const layoutName = this.SD();
    try {
      const layout = await import(`@/Application/Homepage/Shared/${layoutName}/Layout.vue`);
      this.layoutComponent = layout.default;
    } catch (error) {
      console.warn(`Layout für Subdomain "${layoutName}" nicht gefunden, lade DefaultLayout.`, error);
      const defaultLayout = await import(`@/Application/Homepage/Shared/Layout.vue`);
      this.layoutComponent = defaultLayout.default;
    }
  },



    // Asynchrone Methode, um die Rechte zu laden
    async hasRight(right, table) {
    // Überprüfe, ob die Rechte bereits geladen wurden
    if (!this.rightsData[`${right}_${table}`] && table) {
      // Wenn die Rechte noch nicht geladen wurden, lade sie
      await this.checkRight(right, table);
    }
    // Wenn die Rechte nach dem Laden vorhanden sind, gib den Wert zurück
    return this.rightsData[`${right}_${table}`] === 1; // Beispiel: Wenn das Recht '1' ist, erlauben wir den Zugriff
  },

  async checkRight(right, table) {
    // Lade die Rechte für den User
    const value = await GetRights(right, table);
    // Speichere die geladenen Rechte im rightsData-Objekt
    this.$set(this.rightsData, `${right}_${table}`, value);
  }

  },
    async mounted() {
    // Rechte, die du brauchst dynamisch laden
    // await this.checkRight('view_table', 'images');
    // await this.checkRight('edit_table', 'blogs');
    // beliebig erweiterbar
    await loadAllRights(); // aus utils/rights.js
    this.rightsReady = true;
  },
});
</script>
<style>
.ai-image-corner{
    border-bottom-right-radius: 64px;
}
</style>


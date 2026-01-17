<template>
  <Layout>
    <MetaHeader title="Shortpoems" />

    <section class="max-w-3xl mx-auto mt-10 px-4">
      <h1 class="text-3xl font-bold mb-6 text-layout-title">Shortpoems</h1>

      <newbtn table="shortpoems" />

      <div class="flex justify-between items-center">
        <search-filter
          v-if="searchFilter"
          v-model="form.search"
          :placeholder="searchText"
          class="w-full"
          @reset="reset"
          @input="$emit('update:modelValue', $event.target.value)"
        />
      </div>

      <div
        class="p-2 md:p-4"
        v-if="Array.isArray(items.data) && items.data.length === 0 && form.search"
      >
        <alert type="warning">
          Für den vorgegebenen Suchbegriff wurden keine Shortpoems gefunden.
        </alert>
      </div>
    <form @submit.prevent="submitForm" enctype="multipart/form-data" >
    <div v-if="AID" class="container mx-auto p-4">
    <div class="grid grid-cols-6 gap-2">

        <!-- 5 Textfelder -->
      <div v-for="n in 5" :key="n" class="col-span-1">
        <InputFormText v-model="inputs[n - 1]" :placeholder="'Wort ' + n " :name="'word[' + n +  ']'" :required="true"/>
      </div>

      <!-- Button -->
      <div class="col-span-1">
        <button
          @click="submit"
          type="submit"
          class="w-full h-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 flex items-center justify-center"
        >
          Vorschlagen
        </button>

      </div>
    </div>
  </div>
  </form>
      <div
        v-for="(item, index) in items.data"
        :key="item.id || index"
        class="mb-4 border border-gray-300 dark:border-gray-600 rounded-lg"
      >
        <!-- Marker für Hash -->
        <div :id="'st' + item.id"></div>
        <button
          @click="toggle(index, item)"
          class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 flex justify-between items-center transition"
          :title="'Shortpoem anzeigen/verbergen'"
        >
          <span class="text-lg font-medium text-gray-900 dark:text-white">{{ item.headline }}</span>
          <svg
            :class="{ 'rotate-180': openIndex === index }"
            class="h-5 w-5 transform transition-transform text-gray-600 dark:text-gray-300"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <!-- Accordion-Content -->
        <div
          v-if="openIndex === index || items.total == '1'"
          :ref="`content-${item.id}`"
          class="px-4 py-3 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-300 border-t border-gray-300 dark:border-gray-700"
        >
          <p>{{ item.story || 'Kein Text vorhanden.' }}</p>
          <editbtns :id="item.id" table="shortpoems" /><br />
          <RatingWrapper
            :postId="item.id"
            table="shortpoems"
            />

          <SocialButtons :postId="item.id" :xslug="true" :sse="item.headline"/>
        </div>

        <!-- Accordion Button -->

      </div>
    </section>

    <!-- Pagination -->
    <div class="flex items-center justify-center flex-wrap mt-6 -mb-1 text-xs md:text-base bg-transparent text-layout-sun-700 dark:text-layout-night-700">
      <template v-for="(link, index) in items.links" :key="index">
        <div
          v-if="!link.url"
          class="flex items-center px-3 py-0.5 mx-1 mb-1 rounded-md cursor-not-allowed"
        >
          <span v-if="link.label === 'pagination.previous'">&laquo; Zurück</span>
          <span v-else-if="link.label === 'pagination.next'">Weiter &raquo;</span>
        </div>

        <a
          v-else-if="link.active"
          href="#"
          @click.prevent="$inertia.get(link.url)"
          class="flex items-center px-2.5 py-0.5 mx-1 mb-1 h-7 transition-colors duration-200 transform rounded-md border border-primary-sun-500 text-primary-sun-900 dark:border-primary-night-500 dark:text-primary-night-900 hover:bg-layout-sun-200 hover:text-layout-sun-800 dark:hover:bg-layout-night-200 dark:hover:text-layout-night-800 font-bold"
        >
          <span v-html="link.label"></span>
        </a>

        <a
          v-else
          href="#"
          @click.prevent="$inertia.get(link.url)"
          class="flex items-center px-2.5 py-0.5 mx-1 mb-1 h-7 transition-colors duration-200 transform rounded-md border hover:bg-layout-sun-200 hover:text-layout-sun-800 dark:hover:bg-layout-night-200 dark:hover:text-layout-night-800"
        >
          <span v-if="link.label === 'pagination.previous'">&laquo; Zurück</span>
          <span v-else-if="link.label === 'pagination.next'">Weiter &raquo;</span>
          <span v-else v-html="link.label"></span>
        </a>
      </template>
    </div>
  </Layout>
</template>

<script>
import Layout from '@/Application/Homepage/Shared/Layout.vue';
import axios from "axios";
import { route } from "ziggy-js";
import MetaHeader from '@/Application/Homepage/Shared/MetaHeader.vue';
import newbtn from '@/Application/Components/Form/newbtn.vue';
import SearchFilter from '@/Application/Components/Lists/SearchFilter.vue';
import InputFormText from '@/Application/Components/Form/InputFormText_sm.vue';
import Alert from '@/Application/Components/Content/Alert.vue';
import editbtns from '@/Application/Components/Form/editbtns.vue';
import SocialButtons from "@/Application/Components/Social/socialButtons.vue";
import RatingWrapper from "@/Application/Components/Social/RatingWrapper.vue";
import pickBy from "lodash/pickBy";
// import Toast from "@/Application/Components/Content/Toast.vue";
import { toastBus } from '@/utils/toastBus';
import { throttle } from "lodash";

export default {
    name:"ShortPoems",
  components: {
    Layout, MetaHeader, newbtn, SearchFilter, Alert, editbtns, SocialButtons, RatingWrapper,InputFormText,
  },
  props: {
    items: { type: Object, required: true },
    ratings: { type: [Array, Object], default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
    searchFilter: { type: Boolean, default: true },
    searchText: { type: String, default: "Hier kannst du den Suchbegriff eingeben" },
  },
  data() {
    return {
      openIndex: null,
      inputs: ['', '', '', '', ''],
      form: { search: this.filters?.search ?? "" },
    };
  },
  computed:{
    AID(){
        return window.authid;
    }

  },
  watch: {
    form: {
      handler: throttle(function () {
        const query = pickBy(this.form, v => v != null && v !== '');
        this.$inertia.get(
          this.route("home.shortpoems"),
          Object.keys(query).length ? query : { remember: "forget" },
          { preserveState: true, preserveScroll: false, replace: true }
        );
      }, 150),
      deep: true
    },
    items: {
      immediate: true,
      async handler() {
        await this.$nextTick();
        this.scrollToHash();
      }
    }
  },
  methods: {
   async submitForm(){
        const payload = this.inputs.join(", ");
        const response = await axios.post(route('save.shp'), {
        words: payload
        });
//         console.log("RED: " + response.data);
        window.toastBus.emit( response.data); // ← erwartet { status: "...", message: "..." }
    },
    reset() { this.form.search = null },

    toggle(index, item) {
      const opening = this.openIndex !== index;
      this.openIndex = opening ? index : null;
      if (!opening || !item?.id) return;
      this.$nextTick(() => this.scrollToItem(item.id));
    },

    async scrollToHash() {
  const hash = window.location.hash;
  if (!hash) return;

  const id = hash.replace('#st', '');

  const index = this.items.data.findIndex(
    item => String(item.id) === id
  );
  if (index === -1) return;

  // Accordion öffnen
  this.openIndex = index;

  // 1️⃣ Warten bis Vue gerendert hat
  await this.$nextTick();

  // 2️⃣ Warten bis Layout stabil ist (SEHR wichtig)
  await new Promise(r => requestAnimationFrame(r));
  await new Promise(r => requestAnimationFrame(r));

  // 3️⃣ Marker verwenden (NICHT content!)
  const marker = document.getElementById(`st${id}`);
  if (!marker) return;

  const y =
    marker.getBoundingClientRect().top +
    window.pageYOffset -
    180;

  window.scrollTo({
    top: y,
    behavior: 'auto' // ❗ beim Laden niemals smooth
  });
},

    async waitForRef(refName) {
      return new Promise(resolve => {
        const check = () => {
          const el = this.$refs[refName]?.[0];
          if (el) resolve(el);
          else requestAnimationFrame(check);
        };
        check();
      });
    },

    scrollToItem(id) {
      const content = this.$refs[`content-${id}`]?.[0];
      if (!content) return;

      const doScroll = () => {
        const y = content.getBoundingClientRect().top + window.pageYOffset - 170;
        window.scrollTo({ top: y, behavior: 'smooth' });
      };

      const imgs = content.querySelectorAll('img');
      if (!imgs.length) return doScroll();

      let loaded = 0;
      imgs.forEach(img => {
        if (img.complete) loaded++;
        else img.addEventListener('load', () => { loaded++; if (loaded === imgs.length) doScroll(); });
      });
      if (loaded === imgs.length) doScroll();
    }
  },
  mounted() {
    // initial scroll beim Laden
    this.scrollToHash();
  }
};
</script>
<style>
INPUT.words{
    max-width:80% !important;
    min-width: 80% !important;
    margin:0px;
}
</style>

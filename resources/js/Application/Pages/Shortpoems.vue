<template>
    <Layout>
      <MetaHeader title="Meine Shortpoems Übersicht" />
      <section class="max-w-3xl mx-auto mt-10 px-4">
        <h1 class="text-3xl font-bold mb-6 text-layout-title">Shortpoems</h1>


        <newbtn table="shortpoems">
        </newbtn>
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
        <div class="p-2 md:p-4" v-if="Array.isArray(items.data) && items.data.length === 0 && form.search">
          <alert type="warning">
            Für den vorgegebenen Suchbegriff wurden keine Shortpoems gefunden.
          </alert>
        </div>
        <div
          v-for="(item, index) in items.data"
          :key="item.id || index"
          class="mb-4 border border-gray-300 dark:border-gray-600 rounded-lg"
        >
          <div :id="'st' + item.id"></div>
          <button
            @click="toggle(index)"
            class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 flex justify-between items-center transition"
            :aria-expanded="openIndex === index"
            :aria-controls="'st' + item.id"
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

          <div
            v-if="openIndex === index"
            class="px-4 py-3 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-300 border-t border-gray-300 dark:border-gray-700"
          >
            <p>{{ item.story || 'Kein Text vorhanden.' }}&nbsp;&nbsp;</p>
            <editbtns :id="item.id" table="shortpoems" /><br />
            <averageRating
              :postId="item.id"
              :av="parseFloat(ratings['original'][item.id]?.average) || 0"
              :tot="ratings['original'][item.id]?.total || 0"
            />
            <SocialButtons :postId="item.id" :xslug="true"/>
          </div>
        </div>
      </section>

      <!-- Pagination -->
      <div class="flex items-center justify-center flex-wrap mt-6 -mb-1 text-xs md:text-base bg-transparent text-layout-sun-700 dark:text-layout-night-700">
        <template v-for="(link, index) in items.links" :key="index">
          <!-- Deaktivierte Links -->
          <div
            v-if="!link.url"
            class="flex items-center px-3 py-0.5 mx-1 mb-1 rounded-md cursor-not-allowed"
          >
            <span v-html="link.label"></span>
          </div>

          <!-- Aktive Seite -->
          <a
            v-else-if="link.active"
            href="#"
            @click.prevent="$inertia.get(link.url)"
            class="flex items-center px-2.5 py-0.5 mx-1 mb-1 h-7 transition-colors duration-200 transform rounded-md border border-primary-sun-500 text-primary-sun-900 dark:border-primary-night-500 dark:text-primary-night-900 hover:bg-layout-sun-200 hover:text-layout-sun-800 dark:hover:bg-layout-night-200 dark:hover:text-layout-night-800 font-bold"
          >
            <span v-html="link.label"></span>
          </a>

          <!-- Normale Links -->
          <a
            v-else
            href="#"
            @click.prevent="$inertia.get(link.url)"
            class="flex items-center px-2.5 py-0.5 mx-1 mb-1 h-7 transition-colors duration-200 transform rounded-md border hover:bg-layout-sun-200 hover:text-layout-sun-800 dark:hover:bg-layout-night-200 dark:hover:text-layout-night-800"
          >
            <span v-html="link.label"></span>
          </a>
        </template>
      </div>
    </Layout>
  </template>

  <script>
  import Layout from '@/Application/Homepage/Shared/Layout.vue';
  import editbtns from '@/Application/Components/Form/editbtns.vue';
  import SocialButtons from "@/Application/Components/Social/socialButtons.vue";
  import averageRating from "@/Application/Components/Social/averageratings.vue";
  import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
  import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
  import Alert from "@/Application/Components/Content/Alert.vue";
  import newbtn from "@/Application/Components/Form/newbtn.vue";

  import pickBy from "lodash/pickBy";
  import mapValues from "lodash/mapValues";
  import { throttle } from "lodash";

  export default {
    components: {
      Layout,
      editbtns,
      SocialButtons,
      averageRating,
      MetaHeader,
      SearchFilter,
      Alert,
      newbtn,
    },
    computed: {
        search() {
    // return this.filters?.search ?? "";
  },
    },
    props: {
      items: {
        type: Object,
        required: true,
      },
      ratings: {
        type: [Array, Object],
        default: () => ({}),
      },
      filters: {
        type: Object,
        default: () => ({}),
      },
      searchFilter: {
        type: Boolean,
        default: true,
      },
      searchText: {
        type: String,
        default: "Hier kannst du den Suchbegriff eingeben",
      },
    //   searchValue: {
    //     type: String,
    //     default: null,
    //   },
      links: {
        type: Array,
        default: () => [],
      },
    },
    data() {
      return {
        openIndex: null,
        form: {
            search: this.filters?.search ?? "",

        }
      };
    },
    watch: {
      form: {
        handler: throttle(function () {
          const query = pickBy(this.form, (v) => v != null && v !== '');
          this.$inertia.get(
            this.route("home.shortpoems"),
            Object.keys(query).length ? query : { remember: "forget" },
            {
              preserveState: true,
              replace: true,
            }
          );
        }, 150),
        deep: true,
      },
    },
    methods: {
      reset() {
        this.form.search = null;
      },
      toggle(index) {
        this.openIndex = this.openIndex === index ? null : index;
      },
    },
    mounted() {
        const hash = window.location.hash;
            if (hash && hash.startsWith("#st")) {
                const id = hash.replace("#st", "");
                const index = this.items.data.findIndex((item) => String(item.id) === id);


                if (index !== -1) {
                this.openIndex = index;

                // Warten, bis das Element sichtbar gerendert ist
                this.$nextTick(() => {
                    const el = document.getElementById(`st${id}`);
                    if (el) {
                    const y = el.getBoundingClientRect().top + window.pageYOffset - 115;
                    window.scrollTo({ top: y, behavior: 'smooth' });
                    }
                });
                }
            }

    },
  };
  </script>

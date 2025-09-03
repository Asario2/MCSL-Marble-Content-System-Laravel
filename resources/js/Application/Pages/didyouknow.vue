    <template>
        <Layout>
            <MetaHeader title="Wussten Sie Schon ?" />
        <section class="max-w-3xl mx-auto mt-10 px-4">
            <h1 class="text-3xl font-bold mb-6 text-layout-title">Wussten Sie schon...</h1>
            <newbtn table="didyouknow">
            </newbtn>
            <div class="flex justify-between items-center">
                <search-filter
                            v-if="searchFilter"
                            v-model="form.search"
                            class="w-full"
                            ref="searchField"
                            @reset="reset"
                            @input="onSearchInput"
                            />

                </div>
                <div class="p-2 md:p-4" v-if="Array.isArray(items.data) && items.data.length === 0 && form.search">
                <alert type="warning">
                    Für den vorgegebenen Suchbegriff wurden keine DidYouKnows gefunden.
                </alert>
            </div>
            <div v-for="(item, index) in items.data" :key="item.id || index" class="mb-4 border border-gray-300 dark:border-gray-600 rounded-lg">
                <span :id="'st' + item.id"></span>
                <button
                @click="toggle(index)"
                class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 flex justify-between transition"
            >
                <span class="text-lg font-medium text-gray-900 dark:text-white">Wussten Sie schon, {{ item.headline }}</span>
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
                {{ item.answer }}&nbsp;&nbsp;<editbtns :id="item.id" table="didyouknow"></editbtns><br />
                <averageRating
              :postId="item.id"
              :av="parseFloat(ratings['original'][item.id]?.average) || 0"
              :tot="ratings['original'][item.id]?.total || 0"
            />
                <SocialButtons :postId="item.id" slug="" :xslug="true"/>
            </div>
            </div>
        </section>
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
                            :href="link.url"
                            class="flex items-center px-2.5 py-0.5 mx-1 mb-1 h-7 transition-colors duration-200 transform rounded-md border border-primary-sun-500 text-primary-sun-900 dark:border-primary-night-500 dark:text-primary-night-900 hover:bg-layout-sun-200 hover:text-layout-sun-800 dark:hover:bg-layout-night-200 dark:hover:text-layout-night-800 font-bold"
                        >
                            <span v-html="link.label"></span>
                        </a>

                        <!-- Normale Links -->
                        <a
                            v-else
                            :href="link.url"
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
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import editbtns from '@/Application/Components/Form/editbtns.vue';
import newbtn from "@/Application/Components/Form/newbtn.vue";
import SocialButtons from "@/Application/Components/Social/socialButtons.vue";
import averageRating from "@/Application/Components/Social/averageratings.vue";
import pickBy from "lodash/pickBy";
import mapValues from "lodash/mapValues";
import { throttle } from "lodash";
import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
import Alert from "@/Application/Components/Content/Alert.vue";
export default {
  components: {
    Layout,
    editbtns,
    SocialButtons,
    averageRating,
    SearchFilter,
    MetaHeader,
    newbtn,
    Alert,
  },
  props: {
    items: {
      type: [Array, Object],
      required: false,
    },
    ratings: {
      type: [Array, Object],
      default: () => ([]),
    },
    searchFilter: {
      type: Boolean,
      default: true,
    },
    filters: {
      type: Object,
      default: () => ({}),
    },
    searchText: {
      type: String,
      default: "Hier kannst du den Suchbegriff eingeben",
    },
    searchValue: {
      type: String,
      default: null,
    },
    links: {
      type: Array,
      default: () => ([]),
    },
  },
  data() {
    return {
      openIndex: null,
      form: {
        // Hier form.search initial mit filters.search befüllen oder leer
        search: this.filters?.search ?? "",
      },
    };
  },
  watch: {
    form: {
      handler: throttle(function () {
        const query = pickBy(this.form);
        if (Object.keys(query).length === 0) return; // Keine Anfrage wenn leer
        this.$inertia.get(
          this.route("home.didyouknow"),
          query,
          {
            preserveState: true,
            replace: true,
          }
        );
      }, 150),
      deep: true,
      immediate: false,
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null);
    },
    toggle(index) {
      this.openIndex = this.openIndex === index ? null : index;
    },
  },
  mounted() {
    const hash = window.location.hash;
    if (hash && hash.startsWith("#st")) {
      // Sicherstellen, dass items.data ein Array ist
      const data = Array.isArray(this.items) ? this.items : (this.items?.data ?? []);
      const id = hash.replace("#st", "");
      const index = data.findIndex((item) => String(item.id) === id);
      if (index !== -1) {
        this.openIndex = index;
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

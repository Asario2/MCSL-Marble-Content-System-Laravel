<template>
    <layout
        header-title="Blog"
        :header-url="$page.props.saas_url + '/blogs'"
        :header-image="$page.props.saas_url + '/images/blogimages/Blog_Idee_480x360.jpg'"
    >
    <MetaHeader title="Asarios Blog" />
        <section class="bg-layout-sun-0 text-layout-sun-800 dark:bg-layout-night-0 dark:text-layout-night-800">
            <div class="p-2 md:p-4" v-if="blogs.data.length === 0 && !form.search">
                <alert type="warning">
                    Zurzeit liegen keine Blogartikel vor!
                </alert>
            </div>

            <div class="p-2 md:p-4">
                <page-title>
                    <template #title> Asarios Blog </template>
                </page-title>
                <newbtn table="blogs">
                </newbtn>
                <div class="flex justify-between items-center">
                    <search-filter
                        v-model="form.search"
                        class="w-full"

                        @reset="reset"
                    />
                </div>
                <div v-if="blogs.data.length === 0 && form.search">
                    <alert type="warning">
                        Für den vorgegebenen Suchbegriff wurden keine Blogartikel gefunden.
                    </alert>
                </div>

                <div v-if="blogs.data.length > 0">
                    <blog-preview-big :blog="blogs.data[0]" />

                    <div class="mt-8 grid justify-center grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <template v-for="(blog, index) in blogs.data" :key="blog.id">
                            <blog-preview-small
                                v-if="index > 0"
                                :blog="blog"
                                :blogs="blogs.data"
                            />
                        </template>
                    </div>
                </div>

                <div v-if="!blogs.data" class="np-dl-td-no-entries">
                    <alert type="info">{{ noEntries }}</alert>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-center flex-wrap mt-6 -mb-1 text-xs md:text-base bg-transparent text-layout-sun-700 dark:text-layout-night-700">
                    <template v-for="(link, index) in blogs.links" :key="index">
                        <!-- Deaktivierte Links -->
                        <div
                            v-if="!link.url"
                            class="flex items-center px-3 py-0.5 mx-1 mb-1 rounded-md cursor-not-allowed"
                        >
                        <span v-if="link.label === 'pagination.previous'">&laquo; Zurück</span>
                        <span v-else-if="link.label === 'pagination.next'">Weiter &raquo;</span>
                        <span v-else v-html="link.label"></span>
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
                        <span v-if="link.label === 'pagination.previous'">&laquo; Zurück</span>
                        <span v-else-if="link.label === 'pagination.next'">Weiter &raquo;</span>
                        <span v-else v-html="link.label"></span>

                        </a>
                    </template>
                </div>
            </div>
        </section>
    </layout>
</template>

<script>
import { defineComponent } from "vue";
import newbtn from "@/Application/Components/Form/newbtn.vue";

import Layout from "@/Application/Homepage/Shared/Layout.vue";
import PageTitle from "@/Application/Components/Content/PageTitle.vue";
import BlogPreviewBig from "@/Application/Homepage/Shared/BlogPreviewBig.vue";
import BlogPreviewSmall from "@/Application/Homepage/Shared/BlogPreviewSmall.vue";
import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
import Alert from "@/Application/Components/Content/Alert.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import { throttle, pickBy, mapValues } from 'lodash';
import { Inertia } from "@inertiajs/inertia";
//import { safeInertiaGet } from '@/utils/inertia';

export default defineComponent({
    name: "Homepage_BlogList",

    components: {
        Layout,
        PageTitle,
        MetaHeader,
        newbtn,
        BlogPreviewBig,
        BlogPreviewSmall,
        SearchFilter,
        Alert,
    },

    props: {
        blogs: {
            type: Object,
            default: () => ({}),
        },
        filters: {
            type: Object,
            default: () => ({}),
        },
        blogImage: {
            type: String,
            default: "",
        },
        noEntries: {
            type: String,
            default: "Keine Einträge gefunden",
        },
    },

    data() {
        return {
            form: {
                search: this.filters.search,
            },
        };
    },
    watch: {
  form: {
    deep: true,
    handler: throttle(function () {
      // Loader sofort aktivieren
      this.loading = true;

      // Leere Werte filtern
      const query = pickBy(this.form, (v) => v !== "" && v !== null);

      // Immer search param senden, auch wenn leer
      const params = { ...query, table: this.table };
      if (!params.search) params.search = null;

      this.$inertia.visit(
        this.route("home.blog.index"),
        {
          method: "get",
          data: params,
          preserveState: true,
          replace: true,
          onFinish: () => {
            this.loading = false; // Loader ausblenden nach Inertia-Finish
          },
        }
      );
    }, 500), // throttle auf 500ms
  },
},
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null);
        },
    },
    mounted() {
        const params = new URLSearchParams(window.location.search);
    const search = params.get("search");

    // Wenn search gesetzt ist, verstecke das Loading-Div
    if (search && search.trim() !== "") {

      this.isLoading = false;
    }
    else{
        this.isLoading = true;
    }
}
});
</script>


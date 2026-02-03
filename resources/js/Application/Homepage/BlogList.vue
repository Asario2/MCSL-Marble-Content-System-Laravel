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
<pre>{{ pag }}</pre>
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
              <Pagination :links="pagination.links" :basePath="'blogs' + '/'"/>
              {{ pagination }}
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
import Pagination from "@/Application/Components/Pagination.vue";
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
        Pagination,
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
        pagination:{
            type:[Object,Array],

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


      const params = {
        ...this.form,
        table: this.table,
        search: this.form.search?.trim() || null, // NUR hier trimmen
      };

//       console.log('before:', JSON.stringify(this.form.search));

      this.$inertia.visit(this.route("home.blog.index"), {
        method: "get",
        data: params,
        preserveState: true,
        replace: true,
        skipLoading: true,
        onFinish: () => {
          this.loading = false;
        },
      });
//     console.log('after:', JSON.stringify(this.form.search));
    }, 500),


},

},

    methods: {
        reset() {
            this.form = mapValues(this.form, () => null);
        },
    },
    mounted() {

}
});
</script>


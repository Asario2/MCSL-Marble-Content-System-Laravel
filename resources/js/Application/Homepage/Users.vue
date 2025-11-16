    <template>
    <layout
        header-title="Blog"
        :header-url="$page.props.saas_url + '/blogs'"
        :header-image="$page.props.saas_url + '/images/blogimages/Blog_Idee_480x360.jpg'"
    >
    <MetaHeader title="Benutzerliste" />

    <newbtn table="users">

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
        <section class="bg-layout-sun-0 text-layout-sun-800 dark:bg-layout-night-0 dark:text-layout-night-800">
            <div class="p-2 md:p-4" v-if="users.data?.length === 0 && !form.search">
                <alert type="warning">
                    Zurzeit liegen keine Benutzer vor!
                </alert>
            </div>

            <div class="p-2 md:p-4">
    <page-title>
        <template #title> Benutzerlsite </template>
    </page-title>

    <div class="flex justify-between items-center">
        <search-filter
            v-model="form.search"
            class="w-full"
            @reset="reset"
        />
    </div>
    <div v-if="users.data?.length === 0 && form.search">
        <alert type="warning">
            Für den vorgegebenen Suchbegriff wurden keine Benutzer gefunden.
        </alert>
    </div>

    <div v-if="users.data?.length > 0">
        <div class="mt-8 grid justify-center grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <template v-for="(user, index) in users.data" :key="user.id">
                <user-preview-small
                    :user="user"
                />
            </template>

        </div>
    </div>

    <div v-if="!users.data" class="np-dl-td-no-entries">
        <alert type="info">{{ noEntries }}</alert>
    </div>
</div> <!-- ← das ist die einzig notwendige schließende div -->

                <!-- Pagination -->
                <div class="flex items-center justify-center flex-wrap mt-6 -mb-1 text-xs md:text-base bg-transparent text-layout-sun-700 dark:text-layout-night-700">
                    <template v-for="(link, index) in users.links" :key="index">
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


        </section>
    </layout>
</template>

<script>
import { defineComponent } from "vue";
import newbtn from "@/Application/Components/Form/newbtn.vue";
import Layout from "@/Application/Homepage/Shared/Layout.vue";
import PageTitle from "@/Application/Components/Content/PageTitle.vue";
import BlogPreviewBig from "@/Application/Homepage/Shared/BlogPreviewBig.vue";
import UserPreviewSmall from "@/Application/Homepage/Shared/UserPreviewSmall.vue";
import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
import Alert from "@/Application/Components/Content/Alert.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import mapValues from "lodash/mapValues";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";

export default defineComponent({
    name: "Homepage_BlogList",

    components: {
        Layout,
        PageTitle,
        BlogPreviewBig,
        UserPreviewSmall,
        SearchFilter,
        Alert,
        MetaHeader,
        newbtn,
    },

    props: {
        users: Object,
        data: {
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
            handler: throttle(function () {
                const query = pickBy(this.form);

                this.$inertia.get(
                    this.route(
                        "home.userlist",
                        Object.keys(query).length ? query : { remember: "forget" },
                    ),
                    this.form,
                    {
                        preserveState: true,
                        replace: true,
                    },
                );
            }, 150),
            deep: true,
            immediate: false,
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
},
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null);
        },
    },
});
</script>


    <template>
        <Layout>
            <MetaHeader title="Meine Bilder Übersicht" />
            <newbtn table="images">
            </newbtn>


        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
            <div class="container mx-auto py-8">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Bildergalerien</h1>
            </div>

            <!-- Grid für Bilder -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                <div
                v-for="(ima, index) in data"
                :key="ima.id"
                class="bg-white dark:bg-gray-800 p-2 rounded-lg shadow-md relative group overflow-hidden"
                >
                <a :href="route('home.images.gallery', [ima.slug])">
                    <!-- Bild mit Zoom-In Effekt -->
                    <div class="relative w-full overflow-hidden rounded-sm">
                    <img
                        :src="`/images/_ab/images/images_cat/${ima.name}.jpg`"
                        :alt="ima.shortname"
                        :title="'zur ' + ima.shortname + 'Galerie'"
                        class="w-full h-full object-cover transition-transform duration-500 ease-in-out transform group-hover:scale-110"
                    />
                    <!-- Overlay in Blau -->
                    <div
                        class="absolute top-0 left-0 w-full h-full bg-blue-500 opacity-40 transition-opacity duration-300 group-hover:opacity-0"
                    ></div>
                    </div>

                    <!-- Text oben -->
                    <div
                    class="absolute left-0 right-0 text-center text-white transition-all duration-300 bott title-top"
                    >
                    <span
                        class="text-lg font-semibold group-hover:text-shadow-md transition-all duration-300 capitalize font-ald2"
                    >
                        {{ ima.name }}
                </span>
                    </div>

                    <!-- Text unten -->
                    <div
                    class="absolute bott2 left-0 right-0 text-center text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                    >
                    <span class="text-sm opacity-75 text-shadow ucfirst">
                        {{ decodeEntities(ima.heading_alt) }}
                    </span>
                    </div>
                </a>
                </div>
            </div>
            </div>
        </div>
        </Layout>
    </template>

    <script>
    import Layout from "@/Application/Homepage/Shared/Layout.vue";
    import he from 'he';
    import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
    import { GetRights } from "@/helpers";
    import mapValues from "lodash/mapValues";
    import newbtn from "@/Application/Components/Form/newbtn.vue";

    import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
    import pickBy from "lodash/pickBy";
    import throttle from "lodash/throttle";
    export default {
        components: { Layout, MetaHeader, SearchFilter,newbtn },
        props: {
        data: Array,
        ima: Object,
        filters: {
        type: Object,
        default: () => ({ search: '' }),
    },

        },
        created() {
//         console.log('filters:', this.filters);
//         console.log('form.search:', this.form.search);
        },

        methods: {
        decodeEntities(text) {
            return he.decode(text);

        },
        reset() {
                this.form = mapValues(this.form, () => null);
            },
        },
        watch: {
                    form: {
                        handler: throttle(function () {
                            const query = pickBy(this.form);

                            this.$inertia.get(
                                this.route('home.images.search.cat'),
                                query,
                                {
                                    preserveState: true,
                                    replace: true,
                                }
                            );
                        }, 150),
                        deep: true,
                    },
                },
        data() {
            return {
                form: {
                search: this.filters?.search ?? "",
                },
            };
        },
    };
    </script>

    <style scoped>
    /* Bildgröße */
    .w-full5 {
        width: 260px;
        height: 250px;
    }

    /* Hover-Effekt für obere Titelzeile */
    .group:hover .title-top {
        transform: translateY(-1.5rem);
    }

    /* Textschatten im Hover */
    /* .group:hover .group-hover\:text-shadow-md {
        /*text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.8);
    } */

    /* Sichtbarkeit Hover-Text */
    .group:hover .group-hover\:opacity-100 {
        opacity: 1;
    }

    /* Textschatten für untere Zeile */
    .text-shadow {
color:#000;
background-color:#fff;
border-radius:3px;
padding:0 3px;
margin:7px;
    }
    .font-ald2{
        font-family:ald2;
        font-size:25px;
    }
    /* Position obere Zeile */
    .bott {
        bottom: 120px;
    }

    /* Position untere Hover-Zeile */
    .bott2 {
        top: 140px;
    }

    /* Großbuchstaben am Anfang */
    .ucfirst::first-letter {
        text-transform: uppercase;
    }
    </style>

<template>

    <div class="blog-container w-full max-w-sm mx-auto group rounded hover:no-underline focus:no-underline bg-layout-sun-100 dark:bg-layout-night-100">
        <Link
            :href="route('home.blog.show', blog.autoslug)"
            class="block text-layout-sun-600 dark:text-layout-night-900"
        >

            <div class="relative">
                <img
                    role="presentation"
                    :class="['object-cover w-full rounded h-44 bg-layout-sun-100 dark:bg-layout-night-100', blog.madewithai ? 'ai-image-corner' : '']"
                    :src="`/images/_ab/blogs/image_path/${blog.url}` || '/images/blogs/008.jpg'"
                    :alt="blog.name"

                    width="480"
                    height="360"
                />
                <div v-if="blog.madewithai">
                    <AiButton :dma="dmaa"></AiButton>
                </div>
            </div>

            <div class="p-6 space-y-2">
            <div class="flex items-center justify-between w-full">
                <!-- Blog-Titel -->
                <h2
                class="text-2xl font-semibold font-title group-hover:underline group-focus:underline"
                >
                {{ blog.title }}
                </h2>

                <!-- Blog-Kategorie -->
                <div
                v-if="blog.category_name"
                class="text-sm min-w-fit min-h-fit bg-primary-sun-500 text-primary-sun-900
                        dark:bg-primary-night-500 dark:text-primary-night-900
                        font-semibold px-2.5 py-0.5 rounded-lg whitespace-nowrap ml-4 mt-3 mr-4"
                >
                {{ blog.category_name }}
                </div>
                <span v-if="blog.xis_anzeige" class="mt-[5px] ml-[-72px]">
            <br /><br />
                            <img :src="'/images/_ab/web/anzeige.png'" alt="Anzeige" class="minw-[70px]"/>
                        </span>
            </div>

                <div class="flex justify-between items-center">
                    <div class="text-xs text-layout-sun-600 dark:text-layout-night-600 py-2">
                        <display-date :value="blog.blog_date" :time-on="false" />
                        von <span>{{ blog.author_name }}</span>
                    </div>
                    <div class="rl">
                        <editbtns  :id="blog.id" table="blogs"></editbtns>
                    </div>
                </div>

                <p v-html="smilies(blog.summary)"></p>
                <div>
                    <display-number class="text-xs text-layout-sun-600 dark:text-layout-night-600 py-2"
                :value="blog.reading_time"
                    :after-digits="0"
                    value-unit="Minuten Lesezeit"
                    value-single-unit="Minute Lesezeit"
                ></display-number>
            </div>
            </div>
        </Link>

        <!-- **Tabelle direkt unterhalb des Blog-Containers (fixiert)** -->
        <SocialButtons :postId="blog.id" sm="Sm" :slug="blog.autoslug"  :nostars="true"       />

    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import IconPlusCircle from "@/Application/Components/Icons/PlusCircle.vue";
import { CleanTable, rumLaut } from '@/helpers';
import averageRating from "@/Application/Components/Social/averageratings.vue";
import IconPencil from "@/Application/Components/Icons/Pencil.vue";
import Comments from "@/Application/Components/Social/comments.vue";
import Share from "@/Application/Components/Social/share.vue";
import SocialButtons from "@/Application/Components/Social/socialButtons.vue";
import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
import IconCamera from "@/Application/Components/Icons/Camera.vue";
import editbtns from "@/Application/Components/Form/editbtns.vue";
import IconComment from "@/Application/Components/Icons/IconComment.vue";
import { throttle } from "lodash";
import { CleanTable_alt, replaceSmilies } from '@/helpers';
import mapValues from "lodash/mapValues";
import pickBy from "lodash/pickBy";
import IconEye from "@/Application/Components/Icons/Eye.vue";
import IconTrash from "@/Application/Components/Icons/Trash.vue";
import DisplayDate from "@/Application/Components/Content/DisplayDate.vue";
import DisplayNumber from "@/Application/Components/Content/DisplayNumber.vue";
import he from "he";
import AiButton from "@/Application/Components/Content/AiButton.vue";
import { nextTick } from "vue";
import { route } from "ziggy-js";
import IconShare from "@/Application/Components/Icons/IconShare.vue";
import IconStar from "@/Application/Components/Icons/IconStar.vue";
export default {
    name: "Shared_Homepage_BlogPreviewSmall",
    //
    components: {
        Link,
    DisplayDate,
    IconPencil,
    DisplayNumber,
    IconPlusCircle,
    editbtns,
    IconTrash,
    IconCamera,
    SearchFilter,
    Comments,
    Share,
    IconComment,
    IconShare,
    IconStar,
    SocialButtons,
    AiButton, MetaHeader},

    props: {
        blog: {
            type: Object,
        },
        blogs: {
            type: Object,
        },
        aiOverlayImage: {
            type: String,
        },
    },
    data() {
    return {
        dma: localStorage.getItem('theme'),
        showShareBox: {} ,
        showStarBox: {}, // Wert aus localStorage speichern
        showComments: null, // Zustand für die Anzeige der Kommentarfunktion
    };
},
methods:{
    smilies(text){
return replaceSmilies(text);
    },
    handleBodyClick(event) {
        this.$nextTick(() => {
            const box = document.getElementById("commentBox");
            if (box && !box.contains(event.target)) {
                this.showComments = null;
            }
        });
    },
    openComments(id) {
        this.showComments = this.showComments === id ? null : id;
        // console.log("showComments:", this.showComments);
    },
    closeComments() {
        this.showComments = null;
    },

    reset() {
        this.form = mapValues(this.form, () => null);
    },
    toggleStarBox(id) {
    // console.log(`toggleStarBox wurde für ID ${id} aufgerufen`);

    // Sicherstellen, dass showShareBox existiert
    if (!this.showStarBox) {
        this.showStarBox = {};
    }

    this.showStarBox[id] = !this.showStarBox[id];

    },
    toggleShareBox(id) {
    // console.log(`toggleShareBox wurde für ID ${id} aufgerufen`);

    // Sicherstellen, dass showShareBox existiert
    if (!this.showShareBox) {
        this.showShareBox = {};
    }

    this.showShareBox[id] = !this.showShareBox[id];

    // console.log("showShareBox:", this.showShareBox);

    if (this.showShareBox[id]) {
        this.$nextTick(() => {
            const shariffRef = this.$refs['shariff_' + id];
            if (!shariffRef) {
                console.error(`Shariff-Element für ID ${id} nicht gefunden.`);
                return;
            }
            // console.log(`Shariff für ID ${id} wird initialisiert...`);
            this.initShariff(id);
        });
    } else {
        this.$nextTick(() => {
            const shariffRef = this.$refs['shariff_' + id];
            if (shariffRef) {
                // console.log(`Shariff-Inhalt für ID ${id} wird geleert.`);
                shariffRef.innerHTML = "";
            }
        });
    }
},

        initShariff(id) {
    nextTick(() => {
        const shariffRef = this.$refs['shariff_' + id];
        if (!shariffRef) {
            console.error(`Shariff-Element für ID ${id} nicht gefunden`);
            return;
        }

        // console.log(`Shariff wird für ID ${id} initialisiert`, shariffRef);
        new Shariff(shariffRef, {
            services: ["facebook", "telegram", "whatsapp", "xing", "twitter"],
            theme: "classic",
            orientation: "horizontal",
        });
    });


    },

    toggleCommentBox(postId) {
        this.showComments = this.showComments === postId ? null : postId;
    },

    decodeEntities(text) {
        if (text) {
            text = text?.replace(/<br\s*\/?>/g, "\n");
            return he.decode(text);
        } else {
            return "";
        }
    },

    deleteDataRow(id) {
        if (confirm("Wollen Sie diesen Beitrag wirklich löschen?")) {
            axios
                .delete(this.routeDelete + id)
                .then(() => {
                    this.$emit("deleted");
                    this.$inertia.reload();
                })
                .catch((error) => {
                    console.error("Fehler beim Löschen:", error);
                });
        }
    },

    editDataRow(id) {
        const table = CleanTable();

        var rt = `/admin/tables/edit/${id}/images`;

        location.href = rt;
    },
},
};
</script>
<style scoped>
.SmMaTable{
margin-left:-7px;
}
</style>


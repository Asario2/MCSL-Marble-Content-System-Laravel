<template>

    <div class="blog-container w-full max-w-sm mx-auto group rounded hover:no-underline focus:no-underline bg-layout-sun-100 dark:bg-layout-night-100">
        <Link
            :href="'/home/users/show/' + user.name + '/' + user.id"
            class="block"
        >

            <div class="relative flex justify-center divvy">
                <!-- {{ '/images' + user.profile_photo_path }} -->
                <img
                    role="presentation"
                    :class="['object-cover w-full rounded h-60 w-60 bg-layout-sun-500 dark:bg-layout-night-500 teaser']"
                    :src="user.profile_photo_path != null ?  '/images/' + user.profile_photo_path.replace('/images/','') : '/images/profile-photos/008.jpg'"
                    :alt="user.name"

                    width="480"
                    height="360"
                />

            </div>

            <div class="p-6 space-y-2">

                <h2 class="text-2xl font-semibold font-title group-hover:underline group-focus:underline">
                    {{ user.title }}
                </h2>



                <p>
                    Nickname: {{user.name}}<br />
                    Vorname: {{ user.first_name }}<br />
                    Registriert seit {{ formatDate(user.created_at) }}&nbsp;&nbsp;<editbtns  :id="user.id" table="users"></editbtns>
                </p>
                <div class="flex justify-between items-center">
                    <div class="rl">

                    </div>
                </div>
                <p>
                    <display-number :value="user.reading_time" :after-digits="0"
                        value-unit="Minuten Lesezeit"
                        value-single-unit="Minute Lesezeit"></display-number>
                </p>
            </div>
        </Link>

        <!-- **Tabelle direkt unterhalb des Blog-Containers (fixiert)** -->
        <SocialButtons :postId="user.id" sm="Sm" :nostars="true" :ublock="user.name"      />

    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import IconPlusCircle from "@/Application/Components/Icons/PlusCircle.vue";
import { CleanTable, CleanId } from '@/helpers';
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
import mapValues from "lodash/mapValues";
import pickBy from "lodash/pickBy";
import IconEye from "@/Application/Components/Icons/Eye.vue";
import IconTrash from "@/Application/Components/Icons/Trash.vue";
import DisplayDate from "@/Application/Components/Content/DisplayDate.vue";
import he from "he";
import AiButton from "@/Application/Components/Content/AiButton.vue";
import { nextTick } from "vue";
import { route } from "ziggy-js";
import IconShare from "@/Application/Components/Icons/IconShare.vue";
import IconStar from "@/Application/Components/Icons/IconStar.vue";
export default {
    name: "Shared_Homepage_UserPreviewSmall",
    //
    components: {
        Link,
    DisplayDate,
    IconPencil,
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
        user: {
      type: Object,
      required: true
    }
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
    formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('de-DE'); // ergibt z. B. 14.05.2025
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
.divvy{
    padding-top:18px;
    padding-bottom: 18px;
}
.teaser{

    height:200px;
    width: 200px;
    border-radius:50%;
}
</style>

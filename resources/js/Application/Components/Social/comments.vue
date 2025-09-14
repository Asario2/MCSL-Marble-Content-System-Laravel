<template>
            <div v-if="showComments" class="w-full zi" :style="{ display: showComments ? 'block' : 'none' }"
            @click.stop.prevent="dummy">
            <div v-if="comments && comments.length > 0" class="space-y-4">
                <div v-for="comment in comments" :key="comment?.id"
                class="flex items-start p-2 pra mt-4 rounded-lg bg-layout-sun-200 dark:bg-layout-night-200 border border-layout-sun-300 dark:border-layout-night-300"  style="word-wrap: break-word;"
            >
            <div :id="'commentBox_' + comment?.id" class="flex items-start space-x-4">
            <!-- Profilbild -->
            <img
                :src="comment?.profile_photo_path != null ? '/images/_' + SD() + '/users/profile_photo_path/' + comment?.profile_photo_path : defaultAvatar"
                alt="Profilbild"
                class="w-[50px] h-[50px] object-cover mxy rounded-full bg-gray-300 dark:bg-gray-600"
            />

            <!-- Kommentarinhalt -->
            <div class="flex-1 pr-14">
                <p class="text-sm flex items-center gap-2 mxy">
                    {{ comment?.author ?? comment?.nick}}
                    <span @click="confirmDelete(comment?.id)" class="text-red-500 cursor-pointer hover:text-red-700">
                        <IconTrash class="w-4 h-4" />
                    </span>
                </p>
                <div class="text-layout-sun-700 dark:text-layout-night-600 w-[190px] max-w-[190px] mxy">
                    <div v-html="smilies(comment?.content)"></div>
                </div>
                <small class="text-xs text-layout-sun-600 dark:text-layout-night-500">
                    <display-date :value="comment?.created_at" :time-on="false" />
                </small>
            </div>
        </div>


        <!-- Kommentar-Eingabefeld -->

    </div>
        </div>

            <!-- <p v-else class="text-center text-sm text-layout-sun-600 dark:text-layout-night-500">
            Keine Kommentare vorhanden.
            </p> -->
        </div>
        <div v-if="this.showComments" class="mb-4 p-4  rounded-lg bg-gray-100 dark:bg-gray-800">
        <textarea @click.stop
            v-model="newComment"
            class="w-full p-2 rounded-lg dark:bg-gray-900 dark:text-white"
            placeholder="Schreibe einen Kommentar..."
            @keydown.enter.exact.prevent="submitComment"
            @keydown.enter.shift="insertNewline"
        ></textarea>
        <button
            @click="submitComment"
            class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700"
        >
            Kommentar senden
        </button>
        </div>

        </template>

        <script>
        import axios from "axios";
        import { useLoadingStore } from '@/loading';

        import IconComment from "@/Application/Components/Icons/IconComment.vue";
        import DisplayDate from "@/Application/Components/Content/DisplayDate.vue";
        import IconTrash from "@/Application/Components/Icons/Trash.vue";
        import { CleanTable_alt, replaceSmilies,SD } from '@/helpers';
        export default {

            components: {
            IconComment,
            IconTrash,

        },


            props: {
                showComments: Number,
            postId: Number,
            table: String,
             // Die ID des Posts, zu dem Kommentare geladen werden
            },
            setup() {
                const loadingStore = useLoadingStore();

                async function loadComments() {
                loadingStore.setLoading(true);
                // Simuliere Ladevorgang
                await new Promise(resolve => setTimeout(resolve, 0));
                loadingStore.setLoading(false);
                }

                return {
                loadComments,
                };
            },

            data() {
            return {
                comments: [],
                newComment: "", // Eingabefeld für neuen Kommentar
                defaultAvatar: "/images/default_avatar.png", // Fallback-Profilbild
                comment: "",
            };
            },
            async mounted() {
                this.LoadCom();
            await this.fetchComments();
            document.querySelectorAll("textarea").forEach((textarea) => {
            textarea.addEventListener("click", function(event) {
                event.stopPropagation(); // Verhindert, dass der Link ausgelöst wird
            });

            textarea.addEventListener("keydown", function(event) {
                event.stopPropagation();
                if (event.key === "Enter") {
                    event.preventDefault(); // Verhindert Absenden mit Enter
                }
            });
            });
            },
            methods: {
                SD,
                handleEnter() {
                    this.$nextTick(() => {
                        this.submitComment();
                    });
                },
                smilies(text){
                    return replaceSmilies(this.nl2br(text));
                },
            nl2br(text){
                return text?.replace(/\n/g,"<br />");
            },
            LoadCom(){
                localStorage.setItem("loading", "true");
            },
        insertNewline(event) {
            // Erlaubt Zeilenumbruch bei Shift+Enter
            const textarea = event.target;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            this.newComment =
                this.newComment.substring(0, start) +
                "\n" +
                this.newComment.substring(end);
            this.$nextTick(() => {
                textarea.selectionStart = textarea.selectionEnd = start + 1;
            });
        },

                async confirmDelete(commentId) {
                    if (!confirm("Möchtest du diesen Kommentar wirklich löschen?")) return;

                    try {
                    await axios.delete(`/comments/delete/${commentId}`);
                    this.comments = this.comments.filter(comment => comment.id !== commentId); // Kommentar aus Liste entfernen
                    } catch (error) {
                    console.error("Fehler beim Löschen des Kommentars:", error);
                    }
                },
                cleanPath() {
                    const searchableTables = ["images", "blogs", "didyouknow","shortpoems"]; // Dein `Settings::$searchable`
                    const parts = window.location.pathname.split("/"); // Teile die URL in Segmente auf

                    let table = null;
                    let table_alt = null;

                    for (const ta of searchableTables) {
                        if (parts.includes(ta)) {
                            table = ta;
                            table_alt = ta;

                            // GET-Parameter setzen
                            const urlParams = new URLSearchParams(window.location.search);

                            // console.log("Gefunden:", table);
                            return table;
                        }
                    }

                },

                // Beispiel
                // const path = "/admin/tables/show/Example/123/create/456/search";
                // const cleanedPath = cleanPath(path);

                // console.log(cleanedPath); // Ausgabe: "/Example"

                async fetchComments() {
                    try {
                        var table = CleanTable_alt() || "blogs";
                        const response = await axios.get(`/comments/${table}/${this.postId}`);
                        console.log("fetchComments Antwort:", response.data);
                        this.comments = Array.isArray(response.data) ? response.data : [];
                    } catch (error) {
                        console.error("Fehler beim Laden der Kommentare:", error);
                    }
                },
    toggleCommentBox(){
        this.showComments = !this.showComments;
        this.imageRemove(this.postId);
    },

    async submitComment() {
    console.log("Sende Kommentar:", this.newComment);

    if (!this.newComment.trim()) return;

    try {
        let table = CleanTable_alt() || "blogs";
        const response = await axios.post(`/comments/store/${table}/${this.postId}`, {
            post_id: this.postId,
            comment: this.newComment,
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        });

        console.log("Antwort:", response.data);

        if (response.data.redirect) {
            window.location.href = response.data.redirect;
            return;
        }

        if (response.data.status === "success") {
            // Neuen Kommentar sofort sichtbar machen
            this.comments.unshift(response.data.comment);

            // Eingabefeld leeren
            this.newComment = "";
        }
    } catch (error) {
        console.error("Fehler beim Speichern des Kommentars:", error);
    }
},

    },
    };
        </script>
<style scoped>
.inline-smiley{
display:inline;
}
.object-cover{
    object-fit:cover;
    width:48px;
    height:48px;
    max-height:48px;
    max-width:48px;
    min-width:48px;
    min-height:48px;
}
.shariff {
    display: block; /* Stellt sicher, dass die Buttons untereinander erscheinen */
    margin-top: 10px; /* Abstand nach oben */
}
.h-fully{
    height:30px;
}
.mxy{
    margin:0 !important;
}
.zi{
    z-index:1000 !important;
}
</style>

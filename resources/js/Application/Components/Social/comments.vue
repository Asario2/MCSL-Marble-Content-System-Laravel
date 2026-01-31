    <template>
<div v-if="showComments" class="w-full zi relative" @click.stop.prevent="dummy">
<div v-if="comments && comments.length > 0" class="space-y-4">
    <div v-for="comment in comments" :key="comment?.id"
    class="flex items-start p-2 pra mt-4 rounded-lg bg-layout-sun-200 dark:bg-layout-night-200 border border-layout-sun-300 dark:border-layout-night-300"
    style="word-wrap: break-word;"
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
            <span v-if="AID" @click="confirmDelete(comment?.id)" class="text-red-500 cursor-pointer hover:text-red-700">
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
    </div>
</div>

<!-- Kommentar-Eingabefeld -->
<div v-if="showComments" class="mb-4 p-4 rounded-lg bg-gray-100 dark:bg-gray-800">
    <textarea @click.stop
    :id="'editor_' + this.postId"
    name="editor"
    v-model="newComment"
    class="w-full p-2 rounded-lg dark:bg-gray-900 dark:text-white"
    placeholder="Schreibe einen Kommentar..."
    @keydown.enter.exact.prevent="submitComment"
    @keydown.enter.shift="insertNewline"
    ></textarea>
    <SmiliesBox
    :name="postId"
    @update:comment="newComment = $event"
    />
    <NoLogin v-if="!AID" v-model="form" :nick="false"></NoLogin>
    <button
    @click="submitComment"
    class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700"
    >
    Kommentar senden
    </button>
</div>

</div>


        </template>
<script>
import axios from "axios";
import { CleanTable_alt, replaceSmilies, SD } from '@/helpers';
import SmiliesBox from "@/Application/Components/Social/SmiliesBox.vue";
import IconTrash from "@/Application/Components/Icons/Trash.vue";
import NoLogin from '@/Application/Components/Social/NoLogin.vue';

export default {
name: "CommentsComponent",

components: {
    SmiliesBox,
    IconTrash,
    NoLogin,
},

props: {
    showComments: Number,
    postId: Number,
    table: String,
},

data() {
    return {
    form: {
        name: null,
        email: null,
        password: null,
    },
    logged: false,
    comments: [],
    newComment: "",
    defaultAvatar: "/images/default_avatar.png",
    AID: true,
    };
},

async mounted() {
    // Prüfe, ob User bereits authid hat
    this.logged = !!window.authid;
    this.AID = window.authid ?? true;

    await this.fetchComments();

    // Stop propagation für Enter & Click in allen Textareas
    document.querySelectorAll("textarea").forEach((textarea) => {
    textarea.addEventListener("click", (e) => e.stopPropagation());
    textarea.addEventListener("keydown", (e) => {
        e.stopPropagation();
        if (e.key === "Enter" && !e.shiftKey) e.preventDefault();
    });
    });
},

methods: {
    SD,

    nl2br(text) {
    return text?.replace(/\n/g, "<br />");
    },

    smilies(text) {
    return replaceSmilies(this.nl2br(text));
    },

    insertNewline(event) {
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

    async fetchComments() {
    try {
        const table = CleanTable_alt() || "blogs";
        const response = await axios.get(`/comments/${table}/${this.postId}`);
        this.comments = Array.isArray(response.data) ? response.data : [];
    } catch (error) {
        console.error("Fehler beim Laden der Kommentare:", error);
    }
    },

    async ensureLogin() {
    // schon eingeloggt
    if (this.logged) return true;

    // KEIN Passwort → anonym erlauben
    if (!this.form.password) {
        return true;
    }

    // Passwort vorhanden → Silent Login
    try {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');

        await axios.post('/login-silent', {
            email: this.form.email,
            password: this.form.password,
        }, {
            withCredentials: true,
            headers: { 'X-CSRF-TOKEN': token },
        });

        this.logged = true;
        return true;
    } catch (e) {
        console.error("Silent login failed", e);
        return false;
    }
},

async submitComment() {
    if (!this.newComment.trim()) return;

    const loggedIn = await this.ensureLogin();
    if (!loggedIn) return;

    const table = CleanTable_alt() || 'blogs';
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        const response = await axios.post(
            `/comments/store/${table}/${this.postId}`,
            {
                post_id: this.postId,
                comment: this.newComment,
                name: this.form.name,
                email: this.form.email,
            },
            {
                headers: { 'X-CSRF-TOKEN': token },
                withCredentials: true
            }
        );

        if (response.data.status === 'success') {
            this.newComment = '';
            await this.fetchComments(); // 🔥 DAS ist der fehlende Punkt
        }
    } catch (error) {
        console.error('Fehler beim Speichern des Kommentars:', error);
    }
},

    async confirmDelete(commentId) {
    if (!confirm("Möchtest du diesen Kommentar wirklich löschen?")) return;
    try {
        await axios.delete(`/comments/delete/${commentId}`);
        this.comments = this.comments.filter(c => c.id !== commentId);
    } catch (error) {
        console.error("Fehler beim Löschen des Kommentars:", error);
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


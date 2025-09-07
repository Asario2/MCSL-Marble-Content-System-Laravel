<template>
    <Layout>
        <MetaHeader :title="'Benutzer - ' + users?.name" />
        <back-btn url="/home/users" r="r">Benutzerliste</back-btn>
        <div id="teaser-img2" class="block max-w-sm gap-3 mx-auto mh_65 sm:max-w-full focus:no-underline lg:grid lg:grid-cols-12 bg-layout-sun-100 dark:bg-layout-night-100 overfl" style="z-index:0;margin-bottom:-0px;" :class="{ 'disable-link': isCommentActive }"
    >
        <!-- Das Bild des Blog-Posts -->
        <div class="blog-container mh_65 lg:col-span-4 bg-layout-sun-100 dark:bg-layout-night-100" style="">

       <img
        :src="users.profile_photo_path != null ?  '/images/' + users.profile_photo_path.replace('/images/','') : '/images/profile-photos/008.jpg'"
        :alt="users.name"
        width="480"
        height="360"
        style="max-height:380px;"
        :class="['object-cover w-full rounded lg:col-span-4 object-cover rounded bg-layout-sun-100 dark:bg-layout-night-100', users.madewithai ? 'ai-image-corner' : '']"

        />


        <div class="relative">
<AiButton></AiButton>


    </div>
        </div>

        <div id="teaser-img" class="p-6 space-y-2 lg:col-span-8 pb-0">

            <!-- Blog-Titel -->
            <h2
                class="text-xl font-semibold sm:text-2xl font-title"
            >
                {{ users.name }}&nbsp;&nbsp;<editbtns :id="users.id" table="users" />
            </h2>
            <table class="table-auto text-left">
            <tbody>
                <tr>
                <th class="pr-4">Vorname:</th>
                <td>{{ users.first_name }}</td>
                </tr>
                <tr>
                <th class="pr-4">Registriert seit:</th>
                <td>{{ formatDate(users.created_at) }}</td>
                </tr>
                <tr>
                <th class="pr-4">Alter:</th>
                <td>{{ get_age(users.birthday) }}</td>
                </tr>
                <tr>
                <th class="pr-4">Musik:</th>
                <td>{{ users.music }}</td>
                </tr>
                <tr>
                <th class="pr-4">Interessen:</th>
                <td>{{ users.interests }}</td>
                </tr>
                <tr>
                <th class="pr-4">Beschäftigung:</th>
                <td>{{ users.occupation }}</td>
                </tr>
                <th class="pr-4">Facebook:</th>
                <td>
                    <a v-if="users.fbd" :href="'https://facebook.com/profile.php?id=' + users.fbd" target="_blank">
                    Zu Faceboook</a>
                    <p v-else>keine Angabe</p>
                </td>
                </tbody>
            </table>


                       <!-- Blog-Zusammenfassung -->


            <!-- Lesezeit anzeigen -->
            <socialButtons :postId="users.id" :empty="true" :nostars="true" />
        </div>

    </div>
    <br />
    <br />
    <div v-if="users.about" class="pb-6 bg-layout-sun-100 dark:bg-layout-night-100 p-4">
    <h1><b>Über {{users.name}}</b></h1>
    <div v-html="users.about"></div>
    </div>
</Layout>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import Layout from "@/Application/Homepage/Shared/Layout.vue";
import DisplayDate from "@/Application/Components/Content/DisplayDate.vue";
import DisplayNumber from "@/Application/Components/Content/DisplayNumber.vue";
import editbtns from "@/Application/Components/Form/editbtns.vue";
import SocialButtons from "@/Application/Components/Social/socialButtons.vue";
import AiButton from "@/Application/Components/Content/AiButton.vue";
import BackBtn from "@/Application/Components/Form/BackBtn.vue";
export default {
    name: "Homepage_Shared_BlogPreviewBig",
    components: {
        Link,
        BackBtn,
        DisplayDate,
        DisplayNumber,
        AiButton,
        editbtns,
        SocialButtons,
        Layout,
        MetaHeader,
    },
    props: {
        blog: {
            type: Object,
        },
        aiOverlayImage: {
            type: String,
        },
        tablename:{
            type: String,
        },
        // editRights:{
        //     type: Number,
        // },
        // deleteRights:{
        //     type: Number,
        // },
        users:{
            type:[Array,Object],
            default: {},
        },
    },
    methods: {
    get_age(birthday) {
      if (!birthday) return 'unbekannt';
      const birthDate = new Date(birthday);
      const today = new Date();
      let age = today.getFullYear() - birthDate.getFullYear();
      const m = today.getMonth() - birthDate.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }
      return age + ' Jahre';
    },
    formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('de-DE'); // ergibt z. B. 14.05.2025
    },
  },
};
</script>

<style scoped>
.rl{
    display:inline;
    margin-top:-5px;

}
.relative {
  position: relative;
}


@media screen and (min-width: 1024px) {
.overfl{
    overflow:hidden;
    max-height:385px;
}
.ai-button-image {
  position: fixed;  /* Fixiere das Bild auf dem Bildschirm */
  bottom: 16px;     /* Abstand von der unteren Kante */
  right: 16px;      /* Abstand von der rechten Kante */
  z-index: 39;
  margin-bottom :236px;
}
}
@media screen and (max-width: 1024px) {
.overfl{
    overflow:visible;
}
}
/* Hier kannst du zusätzliche Anpassungen vornehmen, falls nötig */
</style>

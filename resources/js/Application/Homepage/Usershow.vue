<template>
    <Layout>
        <MetaHeader :title="'Benutzer - ' + users?.name" />
        <back-btn url="/home/users" r="r">Benutzerliste</back-btn>
        <div id="teaser-img2" v-if="users" class="block max-w-sm gap-3 mx-auto mh_65 sm:max-w-full focus:no-underline lg:grid lg:grid-cols-12 bg-layout-sun-100 dark:bg-layout-night-100" style="z-index:10;margin-bottom:-0px;" :class="{ 'disable-link': isCommentActive }"
    >
        <!-- Das Bild des Blog-Posts -->
        <div class="blog-container mh_65 lg:col-span-4 bg-layout-sun-100 dark:bg-layout-night-100" style="">

       <img
        :src="users?.profile_photo_path != null ?  '/images/_'+ SD() + '/users/profile_photo_path/' + users?.profile_photo_path.replace('/images/','') : '/images/profile-photos/008.jpg'"
        :alt="users?.name"
        width="480"
        height="360"
        style="max-height:380px;"
        :class="['object-cover w-full rounded lg:col-span-4 object-cover rounded bg-layout-sun-100 dark:bg-layout-night-100', users?.madewithai ? 'ai-image-corner' : '']"

        />


        <div class="relative">
<AiButton :xis_enabled="users?.madewithai"> </AiButton>



    </div>
        </div>
        <div id="teaser-img" class="p-6 space-y-2 lg:col-span-8 pb-0">

            <!-- Blog-Titel -->
            <h2
                class="text-xl font-semibold sm:text-2xl font-title"
            >
                {{ users?.name }}&nbsp;&nbsp;<editbtns :id="users?.id" table="users" />
            </h2>
            <table class="table-auto text-left">
            <tbody>
                <tr>
                <th class="pr-4">Vorname:</th>
                <td v-html="users?.first_name"></td>
                </tr>
                <tr>
                <th class="pr-4">Registriert seit:</th>
                <td>{{ formatDate(users?.created_at) }}</td>
                </tr>
                <tr>
                <th class="pr-4">Alter:</th>
                <td>{{ get_age(users?.birthday) }}</td>
                </tr>
                <tr>
                <th class="pr-4">Musik:</th>
                <td v-html="users?.music"></td>
                </tr>
                <tr>
                <th class="pr-4">Interessen:</th>
                <td v-html="users?.interests"></td>
                </tr>
                <tr>
                <th class="pr-4">Beschäftigung:</th>
                <td v-html="users?.occupation"></td>
                </tr>
                <tr>
                <th class="pr-4">Statistiken:</th>
                <td>
                <a :href="'/admin/mcslpoints/' + users.id + '#stats'" class="inline-flex items-center gap-2 mb-3">
                <span class="lg:rounded" style="background-color:#3d983b;padding:3px 7px;color:#fff !important;display:inline-flex;align-items:center;gap:4px;">
                    <img :src="'/images/icons/chart.png'" class="w-[16px] h-[16px]" alt="Statistik">Zur Statistik
                </span>
                </a>
                </td>

                </tr>
                <tr>
                <th class="pr-4">Facebook:</th>
                <td>
                    <a v-if="users?.fbd" :href="fbid(users?.fbd)" target="_blank">
                    <span style="background-color:#3b5998;padding:3px;padding: 3px 7px;color:#fff !important" class="lg:rounded">Zu <i class="w-[18px] h-[18px] fab fa-facebook-f"></i>acebook</span> </a>
                    <p v-else>keine Angabe</p>
                </td>
                </tr>
                <tr>
                <th class="pr-4">Letzter Login:</th>
                <td>{{ formatDate(users?.last_login_at) }}</td>
                </tr>
                </tbody>
            </table>


                       <!-- Blog-Zusammenfassung -->


            <!-- Lesezeit anzeigen -->
            <socialButtons :postId="users?.id" :empty="true" :nostars="true" />
        </div>

    </div>
    <br />
    <br />
    <div v-if="users?.about" class="pb-6 bg-layout-sun-100 dark:bg-layout-night-100 p-4">
    <h1><b>Über {{users?.name}}</b></h1>
    <div v-html="users?.about"></div>
    </div>
    <div v-if="!users">
        Konnte den Benutzer nicht finden
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
import { SD } from '@/helpers';
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
        SD,
        fbid(id) {
            if (!id) return "";

            const value = String(id).trim();

            // Bereits eine Facebook-URL
            if (value.includes("facebook.")) {
                return value;
            }

            // Username (nicht rein numerisch)
            if (!/^\d+$/.test(value)) {
                return "https://facebook.com/" + value;
            }

            // Numerische Facebook-ID
            return "https://facebook.com/profile.php?id=" + value;
        },
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


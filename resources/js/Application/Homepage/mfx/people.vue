<template>
    <layout :header-url="$page.props.saas_url + '/'">
    <MetaHeader title="People: Unser Team" />
        <page-content>
            <template #content>
                <div>
                <page-title>
                    <template #title>
                        <span class="dark:text-layout-night-1050 text-layout-sun-1000">Unser Team</span>
                </template>
                </page-title>
                <div class="grid grid-cols-12 gap-6">
                <div
                    v-for="item in data"
                    :key="item.id"
                    class="col-span-6 border border-layout-sun-1000 dark:border-layout-night-1050 bg-layout-sun-50 dark:bg-layout-night-50 rounded-lg grid grid-cols-6"
                >
                    <!-- Profilbild (2 Spalten innerhalb der Box) -->
                    <div class="col-span-2 rounded-lg">
                    <img
                        :src="'/images/_mfx/people/img_bild/' + item.profile_photo_path.replace('profile-photos/','')"
                        :alt="item.name"
                        :title="item.name"
                        height="184"
                        width="158"
                        class="object-cover no_bord rounded-lg"
                        style="max-height:184px"
                    />
                    </div>

                    <!-- Text (4 Spalten innerhalb der Box) -->
                    <div class="col-span-4">
                    <div class="text-layout-sun-1000 dark:text-layout-night-1000 font-semibold text-lg">
                        {{ item.name }} <editbtns :id="item.id" table="people"></editbtns>
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">
                        {{ item.headline }}<br />
                        <b class='text-grey-0'>Name: </b>{{ item.first_name }}<br />
                        <b class='text-grey-0'>Alter: </b>{{ get_age(item.birthday) }}<br />
                        <b class='text-grey-0'>Aufgabe: </b>{{ item.aufgabe }}<br />
                        <b class='text-grey-0'>Wohnort: </b>{{ item.location }}<br />
                        <b class='text-grey-0'>Website: </b><a v-if="item.website" :href="item.website">{{ item.website }}</a><span v-else >Keine</span><br />
                        <span  v-if="item.fbd">
                        <b style="color:#0c69ff;"><i class="fab fa-facebook"></i>&nbsp; </b><a :href="'https://facebook.com/profile.php?id=' + item.fbd">facebook</a>
                        </span>
                        </div>

                    </div>
                </div>
                </div>


                    </div>





            </template>
        </page-content>
    </layout>
</template>
<script>
import { defineComponent } from "vue";
import axios from "axios";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import Layout from "@/Application/Homepage/Shared/mfx/Layout.vue";
import { rumLaut } from "@/helpers";
import PageContent from "@/Application/Components/Content/PageContent.vue";
import PageTitle from "@/Application/Components/Content/PageTitle.vue";
// import PageParagraph from "@/Application/Components/Content/PageParagraph.vue";
// import emailview from "@/Application/Components/Form/email.vue";
import editbtns from "@/Application/Components/Form/editbtns.vue";

export default defineComponent({
    name: "Homepage_Projects",

    components: {
        Layout,
        PageContent,
        PageTitle,
        // PageParagraph,
        // emailview,
        editbtns, MetaHeader},
    props:{
        news:[Array,Object],
        text: [Array,Object],
        data: [Array,Object],
        item: [Array,Object],
        sheets: [Array,Object],
    },
    data() {
    return {
      form: {
        name: '',
        email: '',
        subject: '',
        message: '',
        captcha: '',
        accepted: false
      }
    }
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
        cleanHtml(html) {
      const result = rumLaut(html);
     // console.log("rumLaut output:", result);
      return result;

    },
    async submitForm() {
      try {
        const response = await axios.post('/contact/send', this.form)
        alert('Nachricht erfolgreich gesendet!')
        this.resetForm()
      } catch (error) {
        alert('Fehler beim Senden der Nachricht.')
      }
    },
    resetForm() {
      this.form = {
        name: '',
        email: '',
        subject: '',
        message: '',
        captcha: '',
        accepted: false
      }
    }
  },
});
</script>
<style>
/* /*.input {
  @apply w-full px-4 py-2 border rounded-md dark:bg-zinc-800 dark:text-white;
}

.btn {
  @apply px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50;
}*/ */
IMG.no_bord{
    border-top-right-radius:0 !important;
    border-bottom-right-radius:0 !important;
}
</style>


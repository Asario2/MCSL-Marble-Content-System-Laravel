<template>
<h1 v-if="!nohead" class="2xl">Kontakt</h1>
<form @submit.prevent="submitForm"
class="block p-4 pl-4 w-full rounded-lg border text-sm text-layout-sun-900
bg-layout-sun-50 border-layout-sun-600 focus:ring-primary-sun-500 focus:border-primary-sun-500
 dark:text-layout-night-900 dark:bg-layout-night-50 dark:border-layout-night-1050
  dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500">

<div>
    <label class="block font-semibold mb-1 text-layout-sun-1000 dark:text-layout-night-1000">Name</label>
    <input v-model="form.name" type="text"
        class="block p-2  w-full rounded-lg border text-sm text-layout-sun-900 bg-layout-sun-50 border-layout-sun-300 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:text-layout-night-900 dark:bg-layout-night-50 dark:border-layout-night-300 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500" required />
</div>

<div>
    <label class="block font-semibold mb-1 text-layout-sun-1000 dark:text-layout-night-1000">E-Mail</label>
    <input v-model="form.email" type="email"
        class="block p-2  w-full rounded-lg border text-sm text-layout-sun-900 bg-layout-sun-50 border-layout-sun-300 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:text-layout-night-900 dark:bg-layout-night-50 dark:border-layout-night-300 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500" required />
</div>

<div>
    <label class="block font-semibold mb-1 text-layout-sun-1000 dark:text-layout-night-1000">Betreff</label>
    <input v-model="form.subject" type="text"
        class="block p-2  w-full rounded-lg border text-sm text-layout-sun-900 bg-layout-sun-50 border-layout-sun-300 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:text-layout-night-900 dark:bg-layout-night-50 dark:border-layout-night-300 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500" required />
</div>

<div>
    <label class="block font-semibold mb-1 text-layout-sun-1000 dark:text-layout-night-1000">Nachricht</label>
    <textarea v-model="form.message" rows="5"
            class="block p-2  w-full rounded-lg border text-sm text-layout-sun-900 bg-layout-sun-50 border-layout-sun-300 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:text-layout-night-900 dark:bg-layout-night-50 dark:border-layout-night-300 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500" required></textarea>
</div>

<div>
    <label class="block font-semibold mb-1 text-layout-sun-1000 dark:text-layout-night-1000 mt-[8px]">
        Sicherheitsfrage
    </label>
    <captcha
        @update="updateCaptcha"
        ref="captcha"
        ></captcha>
</div>

<div class="flex items-center mt-[8px] mb-[8px]">
    <input v-model="form.accepted" type="checkbox" id="accept" class="mr-2" />
    <label for="accept" class="text-layout-sun-1000 dark:text-layout-night-1000">
        Ich akzeptiere die <a href='/home/privacy'>Datenschutz-Bestimmungen</a>
    </label>
</div>

<div>
    <button type="submit"
            class="btn"
            :disabled="!form.accepted">
        Absenden
    </button>
</div>
</form>



</template>
<script>
import { defineComponent } from "vue";
import { selectionHelper, GetSettings,rumLaut } from "@/helpers";
import  captcha  from "@/Application/Components/captcha.vue";

export default defineComponent({
    name: "emailview",

    components: {
        captcha,
    },
    props:{
        news:[Array,Object],
        text: [Array,Object],
        nohead:[Array,String,Boolean,Number],
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
        updateCaptcha(value) {
      this.form.captcha = value
    },
        cleanHtml(html) {
      const result = rumLaut(html);
     // console.log("rumLaut output:", result);
      return result;

    },
    async submitForm() {
      try {
//         console.log(this.form);
        const response = await axios.post('/api/contact/send', this.form)
        if(response.data == "1"){
            alert('Nachricht erfolgreich gesendet!');
        }
        else{
            alert("Bitte Captcha Überprüfen");
        }


        this.$refs.captcha.resetCaptcha();
        this.resetForm()
      } catch (error) {
        this.$refs.captcha.resetCaptcha();
        alert("Fehler beim Senden der Nachricht.\nBitte überpfüfen Sie das Captcha")
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
<style scoped>
/*.input {
  @apply w-full px-4 py-2 border rounded-md dark:bg-zinc-800 dark:text-white;
}

.btn {
  @apply px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50;
}*/
</style>


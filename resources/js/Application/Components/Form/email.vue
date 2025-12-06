<template>
  <div>
    <h1 v-if="!nohead" class="text-2xl font-bold mb-4">Kontakt</h1>

    <form @submit.prevent="submitForm"
          class="block p-4 w-full rounded-lg border text-sm text-layout-sun-900
                 bg-layout-sun-50 border-layout-sun-600 focus:ring-primary-sun-500 focus:border-primary-sun-500
                 dark:text-layout-night-900 dark:bg-layout-night-50 dark:border-layout-night-1050
                 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500">

      <!-- Name -->
      <div class="mb-4">
        <input-label name="name" label="Name"></input-label>
        <input-element v-model="form.name" name="name" type="text" :required="true" />
      </div>

      <!-- Email -->
      <div class="mb-4">
        <input-label name="email" label="Email"></input-label>
        <input-element v-model="form.email" name="email" type="email" :required="true" />
      </div>

      <!-- Betreff -->
      <div class="mb-4">
        <input-label name="subject" label="Betreff"></input-label>
        <input-element v-model="form.subject" name="subject" type="text" :required="true" />
      </div>

      <!-- Nachricht -->
      <div class="mb-4">
        <input-label name="message" label="Nachricht"></input-label>
        <InputHtml v-model="form.message" rows="5"
                   class="w-full text-sm rounded-lg block border focus:ring-3 focus:ring-opacity-75 bg-layout-sun-0
                          text-layout-sun-900 border-primary-sun-500 focus:border-primary-sun-500
                          focus:ring-primary-sun-500 placeholder:text-layout-sun-400 selection:bg-layout-sun-200
                          selection:text-layout-sun-1000 dark:bg-layout-night-0 dark:text-layout-night-900
                          dark:border-primary-night-500 dark:focus:border-primary-night-500
                          dark:focus:ring-primary-night-500 placeholder:dark:text-layout-night-400
                          dark:selection:bg-layout-night-200 dark:selection:text-layout-night-1000"
                   name="message" :nohtml="true" :required="true">
        </InputHtml>
      </div>

      <!-- Captcha für Gäste -->
      <div v-if="!$page.props.auth.user" class="mb-4">
        <label class="block font-semibold mb-1 text-layout-sun-1000 dark:text-layout-night-1000">
          Sicherheitsfrage
        </label>
        <captcha @update="updateCaptcha" ref="captcha"></captcha>
      </div>

      <!-- Datenschutz Checkbox -->
      <div class="flex items-center mb-4">
        <input v-model="form.accepted" type="checkbox" id="accept" class="mr-2"/>
        <label for="accept" class="text-layout-sun-1000 dark:text-layout-night-1000">
          Ich akzeptiere die <a href="/home/privacy">Datenschutz-Bestimmungen</a>
        </label>
      </div>

      <!-- Submit -->
      <div>
        <button type="submit" class="button-primary">
          Absenden
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import { defineComponent } from "vue";
import { usePage } from '@inertiajs/vue3';
import InputElement from "@/Application/Components/Form/InputElement.vue";
import InputHtml from "@/Application/Components/Form/InputHtml.vue";
import InputLabel from "@/Application/Components/Form/InputLabel.vue";
import captcha from "@/Application/Components/captcha.vue";

export default defineComponent({
  name: "EmailView",
  components: { InputElement, InputHtml, InputLabel, captcha },
  props: {
    nohead: [Array, String, Boolean, Number],
  },
  data() {
    const page = usePage();
    return {
      form: {
        name: page.props.auth.user?.name ?? '',
        email: page.props.auth.user?.email ?? '',
        subject: '',
        message: '',
        captcha: '',
        accepted: false
      }
    }
  },
  methods: {
    updateCaptcha(value) {
      this.form.captcha = value;
    },
    async submitForm() {
      // 1. Pflichtfelder prüfen
      if (!this.form.name) { alert("Bitte Name ausfüllen"); return; }
      if (!this.form.email) { alert("Bitte Email ausfüllen"); return; }
      if (!this.form.subject) { alert("Bitte Betreff ausfüllen"); return; }
      if (!this.form.message) { alert("Bitte Nachricht eingeben"); return; }

      // 2. Datenschutz prüfen
      if (!this.form.accepted) { alert("Bitte die Datenschutz-Bestimmungen akzeptieren."); return; }

      try {
        // Eingeloggter User → kein Captcha
        if (this.$page.props.auth.user) {
          await axios.post('/api/contact/send', { ...this.form, captcha: 'BYPASS' });
          alert('Nachricht erfolgreich gesendet!');
          this.resetForm();
          return;
        }

        // Nicht eingeloggter User → Captcha prüfen
        const response = await axios.post('/api/contact/send', this.form);
        if (response.data == "1") {
          alert('Nachricht erfolgreich gesendet!');
          this.resetForm();
        } else {
          alert("Bitte Captcha überprüfen");
        }

        this.$refs.captcha.resetCaptcha();

      } catch (error) {
        this.$refs.captcha.resetCaptcha();
        alert("Fehler beim Senden der Nachricht.\nBitte überprüfen Sie das Captcha",error);
      }
    },
    resetForm() {
      this.form = { name: '', email: '', subject: '', message: '', captcha: '', accepted: false };
    }
  }
});
</script>

<style scoped>
.button-primary {
  background-color: #222;
  padding: 5px 7px !important;
  border-radius: 6px;
  border: 2px solid #ff9600;
  line-height: 20px;
  cursor: pointer;
  font-size: 20px;
  color: #ff9600;
  font-family: Tahoma;
  margin: 3px;
  text-decoration: none;
}
.button-primary:hover, a.button-primary:visited:hover {
  background-color: #ff9600 !important;
  color: #222 !important;
  border: 2px solid #ff9600 !important;
  padding: 5px 7px !important;
}
</style>

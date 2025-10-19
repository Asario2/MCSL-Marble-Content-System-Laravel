<template>
  <div class="text-center">
   <button
        @click="showModal = true"
        class="cursor-pointer inline-flex items-center gap-2 font-bold rounded-lg px-2 py-1 text-sm
            text-layout-sun-700 hover:bg-primary-sun-300 hover:text-layout-sun-900 ml-[-10px]
            dark:text-layout-night-700 dark:hover:bg-primary-night-300 dark:hover:text-layout-night-900"
    >
        <IconMail lcol="#ffc800" wi="18" he="18" />
        <span class="font-sans ora">Newsletter abonnieren</span>
    </button>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md">
        <!-- Header -->
        <div class="flex justify-between items-center p-4 border-b dark:border-gray-700">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            Newsletter abonnieren
          </h2>
          <button
            @click="closeModal"
            class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
          >
            ✕
          </button>
        </div>

        <!-- Body -->
        <div class="p-4 space-y-4">
          <p class="text-sm text-gray-600 dark:text-gray-300">
            Bitte gib deine E-Mail-Adresse ein, um unseren Newsletter zu abonnieren.
            Nach dem Absenden erhältst du eine E-Mail mit einem Bestätigungslink.
          </p>

          <!-- E-Mail -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              E-Mail-Adresse *
            </label>
            <input
              type="email"
              v-model="form.email"
              required
              :class="inputClasses"
            />
          </div>

          <!-- Anrede -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Anrede
            </label>
            <select v-model="form.title" :class="inputClasses">
              <option value="">– bitte wählen –</option>
              <option value="Herr">Herr</option>
              <option value="Frau">Frau</option>
              <option value="Divers">Divers</option>
              <option value="Familie">Familie</option>
            </select>
          </div>

          <!-- Vorname / Nachname -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Vorname
              </label>
              <input
                type="text"
                v-model="form.firstname"
                :class="inputClasses"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Nachname
              </label>
              <input
                type="text"
                v-model="form.lastname"
                :class="inputClasses"
              />
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-2 p-4 border-t dark:border-gray-700">
          <button
            @click="closeModal"
            class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200"
          >
            Abbrechen
          </button>
          <button
            @click="submitForm"
            :disabled="loading"
            class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white disabled:opacity-50"
          >
            <span v-if="!loading">Abschicken</span>
            <span v-else>Senden...</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import IconMail from "@/Application/Components/Icons/IconMail.vue";
// Wenn Ziggy aktiviert ist:
import {route} from "ziggy-js";

export default {
  name: "NewsletterSubscribe",
  components: { IconMail },

  data() {
    return {
      showModal: false,
      loading: false,
      form: {
        email: "",
        title: "",
        firstname: "",
        lastname: "",
      },
      inputClasses: [
        "txt w-full p-2.5 text-sm rounded-lg block border focus:ring-3 focus:ring-opacity-75",
        "bg-layout-sun-0 text-layout-sun-900 border-primary-sun-500 focus:border-primary-sun-500 focus:ring-primary-sun-500 placeholder:text-layout-sun-400",
        "selection:bg-layout-sun-200 selection:text-layout-sun-1000",
        "dark:bg-layout-night-0 dark:text-layout-night-900 dark:border-primary-night-500 dark:focus:border-primary-night-500 dark:focus:ring-primary-night-500 placeholder:dark:text-layout-night-400",
        "dark:selection:bg-layout-night-200 dark:selection:text-layout-night-1000",
      ].join(" "),
    };
  },

  methods: {
    closeModal() {
      this.showModal = false;
      this.resetForm();
    },

    resetForm() {
      this.form = {
        email: "",
        title: "",
        firstname: "",
        lastname: "",
      };
    },

    async submitForm() {
      if (!this.form.email) {
        alert("Bitte gib eine gültige E-Mail-Adresse ein.");
        return;
      }

      this.loading = true;

      try {
        // Wenn du Ziggy NICHT nutzt, ersetze das hier durch: await axios.post('/newsletter/store', this.form)
        await axios.post(route("mail.subscribe_newsl"), this.form);

        alert("Fast geschafft! Bitte bestätige deine Anmeldung über den Link in der E-Mail.");
        this.closeModal();
      } catch (error) {
        console.error(error);
        alert("Es ist ein Fehler aufgetreten. Bitte versuche es später erneut.");
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style>
.ora{
  color:#ffc800;
}
button {
  outline: none;
}
</style>

<template>
    <div class="p-4 bg-layout-sun-100 dark:bg-layout-night-100 rounded-lg shadow-sm">
      <table class="w-full border-collapse text-sm md:text-base">
        <thead class="bg-layout-sun-200 dark:bg-layout-night-200 text-layout-sun-800 dark:text-layout-night-800">
          <tr>
            <th>Modul</th>
            <th>Beschreibung</th>
            <th>Aktiv</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(value, key) in localFunc" :key="key" v-if="key">
            <td>{{ stripXkis(key) }}</td>
            <td>{{ getLabel(key) }}</td>
            <td>
              <input-checkbox v-model="localFunc[key]" />
            </td>
          </tr>
        </tbody>
      </table>

      <button
        @click="submit"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded mt-4"
      >
        Modulrechte speichern
      </button>
    </div>
  </template>

  <script>
    import InputCheckbox from "@/Application/Components/Form/InputCheckbox.vue";

  export default {
    name: "Functions",
    components: { InputCheckbox },
    props: {
      urid: [String, Number],
    },
    data() {
      return {
        func: {},
        localFunc: {},
      };
    },
    watch: {
      func: {
        immediate: true,
        deep: true,
        handler(newVal) {
          this.localFunc = {};
          for (const key in newVal) {
            this.localFunc[key] = newVal[key] === 1 ? true : false;
          }
        },
      },
    },
    mounted() {
      this.loadFunctions();
    },
    methods: {
      async loadFunctions() {
        try {
          const response = await axios.get(`/api/roles/${this.urid}`);
          this.func = response.data;
        } catch (error) {
          console.error("Fehler beim Laden der Funktionen:", error);
        }
      },
      submit() {
        const payload = { urid: this.urid };
        for (const key in this.localFunc) {
          payload[key] = this.localFunc[key] ? 1 : 0;
        }
        axios.post("/admin/user-rights/save", payload);
      },
      stripXkis(key) {
        return key.replace(/^xkis_/, '');
      },
      getLabel(key) {
        const labels = {
          xkis_AdminPanel: "Admin Panel",
          xkis_ChangePassword: "Passwort ändern",
          xkis_CommentsCenter: "Kommentarzentrum",
          xkis_DataBases: "Datenbanken",
          xkis_LogViewer: "Log Viewer",
          xkis_SendMail: "E-Mail senden",
          xkis_UserRights: "Benutzerrechte",
        };
        return labels[key] || this.stripXkis(key);
      },
    },
  };
  </script>


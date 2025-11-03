<template>
  <section class="p-5 md:p-10 bg-layout-sun-0 dark:bg-layout-night-0 text-layout-sun-800 dark:text-layout-night-800">
    <div class="max-w-6xl mx-auto rounded overflow-hidden">

      <!-- Tabs -->
      <div class="flex border-b border-gray-300 dark:border-layout-night-200 mb-4 space-x-2 items-center">
        <span @click="selectTab('tables')" :class="tabClass('tables')">Tabellen</span>
        <span @click="selectTab('functions')" :class="tabClass('functions')">Funktionen</span>
        <span @click="selectTab('users')" :class="tabClass('users')">Benutzer</span>

        <!-- Rollen Auswahl -->
        <div class="ml-auto">
          <InputSelect
            v-model="selected"
            :xname="'users_rights_id'"
            :name="'users_rights_id'"
            :options="roles"
            @update:modalValue="selected = $event"
            @input-change="navigate"
          />
        </div>
      </div>

      <!-- Tabellen Rechte -->
      <div v-if="activeTab === 'tables'" class="bg-layout-sun-100 dark:bg-layout-night-100 p-4 rounded-lg shadow-sm">
        <table class="w-full border-collapse text-sm md:text-base rounded overflow-hidden">
          <thead class="bg-layout-sun-300 dark:bg-layout-night-300 text-layout-sun-800 dark:text-layout-night-800">
            <tr>
              <th class="px-4 py-3 text-left"><nobr>An/Aus</nobr></th>
              <th class="px-4 py-3 text-left">Tabelle</th>
              <th v-for="field in Object.keys(rights)" :key="field" class="px-4 py-3 text-left">
                <nobr>{{ ucf2(field) }}</nobr>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(table, index) in adminTables"
              :key="table.name || index"
              class="hover:bg-layout-sun-200 dark:hover:bg-layout-night-200 transition duration-200 border-b border-gray-200 dark:border-gray-700"
            >
              <td class="px-4 py-3 cursor-pointer text-left">
                <button @click="togglerow(index)" class="flex items-center text-blue-500">
                  <IconRight class="w-5 h-5" fill="currentColor" v-tippy />
                  <tippy>{{ ucf(table.name) }} An/Aus</tippy>
                </button>
              </td>
              <td class="px-4 py-3" v-tippy>
                {{ ucf(table.name) }}<tippy>{{ ucf(table.name) }}</tippy>
              </td>
              <td
                v-for="(field, fidx) in Object.keys(rights)"
                :key="field + '_' + index + '_' + fidx"
                class="px-4 py-3 text-left"
              >
                <input-checkbox v-model="rights[field][index]" v-tippy />
                <tippy>{{ ucf2(field) }} von {{ ucf(table.name) }}</tippy>
              </td>
            </tr>
          </tbody>
        </table>

        <button
          @click="saveRights"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded mt-4 transition-colors"
        >
          Rechte speichern
        </button>
      </div>

      <!-- Funktionen Rechte -->
      <div v-if="activeTab === 'functions'">
        <div class="p-4 bg-layout-sun-100 dark:bg-layout-night-100 rounded-lg shadow-sm">
          <table class="w-full border-collapse text-sm md:text-base">
            <thead class="bg-layout-sun-300 dark:bg-layout-night-300 text-layout-sun-800 dark:text-layout-night-800">
              <tr>
                <th class="px-4 py-3 text-left">Modul</th>
                <th class="px-4 py-3 text-left">Beschreibung</th>
                <th class="px-4 py-3 text-left">Aktiv</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(value, key) in lf"
                :key="key"
                class="hover:bg-layout-sun-200 dark:hover:bg-layout-night-200 transition duration-200 border-b border-gray-200 dark:border-gray-700"
              >
                <td class="px-4 py-3 text-left">{{ stripXkis(key) }}</td>
                <td class="px-4 py-3 text-left">{{ getLabel(key) }}</td>
                <td class="px-4 py-3 text-left">
                  <input-checkbox v-model="localFunc[key]" />
                </td>
              </tr>
            </tbody>
          </table>

          <button
            @click="saveRights"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded mt-4"
          >
            Modulrechte speichern
          </button>
        </div>
      </div>

      <!-- Benutzer Tab -->
     <!-- Benutzer Tab -->
<div v-if="activeTab === 'users'" class="p-4 bg-layout-sun-100 dark:bg-layout-night-100 rounded-lg shadow-sm">
  <table class="w-full border-collapse text-sm md:text-base">
    <thead class="bg-layout-sun-300 dark:bg-layout-night-300 text-layout-sun-800 dark:text-layout-night-800">
      <tr>
        <th class="px-4 py-3 text-left">Profil</th>
        <th class="px-4 py-3 text-left">Benutzer</th>
        <th class="px-4 py-3 text-left">Rolle</th>
        <th class="px-4 py-3 text-left">Ausgewählt</th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="u in activeUsers"
        :key="u.id"
        class="hover:bg-layout-sun-200 dark:hover:bg-layout-night-200 transition duration-200 border-b border-gray-200 dark:border-gray-700"
      >
        <!-- Profilbild -->
        <td class="px-4 py-3">
          <img
            v-if="u.profile_photo_path"
            :src="`/images/_ab/users/profile_photo_path/${u.profile_photo_path}`"
            class="w-8 h-8 rounded-full object-cover"
            alt="Profilbild"
          />
          <span v-else class="w-8 h-8 inline-block bg-gray-300 rounded-full"></span>
        </td>

        <!-- Nickname -->
        <td class="px-4 py-3">{{ u.name }}</td>

        <!-- Rechte Auswahl -->
        <td class="px-4 py-3">
  <div class="relative w-48">
    <button
      @click="u.showDropdown = !u.showDropdown"
      class="w-full border rounded px-2 py-1 flex items-center justify-between"
    >
      <img v-if="u.selectedRoleIcon" :src="u.selectedRoleIcon" class="w-4 h-4 mr-2" />
      <span>{{ u.selectedRoleName || 'Wähle Rolle' }}</span>
      <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
      </svg>
    </button>

    <ul v-if="u.showDropdown" class="absolute z-10 w-full border rounded bg-white dark:bg-gray-800 mt-1 max-h-40 overflow-auto">
      <li
        v-for="r in roles"
        :key="r.id"
        @click="selectRole(u, r)"
        class="flex items-center px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer"
      >
        <img :src="`/images/icons/ugr/${r.name}`" class="w-4 h-4 mr-2" />
        <span>{{ r.name }}</span>
      </li>
    </ul>
  </div>

        </td>

        <!-- Checkbox -->
        <td class="px-4 py-3 text-left">
          <input-checkbox v-model="selectedUsers[u.users_rights_id]" />
        </td>
      </tr>
    </tbody>
  </table>

  <!-- Buttons unter der Tabelle -->
  <div class="flex items-center gap-3 mt-4">
    <button
      @click="saveUserRights"
      class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded"
    >
      Benutzerrechte speichern
    </button>

    <button
      @click="selectAllVisible"
      class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded"
    >
      Alle auswählen (sichtbar)
    </button>

    <button
      @click="clearAllSelection"
      class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded"
    >
      Auswahl löschen
    </button>
  </div>
</div>



    </div>
  </section>
</template>

<script>
import { toastBus } from '@/utils/toastBus';
import { GetSettings } from "@/helpers";
import axios from "axios";
import InputSelect from "@/Application/Components/Form/InputSelect.vue";
import InputCheckbox from "@/Application/Components/Form/InputCheckbox.vue";
import IconRight from "@/Application/Components/Icons/IconRight.vue";

export default {
  name: "RightsTable",
  components: { InputSelect, InputCheckbox, IconRight },
  props: {
    adminTables: { type: Array, default: () => [] },
    urid: [String, Number],
    roles: { type: Array, default: () => [] },
  },
  data() {
    const rights = {};
    const fields = [
      "view_table", "add_table", "edit_table",
      "publish_table", "date_table", "delete_table",
    ];
    for (const field of fields) rights[field] = [];

    return {
      rights,
      userRights: {},
      selected: String(this.urid || ''),
      activeTab: "tables",
      settings: {},
      localFunc: {},
      lf: {},
      lf2: {},
      users: [],
      selectedUsers: {},
      userSearch: '',
      filteredUsers: [],
       showDropdown: false,
    selectedRoleId: null,
    selectedRoleName: 'Wähle Rolle',
    selectedRoleIcon: null,
     // Array deiner Rollen
    };
  },
  computed: {
    activeUsers() {
      return this.users.filter(u => Number(u.xis_disabled) === 0);
    }
  },
  methods: {
    selectRole(r) {
    this.selectedRoleId = r.id;
    this.selectedRoleName = r.name;
    this.selectedRoleIcon = `/images/icons/ugr/${r.name}.gif`;
    this.showDropdown = false;
  },
    // safer tab select (we load users when switching to users tab)
    selectTab(tab) {
      this.activeTab = tab;
      if (tab === 'users') {
        this.loadUsers();
      }
      if (tab === 'functions') {
        // ensure functions loaded for current selected role
        this.loadFunctions(this.selected);
      }
      if (tab === 'tables') {
        // ensure rights loaded for selected role
        this.fetchRights(this.selected);
      }
    },

    tabClass(tab) {
      return [
        'cursor-pointer px-4 py-2',
        this.activeTab === tab ? 'border-b-2 border-blue-500 font-bold' : ''
      ];
    },

    navigate() {
      // called when role selection changes
      this.fetchRights(this.selected);
      this.loadFunctions(this.selected);
      if (this.activeTab === 'users') this.loadUsers();
    },

    // --- Users ---
    async loadUsers() {
      try {
        const res = await axios.get('/api/users_rights'); // adjust endpoint as needed
        // Expect array of users with at least { users_rights_id, name, xis_disabled }
        this.users = Array.isArray(res.data) ? res.data : [];
        // initialize selectedUsers map (preserve existing values if present)
        const newMap = {};
        this.users.forEach(u => {
          newMap[u.users_rights_id] = (this.selectedUsers[u.users_rights_id] === true);
        });
        this.selectedUsers = newMap;
        // setup filtered list
        this.applyUserFilter();
      } catch (e) {
        console.error('Fehler beim Laden der User:', e);
        toastBus.emit('toast', { message: 'Fehler beim Laden der Benutzer', type: 'error' });
      }
    },

    applyUserFilter() {
      const q = this.userSearch.trim().toLowerCase();
      if (!q) {
        this.filteredUsers = this.activeUsers.slice();
      } else {
        this.filteredUsers = this.activeUsers.filter(u =>
          (u.name || '').toLowerCase().includes(q) ||
          String(u.users_rights_id).includes(q)
        );
      }
    },

    selectAllVisible() {
      this.filteredUsers.forEach(u => {
    this.selectedUsers[u.users_rights_id] = true;
  });
    },

    clearAllSelection() {
      Object.keys(this.selectedUsers).forEach(k => { this.selectedUsers[k] = false; });
    },

    saveUserRights() {
      const payload = Object.keys(this.selectedUsers).filter(id => this.selectedUsers[id]);
      axios.post('/api/save_user_rights', { users_rights_ids: payload })
        .then(() => {
          toastBus.emit('toast', { message: 'Benutzerrechte gespeichert!', type: 'success' });
        })
        .catch(e => {
          console.error('Fehler beim Speichern:', e);
          toastBus.emit('toast', { message: 'Speichern fehlgeschlagen!', type: 'error' });
        });
    },

    // --- Rechteverwaltung (Tabellen/Funktionen) ---
    async fetchRights(urid) {
      try {
        const response = await axios.get(`/admin/user-rights/get?urid=${urid}`);
        this.userRights = response.data || {};
        this.initializeRights();
      } catch (e) {
        console.error("Fehler beim Laden der Rechte:", e);
      }
    },

    initializeRights() {
      const fieldNames = Object.keys(this.rights);
      for (const field of fieldNames) {
        const binaryString = this.userRights[field] || "";
        const padded = binaryString.padEnd(this.adminTables.length, "0");
        for (let i = 0; i < this.adminTables.length; i++) {
          this.rights[field][i] = padded[i] === "1";
        }
      }
    },

    saveRights() {
      const payload = {};
      for (const [key, value] of Object.entries(this.rights)) {
        if (Array.isArray(value)) payload[key] = value.map(v => (v ? "1" : "0")).join("");
      }
      for (const [key, value] of Object.entries(this.lf2 || {})) payload[key] = value;

      axios.post('/api/admin/user-rights/save?urid=' + this.selected, payload)
        .then(res => toastBus.emit('toast', { message: res.data?.message || 'Gespeichert', type: 'success' }))
        .catch(err => console.error('❌ Fehler beim Speichern:', err));
    },

    togglerow(index) {
      const allEnabled = Object.keys(this.rights).every(field => this.rights[field][index]);
      const newValue = !allEnabled;
      for (const field in this.rights) this.rights[field][index] = newValue;
    },

    async loadFunctions(urid) {
      try {
        const response = await axios.get(`/admin/user-rights/get?urid=${urid}`);
        Object.entries(response.data || {}).forEach(([key, value]) => {
          if (key.includes("xkis_")) this.lf[key] = value;
        });
        this.localFunc = response.data || {};
      } catch (e) {
        console.error("Fehler beim Laden der Funktionen:", e);
      }
    },

    stripXkis(key) {
      return key.replace(/^xkis_/, '');
    },

    getLabel(key) {
      let labels = this.settings?.exl?.[this.stripXkis(key)];
      return labels || this.stripXkis(key);
    },

    ucf(str) {
      return String(str).split("_").map(v => v.charAt(0).toUpperCase() + v.slice(1).toLowerCase()).join(" ");
    },

    ucf2(str) {
      return this.settings.exl?.[str] ?? str;
    },
  },

  async mounted() {
    this.settings = await GetSettings();
    // load initial data for default selection
    if (this.selected) {
      this.fetchRights(this.selected);
      this.loadFunctions(this.selected);
    } else {
      this.fetchRights(this.urid || '');
      this.loadFunctions(this.urid || '');
    }
  },

  watch: {
    // watch selected role and reload
    selected(newVal) {
      this.navigate(newVal);
    },

    localFunc: {
      immediate: true,
      deep: true,
      handler(newVal) {
        this.lf2 = {};
        for (const key in newVal) {
          if (key.includes("xkis_")) this.lf2[key] = newVal[key] === 1 ? 1 : 0;
        }
      }
    }
  }
};
</script>

<style>
.wff {
  min-width: 200px !important;
}
</style>

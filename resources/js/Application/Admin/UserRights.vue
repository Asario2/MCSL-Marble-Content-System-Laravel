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
                  <IconDarr class="w-5 h-5" fill="currentColor" v-tippy />
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
          class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded mt-4 transition-colors"
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

<span v-if="addF" class="flex items-end gap-0">

  <!-- Feld 1 -->
  <div class="w-1/2">
    <InputFormText
      id="addF"
      label="Funktionsname"
      name="addF"
      placeholder="Funktionnamen angeben"
      v-model="addedF"
    >
      <template #label>Funktionsname</template>
    </InputFormText>
  </div>

  <!-- Feld 2 -->
  <div class="w-1/2">
    <InputFormText
      id="function_desc"
      label="Beschreibung"
      name="function_desc"
      placeholder="Beschreibung"
      v-model="fdesc"
    >
      <template #label>Beschreibung</template>
    </InputFormText>
  </div>

  <!-- Button -->
  <button
    @click="addfsubm"
    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded h-[42px]"
  >
    Speichern
  </button>

</span>

          <button
            @click="saveRights"
            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded mt-4"
          >
            Modulrechte speichern
          </button>
          &nbsp;&nbsp;<button @click="addFunc" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded mt-4"> Funktion hinzufügen </button>
        </div>

      </div>

      <!-- Benutzer Tab -->
      <!-- Benutzer Tab -->
<div v-if="activeTab === 'users'" class="p-4 bg-layout-sun-100 dark:bg-layout-night-100 rounded-lg shadow-sm">

  <!-- Suchfeld -->
  <div class="mb-4 flex justify-end items-center gap-2">
  <search-filter
                v-model="userSearch"
                class="w-full"
                ref="searchField"
                @reset="reset"
                placeholder="Benutzer Suchen"
                />
    <!-- <input
    v-model="userSearch"
    type="text"
    placeholder="Benutzer suchen..."
    class="block p-4 pl-10 w-full rounded-lg border text-sm text-layout-sun-900 bg-layout-sun-50 border-layout-sun-300 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:text-layout-night-900 dark:bg-layout-night-50 dark:border-layout-night-300 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
    >

  <button
    @click="userSearch = ''"
    class="absolute right-2 bottom-2 font-medium rounded-lg text-sm px-4 py-2 cursor-pointer border-2 focus:ring focus:outline-none bg-primary-sun-500 text-primary-sun-100 hover:text-primary-sun-900 hover:bg-layout-sun-100 hover:border-primary-sun-600 focus:border-primary-sun-600 dark:bg-primary-night-500 dark:text-primary-night-100 dark:hover:text-primary-night-900 dark:hover:bg-layout-night-100 dark:hover:border-primary-night-600 dark:focus:border-primary-night-600"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
    </svg>
    <span>Zurücksetzen</span>
  </button> -->
</div>


  <!-- Tabelle -->
  <table class="w-full border-collapse text-sm md:text-base">
    <thead class="bg-layout-sun-300 dark:bg-layout-night-300 text-layout-sun-800 dark:text-layout-night-800">
      <tr>
        <th class="px-4 py-3 text-left">Profil</th>
        <th class="px-4 py-3 text-left">Benutzer</th>
        <th class="px-4 py-3 text-left">Rolle</th>
        <th class="px-4 py-3 text-left">
          <button
            @click="toggleDisabledSelection"
            class="flex items-center gap-2 text-left hover:text-red-600 transition"
          >
            <ErrorSVG class="w-5 h-5" />
            <span>Disabled</span>
          </button>
        </th>
      </tr>
    </thead>

    <tbody>
      <tr
        v-for="u in filteredUsers"
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
          <img
            v-else
            :src="`/images/_ab/users/profile_photo_path/008.jpg`"
            class="w-8 h-8 rounded-full object-cover"
            alt="Profilbild"
          />
        </td>

        <!-- Benutzername -->
        <td class="px-4 py-3">{{ u.name }}</td>

        <!-- Rollen-Auswahl mit Icon links -->
        <td class="px-4 py-3">
          <div class="relative flex items-center" :ref="'dropdown' + u.id">
            <!-- Icon links außerhalb -->
            <img
              :src="u.hoverIcon || u.selectedRoleIcon || '/images/icons/ugr/default.gif'"
              alt="icon"
              class="w-5 h-5 mr-2"
            />

            <!-- Button -->
            <button
              @click.stop="toggleDropdown(u)"
              class="border rounded px-3 py-1 w-full text-left dark:bg-gray-800 dark:border-gray-600 flex items-center justify-between"
            >
              <span>{{ u.selectedRoleName || 'Wähle Rolle' }}</span>
              <svg class="w-4 h-4 ml-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>

            <!-- Dropdown -->
            <div
              v-if="u.showDropdown"
              class="absolute left-0 mt-1 w-[calc(100%+2rem)] bg-white dark:bg-gray-900 border dark:border-gray-700 rounded shadow-md z-50"
            >
              <div
                v-for="r in reversedRoles"
                :key="r.id"
                @mouseover="u.hoverIcon = '/images/icons/ugr/' + r.name + '.gif'"
                @mouseleave="u.hoverIcon = u.selectedRoleIcon"
                @click.stop="selectRoleForUser(u, r)"
                class="px-3 py-2 flex items-center gap-2 hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer"
              >
                <img :src="'/images/icons/ugr/' + r.name + '.gif'" class="w-5 h-5" />
                <span>{{ r.name }}</span>
              </div>
            </div>
          </div>
        </td>

        <!-- Checkbox -->
        <td class="px-4 py-3 text-left">
          <input-checkbox v-model="selectedUsers[u.id]"  @change="toggleDisabled(u)"/>

        </td>
      </tr>
    </tbody>
  </table>

  <!-- Buttons -->
  <div class="flex items-center gap-3 mt-4">
    <button
      @click="saveAllUserRoles"
      class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded"
    >
      Benutzerrollen speichern
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
import InputFormText from "@/Application/Components/Form/InputFormText.vue";
import InputSelect from "@/Application/Components/Form/InputSelect.vue";
import InputCheckbox from "@/Application/Components/Form/InputCheckbox.vue";
import IconDarr from "@/Application/Components/Icons/IconDarr.vue";
import ErrorSVG from "@/Application/Components/Icons/ErrorSVG.vue";
import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";

export default {
  name: "UserRights",
  components: { ErrorSVG, SearchFilter, InputSelect, InputCheckbox, IconDarr, InputFormText },
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
      localFunc: {},    // nur xkis_ functions (reactive)
      lf: {},           // functions list
      lf2: {},
      users: [],
      selectedUsers: {},
      userSearch: '',
      addF: false,
      labels: {},       // labels von API (beschreibungen)
      showDisabled: false,

      // fehlende Felder, die im Template/Methods verwendet werden
      addedF: '',
      fdesc: '',
    };
  },
  computed: {
    activeUsers() {
      return this.users.filter(u => Number(u.xis_disabled) === 0);
    },
    disabledUsers() {
      return this.users.filter(u => Number(u.xis_disabled) === 1);
    },
    reversedRoles() {
      return [...this.roles].sort((a, b) => Number(b.position) - Number(a.position));
    },
    filteredUsers() {
      return this.users
        .filter(u => this.showDisabled ? Number(u.xis_disabled) === 1 : Number(u.xis_disabled) === 0)
        .filter(u => u.name.toLowerCase().includes(this.userSearch.toLowerCase()));
    },
  },
  methods: {
    // --- Funktionen / UI ---
    addFunc() {
      this.addF = !this.addF;
    },

    async addfsubm() {
      try {
        const res = await axios.post('/api/AddFunc', {
          name: this.addedF,
          desc: this.fdesc,
        });

        window.toastBus.emit( {
          message: res.data?.message,
          type: 'success'
        });

        // Eingabe zurücksetzen
        this.addedF = "";
        this.fdesc = "";
        this.addF = false;

        // Settings neu laden (damit descriptions aus Settings sichtbar werden)
        await this.reloadSettings();

        // Funktionen neu laden (sichtbar + reaktiv)
        await this.loadFunctions(this.selected);
      } catch (err) {
        console.error(err);
        window.toastBus.emit( {
          message: 'Fehler beim Aktualisieren des Status!',
          type: 'error'
        });
      }
    },

    // reload Settings helper
    async reloadSettings() {
      try {
        this.settings = await GetSettings();
      } catch (e) {
        console.error("reloadSettings error", e);
      }
    },

    // --- User / Rollen ---
    toggleDisabled(u) {
      const isChecked = this.selectedUsers[u.id];
      u.xis_disabled = isChecked ? 1 : 0;

      axios.post('/api/save-user-disabled', {
        id: u.id,
        xis_disabled: u.xis_disabled
      })
        .then(res => {
          window.toastBus.emit( {
            message: res.data?.message || (isChecked ? 'Benutzer deaktiviert' : 'Benutzer aktiviert'),
            type: 'success'
          });
        })
        .catch(err => {
          console.error(err);
          window.toastBus.emit( {
            message: 'Fehler beim Aktualisieren des Status!',
            type: 'error'
          });
        });
    },

    toggleDisabledSelection() {
      this.showDisabled = !this.showDisabled;

      if (this.showDisabled) {
        this.users.forEach(u => {
          if (Number(u.xis_disabled) === 1) {
            this.selectedUsers[u.users_rights_id] = true;
          }
        });
      } else {
        this.users.forEach(u => {
          if (Number(u.xis_disabled) === 1) {
            this.selectedUsers[u.users_rights_id] = false;
          }
        });
      }
    },

    toggleUserCheckbox(u) {
      const roleId = u.users_rights_id;
      const newState = !this.selectedUsers[roleId];

      this.users.forEach(user => {
        if (user.users_rights_id === roleId) {
          this.selectedUsers[roleId] = newState;
        }
      });
    },

    reset() {
      this.userSearch = '';
    },

    saveSelectedUserRoles() {
      this.activeUsers.forEach(u => {
        if (this.selectedUsers[u.users_rights_id]) {
          this.saveUserRole(u);
        }
      });
    },

    saveUserRole(u) {
      if (!u.selectedRoleId) {
        window.toastBus.emit( { message: 'Bitte eine Rolle auswählen!', type: 'error' });
        return;
      }

      const payload = {
        id: u.id,
        users_rights_id: u.selectedRoleId,
        xis_disabled: u.xis_disabled
      };

      axios.post('/api/save-user-role', payload)
        .then(res => {
         
          u.selectedRoleIcon = `/images/icons/ugr/${u.selectedRoleName}.gif`;
          u.hoverIcon = u.selectedRoleIcon;
        })
        .catch(err => {
          console.error('Fehler beim Speichern der Rolle:', err);
          window.toastBus.emit( { message: 'Fehler beim Speichern!', type: 'error' });
        });
    },

    // --- Tabs ---
    selectTab(tab) {
      this.activeTab = tab;
      if (tab === 'users') this.loadUsers();
      if (tab === 'functions') this.loadFunctions(this.selected);
      if (tab === 'tables') this.fetchRights(this.selected);
    },
    tabClass(tab) {
      return ['cursor-pointer px-4 py-2', this.activeTab === tab ? 'border-b-2 border-blue-500 font-bold' : ''];
    },

    navigate() {
      this.fetchRights(this.selected);
      this.loadFunctions(this.selected);
      if (this.activeTab === 'users') this.loadUsers();
    },

    // --- Dropdown ---
    toggleDropdown(u) {
      this.users.forEach(user => { if (user !== u) user.showDropdown = false; });
      u.showDropdown = !u.showDropdown;
    },
    selectRoleForUser(u, r) {
      u.selectedRoleId = r.id;
      u.selectedRoleName = r.name;
      u.selectedRoleIcon = `/images/icons/ugr/${r.name}.gif`;
      u.showDropdown = false;
      u.hoverIcon = u.selectedRoleIcon;
    },

    saveAllUserRoles() {
      const payload = this.users
        .filter(u => u.selectedRoleId)
        .map(u => ({ id: u.id, users_rights_id: u.selectedRoleId }));

      if (payload.length === 0) {
        window.toastBus.emit( { message: 'Keine Rollen zum Speichern ausgewählt!', type: 'error' });
        return;
      }

      axios.post('/api/save-user-roles', { users: payload })
        .then(res => {
          window.toastBus.emit( { message: res.data?.message || 'Rollen gespeichert!', type: 'success' });
          this.users.forEach(u => {
            const role = this.roles.find(r => r.id === u.selectedRoleId);
            if (role) {
              u.selectedRoleIcon = `/images/icons/ugr/${role.name}.gif`;
              u.hoverIcon = u.selectedRoleIcon;
            }
          });
        })
        .catch(err => {
          console.error(err);
          window.toastBus.emit( { message: 'Fehler beim Speichern!', type: 'error' });
        });
    },

    // --- User Actions ---
    async loadUsers() {
      try {
        const res = await axios.get('/api/users_rights');
        this.users = Array.isArray(res.data) ? res.data : [];

        this.users.forEach(u => {
          this.selectedUsers[u.id] = Number(u.xis_disabled) === 1;
        });

        this.users.forEach(u => {
          u.selectedRoleId = this.roles.find(r => r.id === u.users_rights_id)?.id || null;
          u.selectedRoleName = this.roles.find(r => r.id === u.users_rights_id)?.name || '';
          u.selectedRoleIcon = u.selectedRoleName ? `/images/icons/ugr/${u.selectedRoleName}.gif` : null;
          u.showDropdown = false;
          u.hoverIcon = u.selectedRoleIcon;
        });
      } catch (e) {
        console.error(e);
      }
    },

    selectAllVisible() { this.activeUsers.forEach(u => this.selectedUsers[u.users_rights_id] = true); },
    clearAllSelection() { Object.keys(this.selectedUsers).forEach(k => this.selectedUsers[k] = false); },

    saveUserRights() {
      const payload = Object.keys(this.selectedUsers).filter(id => this.selectedUsers[id]);
      axios.post('/api/save_user_rights', { users_rights_ids: payload })
        .then(() => window.toastBus.emit( { message: 'Benutzerrechte gespeichert!', type: 'success' }))
        .catch(() => window.toastBus.emit( { message: 'Speichern fehlgeschlagen!', type: 'error' }));
    },

    // --- Rechteverwaltung Tabellen ---
    async fetchRights(urid) {
      try {
        const res = await axios.get(`/admin/user-rights/get?urid=${urid}`);

        // support both shapes: { rights: {...}, labels: {...} } OR flat object
        const rightsPayload = res.data?.rights || res.data || {};
        this.userRights = rightsPayload;

        // if labels included, set them
        this.labels = res.data?.labels || this.labels || {};

        // initialize rights array states
        this.initializeRights();
      } catch (e) {
        console.error(e);
      }
    },

    initializeRights() {
      const fieldNames = Object.keys(this.rights);

      // ensure adminTables length exists
      const total = this.adminTables.length || 0;

      for (const field of fieldNames) {
        // binary string like "10101" or empty
        let binary = this.userRights[field] ?? '';

        // if numeric (e.g. 0/1), coerce to string
        if (typeof binary === 'number') binary = String(binary);

        // pad to adminTables length
        const padded = (binary || '').padEnd(total, '0');

        // ensure rights[field] array length
        this.rights[field] = this.rights[field] || [];

        for (let i = 0; i < total; i++) {
          this.rights[field][i] = padded[i] === '1';
        }
      }
    },

    togglerow(index) {
      const allEnabled = Object.keys(this.rights).every(f => this.rights[f][index]);
      for (const f in this.rights) this.rights[f][index] = !allEnabled;
    },

    saveRights() {
      const payload = {};
      // tables
      for (const [key, value] of Object.entries(this.rights)) payload[key] = value.map(v => v ? '1' : '0').join('');

      // functions (localFunc)
      for (const [k, v] of Object.entries(this.localFunc || {})) {
        payload[k] = v ? "1" : "0";
      }

      axios.post('/api/admin/user-rights/save?urid=' + this.selected, payload)
        .then(r => window.toastBus.emit( { message: r.data?.message || 'Gespeichert', type: 'success' }))
        .catch(e => console.error(e));
    },

    // --- Funktionen ---
    async loadFunctions(urid) {
      try {
        const res = await axios.get(`/admin/user-rights/get?urid=${urid}`);

//         console.log("RESP", res.data);

        // rights root (don't overwrite this.userRights or tables)
        const rights = res.data?.rights || res.data || {};

        // Only xkis_ keys (functions)
        const functions = Object.entries(rights).filter(([k]) => k.startsWith("xkis_"));

        functions.sort((a, b) => a[0].localeCompare(b[0]));

        this.lf = Object.fromEntries(functions);

        // labels from API (descriptions)
        this.labels = res.data?.labels || this.labels || {};

        // set localFunc only to the functions (deep copy for reactivity)
        const newLocal = {};
        for (const [k, v] of functions) {
          newLocal[k] = v;
        }
        this.localFunc = JSON.parse(JSON.stringify(newLocal));

      } catch (e) {
        console.error(e);
      }
    },

    handleClickOutside(event) {
      this.users.forEach(u => {
        const el = this.$refs['dropdown' + u.id]?.[0];
        if (u.showDropdown && (!el || !el.contains(event.target))) {
          u.showDropdown = false;
          u.hoverIcon = null;
        }
      });
    },

    stripXkis(k) { return k.replace(/^xkis_/, ''); },

    getLabel(k) {
      const key = this.stripXkis(k);
      return this.labels?.[key] || this.settings.exl?.[key] || key;
    },

    ucf(str) { return String(str).split('_').map(s => s.charAt(0).toUpperCase() + s.slice(1).toLowerCase()).join(' '); },
    ucf2(str) { return this.settings.exl?.[str] ?? str; },
  },

  async mounted() {
    // initial settings
    this.settings = await GetSettings();

    // initial load: fetch rights & functions for current selection or urid
    const ur = this.selected || this.urid || '';
    if (ur) {
      await this.fetchRights(ur);
      await this.loadFunctions(ur);
    } else {
      await this.fetchRights('');
      await this.loadFunctions('');
    }

    // click outside dropdown
    document.addEventListener('click', e => {
      this.users.forEach(u => {
        if (u.showDropdown && !e.target.closest('.relative.inline-block')) u.showDropdown = false;
      });
    });
  },

  watch: {
    selected(newVal) { this.navigate(newVal); },
    localFunc: {
      immediate: true,
      deep: true,
      handler(newVal) {
        // build lf2 for backwards compat (if used elsewhere)
        this.lf2 = {};
        for (const k in newVal) if (k.includes('xkis_')) this.lf2[k] = newVal[k] === 1 ? 1 : (newVal[k] ? 1 : 0);
      }
    },
  }
}
</script>


<style>
.wff { min-width: 200px !important; }
</style>


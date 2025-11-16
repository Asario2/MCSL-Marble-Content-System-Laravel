<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-3xl">
      <!-- Header -->
      <div class="flex justify-between items-center p-4 border-b dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
          Empfänger/in hinzufügen
        </h2>
        <button  type="button" @click="closeModal" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">✕</button>
      </div>

      <!-- Tabs -->
      <div class="flex border-b dark:border-gray-700 text-sm font-medium">
        <button type='button'
          v-for="tab in tabs"
          :key="tab.value"
          @click="activeTab = tab.value"
          :class="activeTab === tab.value
            ? 'px-4 py-2 border-b-2 border-blue-500 text-blue-600 dark:text-blue-400'
            : 'px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200'">
          {{ tab.label }}
        </button>
      </div>

      <!-- Tab Content -->
      <div class="p-4 max-h-[70vh] overflow-y-auto">
        <div v-if="activeTab === 'benutzer'">
          <UserSelect
            :users="user"
            v-model:selectedIds="selected.users"
            @selection="updateSelectedItems('users', $event)"
          />
        </div>

        <div v-else-if="activeTab === 'benutzergruppe'">
          <UserGroupSelect
            :usergroups="usergroups"
            v-model:selectedGroupIds="selected.groups"
            @selection="updateSelectedItems('groups', $event)"
          />
        </div>

        <div v-else-if="activeTab === 'kontakte'">
          <ContactSelect
            :contacts="contacts"
            v-model:selectedContactIds="selected.contacts"
            @selection="updateSelectedItems('contacts', $event)"
          />
        </div>
      </div>

      <!-- Footer -->
      <div class="flex items-center justify-between gap-4 p-4 border-t dark:border-gray-700">
  <!-- Checkbox links -->
  <div class="flex items-center gap-2">
    <InputCheckbox id="checkNews" v-model="checkNews" value="1"
    @change="$emit('check-news-changed', checkNews)"
    />
    <label for="checkNews" class="text-sm text-gray-800 dark:text-gray-200">
     An Externe Newsletter Empfänger senden
    </label>
  </div>

  <!-- Buttons rechts -->
  <div class="flex items-center gap-2">
    <button
      type="button"
      @click="closeModal"
      class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200"
    >
      Abbrechen
    </button>
    <button
      type="button"
      @click="confirmSelection"
      class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white"
    >
      Übernehmen ({{ allSelectedNames.length }})
    </button>
  </div>
</div>
</div>
  </div>
</template>

<script>
import UserSelect from "./UserSelect.vue";
import UserGroupSelect from "./UserGroupSelect.vue";
import ContactSelect from "./ContactSelect.vue";
import { nextTick } from 'vue';
import InputCheckbox from "@/Application/Components/Form/InputCheckbox.vue";
export default {
  name: "SelectRecipient",
  components: { UserSelect, UserGroupSelect, ContactSelect, InputCheckbox},
  props: {
    show: Boolean,
    user: { type: [Array, Object], default: () => [] },
    usergroups: { type: [Array, Object], default: () => [] },
    contacts: { type: [Array, Object], default: () => [] },
  },
  data() {
    return {
      activeTab: "benutzer",
      tabs: [
        { label: "Benutzer", value: "benutzer" },
        { label: "BenutzerGruppe", value: "benutzergruppe" },
        { label: "Aus Kontakten", value: "kontakte" },
      ],
        checkNews: "1",
      selected: {
        users: [],
        groups: [],
        contacts: [],
      },

    };
  },
  computed: {
    allSelectedItems() {
      // alles zusammenführen
      return [
        ...this.selected.users,
        ...this.selected.groups,
        ...this.selected.contacts,
        ];
    },
    allSelectedNames() {
      return this.allSelectedItems.map(i => i.name);
    }
  },
  methods: {

  // Wird aufgerufen, wenn der Nutzer Häkchen setzt oder abwählt
  updateSelectedItems(type, items) {
    this.selected[type] = items;
    // Live-Update an Parent senden
    this.$emit('selection', this.allSelectedItems);
  },

    // Wird aufgerufen, wenn der Nutzer auf "Übernehmen" klickt
    confirmSelection() {
    this.$emit("confirm", {
        names: this.allSelectedNames,
        extern: this.checkNews === '1' || this.checkNews === true ? '_Externe Empfänger_, ' : ''
    });
    this.closeModal();
    },
    resetSelection() {
    this.selected = {
      users: [],
      groups: [],
      contacts: []
    };

    // Optional: Parent ebenfalls informieren
    this.$emit('selection', this.allSelectedItems);
  },

  // Wird aufgerufen, wenn das Modal per ✕ oder "Abbrechen" geschlossen wird
  closeModal() {
    // Emit komplettes Objekt {id, name}, damit Parent auch Abwahlen bekommt
    this.$emit("selection", this.allSelectedItems);
    // Modal ausblenden
    this.$emit("close");
  }
},

  mounted() {
    // Optional: vorher gespeicherte Selektion aus SessionStorage laden
    const storageKeys = {
      users: 'selectedUsers',
      groups: 'selectedGroups',
      contacts: 'selectedContacts'
    };
    nextTick(() => {
    this.selected = {
      users: [],
      groups: [],
      contacts: [],
    };
    // Parent informieren
    this.$emit('selection', this.allSelectedItems);
  });
    Object.keys(storageKeys).forEach(type => {
      try {
        const stored = JSON.parse(sessionStorage.getItem(storageKeys[type]) || '[]');
        if (Array.isArray(stored)) {
          // vom Subcomponent IDs zu Items mappen
          const list = this[type === 'users' ? 'user' : type === 'groups' ? 'usergroups' : 'contacts'];
          this.selected[type] = list.filter(item => stored.includes(item.id))
            .map(item => ({ id: item.id, name: item.name }));
        }
      } catch { this.selected[type] = []; }
    });
  }
};
</script>


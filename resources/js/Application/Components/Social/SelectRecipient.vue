<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-3xl">
        <!-- Header -->
        <div class="flex justify-between items-center p-4 border-b dark:border-gray-700">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            Empfänger/in hinzufügen
          </h2>
          <button @click="closeModal" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">✕</button>
        </div>

        <!-- Tabs -->
        <div class="flex border-b dark:border-gray-700 text-sm font-medium">
          <button
            v-for="tab in tabs"
            :key="tab.value"
            @click="activeTab = tab.value"
            class="px-4 py-2 transition-colors duration-150"
            :class="activeTab === tab.value
              ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400'
              : 'text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200'"
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- Tab Content -->
        <div class="p-4 max-h-[70vh] overflow-y-auto">
          <div v-if="activeTab === 'benutzer'">
            <!-- Komponente korrekt eingebunden -->
            <UserSelect />
          </div>

          <div v-else-if="activeTab === 'benutzergruppe'">
            <UserGroupSelect />
          </div>

          <div v-else-if="activeTab === 'kontakte'">
            <ContactSelect />
          </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-2 p-4 border-t dark:border-gray-700">
          <button @click="closeModal" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200">
            Abbrechen
          </button>
          <button @click="confirmSelection" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">
            Übernehmen
          </button>
        </div>
      </div>
    </div>
  </template>

  <script>
  // Passe diese Pfade an, falls deine Dateien an einem anderen Ort liegen:
//   import UserSelect from "./UserSelect.vue";
//   import UserGroupSelect from "./UserGroupSelect.vue";
//   import ContactSelect from "./ContactSelect.vue";

  export default {
    name: 'SelectRecipient',
    components: {
    //   UserSelect,
    //   UserGroupSelect,
    //   ContactSelect
    },
    props: {
      show: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        activeTab: 'benutzer',
        tabs: [
          { label: 'Benutzer', value: 'benutzer' },
          { label: 'BenutzerGruppe', value: 'benutzergruppe' },
          { label: 'Aus Kontakten', value: 'kontakte' }
        ]
      }
    },
    methods: {
      closeModal() {
        this.$emit('close');
      },
      confirmSelection() {
        // z.B. emit Auswahldaten hier: this.$emit('confirm', selected)
        this.$emit('confirm');
        this.$emit('close');
      }
    }
  }
  </script>

  <style scoped>
  button { outline: none; }
  </style>

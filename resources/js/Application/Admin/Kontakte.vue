<template>
    <Layout>
      <div class="p-4 bg-gray-900 text-white min-h-screen">
        <addbtn table="contacts" text="Neuer Kontakt"></addbtn>
        <!-- Prüfen ob Kontakte vorhanden sind -->
<newbtn table="didyouknow">
            </newbtn>
            <div class="flex justify-between items-center">
                <search-filter
                v-if="searchFilter"
                v-model="form.search"
                class="w-full"
                ref="searchField"
                @reset="reset"
                />

                </div>


        <template v-if="hasContacts">
          <template v-for="letter in sortedLetters" :key="letter">
            <div class="mb-6 rounded-lg overflow-hidden border border-black">

              <!-- Buchstaben-Trenner -->
              <div class="px-4 py-2 font-bold text-gray-300 text-lg bg-gray-800">
                {{ letter }}
              </div>

              <!-- Tabelle -->
              <table class="min-w-full table-auto border-collapse">
                <thead>
                  <tr class="bg-gray-700">
                    <th class="px-4 py-2 border-b border-r border-black text-left" width="1%">Mehr</th>
                    <th class="px-4 py-2 border-b border-r border-black text-left" width="1%">Gruppe</th>
                    <th class="px-4 py-2 border-b border-r border-black text-left" width="14%">Name</th>
                    <th class="px-4 py-2 border-b border-r border-black text-left" width="14%">Vorname</th>
                    <th class="px-4 py-2 border-b border-r border-black text-left" width="14%">Nachname</th>
                    <th class="px-4 py-2 border-b border-r border-black text-left" width="1%">Email</th>
                    <th class="px-4 py-2 border-b border-r border-black text-left" width="14%">Telefon</th>
                    <th class="px-4 py-2 border-b border-black text-left" width="14%">Handy</th>
                    <th class="px-4 py-2 border-b border-black text-left" width="1%">Aktionen</th>
                  </tr>
                </thead>
                <tbody>

                  <!-- Kontakt-Zeilen mit aufklappbaren Details -->
                  <template v-for="(contact, index) in groupedContacts[letter]" :key="contact.id">
                    <!-- Normale Zeile -->
                    <tr
                      class="hover:bg-gray-800"
                      :class="{ 'border-b border-black': index !== groupedContacts[letter].length - 1 }"
                    >
                      <!-- Info-Button -->
                      <td class="px-2 py-1 border-r border-black text-center">
                        <button
                          v-if="contact.Kommentar || contact.Adresse || contact.Geburtsdatum"
                          @click="toggleDetails(contact.id)"
                          class="text-blue-400 hover:text-blue-200 font-bold text-3xl"
                        >
                          ⓘ
                        </button>
                      </td>

                      <!-- Gruppen-Icon -->
                      <td class="px-2 py-1 border-r border-black text-center circle">
                        <img
                          :src="`/images/icons/Con_Groups/${contact.Gruppe}.gif`"
                          :alt="contact.Gruppe"
                          class="w-5 h-5 mx-auto"
                          @error="handleImageError"
                        />
                      </td>

                      <!-- Kontaktfelder -->
                      <td class="px-4 py-1 border-r border-black font-medium" v-html="rumLaut(contact.Name) || '-'"></td>
                      <td class="px-4 py-1 border-r border-black" v-html="rumLaut(contact.Vorname) || '-'"></td>
                      <td class="px-4 py-1 border-r border-black" v-html="rumLaut(contact.Nachname) || '-'"></td>
                      <td class="px-4 py-1 border-r border-black">
                        <span v-if="contact.Email">
                          <a :href="'mailto:' + contact.Email">
                            <img :src="'/images/icons/mail.png'">
                          </a>
                        </span>

                      </td>
                      <td class="px-4 py-1 border-r border-black">{{ contact.Telefon || '-' }}</td>
                      <td class="px-4 py-1 border-black">{{ contact.Handy || '-' }}</td>
                      <td class="px-4 py-1 border-black"><editbtns v-if="contact.us_poster == UID" table="contacts" :id="contact.id" :uid="contact.us_poster"></editbtns></td>
                    </tr>

                    <!-- Detail-Zeile -->
                    <tr v-if="isExpanded(contact.id)" :key="contact.id + '-details'">
                      <td colspan="8" class="px-4 py-2 bg-gray-800 text-gray-200 whitespace-pre-line">
                        {{ contact.Kommentar ? contact.Kommentar + "\n" : "" }}
                        {{ contact.Adresse ? contact.Adresse + "\n" : "" }}
                        {{ contact.Geburtsdatum ? "Geburtstag: " + contact.Geburtsdatum : "" }}
                      </td>
                    </tr>
                  </template>

                </tbody>
              </table>
            </div>
          </template>
        </template>

        <!-- Keine Kontakte gefunden -->
        <div v-else class="text-center py-8 text-gray-400">
          Keine Kontakte gefunden
        </div>

      </div>
    </Layout>
  </template>
<script>
import Layout from "@/Application/Admin/Shared/Layout.vue";
import { rumLaut } from "@/helpers";
import editbtns from "@/Application/Components/Form/editbtns.vue";
import { throttle } from "lodash";
import Addbtn from "@/Application/Components/Form/addbtn.vue";
import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
import pickBy from "lodash/pickBy";


export default {
  name: 'ContactTable',
  components: {
    Layout,
    editbtns,
    Addbtn,
    SearchFilter
  },
  props: {
    contacts: {
      type: Array,
      required: true,
      default: () => []
    },
    searchFilter: {
      type: Boolean,
      default: true,
    },
    filters: {
      type: Object,
      default: () => ({}),
    },
    searchText: {
      type: String,
      default: "Hier kannst du den Suchbegriff eingeben",
    }
  },
  data() {
    return {
      form: {
        search: this.filters?.search ?? "",
      },
      expandedRows: [],
      searchTimeout: null,
      isLoading: false,
      loading: false
    }
  },
  watch: {
    'form.search': {
      handler: throttle(function (newSearch) {
        const query = pickBy({ search: newSearch });
        this.$inertia.get(
          this.route("admin.kontakte"),
          query,
          {
            preserveState: true,
            replace: true,
          }
        );
      }, 300),
      immediate: false,
    },
  },
  computed: {
    hasContacts() {
      return Array.isArray(this.contacts) && this.contacts.length > 0;
    },
    groupedContacts() {
      if (!Array.isArray(this.contacts) || this.contacts.length === 0) {
        return {};
      }
      const grouped = {};
      this.contacts.forEach(contact => {
        if (contact && contact.Name) {
          const firstLetter = contact.Name.charAt(0).toUpperCase();
          if (!grouped[firstLetter]) grouped[firstLetter] = [];
          grouped[firstLetter].push(contact);
        }
      });
      return grouped;
    },
    UID(){
      return window.authid;
    },
    sortedLetters() {
      return Object.keys(this.groupedContacts).sort();
    }
  },
  methods: {
    reset() {
      this.form.search = "";
    },
    rumLaut,
    handleImageError(event) {
      event.target.style.display = 'none';
    },
    toggleDetails(id) {
      if (this.expandedRows.includes(id)) {
        this.expandedRows = this.expandedRows.filter(r => r !== id);
      } else {
        this.expandedRows.push(id);
      }
    },
    isExpanded(id) {
      return this.expandedRows.includes(id);
    }
  },
  mounted() {
    const params = new URLSearchParams(window.location.search);
    const search = params.get("search");

    if (search && search.trim() !== "") {
      this.isLoading = false;
      this.loading = false;
    } else {
      this.isLoading = false;
    }
  }
};
</script>

  <style scoped>
  .circle {
    background-image:url("/images/icons/Con_Groups/circle.png");
    background-repeat:no-repeat;
    background-position:center center;
  }

  td:empty::before {

    color: #6b7280;
  }

  button {
    cursor: pointer;
    background: none;
    border: none;
  }
  </style>

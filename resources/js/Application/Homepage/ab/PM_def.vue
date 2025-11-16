<template>
<Layout>
    <template #header>
      <breadcrumb :breadcrumbs="breadcrumbs" :current="'Private_Nachrichten'"></breadcrumb>
    </template>
    <MetaHeader title="Private Nachrichten" />

    <div class="max-w-none bg-layout-sun-100 dark:bg-layout-night-100 p-7 rounded-2xl shadow">
      <!-- Tabs -->
      <div class="flex border-b border-gray-300 dark:border-gray-700 mb-6">
        <button
          v-for="tabItem in tabs"
          :key="tabItem.id"
          @click="tab = tabItem.id"
          :class="[
            'flex-1 text-center py-3 border-b-2 font-medium transition',
            tab === tabItem.id
              ? 'border-blue-500 text-blue-600 dark:text-blue-400'
              : 'border-transparent hover:text-gray-600 dark:hover:text-gray-300'
          ]"
        >
          {{ tabItem.icon }} {{ tabItem.label }}
        </button>
      </div>

      <!-- Inhalte -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow px-4 pt-1">

        <!-- Inbox -->
        <div v-if="tab === 'inbox'" class="mt-[-20px] pb-3">
          <h2 class="text-xl font-semibold m-3 pt-5">📥 Posteingang</h2>
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Suchen..."
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 dark:bg-gray-700 dark:text-white mb-4"
          />
          <div class="p-6 bg-layout-sun-100 dark:bg-layout-night-100 rounded-xl">
            <table class="min-w-full text-left border-collapse border border-gray-300 dark:border-gray-700">
              <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                  <th class="border border-gray-300 dark:border-gray-700 text-center" width="16">
                    <InputCheckbox
                      v-model="selectAllInbox"
                      @change="toggleSelectAll('inbox')"
                      class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    />
                  </th>
                  <th class="border border-gray-300 dark:border-gray-700 text-center" width="16">Gelesen</th>
                  <th class="border border-gray-300 dark:border-gray-700 text-center" width="15%">Von</th>
                  <th class="border border-gray-300 dark:border-gray-700">Betreff</th>
                  <th class="border border-gray-300 dark:border-gray-700"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="msg in paginatedInbox" :key="msg.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="border border-gray-300 dark:border-gray-700 text-center">
                    <InputCheckbox
                      :value="msg.id"
                      v-model="selectedInbox"
                      class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    />
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700 text-center">
                    <span
                      class="inline-block w-3 h-3 rounded-full"
                      :class="msg.checked ? 'bg-green-500' : 'bg-red-500'"
                    ></span>
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700 flex items-center">
                    <img :src="GetProfileImagePath(msg.avatar)" :alt="msg.user" :title="msg.user" class="w-8 h-8 rounded-full object-cover"/>
                    <span class="ml-2">{{ msg.user }}</span>
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700">
                    <span class="font-bold cursor-pointer" @click="ShowMessage(msg)">{{ msg.subject }}</span>
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700 text-center pr-2">
                    <editbtns table="private_messages" :pm="true" :id="msg.id" :noedit="true"></editbtns>
                  </td>
                </tr>
                <tr v-if="paginatedInbox.length === 0">
                  <td colspan="5" class="text-center py-4 text-gray-500 dark:text-gray-300">Keine Nachrichten gefunden</td>
                </tr>
              </tbody>
            </table>

            <!-- Buttons Inbox -->
            <div class="mt-4 flex gap-2">
              <button
                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
                @click="markAsRead"
                :disabled="selectedInbox.length === 0"
              >
                Als gelesen markieren
              </button>
              <button
                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                @click="deleteMessages('inbox')"
                :disabled="selectedInbox.length === 0"
              >
                Löschen
              </button>
            </div>

            <!-- Pagination Inbox -->
            <div class="mt-4 flex justify-center space-x-1">
              <button
                v-for="page in inboxTotalPages"
                :key="page"
                @click="currentPageInbox = page"
                :class="['px-3 py-1 rounded', currentPageInbox === page ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300']"
              >
                {{ page }}
              </button>
            </div>
          </div>
        </div>

        <!-- Outbox -->
        <div v-else-if="tab === 'outbox'" class="mt-[-20px] pb-3">
          <h2 class="text-xl font-semibold m-3 pt-5">📤 Gesendete Nachrichten</h2>
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Suchen..."
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 dark:bg-gray-700 dark:text-white mb-4"
          />
          <div class="p-6 bg-layout-sun-100 dark:bg-layout-night-100 rounded-xl">
            <table class="min-w-full text-left border-collapse border border-gray-300 dark:border-gray-700">
              <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                  <th class="border border-gray-300 dark:border-gray-700 text-center">
                    <InputCheckbox
                      v-model="selectAllOutbox"
                      @change="toggleSelectAll('outbox')"
                      class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    />
                  </th>
                  <th class="border text-center" width="15%">An</th>
                  <th class="border">Betreff</th>
                  <th class="border"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="msg in paginatedOutbox" :key="msg.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="border border-gray-300 dark:border-gray-700 text-center">
                    <InputCheckbox"
                      :value="msg.id"
                      v-model="selectedOutbox"
                      class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    />
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700 flex items-center">
                    <img :src="GetProfileImagePath(msg.avatar)" :alt="msg.user" :title="msg.user" class="w-8 h-8 rounded-full object-cover"/>
                    <span class="ml-2">{{ msg.user }}</span>
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700">
                    <span class="font-bold cursor-pointer" @click="ShowMessage(msg)">{{ msg.subject }}</span>
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700 text-center pr-2">
                    <editbtns table="private_messages" :pm="true" :id="msg.id" :noedit="true"></editbtns>
                  </td>
                </tr>
                <tr v-if="paginatedOutbox.length === 0">
                  <td colspan="4" class="text-center py-4 text-gray-500 dark:text-gray-300">Keine Nachrichten gefunden</td>
                </tr>
              </tbody>
            </table>

            <!-- Buttons Outbox -->
            <div class="mt-4 flex gap-2">
              <button
                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                @click="deleteMessages('outbox')"
                :disabled="selectedOutbox.length === 0"
              >
                Löschen
              </button>
            </div>

            <!-- Pagination Outbox -->
            <div class="mt-4 flex justify-center space-x-1">
              <button
                v-for="page in outboxTotalPages"
                :key="page"
                @click="currentPageOutbox = page"
                :class="['px-3 py-1 rounded', currentPageOutbox === page ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300']"
              >
                {{ page }}
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </Layout>
</template>

<script>
import Layout from "@/Application/Admin/Shared/Layout.vue";
import editbtns from '@/Application/Components/Form/editbtns.vue';
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import { SD, GetProfileImagePath, rumLaut, nl2br } from "@/helpers";
import InputSelectU from "@/Application/Components/Form/InputSelectU.vue";
import InputHtml from "@/Application/Components/Form/InputHtml.vue";
import InputFormText from "@/Application/Components/Form/InputFormText.vue";
import InputCheckbox from "@/Application/Components/Form/InputCheckbox.vue";
import axios from "axios";

import { route } from 'ziggy-js';

export default {
  components: {
    Layout,
    Breadcrumb,
    MetaHeader,
    editbtns,
    InputSelectU,
    InputCheckbox,
    InputHtml,
    InputFormText,
  },

  props: {
    inboxArr: { type: Array, default: () => [] },
    outboxArr: { type: Array, default: () => [] },
  },

  data() {
    return {
      tab: "inbox",
      message: "",
      subject: "",
      to_id: null,
      UID: window?.Laravel?.userId || null,
      selectedMessage: null,
      searchQuery: "",
      perPage: 10,
      currentPageInbox: 1,
      currentPageOutbox: 1,
      selectAllInbox: false,
      selectAllOutbox: false,
      selectedInbox: [],
      selectedOutbox: [],
      tabs: [
        { id: "inbox", label: "Inbox", icon: "📥" },
        { id: "outbox", label: "Outbox", icon: "📤" },
        { id: "new", label: "Neue Nachricht", icon: "✉️" },
        { id: "settings", label: "Einstellungen", icon: "⚙️" },
      ],
      breadcrumbs: [
        { name: "Home", link: "/" },
        { name: "Admin", link: "/admin" },
        { name: "Private Nachrichten" },
      ],
    };
  },

  computed: {
    filteredInbox() {
      if (!this.searchQuery) return this.inboxArr;
      const q = this.searchQuery.toLowerCase();
      return this.inboxArr.filter(msg => msg.user.toLowerCase().includes(q) || msg.subject.toLowerCase().includes(q));
    },
    paginatedInbox() {
      const start = (this.currentPageInbox - 1) * this.perPage;
      return this.filteredInbox.slice(start, start + this.perPage);
    },
    inboxTotalPages() {
      return Math.ceil(this.filteredInbox.length / this.perPage) || 1;
    },

    filteredOutbox() {
      if (!this.searchQuery) return this.outboxArr;
      const q = this.searchQuery.toLowerCase();
      return this.outboxArr.filter(msg => msg.user.toLowerCase().includes(q) || msg.subject.toLowerCase().includes(q));
    },
    paginatedOutbox() {
      const start = (this.currentPageOutbox - 1) * this.perPage;
      return this.filteredOutbox.slice(start, start + this.perPage);
    },
    outboxTotalPages() {
      return Math.ceil(this.filteredOutbox.length / this.perPage) || 1;
    },
  },

  watch: {
    selectedInbox: {
      handler(newVal) {
        // Update selectAllInbox basierend auf aktueller Seite
        this.selectAllInbox = this.paginatedInbox.length > 0 &&
          this.paginatedInbox.every(msg => newVal.includes(msg.id));
      },
      deep: true
    },
    selectedOutbox: {
      handler(newVal) {
        // Update selectAllOutbox basierend auf aktueller Seite
        this.selectAllOutbox = this.paginatedOutbox.length > 0 &&
          this.paginatedOutbox.every(msg => newVal.includes(msg.id));
      },
      deep: true
    },
    tab() {
      this.resetPage();
      this.clearSelections();
    },
    searchQuery() {
      this.resetPage();
      this.clearSelections();
    },
    paginatedInbox() {
      // Reset selectAll wenn sich die Seite ändert
      this.selectAllInbox = false;
    },
    paginatedOutbox() {
      // Reset selectAll wenn sich die Seite ändert
      this.selectAllOutbox = false;
    }
  },

  methods: {
    SD,
    rumLaut,
    nl2br,
    GetProfileImagePath,

    // Select-All für Inbox/Outbox
    toggleSelectAll(tab) {
      if (tab === "inbox") {
        if (this.selectAllInbox) {
          // Füge alle Nachrichten der aktuellen Seite hinzu
          const pageIds = this.paginatedInbox.map(msg => msg.id);
          this.selectedInbox = [...new Set([...this.selectedInbox, ...pageIds])];
        } else {
          // Entferne alle Nachrichten der aktuellen Seite
          const pageIds = this.paginatedInbox.map(msg => msg.id);
          this.selectedInbox = this.selectedInbox.filter(id => !pageIds.includes(id));
        }
      } else if (tab === "outbox") {
        if (this.selectAllOutbox) {
          // Füge alle Nachrichten der aktuellen Seite hinzu
          const pageIds = this.paginatedOutbox.map(msg => msg.id);
          this.selectedOutbox = [...new Set([...this.selectedOutbox, ...pageIds])];
        } else {
          // Entferne alle Nachrichten der aktuellen Seite
          const pageIds = this.paginatedOutbox.map(msg => msg.id);
          this.selectedOutbox = this.selectedOutbox.filter(id => !pageIds.includes(id));
        }
      }
    },

    clearSelections() {
      this.selectedInbox = [];
      this.selectedOutbox = [];
      this.selectAllInbox = false;
      this.selectAllOutbox = false;
    },

    markAsRead() {
      if (!this.selectedInbox.length) return;
      axios.post(route('pm.markAsRead'), { ids: this.selectedInbox })
        .then(() => {
          this.selectedInbox = [];
          this.selectAllInbox = false;
          this.$inertia.reload({ only: ['inboxArr'] });
        });
    },

    deleteMessages(tab) {
      const ids = tab === 'inbox' ? this.selectedInbox : this.selectedOutbox;
      if (!ids.length) return;
      if (!confirm('Sind Sie sicher, dass Sie die Nachricht(en) löschen möchten?')) return;
      axios.post(route('pm.delete'), { ids })
        .then(() => {
          if (tab === 'inbox') this.selectedInbox = [];
          else this.selectedOutbox = [];
          this.selectAllInbox = false;
          this.selectAllOutbox = false;
          this.$inertia.reload({ only: ['inboxArr', 'outboxArr'] });
        });
    },

    resetPage() {
      if (this.tab === "inbox") this.currentPageInbox = 1;
      else if (this.tab === "outbox") this.currentPageOutbox = 1;
    },

    ShowMessage(msg) {
      this.selectedMessage = msg;
      const exists = this.tabs.find(t => t.id === "read");
      if (!exists) {
        this.tabs.unshift({ id: "read", label: "Nachricht lesen", icon: "📖" });
      }
      this.tab = "read";
      this.checkMessageRead();
    },

    checkMessageRead() {
      if (!this.selectedMessage?.id) return;
      axios.post(route('admin.pm.check', this.selectedMessage.id))
        .then(() => {
          this.$inertia.reload({
            only: ['inboxArr', 'outboxArr'],
            preserveScroll: true,
            onFinish: () => { this.tab = 'read'; }
          });
        })
        .catch(err => console.error(err));
    },

    sendMessage() {
      if (!this.message.trim()) {
        alert("Bitte gib eine Nachricht ein.");
        return;
      }
      axios.post(route('pm.save'), {
        message: this.message,
        to_id: this.to_id,
        subject: this.subject,
      })
        .then(() => {
          this.message = "";
          this.subject = '';
          this.to_id = null;
          this.tab = "outbox";
          this.$inertia.reload({ only: ['inboxArr', 'outboxArr'] });
        })
        .catch(err => console.error('Fehler beim Speichern:', err));
    },

    answer(msg) {
      if (!msg) return;
      this.tab = "new";
      this.to_id = msg.users_id || msg.user_id || null;
      const subj = msg.subject || msg.title || "";
      this.subject = subj.startsWith("Re:") ? subj : "Re: " + subj;
      const originalMessage = msg.message || msg.body || "";
      const quoted = originalMessage.split("\n").map(line => `> ${line}`).join("\n") || "> ";
      this.message = `<blockquote>${quoted}</blockquote>\n\n`;
      this.$nextTick(() => {
        const textarea = this.$el.querySelector("textarea");
        if (textarea) textarea.focus();
      });
    },

    rewrite(msg) {
      if (!msg) return;
      this.tab = "new";
      this.to_id = msg.to_id || msg.users_id || null;
      this.subject = "";
      this.message = "";
      this.$nextTick(() => {
        const textarea = this.$el.querySelector("textarea");
        if (textarea) textarea.focus();
      });
    },

    formatDate(dateStr) {
      const date = new Date(dateStr);
      return date.toLocaleString();
    },

    updateFormData(value) {
      this.to_id = value;
    },
  },
};
</script>


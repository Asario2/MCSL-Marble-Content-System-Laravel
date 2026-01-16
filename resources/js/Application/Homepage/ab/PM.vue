<template>
  <Layout>
    <template #header>
      <breadcrumb :breadcrumbs="breadcrumbs" :current="'Private_Nachrichten'"></breadcrumb>
    </template>
    <MetaHeader title="Private Nachrichten" />

    <div class="max-w-none bg-layout-sun-100 dark:bg-layout-night-100 p-7 rounded-2xl shadow">

      <!-- Tabs -->
        <div class="flex border-b border-gray-300 dark:border-gray-700 mb-6 overflow-x-auto">
            <button
            v-for="tabItem in tabs"
            :key="tabItem.id"
            @click="tab = tabItem.id"
            :class="[
                'flex-1 text-center p-3 border-b-2 font-medium transition',
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
          <h2 class="text-2xl font-semibold m-3 pt-5">📥 Posteingang</h2>

          <input
            type="text"
            v-model="searchInbox"
            placeholder="Suchen..."
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 dark:bg-gray-700 dark:text-white mb-4"
          />

          <div class="p-6 bg-layout-sun-100 dark:bg-layout-night-100 rounded-xl overflow-x-auto w-full">
            <table class="min-w-full text-left border-collapse border border-gray-300 dark:border-gray-700">
              <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                  <th class="border border-gray-300 dark:border-gray-700 text-center pl-3" width="56">
                    <InputCheckbox
                      v-model="selectAllInbox"
                      @update:modelValue="toggleSelectAll('inbox', $event)"
                      name="select_all_inbox"
                    />
                  </th>
                  <th class="border border-gray-300 dark:border-gray-700 text-center" width="56">Gelesen</th>
                  <th class="border border-gray-300 dark:border-gray-700 text-center min-w-[120px]" width="15%">Von</th>
                  <th class="border border-gray-300 dark:border-gray-700">Betreff</th>
                  <th class="border border-gray-300 dark:border-gray-700"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(msg, index) in paginatedInbox" :key="msg.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="border border-gray-300 dark:border-gray-700 text-center pl-3">
                    <InputCheckbox
                      :model-value="(selectedInbox[msg.id] === 1) ? 1 : 0"
                      @update:modelValue="val => setSelected('inbox', msg.id, val)"
                      :name="'inbox_' + msg.id"
                      :id="'inbox_' + msg.id"
                    />
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700 text-center">
                  <PublishButton
                    table="private_messages"
                    :id="msg.id"
                    public='1'
                    :published="msg.checked === 1"
                />
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700 flex items-center">
                    <img :src="GetProfileImagePath(msg.avatar)" :alt="msg.user" :title="msg.user" class="w-8 h-8 rounded-full object-cover"/>
                    <span class="ml-2">{{ msg.user }}</span>
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700">
                    <span class="font-bold cursor-pointer" @click="ShowMessage(msg)">{{ rumLaut(msg.subject) }}</span>
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
          </div>

          <!-- Buttons Inbox -->
          <div class="mt-4 flex gap-2">
            <button
              class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
              @click="markAsRead"
              :disabled="selectedInboxIds.length === 0"
            >
              Als gelesen markieren
            </button>
            <button
              class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
              @click="deleteMessages('inbox')"
              :disabled="selectedInboxIds.length === 0"
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

        <!-- Outbox -->
        <div v-else-if="tab === 'outbox'" class="mt-[-20px] pb-3">
          <h2 class="text-2xl font-semibold m-3 pt-5">📤 Gesendete Nachrichten</h2>

          <input
            type="text"
            v-model="searchOutbox"
            placeholder="Suchen..."
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300 dark:bg-gray-700 dark:text-white mb-4"
          />

          <div class="p-6 bg-layout-sun-100 dark:bg-layout-night-100 rounded-xl overflow-x-auto w-full">
            <table class="min-w-full text-left border-collapse border border-gray-300 dark:border-gray-700">
              <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                  <th class="border border-gray-300 dark:border-gray-700 text-center pl-3" width="56">
                    <InputCheckbox
                      v-model="selectAllOutbox"
                      @update:modelValue="toggleSelectAll('outbox', $event)"
                      name="select_all_outbox"
                    />
                  </th>
                  <th class="border border-gray-300 dark:border-gray-700 text-center" width="15%">An</th>
                  <th class="border border-gray-300 dark:border-gray-700">Betreff</th>
                  <th class="border border-gray-300 dark:border-gray-700"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(msg) in paginatedOutbox" :key="msg.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="border border-gray-300 dark:border-gray-700 text-center pl-3">
                    <InputCheckbox
                      :model-value="(selectedOutbox[msg.id] === 1) ? 1 : 0"
                      @update:modelValue="val => setSelected('outbox', msg.id, val)"
                      :name="'outbox_' + msg.id"
                      :id="'outbox_' + msg.id"
                    />
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700 flex items-center">
                    <img :src="GetProfileImagePath(msg.avatar)" :alt="msg.user" :title="msg.user" class="w-8 h-8 rounded-full object-cover"/>
                    <span class="ml-2">{{ msg.user }}</span>
                  </td>
                  <td class="border border-gray-300 dark:border-gray-700">
                    <span class="font-bold cursor-pointer" @click="ShowMessage(msg)">{{ rumLaut(msg.subject) }}</span>
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
          </div>

          <!-- Buttons Outbox -->
          <div class="mt-4 flex gap-2">
            <button
              class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
              @click="deleteMessages('outbox')"
              :disabled="selectedOutboxIds.length === 0"
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

        <!-- Nachricht lesen -->
        <div v-else-if="tab === 'read' && selectedMessage" class="mt-[-20px] pb-3">
          <h2 class="text-2xl font-semibold m-3 pt-5">📖 Nachricht lesen</h2>

          <div class="p-6 bg-layout-sun-100 dark:bg-layout-night-100 rounded-xl flex flex-col md:flex-row gap-6">
            <!-- Linker Bereich: Nachricht -->
            <div class="flex-1 max-w-[70%]">
              <!-- KORRIGIERT: Vereinfachte Bedingungen für Antworten -->
              <p v-if="selectedMessage.public=='1' && selectedMessage.users_id != '4'"
                @click="answer(selectedMessage)"
                class="cursor-pointer font-bold text-[#58aaf8] hover:text-[#7cc0ff] transition mb-2"
              >
                ➡️ Antworten
              </p>
              <p v-if="selectedMessage.public == '2' && selectedMessage.users_id != '4'"
                @click="rewrite(selectedMessage)"
                class="cursor-pointer font-bold text-[#58aaf8] hover:text-[#7cc0ff] transition mb-2"
              >
                ➡️ Neue Nachricht an {{ selectedMessage.user }}
              </p>

              <p><strong>Von:</strong> {{ selectedMessage.user }}</p>
              <p><strong>Betreff:</strong> {{ selectedMessage.subject }}</p>
              <p><strong>Datum:</strong> {{ formatDate(selectedMessage.created_at) }}</p>
              <hr class="my-3 border-gray-300 dark:border-gray-600"/>
              <span v-html="nl2br(rumLaut(nl2br(selectedMessage.message)))"></span>
            </div>

            <!-- Rechter Bereich: Benutzerinfos -->
            <div class="w-full md:w-64 flex-shrink-0 bg-gray-100 dark:bg-gray-800 p-4 rounded-xl text-center self-start">

              <a :href="'/home/users/show/' + selectedMessage.user + '/' + selectedMessage.users_id">
                <img :src="GetProfileImagePath(selectedMessage.avatar || 'default.jpg')" class="mx-auto w-24 h-24 rounded-full object-cover mb-2" />
              </a>
              <h4 class="font-semibold mb-1">Über {{ selectedMessage.user }}</h4>
              <p>Vorname: {{ selectedMessage.first_name }}</p>
              <p>Letzter Login:<br /> {{ formatDate(selectedMessage.lastlogin) }}</p>
              <p>Alter: {{ selectedMessage.age || '-' }}</p>
              <p>Website: <span v-if="selectedMessage.website"><a :href="selectedMessage.website">{{selectedMessage.website}}</a></span><span v-else>Keine</span></p>
            </div>
          </div>
        </div>

        <!-- Neue Nachricht -->
        <div v-else-if="tab === 'new'" class="mt-[-20px] pb-3">
          <h2 class="text-2xl font-semibold m-3 pt-5">✉️ Neue Nachricht</h2>
          <InputSelectU
            width="100%"
            @input-change="updateFormData"
            id="to_id"
            v-model="to_id"
            :owner="UID"
            name="users_id"
            xname="to_id"
            :required="true"
          />
          <InputFormText
            v-model="subject"
            label="Betreff"
            id="subject"
            name="subject"
            :required="true"
          >
            <template #label>Betreff</template>
          </InputFormText>

          <InputHtml
            :modelValue="message"
            @update:modelValue="message = $event"
            class="w-full border dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900 dark:text-gray-200"
            placeholder="Schreibe eine Nachricht..."
            rows="4"
            name="message"
          ></InputHtml>
          <button
            class="mt-3 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition"
            @click="sendMessage"
          >
            Senden
          </button>
        </div>

        <!-- Einstellungen -->
        <div v-else-if="tab === 'settings'" class="mt-[-20px] pb-3">
          <h2 class="text-2xl font-semibold m-3 pt-5">⚙️ Einstellungen</h2>
          <div class="py-3 border-b border-gray-200 dark:border-gray-700">
            <MessageSettings :nohead="true">, siehe auch <a href='/admin/profile'>Profil</a></MessageSettings>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
import editbtns from '@/Application/Components/Form/editbtns.vue';
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import MessageSettings from "@/Application/Shared/MessageSettings.vue";
import PublishButton from "@/Application/Components/Form/PublishButton.vue";
import { SD, GetProfileImagePath,rumLaut,nl2br, GetSettings } from "@/helpers";
import InputSelectU from "@/Application/Components/Form/InputSelectU.vue";
import InputHtml from "@/Application/Components/Form/InputHtml.vue";
import InputCheckbox from "@/Application/Components/Form/InputCheckbox.vue";
import axios from "axios";
import {route} from 'ziggy-js';
import InputFormText from "@/Application/Components/Form/InputFormText.vue";

export default {
  components: {
    Layout,
    InputHtml,
    MetaHeader,
    editbtns,
    Breadcrumb,
    InputSelectU,
    InputFormText,
    PublishButton,
    InputCheckbox,
    MessageSettings,
  },

  props: {
    inboxArr: { type: Array, default: () => [] },
    outboxArr: { type: Array, default: () => [] },
    form: { type: Array, default: () => [] },
    settings: {type: [Array,Object], default: () => [] },
  },

  data() {
    return {
      tab: "inbox",
      message: "",
      subject: "",
      to_id: null,
      tabs: [
        { id: "inbox", label: "Inbox", icon: "📥" },
        { id: "outbox", label: "Outbox", icon: "📤" },
        { id: "new", label: "Neue Nachricht", icon: "✉️" },
        { id: "settings", label: "Einstellungen", icon: "⚙️" },
      ],
      searchQuery: '',
      perPage: this.form[0]?.cnt_numrows || 10,
      currentPageInbox: 1,
      currentPageOutbox: 1,
      selectedMessage: null,
      UID: window?.Laravel?.userId || null,
      searchInbox: '',
      searchOutbox: '',
      selectAllInbox: 0,
      selectAllOutbox: 0,
      selectedInbox: {},
      selectedOutbox: {},
    };
  },

  computed: {
    filteredInbox() {
      if (!this.searchInbox) return this.inboxArr;
      const q = this.searchInbox.toLowerCase();
      return this.inboxArr.filter(msg =>
        msg.user.toLowerCase().includes(q) ||
        msg.subject.toLowerCase().includes(q)
      );
    },
    paginatedInbox() {
      const start = (this.currentPageInbox - 1) * this.perPage;
      return this.filteredInbox.slice(start, start + this.perPage);
    },
    inboxTotalPages() {
      return Math.ceil(this.filteredInbox.length / this.perPage) || 1;
    },
    selectedInboxIds() {
      return Object.keys(this.selectedInbox)
        .filter(id => this.selectedInbox[id])
        .map(id => parseInt(id));
    },

    filteredOutbox() {
      if (!this.searchOutbox) return this.outboxArr;
      const q = this.searchOutbox.toLowerCase();
      return this.outboxArr.filter(msg =>
        msg.user.toLowerCase().includes(q) ||
        msg.subject.toLowerCase().includes(q)
      );
    },
    paginatedOutbox() {
      const start = (this.currentPageOutbox - 1) * this.perPage;
      return this.filteredOutbox.slice(start, start + this.perPage);
    },
    outboxTotalPages() {
      return Math.ceil(this.filteredOutbox.length / this.perPage) || 1;
    },
    selectedOutboxIds() {
      return Object.keys(this.selectedOutbox)
        .filter(id => this.selectedOutbox[id] === 1)
        .map(id => parseInt(id));
    },
  },

  async mounted() {
     const settings = await GetSettings();
  },

  methods: {
    SD,
    rumLaut,
    nl2br,
    GetProfileImagePath,
    GetSettings,

    setSelected(box, id, val) {
      if (box === 'inbox') {
        this.selectedInbox[id] = val;
      } else if (box === 'outbox') {
        this.selectedOutbox[id] = val;
      }
    },

    toggleSelectAll(tab, value) {
      if (tab === 'inbox') {
        this.selectAllInbox = value;
        this.paginatedInbox.forEach(msg => {
          this.selectedInbox[msg.id] = value ? 1 : 0;
        });
      } else if (tab === 'outbox') {
        this.selectAllOutbox = value;
        this.paginatedOutbox.forEach(msg => {
          this.selectedOutbox[msg.id] = value ? 1 : 0;
        });
      }
    },

    markAsRead() {
      if (this.selectedInboxIds.length === 0) {
        alert('Keine Nachrichten ausgewählt.');
        return;
      }

      axios.post(route('admin.pm.mark'), { ids: this.selectedInboxIds.join(',') })
        .then(() => {
          this.selectedInboxIds.forEach(id => {
            this.selectedInbox[id] = 0;
          });
          this.selectAllInbox = 0;
          this.$inertia.reload({ only: ['inboxArr'] });
        })
        .catch(err => {
          console.error('Fehler beim Markieren als gelesen:', err);
          alert('Fehler beim Markieren der Nachrichten.');
        });
    },

    deleteMessages(tab) {
      const ids = tab === 'inbox'
        ? this.selectedInboxIds
        : this.selectedOutboxIds;

      if (ids.length === 0) {
        alert('Keine Nachrichten ausgewählt.');
        return;
      }

      if (!confirm(`Sind Sie sicher, dass Sie ${ids.length} Nachricht(en) löschen möchten?`)) return;

      const idsStr = ids.join(',');

      axios.post(route('admin.pm.delmore'), { ids: idsStr })
        .then(() => {
          if (tab === 'inbox') {
            ids.forEach(id => { this.selectedInbox[id] = 0; });
            this.selectAllInbox = 0;
          } else {
            ids.forEach(id => { this.selectedOutbox[id] = 0; });
            this.selectAllOutbox = 0;
          }

          this.$inertia.reload({ only: ['inboxArr', 'outboxArr'] });
        })
        .catch(err => {
          console.error('Fehler beim Löschen:', err);
          alert('Fehler beim Löschen der Nachrichten.');
        });
    },

    checkAll() {
      if (!this.selectedMessage?.id) return;

      axios.post(route('admin.pm.check', this.selectedMessage.id))
        .then(() => {
          this.$inertia.reload({
            only: ['inboxArr', 'outboxArr'],
            preserveScroll: true,
            onFinish: () => {
              this.tab = 'read';
            }
          });
        })
        .catch(err => console.error(err));
    },

    resetPage() {
      if (this.tab === "inbox") {
        this.currentPageInbox = 1;
        this.selectAllInbox = 0;
      } else if (this.tab === "outbox") {
        this.currentPageOutbox = 1;
        this.selectAllOutbox = 0;
      }
    },

    answer(msg) {
      console.log('Answer function called with message:', msg);

      if (!msg) {
        console.error('No message provided for answer');
        return;
      }

      // Zur "Neue Nachricht" Tab wechseln
      this.tab = "new";

      // Empfänger-ID setzen - verschiedene mögliche Felder prüfen
      this.to_id = msg.users_id || msg.user_id || msg.from_id || msg.UID;
      console.log('Set to_id:', this.to_id, 'from message fields:', {
        users_id: msg.users_id,
        user_id: msg.user_id,
        from_id: msg.from_id,
        UID: msg.UID
      });

      // Betreff vorbereiten
      const subj = msg.subject || "";
      this.subject = subj.startsWith("Re:") ? subj : "Re: " + subj;
      console.log('Set subject:', this.subject);

      // Nachricht vorbereiten
      let original = msg.message || "";
      console.log('Original message:', original);

      // HTML zu Text konvertieren (vereinfacht)
      if (original.includes('<') || original.includes('>')) {
        // Einfache HTML-Bereinigung
        original = original
          .replace(/<br\s*\/?>/gi, "\n")
          .replace(/<\/p>/gi, "\n\n")
          .replace(/<[^>]*>/g, "")
          .trim();
      }

      console.log('Cleaned message:', original);

      // Zitat erstellen
      const quoted = original
        .split("\n")
        .filter(line => line.trim()) // Leere Zeilen entfernen
        .map(line => `> ${line.trim()}`)
        .join("\n");

      this.message = `<blockquote>${quoted}</blockquote><br><br>`;
      console.log('Final message set');

      // Kurze Verzögerung für UI-Update
      this.$nextTick(() => {
        console.log('UI updated, ready for input');
      });
    },

    rewrite(msg) {
      console.log('Rewrite function called with message:', msg);

      if (!msg) return;
      this.tab = "new";
      this.to_id = msg.users_id || msg.user_id || msg.from_id || msg.UID;
      this.subject = "";
      this.message = "";

      console.log('Rewrite completed:', { to_id: this.to_id });
    },

    sendMessage() {
      console.log('Sending message:', {
        to_id: this.to_id,
        subject: this.subject,
        message: document.getElementById("editor_message").innerHTML,
        message_length: this.message.length
      });
      this.message = document.getElementById("editor_message").innerHTML;
      if (!this.message.trim()) {
        alert("Bitte gib eine Nachricht ein.");
        return;
      }

      if (!this.to_id) {
        alert("Bitte wählen Sie einen Empfänger aus.");
        return;
      }

      if (!this.subject.trim()) {
        alert("Bitte geben Sie einen Betreff ein.");
        return;
      }

      axios.post(route('pm.save'), {
        message: document.getElementById("editor_message").innerHTML,
        to_id: this.to_id,
        subject: this.subject,
      })
      .then(response => {
        console.log('Gespeichert:', response.data);
        this.message = "";
        this.subject = '';
        this.to_id = null;
        this.tab = "outbox";
        this.$inertia.reload();
      })
      .catch(error => {
        console.error('Fehler beim Speichern:', error);
        alert('Fehler beim Senden der Nachricht: ' + error.message);
      });
    },

    ShowMessage(msg) {
      console.log('ShowMessage called with:', msg);
      this.selectedMessage = msg;

      const exists = this.tabs.find(t => t.id === "read");
      if (!exists) {
        this.tabs.unshift({ id: "read", label: "Nachricht lesen", icon: "📖" });
      }

      this.tab = "read";
      this.checkAll();
    },

    formatDate(dateStr) {
      const date = new Date(dateStr);
      return date.toLocaleString();
    },

    updateFormData(value) {
      this.to_id = value;
      console.log('Form data updated - to_id:', value);
    },
  },

  watch: {
    tab(newTab) {
      console.log('Tab changed to:', newTab);
      this.searchInbox = '';
      this.searchOutbox = '';
      this.resetPage();
    },
    searchQuery() { this.resetPage(); },

    form: {
      handler(newVal) {
        if (newVal && newVal[0] && newVal[0].cnt_numrows) {
          this.perPage = parseInt(newVal[0].cnt_numrows);
          this.resetPage();
        }
      },
      deep: true,
      immediate: true
    }
  }
};
</script>

<style>
.w-fully{
    min-width:100% !important;
    max-width:100% !important;
}
.tw-row {
    @apply grid grid-cols-4 gap-4 place-items-center;
}

.tw-col {
    @apply flex items-center justify-center gap-2;
}
</style>

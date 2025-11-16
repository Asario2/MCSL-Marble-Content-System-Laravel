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
            v-model="searchQuery"
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
                    <span
                      class="inline-block w-5 h-5 rounded-full"
                      :class="msg.checked ? 'bg-green-500' : 'bg-red-500'"
                    ></span>
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
              @click="changePage(page)"
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
            v-model="searchQuery"
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
              @click="changePage(page)"
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
              <p v-if="selectedMessage.public == '1' && selectedMessage.UID !='4'" @click="answer(selectedMessage)" class="cursor-pointer font-bold text-[#58aaf8] hover:text-[#7cc0ff] transition"> ➡️ Antworten</p>
              <p v-if="selectedMessage.public == '2'" @click="rewrite(selectedMessage)" class="cursor-pointer font-bold text-[#58aaf8] hover:text-[#7cc0ff] transition">➡️ Neue Nachricht an {{ selectedMessage.user }}</p>
              <p><strong>Von:</strong> {{ selectedMessage.user }}</p>
              <p><strong>Betreff:</strong> {{ selectedMessage.subject }}</p>
              <p><strong>Datum:</strong> {{ formatDate(selectedMessage.created_at) }}</p>
              <hr class="my-3 border-gray-300 dark:border-gray-600"/>
              <span v-html="nl2br(rumLaut(nl2br(selectedMessage.message)))"></span>
            </div>

            <!-- Rechter Bereich: Benutzerinfos -->
            <div class="w-full md:w-64 flex-shrink-0 bg-gray-100 dark:bg-gray-800 p-4 rounded-xl text-center">
              <a :href="'/home/users/show/' + selectedMessage.user + '/' + selectedMessage.users_id"><img :src="GetProfileImagePath(selectedMessage.avatar || 'default.jpg')" class="mx-auto w-24 h-24 rounded-full object-cover mb-2" />
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
          <InputSelectU width="100%"
           @input-change="updateFormData"
                        id="to_id"
                        v-model="to_id"
                        :owner="UID"
                        name="users_id"
                        xname="to_id"
                        :required="true"
                    />
            <InputFormText v-model="subject" label="Betreff" id="subject" name="subject" :required="true">
                <template #label>Betreff</template>
            </InputFormText>

          <InputHtml
            v-model="message"
            class="w-full border dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900 dark:text-gray-200"
            placeholder="Schreibe eine Nachricht..."
            rows="4"
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



    <!-- Immer 4 Radios in einer Zeile -->
<!--     <div class="tw-row">
      <label class="tw-col cursor-pointer">
        <input
          type="radio"
          name="xch_newsletter"
          value="0"
          v-model="form[0].xch_newsletter"
          class="w-5 h-5 rounded-full border cursor-pointer
    bg-primary-sun-50 text-primary-sun-500 border-primary-sun-300
    focus:ring-3 focus:ring-primary-sun-300 ring-offset-primary-sun-800
    checked:bg-primary-sun-500 cursor-pointer
    dark:bg-primary-night-50 dark:text-primary-night-500
    dark:border-primary-night-300 dark:focus:ring-primary-night-300
    dark:ring-offset-primary-night-800 dark:checked:bg-primary-night-500"
        />
        <span>Nein</span>
      </label>

      <label class="tw-col cursor-pointer">
        <input
          type="radio"
          name="xch_newsletter"
          value="to_mail"
          v-model="form[0].xch_newsletter"
          class="w-5 h-5 rounded-full border cursor-pointer
    bg-primary-sun-50 text-primary-sun-500 border-primary-sun-300
    focus:ring-3 focus:ring-primary-sun-300 ring-offset-primary-sun-800
    checked:bg-primary-sun-500
    dark:bg-primary-night-50 dark:text-primary-night-500
    dark:border-primary-night-300 dark:focus:ring-primary-night-300
    dark:ring-offset-primary-night-800 dark:checked:bg-primary-night-500"
        />
        <span>Per Email</span>
      </label>

      <label class="tw-col cursor-pointer">
        <input
          type="radio"
          name="xch_newsletter"
          value="to_pm"
          v-model="form[0].xch_newsletter"
          class="w-5 h-5 rounded-full border cursor-pointer
    bg-primary-sun-50 text-primary-sun-500 border-primary-sun-300
    focus:ring-3 focus:ring-primary-sun-300 ring-offset-primary-sun-800
    checked:bg-primary-sun-500
    dark:bg-primary-night-50 dark:text-primary-night-500
    dark:border-primary-night-300 dark:focus:ring-primary-night-300
    dark:ring-offset-primary-night-800 dark:checked:bg-primary-night-500"
        />
        <span>Per Privater Nachricht</span>
      </label>

      <label class="tw-col cursor-pointer">
        <input
          type="radio"
          name="xch_newsletter"
          value="to_pm_and_mail"
          v-model="form[0].xch_newsletter"
          class="w-5 h-5 rounded-full border cursor-pointer
            bg-primary-sun-50 text-primary-sun-500 border-primary-sun-300
            focus:ring-3 focus:ring-primary-sun-300 ring-offset-primary-sun-800
            checked:bg-primary-sun-500 checked:ring-white
            dark:bg-primary-night-50 dark:text-primary-night-500
            dark:border-primary-night-300
            dark:ring-offset-primary-night-800 dark:checked:bg-primary-night-500 dark:checked:ring-white"
        />
        <span>Per Email & Private Nachricht</span>
      </label>
      </div>-->
      <!-- <RadioSet
  name="newsletter"
  :options="[
    { label: 'Nein', value: '0' },
    { label: 'Per Mail', value: 'to_mail' },
    { label: 'Per Privater Nachricht', value: 'to_pm' },
    { label: 'Per Mail & Private Nachrichten', value: 'to_pm_and_mail' },
  ]"
  v-model="form[0].xch_newsletter"
/> -->
      <MessageSettings :nohead="true">, siehe auch <a href='/admin/profile'>Profil</a></MessageSettings>
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
import MessageSettings from "@/Application/Shared/MessageSettings.vue";
import { SD, GetProfileImagePath,rumLaut,nl2br } from "@/helpers";
import InputSelectU from "@/Application/Components/Form/InputSelectU.vue";
// import RadioSet from "@/Application/Components/Form/RadioSet.vue";
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
    InputCheckbox,
    // RadioSet,
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
      to_id: null,
      // Checkbox States - als Objekte für persistente Speicherung nach ID
      selectAllInbox: 0,
      selectAllOutbox: 0,
      selectedInbox: {},
      selectedOutbox: {},
      };
  },

  computed: {
    // Inbox
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
    selectedInboxIds() {
    return Object.keys(this.selectedInbox)
        .filter(id => this.selectedInbox[id])
        .map(id => parseInt(id));
    },

    // Outbox
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
    selectedOutboxIds() {
      return Object.keys(this.selectedOutbox)
        .filter(id => this.selectedOutbox[id] === 1)
        .map(id => parseInt(id));
    },
  },
mounted() {
  console.log('INBOX ARR:', this.inboxArr);
  console.log('SELECTED INBOX:', this.selectedInbox);
},

  methods: {
    SD,
    rumLaut,
    nl2br,
    GetProfileImagePath,
    setSelected(box, id, val) {
    if (box === 'inbox') {
        this.selectedInbox[id] = val;   // einfach direkt zuweisen
    } else if (box === 'outbox') {
        this.selectedOutbox[id] = val;  // direkt zuweisen
    }
    },
    // Checkbox Methods
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

    toggleSelectOne(tab, id) {
    if (tab === 'inbox') {
        if (this.selectedInbox[id] === undefined) this.selectedInbox[id] = 0; // 👈 Sicherstellen, dass Key existiert
        this.selectedInbox[id] = this.selectedInbox[id] ? 0 : 1;

        const allSelected = this.paginatedInbox.every(msg => this.selectedInbox[msg.id] === 1);
        this.selectAllInbox = allSelected ? 1 : 0;
    } else if (tab === 'outbox') {
        if (this.selectedOutbox[id] === undefined) this.selectedOutbox[id] = 0;
        this.selectedOutbox[id] = this.selectedOutbox[id] ? 0 : 1;

        const allSelected = this.paginatedOutbox.every(msg => this.selectedOutbox[msg.id] === 1);
        this.selectAllOutbox = allSelected ? 1 : 0;
    }
    },

    markAsRead() {
      if (this.selectedInboxIds.length === 0) {
        alert('Keine Nachrichten ausgewählt.');
        return;
      }

      axios.post(route('admin.pm.mark'), { ids: this.selectedInboxIds.join(',') })
        .then(() => {
          // Zurücksetzen der Auswahl
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

    // IDs als String zusammenfassen
    const idsStr = ids.join(',');

    axios.post(route('admin.pm.delmore'), { ids: idsStr })
        .then(() => {
        // Auswahl zurücksetzen
        if (tab === 'inbox') {
            ids.forEach(id => { this.selectedInbox[id] = 0; });
            this.selectAllInbox = 0;
        } else {
            ids.forEach(id => { this.selectedOutbox[id] = 0; });
            this.selectAllOutbox = 0;
        }

        // Inertia neu laden
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

    changePage(page) {
      if (this.tab === "inbox") this.currentPageInbox = page;
      else if (this.tab === "outbox") this.currentPageOutbox = page;
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
        });
    },

    ShowMessage(msg) {
      this.selectedMessage = msg;
      console.log('SELECTED MESSAGE:', msg);

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
    },
  },

watch: {
  // Bereits vorhandene watcher
  tab() { this.resetPage(); },
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


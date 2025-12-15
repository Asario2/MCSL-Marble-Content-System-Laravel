<template>
  <Layout>
    <template #header>
      <breadcrumb
        :application-name="$page.props.applications.app_admin_name"
        :start-page="false"
        current="SQL Updater"
      ></breadcrumb>
    </template>

    <div class="p-6 space-y-8">

      <!-- Header -->
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
          MySQL Synchronisation
        </h1>
        <div class="flex justify-between items-center py-2 px-4">
          <p class="text-gray-600 dark:text-gray-400">
            Zeigt alle Tabellen, Änderungen sind grün, keine Änderungen rot.
          </p>

          <div class="flex items-center gap-2">
            <select
              name="domain"
              v-model="domain"
              class="p-2.5 text-sm rounded-lg block border border-layout-sun-300 text-layout-sun-900 bg-layout-sun-50 placeholder-layout-sun-400 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:border-layout-night-300 dark:text-layout-night-900 dark:bg-layout-night-50 dark:placeholder-layout-night-400 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
            >
              <option value="ab">Asarios Blog</option>
              <option value="mfx">MarbleFX</option>
              <option value="dag">Monika Dargies</option>
            </select>

            <button
              @click="loadTables()"
              class="bg-blue-600 hover:bg-blue-700 text-white p-2.5 rounded"
            >
              Auswählen
            </button>
          </div>
        </div>
      </div>

      <!-- Sync-To-All Button -->
      <div>
        <div class="p-4 border-2 rounded-lg radius border-gray-300 w-full bg-gray-200 text-black dark:bg-gray-800 dark:text-white">
          <div class="flex justify-between items-center py-2 px-4">
            <button
              v-if="allTables.some(t => t.status_local === 'green') || allTables.some(t => t.status_online === 'green')"
              @click="syncToAll"
              :disabled="loading"
              class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow disabled:bg-gray-400"
            >
              🔁 Sync To All (Neue Version -> Alte Version)
            </button>
            <span v-else class="font-semibold text-green-600">Alles Up to Date</span>
            <span class="text-sm text-gray-500">Letztes Update: {{ cor_date }}</span>
          </div>
        </div>
      </div>

      <!-- Grid für Local/Online -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Local → Online -->
        <div class="p-4 rounded-xl bg-white dark:bg-gray-900 shadow border border-gray-200 dark:border-gray-700">
          <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-200">
            Local → Online
          </h2>

          <ul class="space-y-2">
            <li
              v-for="t in filteredTables"
              :key="t.name"
              :class="[
                'p-3 rounded-lg border flex justify-between items-center bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700',
                t.status_local === 'green' ? 'text-green-900 dark:text-green-400' : 'text-red-900 dark:text-red-400'
              ]"
            >
              <span @click="loadDiff(t)" class="cursor-pointer">{{ t.name }}</span>
              <span class="text-sm text-gray-500 dark:text-gray-400">{{ t.hash_local }}</span>
            </li>
          </ul>

          <button
            v-if="allTables.some(t => t.status_local === 'green')"
            @click="syncTables('local_to_online')"
            class="mt-4 w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition"
          >
            Sync Local → Online
          </button>
        </div>

        <!-- Online → Local -->
        <div class="p-4 rounded-xl bg-white dark:bg-gray-900 shadow border border-gray-200 dark:border-gray-700">
          <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-200">
            Online → Local
          </h2>

          <ul class="space-y-2">
            <li
              v-for="t in filteredTables"
              :key="t.name"
              :class="[
                'p-3 rounded-lg border flex justify-between items-center bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700',
                t.status_online === 'green' ? 'text-green-900 dark:text-green-400' : 'text-red-900 dark:text-red-400'
              ]"
            >
              <span @click="loadDiff(t)" class="cursor-pointer">{{ t.name }}</span>
              <span class="text-sm text-gray-500 dark:text-gray-400">{{ t.hash_online }}</span>
            </li>
          </ul>

          <button
            v-if="allTables.some(t => t.status_online === 'green')"
            @click="syncTables('online_to_local')"
            class="mt-4 w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition"
          >
            Sync Online → Local
          </button>
        </div>

      </div>

      <!-- Diff Panel -->
      <div
  v-if="diffData.length"
  class="mt-6 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-4"
>
  <h3 class="font-semibold text-lg mb-4">
    Diff für Tabelle: {{ selectedTable }}
  </h3>

<!-- Diff MODAL -->
<div
  v-if="diffData.length"
  class="fixed inset-0 z-[70] flex items-center justify-center bg-black/50"
>
  <!-- Modal Box -->
  <div class="w-full max-w-5xl mx-4 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 overflow-y-auto max-h-[90vh]">

    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-semibold text-lg">
        Diff für Tabelle: {{ selectedTable }}
      </h3>

      <button
        @click="diffData = []"
        class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-xl leading-none"
      >
        ✕
      </button>
    </div>

    <!-- Diff Inhalt -->
    <div
      v-for="row in diffData"
      :key="row.row"
      class="mb-8"
    >
      <p class="mb-2 font-semibold text-sm text-gray-600 dark:text-gray-400">
        Zeile {{ (row.row+1) }}
      </p>

      <table class="w-full border-collapse text-sm">
        <thead>
          <tr class="bg-gray-100 dark:bg-gray-800">
            <th class="px-3 py-2 text-left w-[25%]">Spalte</th>
            <th class="px-3 py-2 text-left w-[25%]">Offline</th>
            <th class="px-3 py-2 text-left w-[25%]">Online</th>
            <th class="px-3 py-2 text-left w-[25%]">Name</th>
          </tr>
        </thead>

        <tbody>
          <!-- Diff-Zeilen -->
          <tr
            v-for="(change, col) in row.changes"
            :key="col"
          >
            <td class="px-3 py-2 font-medium">
              {{ col }}
            </td>

            <td class="px-3 py-2">
              <pre>&quot;{{ change.local }}&quot;</pre>
            </td>

            <td class="px-3 py-2">
              <pre>&quot;{{ change.online }}&quot;</pre>
            </td>
            <td class="px-3 py-2">
                {{ row.name }}
            </td>
          </tr>

          <!-- Buttons (NUR EINMAL) -->


        </tbody>
      </table>

    </div>
<table class="w-full">
        <tr class="border-t">
            <td class="px-3 py-3 w-[25%]"></td>

            <td class="px-3 py-3 text-left w-[25%]">
              <button
                @click="syncDiff('local_to_online')"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm"
              >
                Offline → Online
              </button>
            </td>

            <td class="px-3 py-3 text-left w-[25%]">
              <button
                @click="syncDiff('online_to_local')"
                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded text-sm"
              >
                Online → Offline
              </button>
            </td>
          </tr>
      </table>
  </div>
</div>

      </div>

      <!-- Statusmeldung -->
      <div
        v-if="syncStatus"
        class="mt-4 p-3 bg-gray-100 dark:bg-gray-800 border rounded text-gray-700 dark:text-gray-200"
        v-html="syncStatus"
        >

      </div>

    </div>
  </Layout>
</template>

<script>
import axios from "axios";
import { SD } from "@/helpers";
import { defineAsyncComponent } from "vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";

export default {
  name: "SQLSyncTables",
  components: {
    Layout: defineAsyncComponent(() => import(`@/Application/Admin/Shared/${SD()}/Layout.vue`)),
    Breadcrumb,

  },
  props:{
    cor_date:String,
  },
  data() {
    return {
      localTables: [],
      onlineTables: [],
      diffData: [],
      showDiffModal: false,
      selectedTable: null,
      syncStatus: "",
      loading: false,
      domain: "ab",
        direct: {
      local_to_online: "<span class='text-blue-400 font-bold'> Local → Online </span",
      online_to_local: "<span class='text-purple-700 font-bold'>Online → Local</span>",
    },
    };
  },
  computed: {
    allTables() {
      if (!this.localTables || !this.onlineTables) return [];
      return this.localTables.map(t => {
        const online = this.onlineTables.find(o => o.name === t.name) || {};
        return {
          name: t.name,
          hash_local: t.hash ?? "-",
          hash_online: online.hash_online ?? "-",
          status_local: online.col_offline ?? "red",
          status_online: online.col_online ?? "red",
        };
      });
    },
    filteredTables() {
      return this.allTables.filter(t => t.hash_local !== t.hash_online);
    },
  },
  methods: {
    async loadTables() {
      try {
        const res = await axios.get("/api/mysqlops/tables/" + this.domain);
        this.localTables = res.data.local;
        this.onlineTables = res.data.online;
      } catch (e) {
        console.error("Fehler beim Laden:", e);
      }
    },

    async loadDiff(table) {
    if (!table || !table.name) return;

    // optional: nur wenn beide Seiten grün
    const tData = this.allTables.find(t => t.name === table.name);
    if (!tData || tData.status_local !== 'green' || tData.status_online !== 'green') {
        return;
    }

    this.selectedTable = table.name;

    try {
        const res = await axios.get(`/api/mysqlops/diff/${table.name}/${this.domain}`);
        this.diffData = res.data.diff || [];

        if (this.diffData.length) {
        this.showDiffModal = true; // 👈 MODAL AUF
        }
    } catch (e) {
        console.error("Fehler beim Laden des Diff:", e);
    }
    },

    async syncTables(direction) {
      try {
        const tablesToSync =
          direction === "local_to_online"
            ? this.allTables.filter(t => t.status_local === "green").map(t => t.name)
            : this.allTables.filter(t => t.status_online === "green").map(t => t.name);

        if (!tablesToSync.length) {
          this.syncStatus = "Keine Tabellen zum Sync vorhanden.";
          return;
        }

        await axios.post("/api/mysqlops/sync", { tables: tablesToSync, direction });

        this.syncStatus =
          direction === "local_to_online"
            ? "<span class='text-blue-400 font-bold'>Sync Local → Online</span> abgeschlossen!"
            : "<span class='text-purple-700 font-bold'>Sync Online → Local</span> abgeschlossen!";

        await this.loadTables();
      } catch (e) {
        console.error("Sync Fehler:", e);
        this.syncStatus = "Fehler beim Sync: " + e.message;
      }
    },

    async syncToAll() {
  this.loading = true;
  this.syncStatus = "Starte SyncToAll…";

  try {
    // Tabellen für Online → Local (Online grün, Local rot)
    const onlineToLocal = this.allTables
      .filter(t => t.status_online === "green" && t.status_local === "red")
      .map(t => t.name);

    if (onlineToLocal.length) {
      await axios.post("/api/mysqlops/sync", { tables: onlineToLocal, direction: "online_to_local" });
    }

    await this.loadTables();

    // Tabellen für Local → Online (Local grün, Online rot)
    const localToOnline = this.allTables
      .filter(t => t.status_local === "green" && t.status_online === "red")
      .map(t => t.name);

    if (localToOnline.length) {
      await axios.post("/api/mysqlops/sync", { tables: localToOnline, direction: "local_to_online" });
    }

    await this.loadTables();

    this.syncStatus = `SyncToAll abgeschlossen!<br />
    <span class='text-blue-400 font-bold'>Local→Online:</span> ${localToOnline.length} Tabellen.<br />
    <span class='text-purple-700 font-bold'>Online→Local:</span> ${onlineToLocal.length} Tabellen,<br />`;


    // -------------------------------
    // Neu: Tabellen bei beiden Seiten grün -> Diff laden
    // -------------------------------
    const greenTables = this.allTables.filter(t => t.status_local === 'green' && t.status_online === 'green');

    if (greenTables.length > 0) {
      // Optional: nur die erste Tabelle anzeigen
      await this.loadDiff(greenTables[0]);
      this.syncStatus = ` Diff-Vorschau für ${greenTables[0].name} angezeigt.`;
    }

  } catch (e) {
    console.error(e);
    this.syncStatus = "Fehler bei SyncToAll: " + e.message;
  }

  this.loading = false;
},

    async syncDiff(direction) {
      if (!this.selectedTable) return;
      try {
        await axios.post("/api/mysqlops/sync", { tables: [this.selectedTable], direction });
        this.syncStatus = 'Diff Sync  ' + this.direct[direction] + ' abgeschlossen für ' + this.selectedTable;
        await this.loadTables();
        this.diffData = [];
      } catch (e) {
        console.error(e);
        this.syncStatus = "Fehler beim Diff-Sync: " + e.message;
      }
    },
  },
  async mounted() {
    this.loadTables();
        const greenTables = this.allTables.filter(
    t => t.status_local === 'green' && t.status_online === 'green'
    );

    if (greenTables.length) {
    await this.loadDiff(greenTables[0]); // öffnet Modal automatisch
    }
    },
};
</script>

<style scoped>

</style>

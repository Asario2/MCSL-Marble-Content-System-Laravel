<template>
  <Layout>
    <div class="p-6 space-y-8">

      <!-- Header -->
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
          MySQL Synchronisation
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
          Zeigt alle Tabellen, Änderungen sind grün, keine Änderungen rot.
        </p>
      </div>

      <!-- Sync-To-All Button -->
      <div>
        <button
            v-if="allTables.some(t => t.status_local === 'green') || allTables.some(t => t.status_online === 'green')"
            @click="syncToAll"
            :disabled="loading"
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow disabled:bg-gray-400"
        >
          <span >🔁 Sync To All (Neue Version -> Alte Version)</span>

        </button>
        <div v-else class="p-4 border-2 border-gray-300 w-full bg-gray-200 text-black dark:bg-gray-800 dark:text-white">Alles Up to Date</div    >
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
              <span>{{ t.name }}</span>
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
              <span>{{ t.name }}</span>
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

      <!-- Statusmeldung -->
      <div
        v-if="syncStatus"
        class="mt-4 p-3 bg-gray-100 dark:bg-gray-800 border rounded text-gray-700 dark:text-gray-200"
      >
        {{ syncStatus }}
      </div>

    </div>
  </Layout>
</template>

<script>
import axios from "axios";
import Layout from "@/Application/Admin/Shared/Layout.vue";

export default {
  name: "SQLSyncTables",
  components: { Layout },

  data() {
    return {
      localTables: [],
      onlineTables: [],
      syncStatus: "",
      loading: false,
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
        const res = await axios.get("/api/mysqlops/tables");
        this.localTables = res.data.local;
        this.onlineTables = res.data.online;
      } catch (e) {
        console.error("Fehler beim Laden:", e);
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
            ? "Sync Local → Online abgeschlossen!"
            : "Sync Online → Local abgeschlossen!";

        await this.loadTables();
      } catch (e) {
        console.error("Sync Fehler:", e);
        this.syncStatus = "Fehler beim Sync: " + e.message;
      }
    },

    // *******************************************************************
    // NEU: SyncToAll – erst Online→Local, dann Local→Online (green→red)
    // *******************************************************************
    async syncToAll() {
      this.loading = true;
      this.syncStatus = "Starte SyncToAll…";

      try {
        // --------------------------------------------------------
        // 1) Online → Local (wenn Online grün und Local rot)
        // --------------------------------------------------------
        const onlineToLocal = this.allTables
          .filter(t => t.status_online === "green" && t.status_local === "red")
          .map(t => t.name);

        if (onlineToLocal.length) {
          await axios.post("/api/mysqlops/sync", {
            tables: onlineToLocal,
            direction: "online_to_local",
          });
        }

        // Reload nach erstem Durchlauf
        await this.loadTables();

        // --------------------------------------------------------
        // 2) Local → Online (wenn Local grün und Online rot)
        // --------------------------------------------------------
        const localToOnline = this.allTables
          .filter(t => t.status_local === "green" && t.status_online === "red")
          .map(t => t.name);

        if (localToOnline.length) {
          await axios.post("/api/mysqlops/sync", {
            tables: localToOnline,
            direction: "local_to_online",
          });
        }

        await this.loadTables();

        this.syncStatus = `SyncToAll abgeschlossen!
            Online→Local: ${onlineToLocal.length} Tabellen,
            Local→Online: ${localToOnline.length} Tabellen.`;

      } catch (e) {
        console.error(e);
        this.syncStatus = "Fehler bei SyncToAll: " + e.message;
      }

      this.loading = false;
    },
  },

  mounted() {
    this.loadTables();
  },
};
</script>

<style scoped>
</style>

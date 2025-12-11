<template>
  <Layout>
    <div class="p-6 space-y-8">

      <!-- Header -->
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
          MySQL Synchronisation
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
          Zeigt Tabellen, die neuer sind als der letzte Sync-Zeitpunkt.
        </p>
      </div>

      <!-- Date Info -->
      <div class="p-4 rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700">
        <span class="font-semibold text-gray-700 dark:text-gray-200">Letzter Sync:</span>
        <span class="ml-2 text-blue-600 dark:text-blue-400">{{ cordate }}</span>
      </div>

      <!-- Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Localhost Tables -->
        <div class="p-4 rounded-xl bg-white dark:bg-gray-900 shadow-sm border border-gray-200 dark:border-gray-700">
          <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-200">Localhost Tabellen (neu)</h2>

          <ul class="space-y-2">
            <li v-for="t in localNewerTables" :key="t.name"
                class="p-3 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-700">
              <div class="font-semibold">{{ t.name }}</div>
              <div class="text-sm text-gray-600 dark:text-gray-400">{{ cordate }}</div>
            </li>

            <li v-if="localNewerTables.length === 0"
                class="text-gray-500 dark:text-gray-400 italic">
              Keine neuen Tabellen gefunden.
            </li>
          </ul>
        </div>

        <!-- Online Tables -->
        <div class="p-4 rounded-xl bg-white dark:bg-gray-900 shadow-sm border border-gray-200 dark:border-gray-700">
          <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-200">Online Tabellen (neu)</h2>

          <ul class="space-y-2">
            <li v-for="t in onlineNewerTables" :key="t.name"
                class="p-3 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-700">
              <div class="font-semibold">{{ t.name }}</div>
              <div class="text-sm text-gray-600 dark:text-gray-400">{{ t.updated_at }}</div>
            </li>

            <li v-if="onlineNewerTables.length === 0"
                class="text-gray-500 dark:text-gray-400 italic">
              Keine neuen Tabellen gefunden.
            </li>
          </ul>
        </div>

      </div>

      <!-- Sync Button -->
      <div class="text-center space-x-4">

        <button
            @click="syncTables('local_to_online')"
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow transition"
        >
            Localhost → Online synchronisieren
        </button>

        <button
            @click="syncTables('online_to_local')"
            class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl shadow transition"
        >
            Localhost ←  Online synchronisieren
        </button>

        </div>

        <p v-if="syncStatus"
        class="mt-4 text-green-600 dark:text-green-400 font-semibold text-center">
        {{ syncStatus }}
        </p>
    </div>
  </Layout>
</template>

<script>
import axios from "axios";
import Layout from "@/Application/Admin/Shared/Layout.vue";

export default {
  name: "SQLUpdate",
  components: { Layout },

  data() {
    return {
      lastOpsDate: null,
      localTables: [],
      onlineTables: [],
      syncStatus: null,
    };
  },

  computed: {
    localNewerTables() {
      return this.localTables.filter(
        t => new Date(t.updated_at) > new Date(this.lastOpsDate)
      );
    },
    onlineNewerTables() {
      return this.onlineTables.filter(
        t => new Date(t.updated_at) > new Date(this.lastOpsDate)
      );
    }
  },
  async mounted() {
    await this.loadLastOpsDate();
    await this.loadTables();
  },

  methods: {
    async loadLastOpsDate() {
      const res = await axios.get('/api/mysqlops/last');
      this.lastOpsDate = res.data.lastDate;
      this.cordate = res.data.cor_date;
    },

    async loadTables() {
      const res = await axios.get('/api/mysqlops/tables');
      this.localTables = res.data.local;
      this.onlineTables = res.data.online;
    },

    async syncTables(direction) {
    const tablesToSync =
        direction === 'local_to_online'
        ? this.localNewerTables.map(t => t.name)
        : this.onlineNewerTables.map(t => t.name);

    await axios.post('/api/mysqlops/sync', {
        tables: tablesToSync,
        direction: direction
    });

    this.syncStatus =
        direction === 'local_to_online'
        ? "Sync Local → Online abgeschlossen!"
        : "Sync Online → Local abgeschlossen!";
    }

  }
};
</script>

<style scoped>
</style>

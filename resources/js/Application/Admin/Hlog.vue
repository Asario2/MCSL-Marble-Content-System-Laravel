<template>
  <div class="p-6 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <h2 class="text-xl font-semibold mb-4">Request Logs</h2>

    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 dark:bg-gray-800">
          <tr>
            <th class="px-3 py-2 text-left">IP</th>
            <th class="px-3 py-2 text-left">URL</th>
            <th class="px-3 py-2 text-left">Method</th>
            <th class="px-3 py-2 text-left">Score</th>
            <th class="px-3 py-2 text-left">Matches</th>
            <th class="px-3 py-2 text-left">Agent</th>
            <th class="px-3 py-2 text-left">Datum</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(row, index) in rows"
            :key="index"
            class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800"
          >
            <td class="px-3 py-2 font-mono">{{ row.ip }}</td>
            <td class="px-3 py-2 truncate max-w-xs">{{ row.url }}</td>
            <td class="px-3 py-2">{{ row.method }}</td>
            <td class="px-3 py-2 font-semibold" :class="row.score > 0 ? 'text-red-500' : ''">
              {{ row.score }}
            </td>
            <td class="px-3 py-2">
              <button
                v-if="row.matches"
                @click="openMatches(row)"
                class="text-indigo-600 dark:text-indigo-400 hover:underline"
              >
                anzeigen
              </button>
              <span v-else class="text-gray-400">–</span>
            </td>
            <td class="px-3 py-2 truncate max-w-xs">{{ row.agent }}</td>
            <td class="px-3 py-2 whitespace-nowrap">{{ row.created_at }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
    >
      <div class="bg-white dark:bg-gray-900 w-full max-w-3xl rounded-xl shadow-xl">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
          <h3 class="font-semibold">Matches</h3>
          <button
            @click="showModal = false"
            class="text-gray-500 hover:text-gray-800 dark:hover:text-gray-200"
          >
            ✕
          </button>
        </div>

        <pre class="p-4 text-xs overflow-auto max-h-[70vh] bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100">
{{ activeMatches }}
        </pre>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RequestLogTable',

  props: {
    rows: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      showModal: false,
      activeMatches: null,
    }
  },

  methods: {
    openMatches(row) {
      try {
        this.activeMatches = typeof row.matches === 'string'
          ? JSON.parse(row.matches)
          : row.matches
      } catch (e) {
        this.activeMatches = row.matches
      }

      this.activeMatches = JSON.stringify(this.activeMatches, null, 2)
      this.showModal = true
    },
  },
}
</script>

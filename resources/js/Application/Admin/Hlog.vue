<template>
    <MetaHeader title="HackLog" />

    <Layout>
    <template #header>
      <Breadcrumb :breadcrumbs="breadcrumbs" />
    </template>
    <div class="p-6 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100">
      <h2 class="text-xl font-semibold mb-4">HackLog</h2>

      <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-100 dark:bg-gray-800">
            <tr>
              <th class="px-3 py-2 text-left">Datum</th>
              <th class="px-3 py-2 text-left">DOM</th>
              <th class="px-3 py-2 text-left">IP</th>
              <th class="px-3 py-2 text-left">URL</th>
              <th class="px-3 py-2 text-left">Gesperrt bis</th>
              <th class="px-3 py-2 text-left">Method</th>
              <th class="px-3 py-2 text-left">Score</th>
              <th class="px-3 py-2 text-left">Matches</th>
              <th class="px-3 py-2 text-left">Agent</th>
              <th class="px-3 py-2 text-left"></th>

            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(row, index) in data"
              :key="index"
              class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800"
            >
              <td class="px-3 py-2 whitespace-nowrap">{{ row.created_at }}</td>
              <td class="px-3 py-2 font-mono">{{ row.dom }}</td>
              <td class="px-3 py-2 font-mono">{{ row.ip }}</td>
              <td class="px-3 py-2 truncate max-w-xs">
                <button
                    v-if="row.url"
                    @click="openModal('URL', row.url)"
                    class="text-indigo-600 dark:text-indigo-400 hover:underline text-left"
                >
                    {{ row.url }}
                </button>
                <span v-else class="text-gray-400">–</span>
              </td>

              <td class="px-3 py-2 whitespace-nowrap">{{ row.banned_until }}</td>
              <td class="px-3 py-2">{{ row.method }}</td>
              <td class="px-3 py-2 font-semibold" :class="row.score > 0 ? 'text-red-500' : ''">
                {{ row.score }}
              </td>
              <td class="px-3 py-2">
                <button
                  v-if="row.matches"
                  @click="openModal('Matches', row.matches)"
                  class="text-indigo-600 dark:text-indigo-400 hover:underline"
                >
                  anzeigen
                </button>
                <span v-else class="text-gray-400">–</span>
              </td>
              <td class="px-3 py-2 truncate max-w-xs">
                <button
                  v-if="row.agent"
                  @click="openModal('Agent', row.agent)"
                  class="text-indigo-600 dark:text-indigo-400 hover:underline"
                >
                  anzeigen
                </button>
                <span v-else class="text-gray-400">–</span>
              </td>
              <td>
                <delhlog :id="row.id"></delhlog>
              </td>
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
            <h3 class="font-semibold">{{ modalTitle }}</h3>
            <button
              @click="showModal = false"
              class="text-gray-500 hover:text-gray-800 dark:hover:text-gray-200"
            >
              ✕
            </button>
          </div>

          <pre class="p-4 text-xs overflow-auto max-h-[70vh] bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100">
{{ modalContent }}
          </pre>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
import delhlog from "@/Application/Admin/Shared/delhlog.vue"
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue"
export default {
  name: 'RequestLogTable',
  components: {
    Layout,
    delhlog,
    MetaHeader,
    Breadcrumb,

  },
  props: {
    data: {
      type: Array,
      required: true,
    },
    breadcrumbs:String,
  },
  data() {
    return {
      showModal: false,
      modalTitle: '',
      modalContent: '',
    }
  },
  methods: {
    openModal(title, content) {
      this.modalTitle = title

      if (title === 'Matches') {
        try {
          content = typeof content === 'string'
            ? JSON.parse(content)
            : content
        } catch (e) {
          // fallback: leave as string
            this.asd = e;
        }
        this.modalContent = JSON.stringify(content, null, 2)
      } else {
        this.modalContent = content
      }

      this.showModal = true
    },
  },
}
</script>

<template>
  <layout>
    <template #header>
      <breadcrumb v-if="CleanTable() !== 'contacts'"
                  :application-name="$page.props.applications.app_admin_name"
                  :start-page="false"
                  current="Liste der Tabellen"
      />
    </template>

    <section class="mt-8">
      <list-container
        title="Liste der Tabellen"
        :datarows="rows"
        route-index="admin.tables.index"
        :filters="filters"
        :search-filter="true"
        :edit-on="false"
        route-edit="admin.tables.edit"
        :create-on="false"
        :route-create="routeCreate"
        :delete-on="false"
        route-delete="admin.tables.destroy"
      >

        <template #header>
          <tr>
            <th class="np-dl-th-normal">Tabellenname</th>
            <th class="np-dl-th-normal">Beschreibung</th>
          </tr>
        </template>

        <!-- Tabellen Zeilen -->
        <template #datarow="data">
          <td class="np-dl-td-normal">
            <a :href="route('admin.tables.show', data.datarow.full_name)"
               class="text-blue-600 dark:text-blue-600 hover:underline">
              {{ data.datarow.name }}
            </a>
          </td>
          <td class="np-dl-td-normal">
            {{ data.datarow.description }}
          </td>
        </template>
      </list-container>
    </section>
  </layout>
</template>

<script>
import { defineComponent } from "vue";
import Layout from "@/Application/Admin/Shared/Layout.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import ListContainer from "@/Application/Components/Lists/ListContainer.vue";
import { CleanTable, GetBatchRights, GetRightsParallel } from '@/helpers'; // NEU: Import der Batch-Funktionen
import { route } from 'ziggy-js';

export default defineComponent({
  name: "Admin_TableList",

  components: {
    Layout,
    Breadcrumb,
    ListContainer,
  },

  props: {
    rows: {
      type: Object,
      required: true,
      default: () => ({ data: [] }),
    },
  },

  data() {
    return {
      filteredRows: [],
      filters: {},
      isLoading: true,
    };
  },

  computed: {
    routeCreate() {
      const table = CleanTable();
      return route('admin.tables.create', table);
    },
  },

  async mounted() {




    this.isLoading = false;

  },

  methods: {
    CleanTable,
    route,


  },
});
</script>

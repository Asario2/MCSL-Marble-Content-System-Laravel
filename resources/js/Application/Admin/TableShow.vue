<template>
    <layout>
      <!-- Header -->
      <template #header>
        <breadcrumb :breadcrumbs="breadcrumbs" :current="tablet"></breadcrumb>
      </template>

      <!-- Table -->
      <section class="mt-8">
        <list-container
          :title="'Tabelle ' + tablez"
          :datarows="{ data: localRows }"
          :route-index="showRoute"
          :xxtable="tableq"
          :filters="filters"
          search-filter="true"
          search-text="Suche nach Inhalten anhand ihres Namens oder ihrer Beschreibung."
          :edit-on="true"
          route-edit="admin.tables.edit"
          :create-on="true"
          :route-create="routeCreate"
          :delete-on="true"
          route-delete="admin.tables.destroy"
          :tableq="this.tableq ?? ''"
          @update-checked-status="onCheckedStatusUpdate"
        >
          <!-- Header-Spalten -->
          <template #header>
            <tr>
              <th class="np-dl-th-normal">ID</th>
              <th class="np-dl-th-normal">Pub</th>
              <th v-if="cat_on_head" class="np-dl-ht-normal">{{ cat_on_head }}</th>
              <th v-if="table_head" class="np-dl-ht-normal">{{ table_head }}</th>
              <th class="np-dl-th-normal">{{ prename }}</th>
              <th class="np-dl-th-normal">{{ predesc }}</th>
              <th v-if="table == 'ratings'">{{ imagedesc }}</th>
              <th v-if="aftsetting" class="np-dl-ht-normal">{{ aftsetting }}</th>
              <th class="np-dl-th-normal" v-if="table === 'comments'">Check</th>
              <th class="np-dl-th-normal" v-if="table === 'images'">Beschreibung</th>
              <th class="np-dl-th-normal" v-if="hasCreated">Datum</th>
              <th class="np-dl-th-normal" colspan="2"></th>
            </tr>
          </template>

          <!-- Datenzeilen -->
          <template v-slot:datarow="data">
            <td class="np-dl-td-normal">{{ getMixId(data.datarow) }}</td>

            <!-- Pub -->
            <td class="np-dl-td-normal" v-if="data.datarow.pub !== 'undefined'">
              <PublishButton
                :table="CleanTable()"
                  :id="data.datarow.id"
                :published="data.datarow.pub === 1"
              />
            </td>

            <!-- Kategorie -->
            <td v-if="data.datarow.image_categories" class="np-dl-td-normal">
              <img :src="'/images/_ab/images_categories/sm/' + data.datarow.image_categories + '.jpg'" />
            </td>
            <td v-if="data.datarow.blog_categories" class="np-dl-td-normal">
              <span
                class="text-sm min-w-fit min-h-fit bg-primary-sun-500 text-primary-sun-900
                       dark:bg-primary-night-500 dark:text-primary-night-900
                       font-semibold px-2.5 py-0.5 rounded-lg whitespace-nowrap"
              >
                {{ ucf(data.datarow.blog_categories) }}
              </span>
            </td>

            <!-- Projekte -->
            <td v-if="table === 'projects_sheets'" class="np-dl-td-normal break-words whitespace-normal">
              {{ ucf(data.datarow.projects) }}
            </td>

            <!-- Comments Tabelle -->
            <td v-if="table == 'comments'" class="np-dl-td-normal">
              <CreatedAt :post_id="data.datarow.post_id" :table="data.datarow.admin_table">
                <span
                  class="text-sm min-w-fit min-h-fit bg-primary-sun-500 text-primary-sun-900
                         dark:bg-primary-night-500 dark:text-primary-night-900
                         font-semibold px-2.5 py-0.5 rounded-lg whitespace-nowrap"
                >
                  {{ ucf(data.datarow.admin_table) }}
                </span>
              </CreatedAt>
            </td>

            <!-- Admin Table -->
            <td v-else-if="table != 'comments' && table_head" class="np-dl-td-normal">
              <span
                class="text-sm min-w-fit min-h-fit bg-primary-sun-500 text-primary-sun-900
                       dark:bg-primary-night-500 dark:text-primary-night-900
                       font-semibold px-2.5 py-0.5 rounded-lg whitespace-nowrap"
              >
                {{ ucf(data.datarow.admin_table) }}
              </span>
            </td>

            <!-- Name -->
            <td class="np-dl-td-normal break-words whitespace-normal max-w-[600px]">
              <span v-html="ucf(data.datarow.name)"></span>
            </td>

            <!-- User bei Kommentaren -->
            <td
              class="np-dl-td-normal"
              v-if="table != 'people' && (users[data.datarow.users_id]?.img || data.datarow.nick || data.datarow.users)"
            >
              <div v-if="users[data.datarow.users_id]?.img && users[data.datarow.users_id].img !== '008.jpg'">
                <nobr>
                  <img
                    :src="'/images/_' + SD() + '/users/profile_photo_path/' + users[data.datarow.users_id].img"
                    class="w-[24px] h-[24px] object-cover rounded-full inline"
                  />
                  &nbsp;{{ data.datarow.users }}
                </nobr>
              </div>
              <div v-else>
                <nobr>
                  <img
                    :src="'/images/_' + SD() + '/users/profile_photo_path/008.jpg'"
                    class="max-w-[24px] max-h-[24px] object-cover rounded-full inline"
                  />
                  <span class="inline">&nbsp;&nbsp;{{ data.datarow.users || data.datarow.nick }}</span>
                </nobr>
              </div>
            </td>

            <!-- Description -->
            <td
              class="np-dl-td-normal break-words whitespace-normal"
              v-if="table !== 'comments' && table != 'ratings' && table != 'projects_sheets'"
            >
              <span v-html="data.datarow.description"></span>
            </td>

            <!-- Ratings -->
            <td v-else-if="table === 'ratings'" class="np-dl-td-normal break-words whitespace-normal">
              {{ data.datarow.images }}
            </td>
            <td v-if="table === 'ratings'" class="np-dl-td-normal break-words whitespace-normal">
              <IconStar
                v-for="i in data.datarow.rating"
                :key="i"
                we="16"
                he="16"
                color="#ffa500"
                class="inline"
              />
              <span class="ml-1">{{ val }}</span>
            </td>

            <!-- Check -->
            <td class="np-dl-td-normal" v-if="table === 'comments'">
              <span v-if="checkedStatus && checkedStatus[data.datarow.id]" style="font-size:24px;">✅</span>
              <span v-else class="bg-[rgb(50,174,179)] rounded-full w-[24px] h-[24px] px-[3px] text-white">O</span>
            </td>
          </template>
        </list-container>
      </section>
    </layout>
  </template>

  <script>
  import { defineComponent, nextTick } from "vue";
  import Layout from "@/Application/Admin/Shared/Layout.vue";
  import CreatedAt from "@/Application/Components/Form/CreatedAt.vue";
  import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
  import ListContainer from "@/Application/Components/Lists/ListContainer.vue";
  import PublishButton from "@/Application/Components/Form/PublishButton.vue";
  import IconStar from "@/Application/Components/Icons/IconStar.vue";
  import { CleanTable, ucf, SD, GetSettings } from "@/helpers";
  import Sortable from "sortablejs";
  import axios from "axios";

  let table_z = CleanTable();
  let table_alt = table_z;
  let table = table_z.toLowerCase();

  export default defineComponent({
    name: "AdminTableShow",
    components: {
      Layout,
      Breadcrumb,
      CreatedAt,
      ListContainer,
      PublishButton,
      IconStar,
    },
    props: {
      applicationName: { type: String, default: "Administrator-Anwendung" },
      users: Object,
      table_alt: { type: String },
      table_q: { type: String, default: table_alt },
      table: { type: String, required: true },
      startPage: { type: Boolean, default: true },
      breadcrumbs: { type: Object, required: true },
      additionalEntries: { type: Array, default: () => [] },
      tableq: { type: String },
      current: { type: String, required: true },
      filters: { type: Object, default: () => ({}) },
      rows: { type: [Array, Object], required: true, default: () => [] },
      datarows: { type: [Array, String], required: true, default: () => [] },
      itemName_des: { type: String, default: "" },
      formData: { type: String, default: "" },
    },
    data() {
      return {
        aftsetting: this.aftSET(),
        imagedesc: "Bild",
        searchQuery: "",
        ItemName: "Tabellen",
        tablez: this.ucf(table_z),
        checkedStatus: {},
        sortable: null,
        table: table.toLowerCase(),
        tableq: CleanTable(),
        settings: {},
        namealias: "",
        descalias: "",
        hasCreated: false,
        cat_on_head: "",
        userName: "",
        tablet: "Übersicht",
        localRows: this.datarows ?? [],
      };
    },
    computed: {
      prename() {
        return this.namealias[this.table] ?? "Name";
      },
      predesc() {
        return this.descalias[this.table] ?? "Beschreibung";
      },
      table_head() {
        return (Array.isArray(this.localRows) && this.localRows[0]?.admin_table_id) ||
          typeof this.localRows[0]?.admin_table_id !== "undefined"
          ? "Tabelle"
          : "";
      },
      routeCreate() {
        if (!this.tableq) return null;
        return route("admin.tables.create", this.tableq);
      },
      showRoute() {
        return route("admin.tables.show", table);
      },
    },
    async mounted() {
      this.cat_on_head = this.checkCat();
      this.checkhasCreated();
      this.settings = await GetSettings();
      window.settings = this.settings;

      if (window.settings?.namealias) {
        this.namealias = window.settings.namealias;
      }
      if (window.settings?.descalias) {
        this.descalias = window.settings.descalias;
      }

      this.initSortable();
    },
    methods: {
      CleanTable,
      ucf,
      SD,

      async removeItem() {
        try {
          const response = await axios.get(`/admin/tables/data/${this.table}`);
          this.localRows = response.data.rows;
          this.localRows = [...this.localRows]; // Update erzwingen
        } catch (error) {
          console.error("Fehler beim Neuladen der Tabelle:", error);
        }
      },

      initSortable() {
        const tableBody = this.$el.querySelector("tbody");
        if (!tableBody) return;

        this.sortable = Sortable.create(tableBody, {
          animation: 150,
          onEnd: async (evt) => {
            const movedItem = this.localRows.splice(evt.oldIndex, 1)[0];
            this.localRows.splice(evt.newIndex, 0, movedItem);

            // Vue-Update erzwingen
            this.localRows = [...this.localRows];

            // Reihenfolge speichern
            try {
                await axios.post(`/api/save-order/${this.table}`, {
                rows: this.localRows.map((row, index) => ({
                    id: row.id,
                    position: index
                }))

              });
            } catch (error) {
              console.error("Fehler beim Speichern der Reihenfolge:", error);
            }
          },
        });
      },

      aftSET() {
        if (CleanTable() == "shortpoems" || CleanTable() == "didyouknow" || CleanTable() == "texts") {
          return "Beschreibung";
        }
        if (CleanTable() == "ratings") {
          return "Sterne";
        }
        if (CleanTable() == "projects_sheets") {
          return "Benutzer";
        }
      },

      onCheckedStatusUpdate(status) {
        this.checkedStatus = status;
      },

      async fetchStatus() {
        await this.$nextTick();
        if (!this.localRows || this.localRows.length === 0) return;
        try {
          const response = await axios.get("/api/chkcom/");
          this.checkedStatus = response.data.success;
        } catch (error) {
          console.error("Fehler beim Batch-Status laden:", error);
        }
      },

      getMixId(row) {
        return this.table !== "comments" ? row.id : row.post_id;
      },

      checkCat() {
        let rows;
        if (typeof this.localRows === "string") {
          try {
            rows = JSON.parse(this.localRows);
          } catch (e) {
            return null;
          }
        } else if (Array.isArray(this.localRows)) {
          rows = this.localRows;
        } else if (typeof this.localRows === "object") {
          rows = Object.values(this.localRows);
        } else {
          return null;
        }
        if (!rows.length) return null;
        const hasCategory = rows.some((row) => row?.image_categories || row?.blog_categories);
        return hasCategory ? "Kategorie" : null;
      },

      async getChecked(id) {
        try {
          const response = await axios.get(`/api/getCheckedDone/${id}`);
          return response.data.done === true;
        } catch (error) {
          console.error("Fehler beim Abrufen des Status:", error);
          return false;
        }
      },

      async checkhasCreated() {
        try {
          const response = await axios.get(`/hasCreated/${this.table}`);
          this.hasCreated = response.data;
        } catch (error) {
          console.error("Fehler bei hasCreated:", error);
          this.hasCreated = false;
        }
      },
    },
  });
  </script>

  <style>
  td {
    white-space: normal;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }
  .wwr {
    word-wrap: break-word;
    overflow-wrap: break-word;
    white-space: normal;
  }
  .oton {
    background-color: rgb(50, 174, 179);
    border-radius: 50%;
    width: 24px !important;
    height: 24px;
    padding: 0px 3px;
    color: #fff;
  }
  .np-dl-th-normal {
    margin-left: 8px;
  }
  </style>

<template>
    <layout>
      <!-- Header -->
      <template #header>
        <breadcrumb :breadcrumbs="breadcrumbs" :current="'Tabelle ' + table"></breadcrumb>
      </template>

      <!-- Table -->
      <section class="mt-8">
        <list-container
          :title="'Tabelle ' + tablez"
          :datarows="rows"
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
              <th class="np-dl-th-normal" >Pub</th>

              <th v-if="cat_on_head" class="np-dl-ht-normal">{{ cat_on_head }}</th>
              <th v-if="table_head" class="np-dl-ht-normal">{{ table_head }}</th>
              <th class="np-dl-th-normal">{{ prename }}</th>
              <th class="np-dl-th-normal">{{ predesc }}</th>
              <th v-if="aftsetting" class="np-dl-ht-normal">{{ aftsettingshead }}</th>
              <th class="np-dl-th-normal" v-if="table === 'comments'">Check</th>
              <th class="np-dl-th-normal" v-if="hasCreated">Datum</th>
              <th class="np-dl-th-normal" colspan="2"></th>
            </tr>
          </template>

          <!-- Datenzeilen -->
          <template v-slot:datarow="data" :items="items" @update-list="removeItem">
            <td class="np-dl-td-normal">{{ getMixId(data.datarow) }}</td>
            <td class="np-dl-td-normal" v-if="data.datarow.pub !== 'undefined'">
                <PublishButton
                :table="CleanTable()"
                :id="data.datarow.id"
                :published="data.datarow.pub === 1"
                />
                <!-- <span v-html="onoffbtn(data.datarow.pub)"></span> -->
            </td>
            <!-- Kategorie -->
            <td v-if="data.datarow.image_categories" class="np-dl-td-normal">
              <img :src="'/images/_ab/images_categories/sm/' + data.datarow.image_categories + '.jpg'" />
            </td>
            <td v-if="data.datarow.blog_categories" class="np-dl-td-normal">
              <span
                class="text-sm min-w-fit min-h-fit bg-primary-sun-500 text-primary-sun-900 dark:bg-primary-night-500 dark:text-primary-night-900 font-semibold px-2.5 py-0.5 rounded-lg whitespace-nowrap"
              >
                {{ data.datarow.blog_categories }}
              </span>
            </td>

            <td v-if="table == 'comments'" class="np-dl-td-normal">
              <CreatedAt :post_id="data.datarow.post_id" :table="data.datarow.admin_table">
                <span
                  class="text-sm min-w-fit min-h-fit bg-primary-sun-500 text-primary-sun-900 dark:bg-primary-night-500 dark:text-primary-night-900 font-semibold px-2.5 py-0.5 rounded-lg whitespace-nowrap"
                >
                  {{ ucf(data.datarow.admin_table) }}
                </span>
              </CreatedAt>
            </td>

            <td v-else-if="table != 'comments' && table_head" class="np-dl-td-normal">
              <span
                class="text-sm min-w-fit min-h-fit bg-primary-sun-500 text-primary-sun-900 dark:bg-primary-night-500 dark:text-primary-night-900 font-semibold px-2.5 py-0.5 rounded-lg whitespace-nowrap"
              >
                {{ ucf(data.datarow.admin_table) }}
              </span>
            </td>

            <td class="np-dl-td-normal break-words whitespace-normal max-w-[600px]">
                <span v-html="data.datarow.name"></span></td>

            <!-- User bei Kommentaren -->
            <td class="np-dl-td-normal" v-if="table === 'comments'">
              <div v-if="users[data.datarow.users_id]?.img && users[data.datarow.users_id].img !== '008.jpg'">
                <nobr>
                  <img
                    :src="'/images/' + users[data.datarow.users_id].img"
                    class="max-w-[24px] max-h-[24px] object-cover rounded-full inline"
                  />
                  &nbsp; {{ users[data.datarow.users_id].name }}
                </nobr>
              </div>
              <div v-else>
                <img
                  :src="'/images/profile-photos/008.jpg'"
                  class="max-w-[24px] max-h-[24px] object-cover rounded-full inline"
                />
                <span>&nbsp;{{ data.datarow.nick }}</span>
              </div>
            </td>

            <td class="np-dl-td-normal break-words whitespace-normal" v-if="table !== 'comments'">
              <span v-html="data.datarow.description"></span>
            </td>

            <!-- Check ✅ -->
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
  import { defineComponent } from "vue";
  import Layout from "@/Application/Admin/Shared/Layout.vue";
  import CreatedAt from "@/Application/Components/Form/CreatedAt.vue";
  import { CleanTable } from "@/helpers";
  import { GetSettings } from "@/helpers";
  import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
  import ListContainer from "@/Application/Components/Lists/ListContainer.vue";
  import PublishButton from "@/Application/Components/Form/PublishButton.vue";
  import axios from "axios";
  import { nextTick } from 'vue';
  import Sortable from "sortablejs"; // <-- NEU


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
    },
    props: {
      applicationName: {
        type: String,
        default: "Administrator-Anwendung",
      },
      users: Object,
      table_alt: {
        type: String,
      },
      table_q: {
        type: String,
        default: table_alt,
      },
      table: {
        type: String,
        required: true,
      },
      startPage: {
        type: Boolean,
        default: true,
      },
      breadcrumbs: {
        type: Object,
        required: true,
      },
      additionalEntries: {
        type: Array,
        default: () => [],
      },
      tableq: {
        type: String,
      },
      current: {
        type: String,
        required: true,
      },
      filters: {
        type: Object,
        default: () => ({}),
      },
      rows: {
        type: [Array, Object],
        required: true,
        default: () => [],
      },
      datarows: {
        type: [Array, String],
        required: true,
        default: () => [],
      },
      itemName_des: {
        type: String,
        default: "",
      },
      formData: {
        type: String,
        default: "",
      },
    },
    data() {
      return {
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
      datarows(){
        return [...this.localRows].sort((a, b) => a.id - b.id);
        // console.log(this.datarows);
        // return this.datarows;
      },
      table_head() {
        return (Array.isArray(this.datarows) && this.datarows[0]?.admin_table_id) ||
          typeof this.datarows[0]?.admin_table_id !== "undefined"
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
        this.sortById();
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
//         await nextTick(); // <-- DOM vollständig gerendert
//         this.initSortable();
//     },
//     methods: {
//         initSortable() {
//   const tbody = this.$el.querySelector("tbody"); // tbody aus der Tabelle holen
//   if (!tbody) return;

//   this.sortable = Sortable.create(tbody, {
//     handle: "td", // ganze Zelle als Drag-Bereich
//     animation: 150,
//     onEnd: (evt) => {
//       const movedItem = this.rows?.splice(evt.oldIndex, 1)[0];
//       this.rows.splice(evt.newIndex, 0, movedItem);

//       // Optional: API-Call zum Speichern der Reihenfolge
//       axios.post(`/api/${this.table}/reorder`, {
//         order: this.rows.map((item) => item.id),
//       });
//     },
//   });
},
methods: {
        onoffbtn(state)
        {
            if(state == "1")
            {
                return "<img src='/images/icons/online.png' />"
            }
            return "<img src='/images/icons/offline.png' />"
        },
        CleanTable,
      onCheckedStatusUpdate(status) {
        this.checkedStatus = status;
      },
      sortById() {
      this.datarows.sort((a, b) => a.id - b.id);
    },
      ucf(str) {
    // Teilt den String an den Unterstrichen
    const arr = str.split("_");

    // Wandelt jedes Element des Arrays um, falls es mehr als ein Wort gibt
    const na = arr.map(
        (val) => val.charAt(0).toUpperCase() + val.slice(1).toLowerCase(),
    );

    // Setzt die W�rter mit einem Leerzeichen zusammen
    return na.join(" ");
},
      async fetchStatus() {
        await this.$nextTick();
        if (!this.datarows || this.datarows.length === 0) return;
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
        if (typeof this.datarows === "string") {
          try {
            rows = JSON.parse(this.datarows);
          } catch (e) {
            return null;
          }
        } else if (Array.isArray(this.datarows)) {
          rows = this.datarows;
        } else if (typeof this.datarows === "object") {
          rows = Object.values(this.datarows);
        } else {
          return null;
        }
        if (!rows.length) return null;
        const hasCategory = rows.some(
          (row) => row?.image_categories || row?.blog_categories
        );
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
          console.log("hascreated:" + response.data);
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
    white-space: normal;        /* Textumbruch aktivieren */
    word-wrap: break-word;      /* für ältere Browser */
    overflow-wrap: break-word;  /* moderne Option */
  }
  .wwr{
    word-wrap: break-word;       /* für ältere Browser */
    overflow-wrap: break-word;   /* moderne Variante */
    white-space: normal;
  }
  .oton{
    background-color:rgb(50, 174, 179);
    border-radius: 50%;
    width:24px !important;
    height:24px;
    padding:0px 3px;
    color:#fff;
  }
  </style>

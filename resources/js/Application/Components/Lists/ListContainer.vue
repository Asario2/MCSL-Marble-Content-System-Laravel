<template>
    <div>
        <div :class="[withinAccordion ? 'mx-1' : 'np-dl-outer-container']">
            <div class="np-dl-data-container">
                <!-- Titel und Button-Gruppe -->
                <div class="np-dl-title">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <div>{{ title }}</div>
                        <div>
                            <button-group v-if="hasRight('add',tablex)">
                                <input-icon-hyperlink v-if="createOn" :href="routeCreate" display_type="table">
                                    <template #icon >
                                        <icon-plus-circle class="button_icon" />
                                        Erstelle
                                    </template>
                                </input-icon-hyperlink>
                                <slot name="button" />
                            </button-group>
                        </div>
                    </div>
                </div>

                <!-- Fehleranzeige -->
                <error-list :errors="errors" />

                <!-- Suchfeld -->
                <div v-if="searchFilter" class="mb-4">
                    <div class="my-6 flex justify-between items-center">
                        <search-filter
                            v-model="form.search"
                            class="w-full"
                            :searchText="searchText"
                            @reset="reset"
                        />
                    </div>
                </div>
                <!-- Tabelle -->
                <table class="np-dl-table">
                    <thead class="np-dl-thead">
                        <slot name="header" />
                    </thead>
                    <tbody v-if="numberOfRows > 0">
                    <!-- Draggable rows -->
                    <template v-for="(row, index) in rows" :key="'draggable-' + row.id">
                        <tr v-if="row.position !== null && row.position !== undefined"
                        :draggable="this.CleanTable() !== ''"
                        @dragstart="this.CleanTable() !== '' && onDragStart($event, index)"
                        @dragover.prevent="this.CleanTable() !== ''"
                        @drop="this.CleanTable() !== '' && onDrop($event, index)"
                        class="cursor-move hover:dark:bg-gray-800 hover:bg-gray-800">

                        <slot name="datarow" :datarow="row" />

                        <td v-if="row.created_at && hasRight('view', row.full_name)" class="np-dl-td-normal">
                               {{ new Date(row.created_at).toLocaleString('de-DE', {
                            day: '2-digit', month: '2-digit', year: 'numeric',
                            hour: '2-digit', minute: '2-digit', second: '2-digit'
                        }) }}
                        </td>

                            <td v-else-if="hasRight('view',row.full_name)"
                            class="np-dl-td-edit"></td>
                            <td v-if="hasRight('edit', row.full_name) && hasRight('view', row.full_name) && tableClean"
                                class="np-dl-td-edit"
                                @click.prevent="editDataRow(row['id'])">
                                <icon-pencil class="w-6 h-6" v-tippy />
                                <tippy>{{ editDescription }}</tippy>
                            </td>
                            <td v-if="hasRight('delete', row.full_name) && hasRight('view', row.full_name && tableClean)"
                                class="np-dl-td-edit"
                                @click="deleteDataRow(row['id'])">
                                <icon-trash class="w-6 h-6" v-tippy />
                                <tippy>{{ deleteDescription }}</tippy>
                            </td>
                            <td v-else-if="hasRight('view',row.full_name)"
                            class="np-dl-td-edit"></td>
                        </tr>
                        </template>
                        <template v-for="(row, index) in rows" :key="'normal-' + row.id">
                        <tr v-if="row.position === null || row.position === undefined"
                            class="hover:dark:bg-gray-800 hover:bg-gray-800">
                        <slot name="datarow" :datarow="row" />
                        <td v-if="row.created_at && hasRight('view', row.full_name)" class="np-dl-td-normal">
                            {{ new Date(row.created_at).toLocaleString('de-DE', {
                                day: '2-digit', month: '2-digit', year: 'numeric',
                                hour: '2-digit', minute: '2-digit', second: '2-digit'
                            }) }}
                        </td>

                            <td v-if="hasRight('edit', row.full_name) && hasRight('view', row.full_name) && tableClean"
                                class="np-dl-td-edit"
                                @click.prevent="editDataRow(row['id'])">
                                <icon-pencil class="w-6 h-6" v-tippy />
                                <tippy>{{ editDescription }}</tippy>
                            </td>
                            <td v-else-if="hasRight('view',row.full_name)"
                            class="np-dl-td-edit"></td>
                            <td v-if="hasRight('delete', row.full_name) && hasRight('view', row.full_name && tableClean)"
                                class="np-dl-td-edit"
                                @click="deleteDataRow(row['id'])">
                                <icon-trash class="w-6 h-6" v-tippy />
                                <tippy>{{ deleteDescription }}</tippy>
                            </td>
                            <td v-else-if="hasRight('view',row.full_name)"
                            class="np-dl-td-edit"></td>
                        </tr>
                        </template>
                    </tbody>
                </table>

                <!-- Pagination -->
                <pagination :links="datarows.links" v-if="numberOfRows > 0" />
                <div v-else class="np-dl-td-no-entries">
                    <alert type="info">{{ noEntries }}</alert>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import { toastBus } from '@/utils/toastBus';
import SearchFilter from "@/Application/Components/Lists/SearchFilter.vue";
import Pagination from "@/Application/Components/Lists/Pagination.vue";
import ButtonGroup from "@/Application/Components/Form/ButtonGroup.vue";
import InputIconHyperlink from "@/Application/Components/Form/InputIconHyperlink.vue";
import ErrorList from "@/Application/Components/Form/ErrorList.vue";
import IconPlusCircle from "@/Application/Components/Icons/PlusCircle.vue";
import IconPencil from "@/Application/Components/Icons/Pencil.vue";
import IconTrash from "@/Application/Components/Icons/Trash.vue";
import Alert from "@/Application/Components/Content/Alert.vue";
import { GetRights,CleanTable,CleanId } from '@/helpers';
import mapValues from "lodash/mapValues";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import { hasRight } from "@/utils/rights";
import { Inertia } from '@inertiajs/inertia'
//import { safeInertiaGet,safeInertiaVisit } from '@/utils/inertia';

export default {
    name: "Contents_Lists_ListContainer",
    components: {
        Link,
        toastBus,
        SearchFilter,
        Pagination,
        ButtonGroup,
        InputIconHyperlink,
        ErrorList,
        IconPlusCircle,
        IconPencil,
        IconTrash,
        Alert,
        CleanTable,
    },
    props: {
        items: {},
        withinAccordion: { type: Boolean, default: false },
        title: { type: String, required: false },
        rowId: { type: String, default: "id" },
        datarows: { type: [Object, Array, String], default: () => [] },
        datarow: { type: [Object, Array, String], default: () => [] },
        noEntries: { type: String, default: "Es wurden keine Datensätze gefunden." },
        filters: { type: [Object, Array], default: () => [] },
        routeIndex: { type: String, default: null },
        routeParamName: { type: [Number, String], default: "table" },
        routeParamValue: { type: [Number, String], default: null },
        searchFilter: { type: Boolean, default: true },
        searchText: { type: String, default: "Hier kannst du den Suchbegriff eingeben" },
        searchValue: { type: String, default: null },
        showOn: { type: Boolean, default: false },
        routeShow: { type: String },
        routeDelete: { type: String },
        deleteOn: { type: Boolean, default: false },
        editOn: { type: Boolean, default: false },
        routeEdit: { type: String },
        createOn: { type: Boolean, default: false },
        deleteDescription: { type: String, default: "Datensatz löschen" },
        editDescription: { type: String, default: "Daten ändern" },
        errors: { type: Object, default: () => ({}) },
        tableq: { type: String },

    },
    async mounted() {
  if (!this.datarow?.full_name) {
    this.datarow.full_name = CleanTable();
  }
  const params = new URLSearchParams(window.location.search);
    const search = params.get("search");

    // Wenn search gesetzt ist, verstecke das Loading-Div
    if (search && search.trim() !== "") {

      this.isLoading = false;
      this.loading = false;
    }
    else{
        this.isLoading = true;
    }
 this.pstate();
//  this.$emit('status-changed', this.checkedStatus);
 this.fetchCheckedStatus();
  // prüfen ob Array
  if (this.datarows && Array.isArray(this.datarows.data)) {
      this.rows = [...this.datarows.data]
    } else {
      console.error("datarows.data ist kein Array:", this.datarows)
      this.rows = []
    }
    this.initRows();

},
    data() {
        return {
            form: { search: this.filters.search },
            rightsData: {},
            rightsReady: false,
            routeCreate: "/admin/tables/create/" + this.tableq,
            routeDelete: "/admin/tables/delete/" + this.tableq + "/" + this.id,
            table: '',
            dragIndex: null,
            draggedIndex: null,
            checkedStatus: {}, // z. B. { 100: true, 101: false }
            rows: [...this.datarows.data],
            currentPage: 1,
            perPage: 20,
            seaRoute:'',
            tab:'',

        };
    },
    created() {
        this.form.search = this.filters.search;
        // console.log(this.form.search);

    },
    computed: {
        drg(){
            if(CleanTable()){
                alert("CLT");
                return true;
            }
            return false;
        },

        numberOfRows() {
            if (Array.isArray(this.datarows.data)) {
                return this.datarows.data.length;
            } else if (typeof this.datarows === "object") {
                return Object.keys(this.datarows.data || {}).length;
            } else {
                return 0;
            }
        },
        isRightsReady() {
            return this.$isRightsReady;
        },
        hasRight() {
      return this.$hasRight; // Zugriff auf globale Methode
    },
    tableClean(){
        return CleanTable();
    }
    },
    watch: {
  form: {
    deep: true,
    handler: throttle(function () {
      // Loader sofort aktivieren
      this.loading = true;

      // Leere Werte filtern
      const query = pickBy(this.form, (v) => v !== "" && v !== null);

      // Immer search param senden, auch wenn leer
      const params = { ...query, table: this.table };
      if (!params.search) params.search = null;
        if(CleanTable() == "")
        {
            this.seaRoute = "admin.tables.index";
            this.tab = '';
        }
        else{
            this.seaRoute = "admin.tables.show"
            this.tab = CleanTable();
        }
      this.$inertia.visit(
        this.route(this.seaRoute,this.tab),
        {
          method: "get",
          data: params,
          preserveState: true,
          replace: true,
          onFinish: () => {
            this.loading = false; // Loader ausblenden nach Inertia-Finish
          },
        }
      );
    }, 50), // throttle auf 500ms
  },
        datarows: {
    handler(newVal) {
      if (newVal && Array.isArray(newVal.data)) {
        this.rows = [...newVal.data]
      } else {
        this.rows = []
      }
    },
    deep: true,
    immediate: true
  },
    },
    methods: {
        CleanTable,
        initRows() {
      this.currentPage = this.datarows.current_page || 1
      this.perPage = this.datarows.per_page || 20

      this.rows = this.datarows.data.map(row => ({
        ...row,
        original_position: row.position || 0
      }))
    },
    onDragStart(event, index) {
    if (!this.rows[index]) return;
    event.dataTransfer.effectAllowed = "move";
    event.dataTransfer.setData("text/plain", index ? index.toString() : '');
  },

  onDrop(event, dropIndex) {
    const dragIndex = parseInt(event.dataTransfer.getData("text/plain"));
    if (isNaN(dragIndex)) return;

    const item = this.rows[dragIndex];
    if (!item) return;

    // Element verschieben
    this.rows.splice(dragIndex, 1);
    this.rows.splice(dropIndex, 0, item);

    // Absolute Start-Position der Seite berücksichtigen
    // z.B. bei 20 Items pro Seite, Seite 2 => offset = 20
    const perPage = this.perPage || 20; // Items pro Seite
    const offset = ((this.currentPage || 1) - 1) * perPage;

    this.rows.forEach((row, i) => {
      // Nur ändern, wenn neue Position != alte Position
      const newPos = offset + i + 1;
      if (row.position !== newPos) {
        row.position = newPos;
      }
    });

    this.saveOrdering();
  },

  async saveOrdering() {
    try {
      // Nur geänderte Reihen senden
      const payload = this.rows
        .filter(row => row.position !== row.original_position)
        .map(row => ({ id: row.id, position: row.position }));

      if (payload.length === 0) return;

      await axios.post("/api/save-order/" + CleanTable(), { rows: payload });
      console.log("Neue Reihenfolge gespeichert");

      // Originalpositionen aktualisieren
      this.rows.forEach(row => {
        row.original_position = row.position;
      });
    } catch (e) {
      console.error("Fehler beim Speichern:", e);
    }
  },
          updateCheckedStatus(newStatus) {
      this.checkedStatus = newStatus
      this.$emit('update-checked-status', this.checkedStatus)
    },

    async fetchCheckedStatus() {
      const response = await axios.get('/api/chkcom/')
      this.checkedStatus = response.data.success
      this.$emit('update-checked-status', this.checkedStatus)
    },

async pstate(){
            let url = location.href;

            if (this.filters.search) {
                // Prüfen, ob URL bereits ein ? enthält (also schon Parameter hat)
                const separator = url.includes('?') ? '&' : '?';
                url = url + separator + 'search=' + encodeURIComponent(this.filters.search);
            }
            // console.log(this.filters.search);
            history.pushState(null, "", url);

        },
        async chkcom(){
            await this.$nextTick();

            if (!this.datarows || this.datarows.length === 0) {
                console.error("Daten noch nicht verfügbar");
                return;
            }



            try {

        const response = await axios.get('/api/chkcom/');
        this.checkedStatus = response.data.success; // { [id]: true/false }
        console.log("CS:" + JSON.stringify(this.checkedStatus,null,2));
        } catch (error) {
        console.error("Fehler beim Batch-Status laden:", error);
        }
        location.reload();

        },
        // async chkcom(){
        //     alert(route('comments.check'));
        //     try {
        //         const response = await axios.get(route('comments.check'), {
        //             params: { id: CleanId() }
        //         });

        //         if(response.data.success) {
        //             console.log('Kommentarstatus aktualisiert');
        //             const coms = response.data.success;
        //             location.reload();// oder Kommentar neu laden
        //         }
        //     } catch (error) {
        //         console.error('Fehler beim Prüfen:', error);
        //     }
        // },
        // async fetchStatus(){
        //     if (!this.datarows || this.datarows.length === 0) {
        //         console.warn("Daten noch nicht verfügbar");
        //         return;
        //     }


        //     if (this.datarows && this.datarows.length > 0) {
        // const ids = this.datarows.map(r => r.id);
        // try {

        // const response = await axios.post('/api/getCheckedBatch', { ids });
        // this.checkedStatus = response.data; // { [id]: true/false }
        // // console.log("CS:" + JSON.stringify(this.checkedStatus,null,2));
        // } catch (error) {
        // console.error("Fehler beim Batch-Status laden:", error);
        // }
        //     }
        // },
        reset() {
            this.form = mapValues(this.form, () => null);
            this.$emit("list-container-search-reset");
        },
        async hasRight(right, table) {
            if (!this.rightsData[`${right}_${table}`] && table) {
                await this.checkRight(right, table);

            }
            return this.rightsData[`${right}_${table}`] === 1;
        },
        async checkRight(right, table) {
            const value = await GetRights(right, table);
            this.rightsData[`${right}_${table}`] = value;

        },
        deleteDataRow(id) {
            this.table = CleanTable();
            this.table = this.table ? this.table : "admin_table";
            if (confirm("Wollen Sie diesen Beitrag wirklich löschen?")) {
                    axios.delete("/admin/tables/delete/" + this.table + "/" + id, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
                })
                .then(response => {
                    toastBus.emit('toast', response.data); // ← erwartet { status: "...", message: "..." }
                    this.$emit("deleted");

                         this.$inertia.reload();
                    })
                    .catch(error => console.error("Fehler beim Löschen2:", error));
            }
        },
        resetForm() {
  this.formData = {
    id: null,
    text: "",
    // weitere Felder, die du brauchst
  };
},
        editDataRow(id) {
            Inertia.visit(`/admin/tables/edit/${id}/${this.tableq}`, {
            preserveScroll: true,
            preserveState: false, // <- Wichtig: sorgt für kompletten reload
            });
        },
    },

};
</script>
<style>
tr {
  transition: transform 0.2s ease, background-color 0.2s ease;
}

tr.dragging {
  opacity: 0.5;
  background-color: #d1e7fd;
}
</style>

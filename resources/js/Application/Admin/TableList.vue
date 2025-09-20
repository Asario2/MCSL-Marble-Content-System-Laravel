<template>
    <layout>
        <template #header>
            <breadcrumb
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
                search-text="Gesucht werden alle Tabellen, die den Suchbegriff im Titel, in der Zusammenfassung bzw. im Artikel selbst enthalten."
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
                        <th class="np-dl-th-normal"><pre>{{ data     }}</pre></th>
                    </tr>
                </template>

                <template #datarow="data" >

                        <td class="np-dl-td-normal" v-if="canView(data.datarow.full_name)">
                            <a
                                :href="route('admin.tables.show', data.datarow.full_name)"
                                class="text-blue-600 dark:text-blue-600 hover:underline"
                            >
                                <span v-html="data.datarow.name"></span>
                            </a>
                        </td>
                        <td class="np-dl-td-normal" v-if="canView(data.datarow.full_name)">
                            <span v-html="data.datarow.description"></span>
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
import { CleanTable } from '@/helpers';
import { toastBus } from '@/utils/toastBus';
import { data } from "jquery";
import { hasRight,loadAllRights,isRightsReady } from '@/utils/rights';
export default defineComponent({
    name: "Admin_TableList",

    components: {
        Layout,
        Breadcrumb,
        ListContainer,
        toastBus,
    },

    props: {
        rows: {
            type: Array,
            required: true,
            default: () => [],
        },
        datarows: {
            type: [Array, String],
            required: true,
            default: () => [],
        },
    },

    data() {
        return {
            rightsData: {},
            rightsReady: false,
        };
    },

    computed: {
        routeCreate() {
            const table = CleanTable(); // oder aus props holen?
            return route('admin.tables.create', table);
        },
        isRightsReady() {
            return this.$isRightsReady;
        },
        hasRight() {
            return this.$hasRight; // Zugriff auf globale Methode
            },
    },

    async mounted() {
        // Dummy-Wartefunktion (wenn benötigt)
        const waitUntilReady = () =>
            new Promise((resolve) => {
                const check = () => {
                    if (window.isRightsReady && window.isRightsReady()) {
                        resolve();
                    } else {
                        setTimeout(check, 50);
                    }
                };
                check();
            });
    },

    methods: {
        async checkRight(right, table) {
            const value = await GetRights(right, table);
            this.rightsData[`${right}_${table}`] = value;
        },
        async hasRightLocal(right, table) {
            if (!this.rightsData[`${right}_${table}`] && table) {
                await this.checkRight(right, table);
            }
            return this.rightsData[`${right}_${table}`] === 1;
        },
        canView(table) {
            return this.$hasRight('view', table); // Globale Methode
        },
    },
});
</script>

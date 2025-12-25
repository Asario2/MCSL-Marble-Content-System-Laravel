<template>
    <layout title="Admin-Dashboard">
        <template #header>
            <breadcrumb
                :application-name="$page.props.applications.app_admin_name"
                :start-page="true"
            ></breadcrumb>
        </template>
        <MetaHeader title="Dashboard" />
        <div
            class="w-full bg-layout-sun-0 dark:bg-layout-night-0 grid grid-cols-1 md:grid-cols-2 gap-2 lg:gap-3 p-6 lg:p-4"
        >
            <!-- Blogartikel -->

            <span v-for="table in adminTables" :key="table.id">
            <navigation-card
                class="navigation_card p-4 rounded-md bg-layout-sun-100 dark:bg-layout-night-100"
                :title="ucf(table.db_name)"
                :routeName="route('admin.tables.show',table.name)"
                :linkName="table.db_link"
                :routeName2="route('admin.tables.create', table.name)"
                :linkName2="table.db_new"
                :withIcon="true"
                :icon="table.db_icon"
            >
                <template #description>
                               {{ table.db_desc }}
                </template>
            </navigation-card>
            </span>

            <!-- Tabellen Overview -->
            <navigation-card v-if="modulRights?.DataBases"
                class="navigation_card p-4 rounded-md bg-layout-sun-100 dark:bg-layout-night-100"
                title="Tabellen"
                :routeName="route('admin.tables.index', table)"
                linkName="Inhalte Verwalten"
                target="_self"
                :withIcon="true"
                icon="IconDB"
            >
                <template #description>
                    Hier kannst du alle Datenbankinhalte bearbeiten
                </template>
            </navigation-card>
            <navigation-card v-if="modulRights?.Contacts"
                class="navigation_card p-4 rounded-md bg-layout-sun-100 dark:bg-layout-night-100"
                title="Kontakte"
                :routeName="route('admin.kontakte')"
                linkName="KontaktFe verwalten"
                target="_self"
                :withIcon="true"
                icon="IconContacts"
            >
                <template #description>
                    Deine Kontakte
                </template>
            </navigation-card>
            {{ console.log(modulRights) }}
            <navigation-card v-if="modulRights?.SendMail"
                class="navigation_card p-4 rounded-md bg-layout-sun-100 dark:bg-layout-night-100"
                title="Email Center"
                linkName="E-Mails Senden"
                target="_self"
                :routeName="route('admin.mailcenter')"
                :withIcon="true"
                icon="IconMail"
            >
                <template #description>
                    Email / Newsletter
                </template>
            </navigation-card>
            <!-- Statistics -->
            <navigation-card v-if="modulRights?.Statistics"
                class="navigation_card p-4 rounded-md bg-layout-sun-100 dark:bg-layout-night-100"
                title="Statistiken"
                linkName="Statsitiken Anzeigen"
                target="_self"
                :routeName="route('stats')"
                :withIcon="true"
                icon="IconChart"
            >
                <template #description>
                    Statistiken
                </template>
            </navigation-card>
            <!-- laravel.log -->

            <navigation-card v-if="modulRights?.LogViewer"
                class="navigation_card p-4 rounded-md bg-layout-sun-100 dark:bg-layout-night-100"
                title="Log"
                :routeName="route('admin.laravel_log')"
                linkName="Zum Laravel-Log"
                target="_blank"
                :withIcon="true"
                icon="IconLogs"
            >
                <!-- DB updaten -->
                <template #description> Logs anzeigen    </template>
            </navigation-card>
            <navigation-card v-if="modulRights?.SQLUpdate"
                class="navigation_card p-4 rounded-md bg-layout-sun-100 dark:bg-layout-night-100"
                title="DB Synchronisieren"
                :routeName="route('SQL.index')"
                linkName="Datenbanken abgleichen"
                target="_self"
                :withIcon="true"
                icon="IconSync"
            >
                <!-- DB updaten -->
                <template #description> Datenbanken Synchronisieren </template>
            </navigation-card>
            <navigation-card v-if="modulRights?.UserRights"
                class="navigation_card p-4 rounded-md bg-layout-sun-100 dark:bg-layout-night-100"
                title="Rechte"
                :routeName="route('admin.users_rights')"
                linkName="Benutzerberechtigungen"
                target="_self"
                :withIcon="true"
                icon="IconLock"
            >
                <!-- DB updaten -->
                <template #description> Benutzer Rechte </template>
            </navigation-card>

        </div>
    </layout>
</template>

<script>
import { defineComponent } from "vue";
import {route} from 'ziggy-js';
//import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import NavigationCard from "@/Application/Components/NavigationCard.vue";
import { loadRights,ucf,CleanTable,GetRights,SD } from '@/helpers';
// import { hasRight,loadAllRights,isRightsReady } from '@/utils/rights';
import { defineAsyncComponent } from "vue";
import axios from "axios";
export default defineComponent({
    name: "Admin_Dashboard",

    components: {
        Layout: defineAsyncComponent(() =>
        import(`@/Application/Admin/Shared/${SD()}/Layout.vue`)),
        Breadcrumb,
        NavigationCard,
        MetaHeader,
    },



  data() {
    return {
      modulRights: null,
      rightsData: {},
      rightsReady: false,
      adminTables: [],
    };
  },

  async mounted() {
    this.modulRights = await loadRights();
    // console.log("Geladene Rechte:", this.modulRights);
    this.fetchAdminTables();
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
    methods: {
        ucf,
        SD,
        CleanTable,
        GetRights,
        fetchAdminTables() {
            axios.get('/api/admin-tables')
                .then(response => {
                this.adminTables = response.data;
                // console.log(this.adminTables);
                })
                .catch(error => {
                console.error('Fehler beim Laden der Tabellen:', error);
                });

        },
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
        const result = this.hasRight('view', table);
        // console.log(`canView(${table}) →`, result);
        return result;
        },
        async fetchtables(){

        },
    },
});

</script>
<style scoped>
.asd{
    color:#fff;
}
</style>

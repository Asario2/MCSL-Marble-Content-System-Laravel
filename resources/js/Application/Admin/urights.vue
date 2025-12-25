<template>
    <Layout>
        <MetaHeader title="Benutzerrechte" />
      <template #header>
        <Breadcrumb
          :application-name="$page.props.applications.app_admin_name"
          :start-page="false"
          current="Benutzerrechte"
        />
      </template>

      <RightsTable
        :adminTables="adminTables"
        :userRights="userRights"
        :urid="urid"
        :roles="roles"
        :func="func"
      />
    </Layout>
  </template>

  <script>
  import RightsTable from "@/Application/Admin/UserRights.vue";
  import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
  import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
  import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
  import axios from "axios";

  export default {
    name: "UserRightsPage",
    components: {
      RightsTable,
      Layout,
      Breadcrumb,
      MetaHeader,
    },
    props: {
      adminTables: Array,
      roles: [Array,Object],
      urid: [String, Number],
    },
    data() {
      const fields = ["view_table", "add_table", "edit_table", "publish_table", "date_table", "delete_table"];
      const rights = {};
      for (const field of fields) {
        rights[field] = [];
      }
      return {
        fields,
        rights,
        userRights: {},
        func: {},
      };
    },
    mounted() {
      this.fetchRights();
      this.fetchFunctions();
    },
    methods: {
      async fetchRights() {
        try {
          const response = await axios.get(`/admin/user-rights/get?urid=${this.urid}`);
          const data = response.data || {};
          this.userRights = data;

          for (const field of this.fields) {
            const binaryString = (data[field] || "").padEnd(this.adminTables.length, "0");
            for (let i = 0; i < this.adminTables.length; i++) {
              this.rights[field][i] = binaryString[i] === "1";
            }
          }
        } catch (error) {
          console.error("Fehler beim Laden der Tabellenrechte:", error);
        }
      },
      async fetchFunctions() {
        try {
          const response = await axios.get(`/api/roles/${this.urid}`);
          this.func = response.data || {};
        } catch (error) {
          console.error("Fehler beim Laden der Funktionenrechte:", error);
        }
      },
    },
  };
  </script>

  <style scoped>
  </style>


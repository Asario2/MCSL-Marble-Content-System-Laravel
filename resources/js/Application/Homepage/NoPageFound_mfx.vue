<template>
    <layout
      header-title="Seite nicht gefunden"
      :header-url="$page.props.saas_url + '/no_page_found'"
    >
      <page-title>
        <template #title>Seite nicht gefunden!</template>
      </page-title>
    </layout>
  </template>

  <script>
  import { defineComponent, defineAsyncComponent } from "vue";
  import PageTitle from "@/Application/Components/Content/PageTitle.vue";
  import Layout from "@/Application/Homepage/Shared/mfx/Layout.vue";

  export default defineComponent({
    name: "Homepage_NoPageFound_mfx",
    components: {
      Layout,

      PageTitle,
    },
  });
  </script>


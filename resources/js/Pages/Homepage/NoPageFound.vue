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

  // Mapping von Subdomain → Layout-Datei
  const layoutComponents = {
    mfx: () => import("@/Application/Homepage/Shared/mfx/Layout.vue"),
    ab: () => import("@/Application/Homepage/Shared/ab/Layout.vue"),
    dag: () => import("@/Application/Homepage/Shared/dag/Layout.vue"),
    default: () => import("@/Application/Homepage/Shared/default/Layout.vue"),
  };

  // Subdomain ermitteln
  function getDomKey() {
    const hostname = window.location.hostname;
    const parts = hostname.split(".");
    return parts.length > 2 ? parts[0] : "default";
  }

  export default defineComponent({
    name: "Homepage_NoPageFound",
    components: {
      Layout: defineAsyncComponent(
        layoutComponents[getDomKey()] || layoutComponents.default
      ),
      PageTitle,
    },
  });
  </script>

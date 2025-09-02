<template>
    <layout
        :header-url="$page.props.saas_url + '/no_page_found'"
    >
    <MetaHeader title="Seite nicht gefunden" />
        <page-title>
            <template #title>Seite nicht gefunden!</template>
        </page-title>
    </layout>
</template>
<script>
import { defineComponent, defineAsyncComponent } from 'vue';
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import PageTitle from "@/Application/Components/Content/PageTitle.vue";

// Mapping für dynamische Layouts
const layoutComponents = {
  mfx: () => import('@/Application/Homepage/Shared/Layout.vue'),
 // dag: () => import('@/Application/Homepage/Shared/dag/Layout.vue'),
  default: () => import('@/Application/Homepage/Shared/Layout.vue'),
};

// Funktion zur Ermittlung der ersten Subdomain
function getDomKey(urlString) {
  try {
    const url = new URL(urlString);
    const hostname = url.hostname;
    const parts = hostname.split('.');

    return parts.length > 2 ? parts[0] : "default";
  } catch (e) {
    return "default";
  }
}

export default defineComponent({
  name: "Homepage_NoPageFound",
  components: {
    Layout: defineAsyncComponent(
      layoutComponents[getDomKey(window.location.href)] || layoutComponents['default']
    ),
    PageTitle, MetaHeader},
});
</script>
